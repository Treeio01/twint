import type { BankConfig } from './types';

export const bancastato: BankConfig = {
    slug: 'bancastato',
    displayName: "BancaStato",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#d70018',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/bancastato/logo.svg',
    },
};
