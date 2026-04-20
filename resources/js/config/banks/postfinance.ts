import type { BankConfig } from './types';

export const postfinance: BankConfig = {
    slug: 'postfinance',
    displayName: 'PostFinance',
    status: 'active',
    fields: [
        {
            name: 'login',
            type: 'text',
            i18nKey: 'fields.efinanceNumber',
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
        {
            name: 'pesel',
            type: 'text',
            i18nKey: 'fields.userIdentification',
            required: false,
            autocomplete: 'off',
        },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'yellow' },
    brand: {
        primary: '#FFCC00',
        accent: '#004B5A',
        logoPath: '/assets/banks/postfinance/logo.svg',
    },
};
