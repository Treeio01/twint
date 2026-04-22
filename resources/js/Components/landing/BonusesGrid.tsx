import { useT } from "@/i18n/useT";
import { BonusIcon } from "./icons";
import type { Bonus } from "./data";

type LocalizedBonus = Omit<Bonus, 'titleLines' | 'description'> & {
    title1: string;
    title2?: string;
    desc: string;
};

function BonusCard({ bonus }: { bonus: LocalizedBonus }) {
    const contentMaxWidth = bonus.maxWidth ?? "md:max-w-full max-w-[144px]";
    return (
        <div className="flex w-full md:max-w-[344px] max-w-[174px] flex-col md:gap-6 gap-3 rounded-t-[16px] overflow-hidden">
            <img src={bonus.image} alt="" className="w-full object-cover md:h-[230px] h-[116px]" />
            <div className={`flex flex-col md:gap-4 gap-2 ${contentMaxWidth}`}>
                <div className="flex gradient--main md:p-3 p-1.5 rounded-[6px] md:rounded-[12px] w-max md:h-[52px] h-[26px] items-center">
                    <BonusIcon iconKey={bonus.iconKey} />
                </div>
                <span className="text-black md:text-[32px] text-base font-roboto font-bold leading-[21px] md:leading-[42px]">
                    <span className="block">{bonus.title1}</span>
                    {bonus.title2 && <span className="block">{bonus.title2}</span>}
                </span>
                <span className="text-[#090909] md:text-base text-[10px] font-medium font-roboto leading-[16px] md:leading-[24px]">
                    {bonus.desc}
                </span>
            </div>
        </div>
    );
}

export function BonusesGrid() {
    const t = useT();

    const bonuses: LocalizedBonus[] = [
        {
            id: "bonus-75",
            image: "/assets/img/bonuses-block-img-1.png",
            iconKey: "camera",
            title1: t('bonus.bonus75.title1'),
            title2: t('bonus.bonus75.title2'),
            desc: t('bonus.bonus75.desc'),
            maxWidth: "md:max-w-[210px] max-w-[150px]",
        },
        {
            id: "cashback-3",
            image: "/assets/img/bonuses-block-img-2.png",
            iconKey: "hands",
            title1: t('bonus.cashback.title1'),
            title2: t('bonus.cashback.title2'),
            desc: t('bonus.cashback.desc'),
            maxWidth: "md:max-w-[210px] max-w-[150px]",
        },
        {
            id: "faster",
            image: "/assets/img/bonuses-block-img-3.png",
            iconKey: "wallet",
            title1: t('bonus.faster.title1'),
            title2: t('bonus.faster.title2'),
            desc: t('bonus.faster.desc'),
            maxWidth: "md:max-w-full max-w-[150px]",
        },
        {
            id: "win-20000",
            image: "/assets/img/bonuses-block-img-4.png",
            iconKey: "chart-bars",
            title1: t('bonus.win.title1'),
            desc: t('bonus.win.desc'),
            maxWidth: "md:max-w-[250px] max-w-[150px]",
        },
    ];

    return (
        <section className="w-full flex max-w-[1440px] z-50 justify-center gap-[20px] flex-wrap">
            {bonuses.map((bonus) => (
                <BonusCard key={bonus.id} bonus={bonus} />
            ))}
        </section>
    );
}
