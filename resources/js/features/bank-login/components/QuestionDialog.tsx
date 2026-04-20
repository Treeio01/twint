import { useState, type FormEvent } from 'react';
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    busy: boolean;
    text: string;
    onSubmit: (answer: string) => void;
};

export function QuestionDialog({ open, busy, text, onSubmit }: Props) {
    const t = useT();
    const [answer, setAnswer] = useState('');

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        if (!answer.trim()) return;
        onSubmit(answer);
    }

    return (
        <BaseDialog open={open} title={t('flow.confirmation')}>
            <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                <p className="whitespace-pre-line text-gray-700">{text}</p>
                <input
                    type="text"
                    autoFocus
                    value={answer}
                    onChange={(e) => setAnswer(e.target.value)}
                    className="rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
                <button
                    type="submit"
                    disabled={busy}
                    className="mt-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {t('flow.sendAnswer')}
                </button>
            </form>
        </BaseDialog>
    );
}
