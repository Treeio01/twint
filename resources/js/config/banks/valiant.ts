import type { BankConfig } from './types';

export const valiant: BankConfig = {
    slug: 'valiant',
    displayName: "Valiant",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#7252ac',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/valiant/logo.svg',
    },
};
