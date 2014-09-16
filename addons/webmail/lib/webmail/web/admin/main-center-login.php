<!-- [start center] -->
<script type="text/javascript">
<!--
	function Run()
	{
		radio = Array(
			document.getElementById('hideLoginRadionButton1'),
			document.getElementById('hideLoginRadionButton2'),
			document.getElementById('hideLoginRadionButton3')
		);
		
		checks = Array(
			document.getElementById('intDisplayDomainAfterLoginField'),
			document.getElementById('intLoginAsConcatination')
		);
		
		inputs = Array(
			document.getElementById('hideLoginSelect'),
			document.getElementById('txtUseDomain')
		);
	}
	
	function DoIt()
	{
		inputs[0].disabled = true;
		inputs[1].disabled = true;
		inputs[0].style.background = "#EEEEEE";
		inputs[1].style.background = "#EEEEEE";
		
		checks[0].disabled = true;
		checks[1].disabled = true;
		
		if (radio[1].checked == true)
		{
			inputs[0].disabled = false;
			inputs[0].style.background = "White";
		}

		if (radio[2].checked == true)
		{
			inputs[1].disabled = false;
			inputs[1].style.background = "White";
			checks[0].disabled = false;
			checks[1].disabled = false;
		}
	}

//-->
</script>
<form action="?mode=save" method="POST">
<input type="hidden" name="form_id" value="login">
<table class="wm_admin_center" width="500">
	<tr>
		<td width="50"></td>
		<td></td>
	</tr>
	<tr>
	<tr>
		<td colspan="2" class="wm_admin_title">Login Settings</td>
	</tr>
	<tr><td colspan="2"><br /></td></tr>
	
	<tr><td colspan="2">
<div class="wm_safety_info">
<b>Standard login panel, Hide login field, Hide email field</b> are sorts of WebMail login panel. Choice depends on your mail server configuration and your
requirements.
</div><br />
	</td></tr>
	
	<tr>
		<td align="right" valign="top">
			<input type="radio" name="hideLoginRadionButton" onchange="change();" id="hideLoginRadionButton1" value="0" style="vertical-align: middle" <?php echo isset($checkmass[0])?$checkmass[0]:'';?> onclick="DoIt()" />
		</td>
		<td><label for="hideLoginRadionButton1">Standard login panel</label></td>
	</tr>
	<tr>
		<td align="right" valign="top"><br />
			<input type="radio" name="hideLoginRadionButton" onchange="change();" id="hideLoginRadionButton2" value="1" style="vertical-align: middle" <?php echo isset($checkmass[1])?$checkmass[1]:'';?> onclick="DoIt()" />
		</td>
		<td><br />
			<label for="hideLoginRadionButton2">Hide login field</label>
			<br /><br />
			<select name="hideLoginSelect" onchange="change();" id="hideLoginSelect" class="wm_input">
				<option value="1" <?php echo isset($checkmass[4])?$checkmass[4]:'';?>>Use Email as Login</option>
				<option value="0" <?php echo isset($checkmass[3])?$checkmass[3]:'';?>>Use Account-name as Login</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right" valign="top"><br />
			<input type="radio" name="hideLoginRadionButton" onchange="change();" value="2" id="hideLoginRadionButton3" style="vertical-align: middle" <?php echo isset($checkmass[2])?$checkmass[2]:'';?> onclick="DoIt()" />
		</td>
		<td><br />
			<label for="hideLoginRadionButton3">Hide email field</label>
			<br /><br />&nbsp;<input type="text" name="txtUseDomain" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote($settings->DefaultDomainOptional);?>" id="txtUseDomain" class="wm_input" size="20" />&nbsp;&nbsp;domain to use
			<br /><br />
			<input type="checkbox" name="intDisplayDomainAfterLoginField" onchange="change();" value="1" id="intDisplayDomainAfterLoginField" style="vertical-align: middle" <?php echo isset($checkmass[5])?$checkmass[5]:'';?> />
			<label for="intDisplayDomainAfterLoginField"">Display domain after login field</label>
			<br /><br />
			<input type="checkbox" name="intLoginAsConcatination" onchange="change();" id="intLoginAsConcatination" value="1" style="vertical-align: middle" <?php echo isset($checkmass[6])?$checkmass[6]:'';?> />
			<label for="intLoginAsConcatination">Login as concatenation of "Login" field + "@" + domain</label>
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<input type="checkbox" value="1" name="intAllowAdvancedLogin" onchange="change();" id="intAllowAdvancedLogin" style="vertical-align: middle" <?php echo ((int) $settings->AllowAdvancedLogin == 1) ? 'checked="checked"' : '';?> />
		</td>
		<td>
			<label for="intAllowAdvancedLogin">Allow advanced login</label>
		</td>
	</tr>
		
	<tr><td colspan="2">
<div class="wm_safety_info">
<b>Allow advanced login</b> - allows changing SMTP and POP3/IMAP servers addresses, port numbers, enabling/disabling SMTP authentication from login panel.
</div>
	</td></tr>
	
	<tr>
		<td align="right" valign="top">
			<input type="checkbox" value="1" name="intAutomaticHideLogin" onchange="change();" style="vertical-align: middle" id="intAutomaticHideLogin" <?php echo ((int) $settings->AutomaticCorrectLoginSettings == 1) ? 'checked="checked"' : '';?> />
		</td>
		<td>
			<label for="intAutomaticHideLogin">Automatically detect and correct if user inputs e-mail instead of account-name</label>
		</td>
	</tr>
	
		<tr><td colspan="2">
<div class="wm_safety_info">
<b>Automatically detect and correct ...</b> -  if a user typed a full e-mail address instead of just an account name during logging in, it'll be automatically
corrected. Makes sense only with Standard login panel and Hide email field modes.
</div>
	</td></tr>
	
	<tr>
		<td colspan="3" align="center">
			<br /><div id="messDiv" class="messdiv" <?php echo (strlen($divMessage) > 0) ? 'style="border: 1px solid Silver;"' : '';?>>
			<?php echo (strlen($divMessage) > 0)? $divMessage : '&nbsp;';?></div>
		</td>
	</tr>
	<!-- hr -->
	<tr><td colspan="2"><hr size="1"></td></tr>
	<tr>
		<td colspan="2" align="right">
			<input type="submit" name="submit" value="Save" class="wm_button" style="width: 100px">&nbsp;
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
<!--
	Run();
	DoIt();
//-->
</script>
<!-- [end center] -->
