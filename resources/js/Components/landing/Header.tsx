import { useState } from "react";
import { BankSearch } from "./BankSearch";
import type { LanguageCode } from "./data";
import { LanguageDropdown } from "./LanguageDropdown";

const NAV_LINKS = [
    { label: "Betalen", href: "" },
    { label: "Verzekeren", href: "" },
];

const LINK_CLASSES =
    "relative text-[#3C3C3C] text-[14px] md:text-[20px] font-inter font-medium leading-[100%] hover:text-black transition-colors duration-300 ease-in-out after:content-[''] after:absolute after:left-0 after:-bottom-1.5 after:h-[2px] after:w-0 after:bg-gradient-to-r after:from-[#88CDF4] after:to-[#579FCF] after:transition-all after:duration-300 hover:after:w-full";

export function Header() {
    const [lang, setLang] = useState<LanguageCode>("DE");

    return (
        <header className="w-full max-w-[1440px] py-3.5 justify-between flex md:gap-0 gap-[24px] flex-col md:flex-row md:items-center items-start 1440:px-0 px-4">
            <div className="flex w-full items-center justify-between">
                <div className="flex gap-[121px] items-center">
                    <img
                        src="/assets/img/logo.svg"
                        className="md:max-w-[153px] max-w-[103px]"
                        alt=""
                    />
                    <ul className="md:flex hidden items-center gap-[36px]">
                        {NAV_LINKS.map((link) => (
                            <li key={link.label}>
                                <a href={link.href} className={LINK_CLASSES}>
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
                <div className="flex items-center md:gap-3 gap-2">
                    <BankSearch />
                    <button className="py-3.5 px-5.5 rounded-[11px] flex bg-linear-to-r from-[#88CDF4] to-[#579FCF] shadow-md shadow-[#88CDF4]/30 hover:shadow-lg hover:shadow-[#579FCF]/50 hover:-translate-y-0.5 hover:brightness-110 active:translate-y-0 active:brightness-95 transition-all duration-300 ease-out cursor-pointer">
                        <span className="text-white font-inter font-medium md:text-lg text-[12px] leading-[100%]">
                            Open an account
                        </span>
                    </button>
                    <LanguageDropdown value={lang} onChange={setLang} />
                </div>
            </div>
            <ul className="md:hidden flex items-center gap-[45px]">
                {NAV_LINKS.map((link) => (
                    <li key={link.label}>
                        <a href={link.href} className={LINK_CLASSES}>
                            {link.label}
                        </a>
                    </li>
                ))}
            </ul>
        </header>
    );
}
