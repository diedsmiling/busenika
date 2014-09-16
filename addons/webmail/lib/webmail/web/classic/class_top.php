<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	class TopPanel
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var array
		 */
		var $_bodyonclick;
		
		/**
		 * @var array
		 */
		var $_bodyonresize;
		
		/**
		 * @param PageBuilder $pagebuilder
		 * @return TopPanel
		 */
		function TopPanel(&$pagebuilder)
		{
			$this->_bodyonclick = array();
			$this->_bodyonresize = array();
			
			$this->_pagebuilder = &$pagebuilder;
			
			$this->_pagebuilder->AddJSText('function changeLocation(urlstring) { document.location = urlstring; }');
			$this->AddOnClick('PopupMenu.checkShownItems();');
			$this->_pagebuilder->AddInitText('InfoPanel = new CInfo(document.getElementById("info"), "wm_info_message", document.getElementById("info_message"), document.getElementById("info_image"), "'.$this->_pagebuilder->_proc->account->DefaultSkin.'");');
		}
		
		/**
		 * @param string $text
		 */
		function AddOnClick($text)
		{
			$text = trim($text);
			if ($text) $this->_bodyonclick[] = $text;
		}
		
		/**
		 * @param string $text
		 */
		function AddOnResize($text)
		{
			$text = trim($text);
			if ($text) $this->_bodyonresize[] = $text;
		}
		
		function OnClickToHtml()
		{
			$out = '';
			$count = count($this->_bodyonclick);
			if ($count > 0)
			{
				$out .= 'onclick="';
				
				for ($i = 0; $i < $count; $i++)
				{
					$out .= $this->_bodyonclick[$i].' ';
				}
				$out = trim($out).'"';
			}
			return $out;
		}
		
		function OnResizeToHtml()
		{
			$out = '';
			$count = count($this->_bodyonresize);
			if ($count > 0)
			{
				$out .= 'onresize="';
				
				for ($i = 0; $i < $count; $i++)
				{
					$out .= $this->_bodyonresize[$i].' ';
				}
				$out = trim($out).'"';
			}
			return $out;
		}
		
		/*
		 * @return string
		 */
		function ToHTML()
		{
			$noscroll = ($this->_pagebuilder->_proc->sArray[SCREEN] != SCREEN_MAILBOX) ? '' : 'scroll="no" style="overflow: hidden;"';
			$infotext = (isset($_SESSION[INFORMATION]) && strlen($_SESSION[INFORMATION]) > 0) ? $_SESSION[INFORMATION] : JS_LANG_InfoWebMailLoading;
			
			if (isset($_SESSION[REPORT]))
			{
				$this->_pagebuilder->AddInitText('
Report = new CReport(\'Report\');
Report.Build();
Report.Show("'.ConvertUtils::ReBuildStringToJavaScript($_SESSION[REPORT], '"').'");
				');
				unset($_SESSION[REPORT]);
			}
			
	return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html id="html">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="private,max-age=1209600" />
	<title>' . $this->_pagebuilder->Title() . '</title>
	<link rel="stylesheet" href="./skins/' . $this->_pagebuilder->SkinName() . '/styles.css" type="text/css" />'
	.	
	$this->_pagebuilder->_js->ToHTML()
	.
'</head>
<body '.$this->OnClickToHtml().' '.$this->OnResizeToHtml().' '.$noscroll.'>
<div align="center" class="wm_content">
	<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
		<span><?php echo StoreWebmail;?></span>
		<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
	</div>
	
	<table class="wm_information" id="info">
		<tr>
			<td class="wm_info_image" id="info_image_td">
				<img src="skins/'.$this->_pagebuilder->SkinName().'/info.gif" id="info_image" class="wm_hide">
			</td> 
			<td class="wm_info_message" id="info_message">
			'.$infotext.'
			</td>
		</tr>
	</table>';
		}
		
	}