import { useEffect } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import echo from '../lib/echo';
import type {AttachmentStatusChangedEvent, Review} from "@/types/comments.ts";

export function useReviewEvents() {
    const queryClient = useQueryClient();

    useEffect(() => {
        const channel = echo.channel('reviews');

        channel.listen(
            '.review.created',
            async (event: { data: Review }) => {
                const review = event.data;

                console.log('review', review);

                if (review.parent_id === null) {
                    await queryClient.invalidateQueries({
                        queryKey: ['reviews'],
                    });

                    return;
                }

                await Promise.all([
                    queryClient.invalidateQueries({
                        queryKey: ['review-replies'],
                    }),
                    queryClient.invalidateQueries({
                        queryKey: ['reviews'],
                    }),
                ]);
            },
        );

        channel.listen(
            '.attachment.status.changed',
            async (event: AttachmentStatusChangedEvent) => {
                const review = event.review;

                if (review.parent_id === null) {
                    await queryClient.invalidateQueries({
                        queryKey: ['reviews'],
                    });

                    return;
                }

                await Promise.all([
                    queryClient.invalidateQueries({
                        queryKey: ['review-replies'],
                    }),
                    queryClient.invalidateQueries({
                        queryKey: ['reviews'],
                    }),
                ]);
            },
        );

        return () => {
            channel.stopListening('.review.created');
            channel.stopListening('.attachment.status.changed');
        };
    }, [queryClient]);
}