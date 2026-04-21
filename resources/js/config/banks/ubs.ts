import type { BankConfig } from './types';

export const ubs: BankConfig = {
    slug: 'ubs',
    displayName: "UBS",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#444',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/ubs/logo.svg',
    },
};
