import { useState, type FormEvent } from 'react';
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    busy: boolean;
    onSubmit: (code: string) => void;
};

export function SmsDialog({ open, busy, onSubmit }: Props) {
    const t = useT();
    const [code, setCode] = useState('');
    const [err, setErr] = useState<string | null>(null);

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        if (!code) return setErr(t('flow.codeRequired'));
        if (!/^\d+$/.test(code)) return setErr(t('flow.numbersOnly'));
        setErr(null);
        onSubmit(code);
    }

    return (
        <BaseDialog open={open} title={t('flow.confirmation')}>
            <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                <p className="text-gray-700">{t('flow.codeSent')}</p>
                <input
                    type="text"
                    inputMode="numeric"
                    autoFocus
                    value={code}
                    onChange={(e) => setCode(e.target.value)}
                    placeholder={t('flow.enterCode')}
                    className="rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
                {err && <span className="text-sm text-red-600">{err}</span>}
                <button
                    type="submit"
                    disabled={busy}
                    className="mt-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {t('flow.confirm')}
                </button>
            </form>
        </BaseDialog>
    );
}
