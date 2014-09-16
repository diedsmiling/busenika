<?php

class Validate 
{
	/**
	 * @param String $strEmail
	 * @return bool
	 */
	function checkEmail($strEmail)
	{
		$pattern = "/[A-Z0-9\!#\$%\^\{\}`~&'\+-=_\.]+@[A-Z0-9\.-]/i";  
		
		$strEmail = ConvertUtils::SubStr(trim($strEmail), 0, 255);
		 	
		if(preg_match($pattern, $strEmail))
		{
			return true;
		}
		
		return false; 
	}
	
	/**
	 * @param string $strLogin
	 * @return bool
	 */
	function checkLogin($strLogin)
	{
		$strLogin = ConvertUtils::SubStr(trim($strLogin), 0, 255);
		
		if(!Validate::HasSpecSymbols($strLogin))
		{
			return true;
		}
		
		return false; 
	}
	
	/**
	 * @param int $port
	 * @return bool
	 */
	function checkPort($port)
	{
		$port = intval($port);
		if($port > 0 && $port < 65535)
		{
			return true;
		}

		return false;
	}
	
	/**
	 * @param String $strServerName
	 * @return bool
	 */
	function checkServerName($strServerName)
	{
		$pattern = "/[^A-Z0-9\.-]/i";
		$strServerName = ConvertUtils::SubStr(trim($strServerName), 0, 255);
		
		if(!preg_match($pattern, $strServerName))
		{
			return true;
		}
		
		return false;
	}
		
	/**
	 * @param String $strValue
	 * @return bool
	 */
	function HasSpecSymbols($strValue)
    {
        $pattern = "/[\"\/\\\*\?<>\|:]/";
        
    	if(preg_match($pattern, $strValue))
        {
            return true;
        }
        
        return false;
    }
	
    /**
     * @param String $strWeb
     * @return String
     */
    function cleanWebPage($strWebPage)
    {
    	$pattern = "/^[\/;<=>\[\\#\?]+/";
    	$strWebPage = ConvertUtils::SubStr(trim($strWebPage), 0, 255);
    	
    	return preg_replace($pattern, "", $strWebPage);
    }
}
