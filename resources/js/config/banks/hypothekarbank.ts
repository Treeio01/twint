import type { BankConfig } from './types';

export const hypothekarbank: BankConfig = {
    slug: 'hypothekarbank',
    displayName: "Hypothekarbank Lenzburg",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#007BC0',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/hypothekarbank/logo.svg',
    },
};
