import { Dialog, DialogPanel, DialogTitle } from '@headlessui/react';
import type { ReactNode } from 'react';

type Props = {
    open: boolean;
    title: string;
    children: ReactNode;
    onClose?: () => void;
    closable?: boolean;
};

export function BaseDialog({ open, title, children, onClose, closable = false }: Props) {
    return (
        <Dialog
            open={open}
            onClose={() => (closable && onClose ? onClose() : undefined)}
            className="relative z-50"
        >
            <div className="fixed inset-0 bg-black/40" aria-hidden="true" />
            <div className="fixed inset-0 flex items-center justify-center p-4">
                <DialogPanel className="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                    <DialogTitle className="text-lg font-semibold text-gray-900">
                        {title}
                    </DialogTitle>
                    <div className="mt-4">{children}</div>
                </DialogPanel>
            </div>
        </Dialog>
    );
}
