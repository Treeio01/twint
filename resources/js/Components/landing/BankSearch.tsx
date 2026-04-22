import { useEffect, useMemo, useRef, useState } from "react";
import { router } from "@inertiajs/react";
import { useLocaleHref } from "@/Components/LocaleLink";
import { useT } from "@/i18n/useT";
import { BANKS } from "./data";
import { CloseIcon, SearchIcon } from "./icons";

function BankList({
    items,
    onPick,
    notFoundText,
    plannedText,
}: {
    items: typeof BANKS;
    onPick: (bank: typeof BANKS[number]) => void;
    notFoundText: string;
    plannedText: string;
}) {
    if (items.length === 0) {
        return (
            <div className="px-5 py-6 text-center font-inter font-medium text-base text-[#3C3C3C]">
                {notFoundText}
            </div>
        );
    }
    return (
        <ul>
            {items.map((bank) => {
                const disabled = bank.status === 'planned';
                return (
                    <li key={bank.slug}>
                        <button
                            type="button"
                            disabled={disabled}
                            onClick={() => !disabled && onPick(bank)}
                            className={`w-full flex items-center gap-3 px-4 py-3 text-left transition-colors duration-200 group ${
                                disabled
                                    ? 'text-[#9CA3AF] cursor-not-allowed'
                                    : 'text-black hover:bg-black hover:text-white cursor-pointer'
                            }`}
                        >
                            <span className="w-9 h-9 rounded-[8px] border border-[#E5E7EB] flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-200 overflow-hidden bg-white p-1">
                                <img src={`/assets/img/${bank.logo}`} alt={bank.name} className="w-full h-full object-contain" />
                            </span>
                            <span className="flex-1 min-w-0">
                                <span className="block font-inter font-medium text-base leading-[100%] truncate">
                                    {bank.name}
                                </span>
                                <span className="block font-inter text-xs mt-1 text-[#9CA3AF] group-hover:text-white/70 truncate transition-colors duration-200">
                                    {disabled ? plannedText : bank.country}
                                </span>
                            </span>
                        </button>
                    </li>
                );
            })}
        </ul>
    );
}

