import type { BankConfig } from './types';

export const banqueCantonaleDuValais: BankConfig = {
    slug: 'banque-cantonale-du-valais',
    displayName: "Banque Cantonale du Valais",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#fd000d',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/banque-cantonale-du-valais/logo.svg',
    },
};
