<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id: class.mailer.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

require(DIR_LIB . 'phpmailer/class.phpmailer.php');

class Mailer extends PHPMailer
{
    function SetLanguage($lang_type = 'en', $lang_path = "language/") 
	{
		$lang_path = DIR_LIB . 'phpmailer/' . $lang_path;
		return parent::SetLanguage($lang_type, $lang_path);
    }

    function AddImageStringAttachment($string, $filename, $encoding = "base64", $type = "application/octet-stream") 
	{
        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $string;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $filename;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = true; // isString
        $this->attachment[$cur][6] = "inline";
        $this->attachment[$cur][7] = $filename;
    }

	function RFCDate()
	{
		return date('r');
	}
}

?>