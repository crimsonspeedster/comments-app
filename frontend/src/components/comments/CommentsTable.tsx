import type {
    Review,
    CommentSortField,
    SortDirection,
} from '@models/comments';

import {CommentThread} from '@components/comments/CommentThread';

interface CommentsTableProps {
    reviews: Review[];
    allReviews: Review[];
    sortField: CommentSortField;
    sortDirection: SortDirection;
    onSort: (field: CommentSortField) => void;
    onReply: (review: Review) => void;
}

interface SortButtonProps {
    field: CommentSortField;
    label: string;
    currentField: CommentSortField;
    direction: SortDirection;
    onSort: (field: CommentSortField) => void;
}

function SortButton(
    {
        field,
        label,
        currentField,
        direction,
        onSort,
    }: SortButtonProps
) {
    const active = field === currentField;

    return (
        <button
            type="button"
            onClick={() => onSort(field)}
            className={`inline-flex items-center gap-1 font-semibold hover:text-indigo-600 ${
                active ? 'text-indigo-600' : 'text-slate-700'
            }`}
        >
            {label}

            <span aria-hidden="true">
                {active ? (direction === 'asc' ? '↑' : '↓') : '↕'}
            </span>
        </button>
    );
}

export function CommentsTable(
    {
        reviews,
        allReviews,
        sortField,
        sortDirection,
        onSort,
        onReply,
    }: CommentsTableProps
) {
    return (
        <section className="space-y-5">
            <div className="flex flex-wrap items-center gap-x-6 gap-y-3 rounded-xl border border-slate-200 bg-white p-4">
                <span className="text-sm text-slate-500">
                    Sort by:
                </span>

                <SortButton
                    field="user_name"
                    label="User name"
                    currentField={sortField}
                    direction={sortDirection}
                    onSort={onSort}
                />

                <SortButton
                    field="email"
                    label="Email"
                    currentField={sortField}
                    direction={sortDirection}
                    onSort={onSort}
                />

                <SortButton
                    field="created_at"
                    label="Date"
                    currentField={sortField}
                    direction={sortDirection}
                    onSort={onSort}
                />
            </div>

            {reviews.length === 0 ? (
                <div className="rounded-xl border border-dashed border-slate-300 p-10 text-center text-slate-500">
                    No comments yet.
                </div>
            ) : (
                reviews.map((review) => (
                    <CommentThread
                        key={review.id}
                        review={review}
                        reviews={allReviews}
                        onReply={onReply}
                    />
                ))
            )}
        </section>
    );
}