<!-- [start center] -->
<script type="text/javascript">
<!--

	function CheckForm()
	{
		var error = false;
		
		for (var i = 0; i < 5; i++)
		{
			if (needInput[i].value == '')
			{
				if (i > 0 && wmservercheckbox && wmservercheckbox.checked == true) continue;
				error = true;
				needInput[i].style.background = '#F39595';
			}
		}
		
		if (error == true)
		{
			writeDiv('<font color="red"><b>You cannot leave this field blank</b></font>');
			return false;
		}
		return true;
	}

	function hideTR()
	{
		if (!isHide)
		{
			for (i = 0; i < 4; i++)
			{
				document.getElementById('tr_' + i).className = '';
			}
			//isHide = false;
		}
		else
		{
			for (i = 0; i < 4; i++)
			{
				document.getElementById('tr_' + i).className = 'wm_hide';
			}
			//isHide = true;
		}

	}

	function changeProtocol(obj)
	{
		change();
		if (obj.value == 1)
		{
			isHide = true;
			needInput[2].value = '143';
			radio[0].disabled = true;
			radio[1].disabled = true;
			inputs[0].disabled = true;
			inputs[0].style.background = "#EEEEEE";
			checks[0].disabled = true;
			checks[1].disabled = true;
			checks[2].disabled = true;
			checks[3].disabled = true;
		}
		else
		{
			isHide = false;
			needInput[2].value = '110';
			
		}
		DoIt();
		hideTR();
	}

	function Run()
	{
		<?php
		
		echo (!isset($editAccount->MailProtocol) && ($settings->IncomingMailProtocol == MAILPROTOCOL_POP3 || $settings->IncomingMailProtocol == MAILPROTOCOL_WMSERVER))
			? 'isHide = false;' : 'isHide = true;'
		?>
		
		needInput = Array (
			document.getElementById('fm_email'),
			document.getElementById('fm_incoming_server'),
			document.getElementById('fm_incoming_server_port'),
			document.getElementById('fm_incoming_login'),
			document.getElementById('fm_smtp_server_port')
		);
		
		radio = Array(
			document.getElementById('fm_mail_management_mode1'),
			document.getElementById('fm_mail_management_mode2')
		);
		
		inputs = new Array(
			document.getElementById('fm_keep_messages_days')
		);
		
		selects = new Array(
			document.getElementById('synchronizeSelect'),
			document.getElementById('fm_incoming_protocol')
		);
		
		checks = Array(
			document.getElementById('fm_keep_for_x_days'),
			document.getElementById('fm_delete_messages_from_trash'),
			document.getElementById('fm_allow_direct_mode'),
			document.getElementById('fm_int_deleted_as_server')
		);
		
		wmservercheckbox = document.getElementById('int_is_wmserver');
	}
	
	function DoIt()
	{
		radio[0].disabled = false;
		radio[1].disabled = false;
		inputs[0].disabled = false;
		inputs[0].style.background = "White";
		checks[0].disabled = false;
		checks[1].disabled = false;
		checks[2].disabled = false;
		checks[3].disabled = false;
		
		if (selects[0].value == 1)
		{
			if (radio[0].checked == true)
			{
				radio[0].checked = false;
				radio[1].checked = true;
			}
			radio[0].disabled = true;			
		}
		
		if (selects[0].value == 5)
		{
			radio[0].disabled = true;
			radio[1].checked = true;
			radio[1].disabled = true;
			checks[0].checked = false;
			checks[1].checked = false;
			checks[0].disabled = true;
			checks[3].checked = false;
			checks[1].disabled = true;
			checks[3].disabled = true;
			inputs[0].disabled = true;
			inputs[0].style.background = "#EEEEEE";
		}
	
		if (radio[0].checked == true)
		{
			checks[0].checked = false;
			checks[1].checked = false;
			checks[0].disabled = true;
			checks[1].disabled = true;
			inputs[0].disabled = true;
			inputs[0].style.background = "#EEEEEE";
		}
		
		if (checks[0].checked != true)
		{
			inputs[0].disabled = true;	
			inputs[0].style.background = "#EEEEEE";
		}
		
		if (wmservercheckbox && wmservercheckbox.checked == true)
		{
			needInput[1].style.background = "#EEEEEE";
			needInput[2].style.background = "#EEEEEE";			
			needInput[3].style.background = "#EEEEEE";	
			selects[1].style.background = "#EEEEEE";
			
			needInput[1].disabled = true;
			needInput[2].disabled = true;
			needInput[3].disabled = true;
			selects[1].disabled = true;
		}
		else
		{
			needInput[1].style.background = "White";
			needInput[2].style.background = "White";	
			needInput[3].style.background = "White";
			selects[1].style.background = "White";
			
			needInput[1].disabled = false;
			needInput[2].disabled = false;
			needInput[3].disabled = false;
			selects[1].disabled = false;
		}
	}
