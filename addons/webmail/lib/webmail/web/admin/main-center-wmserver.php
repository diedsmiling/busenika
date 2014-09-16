<!-- [start center] -->
<script>
	function clearDiv()
	{
		document.getElementById('server_connection').disabled = false;
	}

	dotest = false;
	function AddDomain()
	{
		var newdomaininput = document.getElementById("strNewDomain");
		var form = CreateChildWithAttrs(document.body, 'form', [['action', 'mailadm.php?mode=save'], ['method', 'POST']]);
		CreateChildWithAttrs(form, 'input', [['type', 'hidden'], ['name', 'form_id'], ['value', 'adddomain']]);
		CreateChildWithAttrs(form, 'input', [['type', 'hidden'], ['name', 'newdomainname'], ['value', newdomaininput.value]]);
		form.submit();
	}	
	
	function DelDomain(obj)
	{
		var deldomainname = obj.id;
		var form = CreateChildWithAttrs(document.body, 'form', [['action', 'mailadm.php?mode=save'], ['method', 'POST']]);
		CreateChildWithAttrs(form, 'input', [['type', 'hidden'], ['name', 'form_id'], ['value', 'deldomain']]);
		CreateChildWithAttrs(form, 'input', [['type', 'hidden'], ['name', 'deldomainname'], ['value', deldomainname]]);
		form.submit();
	}	
	
	function formSubmit()
	{
		var actform = document.getElementById('actionform');
			
		if (dotest)
		{
			dotest = false;
			writeDiv("<font color='Black'><b>processing ...</b></font>");
			
			document.getElementById('server_connection').disabled = true;
			// document.getElementById('messDiv').innerHTML = '<img src="./admin/indicator_arrows.gif">'; 
			actform.action = "mailadm.php?mode=server_connection";
			actform.target = "frm";
		}
		else
		{
			alert
			actform.action = "mailadm.php?mode=save";
			actform.target = "_self";
		}
		return true;
	}
	
</script>
<form action="?mode=save" method="POST" name="actionform" id="actionform" onsubmit="return formSubmit()">
<input type="hidden" name="form_id" value="wmserver">
<table class="wm_admin_center" width="500" border="0">
	<tr>
		<td width="100"></td>
		<td width="160"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3" class="wm_admin_title">Settings</td>
	</tr>
	<tr><td colspan="3"><br /></td></tr>
	<tr>
		<td  align="right">
			<input type="checkbox" name="intEnableMwServer" onchange="change();" id="intEnableMwServer" <?php echo ((bool) $settings->EnableWmServer) ? 'checked="checked"' : '';?> value="1" />
		</td>
		<td><label for="intEnableMwServer">Enable Integration</label></td>
	</tr>
	<tr><td colspan="3">
<div class="wm_safety_info">
<b>Enable Integration</b> - allows managing accounts on AfterLogic XMail Server from WebMail.
</div><br />
	</td></tr>
	<tr>
		<td align="right">Path to Server: </td>
		<td colspan="2">
			<input type="text" name="txtWmServerRootPath" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote($settings->WmServerRootPath);?>" size="50" class="wm_input" maxlength="500">
		</td>
	</tr>
	<tr><td colspan="3">
<div class="wm_safety_info">
<b>Path to Server</b> - path to the MailRoot folder of AfterLogic XMail Server in your system, for instance C:/Program Files/AfterLogic XMail Server/MailRoot/.
</div><br />
	</td></tr>
	<tr>
		<td align="right">Server Host: </td>
		<td colspan="2">
			<input type="text" name="txtWmServerHostName" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote($settings->WmServerHost);?>" size="50" class="wm_input" maxlength="500">
		</td>
	</tr>
	<tr><td colspan="3">
<div class="wm_safety_info">
<b>Server Host</b> - IP address or hostname where AfterLogic XMail Server resides.
</div><br />
	</td></tr>
	
	<tr>
		<td align="right">
			<input type="checkbox" name="intWmAllowManageXMailAccounts" onchange="change();" id="intWmAllowManageXMailAccounts" <?php echo ((bool) $settings->WmAllowManageXMailAccounts) ? 'checked="checked"' : '';?> value="1" />
		</td>
		<td colspan="2"><label for="intWmAllowManageXMailAccounts">Allow&nbsp;users&nbsp;to&nbsp;manage&nbsp;accounts&nbsp;on&nbsp;AfterLogic&nbsp;XMail&nbsp;Server</label></td>
	</tr>
	<tr><td colspan="3">
<div class="wm_safety_info">
If a user adds or removes a linked account in his primary account settings and domain part of this account matches any of
your domains hosted by AfterLogic XMail Server, this account will be added/removed on AfterLogic XMail Server.
</div>
	</td></tr>
	<tr>
	
	<tr>
		<td colspan="3" align="center">
			<br /><div id="messDiv" class="messdiv" <?php echo (strlen($divMessage) > 0) ? 'style="border: 1px solid Silver;"' : '';?>>
			<?php echo (strlen($divMessage) > 0)? $divMessage : '&nbsp;';?></div>
		</td>
	</tr>
	
	<!-- hr -->
	<tr><td colspan="3"><hr size="1"></td></tr>
	<tr>
		<td colspan="3" align="right">
			<input type="submit" class="wm_button" name="server_connection" id="server_connection" onclick="dotest = true;" value="Test Server Connection" style="width: 200px; float: left;" />
			<input type="submit" name="save" class="wm_button" value="Save" style="width: 100px;">&nbsp;
		</td>
	</tr>
</table>
</form>
<iframe name="frm" height="0" width="0" style="visibility: hidden;"></iframe>
<!-- [end center] -->