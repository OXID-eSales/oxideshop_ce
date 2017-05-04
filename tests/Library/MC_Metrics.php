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
 * @copyright (C) OXID eSales AG 2003-2017
 */

ini_set( "display_errors", false );

/**
 * Calculate all needed metrics.
 */
class Metrics
{
    /**
     * Variable store loaded xml file
     * @var null|SimpleXMLElement
     */
    protected $_oMetrics = null;

    /**
     * To store stats for classes
     * @var array
     */
    protected $_aStats = array();
    /**
     * Variable to store total of CCN
     * @var int
     */
    protected $_iTotalCnn = 0;

    /**
     * Variable to store total value of Crap index
     * @var int
     */
    protected $_iTotalCrapIndex = 0;

    /**
     * Variable to store total value of NPath
     * @var int
     */
    protected $_iTotalNPath = 0;

    /**
     * To store total number of logical lines of code count
     * @var int
     */
    protected $_iTotalLLOC = 0;

    /**
     * To store max number of CCN
     * @var int
     */
    protected $_iMaxCCN = 0;

    /**
     * To store max value of Crap index
     * @var int
     */
    protected $_iMaxCrapIndexIndex = 0;

    /**
     * To store max value of NPath
     * @var int
     */
    protected $_iMaxNPath = 0;

    /**
     * To store number of max lines of logical code
     * @var int
     */
    protected $_iMaxLLOC = 0;

    /**
     * To store check value then files exist or not
     * @var bool
     */
    protected $_blFileExist = false;

    /**
     * On creation load generated metrics file
     *
     * @param string $sFileName metrics file name
     */
    public function __construct($sFileName)
    {
        if ( file_exists( $sFileName ) ) {
            $this->_oMetrics    = new SimpleXMLElement($sFileName, null, true);
            $this->_blFileExist = true;
        }
    }

    /**
     * To start generated metrics xml file analysis for CCN, Crap index and NPath
     *
     * @return null
     */
    public function read()
    {
        if ( $this->isExistingMetricsFile() ) {
            $this->_resetTotalValues();

            foreach ( $this->_oMetrics->package as $oPackage ) {
                $this->_readClassMetricsPerPackage( $oPackage );
            }
        }
    }

    /**
     * To read metrics existing per package class
     *
     * @param object $oPackage package object
     */
    protected function _readClassMetricsPerPackage($oPackage)
    {
        foreach ( $oPackage as $oClass ) {
            $this->_readMetricsForClass( $oClass );
        }
    }

    /**
     * To make analysis for class
     *
     * @param object $oClass class xml object
     */
    protected function _readMetricsForClass($oClass)
    {
        $sClass = (string)$oClass['name'];

        foreach ( $oClass as $oFunction ) {
            $this->_readFunctionMetrics( $sClass, $oFunction );
        }

        $iLocSum = $this->_aStats[$sClass]['sum']['locExecutable'];

        // Statistics
        if ( $iLocSum ) {
            $this->_aStats[$sClass]['stat']['cnn']   = $this->_aStats[$sClass]['sum']['cnn'] / $iLocSum;
            $this->_aStats[$sClass]['stat']['crap']  = $this->_aStats[$sClass]['sum']['crap'] / $iLocSum;
            $this->_aStats[$sClass]['stat']['npath'] = $this->_aStats[$sClass]['sum']['npath'] / $iLocSum;
        }
    }

    /**
     * To make metrics analysis for class
     *
     * @param string $sClass class name
     * @param object $oFunction class method metrics
     */
    protected function _readFunctionMetrics($sClass, $oFunction)
    {
        $iCcn   = (int)$oFunction['ccn'];
        $iCrap  = (int)$oFunction['ccn2'];
        $iNPath = (int)$oFunction['npath'];
        $iLoc   = (int)$oFunction['lloc'];

        // Sums
        $this->appendClassTotalCNN( $sClass, (int)$iCcn * $iLoc );
        $this->appendClassTotalCrapIndex( $sClass, (int)$iCrap * $iLoc );
        $this->appendClassTotalNPath( $sClass, (int)$iNPath * $iLoc );
        $this->appendClassTotalLLOC( $sClass, $iLoc );

        // Max
        $this->updateClassMaxCCN( $sClass, $iCcn );
        $this->updateClassMaxCrapIndex( $sClass, $iCrap );
        $this->updateClassMaxNPath( $sClass, $iNPath );
        $this->updateClassMaxLLOC( $sClass, $iLoc );

        $this->setGlobalValues( $iCcn, $iCrap, $iNPath, $iLoc );
    }

