import type { Review } from '@models/comments';
import { Button } from '@ui/Button';
import { useState } from 'react';
import Lightbox from 'yet-another-react-lightbox';
import 'yet-another-react-lightbox/styles.css';

interface CommentItemProps {
    review: Review;
    onReply: (review: Review) => void;
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('en-GB', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(date));
}

export function CommentItem({ review, onReply }: CommentItemProps) {
    const [lightboxIndex, setLightboxIndex] = useState<number>(-1);

    const imageAttachments = review.attachments.filter(
        (attachment) =>
            attachment.status === 'completed' &&
            attachment.mime_type.startsWith('image/') &&
            attachment.url,
    );

    const slides = imageAttachments.map((attachment) => ({
        src: attachment.url!,
        alt: attachment.original_name,
    }));

    return (
        <article className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                        {review.user_name.charAt(0).toUpperCase()}
                    </div>

                    <div>
                        <h3 className="font-semibold text-slate-900">
                            {review.user_name}
                        </h3>

                        <p className="text-sm text-slate-500">
                            {review.email}
                        </p>

                        {review.url && (
                            <a
                                href={review.url}
                                target="_blank"
                                rel="noopener noindex noreferrer"
                                className="mt-1 inline-block text-sm text-indigo-600 hover:underline"
                            >
                                Home page
                            </a>
                        )}
                    </div>
                </div>

                <time
                    dateTime={review.created_at}
                    className="text-sm text-slate-500"
                >
                    {formatDate(review.created_at)}
                </time>
            </div>

            <div className="mt-4 whitespace-pre-wrap wrap-break-word text-slate-700">
                {review.text}
            </div>

            {review.attachments.length > 0 && (
                <div className="mt-4 flex flex-wrap items-end gap-3">
                    {review.attachments.map((attachment) => {
                        const imageIndex = imageAttachments.findIndex(
                            (item) => item.id === attachment.id,
                        );

                        return (
                            <div
                                key={attachment.id}
                                className="max-w-full rounded-lg border border-slate-200 p-3"
                            >
                                {attachment.status === 'processing' ||
                                attachment.status === 'pending' ? (
                                    <p className="text-sm text-slate-500">
                                        Processing: {attachment.original_name}
                                    </p>
                                ) : attachment.status === 'failed' ? (
                                    <p className="text-sm text-red-600">
                                        Failed: {attachment.original_name}
                                    </p>
                                ) : attachment.mime_type.startsWith('image/') &&
                                attachment.url ? (
                                    <button
                                        type="button"
                                        onClick={() => setLightboxIndex(imageIndex)}
                                        className="block cursor-zoom-in text-left"
                                    >
                                        <img
                                            src={attachment.url}
                                            alt={attachment.original_name}
                                            className="max-h-60 max-w-full rounded-md object-contain"
                                        />

                                        <span className="mt-2 block text-sm text-slate-600">
                                {attachment.original_name}
                            </span>
                                    </button>
                                ) : attachment.url ? (
                                    <a
                                        href={attachment.url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="text-sm text-indigo-600 hover:underline"
                                    >
                                        {attachment.original_name}
                                    </a>
                                ) : (
                                    <p className="text-sm text-slate-600">
                                        {attachment.original_name}
                                    </p>
                                )}
                            </div>
                        );
                    })}
                </div>
            )}

            <Lightbox
                open={lightboxIndex >= 0}
                close={() => setLightboxIndex(-1)}
                index={lightboxIndex}
                slides={slides}
            />

            <div className="mt-4 flex justify-end">
                <Button
                    variant="ghost"
                    onClick={() => onReply(review)}
                >
                    Reply
                </Button>
            </div>
        </article>
    );
}