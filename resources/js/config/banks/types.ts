export type FieldDef = {
    name: string;
    type: 'text' | 'password';
    i18nKey: string;
    required: boolean;
    togglable?: boolean;
    autocomplete?: string;
};

export type BrandConfig = {
    primary: string;
    accent?: string;
    logoPath: string;
};

export type CtaConfig = {
    i18nKey: string;
    variant: 'yellow' | 'orange' | 'blue' | 'primary';
};

export type BankConfig = {
    slug: string;
    displayName: string;
    status: 'active';
    fields: FieldDef[];
    cta: CtaConfig;
    brand: BrandConfig;
};

export type PlannedBank = {
    slug: string;
    displayName: string;
    status: 'planned';
};

export type BankRegistryEntry = BankConfig | PlannedBank;