    /**
     * To reset total values
     *
     * @return null
     */
    protected function _resetTotalValues()
    {
        $this->_iTotalCnn   = 0;
        $this->_iTotalCrapIndex  = 0;
        $this->_iTotalNPath = 0;
        $this->_iTotalLLOC  = 0;
        $this->_aStats      = array();
    }

    /**
     * To check metrics file exist or not
     *
     * @return bool on success returns true
     */
    public function isExistingMetricsFile()
    {
        return $this->_blFileExist;
    }

    /**
     * To append total CCN for needed class
     *
     * @param string $sClassName class name
     * @param int $iCNN new ccn value which needs to add
     */
    public function appendClassTotalCNN($sClassName, $iCNN)
    {
        if ( !isset($this->_aStats[$sClassName]['sum']['cnn']) ) {
            $this->_aStats[$sClassName]['sum']['cnn'] = 0;
        }

        $this->_aStats[$sClassName]['sum']['cnn'] += $iCNN;
    }

    /**
     * To append to total ccn
     *
     * @param int $iCCN new ccn value which needs to add
     */
    public function appendTotalCCN($iCCN)
    {
        $this->_iTotalCnn += $iCCN;
    }

    /**
     * To get global ccn
     *
     * @return int
     */
    public function getTotalCCN()
    {
        return $this->_iTotalCnn;
    }

    /**
     * To append total crap index for class
     *
     * @param string $sClassName class name
     * @param string $iCrapIndex Crap index
     */
    public function appendClassTotalCrapIndex($sClassName, $iCrapIndex)
    {
        if ( !isset($this->_aStats[$sClassName]['sum']['crap']) ) {
            $this->_aStats[$sClassName]['sum']['crap'] = 0;
        }
        $this->_aStats[$sClassName]['sum']['crap'] += $iCrapIndex;
    }

    /**
     * To append new value to total Crap index
     *
     * @param int $iCrapIndex new Crap index value
     */
    public function appendToTotalCrapIndex($iCrapIndex)
    {
        $this->_iTotalCrapIndex += $iCrapIndex;
    }

    /**
     * To get total value of Crap index
     *
     * @return int
     */
    public function getTotalCrapIndex()
    {
        return $this->_iTotalCrapIndex;
    }

    /**
     * To append new value to class total NPath
     *
     * @param string $sClassName class name
     * @param int $iNPath new value of NPath
     */
    public function appendClassTotalNPath($sClassName, $iNPath)
    {
        if ( !isset($this->_aStats[$sClassName]['sum']['npath']) ) {
            $this->_aStats[$sClassName]['sum']['npath'] = 0;
        }

        $this->_aStats[$sClassName]['sum']['npath'] += $iNPath;
    }

    /**
     * To append new value to total NPath
     *
     * @param int $iNPath new value of NPath
     */
    public function appendToTotalNPath($iNPath)
    {
        $this->_iTotalNPath += $iNPath;
    }

    /**
     * To get total of NPath
     *
     * @return int
     */
    public function getTotalNPath()
    {
        return $this->_iTotalNPath;
    }

    /**
     * To append class total logical lines of code
     *
     * @param string $sClassName name of class
     * @param int $iTotalLLOC number of logical code lines
     */
    public function appendClassTotalLLOC($sClassName, $iTotalLLOC)
    {
        if ( !isset($this->_aStats[$sClassName]['sum']['locExecutable']) ) {
            $this->_aStats[$sClassName]['sum']['locExecutable'] = 0;
        }

        $this->_aStats[$sClassName]['sum']['locExecutable'] += $iTotalLLOC;
    }

    /**
     * To append total logical lines of code
     *
     * @param int $iTotalLLOC number of logical code lines
     */
    public function appendToTotalLLOC($iTotalLLOC)
    {
        $this->_iTotalLLOC += $iTotalLLOC;
    }

