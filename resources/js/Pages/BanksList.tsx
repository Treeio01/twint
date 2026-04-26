import { Header } from "@/Components/landing/Header";
import { LocaleLink } from "@/Components/LocaleLink";
import { useT } from "@/i18n/useT";

const BANKS: { slug: string; name: string; logo: string }[] = [
    { slug: 'migros', name: 'Migros Bank', logo: 'Migros Bank.png' },
    { slug: 'ubs', name: 'UBS', logo: 'UBS.png' },
    { slug: 'postfinance', name: 'PostFinance', logo: 'PostFinance.png' },
    { slug: 'aek-bank', name: 'AEK Bank', logo: 'Aek Bank.png' },
    { slug: 'bank-avera', name: 'Bank Avera', logo: 'BANK avera.png' },
    { slug: 'swissquote', name: 'Swissquote', logo: 'swissqoute.jpg' },
    { slug: 'baloise', name: 'Baloise', logo: 'baloise.png' },
    { slug: 'bancastato', name: 'BancaStato', logo: 'BancaStato.png' },
    { slug: 'next-bank', name: 'Next Bank', logo: 'Next Bank.png' },
    { slug: 'llb', name: 'LLB', logo: 'llb.png' },
    { slug: 'raiffeisen', name: 'Raiffeisen', logo: 'RAIFFEISEN.png' },
    { slug: 'valiant', name: 'Valiant', logo: 'valiant.png' },
    { slug: 'bernerland', name: 'Bernerland Bank', logo: 'Bernerlend-bank.png' },
    { slug: 'cler', name: 'Cler Bank', logo: 'Cler Bank.png' },
    { slug: 'dc-bank', name: 'DC Bank', logo: 'DC bank.png' },
    { slug: 'banque-du-leman', name: 'Banque du Léman', logo: 'Banque du leman.png' },
    { slug: 'bank-slm', name: 'Bank SLM', logo: 'Bank slm.png' },
    { slug: 'sparhafen', name: 'Sparhafen', logo: 'Sparhafen.png' },
    { slug: 'alternative-bank', name: 'Alternative Bank Schweiz', logo: 'Alternative bank schweiz.png' },
    { slug: 'hypothekarbank', name: 'Hypothekarbank Lenzburg', logo: 'Hypothekarbank lenzburg.png' },
    { slug: 'banque-cantonale-du-valais', name: 'Banque Cantonale du Valais', logo: 'Banque-Cantonale-du-valais.png' },
    { slug: 'kantonalbank', name: 'Kantonalbank', logo: 'Kantonalbank.png' },
];

export default function BanksList() {
    const t = useT();
    return (
        <div className="flex flex-col w-full items-center relative">
            <img
                src="/assets/img/main-vector.svg"
                className="absolute left-0 top-[265px]"
                alt=""
            />
            <Header />

            <section className="flex z-[50] mt-[20px] flex-wrap gap-[9px] md:gap-[20px] w-full justify-center max-w-[1440px]">
                {BANKS.map((bank) => (
                    <LocaleLink
                        key={bank.slug}
                        href={`/${bank.slug}`}
                        className="flex max-w-[176px] md:max-w-[345px] w-[calc(50%-5px)] md:w-[calc(25%-16px)] flex-col gap-2 md:gap-4 rounded-[8px] md:rounded-[16px] p-3 md:p-6 border border-[#EEEEEE] bg-white hover:border-[#C7C5C3] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
                    >
                        <div className="flex items-center justify-center rounded-[6px] border border-[#C7C5C3] w-[28px] h-[28px] md:w-[56px] md:h-[56px] overflow-hidden shrink-0">
                            <img
                                src={`/assets/img/${bank.logo}`}
                                alt={bank.name}
                                className="w-full h-full object-contain p-[3px] md:p-[6px]"
                            />
                        </div>
                        <span className="text-[#090909] font-medium font-roboto text-sm md:text-[28px] md:leading-[38px] truncate w-full">
                            {bank.name}
                        </span>
                        <button className="gradient--main flex justify-center w-full py-[10px] md:py-[20px] px-[33px] md:px-[60px] rounded-[8px] md:rounded-[16px]">
                            <span className="text-white font-roboto font-semibold text-[10px] md:text-xl">
                                {t('banks.select')}
                            </span>
                        </button>
                    </LocaleLink>
                ))}
            </section>
        </div>
    );
}
