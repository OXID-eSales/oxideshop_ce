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

$myConfig = oxRegistry::getConfig();

$sTcPdfPath = $myConfig->getConfigParam( 'sCoreDir' ) . "tcpdf/";
$sTcPdfUrl  = $myConfig->getConfigParam( 'sShopURL') . "/" . $myConfig->getConfigParam( 'sCoreDir' ) . "tcpdf/";

/**
 * Using own config params
 */
define ('K_TCPDF_EXTERNAL_CONFIG', 1 );

/**
 * Installation path (/var/www/tcpdf/).
 * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
 */
define ('K_PATH_MAIN', $sTcPdfPath );

/**
 * URL path to tcpdf installation folder (http://localhost/tcpdf/).
 * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
 */
define ('K_PATH_URL', $sTcPdfUrl );

/**
 * path for PDF fonts
 * use K_PATH_MAIN.'fonts/old/' for old non-UTF8 fonts
 */
define ('K_PATH_FONTS', K_PATH_MAIN.'fonts/');

/**
 * cache directory for temporary files (full path)
 */
define ('K_PATH_CACHE', K_PATH_MAIN.'cache/');

/**
 * cache directory for temporary files (url path)
 */
define ('K_PATH_URL_CACHE', K_PATH_URL.'cache/');

/**
 *images directory
 */
define ('K_PATH_IMAGES', K_PATH_MAIN.'images/');

/**
 * blank image
 */
define ('K_BLANK_IMAGE', K_PATH_IMAGES.'_blank.png');

/**
 * page format
 */
define ('PDF_PAGE_FORMAT', 'A4');

/**
 * page orientation (P=portrait, L=landscape)
 */
define ('PDF_PAGE_ORIENTATION', 'P');

/**
 * document creator
 */
define ('PDF_CREATOR', 'TCPDF');

/**
 * document author
 */
define ('PDF_AUTHOR', 'TCPDF');

/**
 * header title
 */
define ('PDF_HEADER_TITLE', 'TCPDF Example');

/**
 * header description string
 */
define ('PDF_HEADER_STRING', "by Nicola Asuni - Tecnick.com\nwww.tcpdf.org");

/**
 * image logo
 */
define ('PDF_HEADER_LOGO', 'tcpdf_logo.jpg');

/**
 * header logo image width [mm]
 */
define ('PDF_HEADER_LOGO_WIDTH', 30);

/**
 *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
 */
define ('PDF_UNIT', 'mm');

/**
 * header margin
 */
define ('PDF_MARGIN_HEADER', 5);

/**
 * footer margin
 */
define ('PDF_MARGIN_FOOTER', 10);

/**
 * top margin
 */
define ('PDF_MARGIN_TOP', 27);

/**
 * bottom margin
 */
define ('PDF_MARGIN_BOTTOM', 25);

/**
 * left margin
 */
define ('PDF_MARGIN_LEFT', 15);

/**
 * right margin
 */
define ('PDF_MARGIN_RIGHT', 15);

/**
 * default main font name
 */
define ('PDF_FONT_NAME_MAIN', 'helvetica');

/**
 * default main font size
 */
define ('PDF_FONT_SIZE_MAIN', 10);

/**
 * default data font name
 */
define ('PDF_FONT_NAME_DATA', 'helvetica');

/**
 * default data font size
 */
define ('PDF_FONT_SIZE_DATA', 8);

/**
 * default monospaced font name
 */
define ('PDF_FONT_MONOSPACED', 'courier');

/**
 * ratio used to adjust the conversion of pixels to user units
 */
define ('PDF_IMAGE_SCALE_RATIO', 1);

/**
 * magnification factor for titles
 */
define('HEAD_MAGNIFICATION', 1.1);

/**
 * height of cell repect font height
 */
define('K_CELL_HEIGHT_RATIO', 1.25);

/**
 * title magnification respect main font size
 */
define('K_TITLE_MAGNIFICATION', 1.3);

/**
 * reduction factor for small font
 */
define('K_SMALL_RATIO', 2/3);

/**
 * Including language file
 */
require_once $sTcPdfPath . "config/lang/eng.php";

/**
 * Including parent class
 */
require_once $sTcPdfPath . "tcpdf.php";

/**
 * TCPDF class wrapper, set/overrides oxid specific functionality
 * @package core
 */
