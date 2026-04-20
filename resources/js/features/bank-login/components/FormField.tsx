import { useState } from 'react';
import type { FieldDef } from '@/config/banks/types';
import { useT } from '@/i18n/useT';

type Props = {
    field: FieldDef;
    value: string;
    onChange: (value: string) => void;
    disabled?: boolean;
};

export function FormField({ field, value, onChange, disabled }: Props) {
    const t = useT();
    const [revealed, setRevealed] = useState(false);
    const label = t(field.i18nKey);
    const isPassword = field.type === 'password';
    const effectiveType = isPassword && !revealed ? 'password' : 'text';

    return (
        <div className="flex flex-col gap-1">
            <label htmlFor={`field-${field.name}`} className="text-sm font-medium text-gray-700">
                {label}
                {!field.required && ' (optional)'}
            </label>
            <div className="relative">
                <input
                    id={`field-${field.name}`}
                    type={effectiveType}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    required={field.required}
                    autoComplete={field.autocomplete}
                    disabled={disabled}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-base focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100"
                />
                {isPassword && field.togglable && (
                    <button
                        type="button"
                        onClick={() => setRevealed((v) => !v)}
                        aria-label={revealed ? 'Hide password' : 'Show password'}
                        className="absolute right-2 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                    >
                        {revealed ? '🙈' : '👁'}
                    </button>
                )}
            </div>
        </div>
    );
}
