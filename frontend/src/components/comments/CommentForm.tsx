import {useState} from 'react';
import {useForm} from 'react-hook-form';
import {z} from 'zod';
import {zodResolver} from '@hookform/resolvers/zod';
import { getRecaptchaToken } from '@/lib/recaptcha';

import {Button} from "@ui/Button.tsx";

const commentSchema = z.object({
    userName: z
        .string()
        .min(1, 'Username is required')
        .regex(/^[a-zA-Z0-9]+$/, 'Use Latin letters and numbers only'),

    email: z
        .string()
        .min(1, 'Email is required')
        .email('Enter a valid email address'),

    homePage: z
        .union([
            z.literal(''),
            z.string().url('Enter a valid URL'),
        ])
        .optional(),

    text: z
        .string()
        .trim()
        .min(1, 'Comment text is required'),
});

export interface CommentFormValues {
    userName: string;
    email: string;
    homePage?: string;
    text: string;
}

interface CommentFormProps {
    parentId?: number | null;
    onCancel: () => void;
    onSubmit: (values: CommentFormValues, files: File[], recaptchaToken: string) => void;
}

const inputClass =
    'mt-1.5 w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100';

const labelClass = 'text-sm font-medium text-slate-700';

export default function CommentForm(
    {
        parentId,
        onCancel,
        onSubmit,
    }: CommentFormProps
) {
    const [files, setFiles] = useState<File[]>([]);
    const [showPreview, setShowPreview] = useState(false);

    const {
        register,
        handleSubmit,
        watch,
        setValue,
        formState: {errors, isSubmitting},
    } = useForm<CommentFormValues>({
        resolver: zodResolver(commentSchema),
        defaultValues: {
            userName: '',
            email: '',
            homePage: '',
            text: '',
        },
    });

    const text = watch('text');

    function insertTag(tag: 'i' | 'strong' | 'code' | 'a') {
        const templates = {
            i: '<i>italic text</i>',
            strong: '<strong>bold text</strong>',
            code: '<code>code</code>',
            a: '<a href="https://example.com" title="Example">link text</a>',
        };

        setValue('text', `${text ?? ''}${templates[tag]}`, {
            shouldDirty: true,
            shouldValidate: true,
        });
    }

    function handleFiles(selectedFiles: FileList | null) {
        if (!selectedFiles) {
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'text/plain',
        ];

        const selected = Array.from(selectedFiles);

        const validFiles = selected.filter((file) =>
            allowedTypes.includes(file.type),
        );

        setFiles((current) => {
            const combined = [...current, ...validFiles];

            return combined.slice(0, 5);
        });
    }

    async function submit(values: CommentFormValues) {
        const recaptchaToken = await getRecaptchaToken('create_comment');

        await onSubmit(values, files, recaptchaToken);
    }

    return (
        <form
            onSubmit={handleSubmit(submit)}
            className="space-y-5"
        >
            {parentId && (
                <div className="rounded-lg bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                    You are replying to comment #{parentId}.
                </div>
            )}

            <div className="grid gap-5 sm:grid-cols-2">
                <div>
                    <label className={labelClass} htmlFor="userName">
                        User name <span className="text-red-500">*</span>
                    </label>

                    <input
                        id="userName"
                        {...register('userName')}
                        placeholder="e.g. AlexDev"
                        className={inputClass}
                    />

                    {errors.userName && (
                        <p className="mt-1 text-xs text-red-600">
                            {errors.userName.message}
                        </p>
                    )}
                </div>

                <div>
                    <label className={labelClass} htmlFor="email">
                        Email <span className="text-red-500">*</span>
                    </label>

                    <input
                        id="email"
                        type="email"
                        {...register('email')}
                        placeholder="you@example.com"
                        className={inputClass}
                    />

                    {errors.email && (
                        <p className="mt-1 text-xs text-red-600">
                            {errors.email.message}
                        </p>
                    )}
                </div>
            </div>

            <div>
                <label className={labelClass} htmlFor="homePage">
                    Home page <span className="text-slate-400">(optional)</span>
                </label>

                <input
                    id="homePage"
                    {...register('homePage')}
                    placeholder="https://example.com"
                    className={inputClass}
                />

                {errors.homePage && (
                    <p className="mt-1 text-xs text-red-600">
                        {errors.homePage.message}
                    </p>
                )}
            </div>

            <div>
                <div className="flex flex-wrap items-center justify-between gap-2">
                    <label className={labelClass} htmlFor="text">
                        Comment <span className="text-red-500">*</span>
                    </label>

                    <button
                        type="button"
                        onClick={() => setShowPreview((value) => !value)}
                        className="text-xs font-medium text-indigo-600 hover:text-indigo-700"
                    >
                        {showPreview ? 'Edit comment' : 'Preview'}
                    </button>
                </div>

                {!showPreview ? (
                    <>
                        <div
                            className="mt-2 flex flex-wrap gap-1 rounded-t-lg border border-b-0 border-slate-200 bg-slate-50 p-2">
                            {([
                                ['i', 'Italic'],
                                ['strong', 'Bold'],
                                ['code', 'Code'],
                                ['a', 'Link'],
                            ] as const).map(([tag, label]) => (
                                <button
                                    key={tag}
                                    type="button"
                                    onClick={() => insertTag(tag)}
                                    className="rounded-md px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-white hover:text-indigo-600"
                                >
                                    {label}
                                </button>
                            ))}
                        </div>

                        <textarea
                            id="text"
                            {...register('text')}
                            rows={6}
                            placeholder="Write your comment..."
                            className="block w-full resize-y rounded-b-lg border border-slate-200 px-3.5 py-3 text-sm leading-6 text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        />
                    </>
                ) : (
                    <div
                        className="mt-2 min-h-40 whitespace-pre-wrap break-words inner-link rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700"
                        dangerouslySetInnerHTML={{ __html: text || 'Nothing to preview yet.' }}
                    />
                )}

                {errors.text && (
                    <p className="mt-1 text-xs text-red-600">
                        {errors.text.message}
                    </p>
                )}

                <p className="mt-1.5 text-xs text-slate-400">
                    Allowed tags: &lt;a&gt;, &lt;code&gt;, &lt;i&gt;, &lt;strong&gt;.
                </p>
            </div>

            <div>
                <label className={labelClass}>
                    Attachments
                </label>

                <label
                    className="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 px-4 py-6 text-center transition hover:border-indigo-300 hover:bg-indigo-50/30">
                    <span className="text-2xl">📎</span>

                    <span className="mt-2 text-sm font-medium text-slate-700">
                        Click to upload files
                    </span>

                    <span className="mt-1 text-xs text-slate-400">
                        JPG, PNG, GIF or TXT
                    </span>

                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/gif,text/plain"
                        multiple
                        className="sr-only"
                        onChange={(event) => {
                            handleFiles(event.target.files);
                            event.target.value = '';
                        }}
                    />
                </label>

                {files.length > 0 && (
                    <ul className="mt-3 space-y-2">
                        {files.map((file, index) => (
                            <li
                                key={`${file.name}-${index}`}
                                className="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2 text-sm"
                            >
                                <span className="min-w-0 truncate text-slate-700">
                                    {file.name}
                                </span>

                                <button
                                    type="button"
                                    onClick={() => {
                                        setFiles((current) =>
                                            current.filter((_, i) => i !== index),
                                        );
                                    }}
                                    className="shrink-0 text-xs text-red-600 hover:text-red-700"
                                >
                                    Remove
                                </button>
                            </li>
                        ))}
                    </ul>
                )}
            </div>

            <div className="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <Button
                    type="button"
                    variant="secondary"
                    onClick={onCancel}
                >
                    Cancel
                </Button>

                <Button type="submit" disabled={isSubmitting}>
                    Publish comment
                </Button>
            </div>
        </form>
    );
}