import type { BankConfig } from './types';

export const migros: BankConfig = {
    slug: 'migros',
    displayName: "Migros Bank",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#144B3C',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/migros/logo.svg',
    },
};
