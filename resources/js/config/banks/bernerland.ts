import type { BankConfig } from './types';

export const bernerland: BankConfig = {
    slug: 'bernerland',
    displayName: "Bernerlend Bank",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#472D00',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/bernerland/logo.svg',
    },
};
