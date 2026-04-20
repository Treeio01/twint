import { useLocaleContext } from './LocaleProvider';

export function useT() {
    const { dict } = useLocaleContext();
    return (key: string, fallback?: string): string => {
        return dict[key] ?? fallback ?? key;
    };
}
