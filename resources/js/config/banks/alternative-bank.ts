import type { BankConfig } from './types';

export const alternativeBank: BankConfig = {
    slug: 'alternative-bank',
    displayName: "Alternative Bank Schweiz",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#AAC600',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/alternative-bank/logo.svg',
    },
};
