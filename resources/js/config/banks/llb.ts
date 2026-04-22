import type { BankConfig } from './types';

export const llb: BankConfig = {
    slug: 'llb',
    displayName: "LLB",
    status: 'active',
    fields: [
        { name: "login", type: "text", i18nKey: "fields.username", required: true, autocomplete: "off" },
        { name: "password", type: "password", i18nKey: "fields.password", required: true, autocomplete: "new-password" },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#38462F',
        ctaText: '#fff',
        borderRadius: '4px',
        logoPath: '/assets/banks/llb/logo.svg',
    },
};
