import { Button } from '@ui/Button';

interface HeroProps {
    onAddComment: () => void;
}

export default function Hero({ onAddComment }: HeroProps) {
    return (
        <section className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 className="text-3xl font-bold tracking-tight text-slate-900">
                    Comments
                </h1>

                <p className="mt-2 text-slate-600">
                    Share your thoughts and join the discussion.
                </p>
            </div>

            <Button onClick={onAddComment}>
                Add comment
            </Button>
        </section>
    );
}