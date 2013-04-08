<?php
class dtausbuilder
{
	/**
	 * @var	string	Type of DTAUS (L = Lastschrift, G = Ueberweisung)
	 */
	protected $strType = 'L';
	
	/**
	 * @var	string	Subtype
	 */	 
	protected $strDetailedType; // = '05000';
	
	/**
	 * @var	string	Execution date in format DDMMYY
	 */
	protected $strExecDate; // date of execution
	
	/**
	 * @var	int	Referencenumber
	 */
	protected $intReference;
	
	/**
	 * @var	string	Name of sender
	 */
	protected $strName;
	
	/**
	 * @var	int	BLZ
	 */
	protected $intBank;
	
	/**
	 * @var	string	Accountid
	 */
	protected $intAccount;
	
	/**
	 * @var	array	array with entries
	 */
	protected $arrEntries = array(); // array for entries
	
	/**
	 * @var	array	array with checksums
	 */
	protected $arrCheck = array('entries' => 0, 'accountsum' => 0, 'banksum' => 0, 'amountsum' => 0);
	
	/**
	 * @var	string	Extenions will be added to every entry
	 */
	protected $arrGlobalExtensions = array();
	
	
	/**
	 * Constructor
	 * @param	string	$strName	Sendername
	 * @param	int		$intBank	BLZ
	 * @param	int		$intAccount	ID of account
	 */
	public function __construct($strName, $intBank, $intAccount)
	{
		// cut name into extensions if longer than 27
		$strName = $this->convertText($strName);
		if (strlen($strName) > 27)
		{
			$this->arrGlobalExtensions[] = array('type' => 3, 'text' => substr($strName, 27, 27));
			$strName = substr($strName, 0, 27);
		}
		$this->strName = $strName;
		$this->intBank = (int)$intBank;
		$this->intAccount = (int)$intAccount;
		
	}
	
	/**
	 * Sets type of DTAUS
	 * @param	string	$strType	L = Lastschrift; G = Gutschrift
	 */
	public function setType($strType, $strDetailedType = '')
	{
		$this->strType = $strType;
		$this->strDetailedType = $strDetailedType;
	}
	
	/**
	 * Sets execution date of DTAUS
	 * @param	string	$strExecTime	DDMMYY
	 */
	public function setExecDate($strExecTime)
	{
		$this->strExecTime = $strExecTime;
	}
	
	/**
	 * Sets reference number
	 * @param	integer	$intReference	Reference Number
	 */
	public function setReference($intReference)
	{
		$this->intReference = (int)$intReference;
	}
	
	/**
	 * Prefix string with zeros
	 * @param	int		$intCount	length of expected string
	 * @param	string	$strText	string with text used (optional)
	 * @return	string				new string, prefixed with zeros
	 */
	protected function pre0($intCount, $strText = '')
	{
		return str_pad(substr(($strText), 0, $intCount), $intCount, '0', STR_PAD_LEFT);
	}
	
	/**
	 * Convert text to upper, replace umlauts
	 * @param	string	$strText	string with text used
	 * @return	string				new converted string
	 */
	protected function convertText($strText)
	{
		return strtoupper(str_replace(array('ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'), array('AE', 'OE', 'UE', 'AE', 'OE', 'UE', 'SS'), $strText));
	}
	
	/**
	 * Suffix string with spaces
	 * @param	int		$intCount	length of expected string
	 * @param	string	$strText	string with text used (optional)
	 * @return	string				new string, suffixed with spaces
	 */
	protected function sufE($intCount, $strText = '')
	{
		return str_pad(substr($this->convertText($strText), 0, $intCount), $intCount, ' ', STR_PAD_RIGHT);
	}
	
	/**
	 * sort array with typeid
	 * @param	array	$a1	first array
	 * @param	array	$a2	first array
	 * @return	string		0, -1 or 1
	 */
	protected function sortExtensions($a1, $a2)
	{
		if ($a1['type'] == $a2['type']) {
			return 0;
		}
		return ($a1['type'] < $a2['type']) ? -1 : 1;
	}
	
	/**
	 * add new entry
	 * @param	string|array	$arrName		array with name, if is string, it will be cut every 27 signs
	 * @param	int				$intBank		BLZ
	 * @param	int				$intAccount		accountid
	 * @param	float			$floatAmmount 	amount, example: 12.34
	 * @param	string|array	$arrReference	array with reference, if is string, it will be cut every 27 signs (optional)
	 * @param	int				$intCustomerid	ID for customer (optional)
	 */
	public function add($arrName, $intBank, $intAccount, $floatAmount, $arrReference = array(), $intCustomerid = 0)
	{
		$arrExtensions = array();
		
		if (!is_array($arrReference))
			$arrReference = str_split($arrReference, 27);
		if (!is_array($arrName))
			$arrName = str_split($arrName, 27);
			
		array_map(array($this, 'convertText'), $arrReference, $arrName);
		
		
		for ($i = 1; $i < count($arrReference); $i++)
			$arrExtensions[] = array('type' => 2, 'text' => $arrReference[$i]);
		for ($i = 1; $i < count($arrName); $i++)
			$arrExtensions[] = array('type' => 1, 'text' => $arrName[$i]);
			
		$intAmount = $floatAmount * 100;
		$this->arrEntries[] = array
			(
				'name' => $arrName[0],
				'bank' => $intBank,
				'account' => $intAccount,
				'amount' => $intAmount,
				'reference' => $arrReference[0],
				'arrExtensions' => array_merge($arrExtensions, $this->arrGlobalExtensions),
				'customerid' => (int)$intCustomerid
			);
			
		$this->arrCheck['entries']++;
		$this->arrCheck['accountsum'] += $intAccount;
		$this->arrCheck['banksum']  += $intBank;
		$this->arrCheck['amountsum'] += $intAmount;
	}
	
