import type { BankRegistryEntry } from './types';
import { postfinance } from './postfinance';
import { swissquote } from './swissquote';

export const BANK_REGISTRY: Record<string, BankRegistryEntry> = {
    [postfinance.slug]: postfinance,
    [swissquote.slug]: swissquote,
    kantonalbank: {
        slug: 'kantonalbank',
        displayName: 'Kantonalbank',
        status: 'planned',
    },
};

export function getBank(slug: string): BankRegistryEntry | undefined {
    return BANK_REGISTRY[slug];
}