    /**
     * To get total of logical lines of code
     *
     * @return int
     */
    public function getTotalLLOC()
    {
        return $this->_iTotalLLOC;
    }

    /**
     * To update class value of CCN if new one is bigger then older one
     *
     * @param string $sClassName class name
     * @param int $iCCN new value of CCN
     */
    public function updateClassMaxCCN($sClassName, $iCCN)
    {
        if ( !isset($this->_aStats[$sClassName]['max']['cnn']) || $this->_aStats[$sClassName]['max']['cnn'] < $iCCN ) {
            $this->_aStats[$sClassName]['max']['cnn'] = $iCCN;
        }
    }

    /**
     * To update value of CCN if new one is bigger then older one
     *
     * @param int $iCCN new value of CCN
     */
    public function updateMaxCCN($iCCN)
    {
        if ( $this->_iMaxCCN < $iCCN ) {
            $this->_iMaxCCN = $iCCN;
        }
    }

    /**
     * To get max value for CCN
     *
     * @return int
     */
    public function getMaxCCN()
    {
        return $this->_iMaxCCN;
    }

    /**
     * To update class value of Crap index with new one if is bigger then older
     *
     * @param string $sClassName class name
     * @param int $iCrapIndex value of Crap index
     */
    public function updateClassMaxCrapIndex($sClassName, $iCrapIndex)
    {
        if ( !isset($this->_aStats[$sClassName]['max']['crap'])
            || $this->_aStats[$sClassName]['max']['crap'] < $iCrapIndex
        ) {
            $this->_aStats[$sClassName]['max']['crap'] = $iCrapIndex;
        }
    }

    /**
     * To update value of Crap index with new one if is bigger then older
     *
     * @param int $iCrapIndex value of Crap index
     */
    public function updateMaxCrapIndex($iCrapIndex)
    {
        if ( $this->_iMaxCrapIndexIndex < $iCrapIndex ) {
            $this->_iMaxCrapIndexIndex = $iCrapIndex;
        }
    }

    /**
     * To get max value of Crap index
     *
     * @return int max value of Crap index
     */
    public function getMaxCrapIndex()
    {
        return $this->_iMaxCrapIndexIndex;
    }

    /**
     * To update NPath value if new one is bigger then existing
     *
     * @param int $iNPath NPath value
     */
    public function updateMaxNPath($iNPath)
    {
        if ( $this->_iMaxNPath < $iNPath ) {
            $this->_iMaxNPath = $iNPath;
        }
    }

    /**
     * To update class NPath value if new one is bigger then existing
     *
     * @param string $sClassName class name
     * @param int $iNPath NPath value
     */
    public function updateClassMaxNPath($sClassName, $iNPath)
    {
        if ( !isset($this->_aStats[$sClassName]['max']['npath'])
            || $this->_aStats[$sClassName]['max']['npath'] < $iNPath
        ) {
            $this->_aStats[$sClassName]['max']['npath'] = $iNPath;
        }
    }

    /**
     * To get max value of NPath
     *
     * @return int
     */
    public function getMaxNPath()
    {
        return $this->_iMaxNPath;
    }

    /**
     * To update max LLOC value if new one is bigger then older one
     *
     * @param int $iLLOC value of LLOC
     */
    public function updateMaxLLOC($iLLOC)
    {
        if ( $this->_iMaxLLOC < $iLLOC ) {
            $this->_iMaxLLOC = $iLLOC;
        }
    }

    /**
     * To update class max LLOC value if new one is bigger then older one
     *
     * @param string $sClassName class name
     * @param int $iLLOC value of LLOC
     */
    public function updateClassMaxLLOC($sClassName, $iLLOC)
    {
        if ( !isset($this->_aStats[$sClassName]['max']['locExecutable'])
            || $this->_aStats[$sClassName]['max']['locExecutable'] < $iLLOC
        ) {
            $this->_aStats[$sClassName]['max']['locExecutable'] = $iLLOC;
        }
    }

    /**
     * To get number of max LLOC
     *
     * @return int
     */
    public function getMaxLLOC()
    {
        return $this->_iMaxLLOC;
    }