class oxPDF extends TCPDF
{
    /**
     * This is the class constructor.
     * It allows to set up the page format, the orientation and
     * the measure unit used in all the methods (except for the font sizes).
     *
     * @param string  $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li></ul>
     * @param string  $unit        User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
     * @param mixed   $format      The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
     * @param boolean $unicode     TRUE means that the input text is unicode (default = true)
     * @param string  $encoding    charset encoding; default is UTF-8
     * @param boolean $diskcache   if TRUE reduce the RAM memory usage by caching temporary data on filesystem (slower).
     *
     * @access public
     * @since 1.0
     */
    public function __construct( $orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false )
    {
        $myConfig = oxRegistry::getConfig();
        $unicode  = $myConfig->isUtf();
        $encoding = $unicode ? 'UTF-8' : oxRegistry::getLang()->translateString( "charset" );
        //#1161: Thin line and unknown characters on every pdf page
        //we use myorder::pdfFooter()
        $this->setPrintFooter(false);

        parent::__construct( $orientation, $unit, $format, $unicode, $encoding, $diskcache );
    }

    /**
     * Prints a cell (rectangular area) with optional borders, background color and html text string.
     * The upper-left corner of the cell corresponds to the current position. After the call, the current position moves to the right or to the next line.<br />
     * If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
     *
     * @param string  $html   html text to print. Default value: empty string.
     * @param int     $ln     Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL language)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul> Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
     * @param int     $fill   Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
     * @param boolean $reseth if true reset the last cell height (default true).
     * @param float   $cell   if true, the cell extends up to the margin.
     * @param string  $align  allows to center or align the text. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
     *
     * @access public
     * @return null
     * @uses MultiCell()
     * @see Multicell(), writeHTML()
     */
    public function WriteHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
    {
        //HTML parser
        $html = str_replace( "\n", ' ', $html );
        $a    = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
        foreach ($a as $i=>$e) {
            if ($i%2==0) {
                //Text
                if ($this->HREF) {
                    $this->PutLink( $this->HREF, $e );
                } else {
                    $this->Write( 5, $e );
                }
            } else {
                //Tag
                if ($e{0}=='/') {
                    $this->CloseTag(strtoupper(substr($e, 1)));
                } else {
                    //Extract attributes
                    $a2=explode(' ', $e);
                    $tag=strtoupper(array_shift($a2));
                    $attr=array();
                    foreach ($a2 as $v) {
                        if (preg_match('/^([^=]*)=["\']?([^"\']*)["\']?$/', $v, $a3)) {
                            $attr[strtoupper($a3[1])]=$a3[2];
                        }
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
    }

    /**
     * Opening tag
     *
     * @param object $tag  tag name
     * @param object $attr attributes
     *
     * @return null
     */
    public function OpenTag($tag,$attr)
    {
        if ( $tag=='B' or $tag=='I' or $tag=='U' ) {
            $this->SetStyle($tag, true);
        }

        if ( $tag=='A' ) {
            $this->HREF = (is_array($attr) && isset($attr['HREF'])) ? $attr['HREF'] : '';
        }

        if ( $tag=='BR' ) {
            $this->Ln(5);
        }
    }

    /**
     * Closing tag
     *
     * @param object $tag tag name
     *
     * @return null
     */
    public function CloseTag($tag)
    {
        if ( $tag=='B' or $tag=='I' or $tag=='U' ) {
            $this->SetStyle($tag, false);
        }

        if ( $tag=='A' ) {
            $this->HREF = '';
        }
    }

    /**
     * Modify style and select corresponding font
     *
     * @param object $tag    tag name
     * @param object $enable enable style
     *
     * @return null
     */
    public function SetStyle($tag,$enable)
    {
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s>0) {
                $style.=$s;
            }
        }
        $this->SetFont('', $style);
    }

    /**
     * Put a hyperlink
     *
     * @param object $sURL  link url
     * @param object $sText link text
     *
     * @return null
     */
    public function PutLink($sURL, $sText)
    {
        $this->SetTextColor( 0, 0, 255 );
        $this->SetStyle( 'U', true );
        $this->Write( 5, $sText, $sURL );
        $this->SetStyle( 'U', false );
        $this->SetTextColor( 0 );
    }

    /**
     * Sets font for current text line
     *
     * NOTICE: In case you have problems with fonts, you must override this function and set different font
     *
     * @param string $family   font family
     * @param string $style    font style [optional]
     * @param string $size     font size [optional]
     * @param string $fontfile font file[optional]
     *
     * @return null
     */
    public function SetFont($family, $style='', $size=0, $fontfile='')
    {
        if ( $family == 'Arial' ) {
            // overriding standard ..
            $family = oxRegistry::getConfig()->isUtf() ? 'freesans' : '';
        }

        parent::SetFont($family, $style, $size, $fontfile);
    }

}
