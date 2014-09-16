<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>WebMailPro - Administration</title>
	<link rel="stylesheet" href="<?php echo $skinPath;?>/styles.css" type="text/css" />
	<script type="text/javascript" src="class.common.js"></script>
	<script type="text/javascript" src="_functions.js"></script>
	<script type="text/javascript">
<!--
	Browser = new CBrowser();
	function writeDiv(text)
	{
		document.getElementById("messDiv").innerHTML = text;
	}
	
	function RedThis(obj)
	{
		change();
		obj.style.background = (obj.value == '') ? '#F39595' : 'White';
	}
	
	function SaveForm()
	{
		if (hasChanges)
		{
			return confirm('Do you want move to another page without Save? Select OK to continue.');
		}
		return true;
	}

	hasChanges = false;

	function CreateTable()
	{
		if (hasChanges)
		{
			alert('You should save the settings before creating the tables.');
			return false;
		}
		else
		{
			PopUpWindow('db-creator.php');
			return true;
		}
		return false;
	}
	
	function change()
	{
		hasChanges = true;
	}
	
	function PopUpWindow(url)
	{
		var shown = window.open(url, 'Popup',
			'left=(screen.width-700)/2,top=(screen.height-400)/2,'+
			'toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,'+
			'copyhistory=no,width=700,height=400');
		shown.focus();
		return false;
	}
//-->
	</script>
</head>

<body>
<div align="center" class="wm_content">
<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
	<span><?php echo StoreWebmail;?></span>
	<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
</div>
	
	<table class="wm_accountslist" id="accountslist">
	  <tr>
		<td>
			<span class="wm_accountslist_email">
				<a href="index.php" onclick="return SaveForm();">Return to mail login form</a>
			</span>
			<span class="wm_accountslist_logout">
				<a href="?mode=logout">Logout</a>
			</span>
			<span class="wm_accountslist_logout">
				&nbsp;<a href="help/default.htm" target="_blank">Help</a>
			</span>
		</td>
	  </tr>
	</table>