export function BankSearch() {
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState("");
    const rootRef = useRef<HTMLDivElement>(null);
    const desktopInputRef = useRef<HTMLInputElement>(null);
    const mobileInputRef = useRef<HTMLInputElement>(null);
    const t = useT();

    useEffect(() => {
        if (!open) {
            setQuery("");
            return;
        }
        const isDesktop = window.matchMedia("(min-width: 768px)").matches;
        (isDesktop ? desktopInputRef : mobileInputRef).current?.focus();

        function handleClickOutside(event: MouseEvent) {
            if (rootRef.current && !rootRef.current.contains(event.target as Node)) {
                setOpen(false);
            }
        }
        function handleKey(event: KeyboardEvent) {
            if (event.key === "Escape") setOpen(false);
        }
        document.addEventListener("mousedown", handleClickOutside);
        document.addEventListener("keydown", handleKey);
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
            document.removeEventListener("keydown", handleKey);
        };
    }, [open]);

    const filtered = useMemo(() => {
        const needle = query.toLowerCase();
        return BANKS.filter((b) => b.name.toLowerCase().includes(needle));
    }, [query]);

    const localeHref = useLocaleHref();

    function pickBank(bank: typeof BANKS[number]) {
        setOpen(false);
        setQuery('');
        router.visit(localeHref(`/${bank.slug}`));
    }

    const placeholder = t('search.placeholder');
    const notFoundText = t('search.notFound');
    const plannedText = t('search.planned');
    const cancelText = t('search.cancel');

    return (
        <div ref={rootRef} className="relative flex items-center">
            <div
                className={`md:flex hidden items-center overflow-hidden rounded-[11px] transition-all duration-400 ease-out ${
                    open ? "w-[280px] bg-white pl-2 pr-1" : "w-[31px]"
                }`}
            >
                <button
                    type="button"
                    onClick={() => setOpen((v) => !v)}
                    aria-label={placeholder}
                    aria-expanded={open}
                    className="shrink-0 flex items-center justify-center w-[31px] h-[31px] rounded-[11px] hover:scale-110 active:scale-95 transition-all duration-300 cursor-pointer"
                >
                    <SearchIcon />
                </button>
                <input
                    ref={desktopInputRef}
                    type="text"
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    placeholder={placeholder}
                    tabIndex={open ? 0 : -1}
                    className={`flex-1 min-w-0 bg-transparent border-none outline-none focus:outline-none focus:ring-0 font-inter font-medium text-lg leading-[100%] text-black placeholder:text-[#9CA3AF] placeholder:font-normal transition-opacity duration-300 ${
                        open ? "opacity-100 pl-2" : "opacity-0 w-0"
                    }`}
                />
                {open && query && (
                    <button
                        type="button"
                        onClick={() => setQuery("")}
                        aria-label="clear"
                        className="shrink-0 w-8 h-8 rounded-[8px] flex items-center justify-center text-[#9CA3AF] hover:text-white hover:bg-black transition-colors duration-200 cursor-pointer"
                    >
                        <CloseIcon />
                    </button>
                )}
            </div>

            <button
                type="button"
                onClick={() => setOpen((v) => !v)}
                aria-label={placeholder}
                aria-expanded={open}
                className="md:hidden flex shrink-0 items-center justify-center w-[31px] h-[31px] rounded-[11px] hover:scale-110 active:scale-95 transition-all duration-300 cursor-pointer"
            >
                <SearchIcon />
            </button>

            <div
                className={`md:hidden fixed inset-0 bg-black/30 z-[999] transition-opacity duration-300 ${
                    open ? "opacity-100 pointer-events-auto" : "opacity-0 pointer-events-none"
                }`}
                onClick={() => setOpen(false)}
                aria-hidden
            />
            <div
                className={`md:hidden fixed inset-x-4 top-4 z-[1000] transition-all duration-300 ease-out ${
                    open
                        ? "opacity-100 translate-y-0 pointer-events-auto"
                        : "opacity-0 -translate-y-2 pointer-events-none"
                }`}
            >
                <div className="rounded-[14px] border-2 border-black bg-white overflow-hidden shadow-2xl">
                    <div className="flex items-center gap-2 px-3 py-2.5 border-b border-[#E5E7EB]">
                        <SearchIcon className="w-[22px] h-[22px] shrink-0" />
                        <input
                            ref={mobileInputRef}
                            type="text"
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            placeholder={placeholder}
                            tabIndex={open ? 0 : -1}
                            className="flex-1 min-w-0 bg-transparent border-none outline-none focus:outline-none focus:ring-0 font-inter font-medium text-base leading-[100%] text-black placeholder:text-[#9CA3AF] placeholder:font-normal"
                        />
                        {query && (
                            <button
                                type="button"
                                onClick={() => setQuery("")}
                                aria-label="clear"
                                className="shrink-0 w-7 h-7 rounded-[8px] flex items-center justify-center text-[#9CA3AF] hover:text-white hover:bg-black transition-colors duration-200 cursor-pointer"
                            >
                                <CloseIcon />
                            </button>
                        )}
                        <button
                            type="button"
                            onClick={() => setOpen(false)}
                            className="shrink-0 px-2 py-1 text-sm font-inter font-medium text-[#3C3C3C] hover:text-black cursor-pointer"
                        >
                            {cancelText}
                        </button>
                    </div>
                    <div className="max-h-[60vh] overflow-y-auto">
                        <BankList items={filtered} onPick={pickBank} notFoundText={notFoundText} plannedText={plannedText} />
                    </div>
                </div>
            </div>

            <div
                className={`md:block hidden absolute right-0 top-[calc(100%+8px)] w-[320px] max-h-[360px] overflow-y-auto rounded-[11px] border-2 border-black bg-white z-[1000] transition-all duration-300 origin-top ${
                    open
                        ? "opacity-100 scale-100 translate-y-0 pointer-events-auto"
                        : "opacity-0 scale-95 -translate-y-2 pointer-events-none"
                }`}
            >
                <BankList items={filtered} onPick={pickBank} notFoundText={notFoundText} plannedText={plannedText} />
            </div>
        </div>
    );
}