//-->
</script>
<form action="?mode=save#foot" method="POST" onsubmit="return CheckForm();">
<input type="hidden" name="form_id" value="edit">
<input type="hidden" name="uid" value="<?php echo (isset($_GET['uid'])) ? $_GET['uid'] : -1;?>">
<input type="hidden" name="user_id" value="<?php echo (isset($_GET['user_id'])) ? $_GET['user_id'] : -1;?>">
<table class="wm_admin_center" width="500" border="0">
	<tr>
		<td width="140"></td>
		<td width="240"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3" class="wm_admin_title">Users Details</td>
	</tr>
<?php
	
	if (isset($_GET['uid']) && $_GET['uid'] > -1)
	{
		echo '<tr><td colspan="3" align="center">';
		echo '<table class="wm_settings_list" style="margin-bottom: 0px; width: 420px;">';
		
		$c = count($anotherAccounts);
		if ($c > 0)
		{
			foreach ($anotherAccounts as $keyid => $value)
			{
				echo ($keyid == $_GET['uid']) ? '<tr class="wm_settings_list_select">' : '<tr class="wm_control">';
				echo ($keyid == $_GET['uid']) ? '<td align="left"><b>'.$anotherAccounts[$keyid][4].'</b></td>' :
					'<td align="left" onclick="document.location=\'?mode=wm_edit&uid='.$keyid.'\'">'.$anotherAccounts[$keyid][4].'</td>';
				echo '<td style="width: 20px;">&nbsp;&nbsp;';
				echo '&nbsp;&nbsp;<a href="?mode=wm_delete&uid='.$keyid.'" onclick="return confirm(\'Delete Account?\');">Delete</a></td>';
				echo '</tr>';
			}
		}	
		
		echo '</table>';
		
		
		echo '
	<table class="wm_settings_buttons" style="border: 0px; width: 430px;">
		<tr><td>
				<!-- <input type="button" value="Add New Account" onclick="document.location=\'#\';" class="wm_button" id="Button1" name="Button2" /> -->
		</td></tr>
	</table>';
		echo '<br /><br /><br /></td></tr>';
	}
	else 
	{
		echo '<tr><td><br /></td></tr>';
	}
	
