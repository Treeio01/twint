import type { AnchorHTMLAttributes } from "react";
import { useLocaleContext } from "@/i18n/LocaleProvider";

type Props = Omit<AnchorHTMLAttributes<HTMLAnchorElement>, 'href'> & { href: string };

export function LocaleLink({ href, ...props }: Props) {
    const { locale } = useLocaleContext();
    const full = href === '/' ? `/${locale}` : `/${locale}${href}`;
    return <a href={full} {...props} />;
}

export function useLocaleHref() {
    const { locale } = useLocaleContext();
    return (path: string) => path === '/' ? `/${locale}` : `/${locale}${path}`;
}
