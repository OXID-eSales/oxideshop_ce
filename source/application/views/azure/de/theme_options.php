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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

$aLang = array(
'charset'                                       => 'ISO-8859-15',

'SHOP_THEME_GROUP_images'                       => 'Bilder',
'SHOP_THEME_GROUP_features'                     => 'Funktionen',
'SHOP_THEME_GROUP_display'                      => 'Anzeige',

'SHOP_THEME_sIconsize'                          => 'Gr��e des Artikel-Icons in Pixeln (Breite*H�he)',//SHOP_CONFIG_ICONSIZE
'HELP_SHOP_THEME_sIconsize'                     => 'Artikel-Icons sind die kleinsten Bilder eines Artikels. Sie werden z. B. <br>' .
                                                 '<ul><li>im Warenkorb angezeigt</li>' .
                                                 '<li>angezeigt, wenn Artikel in der Seitenleiste aufgelistet werden (z.B. bei den Aktionen <span class="filename_filepath_or_italic">Top of the Shop</span> und <span class="filename_filepath_or_italic">Schn�ppchen</span>).</li></ul>' .
                                                 'Damit die Harmonie des eShops nicht durch zu gro�e Icons gest�rt wird, werden zu gro�e Icons automatisch verkleinert. Die maximale Gr��e k�nnen Sie hier eingeben.<br>' ,

'SHOP_THEME_sThumbnailsize'                     => 'Gr��e des Artikel-Thumbnails in Pixeln (Breite*H�he)',//SHOP_CONFIG_THUMBNAILSIZE
'HELP_SHOP_THEME_sThumbnailsize'                => 'Artikel-Thumbnails sind kleine Bilder eines Artikels. Sie werden z. B. <br>' .
                                                 '<ul><li>in Artikellisten angezeigt. Artikellisten sind z. B. Kategorieansichten (alle Artikel in einer Kategorie werden aufgelistet) und die Suchergebnisse.</li>' .
                                                 '<li>in Aktionen angezeigt, die in der Mitte der Startseite angezeigt werden, z. B. <span class="filename_filepath_or_italic">Die Dauerbrenner</span> und <span class="filename_filepath_or_italic">Frisch eingetroffen!</span>.</li></ul>' .
                                                 'Damit das Design des eShops nicht durch zu gro�e Thumbnails gest�rt wird, werden zu gro�e Thumbnails automatisch verkleinert. Die maximale Gr��e k�nnen Sie hier eingeben.',

'SHOP_THEME_sZoomImageSize'                     => 'Gr��e der Artikel-Zoom-Bilder (Zoom 1-4) in Pixeln (Breite*H�he)',//SHOP_CONFIG_ZOOMIMAGESIZE
'SHOP_THEME_sCatThumbnailsize'                  => 'Gr��e des Kategorie-Thumbnails in Pixeln (Breite*H�he)',//SHOP_CONFIG_CATEGORYTHUMBNAILSIZE
'HELP_SHOP_THEME_sCatThumbnailsize'             => 'In der Kategorie�bersicht wird das Bild der ausgew�hlten Kategorie in der hier definierten Gr��e angezeigt.',//SHOP_CONFIG_CATEGORYTHUMBNAILSIZE
'SHOP_THEME_aDetailImageSizes'                  => 'Gr��e der Artikelbilder  (Bild 1-12) in Pixeln (Breite*H�he)',//SHOP_CONFIG_DETAILIMAGESIZE

'SHOP_THEME_sManufacturerIconsize'              => 'Gr��e des Hersteller-/Markenlogos in Pixeln (Breite*H�he)', // Check if this is really manufacturer or if it is more like "brand"
'HELP_SHOP_THEME_sManufacturerIconsize'         => 'Dieses Logo wird auf der Startseite in der Markenlogo-�bersicht angezeigt.',

'SHOP_THEME_sCatIconsize'                       => 'Gr��e des Kategorie-Icons einer Unterkategorie in Pixeln (Breite*H�he)',
'HELP_SHOP_THEME_sCatIconsize'                  => 'In der Kategorie�bersicht werden die Kategoriebilder von Unterkategorien in der hier definierten Gr��e angezeigt.',

'SHOP_THEME_sCatPromotionsize'                  => 'Gr��e des Kategoriebildes f�r die Startseite in Pixeln (Breite*H�he)',
'HELP_SHOP_THEME_sCatPromotionsize'             => 'Kategorien, die auf der Startseite beworben werden, ben�tigen eine eigens daf�r vorgesehene Gr��genangabe. Stellen Sie diese hier ein.',

'SHOP_THEME_bl_showOpenId'                        => 'Open ID Login aktivieren',//SHOP_CONFIG_SHOWOPENID
'SHOP_THEME_bl_showGiftWrapping'                => 'Geschenkverpackungen aktivieren',//SHOP_CONFIG_SHOWGIFTWRAPPING
'SHOP_THEME_bl_showVouchers'                    => 'Gutscheine aktivieren',//SHOP_CONFIG_SHOWVOUCHERS
'SHOP_THEME_bl_showWishlist'                    => 'Wunschzettel aktivieren',//SHOP_CONFIG_SHOWWISHLIST
'SHOP_THEME_bl_showCompareList'                 => 'Artikelvergleich aktivieren',//SHOP_CONFIG_SHOWCOMPARELIST
'SHOP_THEME_bl_showListmania'                   => 'Lieblingslisten aktivieren',//SHOP_CONFIG_SHOWLISTMANIA
'SHOP_THEME_blShowBirthdayFields'               => 'Eingabefeld f�r das Geburtsdatum anzeigen, wenn Benutzer ihre Daten eingeben',//SHOP_CONFIG_SHOWBIRTHDAYFIELDS

'SHOP_THEME_iTopNaviCatCount'                   => 'Anzahl der Kategorien, die oben angezeigt werden (weitere Kategorien werden ebenfalls oben unter "mehr" aufgelistet)',//SHOP_CONFIG_TOPNAVICATCOUNT
'SHOP_THEME_iNewBasketItemMessage'              => 'Wenn Produkt in den Warenkorb gelegt wird, folgende Aktion ausf�hren',//SHOP_SYSTEM_SHOWNEWBASKETITEMMESSAGE
'HELP_SHOP_THEME_iNewBasketItemMessage'         => 'Wenn Konsumenten ein Produkt in den Warenkorb legen, kann der OXID eShop unterschiedliche Feedback-Aktionen durchf�hren.',//SHOP_SYSTEM_SHOWNEWBASKETITEMMESSAGE
'SHOP_THEME_iNewBasketItemMessage_0'            => 'Keine',
'SHOP_THEME_iNewBasketItemMessage_1'            => 'Meldung ausgeben',
'SHOP_THEME_iNewBasketItemMessage_2'            => 'Popup �ffnen',
'SHOP_THEME_iNewBasketItemMessage_3'            => 'Warenkorb �ffnen',

'SHOP_THEME_blShowListDisplayType'              => 'Produktlistentyp in Produktlisten anzeigen',
'HELP_SHOP_THEME_blShowListDisplayType'         => 'Darf der Besucher Ihres Online-Shops die Art der Listenansicht ausw�hlen? Falls diese Option nicht aktiviert ist, werden die Listenansichten so angezeigt wie in der Dropbox "Standard f�r Produktlistentyp" eingestellt.',
'SHOP_THEME_sDefaultListDisplayType'            => 'Standard f�r Produktlistentyp',
'SHOP_THEME_sDefaultListDisplayType_grid'       => 'Galerie',
'SHOP_THEME_sDefaultListDisplayType_line'       => 'Liste',
'SHOP_THEME_sDefaultListDisplayType_infogrid'   => 'Galerie zweispaltig',
'SHOP_THEME_sStartPageListDisplayType'          => 'Produktlistentyp auf der Startseite',
'SHOP_THEME_sStartPageListDisplayType_grid'     => 'Galerie',
'SHOP_THEME_sStartPageListDisplayType_line'     => 'Liste',
'SHOP_THEME_sStartPageListDisplayType_infogrid' => 'Galerie zweispaltig',


'SHOP_THEME_aNrofCatArticlesInGrid'             => 'F�r Galerie: Anzahl der Artikel, die in einer Artikelliste pro Seite angezeigt werden k�nnen<br><br>Warnung: Eine gro�e Anzahl von Artikeln pro Seite (�ber 100) kann die Geschwindigkeit des Shops erheblich beeinflussen!',
'SHOP_THEME_aNrofCatArticles'                   => 'Anzahl der Artikel, die in einer Artikelliste pro Seite angezeigt werden k�nnen<br><br>Warnung: Eine gro�e Anzahl von Artikeln pro Seite (�ber 100) kann die Geschwindigkeit des Shops erheblich beeinflussen!',
);
