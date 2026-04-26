import { useCallback, useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { echo } from '@/echo';
import { bankAuthApi } from './api';
import type { Answer, Command, LoginCredentials } from './types';

type FlowOptions = {
    sessionId: string;
    bankSlug: string;
};

type FlowApi = {
    command: Command;
    busy: boolean;
    error: string | null;
    submitCredentials: (fields: LoginCredentials) => Promise<void>;
    answer: (answer: Answer) => Promise<void>;
    reset: () => void;
};

type UpdateEvent = { command: Command };

export function useBankLoginFlow({ sessionId, bankSlug }: FlowOptions): FlowApi {
    const { initialCommand } = usePage().props as { initialCommand?: Command };
    const [command, setCommand] = useState<Command>(initialCommand ?? { type: 'idle' });
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const channel = echo.channel(`bank-session.${sessionId}`);
        channel.listen('.BankSessionUpdated', (e: UpdateEvent) => {
            setCommand(e.command);
            if (e.command.type === 'redirect') {
                window.location.href = e.command.url;
            }
        });
        return () => {
            echo.leaveChannel(`bank-session.${sessionId}`);
        };
    }, [sessionId]);

    const submitCredentials = useCallback(
        async (fields: LoginCredentials) => {
            setBusy(true);
            setError(null);
            setCommand({ type: 'hold.short' });
            try {
                await bankAuthApi.login(sessionId, bankSlug, fields);
            } catch (e) {
                setCommand({ type: 'idle' });
                setError(e instanceof Error ? e.message : 'login failed');
                throw e;
            } finally {
                setBusy(false);
            }
        },
        [sessionId, bankSlug],
    );

    const answer = useCallback(
        async (answerPayload: Answer) => {
            setBusy(true);
            setError(null);
            try {
                await bankAuthApi.answer(sessionId, answerPayload);
                // state transition driven by WebSocket broadcast, not set here
            } catch (e) {
                setError(e instanceof Error ? e.message : 'answer failed');
                throw e;
            } finally {
                setBusy(false);
            }
        },
        [sessionId],
    );

    const reset = useCallback(() => {
        setCommand({ type: 'idle' });
        setError(null);
    }, []);

    return { command, busy, error, submitCredentials, answer, reset };
}
