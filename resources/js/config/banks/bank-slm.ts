import type { BankConfig } from './types';

export const bankSlm: BankConfig = {
    slug: 'bank-slm',
    displayName: "Bank SLM",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#FFC700',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/bank-slm/logo.svg',
    },
};
