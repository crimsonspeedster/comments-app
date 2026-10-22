import {useState} from 'react';
import {PAGE_SIZE} from "@/config/env.ts";

import type {
    Review,
    CommentSortField,
    SortDirection,
} from '@models/comments';

import {CommentsTable} from '@components/comments/CommentsTable';
import {Header} from '@layout/Header';
import {Footer} from '@layout/Footer';
import Hero from '@layout/Hero';
import {Pagination} from '@layout/Pagination';
import CommentForm, {type CommentFormValues} from '@components/comments/CommentForm';
import Modal from '@ui/Modal';
import {useQuery} from "@tanstack/react-query";
import {getReviews} from '@/api/reviews';
import {useCreateReview} from "@hooks/useCreateReview.ts";
import {useReviewEvents} from "@hooks/useReviewEvents.ts";

export default function App() {
    useReviewEvents();

    const [currentPage, setCurrentPage] = useState(1);
    const [sortField, setSortField] =
        useState<CommentSortField>('created_at');
    const [sortDirection, setSortDirection] =
        useState<SortDirection>('desc');

    const {
        data: reviewsResponse,
        isPending,
        isError,
    } = useQuery({
        queryKey: [
            'reviews',
            currentPage,
            PAGE_SIZE,
            sortField,
            sortDirection,
        ],
        queryFn: () =>
            getReviews(
                currentPage,
                PAGE_SIZE,
                sortField,
                sortDirection,
            ),
    });

    const reviews = reviewsResponse?.data ?? [];
    const totalPages = reviewsResponse?.meta.last_page ?? 1;

    const [isFormOpen, setIsFormOpen] = useState(false);
    const [replyTo, setReplyTo] = useState<Review | null>(null);

    function handleSort(field: CommentSortField) {
        if (field === sortField) {
            setSortDirection((direction) =>
                direction === 'asc' ? 'desc' : 'asc',
            );
        } else {
            setSortField(field);
            setSortDirection(
                field === 'created_at' ? 'desc' : 'asc',
            );
        }

        setCurrentPage(1);
    }

    function openCreateForm() {
        setReplyTo(null);
        setIsFormOpen(true);
    }

    function openReplyForm(review: Review) {
        setReplyTo(review);
        setIsFormOpen(true);
    }

    function closeForm() {
        setIsFormOpen(false);
        setReplyTo(null);
    }

    const createReviewMutation = useCreateReview();

    async function handleCreateReview(
        values: CommentFormValues,
        files: File[],
        recaptchaToken: string,
    ): Promise<void> {
        try {
            await createReviewMutation.mutateAsync({
                parent_id: replyTo?.id ?? null,
                user_name: values.userName,
                email: values.email,
                url: values.homePage || null,
                text: values.text,
                attachments: files,
                recaptchaToken,
            });

            setCurrentPage(1);
            setSortField('created_at');
            setSortDirection('desc');

            closeForm();
        } catch (error) {
            console.error('Failed to create comment:', error);

            throw error;
        }
    }

    return (
        <div className="min-h-screen">
            <Header onAddComment={openCreateForm}/>

            <main className="mx-auto max-w-5xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
                <Hero
                    onAddComment={openCreateForm}
                />

                {isPending ? (
                    <div className="py-12 text-center text-sm text-slate-500">
                        Loading comments...
                    </div>
                ) : isError ? (
                    <div
                        role="alert"
                        className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        Failed to load replies.
                    </div>
                ) : <CommentsTable
                        reviews={reviews}
                        sortField={sortField}
                        sortDirection={sortDirection}
                        onSort={handleSort}
                        onReply={openReplyForm}
                    />
                }

                <Pagination
                    currentPage={currentPage}
                    totalPages={totalPages}
                    onPageChange={setCurrentPage}
                />
            </main>

            <Footer />

            <Modal
                open={isFormOpen}
                onClose={closeForm}
                title={replyTo ? 'Reply to comment' : 'Add comment'}
            >
                <CommentForm
                    parentId={replyTo?.id}
                    onSubmit={handleCreateReview}
                    onCancel={closeForm}
                />
            </Modal>
        </div>
    );
}