	/**
	 * Get string with data
	 * @return	string		string with created DTAUS
	 */
	 
	public function create()
	{
		if (empty($this->strDetailedType))
			$this->strDetailedType = ($this->strType == 'L'? '05000' : '51000');
		
		// PART A = header
		$strOut = '0128'; // length
		$strOut .= 'A'; // type
		$strOut .= $this->strType . 'K'; // type: LK = Lastschrift, GK = Überweisung
		$strOut .= $this->pre0(8,$this->intBank); // BLZ
		$strOut .= $this->pre0(8); // just zeros
		$strOut .= $this->sufE(27, $this->strName); // sender
		$strOut .= date('dmy'); // creation date
		$strOut .= $this->sufE(4); // empty string
		$strOut .= $this->pre0(10, $this->intAccount); // account number
		$strOut .= $this->pre0(10, $this->intReference);
		$strOut .= $this->sufE(15) . $this->sufE(8, $this->strExecDate) . $this->sufE(24); // reference
		$strOut .= '1'; // currency = Euro

		foreach($this->arrEntries as $arrEntry)
		{
			// PART C = data
			$strOut .= $this->pre0(4, (187 + (count($arrEntry['arrExtensions']) * 29))); // length + (textcount * 29)
			$strOut .= 'C'; // type
			$strOut .= $this->pre0(8, $this->intBank); // blz target
			$strOut .= $this->pre0(8, $arrEntry['bank']); // blz source
			$strOut .= $this->pre0(10, $arrEntry['account']); // account source
			$strOut .= '0' . $this->pre0(11, $arrEntry['customerid']) . '0'; // customer id
			$strOut .= $this->strDetailedType; // Lastschrift = 05000; Gutschrift = 51000
			$strOut .= ' '; // ?
			$strOut .= '00000000000'; // amount in DM
			$strOut .= $this->pre0(8, $this->intBank); // blz target
			$strOut .= $this->pre0(10, $this->intAccount); // blz target
			$strOut .= $this->pre0(11, $arrEntry['amount']); // amount			
			$strOut .= '   ';
			$strOut .= $this->sufE(27, $arrEntry['name']); // name source
			$strOut .= '        ';
			$strOut .= $this->sufE(27, $this->strName); // name target
			$strOut .= $this->sufE(27, $arrEntry['reference']); // text 1
			$strOut .= '1'; // currency = EUR
			$strOut .= '  ';
			$strOut .= $this->pre0(2, count($arrEntry['arrExtensions']));
			
			usort($arrEntry['arrExtensions'], array($this, 'sortExtensions'));
			// and now this damn, ugly, stupid extensions
			$i = 0; // counter for parts
			$intExtensions = count($arrEntry['arrExtensions']);

			if ($i < $intExtensions) // part with only 2 extensions
			{
				$strOut .= $this->pre0(2, $arrEntry['arrExtensions'][$i]['type']);
				$strOut .= $this->sufE(27, $arrEntry['arrExtensions'][$i]['text']);
				$i++;
				if (!isset($arrEntry['arrExtensions'][$i]))
					$strOut .= $this->pre0(29);
				else
				{
					$strOut .= $this->pre0(2, $arrEntry['arrExtensions'][$i]['type']);
					$strOut .= $this->sufE(27, $arrEntry['arrExtensions'][$i]['text']);
				}
				$i++;
				$strOut .= $this->sufE(11);	
			}

			// now 4 blocks; 4th block just 1 part, needs extension
			$intBlock = 0;
			while($i < $intExtensions && $intBlock < 4)
			{
				$intBlock++;
                for ($x = 0; ($x < 4 && $intBlock < 4) || $x < 1; $x++)
                {
                    if ($i < $intExtensions)
                    {
						$strOut .= $this->pre0(2, $arrEntry['arrExtensions'][$i]['type']);
						$strOut .= $this->sufE(27, $arrEntry['arrExtensions'][$i]['text']);
                        $i++;
                    }
                    else
						$strOut .= $this->sufE(29);
				}
                if ($intBlock == 4) // 4th block has 1 extension, must be 128 byte
					$strOut .= $this->sufE(29 * 3);
				
				$strOut .= $this->sufE(12);
			}
		}
		
		// PART E
		$strOut .= '0128'; // length
		$strOut .= 'E'; // type
		$strOut .= '     '; // "     "
		$strOut .= $this->pre0(7, $this->arrCheck['entries']);
		$strOut .= '0000000000000'; // sum in DM; no more required
		$strOut .= $this->pre0(17, $this->arrCheck['accountsum']); // sum of accounts
		$strOut .= $this->pre0(17, $this->arrCheck['banksum']); // sum of blz
		$strOut .= $this->pre0(13, $this->arrCheck['amountsum']); // sum of amounts
		$strOut .= $this->sufE(51);

		return $strOut;
	}
}
?>