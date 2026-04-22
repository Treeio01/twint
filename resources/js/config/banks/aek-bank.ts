import type { BankConfig } from './types';

export const aekBank: BankConfig = {
    slug: 'aek-bank',
    displayName: "AEK Bank",
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
        logoPath: '/assets/banks/aek-bank/logo.svg',
    },
};
