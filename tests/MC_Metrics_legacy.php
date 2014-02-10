<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 */


/**
 * Enter description here...
 *
 * @return null
 */
function printUsage()
{
    echo 'Usage: php ' . basename( $argv[0] ) . ' MetricsXml' . PHP_EOL;
    echo '    MetricsXml    - Metrics in PHPUnit xml format' . PHP_EOL;
    echo PHP_EOL;
    die( "" );
}

if ( !isset( $argv[1] ) || !$argv[1] ) {
    printUsage();
}

$sMetricsXml = $argv[1];

//require_once 'autoload.inc.php';

/**
 * Calculate all needed metrics.
 */
class Metrics
{
    protected $_oMetrics    = null;
    protected $_aStats      = array();
    protected $_iTotalCnn   = 0;
    protected $_iTotalCrap  = 0;
    protected $_iTotalNpath = 0;
    protected $_iLocExec    = 0;
    protected $_iMaxCnn     = 0;
    protected $_iMaxCrap    = 0;
    protected $_iMaxNpath   = 0;
    protected $_iMaxExec    = 0;

    /**
     * Enter description here...
     *
     * @param string $sFileName Enter description here...
     *
     * @return null
     */
    public function __construct( $sFileName )
    {
        $this->_oMetrics = new SimpleXMLElement($sFileName, null, true);
    }

    protected function _readFunction($sClass, $oFunction)
    {
        $sFunc  = (string) $oFunction['name'];
        $iCC   = (int) $oFunction['coverage'];  // Code Coverage
        $iCcn   = (int) $oFunction['ccn'];
        //$iCrap  = (int) $oFunction['crap'];
        $iNpath = (int) $oFunction['npath'];
        $iLoc   = (int) $oFunction['locExecutable'];
        $iCrap  = (int) getCrapIndex($iCcn, $iCC);

        // Per method
        $this->_aStats[$sClass]['method'][$sFunc]['ccn'] = $iCcn;
        $this->_aStats[$sClass]['method'][$sFunc]['crap'] =$iCrap;
        $this->_aStats[$sClass]['method'][$sFunc]['npath'] =$iNpath;
        $this->_aStats[$sClass]['method'][$sFunc]['locExecutable'] = $iLoc;
        // Sums
        $this->_aStats[$sClass]['sum']['cnn'] += (int) $iCcn * $iLoc;
        $this->_aStats[$sClass]['sum']['crap'] += (int) $iCrap * $iLoc;
        $this->_aStats[$sClass]['sum']['npath'] += (int) $iNpath * $iLoc;
        $this->_aStats[$sClass]['sum']['locExecutable'] += $iLoc;
        // Max
        $this->_aStats[$sClass]['max']['cnn'] = $this->_aStats[$sClass]['max']['cnn'] < $iCcn ? $iCcn : $this->_aStats[$sClass]['max']['cnn'];
        $this->_aStats[$sClass]['max']['crap'] = $this->_aStats[$sClass]['max']['crap'] < $iCrap ? $iCrap : $this->_aStats[$sClass]['max']['crap'];
        $this->_aStats[$sClass]['max']['npath'] = $this->_aStats[$sClass]['max']['npath'] < $iNpath ? $iNpath : $this->_aStats[$sClass]['max']['npath'];
        $this->_aStats[$sClass]['max']['locExecutable'] = $this->_aStats[$sClass]['max']['locExecutable'] < $iLoc ? $iLoc : $this->_aStats[$sClass]['max']['locExecutable'];
        // Global sums
        $this->_iTotalCnn   += (int) $iCcn * $iLoc;
        $this->_iTotalCrap  += (int) $iCrap * $iLoc;
        $this->_iTotalNpath += (int) $iNpath * $iLoc;
        $this->_iLocExec    += $iLoc;
        // Global Max
        $this->_iMaxCnn = $this->_iMaxCnn < $iCcn ? $iCcn : $this->_iMaxCnn;
        $this->_iMaxCrap = $this->_iMaxCrap < $iCrap ? $iCrap : $this->_iMaxCrap;
        $this->_iMaxNpath = $this->_iMaxNpath < $iNpath ? $iNpath : $this->_iMaxNpath;
        $this->_iMaxExec = $this->_iMaxExec < $iLoc ? $iLoc : $this->_iMaxExec;
    }

