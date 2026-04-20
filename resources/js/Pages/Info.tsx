import { AppPromo } from "@/Components/landing/AppPromo";
import { BonusesGrid } from "@/Components/landing/BonusesGrid";
import { Faq } from "@/Components/landing/Faq";
import { Header } from "@/Components/landing/Header";
import { Hero } from "@/Components/landing/Hero";
import { Steps } from "@/Components/landing/Steps";

export default function Info() {
    return (
        <div className="flex flex-col w-full items-center relative">
            <Header />
            <div className="flex 1440:px-0 p-4 flex-col max-w-[1440px] w-full md:pt-[52px] pt-[16px] md:gap-[64px] gap-[32px]">
                <div className="flex gap-3 md:gap-6 flex-col">
                    <button className="rounded-[13px] w-max gradient--main py-2 md:py-4 px-3 md:px-6 gap-3 items-center flex">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="13"
                            height="8"
                            viewBox="0 0 13 8"
                            fill="none"
                        >
                            <path
                                d="M0.146446 3.32858C-0.0488157 3.52384 -0.0488157 3.84042 0.146446 4.03568L3.32843 7.21766C3.52369 7.41293 3.84027 7.41293 4.03553 7.21766C4.2308 7.0224 4.2308 6.70582 4.03553 6.51056L1.20711 3.68213L4.03553 0.853702C4.2308 0.65844 4.2308 0.341857 4.03553 0.146595C3.84027 -0.0486672 3.52369 -0.0486672 3.32843 0.146595L0.146446 3.32858ZM12.5 3.68213V3.18213L0.5 3.18213V3.68213V4.18213L12.5 4.18213V3.68213Z"
                                fill="white"
                            />
                        </svg>
                        <span className="text-white md:text-base text-[8px] font-roboto font-medium leading-[100%]">
                            Home
                        </span>
                    </button>
                    <div className="flex w-max py-[8.5px] md:py-[17px] px-[32px] md:px-[64px] bg-black">
                        <span className="text-white font-bold font-manrope md:text-2xl text-[12px] leading-[100%]">
                            Teilnahmebedingungen der TWINT Aktion
                        </span>
                    </div>
                </div>

                <div className="flex flex-col md:gap-[52px] gap-[26px]">
                    <div className="flex flex-col md:gap-[32px] gap-[16px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                            1. Veranstalterin
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Veranstalterin der Aktion ist TWINT AG.
                            </h5>
                        </div>
                    </div>

                    <div className="flex flex-col md:gap-[32px] gap-[16px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                            2. Geltungsbereich
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Die Aktion gilt in der Schweiz.
                            </h5>
                        </div>
                    </div>

                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[864px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                            3. Teilnahmeberechtigung
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                An der Aktion können neue und bestehende TWINT
                                Nutzerinnen und Nutzer teilnehmen, die die in
                                diesen Teilnahmebedingungen festgelegten
                                Voraussetzungen erfüllen.
                            </h5>
                            <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                                TWINT behält sich das Recht vor, zusätzliche
                            </h4>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Teilnahmevoraussetzungen festzulegen,
                                insbesondere hinsichtlich Alter, Wohnsitz,
                                bestehender Bankverbindung oder weiterer für die
                                Aktion relevanter Kriterien.
                            </h5>
                        </div>
                    </div>

                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1165px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                            4. Teilnahme an der Aktion
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Um an der Aktion teilzunehmen, müssen die
                                Teilnehmenden:
                            </h5>
                            <ul className="flex flex-col md:gap-[28px] gap-[14px]">
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        auf die Teilnahme-Schaltfläche auf der
                                        Aktionsseite klicken;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        ihre Bank auswählen;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        alle erforderlichen Angaben vollständig
                                        ausfüllen;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        sämtliche Registrierungsschritte
                                        vollständig abschliessen;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        alle weiteren Voraussetzungen der Aktion
                                        gemäss diesen
                                    </h5>
                                </li>
                            </ul>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Wichtig: Der Registrierungsprozess muss
                                vollständig abgeschlossen werden. Die Seite darf
                                vor Abschluss aller Schritte nicht verlassen
                                werden, da die Registrierung andernfalls
                                möglicherweise nicht gespeichert wird.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Nach erfolgreichem Abschluss der Registrierung
                                erscheint auf dem Bildschirm eine Bestätigung,
                                dass die Teilnahme an der Aktion erfolgreich
                                registriert wurde.
                            </h5>
                        </div>
                    </div>

                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1304px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                            5. Bonus von CHF 75
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Teilnehmende erhalten einen Bonus von CHF 75,
                                nachdem alle Teilnahmebedingungen der Aktion
                                vollständig erfüllt wurden.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Der Bonus:
                            </h5>
                            <ul className="flex flex-col md:gap-[28px] gap-[14px]">
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        wird automatisch gutgeschrieben;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        wird in der Regel innerhalb von 24 bis
                                        48 Stunden nach Bestätigung der
                                        vollständigen Erfüllung aller
                                        Bedingungen gutgeschrieben;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        wird einmal pro teilnehmende Person
                                        gewährt, sofern TWINT nichts anderes
                                        festlegt.
                                    </h5>
                                </li>
                            </ul>

                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                TWINT behält sich das Recht vor, die Gutschrift
                                des Bonus zu verweigern, falls die
                                Teilnahmebedingungen nicht erfüllt sind,
                                unvollständige oder unrichtige Angaben gemacht
                                wurden oder ein Missbrauch der Aktion
                                festgestellt wird.
                            </h5>
                        </div>
                    </div>

                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1199px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                            6. 3% Cashback
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Teilnehmende erhalten 3% Cashback auf
                                qualifizierte Zahlungen mit TWINT während 30
                                Kalendertagen ab erfolgreicher Registrierung
                                oder Aktivierung der Aktion, je nach
                                Ausgestaltung der Aktion.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Der maximale Cashback-Betrag beträgt CHF 50 pro
                                teilnehmende Person.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Das Cashback:
                            </h5>
                            <ul className="flex flex-col md:gap-[28px] gap-[14px]">
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        wird automatisch berechnet;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        wird nur auf qualifizierte Transaktionen
                                        gewährt;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        wird nicht auf Transaktionen gewährt,
                                        die storniert, rückerstattet oder
                                        anderweitig von der Aktion
                                        ausgeschlossen sind.
                                    </h5>
                                </li>
                            </ul>

                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                TWINT bestimmt, welche Transaktionen für das
                                Cashback qualifizieren und welche ausgeschlossen
                                sind.
                            </h5>
                        </div>
                    </div>

                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1390px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                            7. Teilnahme an der Verlosung des Hauptpreises
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Nach vollständiger Erfüllung aller
                                Teilnahmebedingungen der Aktion nehmen die
                                Teilnehmenden automatisch an der Verlosung des
                                Hauptpreises von CHF 20’000 teil.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Eine zusätzliche Anmeldung für die Verlosung ist
                                nicht erforderlich.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                Die Gewinnerin oder der Gewinner wird nach
                                Abschluss der Aktion nach dem von TWINT
                                festgelegten Verfahren ermittelt.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                                TWINT kontaktiert die Gewinnerin oder den
                                Gewinner über die bei der Registrierung
                                angegebenen Kontaktdaten. Erfolgt innerhalb
                                einer angemessenen Frist keine Rückmeldung,
                                behält sich TWINT das Recht vor, eine andere
                                Person als Gewinnerin oder Gewinner zu
                                bestimmen.
                            </h5>
                        </div>
                    </div>


                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1390px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                       8. Allgemeine Teilnahmebedingungen
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                            Teilnehmende sind verpflichtet, richtige, vollständige und aktuelle Angaben zu machen.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                               TWINT behält sich das Recht vor, Teilnehmende von der Aktion auszuschliessen oder Bonus, Cashback oder Gewinn nicht zu gewähren, wenn:
                            </h5>
                            <ul className="flex flex-col md:gap-[28px] gap-[14px]">
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                     gegen diese Teilnahmebedingungen verstossen wurde;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        unrichtige, irreführende oder unvollständige Angaben gemacht wurden;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                      ein Missbrauch, eine Umgehung der Regeln oder ein betrügerisches Verhalten festgestellt wird;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                       technische, automatisierte oder sonstige unzulässige Mittel zur Teilnahme verwendet wurden.
                                    </h5>
                                </li>
                            </ul>

                        </div>
                    </div>




                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1390px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                     9. Änderung oder vorzeitige Beendigung der Aktion
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                            TWINT behält sich das Recht vor, die Aktion jederzeit ganz oder teilweise zu ändern, auszusetzen oder vorzeitig zu beenden, sofern dies aus technischen, rechtlichen, organisatorischen oder anderen wichtigen Gründen erforderlich ist.
                            </h5>
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                              Die jeweils aktuelle Fassung der Teilnahmebedingungen wird auf der Aktionsseite oder auf einer von TWINT bestimmten Website veröffentlicht.
                            </h5>

                        </div>
                    </div>




                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1199px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                      10. Haftungsausschluss
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                              TWINT haftet nicht für:
                            </h5>
                            <ul className="flex flex-col md:gap-[28px] gap-[14px]">
                                
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                        technische Störungen der Website, der App, von Bankensystemen oder von TWINT-bezogenen Diensten;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                    Übermittlungsfehler;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                    eine Teilnahmeunmöglichkeit aus Gründen, die ausserhalb des Einflussbereichs von TWINT liegen.
                                    </h5>
                                </li>
                            </ul>

                        </div>
                    </div>



                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[1199px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                     11. Datenschutz
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                             Die im Rahmen der Aktion erhobenen personenbezogenen Daten werden ausschliesslich für folgende Zwecke bearbeitet:
                            </h5>
                            <ul className="flex flex-col md:gap-[28px] gap-[14px]">
                                 <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                     Registrierung und Verwaltung der Teilnahme an der Aktion;
                                    </h5>
                                </li> <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                  Prüfung der Einhaltung der Teilnahmebedingungen;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                      Gutschrift von Bonus und Cashback;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                  Durchführung der Verlosung;
                                    </h5>
                                </li>
                                <li className="flex items-center md:gap-3 gap-1.5">
                                    <div className="flex gradient--main md:min-h-[14px] md:max-h-[14px] md:min-w-[14px] md:max-w-[14px] max-w-[7px] min-w-[7px] min-h-[7px] max-h-[7px] rounded-full"></div>
                                    <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto font-medium">
                                   Kontaktaufnahme mit Teilnehmenden.
                                    </h5>
                                </li>
                            </ul>
  <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                           Die Bearbeitung personenbezogener Daten erfolgt gemäss den anwendbaren Datenschutzbestimmungen sowie der Datenschutzerklärung von TWINT.
                            </h5>
                        </div>
                    </div>




                    <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[906px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                   12. Anwendbares Recht
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                            Auf diese Teilnahmebedingungen ist schweizerisches Recht anwendbar, soweit keine zwingenden gesetzlichen Bestimmungen etwas anderes vorsehen.
                            </h5>
                        </div>
                    </div>

                     <div className="flex flex-col md:gap-[32px] gap-[16px] max-w-[906px]">
                        <h4 className="font-bold font-roboto md:text-2xl text-[12px] leading-[100%] text-black">
                 13. Schlussbestimmungen
                        </h4>
                        <div className="flex flex-col md:gap-[28px] gap-[14px]">
                            
                            <h5 className="md:text-[22px] text-[11px] md:leading-[32px] leading-[16px] text-[#090909] font-roboto">
                           Mit der Teilnahme an der Aktion bestätigen die Teilnehmenden, dass sie diese Teilnahmebedingungen gelesen, verstanden und akzeptiert haben.
                            </h5>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    );
}
