<?php

	Header('Content-type: text/html; charset=utf-8');
	if (!isset($_GET['tn']))
	{
		die();
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
		<title> </title>
	</head>
	<body bgcolor="Silver">
		<table width="100%" height="100%">
		<tr>
			<td align="center" valign="middle">
				<img border="1" style="border: 1 px Black;" src="attach.php?tn=<?php echo urlencode($_GET['tn']);?>">
			</td>
		</tr>
		</table>
	</body>
</html>	