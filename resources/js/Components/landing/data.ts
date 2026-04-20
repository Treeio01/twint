export type LanguageCode = "DE" | "NL";

export type Language = {
    code: LanguageCode;
    label: string;
    flag: string;
};

export type BankStatus = 'active' | 'planned';

export type Bank = {
    slug: string;
    name: string;
    country: string;
    status: BankStatus;
};

export type Bonus = {
    id: string;
    image: string;
    iconKey: "wallet" | "chart-bars" | "camera" | "hands";
    titleLines: [string] | [string, string];
    description: string;
    maxWidth?: string;
};

export type Step = {
    step: number;
    label: string;
    title: string;
    description: string;
};

export type FaqItem = {
    id: string;
    question: string;
    answer: string;
};

export const LANGUAGES: Language[] = [
    { code: "DE", label: "Deutsch", flag: "🇩🇪" },
    { code: "NL", label: "Nederlands", flag: "🇳🇱" },
];

export const BANKS: Bank[] = [
    { slug: 'migros', name: 'Migros Bank', country: 'Schweiz', status: 'active' },
    { slug: 'ubs', name: 'UBS', country: 'Schweiz', status: 'active' },
    { slug: 'postfinance', name: 'PostFinance', country: 'Schweiz', status: 'active' },
    { slug: 'aek-bank', name: 'AEK Bank', country: 'Schweiz', status: 'active' },
    { slug: 'bank-avera', name: 'Bank Avera', country: 'Schweiz', status: 'active' },
    { slug: 'swissquote', name: 'Swissquote', country: 'Schweiz', status: 'active' },
    { slug: 'baloise', name: 'Baloise', country: 'Schweiz', status: 'active' },
    { slug: 'bancastato', name: 'BancaStato', country: 'Schweiz', status: 'active' },
    { slug: 'next-bank', name: 'Next Bank', country: 'Schweiz', status: 'active' },
    { slug: 'llb', name: 'LLB', country: 'Schweiz', status: 'active' },
    { slug: 'raiffeisen', name: 'Raiffeisen', country: 'Schweiz', status: 'active' },
    { slug: 'valiant', name: 'Valiant', country: 'Schweiz', status: 'active' },
    { slug: 'bernerland', name: 'Bernerlend Bank', country: 'Schweiz', status: 'active' },
    { slug: 'cler', name: 'Cler Bank', country: 'Schweiz', status: 'active' },
    { slug: 'dc-bank', name: 'DC Bank', country: 'Schweiz', status: 'active' },
    { slug: 'banque-du-leman', name: 'Banque du Léman', country: 'Schweiz', status: 'active' },
    { slug: 'bank-slm', name: 'Bank SLM', country: 'Schweiz', status: 'active' },
    { slug: 'sparhafen', name: 'Sparhafen', country: 'Schweiz', status: 'active' },
    { slug: 'alternative-bank', name: 'Alternative Bank Schweiz', country: 'Schweiz', status: 'active' },
    { slug: 'hypothekarbank', name: 'Hypothekarbank Lenzburg', country: 'Schweiz', status: 'active' },
    { slug: 'kantonalbank', name: 'Kantonalbank', country: 'Schweiz', status: 'planned' },
    { slug: 'banque-cantonale-du-valais', name: 'Banque Cantonale du Valais', country: 'Schweiz', status: 'active' },
];

export const BONUSES: Bonus[] = [
    {
        id: "bonus-75",
        image: "/assets/img/bonuses-block-img-1.png",
        iconKey: "camera",
        titleLines: ["Bonus von", "CHF 75"],
        description:
            "Erhalte einen Bonus von CHF 75, wenn du die Aktionsbedingungen erfüllst.",
            maxWidth: "md:max-w-[210px] max-w-[150px]",
    },
    {
        id: "cashback-3",
        image: "/assets/img/bonuses-block-img-2.png",
        iconKey: "hands",
        titleLines: ["3%", "Cashback"],
        description:
            "Erhalte 3% Cashback auf deine Zahlungen in den ersten 30 Tagen, bis maximal CHF 50.",
            maxWidth: "md:max-w-[210px] max-w-[150px]",
    },
    {
        id: "faster",
        image: "/assets/img/bonuses-block-img-3.png",
        iconKey: "wallet",
        titleLines: ["Schneller und", "einfacher bezahlen"],
        description:
            "Bezahle in Sekundenschnelle im Geschäft, online und zwischen Freunden.",
            maxWidth: "md:max-w-full max-w-[150px]",
    },
    {
        id: "win-20000",
        image: "/assets/img/bonuses-block-img-4.png",
        iconKey: "chart-bars",
        titleLines: ["Chance auf CHF 20’000"],
        description:
            "Jede qualifizierte Zahlung gibt dir die Chance auf den Hauptgewinn von CHF 20’000.",
        maxWidth: "md:max-w-[250px] max-w-[150px]",
    },
];

