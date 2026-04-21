import type { BankConfig } from './types';

export const swissquote: BankConfig = {
    slug: 'swissquote',
    displayName: 'Swissquote',
    status: 'active',
    fields: [
        {
            name: 'login',
            type: 'text',
            i18nKey: 'fields.username',
            required: true,
            autocomplete: 'off',
        },
        {
            name: 'password',
            type: 'password',
            i18nKey: 'fields.password',
            required: true,
            autocomplete: 'new-password',
        },
    ],
    cta: { i18nKey: 'cta.login', variant: 'orange' },
    brand: {
        primary: '#EE4923',
        accent: '#000000',
        ctaText: '#ffffff',
        borderRadius: '2px',
        logoPath: '/assets/banks/swissquote/logo.svg',
    },
};
