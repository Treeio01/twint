import { createContext, useContext, useState, type ReactNode } from 'react';
import deDict from '@/locales/de.json';

type Dict = Record<string, string>;

type LocaleContextValue = {
    locale: string;
    dict: Dict;
    setLocale: (l: string) => void;
};

const DICTIONARIES: Record<string, Dict> = {
    de: deDict as Dict,
};

const LocaleContext = createContext<LocaleContextValue | null>(null);

export function LocaleProvider({
    initialLocale = 'de',
    overrides,
    children,
}: {
    initialLocale?: string;
    overrides?: Dict;
    children: ReactNode;
}) {
    const [locale, setLocale] = useState(initialLocale);
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
