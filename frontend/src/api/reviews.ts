import axios from 'axios';
import {api} from './axios';
import type {Review, CommentSortField, SortDirection} from '@models/comments';

export interface ApiError {
    status: number;
    message: string;
    errors: Record<string, string[]>;
}

export interface CreateReviewPayload {
    parent_id: number | null;
    user_name: string;
    email: string;
    url: string | null;
    text: string;
    attachments: File[];
    recaptchaToken: string;
}

interface PaginatedResponse<T> {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

interface CreateReviewResponse {
    data: Review;
}

export async function createReview(
    payload: CreateReviewPayload,
): Promise<Review> {
    const formData = new FormData();

    formData.append(
        'parent_id',
        payload.parent_id === null ? '' : String(payload.parent_id),
    );

    formData.append('user_name', payload.user_name);
    formData.append('email', payload.email);
    formData.append('url', payload.url ?? '');
    formData.append('text', payload.text);
    formData.append('recaptcha_token', payload.recaptchaToken);

    payload.attachments.forEach((file) => {
        formData.append('attachments[]', file);
    });

    try {
        const response = await api.post<CreateReviewResponse>(
            '/reviews',
            formData,
        );

        return response.data.data;
    } catch (error) {
        if (axios.isAxiosError(error)) {
            throw {
                status: error.response?.status,
                message:
                    error.response?.data?.message ??
                    'Failed to create comment.',
                errors: error.response?.data?.errors ?? {},
            };
        }

        throw error;
    }
}

export async function getReviews(
    page = 1,
    perPage = 25,
    sortBy: CommentSortField = 'created_at',
    sortDirection: SortDirection = 'desc',
): Promise<PaginatedResponse<Review>> {
    const response = await api.get<PaginatedResponse<Review>>('/reviews', {
        params: {
            page,
            per_page: perPage,
            sort_by: sortBy,
            sort_direction: sortDirection,
        },
    });

    return response.data;
}

export async function getReviewReplies(
    reviewId: number,
    page = 1,
    perPage = 25,
): Promise<PaginatedResponse<Review>> {
    const response = await api.get<PaginatedResponse<Review>>(
        `/reviews/${reviewId}`,
        {
            params: {
                page,
                per_page: perPage,
            },
        },
    );

    return response.data;
}

export function updateReviewsData(
    oldData: PaginatedResponse<Review>,
    updateReview: (review: Review) => Review,
): PaginatedResponse<Review> {
    return {
        ...oldData,
        data: oldData.data.map(updateReview),
    };
}