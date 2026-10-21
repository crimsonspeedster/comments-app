import type {Review} from '@models/comments';
import {CommentItem} from '@components/comments/CommentItem';

interface CommentThreadProps {
    review: Review;
    reviews: Review[];
    onReply: (review: Review) => void;
    depth?: number;
}

export function CommentThread(
    {
        review,
        reviews,
        onReply,
        depth = 0,
    }: CommentThreadProps
) {
    const replies = reviews.filter(
        (item) => item.parent_id === review.id,
    );

    return (
        <div className="space-y-4">
            <CommentItem
                review={review}
                onReply={onReply}
            />

            {replies.length > 0 && (
                <div
                    className={`space-y-4 border-l-2 border-slate-200 pl-4 sm:pl-6 ${
                        depth >= 3 ? 'ml-0' : 'ml-3 sm:ml-6'
                    }`}
                >
                    {replies.map((reply) => (
                        <CommentThread
                            key={reply.id}
                            review={reply}
                            reviews={reviews}
                            onReply={onReply}
                            depth={depth + 1}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}