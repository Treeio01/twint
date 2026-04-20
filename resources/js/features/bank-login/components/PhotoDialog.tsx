import { useState, type FormEvent } from 'react';
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    busy: boolean;
    text?: string;
    withInput: boolean;
    onSubmit: (file: File, inputText: string) => void;
};

export function PhotoDialog({ open, busy, text, withInput, onSubmit }: Props) {
    const t = useT();
    const [file, setFile] = useState<File | null>(null);
    const [inputText, setInputText] = useState('');

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        if (!file) return;
        if (withInput && !inputText.trim()) return;
        onSubmit(file, inputText);
    }

    return (
        <BaseDialog open={open} title={t('flow.uploadPhoto')}>
            <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                {text && <p className="whitespace-pre-line text-gray-700">{text}</p>}
                <input
                    type="file"
                    accept="image/*"
                    onChange={(e) => setFile(e.target.files?.[0] ?? null)}
                    className="text-sm"
                />
                {withInput && (
                    <input
                        type="text"
                        value={inputText}
                        onChange={(e) => setInputText(e.target.value)}
                        className="rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    />
                )}
                <button
                    type="submit"
                    disabled={busy || !file || (withInput && !inputText.trim())}
                    className="mt-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {t('flow.sendAnswer')}
                </button>
            </form>
        </BaseDialog>
    );
}
