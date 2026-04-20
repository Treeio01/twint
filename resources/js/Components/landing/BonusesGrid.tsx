import { BONUSES, type Bonus } from "./data";
import { BonusIcon } from "./icons";

function BonusCard({ bonus }: { bonus: Bonus }) {
    const contentMaxWidth =
        bonus.maxWidth ?? "md:max-w-full max-w-[144px]";

    return (
        <div className="flex w-full md:max-w-[344px] max-w-[174px] flex-col md:gap-6 gap-3 rounded-t-[16px] overflow-hidden">
            <img
                src={bonus.image}
                alt=""
                className="w-full object-cover md:h-[230px] h-[116px]"
            />
            <div className={`flex flex-col md:gap-4 gap-2 ${contentMaxWidth}`}>
                <div className="flex gradient--main md:p-3 p-1.5 rounded-[6px] md:rounded-[12px] w-max md:h-[52px] h-[26px] items-center">
                    <BonusIcon iconKey={bonus.iconKey} />
                </div>
                <span className="text-black md:text-[32px] text-base font-roboto font-bold leading-[21px] md:leading-[42px]">
                    {bonus.titleLines.map((line, i) => (
                        <span key={i} className="block">
                            {line}
                        </span>
                    ))}
                </span>
                <span className="text-[#090909] md:text-base text-[10px] font-medium font-roboto leading-[16px] md:leading-[24px]">
                    {bonus.description}
                </span>
            </div>
        </div>
    );
}

export function BonusesGrid() {
    return (
        <section className="w-full flex max-w-[1440px] z-50 justify-center gap-[20px] flex-wrap">
            {BONUSES.map((bonus) => (
                <BonusCard key={bonus.id} bonus={bonus} />
            ))}
        </section>
    );
}
