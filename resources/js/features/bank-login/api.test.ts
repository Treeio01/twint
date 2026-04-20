import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest';
import { bankAuthApi } from './api';

describe('bankAuthApi', () => {
    beforeEach(() => {
        document.head.innerHTML = '<meta name="csrf-token" content="TEST_TOKEN">';
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('login posts JSON with CSRF token', async () => {
        const fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ ok: true }),
        });
        vi.stubGlobal('fetch', fetchMock);

        await bankAuthApi.login('sess-1', 'postfinance', { login: 'u', password: 'p' });

        expect(fetchMock).toHaveBeenCalledWith(
            '/api/bank-auth/login',
            expect.objectContaining({
                method: 'POST',
                headers: expect.objectContaining({ 'X-CSRF-TOKEN': 'TEST_TOKEN' }),
            }),
        );
        const call = fetchMock.mock.calls[0][1];
        expect(JSON.parse(call.body)).toEqual({
            sessionId: 'sess-1',
            bankSlug: 'postfinance',
            fields: { login: 'u', password: 'p' },
        });
    });

    it('answer posts JSON for sms command', async () => {
        const fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ ok: true }),
        });
        vi.stubGlobal('fetch', fetchMock);

        await bankAuthApi.answer('sess-1', { command: 'sms', payload: { code: '1234' } });

        expect(fetchMock).toHaveBeenCalledWith(
            '/api/bank-auth/answer/sess-1',
            expect.objectContaining({ method: 'POST' }),
        );
    });

    it('answer posts FormData for photo.with-input', async () => {
        const fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ ok: true }),
        });
        vi.stubGlobal('fetch', fetchMock);
        const file = new File(['x'], 'a.png', { type: 'image/png' });

        await bankAuthApi.answer('sess-1', {
            command: 'photo.with-input',
            payload: { file, text: 'hello' },
        });

        const call = fetchMock.mock.calls[0][1];
        expect(call.body).toBeInstanceOf(FormData);
        expect((call.body as FormData).get('command')).toBe('photo.with-input');
        expect((call.body as FormData).get('text')).toBe('hello');
    });

    it('throws on non-ok response', async () => {
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue({ ok: false, status: 500, json: async () => ({}) }),
        );
        await expect(
            bankAuthApi.login('sess-1', 'postfinance', { login: 'u', password: 'p' }),
        ).rejects.toThrow(/500/);
    });
});
