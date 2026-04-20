import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    text: string;
    onAcknowledge: () => void;
};

export function ErrorDialog({ open, text, onAcknowledge }: Props) {
    const t = useT();
    return (
        <BaseDialog open={open} title={t('flow.error')} closable onClose={onAcknowledge}>
            <p className="whitespace-pre-line text-gray-700">{text}</p>
            <div className="mt-4 flex justify-end">
                <button
                    type="button"
                    onClick={onAcknowledge}
                    className="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-800"
                >
                    {t('flow.ok')}
                </button>
            </div>
        </BaseDialog>
    );
}
