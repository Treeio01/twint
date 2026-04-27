import type { BankConfig } from './types';

export const kantonalbank: BankConfig = {
    slug: 'kantonalbank',
    displayName: 'Kantonalbank',
    status: 'active',
    fields: [
        { name: 'login', type: 'text', i18nKey: 'fields.username', required: true, autocomplete: 'off' },
        { name: 'password', type: 'password', i18nKey: 'fields.password', required: true, autocomplete: 'new-password' },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'primary' },
    brand: {
        primary: '#1C171D',
        ctaText: '#fff',
        borderRadius: '12px',
        logoPath: '/assets/banks/kantonalbank/logo.svg',
    },
};
