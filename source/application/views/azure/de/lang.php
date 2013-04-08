<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   lang
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: lang.php 28697 2010-06-29 11:09:58Z vilma $
 */


/* -----------------------
--  ADAPTING THIS FILE  --
--------------------------
If you want to adapt this language file, use cust_lang.php instead.
Further information is available in the manual at
http://www.oxid-esales.com/en/resources/help-faq/eshop-manual/editing-language-files
*/

$sLangName  = "Deutsch";

// -------------------------------
// RESOURCE IDENTIFIER = STRING
// -------------------------------
$aLang = array(

'charset'                                                     => "ISO-8859-15",
'fullDateFormat'                                              => "d.m.Y H:i:s",
'simpleDateFormat'                                            => "d.m.Y",


'BETA_NOTE'                                                   => "Willkommen zum Release-Kandidat ",
'BETA_NOTE_MIDDLE'                                            => " von OXID eShop ",
'BETA_NOTE_FAQ'                                               => ". Häufig gestellte Fragen und Antworten sind in der <a href='http://wiki.oxidforge.org/Development/Beta' class=\"external\">FAQ</a> gelistet.",

'BETA_NOTE_CLOSE'                                             => "schließen",

'ADD_RECOMM_ADDRECOMMLINK1'                                   => "Es liegen zur Zeit keine Lieblingslisten vor. Um eine neue Lieblingsliste anzulegen, klicken Sie",
'ADD_RECOMM_ADDRECOMMLINK2'                                   => "hier",
'DETAILS_ARTNUMBER'                                           => "ArtNr.:",
'DETAILS_LOCATOR_PRODUCT'                                     => "ARTIKEL",
'DETAILS_PLUSSHIPPING2'                                       => "Versandkosten.",
'DETAILS_PRICEALARM'                                          => "<b>[!]</b> Preisalarm!",
'DETAILS_VPE_MESSAGE_1'                                       => "Dieser Artikel kann nur in Verpackungseinheiten zu je ",
'DETAILS_VPE_MESSAGE_2'                                       => " erworben werden.",
'DETAILS_CHOOSEVARIANT'                                       => "Bitte wählen Sie eine Variante",
'EMAIL_INVITE_HTML_INVITETOSHOP'                              => "Eine Einladung von",
'EMAIL_INVITE_HTML_INVITETOSHOP2'                             => "",
'EMAIL_INVITE_HTML_INVITETOSHOP3'                             => "zu besuchen.",
'EMAIL_ORDER_CUST_HTML_ARTNOMBER'                             => "Artnr.:",
'EMAIL_ORDER_CUST_HTML_NONE'                                  => "KEINE",
'EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEVAT1'                     => "Aufsch. Zahl. MwSt.",
'EMAIL_ORDER_CUST_HTML_PAYMENTMETHOD'                         => "Die Bezahlung erfolgt mit:",
'EMAIL_ORDER_CUST_HTML_PLUSTAX1'                              => "zzgl. MwSt.",
'EMAIL_ORDER_CUST_HTML_PLUSTAX21'                             => "zzgl. MwSt.",
'EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER'                       => "Der Versand erfolgt mit",
'EMAIL_ORDER_CUST_HTML_SHIPPINGNET'                           => "Versandkosten (netto):",
'EMAIL_ORDER_CUST_HTML_TAX1'                                  => "zzgl. MwSt.",
'EMAIL_ORDER_CUST_HTML_TSPROTECTIONCHARGETAX1'                => "zzgl. MwSt.",
'EMAIL_ORDER_OWNER_HTML_MESSAGE'                              => "Mitteilung:",
'EMAIL_ORDER_OWNER_HTML_PAYMENTINFOOFF'                       => "<b>BEZAHLINFORMATIONEN AUSGESCHALTET</b> - um diese einzuschalten bitte application/views/[theme]/email/html/order_owner.tpl aendern.",
'EMAIL_SUGGEST_HTML_MENYGREETINGS'                            => "Viele Grüsse,",

'FOOTER_CATEGORIES'                                           => "KATEGORIEN",
'FOOTER_DISTRIBUTORS'                                         => "LIEFERANTEN",
'FOOTER_INFORMATION'                                          => "INFORMATIONEN",
'FOOTER_MANUFACTURERS'                                        => "MARKE",
'FOOTER_SERVICES'                                             => "SERVICE",
'FORM_CONTACT_SEND'                                           => "Nachricht abschicken",
'FORM_FIELDSET_USER_ACCOUNT_CONFIRMPWD'                       => "Passwort wiederholen:",
'FORM_FIELDSET_USER_BILLING_ADDITIONALINFO_TOOLTIP'           => "",
'FORM_FIELDSET_USER_SHIPPING_ADDITIONALINFO2_TOOLTIP'         => "",
'FORM_LOGIN_ACCOUNT_FORGOTPWD'                                => "Passwort vergessen?",
'FORM_NEWSLETTER_FIRSTNAME'                                   => "Vorname",
'FORM_PRICEALARM_SEND'                                        => "abschicken",
'FORM_PRIVATESALES_INVITE_FROM'                               => "From:",
'FORM_PRIVATESALES_INVITE_MESSAGE1'                           => "Hallo! Heute habe ich den interessanten Shop",
'FORM_PRIVATESALES_INVITE_MESSAGE2'                           => "für dich gefunden. Einfach auf den Link unten klicken, und du gelangst direkt zum Shop.",
'FORM_PRIVATESALES_INVITE_SEND'                               => "Einladung abschicken",
'FORM_PRIVATESALES_INVITE_SENDEREMAIL'                        => "E-Mail des Absenders:",
'FORM_RECOMMENDATION_EDIT_LISTTITLE'                          => "Überschrift",
'FORM_REGISTER_BILLINGADDRESS'                                => "Rechnungsadresse",
'FORM_REGISTER_COMPLETEMARKEDFIELDS'                          => "(Bitte alle fett beschrifteten Pflichtfelder ausfüllen.)",
'FORM_REGISTER_IAGREETORIGHTOFWITHDRAWAL1'                    => "Ich wurde über mein",
'FORM_REGISTER_IAGREETORIGHTOFWITHDRAWAL3'                    => "informiert.",
'FORM_REGISTER_IAGREETOTERMS1'                                => "Ich habe die",
'FORM_REGISTER_IAGREETOTERMS3'                                => "gelesen und erkläre mich mit ihnen einverstanden.",
'FORM_SUGGEST_MESSAGE1'                                       => "Hallo, Heute habe ich den interessanten Shop",
'FORM_SUGGEST_MESSAGE2'                                       => "für dich gefunden. Einfach auf den Link unten klicken, und du gelangst direkt zum Shop.",
'FORM_USER_BILLINGADDRESS'                                    => "Rechnungsadresse",
'FORM_USER_PASSWORD_CONFIRMPASSWORD'                          => "Passwort wiederholen:",
'FORM_WISHLIST_SEARCH_SEARCH'                                 => "Jetzt suchen",
'FORM_WISHLIST_SUGGEST_BUYFORME1'                             => "Hallo, ich habe mir hier bei",
'FORM_WISHLIST_SUGGEST_BUYFORME2'                             => "einen Wunschzettel angelegt. Es wäre toll, wenn du mir davon etwas kaufen könntest.",
'FORM_WISHLIST_SUGGEST_ERRWRONGEMAIL'                         => "Fehler beim Versenden - bitte E-Mail-Adresse überprüfen.",
'NEWSLETTER_COMPLETEALLFIELEDS'                               => "Bitte alle Felder mit * ausfüllen!",
'PAGE_ACCOUNT_DASHBOARD_LOGOUT'                               => "LOGOUT",
'PAGE_ACCOUNT_FORGOTPWD_UPDATEPASSWORD'                       => "Neues Passwort speichern",
'PAGE_ACCOUNT_REGISTER_OPENACCOUNT'                           => "Neues Konto eröffnen",
'PAGE_ACCOUNT_WISHLIST_SENDSUCCESSFULLY1'                     => "Ihr Wunschzettel wurde erfolgreich an",
'PAGE_ACCOUNT_WISHLIST_SENDSUCCESSFULLY2'                     => "verschickt!",
'PAGE_CHECKOUT_BASKETCONTENTS_ARTNOMBER'                      => "Artikel Nr.",
'PAGE_CHECKOUT_BASKETCONTENTS_COUPONNOTACCEPTED1'             => "Ihr Gutschein",
'PAGE_CHECKOUT_BASKETCONTENTS_COUPONNOTACCEPTED2'             => "wurde abgewiesen.",
'PAGE_CHECKOUT_BASKETCONTENTS_GRANDTOTAL'                     => "Gesamtsumme",
'PAGE_CHECKOUT_BASKETCONTENTS_PAYMENTTAX1'                    => "Aufsch. Zahl. MwSt.",
'PAGE_CHECKOUT_BASKETCONTENTS_PAYMENTTAX2'                    => "% Betrag",
'PAGE_CHECKOUT_BASKETCONTENTS_PLUSTAX1'                       => "zzgl. MwSt.",
'PAGE_CHECKOUT_BASKETCONTENTS_PLUSTAX2'                       => "% Betrag",
'PAGE_CHECKOUT_BASKETCONTENTS_REASON'                         => "Grund",
'PAGE_CHECKOUT_BASKETCONTENTS_TAX1'                           => "zzgl. MwSt.",
'PAGE_CHECKOUT_BASKETCONTENTS_TAX2'                           => "% Betrag",
'PAGE_CHECKOUT_BASKETCONTENTS_TOTAL'                          => "Gesamtsumme",
'PAGE_CHECKOUT_BASKETCONTENTS_TOTALGROSS'                     => "Summe Artikel (brutto)",
'PAGE_CHECKOUT_BASKETCONTENTS_TOTALNET'                       => "Summe Artikel (netto)",
'PAGE_CHECKOUT_BASKETCONTENTS_TSPROTECTIONCHARGETAX1'         => "zzgl. MwSt.",
'PAGE_CHECKOUT_BASKETCONTENTS_WRAPPINGTAX1'                   => "zzgl. MwSt.",
'PAGE_CHECKOUT_BASKETCONTENTS_YOURMESSAGE'                    => "Ihr Text:",
'PAGE_CHECKOUT_ORDER_BILLINGADDRESS'                          => "Rechnungsadresse",
'PAGE_CHECKOUT_ORDER_COUPONNOTACCEPTED1'                      => "Ihr Gutschein",
'PAGE_CHECKOUT_ORDER_COUPONNOTACCEPTED2'                      => "wurde abgewiesen.",
'PAGE_CHECKOUT_ORDER_SHIPPINGADDRESS'                         => "Lieferadresse",
'PAGE_CHECKOUT_PAYMENT_ACCOUNTNUMBER'                         => "Kontonummer:",
'PAGE_CHECKOUT_PAYMENT_COMLETEALLFIELDS'                      => "Bitte alle Felder richtig ausfüllen!",
'PAGE_CHECKOUT_PAYMENT_EMPTY_TEXT'                            => '<p>Derzeit ist keine Versandart für dieses Land definiert.</p> <p>Wir werden versuchen, Liefermöglichkeiten zu finden und Sie über die Versandkosten informieren.</p>',
'PAGE_CHECKOUT_PAYMENT_INCLUDEVAT'                            => "inkl. MwSt.",
'PAGE_CHECKOUT_PAYMENT_PLUSCODCHARGE1'                        => "zuzüglich",
'PAGE_CHECKOUT_PAYMENT_PLUSCODCHARGE2'                        => "Nachnahmegebühr",
'PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT'                      => "Der Trusted Shops Käuferschutz sichert Ihren Online-Kauf ab. Mit der Übermittlung und",
'PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT2'                     => "Speicherung",
'PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT3'                     => "meiner E-Mail-Adresse zur Abwicklung des Käuferschutzes durch Trusted Shops bin ich einverstanden.",
'PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT4'                     => "Bedingungen",
'PAGE_CHECKOUT_PAYMENT_TSPROTECTIONTEXT5'                     => "für den Käuferschutz.",
'PAGE_CHECKOUT_THANKYOU_REGISTEREDYOUORDERNO1'                => "Ihre Bestellung ist unter der Nummer",
'PAGE_CHECKOUT_THANKYOU_REGISTEREDYOUORDERNO2'                => "bei uns registriert.",
'PAGE_CHECKOUT_THANKYOU_THANKYOU1'                            => "Vielen Dank für Ihre Bestellung",
'PAGE_CHECKOUT_THANKYOU_THANKYOU2'                            => "im",
'PAGE_CHECKOUT_USER'                                          => "Versenden",
'PAGE_CHECKOUT_USER_OPTION_REGISTRATION_DESCRIPTION'          => "Mit einem persönlichen Kundenkonto haben Sie folgende Vorteile:<br> - Verwaltung der Lieferadressen<br> - Prüfung des Bestellstatus<br> - Bestellhistorie<br> - persönlicher Merkzettel<br> - persönliche Wunschliste<br> - Newsletter-Verwaltung<br> - Sonder- und Rabattaktionen",
'PAGE_CHECKOUT_WRAPPING_CANCEL'                               => "Abbrechen",
'PAGE_CHECKOUT_WRAPPING_NONE'                                 => "KEINE",
'PAGE_DETAILS_THANKYOUMESSAGE1'                               => "Vielen Dank für Ihre Nachricht an",
'PAGE_DETAILS_THANKYOUMESSAGE2'                               => ".",
'PAGE_DETAILS_THANKYOUMESSAGE3'                               => "Sie bekommen eine Nachricht von uns sobald der Preis unter",
'PAGE_DETAILS_THANKYOUMESSAGE4'                               => "fällt.",
'PAGE_INFO_CONTACT_THANKYOU1'                                 => "Vielen Dank für Ihre Nachricht an",
'PAGE_INFO_CONTACT_THANKYOU2'                                 => ".",
'PAGE_INFO_NEWS_LATESTNEWSBY'                                 => "Neuigkeiten bei",
'PAGE_WISHLIST_PRODUCTS_PRODUCTS1'                            => "Diese Artikel hat sich",
'PAGE_WISHLIST_PRODUCTS_PRODUCTS2'                            => " gewünscht. Wenn Sie ihr/ihm eine Freude machen wollen, dann kaufen Sie einen oder mehrere von diesen Artikeln.",
'WIDGET_FOOTER_MANUFACTURERS_MORE'                            => "Mehr...",
'WIDGET_PRODUCT_PRODUCT_DETAILS'                              => "Produktdetails",
'WIDGET_SERVICES_GUESTBOOK'                                   => "Gästebuch",
'WIDGET_SERVICES_HOME'                                        => "Home",
'WIDGET_TRUSTEDSHOPS_ITEM_ALTTEXT'                            => "Mehr Informationen",
'allBrands'                                                   => "Alle Marken",
'byBrand'                                                     => "Nach Marke",
'byManufacturer'                                              => "Nach Hersteller",
'byVendor'                                                    => "Nach Lieferant",
'grid'                                                        => "Galerie",
'infogrid'                                                    => "Galerie zweispaltig",
'line'                                                        => "Liste",
'searchResult'                                                => 'Suchergebnis für "%s"',
'usrRegistered'                                               => "Der Benutzer wird nach der Eingabe seines Passwortes registriert",
'view'                                                        => "Ansicht",
'EMAIL_STOCKREMINDER_SUBJECT'                                 => 'Lagerbestand niedrig',
'EMAIL_SENDDOWNLOADS_GREETING'                                => 'Guten Tag',
'EMAIL_SENDDOWNLOADS_PAYMENT_PENDING'                         => 'Die Bezahlung der Bestellung ist noch nicht abgeschlossen.',
'EMAIL_SENDDOWNLOADS_DOWNLOADS_DESC'                          => 'Laden Sie Ihre bestellten Dateien hier herunter.',
'EMAIL_SENDDOWNLOADS_SUBJECT'                                 => 'Downloadlinks',
'EMAIL_SENDEDNOW_HTML_ORDERSHIPPEDTO'                         => 'Die Sendung geht an:',
'EMAIL_SENDEDNOW_HTML_ORDERNOMBER'                            => 'Ihre Bestellnr.:',
'EMAIL_SENDEDNOW_HTML_QUANTITY'                               => 'Anzahl',
'EMAIL_SENDEDNOW_HTML_PRODUCT'                                => 'Artikel',
'EMAIL_SENDEDNOW_HTML_PRODUCTRATING'                          => 'Artikel bewerten',
'EMAIL_SENDEDNOW_HTML_ARTNOMBER'                              => 'Art.Nr.:',
'EMAIL_SENDEDNOW_HTML_REVIEW'                                 => 'bewerten',
'EMAIL_SENDEDNOW_HTML_YUORTEAM1'                              => 'Ihr',
'EMAIL_SENDEDNOW_HTML_YUORTEAM2'                              => 'Team',
'EMAIL_SENDEDNOW_HTML_TS_RATINGS_RATEUS'                      => 'Bitte nehmen Sie sich eine Minute, um unseren Shop zu bewerten.',
'EMAIL_SENDEDNOW_HTML_SHIPMENTTRACKING'                       => 'Ihr Link zur Sendungsverfolgung:',
'EMAIL_SENDEDNOW_HTML_SHIPMENTTRACKINGURL'                    => 'hier klicken',
'PAGE_INFO_CLEARCOOKIE_TITLE'                                 => 'Information über Cookies',
'PAGE_INFO_CLEARCOOKIE_CONTENT'                               => 'Sie haben sich entschieden, keine Cookies von unserem Online-Shop zu akzeptieren. Die Cookies wurden gelöscht. Sie können in den Einstellungen Ihres Browsers die Verwendung von Cookies deaktivieren und den Online-Shop mit einigen funktionellen Einschränkungen nutzen. Sie können auch zurück zum Shop gehen, ohne die Einstellungen zu ändern, und den vollen Funktionsumfang des Online-Shops genießen.<br/><br/>Informationen zu Cookies auf Wikipedia: <a href="http://de.wikipedia.org/wiki/HTTP-Cookie"><strong>http://de.wikipedia.org/wiki/HTTP-Cookie</strong></a>',

);