?>
	<tr>
		<td align="right">Mailbox limit: </td>
		<td colspan="2" align="left">
			<input type="text" size="13" onchange="change();" value="<?php echo isset($editAccount->MailboxLimit) ? ConvertUtils::AttributeQuote($editAccount->MailboxLimit) : '';?>" name="int_limit_mailbox" class="wm_input" maxlength="10"> bytes
		</td>
	</tr>	
	<tr>
		<td align="right">Name: </td>
		<td colspan="2" align="left">
			<input type="text" style="width: 350px" onchange="change();" name="fm_friendly_name" value="<?php echo isset($editAccount->FriendlyName) ? ConvertUtils::AttributeQuote($editAccount->FriendlyName) : '';?>" class="wm_input" maxlength="100">
		</td>
	</tr>	
	<tr>
		<td align="right">* Email: </td>
		<td colspan="2" align="left">
			<input type="text" style="width: 350px" onkeyup="RedThis(this);" id="fm_email" name="fm_email" value="<?php echo isset($editAccount->Email) ? ConvertUtils::AttributeQuote($editAccount->Email) : '';?>" class="wm_input" maxlength="100">
		</td>
	</tr>		
	<tr>
		<td align="right">* Incoming mail: </td>
		<td align="left">
			<input type="text" name="fm_incoming_server" id="fm_incoming_server" onkeyup="RedThis(this);" value="<?php echo isset($editAccount->MailIncHost) ? ConvertUtils::AttributeQuote($editAccount->MailIncHost) : '';?>" class="wm_input" maxlength="100">
			
			<?php

			if (isset($_GET['uid']) && $_GET['uid'] > -1)
			{
				if (isset($editAccount->MailProtocol) && $editAccount->MailProtocol == MAILPROTOCOL_IMAP4)
				{
					echo '<input type="hidden" name="fm_incoming_protocol" id="fm_incoming_protocol" value="1">&nbsp;&nbsp;IMAP';
				}
				else if (isset($editAccount->MailProtocol) && $editAccount->MailProtocol == MAILPROTOCOL_POP3)
				{
					echo '<input type="hidden" name="fm_incoming_protocol" id="fm_incoming_protocol" value="0">&nbsp;&nbsp;POP3';
				}
				else if (isset($editAccount->MailProtocol) && $editAccount->MailProtocol == MAILPROTOCOL_WMSERVER)
				{
					echo '<input type="hidden" name="fm_incoming_protocol" id="fm_incoming_protocol" value="2">&nbsp;&nbsp;WM';
				}
				else 
				{
					echo '<input type="hidden" name="fm_incoming_protocol" id="fm_incoming_protocol" value="0">&nbsp;&nbsp;ERROR';
				}
			}
			else 
			{
				$tempSelected = array('', '', '');
				$tempSelected[(int) (bool) $settings->IncomingMailProtocol] = 'selected="selected"';

				echo '
				<select name="fm_incoming_protocol" id="fm_incoming_protocol" class="wm_input" onchange="changeProtocol(this)">
					<option '.$tempSelected[0].' value="0"> POP3</option>
					<option '.$tempSelected[1].' value="1"> IMAP4</option>
				</select>';
			}
			?>
		</td>
		<td align="right">* Port:&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" size="3" name="fm_incoming_server_port" id="fm_incoming_server_port" onkeyup="RedThis(this);" id="fm_incoming_server_port" value="<?php echo isset($editAccount->MailIncPort) ? ConvertUtils::AttributeQuote($editAccount->MailIncPort) : '110';?>" class="wm_input" maxlength="4">
		</td>
	</tr>		
	
	<tr>
		<td align="right">* Login: </td>
		<td colspan="2" align="left">
			<input type="text" style="width: 350px" id="fm_incoming_login" onkeyup="RedThis(this);" name="fm_incoming_login" value="<?php echo isset($editAccount->MailIncLogin) ? ConvertUtils::AttributeQuote($editAccount->MailIncLogin) : '';?>" class="wm_input" maxlength="100">
		</td>
	</tr>	
	<tr>
		<td align="right">Password: </td>
		<td colspan="2" align="left">
			<input type="password" style="width: 350px" id="fm_incoming_password" onchange="change();" name="fm_incoming_password" class="wm_input" maxlength="100" value="<?php echo (isset($editAccount) && !empty($editAccount->MailIncPassword)) ? ConvertUtils::AttributeQuote(DUMMYPASSWORD) : '';?>">
		</td>
	</tr>	
	
	<tr>
		<td align="right">* SMTP server: </td>
		<td align="left">
			<input type="text" name="fm_smtp_server" 
			onfocus="if (this.value.length == 0) { this.value = document.getElementById('fm_incoming_server').value; this.select(); }"
			onchange="change();" onkeyup="RedThis(this);" value="<?php echo isset($editAccount->MailOutHost) ? ConvertUtils::AttributeQuote($editAccount->MailOutHost) : '';?>" class="wm_input" maxlength="100">
		</td>
		<td align="right">* Port:&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" size="3" onkeyup="RedThis(this);" id="fm_smtp_server_port" name="fm_smtp_server_port" value="<?php echo isset($editAccount->MailOutPort) ? ConvertUtils::AttributeQuote($editAccount->MailOutPort) : '25';?>" class="wm_input" maxlength="4">
		</td>
	</tr>

	<tr>
		<td align="right">SMTP login: </td>
		<td colspan="2" align="left">
			<input type="text" style="width: 350px" onchange="change();" name="fm_smtp_login" value="<?php echo isset($editAccount->MailOutLogin) ? ConvertUtils::AttributeQuote($editAccount->MailOutLogin) : '';?>" class="wm_input" maxlength="100">
		</td>
	</tr>
	<tr>
		<td align="right">SMTP password: </td>
		<td colspan="2" align="left">
			<input type="password" style="width: 350px" onchange="change();" name="fm_smtp_password" class="wm_input" maxlength="100" value="<?php echo (isset($editAccount) && !empty($editAccount->MailOutPassword)) ? ConvertUtils::AttributeQuote(DUMMYPASSWORD) : '';?>">
		</td>
	</tr>

	<tr>
		<td colspan="3" align="left">
			<input type="checkbox" name="fm_smtp_authorisation" onchange="change();" style="vertical-align: middle" id="fm_smtp_authorisation" value="1" class="wm_checkbox" <?php echo (isset($editAccount->MailOutAuthentication) && (int) $editAccount->MailOutAuthentication == 1) ? 'checked="checked"' : '';?> /> 
			<label for="fm_smtp_authorisation">Use SMTP authentication (You may leave SMTP login/password fields blank, if they're the same as POP3 login/password)</label>
		</td>
	</tr>
	
	<tr>
		<td colspan="3" align="left">
			<input type="checkbox" name="fm_use_friendly_name" onchange="change();" style="vertical-align: middle" id="fm_use_friendly_name" value="1" class="wm_checkbox" <?php echo (isset($editAccount->UseFriendlyName) && (int) $editAccount->UseFriendlyName == 1) ? 'checked="checked"' : '';?> />
			<label for="fm_use_friendly_name">Use Friendly Name in "From:" field (Your name &lt;sender@mail.com&gt;)</label> 
		</td>
	</tr>
	
	<tr>
		<td colspan="3" align="left">
			<input type="checkbox" name="fm_getmail_at_login" onchange="change();" style="vertical-align: middle" id="fm_getmail_at_login" value="1" class="wm_checkbox" <?php echo (isset($editAccount->GetMailAtLogin) && (int) $editAccount->GetMailAtLogin == 1) ? 'checked="checked"' : '';?> />
			<label for="fm_getmail_at_login">Synchronize folders at login</label> 			
		</td>
	</tr>	

	<?php
	$trIsHide = 'class="wm_hide"';
	$checkArray = array('','','','');
if (isset($editAccount->MailProtocol) && ($editAccount->MailProtocol == MAILPROTOCOL_POP3 || $editAccount->MailProtocol == MAILPROTOCOL_WMSERVER))
{	
	
	$trIsHide = '';
	if (isset($editAccount->MailMode))
	{
		switch ($editAccount->MailMode)
		{
			case MAILMODE_DeleteMessagesFromServer:
				$checkArray[0] = 'checked="checked"';
				break;
			case MAILMODE_LeaveMessagesOnServer:
				$checkArray[1] = 'checked="checked"';
				break;
			case MAILMODE_KeepMessagesOnServer:
				$checkArray[1] = 'checked="checked"';
				$checkArray[2] = 'checked="checked"';				
				break;
			case MAILMODE_DeleteMessageWhenItsRemovedFromTrash:
				$checkArray[1] = 'checked="checked"';
				$checkArray[3] = 'checked="checked"';				
				break;
			case MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash:
				$checkArray[1] = 'checked="checked"';
				$checkArray[2] = 'checked="checked"';
				$checkArray[3] = 'checked="checked"';
				break;
		}
	}
}
	
	?>
	<tr id="tr_0" <?php echo $trIsHide;?>>
		<td colspan="3" align="left">
				<input type="radio" value="1" onchange="change();" name="fm_mail_management_mode" style="vertical-align: middle" id="fm_mail_management_mode1" onclick="DoIt();" <?php echo $checkArray[0];?> />
				<label for="fm_mail_management_mode1">Delete received messages from server</label>
				<br />
				<input type="radio" value="2" onchange="change();" name="fm_mail_management_mode" style="vertical-align: middle" id="fm_mail_management_mode2" onclick="DoIt();" <?php echo $checkArray[1];?> />
				<label for="fm_mail_management_mode2">Leave messages on server</label>
		</td>
	</tr>
	<tr id="tr_1" <?php echo $trIsHide;?>>
		<td colspan="3" align="left">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="checkbox" name="fm_keep_for_x_days" onchange="change();" style="vertical-align: middle" id="fm_keep_for_x_days" value="1" class="wm_checkbox" <?php echo $checkArray[2];?> onclick="DoIt();" />
			<label for="fm_keep_for_x_days">Keep messages on server for</label> <input type="text" size="1" value="<?php echo isset($editAccount->MailsOnServerDays) ? (int) $editAccount->MailsOnServerDays : '';?>" name="fm_keep_messages_days" id="fm_keep_messages_days" class="wm_input" /> day(s)
			<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="checkbox" name="fm_delete_messages_from_trash" onchange="change();" style="vertical-align: middle" id="fm_delete_messages_from_trash" value="1" class="wm_checkbox" <?php echo $checkArray[3];?> />
			<label for="fm_delete_messages_from_trash">Delete message from server when it is removed from Trash</label>
		</td>
	</tr>	

	<tr id="tr_2" <?php echo $trIsHide;?>>
		<td colspan="3" align="left">
			Type of Inbox Synchronize: 
			<select onchange="DoIt();" name="synchronizeSelect" onchange="change();" id="synchronizeSelect">
				<option value="1" <?php echo ($folderSyncType == FOLDERSYNC_NewHeadersOnly || $folderSyncType == FOLDERSYNC_AllHeadersOnly) ? 'selected="selected"' : ''; ?>>Entire Headers</option>
				<option value="3" <?php echo ($folderSyncType == FOLDERSYNC_NewEntireMessages || $folderSyncType == FOLDERSYNC_AllEntireMessages) ? 'selected="selected"' : ''; ?>>Entire Messages</option>
				<option value="5" <?php echo ($folderSyncType == FOLDERSYNC_DirectMode) ? 'selected="selected"' : ''; ?>>Direct Mode</option>
			</select>
		</td>
	</tr>
	
	<tr id="tr_3" <?php echo $trIsHide;?>>
		<td colspan="3" align="left">
			<input type="checkbox" name="fm_int_deleted_as_server" onchange="change();" style="vertical-align: middle" id="fm_int_deleted_as_server" value="1" class="wm_checkbox" <?php
				echo ($folderSyncType == FOLDERSYNC_AllHeadersOnly || $folderSyncType == FOLDERSYNC_AllEntireMessages) ? 'checked="checked"' : '';
			?> />
			<label for="fm_int_deleted_as_server">Delete message from database if it no longer exists on mail server</label>
		</td>
	</tr>	
	
	<tr>
		<td colspan="3" align="left">
			<input type="checkbox" class="wm_checkbox" onchange="change();" name="fm_allow_direct_mode" style="vertical-align: middle" id="fm_allow_direct_mode" value="1" onclick="DoIt();" ID="Checkbox1" <?php echo (isset($editAccount->AllowDirectMode) && (int) $editAccount->AllowDirectMode == 1) ? 'checked="checked"' : '';?> />
			<label for="fm_allow_direct_mode">Allow Direct Mode</label>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="left">
			<input type="checkbox" class="wm_checkbox" onchange="change();" name="fm_allow_user_change_email_settings" style="vertical-align: middle" id="fm_allow_user_change_email_settings" value="1" ID="Checkbox2" <?php echo (isset($editAccount->AllowChangeSettings) && (int) $editAccount->AllowChangeSettings == 1) ? 'checked="checked"' : '';?> />
			<label for="fm_allow_user_change_email_settings">Allow user to change email settings</label>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="center">
			<br /><div id="messDiv" class="messdiv" <?php echo (strlen($divMessage) > 0) ? 'style="border: 1px solid Silver;"' : '';?>>
			<?php echo (strlen($divMessage) > 0)? $divMessage : '&nbsp;';?></div>
		</td>
	</tr>
	<!-- hr -->
	<tr><td colspan="3"><hr size="1"></td></tr>
	<tr>
		<td>
			<input type="button" name="backtolist" class="wm_button" onclick="document.location.replace('?mode=wm_users');" value="Cancel" style="width: 100px; font-weight: bold">
		</td>
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
