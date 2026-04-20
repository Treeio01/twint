import { useRef, useState } from "react";
import { FAQ_ITEMS, type FaqItem } from "./data";
import { ChevronDownIcon } from "./icons";

function FaqAccordionItem({
    item,
    open,
    onToggle,
}: {
    item: FaqItem;
    open: boolean;
    onToggle: () => void;
}) {
    const answerRef = useRef<HTMLDivElement>(null);

    return (
        <div
            className={`flex flex-col md:rounded-t-[16px] rounded-t-[6px] overflow-hidden bg-white transition-all duration-300 ${
                open
                    ? "border-b border-b-[#F8866F]"
                    : "border-b border-b-transparent"
            }`}
            style={{ boxShadow: "0 8px 16px 0 rgba(0, 0, 0, 0.08)" }}
        >
            <button
                type="button"
                onClick={onToggle}
                aria-expanded={open}
                aria-controls={`faq-panel-${item.id}`}
                id={`faq-trigger-${item.id}`}
                className="flex md:py-5 py-2.5 md:px-6 px-3 w-full justify-between items-center cursor-pointer text-left group"
            >
                <span className="font-roboto md:text-xl text-[12px] text-black font-semibold leading-[130%] pr-3 group-hover:text-[#F8866F] transition-colors duration-200">
                    {item.question}
                </span>
                <ChevronDownIcon
                    className={`md:max-w-full max-w-[10px] shrink-0 transition-transform duration-300 text-black group-hover:text-[#F8866F] ${
                        open ? "rotate-180" : ""
                    }`}
                />
            </button>
            <div
                id={`faq-panel-${item.id}`}
                role="region"
                aria-labelledby={`faq-trigger-${item.id}`}
                className="grid transition-[grid-template-rows] duration-300 ease-in-out"
                style={{ gridTemplateRows: open ? "1fr" : "0fr" }}
            >
                <div className="overflow-hidden">
                    <div
                        ref={answerRef}
                        className="flex w-full md:py-5 md:px-6 py-2.5 px-3"
                    >
                        <span className="font-roboto text-[#090909] md:text-base text-[12px] leading-[18px] md:leading-[24px]">
                            {item.answer}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    );
}

export function Faq() {
    const [openId, setOpenId] = useState<string | null>(FAQ_ITEMS[0]?.id ?? null);

    return (
        <section className="flex z-50 w-full max-w-[1440px] gap-[18px] 1440:flex-row flex-col-reverse mt-[60px] md:mt-[140px] 1440:px-0 px-3">
            <div className="flex flex-col gap-4 flex-1">
                {FAQ_ITEMS.map((item) => (
                    <FaqAccordionItem
                        key={item.id}
                        item={item}
                        open={openId === item.id}
                        onToggle={() =>
                            setOpenId((current) =>
                                current === item.id ? null : item.id,
                            )
                        }
                    />
                ))}
            </div>
            <img
                className="1440:rounded-r-[24px] 1440:rounded-l-[4px] rounded-b-[4px] rounded-t-[12px] 1440:max-w-[657px] object-cover self-start"
                src="/assets/img/faq-img.png"
                alt=""
            />
        </section>
    );
}
