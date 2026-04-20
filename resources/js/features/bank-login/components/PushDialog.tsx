import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

export function PushDialog({ open }: { open: boolean }) {
    const t = useT();
    return (
        <BaseDialog open={open} title={t('flow.loginConfirmation')}>
            <div className="flex flex-col items-center gap-4">
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-blue-500" />
                <p className="text-center text-gray-700">{t('flow.pushNotification')}</p>
            </div>
        </BaseDialog>
    );
}
