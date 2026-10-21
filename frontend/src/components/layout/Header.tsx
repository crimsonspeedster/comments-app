import { Button } from '@ui/Button';

interface HeaderProps {
    onAddComment: () => void;
}

export function Header({ onAddComment }: HeaderProps) {
    return (
        <header className="border-b border-slate-200 bg-white">
            <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a
                    href="/"
                    className="text-xl font-bold text-slate-900"
                >
                    CommentsHUB
                </a>

                <Button onClick={onAddComment}>
                    Add comment
                </Button>
            </div>
        </header>
    );
}