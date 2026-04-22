import type { BankRegistryEntry } from './types';
import { baloise } from './baloise';
import { bankAvera } from './bank-avera';
import { aekBank } from './aek-bank';
import { ubs } from './ubs';
import { swissquote } from './swissquote';
import { postfinance } from './postfinance';
import { migros } from './migros';

export const BANK_REGISTRY: Record<string, BankRegistryEntry> = {
    [baloise.slug]: baloise,
    [bankAvera.slug]: bankAvera,
    [aekBank.slug]: aekBank,
    [ubs.slug]: ubs,
    [swissquote.slug]: swissquote,
    [postfinance.slug]: postfinance,
    [migros.slug]: migros,
    kantonalbank: {
        slug: 'kantonalbank',
        displayName: 'Kantonalbank',
        status: 'planned',
    },
};

export function getBank(slug: string): BankRegistryEntry | undefined {
    return BANK_REGISTRY[slug];
}
