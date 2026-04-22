import type { BankConfig } from './types';

export const nextBank: BankConfig = {
    slug: 'next-bank',
    displayName: "Next Bank",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'blue' },
    brand: {
        primary: '#6f53a3',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/next-bank/logo.svg',
    },
};
