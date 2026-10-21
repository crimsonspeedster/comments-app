import {useMemo, useState} from 'react';

import type {
    Review,
    CommentSortField,
    SortDirection,
} from '@models/comments';

import {comments as initialComments} from '@data/comments';
import {CommentsTable} from '@components/comments/CommentsTable';
import {Header} from '@layout/Header';
import {Footer} from '@layout/Footer';
import Hero from '@layout/Hero';
import {Pagination} from '@layout/Pagination';
import CommentForm, {type CommentFormValues} from '@components/comments/CommentForm';
import Modal from '@ui/Modal';

const PAGE_SIZE = 25;

export default function App() {
    const [reviews, setReviews] = useState<Review[]>(initialComments);

    const [sortField, setSortField] =
        useState<CommentSortField>('created_at');

    const [sortDirection, setSortDirection] =
        useState<SortDirection>('desc');

    const [currentPage, setCurrentPage] = useState(1);

    const [isFormOpen, setIsFormOpen] = useState(false);
    const [replyTo, setReplyTo] = useState<Review | null>(null);

    const rootReviews = useMemo(() => {
        return reviews
            .filter((review) => review.parent_id === null)
            .sort((a, b) => {
                const aValue = a[sortField];
                const bValue = b[sortField];

                const comparison =
                    sortField === 'created_at'
                        ? new Date(aValue).getTime() -
                        new Date(bValue).getTime()
                        : String(aValue).localeCompare(String(bValue));

                return sortDirection === 'asc'
                    ? comparison
                    : -comparison;
            });
    }, [reviews, sortField, sortDirection]);

    const totalPages = Math.max(
        1,
        Math.ceil(rootReviews.length / PAGE_SIZE),
    );

    const paginatedReviews = rootReviews.slice(
        (currentPage - 1) * PAGE_SIZE,
        currentPage * PAGE_SIZE,
    );

    function handleSort(field: CommentSortField) {
        if (field === sortField) {
            setSortDirection((direction) =>
                direction === 'asc' ? 'desc' : 'asc',
            );
        } else {
            setSortField(field);
            setSortDirection(field === 'created_at' ? 'desc' : 'asc');
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

    function handleCreateReview(
        values: CommentFormValues,
        files: File[],
    ) {
        const newReview: Review = {
            id: Math.max(0, ...reviews.map((review) => review.id)) + 1,
            parent_id: replyTo?.id ?? null,
            user_name: values.userName,
            email: values.email,
            url: values.homePage || null,
            text: values.text,
            created_at: new Date().toISOString(),
            has_replies: false,
            attachments: [],
        };

        setReviews((currentReviews) => {
            const updated = currentReviews.map((review) =>
                replyTo && review.id === replyTo.id
                    ? { ...review, has_replies: true }
                    : review,
            );

            return [newReview, ...updated];
        });

        // Пока файлы используются только на уровне формы.
        // Их отправку подключим при интеграции с API.
        console.log('Selected files:', files);

        setCurrentPage(1);
        setSortField('created_at');
        setSortDirection('desc');

        closeForm();
    }

    return (
        <div className="min-h-screen">
            <Header onAddComment={openCreateForm}/>

            <main className="mx-auto max-w-5xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
                <Hero
                    onAddComment={openCreateForm}
                />

                <CommentsTable
                    reviews={paginatedReviews}
                    allReviews={reviews}
                    sortField={sortField}
                    sortDirection={sortDirection}
                    onSort={handleSort}
                    onReply={openReplyForm}
                />

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
                    onSubmit={handleCreateReview}
                    onCancel={closeForm}
                />
            </Modal>
        </div>
    );
}