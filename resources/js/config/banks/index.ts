import type { BankRegistryEntry } from './types';

export const BANK_REGISTRY: Record<string, BankRegistryEntry> = {};

export function getBank(slug: string): BankRegistryEntry | undefined {
    return BANK_REGISTRY[slug];
}
