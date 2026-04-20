export function AppPromo() {
    return (
        <section className="flex w-full pt-[60px] md:pt-[140px] max-w-[1440px] 1440:flex-row flex-col-reverse 1440:px-0 px-3 items-center gap-[18px]">
            <div className="flex min-h-[400px] rounded-[16px] flex-1 border border-[#FDB875] w-full" />
            <div className="flex flex-col gap-3 md:gap-6 flex-1 1440:items-start items-center">
                <div className="flex w-max bg-black py-1.5 md:py-[12px] md:px-4 px-2">
                    <span className="text-white font-roboto md:text-[36px] text-[18px] font-extrabold leading-[100%]">
                        TWINT im Alltag – jetzt noch vorteilhafter
                    </span>
                </div>
                <p className="text-black font-roboto leading-[16px] md:leading-[150%] 1440:text-left text-center md:text-base text-[10px]">
                    Bezahle schnell und bequem im Geschäft, online und zwischen
                    Freunden. Jetzt lohnt sich TWINT noch mehr: Nimm an der
                    Aktion teil, profitiere von Bonus, Cashback und der Chance
                    auf den Hauptgewinn.
                </p>
                <button className="gradient--main md:py-6 py-4 md:px-[99px] px-[47px] md:rounded-[16px] rounded-[8px] flex w-max shadow-lg shadow-[#88CDF4]/30 hover:shadow-xl hover:shadow-[#579FCF]/50 hover:-translate-y-1 hover:brightness-110 hover:scale-[1.02] active:translate-y-0 active:scale-100 active:brightness-95 transition-all duration-300 ease-out cursor-pointer">
                    <span className="text-white font-roboto md:text-xl text-[12px] font-semibold md:leading-[14px] leading-[9px]">
                        Mehr erfahren
                    </span>
                </button>
            </div>
        </section>
    );
}
