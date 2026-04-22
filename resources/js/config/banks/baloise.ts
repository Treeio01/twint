import type { BankConfig } from './types';

export const baloise: BankConfig = {
    slug: 'baloise',
    displayName: "Baloise",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'blue' },
    brand: {
        primary: '#000D6E',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/baloise/logo.svg',
    },
};
