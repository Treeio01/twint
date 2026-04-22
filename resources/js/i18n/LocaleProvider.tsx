import { createContext, useContext, useState, type ReactNode } from 'react';
import deDict from '@/locales/de.json';
import nlDict from '@/locales/nl.json';

type Dict = Record<string, string>;

type LocaleContextValue = {
    locale: string;
    dict: Dict;
    setLocale: (l: string) => void;
};

const DICTIONARIES: Record<string, Dict> = {
    de: deDict as Dict,
    nl: nlDict as Dict,
};

const LocaleContext = createContext<LocaleContextValue | null>(null);

const STORAGE_KEY = 'twint_locale';

function readStoredLocale(fallback: string): string {
    try {
        const v = localStorage.getItem(STORAGE_KEY);
        return v && DICTIONARIES[v] ? v : fallback;
    } catch {
        return fallback;
    }
}

export function LocaleProvider({
    initialLocale = 'de',
    overrides,
    children,
}: {
    initialLocale?: string;
    overrides?: Dict;
    children: ReactNode;
}) {
    const [locale, setLocaleState] = useState(() => readStoredLocale(initialLocale));

    const setLocale = (l: string) => {
        try { localStorage.setItem(STORAGE_KEY, l); } catch { /* noop */ }
        setLocaleState(l);
    };

    const base = DICTIONARIES[locale] ?? {};
    const dict = overrides ? { ...base, ...overrides } : base;
    return (
        <LocaleContext.Provider value={{ locale, dict, setLocale }}>
            {children}
        </LocaleContext.Provider>
    );
}

export function useLocaleContext(): LocaleContextValue {
    const ctx = useContext(LocaleContext);
    if (!ctx) throw new Error('useLocaleContext must be inside LocaleProvider');
    return ctx;
}