    /**
     * Enter description here...
     *
     * @return null
     */
    public function read()
    {
        $this->_iTotalCnn   = 0;
        $this->_iTotalCrap  = 0;
        $this->_iTotalNpath = 0;
        $this->_iLocExec    = 0;

        foreach ( $this->_oMetrics as $oFile ) {
            foreach ( $oFile as $oClass ) {
                $sClass = '';
                switch ($oClass->getName()) {
                    case 'class':
                        $sClass = (string) $oClass['name'];
                        foreach ( $oClass as $oFunction ) {
                            $this->_readFunction($sClass, $oFunction);
                        }
                        break;
                    case 'function':
                        $sClass = '_GLOBAL_FUNCTIONS_';
                        $this->_readFunction($sClass, $oClass);
                        break;
                }
                $iLocSum = $this->_aStats[$sClass]['sum']['locExecutable'];
                // Statistic
                if ($iLocSum) {
                    $this->_aStats[$sClass]['stat']['cnn']   = $this->_aStats[$sClass]['sum']['cnn'] / $iLocSum;
                    $this->_aStats[$sClass]['stat']['crap']  = $this->_aStats[$sClass]['sum']['crap'] / $iLocSum;
                    $this->_aStats[$sClass]['stat']['npath'] = $this->_aStats[$sClass]['sum']['npath'] / $iLocSum;
                } else {
                    $this->_aStats[$sClass]['stat']['cnn']   = '-';
                    $this->_aStats[$sClass]['stat']['crap']  = '-';
                    $this->_aStats[$sClass]['stat']['npath'] = '-';
                }
            }
        }
        // Calculate total stats
        $this->_iTotalCnn = $this->_iTotalCnn / $this->_iLocExec;
        $this->_iTotalCrap = $this->_iTotalCrap / $this->_iLocExec;
        $this->_iTotalNpath = $this->_iTotalNpath / $this->_iLocExec;
    }

    /**
     * Enter description here...
     *
     * @return null
     */
    public function echoResults()
    {
        $iLocSum = $this->_aStatsSum['locExecutable'];
        echo ("Total Avg cnn = $this->_iTotalCnn (max: $this->_iMaxCnn)" . PHP_EOL);
        echo ("Total Avg crap = $this->_iTotalCrap (max: $this->_iMaxCrap)" . PHP_EOL);
        echo ("Total Avg npath = $this->_iTotalNpath (max: $this->_iMaxNpath)" . PHP_EOL);
        echo ("Total Avg locExecutable = $iLocSum (max: $iLocSum)" . PHP_EOL . PHP_EOL);
        foreach ($this->_aStats as $sClass => $aClass) {
            $iCcn      = $aClass['stat']['cnn'];
            $iCrap     = $aClass['stat']['crap'];
            $iNpath    = $aClass['stat']['npath'];
            $iCcnMax   = $aClass['max']['cnn'];
            $iCrapMax  = $aClass['max']['crap'];
            $iNpathMax = $aClass['max']['npath'];
            $iLoc      = $aClass['sum']['locExecutable'];
            echo ("Total for $sClass" . PHP_EOL);
            echo ("\tAvg cnn = $iCcn (max: $iCcnMax)" . PHP_EOL);
            echo ("\tAvg crap = $iCrap (max: $iCrapMax)" . PHP_EOL);
            echo ("\tAvg npath = $iNpath (max: $iNpathMax)" . PHP_EOL);
            echo ("\tAvg locExecutable = $iLoc" . PHP_EOL . PHP_EOL);
        }
    }
}

try {

    $oMetrics = new Metrics($sMetricsXml);
    $oMetrics->read();
    $oMetrics->echoResults();

} catch (Exception $oE) {
    $sMsg = $oE->getMessage() . ' ' . $oE->getTraceAsString();
    echo ($sMsg);
}

/**
 * Based off of http://www.artima.com/weblogs/viewpost.jsp?thread=210575
 * comp(m) = cyclomatic complexity
 * cov(m) = code coverage
 *
 * C.R.A.P. Level is based off of the file and not the method.
 *
 * @param int $cyclomatic_complexity Enter description here...
 * @param int $code_coverage         Enter description here...
 *
 * @return unknown_type
 */
function getCrapIndex($cyclomatic_complexity, $code_coverage)
{

    if ($code_coverage == 0) {
        // comp(m)^2 + comp(m)
        if (function_exists('gmp_pow')) {
            return gmp_pow($cyclomatic_complexity, 2) + $cyclomatic_complexity;
        } else if (function_exists('bcpow')) {
            return bcpow($cyclomatic_complexity, 2) + $cyclomatic_complexity;
        } else {
            return pow($cyclomatic_complexity, 2) + $cyclomatic_complexity;
        }
    } else if ($code_coverage >= 95) {
        // comp(m)
        return $cyclomatic_complexity;
    } else {
        // comp(m)^2 * (1 - cov(m)/100)^3 + comp(m)
        if (function_exists('gmp_pow')) {
            return gmp_mul( gmp_pow($cyclomatic_complexity, 2), ( gmp_pow( ( gmp_sub(1, $code_coverage/100) ), 3) + $cyclomatic_complexity ) );
        } else if (function_exists('bcpow')) {
            return bcmul( bcpow($cyclomatic_complexity, 2), ( bcpow( ( bcsub(1, $code_coverage/100) ), 3) + $cyclomatic_complexity ) );
        } else {
            return pow($cyclomatic_complexity, 2) * (pow(1-$code_coverage/100, 3) + $cyclomatic_complexity);
        }
    }
}
