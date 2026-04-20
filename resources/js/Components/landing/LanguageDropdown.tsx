import { useEffect, useRef, useState } from "react";
import { LANGUAGES, type LanguageCode } from "./data";
import { TriangleDownIcon } from "./icons";

type Props = {
    value: LanguageCode;
    onChange: (code: LanguageCode) => void;
};

export function LanguageDropdown({ value, onChange }: Props) {
    const [open, setOpen] = useState(false);
    const rootRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!open) return;
        function handleClickOutside(event: MouseEvent) {
            if (
                rootRef.current &&
                !rootRef.current.contains(event.target as Node)
            ) {
                setOpen(false);
            }
        }
        document.addEventListener("mousedown", handleClickOutside);
        return () =>
            document.removeEventListener("mousedown", handleClickOutside);
    }, [open]);

    return (
        <div ref={rootRef} className="relative">
            <button
                type="button"
                onClick={() => setOpen((v) => !v)}
                aria-haspopup="listbox"
                aria-expanded={open}
                className="py-3 md:px-4.5 px-3.5 gap-2.5 items-center rounded-[11px] flex border-2 border-black hover:bg-black hover:text-white hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300 ease-out cursor-pointer group"
            >
                <span className="font-inter font-medium md:text-lg text-[12px] leading-[100%] text-black group-hover:text-white transition-colors duration-300">
                    {value}
                </span>
                <TriangleDownIcon
                    className={`transition-transform duration-300 fill-black group-hover:fill-white ${open ? "rotate-180" : ""}`}
                />
            </button>
            <ul
                role="listbox"
                className={`absolute right-0 top-[calc(100%+8px)] min-w-[180px] rounded-[11px] border-2 border-black bg-white overflow-hidden shadow-xl z-50 transition-all duration-300 origin-top ${
                    open
                        ? "opacity-100 scale-100 translate-y-0 pointer-events-auto"
                        : "opacity-0 scale-95 -translate-y-2 pointer-events-none"
                }`}
            >
                {LANGUAGES.map((item) => {
                    const active = item.code === value;
                    return (
                        <li
                            key={item.code}
                            role="option"
                            aria-selected={active}
                        >
                            <button
                                type="button"
                                onClick={() => {
                                    onChange(item.code);
                                    setOpen(false);
                                }}
                                className={`w-full flex items-center gap-3 px-4 py-3 text-left font-inter font-medium text-base transition-colors duration-200 cursor-pointer ${
                                    active
                                        ? "bg-gradient-to-r from-[#88CDF4] to-[#579FCF] text-white"
                                        : "text-black hover:bg-black hover:text-white"
                                }`}
                            >
                                <span className="text-xl leading-none">
                                    {item.flag}
                                </span>
                                <span className="flex-1">{item.label}</span>
                                <span className="text-sm opacity-70">
                                    {item.code}
                                </span>
                            </button>
                        </li>
                    );
                })}
            </ul>
        </div>
    );
}
