import type { BankConfig } from './types';

export const sparhafen: BankConfig = {
    slug: 'sparhafen',
    displayName: "Sparhafen",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#002966',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/sparhafen/logo.svg',
    },
};
