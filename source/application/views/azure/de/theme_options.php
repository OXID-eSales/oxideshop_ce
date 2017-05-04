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

$aLang = array(
'charset'                                       => 'ISO-8859-15',

'SHOP_THEME_GROUP_images'                       => 'Bilder',
'SHOP_THEME_GROUP_features'                     => 'Funktionen',
'SHOP_THEME_GROUP_display'                      => 'Anzeige',

'SHOP_THEME_sIconsize'                          => 'Größe des Artikel-Icons in Pixeln (Breite*Höhe)',//SHOP_CONFIG_ICONSIZE
'HELP_SHOP_THEME_sIconsize'                     => 'Artikel-Icons sind die kleinsten Bilder eines Artikels. Sie werden z. B. <br>' .
                                                 '<ul><li>im Warenkorb angezeigt</li>' .
                                                 '<li>angezeigt, wenn Artikel in der Seitenleiste aufgelistet werden (z.B. bei den Aktionen <span class="filename_filepath_or_italic">Top of the Shop</span> und <span class="filename_filepath_or_italic">Schnäppchen</span>).</li></ul>' .
                                                 'Damit die Harmonie des eShops nicht durch zu große Icons gestört wird, werden zu große Icons automatisch verkleinert. Die maximale Größe können Sie hier eingeben.<br>' ,

'SHOP_THEME_sThumbnailsize'                     => 'Größe des Artikel-Thumbnails in Pixeln (Breite*Höhe)',//SHOP_CONFIG_THUMBNAILSIZE
'HELP_SHOP_THEME_sThumbnailsize'                => 'Artikel-Thumbnails sind kleine Bilder eines Artikels. Sie werden z. B. <br>' .
                                                 '<ul><li>in Artikellisten angezeigt. Artikellisten sind z. B. Kategorieansichten (alle Artikel in einer Kategorie werden aufgelistet) und die Suchergebnisse.</li>' .
                                                 '<li>in Aktionen angezeigt, die in der Mitte der Startseite angezeigt werden, z. B. <span class="filename_filepath_or_italic">Die Dauerbrenner</span> und <span class="filename_filepath_or_italic">Frisch eingetroffen!</span>.</li></ul>' .
                                                 'Damit das Design des eShops nicht durch zu große Thumbnails gestört wird, werden zu große Thumbnails automatisch verkleinert. Die maximale Größe können Sie hier eingeben.',

'SHOP_THEME_sZoomImageSize'                     => 'Größe der Artikel-Zoom-Bilder (Zoom 1-4) in Pixeln (Breite*Höhe)',//SHOP_CONFIG_ZOOMIMAGESIZE
'SHOP_THEME_sCatThumbnailsize'                  => 'Größe des Kategorie-Thumbnails in Pixeln (Breite*Höhe)',//SHOP_CONFIG_CATEGORYTHUMBNAILSIZE
'HELP_SHOP_THEME_sCatThumbnailsize'             => 'In der Kategorieübersicht wird das Bild der ausgewählten Kategorie in der hier definierten Größe angezeigt.',//SHOP_CONFIG_CATEGORYTHUMBNAILSIZE
'SHOP_THEME_aDetailImageSizes'                  => 'Größe der Artikelbilder  (Bild 1-12) in Pixeln (Breite*Höhe)',//SHOP_CONFIG_DETAILIMAGESIZE

'SHOP_THEME_sManufacturerIconsize'              => 'Größe des Hersteller-/Markenlogos in Pixeln (Breite*Höhe)', // Check if this is really manufacturer or if it is more like "brand"
'HELP_SHOP_THEME_sManufacturerIconsize'         => 'Dieses Logo wird auf der Startseite in der Markenlogo-übersicht angezeigt.',

'SHOP_THEME_sCatIconsize'                       => 'Größe des Kategorie-Icons einer Unterkategorie in Pixeln (Breite*Höhe)',
'HELP_SHOP_THEME_sCatIconsize'                  => 'In der Kategorieübersicht werden die Kategoriebilder von Unterkategorien in der hier definierten Größe angezeigt.',

'SHOP_THEME_sCatPromotionsize'                  => 'Größe des Kategoriebildes für die Startseite in Pixeln (Breite*Höhe)',
'HELP_SHOP_THEME_sCatPromotionsize'             => 'Kategorien, die auf der Startseite beworben werden, benötigen eine eigens dafür vorgesehene Größgenangabe. Stellen Sie diese hier ein.',

'SHOP_THEME_bl_showOpenId'                        => 'Open ID Login aktivieren',//SHOP_CONFIG_SHOWOPENID
'SHOP_THEME_bl_showGiftWrapping'                => 'Geschenkverpackungen aktivieren',//SHOP_CONFIG_SHOWGIFTWRAPPING
'SHOP_THEME_bl_showVouchers'                    => 'Gutscheine aktivieren',//SHOP_CONFIG_SHOWVOUCHERS
'SHOP_THEME_bl_showWishlist'                    => 'Wunschzettel aktivieren',//SHOP_CONFIG_SHOWWISHLIST
'SHOP_THEME_bl_showCompareList'                 => 'Artikelvergleich aktivieren',//SHOP_CONFIG_SHOWCOMPARELIST
'SHOP_THEME_bl_showListmania'                   => 'Lieblingslisten aktivieren',//SHOP_CONFIG_SHOWLISTMANIA
'SHOP_THEME_blShowBirthdayFields'               => 'Eingabefeld für das Geburtsdatum anzeigen, wenn Benutzer ihre Daten eingeben',//SHOP_CONFIG_SHOWBIRTHDAYFIELDS

'SHOP_THEME_iTopNaviCatCount'                   => 'Anzahl der Kategorien, die oben angezeigt werden (weitere Kategorien werden ebenfalls oben unter "mehr" aufgelistet)',//SHOP_CONFIG_TOPNAVICATCOUNT
'SHOP_THEME_iNewBasketItemMessage'              => 'Wenn Produkt in den Warenkorb gelegt wird, folgende Aktion ausführen',//SHOP_SYSTEM_SHOWNEWBASKETITEMMESSAGE
'HELP_SHOP_THEME_iNewBasketItemMessage'         => 'Wenn Konsumenten ein Produkt in den Warenkorb legen, kann der OXID eShop unterschiedliche Feedback-Aktionen durchführen.',//SHOP_SYSTEM_SHOWNEWBASKETITEMMESSAGE
'SHOP_THEME_iNewBasketItemMessage_0'            => 'Keine',
'SHOP_THEME_iNewBasketItemMessage_1'            => 'Meldung ausgeben',
'SHOP_THEME_iNewBasketItemMessage_2'            => 'Popup öffnen',
'SHOP_THEME_iNewBasketItemMessage_3'            => 'Warenkorb öffnen',

'SHOP_THEME_blShowListDisplayType'              => 'Produktlistentyp in Produktlisten anzeigen',
'HELP_SHOP_THEME_blShowListDisplayType'         => 'Darf der Besucher Ihres Online-Shops die Art der Listenansicht auswählen? Falls diese Option nicht aktiviert ist, werden die Listenansichten so angezeigt wie in der Dropbox "Standard für Produktlistentyp" eingestellt.',
'SHOP_THEME_sDefaultListDisplayType'            => 'Standard für Produktlistentyp',
'SHOP_THEME_sDefaultListDisplayType_grid'       => 'Galerie',
'SHOP_THEME_sDefaultListDisplayType_line'       => 'Liste',
'SHOP_THEME_sDefaultListDisplayType_infogrid'   => 'Galerie zweispaltig',
'SHOP_THEME_sStartPageListDisplayType'          => 'Produktlistentyp auf der Startseite',
'SHOP_THEME_sStartPageListDisplayType_grid'     => 'Galerie',
'SHOP_THEME_sStartPageListDisplayType_line'     => 'Liste',
'SHOP_THEME_sStartPageListDisplayType_infogrid' => 'Galerie zweispaltig',


'SHOP_THEME_aNrofCatArticlesInGrid'             => 'Für Galerie: Anzahl der Artikel, die in einer Artikelliste pro Seite angezeigt werden können<br><br>Warnung: Eine große Anzahl von Artikeln pro Seite (über 100) kann die Geschwindigkeit des Shops erheblich beeinflussen!',
'SHOP_THEME_aNrofCatArticles'                   => 'Anzahl der Artikel, die in einer Artikelliste pro Seite angezeigt werden können<br><br>Warnung: Eine große Anzahl von Artikeln pro Seite (über 100) kann die Geschwindigkeit des Shops erheblich beeinflussen!',
);
