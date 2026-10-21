import {Button} from '@ui/Button';

interface PaginationProps {
    currentPage: number;
    totalPages: number;
    onPageChange: (page: number) => void;
}

export function Pagination(
    {
        currentPage,
        totalPages,
        onPageChange,
    }: PaginationProps
) {
    if (totalPages <= 1) {
        return null;
    }

    return (
        <nav
            aria-label="Comments pagination"
            className="flex items-center justify-between gap-4"
        >
            <Button
                variant="secondary"
                disabled={currentPage === 1}
                onClick={() => onPageChange(currentPage - 1)}
            >
                Previous
            </Button>

            <span className="text-sm text-slate-600">
                Page {currentPage} of {totalPages}
            </span>

            <Button
                variant="secondary"
                disabled={currentPage === totalPages}
                onClick={() => onPageChange(currentPage + 1)}
            >
                Next
            </Button>
        </nav>
    );
}