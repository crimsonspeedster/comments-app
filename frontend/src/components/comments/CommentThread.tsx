import type {Review} from '@models/comments';
import {CommentItem} from '@components/comments/CommentItem';
import {getReviewReplies} from '@/api/reviews';
import {useEffect, useState} from "react";
import {useQuery} from '@tanstack/react-query';

export interface CommentThreadProps {
    review: Review;
    onReply: (review: Review) => void;
    depth?: number;
}

export function CommentThread(
    {
        review,
        onReply,
        depth = 0,
    }: CommentThreadProps
) {
    const [isExpanded, setIsExpanded] = useState(false);

    const {
        data: repliesResponse,
        isFetching,
        isError,
        refetch,
    } = useQuery({
        queryKey: ['review-replies', review.id],
        queryFn: () => getReviewReplies(review.id),
        enabled: isExpanded,
    });

    const replies = repliesResponse?.data ?? [];

    async function toggleReplies() {
        setIsExpanded((current) => !current);
    }

    useEffect(() => {
        console.log({
            id: review.id,
            parent_id: review.parent_id,
            has_replies: review.has_replies,
        });
    }, [review]);

    if (!review) {
        return null;
    }

    return (
        <div className="space-y-4">
            <CommentItem review={review} onReply={onReply}/>

            {review.has_replies && (
                <div className="pl-4">
                    <button
                        type="button"
                        onClick={toggleReplies}
                        disabled={isFetching}
                        className="text-sm font-medium text-blue-600 hover:text-blue-800 disabled:opacity-50"
                    >
                        {isFetching
                            ? 'Loading replies...'
                            : isExpanded
                                ? 'Hide replies'
                                : 'Show replies'}
                    </button>

                    {isError && (
                        <div className="mt-2">
                            <p className="text-sm text-red-600">
                                Failed to load replies.
                            </p>

                            <button
                                type="button"
                                onClick={() => refetch()}
                                className="text-sm underline"
                            >
                                Try again
                            </button>
                        </div>
                    )}
                </div>
            )}

            {isExpanded && replies.length > 0 && (
                <div className="ml-4 space-y-4 border-l border-slate-200 pl-4 sm:ml-6">
                    {replies.map((reply) => (
                        <CommentThread
                            key={reply.id}
                            review={reply}
                            onReply={onReply}
                            depth={depth + 1}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}