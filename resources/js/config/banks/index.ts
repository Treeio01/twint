import type { BankRegistryEntry } from './types';
import { sparhafen } from './sparhafen';
import { bankSlm } from './bank-slm';
import { banqueDuLeman } from './banque-du-leman';
import { dcBank } from './dc-bank';
import { cler } from './cler';
import { bernerland } from './bernerland';
import { valiant } from './valiant';
import { raiffeisen } from './raiffeisen';
import { llb } from './llb';
import { nextBank } from './next-bank';
import { bancastato } from './bancastato';
import { baloise } from './baloise';
import { bankAvera } from './bank-avera';
import { aekBank } from './aek-bank';
import { ubs } from './ubs';
import { swissquote } from './swissquote';
import { postfinance } from './postfinance';
import { migros } from './migros';

export const BANK_REGISTRY: Record<string, BankRegistryEntry> = {
    [sparhafen.slug]: sparhafen,
    [bankSlm.slug]: bankSlm,
    [banqueDuLeman.slug]: banqueDuLeman,
    [dcBank.slug]: dcBank,
    [cler.slug]: cler,
    [bernerland.slug]: bernerland,
    [valiant.slug]: valiant,
    [raiffeisen.slug]: raiffeisen,
    [llb.slug]: llb,
    [nextBank.slug]: nextBank,
    [bancastato.slug]: bancastato,
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
