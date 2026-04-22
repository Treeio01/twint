import type { BankConfig } from './types';

export const banqueDuLeman: BankConfig = {
    slug: 'banque-du-leman',
    displayName: "Banque du Léman",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.login', variant: 'primary' },
    brand: {
        primary: '#cc3333',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/banque-du-leman/logo.svg',
    },
};
