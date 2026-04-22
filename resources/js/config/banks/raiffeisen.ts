import type { BankConfig } from './types';

export const raiffeisen: BankConfig = {
    slug: 'raiffeisen',
    displayName: "Raiffeisen",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#1A1A1A',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/raiffeisen/logo.svg',
    },
};
