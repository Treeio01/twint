import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { LocaleProvider } from './LocaleProvider';
import { useT } from './useT';

function Probe({ k, fallback }: { k: string; fallback?: string }) {
    const t = useT();
    return <span>{t(k, fallback)}</span>;
}

describe('LocaleProvider + useT', () => {
    it('resolves key from base dict', () => {
        render(
            <LocaleProvider initialLocale="de">
                <Probe k="cta.continue" />
            </LocaleProvider>,
        );
        expect(screen.getByText('Weiter')).toBeInTheDocument();
    });

    it('applies overrides on top of base dict', () => {
        render(
            <LocaleProvider initialLocale="de" overrides={{ 'cta.continue': 'Next' }}>
                <Probe k="cta.continue" />
            </LocaleProvider>,
        );
        expect(screen.getByText('Next')).toBeInTheDocument();
    });

    it('returns fallback for missing key', () => {
        render(
            <LocaleProvider initialLocale="de">
                <Probe k="nonexistent.key" fallback="X" />
            </LocaleProvider>,
        );
        expect(screen.getByText('X')).toBeInTheDocument();
    });

    it('returns key itself when no fallback and key missing', () => {
        render(
            <LocaleProvider initialLocale="de">
                <Probe k="nonexistent.key" />
            </LocaleProvider>,
        );
        expect(screen.getByText('nonexistent.key')).toBeInTheDocument();
    });
});
