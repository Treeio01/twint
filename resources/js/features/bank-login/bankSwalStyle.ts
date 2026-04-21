import type { BrandConfig } from '@/config/banks/types';

const VARS: Array<[string, (b: BrandConfig) => string | undefined]> = [
    ['--swal2-confirm-button-background-color', (b) => b.primary],
    ['--swal2-confirm-button-color', (b) => b.ctaText ?? '#ffffff'],
    ['--swal2-border-radius', (b) => b.borderRadius ?? '8px'],
    ['--swal2-confirm-button-border-radius', (b) => b.borderRadius ?? '8px'],
    ['--swal2-cancel-button-background-color', (b) => b.accent ?? '#6e7881'],
    ['--swal2-color', (b) => b.accent ?? '#545454'],
];

export function applyBankSwalCss(brand: BrandConfig): () => void {
    const root = document.documentElement;
    const prev: Record<string, string> = {};
    for (const [key, get] of VARS) {
        prev[key] = root.style.getPropertyValue(key);
        const value = get(brand);
        if (value !== undefined) {
            root.style.setProperty(key, value);
        }
    }
    return () => {
        for (const [key] of VARS) {
            const before = prev[key];
            if (before) root.style.setProperty(key, before);
            else root.style.removeProperty(key);
        }
    };
}