    /**
     * To set global values
     *
     * @param int $iCCN value of CCN
     * @param int $iCrapIndex Crap index
     * @param int $iNPath NPath value
     * @param int $iLLOC logical lines of code
     */
    public function setGlobalValues($iCCN, $iCrapIndex, $iNPath, $iLLOC)
    {
        //Global total
        $this->appendTotalCCN( (int)$iCCN * $iLLOC );
        $this->appendToTotalCrapIndex( (int)$iCrapIndex * $iLLOC );
        $this->appendToTotalNPath( (int)$iNPath * $iLLOC );
        $this->appendToTotalLLOC( $iLLOC );

        // Global Max
        $this->updateMaxCCN( $iCCN );
        $this->updateMaxCrapIndex( $iCrapIndex );
        $this->updateMaxNPath( $iNPath );
        $this->updateMaxLLOC( $iLLOC );
    }

    /**
     * To get average of CNN
     *
     * @return float
     */
    public function getTotalAverageCCN()
    {
        return $this->getTotalCCN() / $this->getTotalLLOC();
    }

    /**
     * To get average of Crap index
     *
     * @return float
     */
    public function getTotalAverageCrapIndex()
    {
        return $this->getTotalCrapIndex() / $this->getTotalLLOC();
    }

    /**
     * To get average of NPath
     * @return float
     */
    public function getTotalAverageNPath()
    {
        return $this->getTotalNPath() / $this->getTotalLLOC();
    }

    /**
     * To get classes stats
     *
     * @return array
     */
    public function getClassesStats()
    {
        return $this->_aStats;
    }
}

function printUsage($arg)
{
    echo 'Usage: php ' . basename( $arg ) . ' MetricsXml' . PHP_EOL;
    echo '    MetricsXml    - Metrics in PDepend xml format' . PHP_EOL;
    echo PHP_EOL;
    die("");
}

if ( !isset($argv["1"]) || !$argv["1"] ) {
    printUsage( $argv["0"] );
}

$sMetricsXml = $argv["1"];


try {
    $oMetrics = new Metrics($sMetricsXml);
    if ( $oMetrics->isExistingMetricsFile() ) {
        $oMetrics->read();

        echo "Total Avg ccn\t= " . round( $oMetrics->getTotalAverageCCN(), 3 ) . " (max: " . $oMetrics->getMaxCCN() . ")" . PHP_EOL;
        echo "Total Avg crap\t= " . round( $oMetrics->getTotalAverageCrapIndex(), 3 ) . " (max: " . $oMetrics->getMaxCrapIndex() . ")" . PHP_EOL;
        echo "Total Avg NPath\t= " . round( $oMetrics->getTotalAverageNPath(), 3 ) . " (max: " . $oMetrics->getMaxNPath() . ")" . PHP_EOL;
        echo "Total LLOC\t= " . $oMetrics->getTotalLLOC() . " (max: " . $oMetrics->getMaxLLOC() . ")" . PHP_EOL . PHP_EOL;

        $aStats = $oMetrics->getClassesStats();

        foreach ( $aStats as $sClass => $aClass ) {
            echo("Total for $sClass" . PHP_EOL);
            echo "\tAvg ccn \t= " . round( $aClass['stat']['cnn'], 3 ) . " (max: " . $aClass['max']['cnn'] . ")" . PHP_EOL;
            echo "\tAvg crap\t= " . round( $aClass['stat']['crap'], 3 ) . " (max: " . $aClass['max']['crap'] . ")" . PHP_EOL;
            echo "\tAvg npath\t= " . round( $aClass['stat']['npath'], 3 ) . " (max: " . $aClass['max']['npath'] . ")" . PHP_EOL;
            echo "\tLLOC \t\t= " . $aClass['sum']['locExecutable'] . PHP_EOL . PHP_EOL;
        }
    } else {
        echo "\n\nMetrics file: " . $sMetricsXml . " not exist, please select existing file!\n\n";
    }
}
catch ( Exception $oE ) {
    $sMsg = $oE->getMessage() . ' ' . $oE->getTraceAsString();
    echo($sMsg);
}