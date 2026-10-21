import { useMutation, useQueryClient } from '@tanstack/react-query';

import { createReview } from '@/api/reviews';
import type { CreateReviewPayload } from '@api/reviews';

export function useCreateReview() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: (payload: CreateReviewPayload) =>
            createReview(payload),

        onSuccess: async (review) => {
            await Promise.all([
                queryClient.invalidateQueries({
                    queryKey: ['reviews'],
                }),

                ...(review.parent_id !== null
                    ? [
                        queryClient.invalidateQueries({
                            queryKey: [
                                'review-replies',
                                review.parent_id,
                            ],
                        }),
                    ]
                    : []),
            ]);
        },
    });
}