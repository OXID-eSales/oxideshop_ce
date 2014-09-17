<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty smartwordwrap modifier plugin
 *
 * Type:     modifier<br>
 * Name:     smartwordwrap<br>
 * Purpose:  wrap a string of text at a given length and row count
 *
 * @param string
 * @param integer
 * @param string
 * @param integer
 * @return integer
 */
function smarty_modifier_smartwordwrap($string, $length=80, $break="\n", $cutrows=0, $tollerance=0, $etc = '...')
{ 
    $wraptag = "<wrap>";
    $wrapchars = array("-");
    $afterwrapchars = array("-".$wraptag);
    
    
    $string = trim($string);
    
    if(strlen($string)<=$length) return $string;
    
    //trying to wrap without cut
    $str  = wordwrap($string,$length,$wraptag,false);
    $arr  = explode($wraptag, $str);
    
    $alt  = array();
    
    $ok = true;
    foreach($arr as $row)
    {
        if( strlen($row) > ($length+$tollerance))
        { 
            $tmpstr = str_replace($wrapchars, $afterwrapchars, $row);
            $tmparr = explode($wraptag,$tmpstr);
            
            foreach($tmparr as $altrow)
            {
                 array_push($alt, $altrow);
                 
                 if( strlen($altrow) > ($length+$tollerance) )
                 {
                    $ok = false;
                 }
            }   
        }
        else
        {
           array_push($alt, $row);
        }
    }
    
    $arr = $alt;
           
    if(!$ok){
        //trying to wrap with cut
        $str  = wordwrap($string,$length,$wraptag,true);
        $arr  = explode($wraptag, $str);
    }
    
    if($cutrows && count($arr)>$cutrows)
    {
        $arr = array_splice($arr, 0,$cutrows );
        
        if( strlen($arr[$cutrows].$etc) > $length + $tollerance)
        {
            $arr[$cutrows-1]= substr( $arr[$cutrows-1], 0, $length - strlen($etc) );      
        } 
        
        $arr[$cutrows-1] = $arr[$cutrows-1].$etc;
    }
    
    return implode($break, $arr);
}

?>