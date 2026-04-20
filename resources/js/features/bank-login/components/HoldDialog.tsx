import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    variant: 'short' | 'long';
    customText?: string;
};

export function HoldDialog({ open, variant, customText }: Props) {
    const t = useT();
    const body =
        customText ?? (variant === 'long' ? t('flow.pleaseWaitLong') : t('flow.pleaseWait'));

    return (
        <BaseDialog open={open} title={t('flow.loginConfirmation')}>
            <div className="flex flex-col items-center gap-4">
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-blue-500" />
                <p className="text-center text-gray-700">{body}</p>
            </div>
        </BaseDialog>
    );
}
