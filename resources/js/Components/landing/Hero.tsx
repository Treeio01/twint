import { LocaleLink } from "@/Components/LocaleLink";
import { useT } from "@/i18n/useT";

export function Hero() {
    const t = useT();
    return (
        <section className="flex w-full md:pt-[120px] pt-[16px] md:pb-[140px] pb-[60px] relative justify-center">
            <img
                src="/assets/img/main-img.png"
                className="absolute md:flex hidden right-0 bottom-[20px] 1340:max-w-[882px] max-w-[450px]"
                alt=""
            />
            <img
                src="/assets/img/main-img-mobile.png"
                className="absolute md:hidden flex right-0 bottom-[60px] max-w-[260px]"
                alt=""
            />
            <div className="flex flex-col md:gap-9 gap-4.5 w-full max-w-[1440px] 1440:px-0 px-4 z-50">
                <div className="flex flex-col md:gap-9 gap-4.5 w-full md:max-w-[750px] max-w-[233px]">
                    <div className="flex w-max bg-black py-2 md:py-[17px] md:px-16 px-3.5">
                        <span className="text-white font-manrope md:text-2xl text-[12px] font-bold leading-[100%]">
                            {t('hero.badge')}
                        </span>
                    </div>
                    <h1 className="text-black font-roboto font-extrabold leading-[32px] md:leading-[65px] text-[28px] md:text-[56px]">
                        {t('hero.title')}
                    </h1>
                    <div className="flex flex-col md:gap-6 gap-3">
                        <p className="md:flex hidden font-manrope font-medium leading-[24px]">
                            {t('hero.description')}
                        </p>
                        <p className="font-manrope text-[10px] md:hidden flex font-medium leading-[16px]">
                            {t('hero.description')}
                        </p>
                        <LocaleLink href="/banks" className="gradient--main md:py-6 py-4 md:px-[99px] px-[47px] md:rounded-[16px] rounded-[8px] flex w-max shadow-lg shadow-[#88CDF4]/30 hover:shadow-xl hover:shadow-[#579FCF]/50 hover:-translate-y-1 hover:brightness-110 hover:scale-[1.02] active:translate-y-0 active:scale-100 active:brightness-95 transition-all duration-300 ease-out cursor-pointer">
                            <span className="text-white font-roboto md:text-xl text-[12px] font-semibold md:leading-[14px] leading-[9px]">
                                {t('hero.cta')}
                            </span>
                        </LocaleLink>
                    </div>
                </div>
            </div>
        </section>
    );
}