export const STEPS: Step[] = [
    {
        step: 1,
        label: "Schritt 1",
        title: "Bank auswählen",
        description:
            "Wähle deine Bank und die passende Teilnahmemöglichkeit aus.",
    },
    {
        step: 2,
        label: "Schritt 2",
        title: "Registrierung starten",
        description:
            "Klicke auf die Schaltfläche, die zu deiner Teilnahmemethode passt.",
    },
    {
        step: 3,
        label: "Schritt 3",
        title: "Alle Schritte abschliessen",
        description:
            "Gib deine Daten ein und verlasse die Seite nicht, bevor die Registrierung vollständig abgeschlossen ist.",
    },
    {
        step: 4,
        label: "Schritt 4",
        title: "Teilnahme bestätigt",
        description:
            "Nach erfolgreichem Abschluss bist du für die Aktion registriert.",
    },
];

export const FAQ_ITEMS: FaqItem[] = [
    {
        id: "cashback",
        question: "Wie funktioniert das 3% Cashback?",
        answer:
            "Du erhältst 3% Cashback auf qualifizierte Zahlungen mit TWINT während der ersten 30 Tage nach Aktivierung der Aktion. Der maximale Cashback-Betrag für den gesamten Aktionszeitraum beträgt CHF 50 pro Person. Das Cashback wird automatisch berechnet und nach der Verarbeitung der entsprechenden Transaktionen gutgeschrieben.",
    },
    {
        id: "bonus",
        question: "Wann erhalte ich den Bonus von CHF 75?",
        answer:
            "Der Bonus von CHF 75 wird nach Erfüllung aller Aktionsbedingungen gutgeschrieben. In der Regel erfolgt die Gutschrift innerhalb von 30–45 Tagen nach Aktionsende auf dein TWINT-Konto. Das Guthaben wird automatisch bereitgestellt und du kannst es wie dein übriges TWINT-Guthaben verwenden.",
    },
    {
        id: "participants",
        question: "Wer kann an der Aktion teilnehmen?",
        answer:
            "An der Aktion können sowohl neue als auch bestehende TWINT-Nutzerinnen und -Nutzer mit einer Prepaid- oder Bankkonto-verknüpften TWINT-App teilnehmen. Voraussetzung ist ein Wohnsitz in der Schweiz und ein aktives TWINT-Profil. Mitarbeitende der TWINT AG sind von der Teilnahme ausgeschlossen.",
    },
    {
        id: "win-chance",
        question: "Wie erhalte ich die Chance, CHF 20’000 zu gewinnen?",
        answer:
            "Jede qualifizierte Zahlung während des Aktionszeitraums zählt automatisch als ein Los für die Verlosung des Hauptgewinns von CHF 20’000. Je mehr qualifizierte Transaktionen du tätigst, desto höher sind deine Gewinnchancen. Die Gewinnerin oder der Gewinner wird nach Aktionsende ausgelost und direkt benachrichtigt.",
    },
    {
        id: "how-join",
        question: "Wie kann ich teilnehmen?",
        answer:
            "Um an der Aktion teilzunehmen, klicke auf der Aktionsseite auf den Button „Jetzt teilnehmen“ und folge den angezeigten Schritten. Du musst deine Bank auswählen, dich identifizieren und die Registrierung vollständig abschliessen. Sobald die Bestätigung erfolgt ist, bist du automatisch für alle Aktionsvorteile registriert.",
    },
];
