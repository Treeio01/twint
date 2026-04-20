import { STEPS, type Step } from "./data";

const HORIZONTAL_LINE_STYLE = {
    background: "linear-gradient(90deg, #F8866F 0%, #FDB875 100%)",
};
const VERTICAL_LINE_STYLE = {
    background: "linear-gradient(180deg, #F8866F 0%, #FDB875 100%)",
};

function StepItem({ step, isLast }: { step: Step; isLast: boolean }) {
    return (
        <div className="flex md:flex-1 md:flex-col md:items-center md:text-center flex-row items-start gap-4 md:gap-0 relative">
            <div className="relative md:w-full flex md:justify-center shrink-0">
                {!isLast && (
                    <>
                        <div
                            className="hidden md:block absolute top-1/2 left-1/2 w-full h-0.5 -translate-y-1/2"
                            style={HORIZONTAL_LINE_STYLE}
                        />
                        <div
                            className="md:hidden absolute left-1/2 top-full h-[calc(100%+32px)] w-0.5 -translate-x-1/2"
                            style={VERTICAL_LINE_STYLE}
                        />
                    </>
                )}
                <div className="gradient--main relative z-10 flex items-center justify-center md:w-14 md:h-14 w-10 h-10 rounded-full text-white font-roboto md:text-[20px] text-[14px] font-bold shadow-lg shadow-[#F8866F]/30">
                    {step.step}
                </div>
            </div>
            <div className="flex flex-col md:items-center items-start md:text-center text-left">
                <span className="md:mt-4 mt-0 md:text-[14px] text-[10px] font-roboto font-medium text-[#090909] leading-[100%]">
                    {step.label}
                </span>
                <span className="md:mt-3 mt-1.5 md:text-base text-[12px] font-bold font-roboto text-black leading-[140%] md:max-w-[240px]">
                    {step.title}
                </span>
                <span className="mt-2 md:text-[14px] text-[10px] font-roboto text-[#3C3C3C] leading-[140%] md:max-w-[240px]">
                    {step.description}
                </span>
            </div>
        </div>
    );
}

export function Steps() {
    return (
        <section className="flex w-full py-[60px] pb-[60px] md:pt-[140px] md:pb-[140px] max-w-[1440px] 1440:px-0 px-3">
            <div className="flex w-full md:flex-row flex-col md:justify-between md:items-start md:gap-4 gap-8 relative">
                {STEPS.map((step, idx) => (
                    <StepItem
                        key={step.step}
                        step={step}
                        isLast={idx === STEPS.length - 1}
                    />
                ))}
            </div>
        </section>
    );
}
