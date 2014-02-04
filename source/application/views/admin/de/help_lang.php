<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * In this file, the content for help popups is stored:
 *
 * Syntax for identifier: HELP_TABNAME_INPUTNAME, e.g. HELP_SHOP_CONFIG_BIDIRECTCROSS.
 * !!!The INPUTNAME is same as in lang.php for avoiding even more different Identifiers.!!!
 * In some cases, in lang.php GENERAL_ identifiers are used. In this file, always the tab name is used.
 *
 *
 * HTML Tags for markup (same as in online manual):
 * <span class="navipath_or_inputname">...</span> for names of input fields, selectlists and Buttons, e.g. <span class="navipath_or_inputname">Active</span>
 * <span class="userinput_or_code">...</span> for input in input fields (also options in selectlists) and code
 * <span class="filename_filepath_or_italic">...</span> for filenames, filepaths and other italic stuff
 * <span class="warning_or_important_hint">...</span> for warning and important things
 * <ul> and <li> for lists
 */

$aLang =  array(
'charset'                                     => 'ISO-8859-15',

'HELP_SHOP_SYSTEM_OTHERCOUNTRYORDER'          => 'Diese Einstellung beeinflusst das Verhalten des OXID eShops, wenn für ein Land, in das Benutzer bestellen wollen, keine Versandkosten definiert sind:<br>' .
                                                 '<ul><li>Wenn die Einstellung aktiv ist, erhalten diese Benutzer im Bestellprozess eine Meldung: Die Versandkosten werden ihnen nachträglich mitgeteilt, wenn Sie damit einverstanden ist. Sie können mit der Bestellung fortfahren.</li>' .
                                                 '<li>Wenn die Einstellung ausgeschaltet ist, können Benutzer aus Ländern, für die keine Versandkosten definiert sind, nicht bestellen.</li></ul>',

'HELP_SHOP_SYSTEM_DISABLENAVBARS'             => 'Wenn Sie diese Einstellung aktivieren, werden die meisten Navigationselemente im Bestellprozess ausgeblendet. Dadurch werden die Benutzer beim Bestellen nicht unnötig abgelenkt.',

'HELP_SHOP_SYSTEM_DEFAULTIMAGEQUALITY'        => 'Empfehlenswerte Einstellungen sind ca. 40-80:<br>' .
                                                 '<ul><li>Unterhalb von ca. 40 werden deutliche Kompressionsartefakte sichtbar, und die Bilder wirken unscharf.</li>'.
                                                 '<li>Oberhalb von ca. 80 kann man kaum eine Verbesserung der Bildqualität feststellen, während die Dateigröße enorm zunimmt.</li></ul><br>'.
                                                 'Die Standardeinstellung ist 75.',

'HELP_SHOP_SYSTEM_DENIEDDYNGROUPS'            => 'Option aktivieren, um angegebene Benutzergruppen herauszufiltern, die über den URL-Parameter "dgr" zugeordnet werden sollen.',

'HELP_SHOP_SYSTEM_LDAP'                       => 'Bitte Datei core/oxldap.php anpassen.',

'HELP_SHOP_SYSTEM_SHOWVARIANTREVIEWS'         => 'Diese Einstellung beeinflusst das Verhalten, wenn Varianten bewertet werden: Wenn die Einstellung aktiv ist, dann werden die Bewertungen der Varianten auch beim Vater-Artikel angezeigt.',

'HELP_SHOP_SYSTEM_VARIANTSSELECTION'          => 'Im eShop gibt es oft Listen, in denen Sie Artikel zuordnen können, z. B. wenn Sie Artikel zu Rabatten zuordnen. Wenn die Einstellung aktiv ist, werden in diesen Listen auch  Varianten angezeigt.',

'HELP_SHOP_SYSTEM_VARIANTPARENTBUYABLE'       => 'Hier können Sie einstellen, ob der Vater-Artikel gekauft werden kann:' .
                                                 '<ul><li>Wenn die Einstellung aktiv ist, kann auch der Vater-Artikel gekauft werden.</li>' .
                                                 '<li>Wenn die Einstellung nicht aktiv ist, können nur die Varianten gekauft werden.</li></ul>',

'HELP_SHOP_SYSTEM_VARIANTINHERITAMOUNTPRICE'  => 'Diese Einstellung beeinflusst das Verhalten des eShops, wenn beim Vater-Artikel Staffelpreise eingerichtet sind: Wenn die Einstellung aktiv ist, werden die Staffelpreise auch bei den Varianten verwendet.',

'HELP_SHOP_SYSTEM_ISERVERTIMESHIFT'           => 'Es kann sein, dass sich der Server in einer anderen Zeitzone befindet. Mit dieser Einstellung können Sie die Zeitverschiebung korrigieren: Geben Sie die Anzahl der Stunden, die zur Serverzeit addiert/abgezogen werden sollen ein, z. B. <kdb>+2</kdb> oder <kdb>-2</kdb>',

'HELP_SHOP_SYSTEM_INLINEIMGEMAIL'             => 'Wenn die Einstellung aktiv ist, werden die Bilder, die in E-Mails verwendet werden, zusammen mit der E-Mail versendet. Wenn die Einstellung nicht aktiv ist, lädt das E-Mail Programm die Bilder herunter, wenn Benutzer die E-Mail öffnen.',

'HELP_SHOP_SYSTEM_SHOP_LOCATION'              => 'Bitte wählen Sie die Region, auf die der Shop ausgerichtet ist. Abhängig davon werden zusätzliche eCommerce Services vom OXID Server nachgeladen. Bitte vergessen Sie nicht, die Einstellung "Zusätzliche Informationen vom OXID Server laden" in Stammdaten -> Grundeinstellungen -> Einstell. -> Administrationsbereich zu aktivieren',

'HELP_SHOP_SYSTEM_UTILMODULE'                 => 'Bitte tragen Sie Ihre .php-Datei ein, mit der beim Shopstart eShop Funktionen überschrieben werden sollen.',



'HELP_SHOP_CACHE_ENABLED'                     => 'Wenn Dynamic content caching aktiv ist, werden zusätzliche Inhalte gecached und dadurch die Performance Ihres eShops weiter gesteigert. Deaktivieren Sie die Einstellung, solange Ihr eShop noch nicht Produktiv eingesetzt und angepasst wird.',

'HELP_SHOP_CACHE_LIFETIME'                    => 'Hier können Sie einstellen, wie viele Sekunden die gecachten Inhalte maximal gespeichert werden, bevor sie erneut berechnet werden. Die Standardeinstellung sind 36000 Sekunden.',

'HELP_SHOP_CACHE_CLASSES'                     => 'Hier stellen Sie ein, welche View-Klassen gecached werden.<br> Ändern Sie diese Einstellung nur, wenn Sie mit den Caching-Mechanismen gut vertraut sind!',

'HELP_SHOP_CACHE_REVERSE_PROXY_ENABLED'       => 'Aktiviert das Caching mit einem HTTP Reverse Proxy. Hinweis: Nicht zusammen mit "Dynamic Content Caching" verwenden, da das die Performance beeinträchtigen könnte.',

'HELP_SHOP_CACHE_LAYOUT_CACHE_AGE'            => 'Setzt die Lebenszeit des Seiten-Layouts in Sekunden. Wird als Header-Wert "Age" über den HTTP-Header gesendet.',



'HELP_SHOP_CONFIG_ORDEROPTINEMAIL'            => 'Wenn Double-Opt-In aktiviert ist, erhalten die Benutzer eine E-Mail mit einem Bestätigungs-Link, wenn sie sich für den Newsletter registrieren. Erst, wenn sie diesen Link besuchen, sind sie für den Newsletter angemeldet.<br>' .
                                                 'Double-Opt-In schützt vor Anmeldungen, die nicht gewollt sind. Ohne Double-Opt-In können beliebige E-Mail Adressen für den Newsletter angemeldet werden. Dies wird z. B. auch von Spam-Robotern gemacht. Durch Double-Opt-In kann der Besitzer der E-Mail Adresse bestätigen, dass er den Newsletter wirklich empfangen will.',

'HELP_SHOP_CONFIG_BIDIRECTCROSS'              => 'Durch Crossselling können zu einem Artikel passende Artikel angeboten werden. Crossselling-Artikel werden im eShop bei <span class="filename_filepath_or_italic">Kennen Sie schon?</span> angezeigt.<br>' .
                                                 'Wenn z.B. einem Auto als Crossselling-Artikel Winterreifen zugeordnet sind, werden beim Auto die Winterreifen angezeigt. Wenn Bidirektionales Crossselling aktiviert ist, funktioniert Crossselling in beide Richtungen: bei den Winterreifen wird das Auto angezeigt.',

'HELP_SHOP_CONFIG_STOCKONDEFAULTMESSAGE'      => 'Bei jedem Artikel können Sie einrichten, welche Meldung den Benutzern angezeigt wird, wenn der Artikel auf Lager ist. ' .
                                                 'Wenn diese Einstellung aktiv ist, wird den Benutzern auch dann eine Meldung angezeigt, wenn bei einem Artikel keine eigene Meldung hinterlegt ist. Es wird die Standardmeldung <span class="filename_filepath_or_italic">sofort lieferbar</span> verwendet.',

'HELP_SHOP_CONFIG_STOCKOFFDEFAULTMESSAGE'     => 'Bei jedem Artikel können Sie einrichten, welche Meldung den Benutzern angezeigt wird, wenn der Artikel nicht auf Lager ist. ' .
                                                 'Wenn diese Einstellung aktiv ist, wird den Benutzern auch dann eine Meldung angezeigt, wenn bei einem Artikel keine eigene Meldung hinterlegt ist. Es wird die Standardmeldung <span class="filename_filepath_or_italic">Dieser Artikel ist nicht auf Lager und muss erst nachbestellt werden</span> verwendet.',

'HELP_SHOP_CONFIG_OVERRIDEZEROABCPRICES'      => 'Sie können für bestimmte Benutzer spezielle Preise einrichten. Dadurch können Sie bei jedem Artikel A, B, und C-Preise eingeben. Wenn Benutzer z. B. in der Benutzergruppe Preis A sind, werden ihnen die A-Preise anstatt dem normalen Artikelpreis angezeigt.<br>' .
                                                 'Wenn die Einstellung aktiv ist, wird diesen Benutzern der normale Artikelpreis angezeigt, wenn für den Artikel kein A, B oder C-Preis vorhanden ist.<br>' .
                                                 'Sie sollten diese Einstellung aktivieren, wenn Sie A,B und C-Preise verwenden: Ansonsten wird den bestimmten Benutzern ein Preis von 0,00 angezeigt, wenn kein A,B oder C-Preis hinterlegt ist.',

'HELP_SHOP_CONFIG_SEARCHFIELDS'               => 'Hier können Sie die Datenbankfelder der Artikel eingeben, in denen gesucht wird. Geben Sie pro Zeile nur ein Datenbankfeld ein.<br>' .
                                                 'Die am häufigsten benötigten Einträge sind:' .
                                                 '<ul><li>oxtitle = Titel (Name) der Artikel</li>' .
                                                 '<li>oxshortdesc = Kurzbeschreibung der Artikel</li>' .
                                                 '<li>oxsearchkeys = Suchwörter, die bei den Artikeln eingetragen sind</li>' .
                                                 '<li>oxartnum = Artikelnummern</li>' .
                                                 '<li>oxtags = Stichworte, bei den Artikeln eingetragen sind</li></ul>',

'HELP_SHOP_CONFIG_SORTFIELDS'                 => 'Hier können Sie die Datenbankfelder der Artikel eingeben, nach denen Artikellisten sortiert werden können. Geben Sie pro Zeile nur ein Datenbankfeld ein.<br>' .
                                                 'Die am häufigsten benötigten Einträge sind:' .
                                                 '<ul><li>oxtitle = Titel (Name) der Artikel</li>' .
                                                 '<li>oxprice = Preis der Artikel</li>' .
                                                 '<li>oxvarminprice = Der niedrigste Preis der Artikel, wenn Varianten mit verschiedenen Preisen verwendet werden.</li>' .
                                                 '<li>oxartnum = Artikelnummern</li>' .
                                                 '<li>oxrating = Die Bewertung der Artikel</li>' .
                                                 '<li>oxstock = Lagerbestand der Artikel</li></ul>',

'HELP_SHOP_CONFIG_MUSTFILLFIELDS'             => 'Hier können Sie eingeben, welche Felder von Benutzern ausgefüllt werden müssen, wenn Sie sich registrieren. Sie müssen die entsprechenden Datenbankfelder angeben. Geben Sie pro Zeile nur ein Datenbankfeld ein.<br>' .
                                                 'Die am häufigsten benötigten Einträge für die Benutzerdaten sind:' .
                                                 '<ul><li>oxuser__oxfname = Vorname</li>' .
                                                 '<li>oxuser__oxlname = Nachname</li>' .
                                                 '<li>oxuser__oxstreet = Straße</li>' .
                                                 '<li>oxuser__oxstreetnr = Hausnummer</li>' .
                                                 '<li>oxuser__oxzip = Postleitzahl</li>' .
                                                 '<li>oxuser__oxcity = Stadt</li>' .
                                                 '<li>oxuser__oxcountryid = Land</li>' .
                                                 '<li>oxuser__oxfon = Telefonnummer</li></ul><br>' .
                                                 'Sie können auch angeben, welche Felder ausgefüllt werden müssen, wenn Benutzer eine Lieferadresse eingeben. Die am häufigsten benötigten Einträge sind:' .
                                                 '<ul><li>oxaddress__oxfname = Vorname</li>' .
                                                 '<li>oxaddress__oxlname = Nachname</li>' .
                                                 '<li>oxaddress__oxstreet = Straße</li>' .
                                                 '<li>oxaddress__oxstreetnr = Straßennummer</li>' .
                                                 '<li>oxaddress__oxzip = Postleitzahl</li>' .
                                                 '<li>oxaddress__oxcity = Stadt</li>' .
                                                 '<li>oxaddress__oxcountryid = Land</li>' .
                                                 '<li>oxaddress__oxfon = Telefonnummer</li></ul>',

'HELP_SHOP_CONFIG_USENEGATIVESTOCK'           => 'Mit <span class="navipath_or_inputname">Negative Lagerbestände erlauben</span> können Sie einstellen, welcher Lagerbestand berechnet wird, wenn ein Artikel ausverkauft ist:<br>' .
                                                 '<ul><li>Wenn die Einstellung aktiv ist, werden negative Lagerbestände berechnet, wenn weitere Exemplare bestellt werden.</li>' .
                                                 '<li>Wenn die Einstellung nicht aktiv ist, fällt der Lagerbestand eines Artikels nie unter 0. Auch dann nicht, wenn der Artikel bereits ausverkauft ist und noch weitere Exemplare bestellt werden.</li></ul>',

'HELP_SHOP_CONFIG_NEWARTBYINSERT'             => 'Auf der Startseite Ihres eShops werden die unter <span class="filename_filepath_or_italic">Frisch eingetroffen!</span> die neusten Artikel in Ihrem eShop angezeigt. Sie können die Artikel, die hier angezeigt werden, manuell einstellen oder automatisch berechnen lassen. Mit dieser Einstellung wählen Sie, wie die neusten Artikel berechnet werden sollen: Nach dem Datum, an dem die Artikel erstellt wurden, oder nach dem Datum der letzten Änderung im Administrationsbereich/der letzten Bestellung im Shop.',

'HELP_SHOP_CONFIG_LOAD_DYNAMIC_PAGES'         => 'Wenn diese Einstellung aktiv ist, werden zusätzliche Informationen zu anderen OXID Produkten im Administrationsbereich angezeigt, z. B. zu OXID eFire. Welche Informationen geladen werden, hängt von der Region ab, die für den Shop gewählt wurde.',


'HELP_SHOP_CONFIG_DELETERATINGLOGS'           => 'Wenn Benutzer einen Artikel bewerten, können Sie den Artikel nicht erneut bewerten. Hier können Sie einstellen, dass die Benutzer nach einer bestimmten Anzahl von Tagen den Artikel erneut bewerten können.',

'HELP_SHOP_CONFIG_DISABLEONLINEVATIDCHECK'    => 'Die Online UST-ID Prüfung wird immer ausgeführt, wenn ein Kunde aus dem Ausland (aber innerhalb der EU) eine Umsatzsteuer-ID beim bestellen angibt. Wenn die UST-ID gültig ist, wird für die Bestellung keine Umsatzsteuer berechnet.<br>'.
                                                 'Wenn die Prüfung ausgeschaltet ist, wird immer die für das entsprechende Land eingestelle Umsatzsteuer berechnet.',

'HELP_SHOP_CONFIG_ALTVATIDCHECKINTERFACEWSDL' => 'Hier können Sie eine alternative URL für die Online UST-ID Prüfung eingeben.',

'HELP_SHOP_CONFIG_PSLOGIN'                    => 'Transformiert Ihren Shop in einen Shop mit beschränktem Zugriff. Auf diese Weise erzeugen ' .
                                                 'Sie Private Sales Communities mit geschlossenen Nutzergruppen.',

'HELP_SHOP_CONFIG_BASKETEXCLUDE'              => 'Ermöglicht es, Produkte aus ausschließlich einer Wurzelkategorie in den Warenkorb zu legen. ' .
                                                 'Beim Wechsel der Wurzelkategorie wird der Konsument gezwungen den Warenkorb zu erwerben oder ihn zu verwerfen.',

'HELP_SHOP_CONFIG_BASKETRESERVATION'          => 'Ist diese Option aktiviert, laufen Warenkörbe nach einer von Ihnen definierbaren Zeit ab.  <br><br>'.
                                                 'Darüber hinaus schaltet diese Option die Produktreservierung ein, mit der die Lagerbestände der Produkte zunächst reserviert ' .
                                                 'und erst bei Auflösung des Warenkorbs endgültig reduziert wird. Produkte aus abgelaufenen Warenkörben ' .
                                                 'werden dem Lagerbestand wieder zugeschlagen.<br><br>'.
                                                 'Für Produkte aus gekauften Warenkörben wird der Lagerbestand endgültig reduziert.',


'HELP_SHOP_CONFIG_BASKETRESERVATIONTIMEOUT'   => 'Nach der eingestellten Anzahl von Sekunden ohne Änderung am Warenkorb, wird der Warenkorb automatisch geleert ' .
                                                 'und die darin enthaltenen Produkte dem Lagerbestand wieder zugeführt.',

'HELP_SHOP_CONFIG_INVITATION'                 => 'Mit Einladungen können Ihre Kunden weitere Freunde einladen sich zu registrieren und Bonuspunkte zu sammeln. ' .
                                                 'Die Anzahl der gesammelten Bonuspunkte wird im jeweiligen Benutzerprofil gespeichert. Sie als Shopbetreiber können diese '.
                                                 'Bonuspunkte dann je nach Ihrem Geschäftskonzept einsetzen.',

'HELP_SHOP_CONFIG_POINTSFORINVITATION'        => 'Anzahl Bonuspunkte, die der Einladende erhält, wenn ein Eingeladener sich aufgrund der Einladung registriert.',

'HELP_SHOP_CONFIG_POINTSFORREGISTRATION'      => 'Anzahl Bonuspunkte, die der Eingeladene erhält, der sich aufgrund der Einladung registriert.',

'HELP_SHOP_CONFIG_SHOP_CONFIG_FACEBOOKAPPID'  => 'Um Ihren eShop mit Facebook zu verbinden, müssen Sie die Facebook \'Application ID\' angeben. ' .
                                                 'Weitere Informationen finden Sie im ' .
                                                 '<a href="http://wiki.oxidforge.org/Tutorials/Connecting_website_to_facebook" target="_blank">Tutorial</a>.',

'HELP_SHOP_CONFIG_SHOP_CONFIG_FACEBOOKCONFIRM'=> 'Um die Privatsphäre des Kunden zu schützen, muss die Anzeige der Facebook Social Plugins explizit bestätigt werden. Erst nach Bestätigung werden Daten mit Facebook ausgetauscht.',

'HELP_SHOP_CONFIG_SHOP_CONFIG_FBSECRETKEY'    => 'Um die Verbindung zwischen eShop und Facebook abzusichern, geben Sie den \'Secure Key\' hier ein. ' .
                                                 'Weitere Informationen finden Sie im ' .
                                                 '<a href="http://wiki.oxidforge.org/Tutorials/Connecting_website_to_facebook" target="_blank">Tutorial</a>.',

'HELP_SHOP_CONFIG_FBCOMMENTS'                 => 'Erlaubt den Shopbesuchern, Kommentare zu Produkten abzugeben.',

'HELP_SHOP_CONFIG_FBFACEPILE'                 => 'Zeigt Ihren Shopbesuchern, wer von deren Freunden in Facebook ein Freund Ihres Shops ist.',

'HELP_SHOP_CONFIG_FBINVITE'                   => 'Erlaubt Ihren Besuchern, Freunde über Facebook einladen',

'HELP_SHOP_CONFIG_FBSHARE'                    => 'Zeigt Facebooks \'Share button\'.',

'HELP_SHOP_CONFIG_FBLIKE'                     => 'Zeigt Facebooks \'Like button\', mit dem die Besucher Ihres Shops in Facebook bekannt geben, dass sie Ihren Shop mögen.',

'HELP_SHOP_CONFIG_FACEBOOKCONNECT'            => 'Facebook Connect anzeigen, damit Anwender sich mit ihren Facebook-Profildaten im Shop anmelden können.',

'HELP_SHOP_CONFIG_ATTENTION'                  => 'ACHTUNG! Üblicherweise ist in den Verträgen mit MasterCard und VISA die Speicherung der Daten ausdrücklich verboten.',

'HELP_SHOP_CONFIG_SHOWTSINTERNATIONALFEESMESSAGE' => 'Im 4. Schritt des Bestellprozesses wird die CMS-Seite "oxtsinternationalfees" ergänzend angezeigt.',

'HELP_SHOP_CONFIG_SHOWTSCODMESSAGE'           => 'Im 4. Schritt des Bestellprozesses wird die CMS-Seite "oxtscodmessage" ergänzend angezeigt.',

'HELP_SHOP_CONFIG_SHOWTAGS'                   => 'Wenn nicht angehakt, werden keine Tags im eShop angezeigt. Auf Seiten, die über Tag URL aufgerufen werden könnten, kann nicht zugegriffen werden.',

'HELP_SHOP_MALL_MALLMODE'                     => 'Hier stellen Sie ein, was auf der Startseite dieses eShops angezeigt werden soll: '.
                                                 '<ul><li><span class="navipath_or_inputname">Shop-Auswahlseite</span>: Eine Seite wird angezeigt, auf der Kunden zwischen den verschiedenen Shops wählen können.</li>' .
                                                 '<li><span class="navipath_or_inputname">Hauptschop anzeigen</span>: Die normale Startseite des Hauptshops wird angezeigt.</li></ul>',

'HELP_SHOP_MALL_PRICEADDITION'                => 'Sie haben die Möglichkeit, auf alle Artikelpreise einen Aufschlag zu berechnen: Geben Sie den entsprechenden Aufschlag ein und wählen Sie in der Auswahlliste aus, ob er prozentual (<span class="userinput_or_code">%</span>) oder absolut (<span class="userinput_or_code">abs</span>) berechnet werden soll.',


'HELP_SHOP_CONFIG_DOWNLOADS'                  => 'Shop mit Download-Artikeln. Aktivieren Sie hier, dass Artikel bestellt und heruntergeladen werden können.',

'HELP_SHOP_CONFIG_DOWNLOADS_PATH'             => 'Pfad, in dem Dateien für Download-Artikel gespeichert werden.',

'HELP_SHOP_CONFIG_MAX_DOWNLOADS_COUNT'        => 'Geben Sie hier an, wie oft Benutzer nach einer Bestellung den Link zum Download verwenden können. Das ist die Standardeinstellung für alle Artikel.'.
                                                 'Sie können diesen Wert für jede Datei des Artikels unter Artikel verwalten -> Artikel -> Downloads ändern.',

'HELP_SHOP_CONFIG_LINK_EXPIRATION_TIME_UNREGISTERED' => 'Geben Sie hier an, wie oft Benutzer, die ohne Registrierung bestellt haben, den Link zum Download verwenden können. Das ist die Standardeinstellung für alle Artikel.'.
                                                 'Sie können diesen Wert für jede Datei des Artikels unter Artikel verwalten -> Artikel -> Downloads ändern.',

'HELP_SHOP_CONFIG_LINK_EXPIRATION_TIME'       => 'Geben Sie die Zeit in Stunden an, die der Downloadlink nach der Bestellung gültig ist. Das ist die Standardeinstellung für alle Artikel.'.
                                                 'Sie können diesen Wert für jede Datei des Artikels unter Artikel verwalten -> Artikel -> Downloads ändern.',

'HELP_SHOP_CONFIG_DOWNLOAD_EXPIRATION_TIME'   => 'Geben Sie die Zeit in Stunden an, die der Downloadlink nach dem ersten Download gültig ist. Das ist die Standardeinstellung für alle Artikel.'.
                                                 'Sie können diesen Wert für jede Datei des Artikels unter Artikel verwalten -> Artikel -> Downloads ändern.',

'HELP_SHOP_PERF_NEWESTARTICLES'               => 'In Ihrem eShop wird eine Liste mit den neusten Artikeln (Frisch eingetroffen!) angezeigt. Hier können Sie einstellen, wie die Liste generiert wird:' .
                                                 '<ul><li><span class="userinput_or_code">ausgeschaltet</span>: Die Liste wird nicht angezeigt.</li>' .
                                                 '<li><span class="userinput_or_code">manuell</span>: Sie können unter <span class="navipath_or_inputname">Kundeninformationen -> Aktionen verwalten</span> in der Aktion <span class="navipath_or_inputname">Frisch eingetroffen</span> einstellen, welche Artikel in der Liste angezeigt werden.</li>' .
                                                 '<li><span class="userinput_or_code">automatisch</span>: Die Liste der neusten Artikel wird automatisch berechnet.</li></ul>',

'HELP_SHOP_PERF_TOPSELLER'                    => 'In Ihrem eShop wird eine Liste mit den meistverkauften Artikeln (Top of the Shop) angezeigt. Hier können Sie einstellen, wie die Liste generiert wird:' .
                                                 '<ul><li><span class="userinput_or_code">ausgeschaltet</span>: Die Liste wird nicht angezeigt.</li>' .
                                                 '<li><span class="userinput_or_code">manuell</span>: Sie können unter <span class="navipath_or_inputname">Kundeninformationen -> Aktionen verwalten</span> in der Aktion <span class="navipath_or_inputname">Topseller</span> einstellen, welche Artikel in der Liste angezeigt werden.</li>' .
                                                 '<li><span class="userinput_or_code">automatisch</span>: Die Liste der meistverkauften Artikel wird automatisch berechnet.</li></ul>',

'HELP_SHOP_PERF_LOADFULLTREE'                 => 'Wenn die Einstellung aktiv ist, wird in der Kategoriennavigation der komplette Kategoriebaum angezeigt (Alle Kategorien sind \'ausgeklappt\'). Diese Einstellung funktioniert nur, wenn die Kategoriennavigation <span class="warning_or_important_hint">nicht</span> oben angezeigt wird.',

'HELP_SHOP_PERF_LOADACTION'                   => 'Wenn die Einstellung aktiv ist, werden Aktionen wie <span class="filename_filepath_or_italic">Die Dauerbrenner</span>, <span class="filename_filepath_or_italic">Top of the Shop</span>, <span class="filename_filepath_or_italic">Frisch eingetroffen!</span> geladen und angezeigt.',

'HELP_SHOP_PERF_LOADREVIEWS'                  => 'Benutzer können Artikel bewerten und Kommentare zu Artikeln verfassen. Wenn die Einstellung aktiv ist, werden die bereits abgegebenen Kommentare und Bewertungen beim Artikel angezeigt.',

'HELP_SHOP_PERF_USESELECTLISTPRICE'           => 'In Auswahllisten können Sie Preis Auf/Abschläge einstellen. Wenn diese Einstellung aktiv ist, werden die Auf/Abschläge berechnet, ansonsten nicht.',

'HELP_SHOP_PERF_DISBASKETSAVING'              => 'Der Warenkorb von angemeldeten Benutzern wird gespeichert. Wenn sich die Benutzer bei einem weiteren Besuch in Ihrem eShop anmelden, wird der gespeicherte Warenkorb automatisch wieder geladen. Wenn sie diese Einstellung aktivieren, werden die Warenkörbe nicht mehr gespeichert.',

'HELP_SHOP_PERF_LOADDELIVERY'                 => 'Wenn Sie diese Einstellung ausschalten, berechnet der eShop keine Versandkosten: es werden immer 0,00 EUR als Versandkosten angegeben.',

'HELP_SHOP_PERF_LOADPRICE'                    => 'Wenn Sie diese Einstellung ausschalten, werden die Artikelpreise nicht mehr berechnet und bei den Artikeln kein Preis mehr angezeigt.',

'HELP_SHOP_PERF_PARSELONGDESCINSMARTY'        => 'Wenn die Einstellung aktiv ist, werden die Beschreibungstexte von Artikeln und Kategorien mit Smarty ausgeführt: Dann können Sie Smarty-Tags in die Beschreibungstexte einbinden (z. B. Variablen ausgeben). <br>' .
                                                 'Wenn die Einstellung nicht aktiv ist, werden die Beschreibungstexte so eingegeben, wie sie im Editor eingegeben werden.',

'HELP_SHOP_PERF_LOADATTRIBUTES'               => 'Normalerweise werden die Attribute eines Artikels nur in der Detailansicht des Artikels geladen. Wenn die Einstellung aktiv ist, werden die Attribute immer zusammen mit dem Artikel geladen (z. B. wenn der Artikel in einem Suchergebnis vorkommt).<br>' .
                                                 'Diese Einstellung kann nützlich sein, wenn Sie die Templates anpassen und die Attribute eines Artikels auch an anderen Stellen anzeigen möchten.',

'HELP_SHOP_PERF_LOADSELECTLISTSINALIST'       => 'Normalerweise werden Auswahllisten nur in der Detailansicht eines Artikels angezeigt. Wenn Sie die Einstellung aktivieren, werden die Auswahllisten auch in Artikellisten (z. B. Suchergebnisse, Kategorieansichten) angezeigt.',

'HELP_SHOP_PERF_CHECKIFTPLCOMPILE'            => 'Wenn diese Einstellung aktiv ist, überprüft der eShop bei jedem Aufruf, ob sich Templates geändert haben und berechnet die Ausgabe neu, falls Änderungen vorhanden sind. Aktivieren Sie die Einstellung, wenn Sie Templates anpassen, und deaktivieren Sie sie, wenn der eShop produktiv verwendet wird.',

'HELP_SHOP_PERF_CLEARCACHEONLOGOUT'           => 'Normalerweise wird der komplette Cache geleert, sobald Änderungen im Administrationsbereich vorgenommen werden. Das kann zu eine verschlechterten Performance im Administrationsbereich führen. Wenn Sie die Einstellung aktivieren, wird der Cache nur geleert, wenn Sie sich aus dem Administrationsbereich ausloggen.',



'HELP_SHOP_SEO_TITLEPREFIX'                   => 'Jede einzelne Seite hat einen Titel. Er wird im Browser als Titel des Browser-Fensters angezeigt. Mit <span class="navipath_or_inputname">Titel Präfix</span> und <span class="navipath_or_inputname">Titel Suffix</span> haben Sie die Möglichkeit, vor und hinter jeden Seitentitel Text einzufügen:<br>' .
                                                 '<ul><li>Geben Sie in <span class="navipath_or_inputname">Titel Präfix</span> den Text ein, der vor dem Titel erscheinen soll.</li>' .
                                                 '<li>Geben Sie in <span class="navipath_or_inputname">Titel Suffix</span> den Text ein, der hinter dem Titel erscheinen soll.</li></ul>',

'HELP_SHOP_SEO_TITLESUFFIX'                   => 'Jede einzelne Seite hat einen Titel. Er wird im Browser als Titel des Browser-Fensters angezeigt. Mit <span class="navipath_or_inputname">Titel Präfix</span> und <span class="navipath_or_inputname">Titel Suffix</span> haben Sie die Möglichkeit, vor und hinter jeden Seitentitel Text einzufügen:<br>' .
                                                 '<ul><li>Geben Sie in <span class="navipath_or_inputname">Titel Präfix</span> den Text ein, der vor dem Titel erscheinen soll.</li>' .
                                                 '<li>Geben Sie in <span class="navipath_or_inputname">Titel Suffix</span> den Text ein, der hinter dem Titel erscheinen soll.</li></ul>',

'HELP_SHOP_SEO_IDSSEPARATOR'                  => 'Das Trennzeichen wird verwendet, wenn Kategorie- oder Artikelnamen aus mehreren Worten bestehen. Das Trennzeichen wird anstelle eines Leerzeichens in die URL eingefügt, z. B. www.ihronlineshop.de/Kategorie-aus-mehreren-Worten/Artikel-aus-mehreren-Worten.html<br>' .
                                                 'Wenn Sie kein Trennzeichen eingeben, wird der Bindestrich - als Trennzeichen verwendet.',

'HELP_SHOP_SEO_SAFESEOPREF'                   => 'Wenn mehrere Artikel den gleichen Namen haben und in der gleichen Kategorie sind, würden sie die gleiche SEO URL erhalten. Damit das nicht passiert, wird das SEO Suffix angehängt. Dadurch werden gleiche SEO URLs vermieden. Wenn Sie kein SEO Suffix angeben, wird <span class="filename_filepath_or_italic">oxid</span> als Standard verwendet.',

'HELP_SHOP_SEO_RESERVEDWORDS'                 => 'Bestimmte URLs sind im eShop festgelegt, z.B. www.ihronlineshop.de/admin, um den Administrationsbereich zu öffnen. Wenn eine Kategorie <span class="filename_filepath_or_italic">admin</span> heißen würde, wäre die SEO URL zu dieser Kategorie ebenfalls www.ihronlineshop.de/admin - die Kategorie könnte nicht geöffnet werden. Deswegen wird an solche SEO URLs automatisch das SEO Suffix angehängt. Mit dem Eingabefeld können Sie einstellen, an welche SEO URLs das SEO Suffix automatisch angehängt werden soll.',

'HELP_SHOP_SEO_SKIPTAGS'                      => 'Wenn bei Artikeln oder Kategorien keine SEO-Einstellungen für die META-Tags vorhanden sind, werden diese Informationen aus der Beschreibung generiert. Dabei können Wörter weggelassen werden, die besonders häufig vorkommen. Alle Wörter, die in diesem Eingabefeld stehen, werden bei der automatischen Generierung ignoriert.',

'HELP_SHOP_SEO_STATICURLS'                    => 'Für bestimmte Seiten (z. B. AGB\'s) im eShop können Sie feste suchmaschinenfreundliche URLs festlegen. Wenn Sie eine statische URL auswählen, wird in dem Feld <span class="navipath_or_inputname">Standard URL</span> die normale URL angezeigt. In den Eingabefeldern weiter unten können Sie für jede Sprache suchmaschinenfreundliche URLs eingeben.',



'HELP_SHOP_MAIN_PRODUCTIVE'                   => 'Für Installation, Konfiguration, Anpassung der Templates und Modul-Debugging sollte der Shop nicht im Produktivmodus sein. Sobald der Produktivmodus aktiviert ist, wird das Cache Handling und das Error Reporting für den Livebetrieb des Shops optimiert.<br>' .
                                                 '<span class="warning_or_important_hint">Aktivieren Sie diese Einstellung, bevor ihr eShop öffentlich zugänglich gemacht wird.</span><br>' .
                                                 'Weitere wichtige Hinweise für den Livegang des OXID eShop finden Sie in unserer <a href="http://wiki.oxidforge.org/Tutorials/Check_vor_dem_Livegang" target="_blank">OXIDforge</a>.',

'HELP_SHOP_MAIN_ACTIVE'                       => 'Mit <span class="navipath_or_inputname">Aktiv</span> können Sie ihren kompletten eShop ein- und ausschalten. Wenn ihr eShop ausgeschaltet ist, wird Ihren Kunden eine Meldung angezeigt, dass der eShop vorübergehend offline ist. Das kann für Wartungsarbeiten am eShop nützlich sein.',

'HELP_SHOP_MAIN_INFOEMAIL'                    => 'An diese E-Mail Adresse werden E-Mails gesendet, wenn die Benutzer E-Mails über das Kontaktformular senden.',

'HELP_SHOP_MAIN_ORDEREMAIL'                   => 'Wenn Benutzer bestellen, erhalten sie eine E-Mail, in der die Bestellung nochmals zusammengefasst ist. Wenn die Benutzer auf diese E-Mail antworten, wird die Antwort an die <span class="navipath_or_inputname">Bestell E-Mail Reply</span> gesendet.',

'HELP_SHOP_MAIN_OWNEREMAIL'                   => 'Wenn Benutzer bestellen, wird an Sie als eShop-Administrator eine E-Mail gesendet, dass eine Bestellung im eShop gemacht wurde. Diese E-Mails werden an <span class="navipath_or_inputname">Bestellungen an</span> gesendet.',

'HELP_SHOP_MAIN_SMTPSERVER'                   => 'Die SMTP-Daten müssen eingegeben werden, damit der eShop E-Mails, beispielsweise die Bestellbestätigung, versenden kann.',

'HELP_ARTICLE_MAIN_ALDPRICE'                  => 'Mit <span class="navipath_or_inputname">Alt. Preise</span> können Sie für bestimmte Benutzer spezielle Preise einrichten (Benutzergruppen "Preis A", "Preis B" und "Preis C").',

'HELP_ARTICLE_MAIN_VAT'                       => 'Hier können Sie für diesen Artikel einen speziellen Mehrwertsteuersatz eingeben (z. B: 7% für Lebensmittel).',

'HELP_ARTICLE_MAIN_TAGS'                      => 'Hier können Sie passende Stichworte zum Artikel eingeben. Aus diesen Stichworten wird die Tagcloud (Stichwortwolke) auf der Startseite generiert. Tags werden durch Komma getrennt.',

'HELP_ARTICLE_EXTEND_UNITQUANTITY'            => 'Mit <span class="navipath_or_inputname">Menge</span> und <span class="navipath_or_inputname">Mengeneinheit</span> können Sie den Grundpreis des Artikels (Preis pro Mengeneinheit) einstellen. Dieser wird berechnet und beim Artikel angezeigt (z.B. 1,43 EUR pro Liter). Geben Sie bei <span class="navipath_or_inputname">Menge</span> die Menge des Artikels (z.B. 1,5) ein und legen Sie bei <span class="navipath_or_inputname">Mengeneinheit</span> die entsprechende Mengeneinheit (z.B. Liter) fest. Sie können eine Mengeneinheit aus der Liste auswählen oder eine Mengeneinheit eintragen, ohne eine Mengeneinheit auszuwählen ("-"). </br> Wie Sie die Liste der Mengeneinheiten erweitern können, wird in diesem <a href="http://wiki.oxidforge.org/Tutorials/Adding_new_unit_types" target="_blank">Tutorial</a> beschrieben.',

'HELP_ARTICLE_EXTEND_EXTURL'                  => 'Bei <span class="navipath_or_inputname">Externe URL</span> können Sie einen Link eingeben, wo weitere Informationen zu dem Artikel erhältlich sind (z. B. auf der Hersteller-Website). Bei <span class="navipath_or_inputname">Text für ext. URL</span> können Sie den Text eingeben, der verlinkt wird (z. B. <span class="userinput_or_code">weitere Informationen vom Hersteller</span>).',

'HELP_ARTICLE_EXTEND_TPRICE'                  => 'Bei <span class="navipath_or_inputname">UVP</span> können Sie die Unverbindliche Preisempfehlung des Herstellers eingeben. Wenn Sie die UVP eingeben, wird diese den Benutzern angezeigt: Beim Artikel wird über dem Preis <span class="filename_filepath_or_italic">statt UVP nur</span> angezeigt.',

'HELP_ARTICLE_EXTEND_QUESTIONEMAIL'           => 'Bei <span class="navipath_or_inputname">Alt. Anspr.partn.</span> können Sie eine E-Mail Adresse eingeben. Wenn die Benutzer eine Frage zu diesem Artikel absenden, wird Sie an diese E-Mail Adresse geschickt. Wenn keine E-Mail Adresse eingetragen ist, wird die Anfrage an die normale Info E-Mail Adresse geschickt.',


'HELP_ARTICLE_EXTEND_SKIPDISCOUNTS'           => 'Wenn <span class="navipath_or_inputname">Alle neg. Nachlässe ignorieren</span> aktiviert ist, werden für diesen Artikel keine negativen Nachlässe berechnet. Das sind z. B. Rabatte und Gutscheine.',

'HELP_ARTICLE_EXTEND_NONMATERIAL'             => 'Einstellung wird vom Vater-Artikel an die Varianten vererbt und gilt für den gesamten Artikel.',

'HELP_ARTICLE_EXTEND_FREESHIPPING'            => 'Einstellung wird vom Vater-Artikel an die Varianten vererbt und gilt für den gesamten Artikel.',

'HELP_ARTICLE_EXTEND_BLFIXEDPRICE'            => 'Der Preisalarm kann für diesen Artikel ausgeschaltet werden.',

'HELP_ARTICLE_EXTEND_TEMPLATE'                => 'Sie können die Detailansicht des Artikels mit einem anderen Template anzeigen lassen. Tragen Sie dazu Pfad und Namen des Templates ein, das verwendet werden soll.',

'HELP_ARTICLE_EXTEND_ISCONFIGURABLE'          => 'Wenn der Artikel individualisierbar ist, wird den Kunden ein zusätzliches Eingabefeld auf der Detailseite des Artikels und im Warenkorb angezeigt. In dieses Eingabefeld können Kunden Text eingeben, um den Artikel zu individualisieren.<br><br>'.
                                                 'Ein typisches Beispiel sind T-Shirts, die bedruckt werden können. In das Eingabefeld können Kunden den Text eingeben, mit dem ein T-Shirt bedruckt werden soll.',

'HELP_ARTICLE_EXTEND_UPDATEPRICE'             => 'Preise können zu einem festgelegten Zeitpunkt geändert werden. Die eingetragenen Preise aktualisieren die Standardpreise. Hat ein Preis den Wert "0", wird er nicht aktualisiert.',

'HELP_ARTICLE_FILES_MAX_DOWNLOADS_COUNT'      => 'Geben Sie hier an, wie oft Benutzer nach einer Bestellung den Link zum Download verwenden können. Hier können Sie für diese Datei die Standardeinstellung überschreiben, die in Stammdaten -> Grundeinstellung -> Einstell. -> Downloads für alle Artikel gesetzt wurde.',

'HELP_ARTICLE_FILES_LINK_EXPIRATION_TIME_UNREGISTERED' => 'Geben Sie hier an, wie oft Benutzer, die ohne Registrierung bestellt haben, den Link zum Download verwenden können. Hier können Sie für diese Datei die Standardeinstellung überschreiben, die in Stammdaten -> Grundeinstellung -> Einstell. -> Downloads für alle Artikel gesetzt wurde.',

'HELP_ARTICLE_FILES_LINK_EXPIRATION_TIME'     => 'Geben Sie die Zeit in Stunden an, die der Downloadlink nach der Bestellung gültig ist. Hier können Sie für diese Datei die Standardeinstellung überschreiben, die in Stammdaten -> Grundeinstellung -> Einstell. -> Downloads für alle Artikel gesetzt wurde.',

'HELP_ARTICLE_FILES_DOWNLOAD_EXPIRATION_TIME' => 'Geben Sie die Zeit in Stunden an, die der Downloadlink nach dem ersten Download gültig ist. Hier können Sie für diese Datei die Standardeinstellung überschreiben, die in Stammdaten -> Grundeinstellung -> Einstell. -> Downloads für alle Artikel gesetzt wurde.',

'HELP_ARTICLE_FILES_NEW'                      => 'Geben Sie den Namen einer per FTP übertragenen Datei an oder laden Sie hier eine neue Datei hoch. Große Dateien sollten immer per FTP übertragen werden. Die Beschränkung der Dateigröße gilt nur für das Hochladen im Administrationsbereich. Sie hängt von den PHP-Einstellungen des Servers ab, die nur dort geändert werden können.',

'HELP_ARTICLE_PICTURES_ICON'                  => 'Icons sind die kleinsten Bilder eines Artikels, sie werden z. B. im Warenkorb verwendet. <br>'.
                                                 'Wenn Sie ein Icon manuell hochladen, wird das automatisch erzeugte Icon überschrieben.<br>' .
                                                 'Nach dem Upload wird der Dateiname des Bildes in Icon angezeigt. Solange noch kein Icon hochgeladen/automatisch generiert wurde, wird --- angezeigt.',

'HELP_ARTICLE_PICTURES_THUMB'                 => 'Thumbnails sind kleine Bilder eines Artikels, sie werden z. B. in der in Artikellisten verwendet. <br>' .
                                                 'Wenn Sie ein Thumbnail manuell hochladen, wird das automatisch erzeugte Thumbnail überschrieben.<br>' .
                                                 'Nach dem Upload wird der Dateiname des Bildes in Thumbnail angezeigt. Solange noch kein Thumbnail hochgeladen/automatisch generiert wurde, wird --- angezeigt.',

'HELP_ARTICLE_PICTURES_PIC1'                  => 'Artikelbilder werden in der Detailansicht eines Artikels verwendet. Sie können bis zu 7 Artikelbilder pro Artikel hochladen. Nach dem Hochladen wird der Dateiname im jeweiligen Eingabefeld angezeigt. Wenn noch kein Bild hochgeladen wurde, wird --- angezeigt. <br>' .
                                                 'Es können Bilder mit maximal 2 MB oder 1500*1500 Pixel Auflösung hochgeladen werden. Diese Einschränkung gilt, um Probleme mit dem PHP-Speicherlimit zu vermeiden. Danach wird aus diesem Bild automatisch das Artikelbild, Zoombild, Thumbnail und Icon generiert.',

'HELP_ARTICLE_PICTURES_ZOOM1'                 => 'Zoom-Bilder sind extra große Artikelbilder, Die in der Detailansicht eines Artikels verlinkt werden. <br>' .
                                                 'Zoom-Bilder können Sie bei <span class="navipath_or_inputname">Zoom X hochladen</span> hochladen. Bei <span class="navipath_or_inputname">Zoom X</span> wird nach dem Hochladen der Dateiname des Zoom-Bildes angezeigt, wenn noch kein Zoom-Bild hochgeladen wurde, wird <span class="userinput_or_code">nopic.jpg</span> angezeigt.',

'HELP_ARTICLE_STOCK_REMINDACTIV'              => 'Einstellung wird vom Vater-Artikel an die Varianten vererbt und gilt für den gesamten Artikel.',

'HELP_ARTICLE_STOCK_STOCKFLAG'                => 'Hier können Sie einstellen, wie sich der eShop verhält, wenn der Artikel ausverkauft ist:<br>' .
                                                 '<ul><li>Standard: Der Artikel kann auch dann bestellt werden, wenn er ausverkauft ist.</li>' .
                                                 '<li>Fremdlager: Der Artikel kann immer gekauft werden und wird immer als <span class="filename_filepath_or_italic">auf Lager</span> angezeigt. (In einem Fremdlager kann der Lagerbestand nicht ermittelt werden. Deswegen wird der Artikel immer als auf Lager geführt).</li>' .
                                                 '<li>Wenn Ausverkauft offline: Der Artikel wird nicht angezeigt, wenn er ausverkauft ist.</li>' .
                                                 '<li>Wenn Ausverkauft nicht bestellbar: Der Artikel wird angezeigt, wenn er ausverkauft ist, aber er kann nicht bestellt werden.</li></ul>',

'HELP_ARTICLE_STOCK_REMINDAMAOUNT'            => 'Hier können Sie einrichten, dass Ihnen eine E-Mail gesendet wird, sobald der der Lagerbestand unter den hier eingegebenen Wert sinkt. Dadurch werden Sie rechtzeitig informiert, wenn der Artikel fast ausverkauft ist. Setzen Sie hierzu das Häkchen und geben Sie den Bestand ein, ab dem Sie informiert werden wollen.',

'HELP_ARTICLE_STOCK_DELIVERY'                 => 'Hier können Sie eingeben, ab wann ein Artikel wieder lieferbar ist, wenn er ausverkauft ist. Das Format ist Jahr-Monat-Tag, z. B. 2008-10-21.',

'HELP_ARTICLE_SEO_FIXED'                      => 'Sie können die SEO URLs vom eShop neu berechnen lassen. Eine Artikelseite bekommt z. B. eine neue SEO URL, wenn Sie den Titel des Artikels ändern. Die Einstellung <span class="navipath_or_inputname">URL fixiert</span> unterbindet das: Wenn sie aktiv ist, wird die alte SEO URL beibehalten und keine neue SEO URL berechnet.',

'HELP_ARTICLE_SEO_KEYWORDS'                   => 'Diese Stichwörter werden in den HTML-Quelltext (Meta Keywords) eingebunden. Diese Information wird von Suchmaschinen ausgewertet. Hier können Sie passende Stichwörter zu dem Artikel eingeben. Wenn Sie nichts eingeben, werden die Stichwörter automatisch erzeugt.',

'HELP_ARTICLE_SEO_DESCRIPTION'                => 'Dieser Beschreibungstext wird in den HTML-Quelltext (Meta Description) eingebunden. Dieser Text wird von vielen Suchmaschinen bei den Suchergebnissen angezeigt. Hier können Sie eine passende Beschreibung zu dem Artikel eingeben. Wenn Sie nichts eingeben, wird die Beschreibung automatisch erzeugt.',

'HELP_ARTICLE_SEO_ACTCAT'                     => 'Sie können für einen Artikel unterschiedliche SEO URLs festlegen: Für bestimmte Kategorien und für den Hersteller des Artikels. Mit <span class="navipath_or_inputname">Aktive Kategorie/Hersteller</span> können Sie wählen, welche SEO URL Sie anpassen möchten.',

'HELP_ARTICLE_STOCK_STOCKTEXT'                => 'Hier können Sie eine Meldung eingeben, die beim Artikel angezeigt wird, falls der Artikel auf Lager ist.',

'HELP_ARTICLE_STOCK_NOSTOCKTEXT'              => 'Hier können Sie eine Meldung eingeben, die beim Artikel angezeigt wird, falls der Artikel ausverkauft ist.',

'HELP_ARTICLE_STOCK_AMOUNTPRICE_AMOUNTFROM'   => 'Mit <span class="navipath_or_inputname">Menge von/bis</span> stellen Sie ein, für welchen Mengenbereich der Staffelpreis gültig ist.',

'HELP_ARTICLE_STOCK_AMOUNTPRICE_PRICE'        => 'Bei <span class="navipath_or_inputname">Preis </span>können Sie den Preis für die eingegebene Menge einstellen. Sie haben die Möglichkeit, den Preis absolut einzugeben oder prozentualen Rabatt einzurichten.',


'HELP_ARTICLE_VARIANT_VARNAME'                => 'Bei <span class="navipath_or_inputname">Name der Auswahl</span> können Sie einstellen, wie die Auswahl zwischen den verschiedenen Varianten heißen soll, z. B. <span class="userinput_or_code">Farbe</span> oder <span class="userinput_or_code">Größe</span>.',

'HELP_ARTICLE_IS_DOWNLOADABLE'                => 'Dateien dieses Artikels können heruntergeladen werden.',

'HELP_ATTRIBUTE_MAIN_DISPLAYINBASKET'         => 'Wenn ausgewählt, wird der Wert dieses Attributs im Warenkorb und in der Bestellübersicht unter dem Artikeltitel angezeigt.',

'HELP_CATEGORY_MAIN_HIDDEN'                   => 'Mit <span class="navipath_or_inputname">Versteckt</span> können Sie einstellen, ob die Kategorie den Benutzern angezeigt werden soll. Wenn eine Kategorie versteckt ist, wird Sie den Benutzern nicht angezeigt, auch wenn die Kategorie aktiv ist.',

'HELP_CATEGORY_MAIN_PARENTID'                 => 'Bei <span class="navipath_or_inputname">Unterkategorie von</span> stellen Sie ein, an welcher Stelle die Kategorie erscheinen soll:' .
                                                 '<ul>' .
                                                 '<li>Wenn die Kategorie keiner anderen Kategorie untergeordnet sein soll, dann wählen Sie <span class="userinput_or_code">--</span> aus.</li>' .
                                                 '<li>Wenn die Kategorie einer anderen Kategorie untergeordnet sein soll, dann wählen Sie die entsprechende Kategorie aus.</li>' .
                                                 '</ul>',

'HELP_CATEGORY_MAIN_EXTLINK'                  => 'Bei <span class="navipath_or_inputname">Externer Link</span> können Sie einen Link eingeben, der geöffnet wird, wenn Benutzer auf die Kategorie klicken. <span class="warning_or_important_hint">Verwenden Sie diese Funktion nur, wenn Sie einen Link in der Kategorien-Navigation anzeigen wollen. Die Kategorie verliert dadurch Ihre normale Funktion!</span>',

'HELP_CATEGORY_MAIN_PRICEFROMTILL'            => 'Mit <span class="navipath_or_inputname">Preis von/bis</span> können sie einstellen, dass in der Kategorie <span class="warning_or_important_hint">alle</span> Artikel angezeigt werden, die einen bestimmten Preis haben. Im ersten Eingabefeld wird die Untergrenze eingegeben, in das zweite Eingabefeld die Obergrenze. Dann werden in der Kategorie <span class="warning_or_important_hint">alle Artikel Ihres eShops</span> angezeigt, die einen entsprechenden Preis haben.',

'HELP_CATEGORY_MAIN_DEFSORT'                  => 'Mit <span class="navipath_or_inputname">Schnellsortierung</span> stellen Sie ein, wie die Artikel in der Kategorie sortiert werden. Mit <span class="navipath_or_inputname">asc</span> und <span class="navipath_or_inputname">desc</span> stellen Sie ein, ob auf- oder absteigend sortiert wird.',

'HELP_CATEGORY_MAIN_SORT'                     => 'Mit <span class="navipath_or_inputname">Sortierung</span> können Sie festlegen, in welcher Reihenfolge die Kategorien angezeigt werden: Die Kategorie mit der kleinsten Zahl wird oben angezeigt, die Kategorie mit der größten Zahl unten.',

'HELP_CATEGORY_MAIN_THUMB'                    => 'Bei <span class="navipath_or_inputname">Bild</span> und <span class="navipath_or_inputname">Bild hochladen</span> können Sie ein Bild für die Kategorie hochladen, dieses Bild wird dann in der Kategorienansicht oben angezeigt. <br>' .
                                                 'Wählen Sie bei <span class="navipath_or_inputname">Bild hochladen</span> das entsprechende Bild aus. Wenn Sie auf Speichern klicken, wird das Bild hochgeladen. Nachdem das Bild hochgeladen ist, wird der Dateiname des Bildes in <span class="navipath_or_inputname">Bild</span> angezeigt.',

'HELP_CATEGORY_MAIN_PROMOTION_ICON'           => 'Das Bild für die Startseite wird angezeigt, wenn diese Kategorie auf der Startseite beworben wird. Siehe auch <span class="navipath_or_inputname">Kundeninformationen -> Aktionen verwalten -> Kategorien-Topangebot</span>.',

'HELP_CATEGORY_MAIN_SKIPDISCOUNTS'            => 'Wenn <span class="navipath_or_inputname">Alle neg. Nachlässe ignorieren</span> aktiv ist, werden für alle Artikel in dieser Kategorie keine negativen Nachlässe berechnet.',

'HELP_CATEGORY_SEO_FIXED'                     => 'Sie können die SEO URLs vom eShop neu berechnen lassen. Eine Kategorie bekommt z. B. eine neue SEO URL, wenn Sie den Titel der Kategorie ändern. Die Einstellung <span class="navipath_or_inputname">URL fixiert</span> unterbindet das: Wenn sie aktiv ist, wird die alte SEO URL beibehalten und keine neue SEO URL berechnet.',

'HELP_CATEGORY_SEO_SHOWSUFFIX'                => 'Diese Einstellung bestimmt, ob das Suffix für den Fenstertitel angezeigt wird, wenn die Kategorieseite im eShop aufgerufen wird. Das Titel Suffix können Sie unter <span class="navipath_or_inputname">Stammdaten -> Grundeinstellungen -> SEO -> Titel Suffix</span> einstellen.',

'HELP_CATEGORY_SEO_KEYWORDS'                  => 'Diese Stichwörter werden in den HTML-Quelltext (Meta Keywords) eingebunden. Diese Information wird von Suchmaschinen ausgewertet. Hier können Sie passende Stichwörter zu der Kategorie eingeben. Wenn Sie nichts eingeben, werden die Stichwörter automatisch erzeugt.',

'HELP_CATEGORY_SEO_DESCRIPTION'               => 'Dieser Beschreibungstext wird in den HTML-Quelltext (Meta Description) eingebunden. Dieser Text wird von vielen Suchmaschinen bei den Suchergebnissen angezeigt. Hier können Sie eine passende Beschreibung für die Kategorie eingeben. Wenn Sie nichts eingeben, wird die Beschreibung automatisch erzeugt.',

'HELP_CONTENT_MAIN_SNIPPET'                   => 'Wenn Sie <span class="navipath_or_inputname">Snippet</span> auswählen, können Sie die CMS-Seite in anderen Seiten mit Hilfe des Idents einbinden: <span class="userinput_or_code">[{ oxcontent ident=Ident_der_CMS_Seite }]</span>',

'HELP_CONTENT_MAIN_MAINMENU'                  => 'Wenn Sie <span class="navipath_or_inputname">Hauptmenü</span> auswählen, wird in der oberen Menüleiste ein Link zu der CMS-Seite angezeigt (bei AGB und Impressum).',

'HELP_CONTENT_MAIN_CATEGORY'                  => 'Wenn Sie <span class="navipath_or_inputname">Kategorie</span> auswählen, wird in der Kategoriennavigation unter den normalen Kategorien ein Link zu der CMS-Seite angezeigt.',

'HELP_CONTENT_MAIN_MANUAL'                    => 'Wenn Sie <span class="navipath_or_inputname">Manuell</span> auswählen, wird ein Link erzeugt, mit dem Sie die CMS-Seite in andere CMS-Seiten einbinden können. Der Link wird weiter unten angezeigt, wenn Sie auf Speichern klicken.',



'HELP_CONTENT_SEO_FIXED'                      => 'Sie können die SEO URLs vom eShop neu berechnen lassen. Eine CMS-Seite bekommt z. B. eine neue SEO URL, wenn Sie den Titel der CMS-Seite ändern. Die Einstellung <span class="navipath_or_inputname">URL fixiert</span> unterbindet das: Wenn sie aktiv ist, wird die alte SEO URL beibehalten und keine neue SEO URL berechnet.',

'HELP_CONTENT_SEO_KEYWORDS'                   => 'Diese Stichwörter werden in den HTML-Quelltext (Meta Keywords) eingebunden. Diese Information wird von Suchmaschinen ausgewertet. Hier können Sie passende Stichwörter zu der CMS-Seite eingeben. Wenn Sie nichts eingeben, werden die Stichwörter automatisch erzeugt.',

'HELP_CONTENT_SEO_DESCRIPTION'                => 'Dieser Beschreibungstext wird in den HTML-Quelltext (Meta Description) eingebunden. Dieser Text wird von vielen Suchmaschinen bei den Suchergebnissen angezeigt. Hier können Sie eine passende Beschreibung für die CMS-Seite eingeben. Wenn Sie nichts eingeben, wird die Beschreibung automatisch erzeugt.',
'HELP_DELIVERY_MAIN_COUNTRULES'               => 'Mit dieser Einstellung können Sie auswählen, wie oft der Preis Auf-/Abschlag berechnet wird:<br>' .
                                                 '<ul><li>Einmal pro Warenkorb: Der Preis wird einmal für die gesamte Bestellung berechnet.</li>' .
                                                 '<li>Einmal pro unterschiedlichem Artikel: Der Preis wird für jeden unterschiedlichen Artikel im Warenkorb einmal berechnet. Wie oft ein Artikel bestellt wird, ist dabei egal.</li>' .
                                                 '<li>Für jeden Artikel: Der Preis wird für jeden Artikel im Warenkorb berechnet.</li></ul>',

'HELP_DELIVERY_MAIN_CONDITION'                => 'Mit <span class="navipath_or_inputname">Bedingung</span> können Sie einstellen, dass die Versandkostenregel nur für eine bestimmte Bedingung gültig ist. Sie können zwischen 4 Bedingungen wählen:<br>' .
                                                 '<ul><li>Menge: Anzahl aller Artikel im Warenkorb.</li>' .
                                                 '<li>Größe: Die Gesamtgröße aller Artikel.</li>' .
                                                 '<li>Gewicht: Das Gesamtgewicht der Bestellung in Kilogramm.</li>' .
                                                 '<li>Preis: Der Einkaufswert der Bestellung.</li></ul>' .
                                                 'Mit den Eingabefeldern <span class="navipath_or_inputname">>=</span> (größer gleich) und <span class="navipath_or_inputname"><=</span> (kleiner gleich) können Sie den Bereich einstellen, für den die Bedingung gültig sein soll. Bei <span class="navipath_or_inputname"><=</span> muss eine größere Zahl als bei <span class="navipath_or_inputname">>=</span> eingegeben werden.',

'HELP_DELIVERY_MAIN_PRICE'                    => 'Mit <span class="navipath_or_inputname">Preis Auf-/Abschlag</span> können Sie eingeben, wie hoch die Versandkosten sind. Der Preis kann auf zwei verschiedene Arten berechnet werden:' .
                                                 '<ul>' .
                                                 '<li>Mit <span class="userinput_or_code">abs</span> wird der Preis absolut angegeben (z. B.: Mit <span class="userinput_or_code">6,90</span> werden 6,90 Euro berechnet).</li>' .
                                                 '<li>Mit <span class="userinput_or_code">%</span> wird der Preis relativ zum Einkaufswert angegeben (z. B.: Mit <span class="userinput_or_code">10</span> werden 10% des Einkaufswerts berechnet).</li>' .
                                                 '</ul>',

'HELP_DELIVERY_MAIN_ORDER'                    => 'Mit <span class="navipath_or_inputname">Reihenfolge der Regelberechnung</span> können Sie festlegen, in welcher Reihenfolge die Versandkostenregeln berechnet werden: Die Versandkostenregel mit der kleinsten Zahl wird als erstes berechnet. Die Reihenfolge ist wichtig, wenn die Einstellung <span class="navipath_or_inputname">Keine weiteren Regeln nach dieser berechnen</span> verwendet wird.',

'HELP_DELIVERY_MAIN_FINALIZE'                 => 'Mit <span class="navipath_or_inputname">Keine weiteren Regeln nach dieser berechnen</span> können Sie einstellen, dass keine weitere Versandkostenregeln berechnet werden, falls diese Versandkostenregel gültig ist und berechnet wird. Für diese Einstellung ist die Reihenfolge wichtig, in der die Versandkostenregeln berechnet werden: Sie wird durch <span class="navipath_or_inputname">Reihenfolge der Regelberechnung</span> festgelegt.',



'HELP_DELIVERYSET_MAIN_POS'                   => 'Mit <span class="navipath_or_inputname">Sortierung</span> können Sie einstellen, in welcher Reihenfolge die Versandarten den Benutzern angezeigt werden:<br>' .
                                                 '<ul><li>Die Versandart mit der niedrigsten Zahl wird ganz oben angezeigt.</li>' .
                                                 '<li>Die Versandart mit der höchsten Zahl wird ganz unten angezeigt.</li></ul>',



'HELP_DISCOUNT_MAIN_PRICE'                    => 'Mit <span class="navipath_or_inputname">Einkaufswert</span> können Sie einstellen, dass der Rabatt nur für bestimmte Einkaufswerte gültig ist. Wenn der Rabatt für alle Einkaufswerte gültig sein soll, dann geben Sie in <span class="navipath_or_inputname">von</span> und in <span class="navipath_or_inputname">bis</span> <span class="userinput_or_code">0</span> ein.',

'HELP_DISCOUNT_MAIN_AMOUNT'                   => 'Mit <span class="navipath_or_inputname">Einkaufsmenge</span> können Sie einstellen, dass der Rabatt nur für bestimmte Einkaufsmengen gültig ist. Wenn Sie möchten, dass der Rabatt für alle Einkaufsmengen gültig ist, dann geben Sie in <span class="navipath_or_inputname">von</span> und in <span class="navipath_or_inputname">bis</span> <span class="userinput_or_code">0</span> ein.',

'HELP_DISCOUNT_MAIN_REBATE'                   => 'Bei <span class="navipath_or_inputname">Rabatt</span> stellen Sie ein, wie hoch der Rabatt sein soll. Mit der Auswahlliste hinter dem Eingabefeld können Sie auswählen, ob der Rabatt absolut oder prozentual sein soll:' .
                                                 '<ul>' .
                                                 '<li><span class="userinput_or_code">abs</span>: Der Rabatt ist absolut, z. B. 5 Euro.</li>' .
                                                 '<li><span class="userinput_or_code">%</span>: Der Rabatt ist prozentual, z. B. 10 Prozent vom Einkaufswert.</li>' .
                                                 '<li><span class="userinput_or_code">itm</span>: Rabatt in Form eines kostenfreien Artikels (Dreingabe). Legen Sie die Menge für diesen Artikel fest. Wählen Sie "Multiplizieren", wenn die Menge des rabattierten Artikels im Warenkorb mit der Menge des kostenlosen Artikels multipliziert werden soll.</li>'.
                                                 '</ul>',



'HELP_GENERAL_SEO_ACTCAT'                     => 'Sie können für einen Artikel unterschiedliche SEO URLs festlegen: Für bestimmte Kategorien und für den Hersteller des Artikels. Mit <span class="navipath_or_inputname">Aktive Kategorie/Hersteller</span> können Sie wählen, welche SEO URL Sie anpassen möchten.',

'HELP_GENERAL_SEO_FIXED'                      => 'Wenn sich die Daten eines Artikels, Kategorie usw. ändern, wird auch die SEO URL neu berechnet. Eine Kategorie bekommt z. B. eine neue SEO URL, wenn Sie den Titel der Kategorie ändern. Die Einstellung <span class="navipath_or_inputname">URL fixiert</span> unterbindet das: Wenn sie aktiv ist, wird die alte SEO URL beibehalten und keine neue SEO URL berechnet.',

'HELP_GENERAL_SEO_SHOWSUFFIX'                 => 'Diese Einstellung bestimmt, ob das Titel-Suffix im Seitentitel angezeigt wird, wenn die entsprechende Seite im eShop aufgerufen wird. Das Titel Suffix können Sie unter <span class="navipath_or_inputname">Stammdaten -> Grundeinstellungen -> SEO -> Titel Suffix</span> einstellen.',

'HELP_GENERAL_SEO_OXKEYWORDS'                 => 'Diese Stichwörter werden in den HTML-Quelltext (Meta Keywords) eingebunden. Diese Stichwörter werden von Suchmaschinen ausgewertet. Wenn Sie nichts eingeben, werden die Stichwörter automatisch erzeugt.',

'HELP_GENERAL_SEO_OXDESCRIPTION'              => 'Dieser Beschreibungstext wird in den HTML-Quelltext (Meta Description) eingebunden. Dieser Text wird von vielen Suchmaschinen bei den Suchergebnissen angezeigt. Wenn Sie nichts eingeben, wird die Beschreibung automatisch erzeugt.',



'HELP_GENIMPORT_FIRSTCOLHEADER'               => 'Aktivieren Sie diese Einstellung, falls in der ersten Zeile der CSV-Datei die Datenbank-Spalten stehen, zu denen die Daten zugeordnet werden sollen. Dann wird die Zuordnung der Daten zu den entsprechenden Datenbank-Spalten automatisch vorgenommen.',

'HELP_GENIMPORT_REPEATIMPORT'                 => 'Wenn Sie diese Einstellung aktivieren, wird nach erfolgreichem Import automatisch wieder Schritt 1 angezeigt, damit sie den nächsten Import machen können.',

'HELP_LANGUAGE_DEFAULT'                       => 'Die Standardsprache wird verwendet, wenn OXID eShop die zu verwendete Sprache nicht anderweitig ermitteln kann: Die gewünschte Sprache ist nicht in der URL angegeben, kann nicht über den Browser ermittelt werden und ist nicht in der aktuellen Session gespeichert. Die Standardsprache kann deaktiviert, aber nicht gelöscht werden.',

'HELP_LANGUAGE_ACTIVE'                        => 'Aktivieren Sie diese Einstellung, um die Sprache für das eShop Frontend zu aktivieren. Die Sprache ist stets im Admin verfügbar, selbst wenn sie für das Frontend inaktiv ist.',

'HELP_PAYMENT_MAIN_SORT'                      => 'Mit <span class="navipath_or_inputname">Sortierung</span> können Sie einstellen, in welcher Reihenfolge die Zahlungsarten den Benutzern angezeigt werden:<br>' .
                                                 '<ul><li>Die Zahlungsart mit der niedrigsten Zahl wird an erster Stelle angezeigt.</li>' .
                                                 '<li>Die Zahlungsart mit der höchsten Zahl wird an letzter Stelle angezeigt.</li></ul>',

'HELP_PAYMENT_MAIN_FROMBONI'                  => 'Hier können Sie einstellen, dass die Zahlungsarten nur Benutzern zur Verfügung stehen, die mindestens einen bestimmten Bonitätsindex haben. Den Bonitätsindex können Sie für jeden Benutzer unter <span class="filename_filepath_or_italic">Benutzer verwalten -> Benutzer -> Erweitert</span> eingeben.',

'HELP_PAYMENT_MAIN_SELECTED'                  => 'Mit <span class="navipath_or_inputname">Ausgewählt</span> können Sie bestimmen, welche Zahlungsart als Standard ausgewählt sein soll, wenn Benutzer im dritten Bestellschritt zwischen verschiedenen Zahlungsarten wählen können.',

'HELP_PAYMENT_MAIN_AMOUNT'                    => 'Mit <span class="navipath_or_inputname">Einkaufswert</span> können Sie einstellen, dass die Zahlungsart nur für bestimmte Einkaufswerte gültig ist. Mit den Feldern <span class="navipath_or_inputname">von</span> und <span class="navipath_or_inputname">bis</span> können Sie den Bereich einstellen.<br>' .
                                                    'Wenn die Zahlungsart für jeden Einkaufswert gültig sein soll, müssen Sie eine Bedingung eingeben, die immer gültig ist: Geben sie in das Feld <span class="navipath_or_inputname">von</span> <span class="userinput_or_code">0</span> ein, in das Feld <span class="navipath_or_inputname">bis</span> <span class="userinput_or_code">999999999</span>.',

'HELP_PAYMENT_MAIN_ADDPRICE'                  => 'Bei <span class="navipath_or_inputname">Preisauf-/abschlag</span> wird der Preis für die Zahlungsart eingegeben. Die Preise können auf zwei verschiedene Arten angegeben werden:' .
                                                 '<ul>' .
                                                 '<li>Mit <span class="userinput_or_code">abs</span> wird der Preis absolut angegeben (z. B.: Wenn Sie <span class="userinput_or_code">7,50</span> eingeben, werden 7,50 Euro berechnet).</li>' .
                                                 '<li>Mit <span class="userinput_or_code">%</span> wird der Preis relativ zum Einkaufspreis berechnet (z. B.: Wenn Sie <span class="userinput_or_code">2</span> eingeben, werden 2 Prozent des Einkaufspreises berechnet).</li>' .
                                                 '</ul>' .
                                                 'Sie können auch negative Werte eingeben. Dadurch wird der eingegebene Wert abgezogen: Wenn Sie z. B. <span class="userinput_or_code">-2</span> eingeben und <span class="userinput_or_code">%</span> auswählen, werden 2% des Einkaufswerts abgezogen.',

'HELP_PAYMENT_MAIN_ADDSUMRULES'               => 'Zur Berechnung von Preisauf- oder abschlägen wird der Warenkorbwert als Basissumme verwendet. Legen Sie fest, welche Kosten in die Berechnung des Warenkorbwertes einbezogen werden.',


'HELP_SELECTLIST_MAIN_TITLEIDENT'             => 'Bei <span class="navipath_or_inputname">Arbeitstitel</span> können Sie einen zusätzlichen Titel eingeben, der den Benutzern Ihres eShops nicht angezeigt wird. Sie können den Arbeitstitel dazu verwenden, um ähnliche Auswahllisten zu unterscheiden (z. B. <span class="filename_filepath_or_italic">Größe für Hosen</span> und <span class="filename_filepath_or_italic">Größe für Hemden</span>).',

'HELP_SELECTLIST_MAIN_FIELDS'                 => 'In der Liste <span class="navipath_or_inputname">Felder</span> werden alle vorhandenen Ausführungen der Auswahlliste angezeigt. Mit den Eingabefeldern rechts neben <span class="navipath_or_inputname">Felder</span> können Sie neue Ausführungen anlegen.',

'HELP_USER_MAIN_HASPASSWORD'                  => 'Hier wird angezeigt, ob der Benutzer ein Passwort hat. Daran können Sie unterscheiden, ob sich der Benutzer bei der Bestellung registriert hat:' .
                                                 '<ul><li>Wenn ein Passwort vorhanden ist, hat sich der Benutzer registriert.</li>' .
                                                 '<li>Wenn kein Passwort vorhanden ist, hat der Benutzer bestellt ohne sich zu registrieren.</li></ul>',

'HELP_USER_PAYMENT_METHODS'                   => 'Auf dieser Registerkarte können Sie:'.
                                                 '<ul><li>Zahlungsarten des Benutzers anzeigen und verwalten.'.
                                                 '<li>Neue Zahlungsarten anlegen und Default-Werte eintragen, beispielsweise Bankeinzug/Lastschrift.</li></ul>',

'HELP_USER_EXTEND_NEWSLETTER'                 => 'Diese Einstellung zeigt an, ob der Benutzer den Newsletter abonniert hat oder nicht.',

'HELP_USER_EXTEND_EMAILFAILED'                => 'Wenn an die E-Mail Adresse des Benutzers keine E-Mails versendet werden können (z. B. weil die Adresse falsch eingetragen ist), dann setzen Sie hier das Häkchen. Dann werden dem Benutzer keine Newsletter mehr zugesendet. Andere E-Mails werden weiterhin versendet.',

'HELP_USER_EXTEND_DISABLEAUTOGROUP'           => 'Die Benutzer werden automatisch zu Benutzergruppen zugeordnet. Wenn Sie diese Einstellung aktivieren, wird dieser Benutzer nicht mehr automatisch zugeordnet.',

'HELP_USER_EXTEND_BONI'                       => 'Hier können Sie einen Zahlenwert für die Bonität des Benutzers eingeben. Mit der Bonität können Sie beeinflussen, welche Zahlungsarten dem Benutzer zur Verfügung stehen.',



'HELP_MANUFACTURER_MAIN_ICON'                 => 'Bei <span class="navipath_or_inputname">Icon</span> und <span class="navipath_or_inputname">Hersteller-Icon hochladen</span> können Sie ein Bild für den Hersteller hochladen (z. B. das Logo des Herstellers). Wählen Sie bei <span class="navipath_or_inputname">Hersteller-Icon hochladen</span> das Bild aus, das Sie hochladen möchten. Wenn Sie auf Speichern klicken, wird das Bild hochgeladen. Nachdem das Bild hochgeladen ist, wird der Dateiname des Bildes in <span class="navipath_or_inputname">Icon</span> angezeigt.',



'HELP_MANUFACTURER_SEO_FIXED'                 => 'Sie können die SEO URLs vom eShop neu berechnen lassen. Eine Herstellerseite bekommt z. B. eine neue SEO URL, wenn Sie den Titel des Herstellers ändern. Die Einstellung <span class="navipath_or_inputname">URL fixiert</span> unterbindet das: Wenn sie aktiv ist, wird die alte SEO URL beibehalten und keine neue SEO URL berechnet.',

'HELP_MANUFACTURER_SEO_SHOWSUFFIX'            => 'Diese Einstellung bestimmt, ob das Suffix für den Fenstertitel angezeigt wird, wenn die Herstellerseite im eShop aufgerufen wird. Das Titel Suffix können Sie unter <span class="navipath_or_inputname">Stammdaten -> Grundeinstellungen -> SEO -> Titel Suffix</span> einstellen.',

'HELP_MANUFACTURER_SEO_KEYWORDS'              => 'Diese Stichwörter werden in den HTML-Quelltext (Meta Keywords) eingebunden. Diese Information wird von Suchmaschinen ausgewertet. Hier können Sie passende Stichwörter zu dem Hersteller eingeben. Wenn Sie nichts eingeben, werden die Stichwörter automatisch erzeugt.',

'HELP_MANUFACTURER_SEO_DESCRIPTION'           => 'Dieser Beschreibungstext wird in den HTML-Quelltext (Meta Description) eingebunden. Dieser Text wird von vielen Suchmaschinen bei den Suchergebnissen angezeigt. Hier können Sie eine passende Beschreibung für den Hersteller eingeben. Wenn Sie nichts eingeben, wird die Beschreibung automatisch erzeugt.',
'HELP_VOUCHERSERIE_MAIN_DISCOUNT'             => 'Bei <span class="navipath_or_inputname">Rabatt</span> stellen Sie ein, wie hoch der Rabatt des Gutscheins sein soll sein soll. Mit der Auswahlliste hinter dem Eingabefeld können Sie auswählen, ob der Rabatt absolut oder prozentual sein soll:' .
                                                 '<ul>' .
                                                 '<li><span class="userinput_or_code">abs</span>: Der Rabatt ist absolut, z. B. 5 Euro.</li>' .
                                                 '<li><span class="userinput_or_code">%</span>: Der Rabatt ist prozentual, z. B. 10 Prozent vom Einkaufswert.</li>' .
                                                 '</ul>',

'HELP_VOUCHERSERIE_MAIN_ALLOWSAMESERIES'      => 'Hier können Sie einstellen, ob Benutzer mehrere Gutscheine dieser Gutscheinserie bei einer Bestellung einlösen dürfen.',

'HELP_VOUCHERSERIE_MAIN_ALLOWOTHERSERIES'     => 'Hier können Sie einstellen, ob Benutzer Gutscheine verschiedener Gutscheinserien bei einer Bestellung einlösen dürfen.',

'HELP_VOUCHERSERIE_MAIN_SAMESEROTHERORDER'    => 'Hier können Sie einstellen, ob Benutzer Gutscheine dieser Gutscheinserie bei mehreren Bestellungen einlösen dürfen.',

'HELP_VOUCHERSERIE_MAIN_RANDOMNUM'            => 'Wenn Sie diese Einstellung aktivieren, wird für jeden Gutschein eine Zufallsnummer erzeugt.',

'HELP_VOUCHERSERIE_MAIN_VOUCHERNUM'           => 'Hier können Sie eine Gutscheinnummer eingeben. Diese wird verwendet wenn, Sie neue Gutscheine anlegen. Wenn Sie mehrere Gutscheine anlegen, erhalten alle Gutscheine die gleiche Nummer.',

'HELP_VOUCHERSERIE_MAIN_CALCULATEONCE'        => 'Wenn sie produkt- oder kategoriebezogene Gutscheine benutzen, deaktivieren sie diese Option, damit der Gutschein für jedes Produkt einer Warenkorbposition berechnet wird. Aktivieren sie diese Option, wenn der Gutschein auf die gesamte Warenkorbposition angewendet werden soll.',

'HELP_WRAPPING_MAIN_PICTURE'                  => 'Bei <span class="navipath_or_inputname">Bild</span> und <span class="navipath_or_inputname">Bild hochladen</span> können Sie ein Bild für die Geschenkverpackung hochladen. Wählen Sie bei <span class="navipath_or_inputname">Bild hochladen</span> das Bild aus, das Sie hochladen möchten. Wenn Sie auf Speichern klicken, wird das Bild hochgeladen. Nachdem das Bild hochgeladen ist, wird der Dateiname des Bildes in <span class="navipath_or_inputname">Bild</span> angezeigt.',



'HELP_DYN_TRUSTED_RATINGS_ID'                 => 'Sie erhalten Ihre Trusted Shops ID für die Kundenbewertungen per E-Mail in Ihrer Auftragsbestätigung von Trusted Shops. Soweit Sie bereits Mitglied bei Trusted Shops sind, verwenden Sie bitte Ihre bekannte Trusted Shops ID. Das grüne Licht zeigt Ihnen an, dass die Trusted Shops Kundenbewertung geprüft und aktiviert wurden, nachdem Sie die Eingabe gespeichert haben.',
'HELP_DYN_TRUSTED_RATINGS_WIDGET'             => 'Aktivieren Sie diese Option, um das Bewertungs-Widget in Ihrem Shop anzuzeigen.',
'HELP_DYN_TRUSTED_RATINGS_THANKYOU'           => 'Aktivieren Sie diese Option, um den Button "Bewerten Sie uns!" auf der Bestätigungsseite "Bestellung abgeschlossen" im direkten Anschluss an eine Bestellung anzuzeigen.',
'HELP_DYN_TRUSTED_RATINGS_ORDEREMAIL'         => 'Aktivieren Sie diese Option, um den Button "Bewerten Sie uns!" in der Bestellbestätigungsmail im direkten Anschluss an eine Bestellung anzuzeigen.',
'HELP_DYN_TRUSTED_RATINGS_ORDERSENDEMAIL'     => 'Aktivieren Sie diese Option, um den Button "Bewerten Sie uns!" in der Benachrichtigung "Bestellung wurde versandt" per E-Mail anzuzeigen.',
'HELP_DYN_TRUSTED_TSID'                       => 'Trusted Shops ID des Online Shops',
'HELP_DYN_TRUSTED_USER'                       => 'Ein Benutzername (wsUser) für den Trusted Shops Webservice ist erforderlich, wenn Sie Ihren Kunden den kostenpflichtigen Käuferschutz Trusted Shops Excellence anbieten. Der Käuferschutz Classic erfordert keine Eingabe eines Benutzernamens.',
'HELP_DYN_TRUSTED_PASSWORD'                   => 'Ein Passwort (wsPasswort) für den Trusted Shops Webservice ist erforderlich, wenn Sie Ihren Kunden den kostenpflichtigen Käuferschutz Trusted Shops Excellence anbieten. Der Käuferschutz Classic erfordert keine Eingabe eines Passworts.',
'HELP_DYN_TRUSTED_TESTMODUS'                  => 'Testumgebung ("Sandbox") einschalten. Nach Abschluss der Zertifizierung sendet Ihnen Ihr Ansprechpartner bei Trusted Shops die Zugangsdaten per E-Mail.',
'HELP_DYN_TRUSTED_ACTIVE'                     => 'Aktivieren Sie diese Option, um das Trusted Shops Siegel im shop anzuzeigen.',
'HELP_DYN_TRUSTED_TSPAYMENT'                  => 'Ordnen Sie den im Shop angebotenen Zahlungsarten die entsprechende Zahlungsart bei Trusted Shop zu.',

'HELP_PROMOTIONS_BANNER_PICTUREANDLINK'       => 'Laden Sie ein Bild für den großen Startseitenbanner hoch und geben die URL für den Klick auf den Banner an. Falls ein Artikel zugeordnet wird, wird dessen URL automatisch als Banner-URL verwendet.',
'HELP_SHOP_PERF_SEO_CACHE'                    => 'Aktivierter SEO Cache verbessert die Performance, benötigt aber sehr viel Speicherplatz im /tmp-Verzeichnis.',

'HELP_ALTERNATIVE_IMAGE_SERVER_NOTE'          => 'In der Konfigurationsdatei config.inc.php kann mit den Parametern <i>sAltImageUrl</i> und <i>sSSLAltImageUrl</i> eine URL zu einem externen Bilder-Server gesetzt werden. Dadurch werden alle Artikelbilder von diesem alternativen Server geladen. Alle hochgeladenen Dateien werden jedoch lokal gespeichert, so dass sie manuell oder per Script mit dem externen Server synchronisiert werden müssen.',

'HELP_SHOP_RDFA_SUBMIT_URL'                   => 'Überträgt Ihre Shop URL zur GR-Notify-Seite. Dort wird die URL gespeichert und an Suchmaschinen und Endpunkte von Linked Open Commerce & Semantic Web weitergeleitet.',
'HELP_SHOP_RDFA_CONTENT_OFFERER'              => 'Wählen Sie hier aus, welche Content-Seite die Hauptinformationen zum eShop anzeigt, beispielsweise "Impressum".',
'HELP_SHOP_RDFA_CONTENT_PAYMENT'              => 'Wählen Sie hier aus, welche Content-Seite (beispielsweise "AGB") Zahlungsinformationen anzeigt, die RDFa nicht zugewiesen wurden. Um Ihre Zahlungsarten den RDFa-Zahlungsarten generell zuzuordnen, gehen Sie zu Shopeinstellungen -> Zahlungsarten -> RDFa.',
'HELP_SHOP_RDFA_CONTENT_DELIVERY'             => 'Wählen Sie hier aus, welche Content-Seite (beispielsweise "Versand und Kosten") Versandinformationen anzeigt, die RDFa nicht zugewiesen wurden. Um Ihre Versandarten den RDFa-Versandarten generell zuzuordnen, gehen Sie zu Shopeinstellungen -> Versandarten -> RDFa.',
'HELP_SHOP_RDFA_VAT'                          => 'Diese Option gibt an, ob die MwSt. im Preis und in den Zahlungs- und Lieferkosten enthalten ist oder nicht.',
'HELP_SHOP_RDFA_DURATION_PRICES'              => 'Geben Sie hier den Gültigkeitszeitraum für die Kosten von Artikeln, Zahlungs- und Versandarten an (z.B.: 1 Tag, 1 Woche).',
'HELP_SHOP_RDFA_LOGO_URL'                     => 'Die Webadresse (URL) eines Logos oder Bildes.',
'HELP_SHOP_RDFA_GEO_LONGITUDE'                => 'Die geografische Länge (Longitude) des Ladengeschäftes als Bestandteil der Geoposition. Bitte nur Zahlen eingeben.',
'HELP_SHOP_RDFA_GEO_LATITUDE'                 => 'Die geografische Breite (Latitude) des Ladengeschäftes als Bestandteil der Geoposition. Bitte nur Zahlen eingeben.',
'HELP_SHOP_RDFA_GLN'                          => 'Global Location Number (GLN) der Firma. Die Global Location Number ist eine 13-stellige Zahl, mit der Firmen und Firmensitz identifiziert werden.',
'HELP_SHOP_RDFA_NAICS'                        => 'Schlüssel Ihrer Firma im North American Industry Classification System (NAICS). Siehe http://www.census.gov/eos/www/naics/.',
'HELP_SHOP_RDFA_ISIC'                         => 'Schlüssel Ihrer Firma im International Standard of Industrial Classification of All Economic Activities (ISIC). Siehe http://unstats.un.org/unsd/cr/registry/isic-4.asp.',
'HELP_SHOP_RDFA_DUNS'                         => 'Die Dun & Bradstreet D-U-N-S ist ein neunstelliger Zahlenschlüssel zur Identifizierung von Unternehmen.',
'HELP_SHOP_RDFA_SHOW_PRODUCTSTOCK'            => 'Ist diese Option aktiviert, bedeutet das, dass der tatsächliche Lagerbestand angezeigt wird.',
'HELP_SHOP_RDFA_RATING_MIN'                   => 'Möglicher Minimalwert für die Bewertung im Shop. Dieser Wert ist nicht die aktuell niedrigste Bewertung eines Artikels!',
'HELP_SHOP_RDFA_RATING_MAX'                   => 'Möglicher Maximalwert für die Bewertung im Shop. Dieser Wert ist nicht die aktuell höchste Bewertung eines Artikels!',
'HELP_SHOP_RDFA_COND'                         => 'Wählen Sie aus, was den Zustand der Artikel beschreibt (neu, gebraucht oder aufgearbeitet).',
'HELP_SHOP_RDFA_FNC'                          => 'Wählen Sie hier die geschäftliche Funktion der Artikel. Werden diese beispielsweise verkauft, vermietet oder repariert?',
'HELP_SHOP_RDFA_COSTUMER'                     => 'Gibt den Kundentypen an, dem die Artikel des Shops gelten (Endverbraucher, Wiederverkäufer, Unternehmen/Gewerbe und/oder Öffentliche Einrichtungen).',
'HELP_SHOP_RDFA_DURATION_OFFERINGS'           => 'Diese Eigenschaft kennzeichnet den Gültigkeitszeitraum von Artikeln, beispielsweise 1 Tag, 1 Woche oder 1 Monat.',

'HELP_SHOP_CONFIG_ADDITIONAL_SERVICE_VAT_CALCULATION_BIGGEST_NET'  => 'Für die Berechnung wird der MwSt.-Satz derjenigen Artikel verwendet, welche den größten Nettowert im Warenkorb ausmachen.',
'HELP_SHOP_CONFIG_ADDITIONAL_SERVICE_VAT_CALCULATION_PROPORTIONAL' => 'Für die Berechnung wird der prozentuale Anteil der Artikel im Warenkorb mit gleichem MwSt.-Satz berücksichtigt.',
'HELP_SHOP_CONFIG_VIEWNETPRICE'               => 'Im Shop werden Artikelpreise als Nettopreise angezeigt.',
'HELP_SHOP_CONFIG_ENTERNETPRICE'              => 'Im Administrationsbereich müssen Artikelpreise als Nettopreise eingegeben werden.',

'HELP_REVERSE_PROXY_GET_FRONTEND'             => 'Überprüft, ob der Reverse Proxy für das Frontend verfügbar ist. Geprüft wird der Header der Startseite des Shops.',
'HELP_REVERSE_PROXY_GET_BACKEND'              => 'Der Administrationsbereich wird ohne Reverse Proxy angezeigt. Varnish Header konnte nicht empfangen werden.',

'HELP_SHOP_CONFIG_DEBIT_OLD_BANK_INFORMATION_NOT_ALLOWED' => 'Im Bestellprozess können nur IBAN und BIC angegeben werden. Die Eingabe von Kontonummer und BLZ ist nur möglich, wenn das Kontrollkästchen nicht aktiviert ist.',
);
