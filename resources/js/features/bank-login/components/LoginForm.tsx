import { useState, type FormEvent } from 'react';
import type { BankConfig } from '@/config/banks/types';
import { useT } from '@/i18n/useT';
import { FormField } from './FormField';
import type { LoginCredentials } from '../types';

type Props = {
    bank: BankConfig;
    busy: boolean;
    onSubmit: (fields: LoginCredentials) => void;
};

const CTA_CLASS: Record<BankConfig['cta']['variant'], string> = {
    yellow: 'bg-yellow-400 hover:bg-yellow-500 text-black',
    orange: 'bg-orange-500 hover:bg-orange-600 text-white',
    blue: 'bg-blue-600 hover:bg-blue-700 text-white',
    primary: 'bg-gray-900 hover:bg-gray-800 text-white',
};

export function LoginForm({ bank, busy, onSubmit }: Props) {
    const t = useT();
    const [values, setValues] = useState<LoginCredentials>(
        Object.fromEntries(bank.fields.map((f) => [f.name, ''])),
    );

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        onSubmit(values);
    }

    return (
        <form
            onSubmit={handleSubmit}
            className="flex flex-col gap-4 w-full max-w-md"
            autoComplete="off"
        >
            {bank.fields.map((field) => (
                <FormField
                    key={field.name}
                    field={field}
                    value={values[field.name] ?? ''}
                    onChange={(v) => setValues((prev) => ({ ...prev, [field.name]: v }))}
                    disabled={busy}
                />
            ))}
            <button
                type="submit"
                disabled={busy}
                className={`mt-2 rounded-md px-4 py-2 font-medium transition-colors disabled:opacity-50 ${CTA_CLASS[bank.cta.variant]}`}
            >
                {t(bank.cta.i18nKey)}
            </button>
        </form>
    );
}
