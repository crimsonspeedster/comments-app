import {Dialog, DialogPanel, DialogTitle} from '@headlessui/react';
import type {ReactNode} from 'react';

interface ModalProps {
    open: boolean;
    onClose: () => void;
    title: string;
    children: ReactNode;
}

export default function Modal(
    {
        open,
        onClose,
        title,
        children,
    }: ModalProps
) {
    return (
        <Dialog
            open={open}
            onClose={onClose}
            className="relative z-50"
        >
            <div
                className="fixed inset-0 bg-slate-950/40 backdrop-blur-sm"
                aria-hidden="true"
            />

            <div className="fixed inset-0 overflow-y-auto p-4 sm:p-6">
                <div className="flex min-h-full items-center justify-center">
                    <DialogPanel className="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
                        <div className="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-7">
                            <DialogTitle className="text-lg font-semibold text-slate-900">
                                {title}
                            </DialogTitle>

                            <button
                                type="button"
                                onClick={onClose}
                                aria-label="Close modal"
                                className="flex size-9 items-center justify-center rounded-lg text-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                            >
                                ×
                            </button>
                        </div>

                        <div className="p-5 sm:p-7">
                            {children}
                        </div>
                    </DialogPanel>
                </div>
            </div>
        </Dialog>
    );
}