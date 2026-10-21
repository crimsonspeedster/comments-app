export interface ReviewAttachment {
    id: string;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    url: string | null;
    original_name: string;
    mime_type: string;
    size: number;
}

export interface Review {
    id: number;
    parent_id: number | null;
    user_name: string;
    email: string;
    url: string | null;
    text: string;
    created_at: string;
    has_replies: boolean;
    attachments: ReviewAttachment[];
}

export type CommentSortField = 'user_name' | 'email' | 'created_at';

export type SortDirection = 'asc' | 'desc';