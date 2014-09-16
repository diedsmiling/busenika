<?php
	// from base_defines.php
	$CHARSETS = array(
		array('default', JS_LANG_CharsetDefault),
		array('iso-8859-6', JS_LANG_CharsetArabicAlphabetISO),
		array('windows-1256', JS_LANG_CharsetArabicAlphabet),
		array('iso-8859-4', JS_LANG_CharsetBalticAlphabetISO),
		array('windows-1257', JS_LANG_CharsetBalticAlphabet),
		array('iso-8859-2', JS_LANG_CharsetCentralEuropeanAlphabetISO),
		array('windows-1250', JS_LANG_CharsetCentralEuropeanAlphabet),
		array('euc-cn', JS_LANG_CharsetChineseSimplifiedEUC), //'51936'
		array('gb2312', JS_LANG_CharsetChineseSimplifiedGB), // '936'		
		array('big5', JS_LANG_CharsetChineseTraditional),
		array('iso-8859-5', JS_LANG_CharsetCyrillicAlphabetISO),
		array('koi8-r', JS_LANG_CharsetCyrillicAlphabetKOI8R),
		array('windows-1251', JS_LANG_CharsetCyrillicAlphabet),
		array('iso-8859-7', JS_LANG_CharsetGreekAlphabetISO),
		array('windows-1253', JS_LANG_CharsetGreekAlphabet),
		array('iso-8859-8', JS_LANG_CharsetHebrewAlphabetISO),
		array('windows-1255', JS_LANG_CharsetHebrewAlphabet),
		array('iso-2022-jp', JS_LANG_CharsetJapanese),
		array('shift-jis', JS_LANG_CharsetJapaneseShiftJIS),
		array('euc-kr', JS_LANG_CharsetKoreanEUC),
		array('iso-2022-kr', JS_LANG_CharsetKoreanISO),
		array('iso-8859-3', JS_LANG_CharsetLatin3AlphabetISO),
		array('windows-1254', JS_LANG_CharsetTurkishAlphabet),
		array('utf-7', JS_LANG_CharsetUniversalAlphabetUTF7),
		array('utf-8', JS_LANG_CharsetUniversalAlphabetUTF8),
		array('windows-1258', JS_LANG_CharsetVietnameseAlphabet),
		array('iso-8859-1', JS_LANG_CharsetWesternAlphabetISO),
		array('windows-1252', JS_LANG_CharsetWesternAlphabet)
	);

?><!-- [start center] -->
<script type="text/javascript">
<!--
	function Run()
	{
		input1 = document.getElementById('intIncomingMailPort');
		select1 = document.getElementById('intIncomingMailProtocol');
		check1 = document.getElementById('intAllowDirectMode');
		check2 = document.getElementById('intDirectModeIsDefault');
		
		input2 = document.getElementById('intAttachmentSizeLimit');
		input3 = document.getElementById('intMailboxSizeLimit');
		check3 = document.getElementById('intEnableAttachSizeLimit');
		check4 = document.getElementById('intEnableMailboxSizeLimit');
		
		inputIncomingMail = document.getElementById('txtIncomingMail');
	}
	
	function DoIt()
	{
		if (check1.checked == true)
		{
			check2.disabled = false;
		}
		else
		{
			check2.checked = false;
			check2.disabled = true;
		}
		
		if (check3.checked == true)
		{
			input2.style.background = "White";
			input2.disabled = false;
		}
		else
		{
			input2.style.background = "#EEEEEE";
			input2.disabled = true;
		}
		
		if (check4.checked == true)
		{
			input3.style.background = "White";
			input3.disabled = false;
		}
		else
		{
			input3.style.background = "#EEEEEE";
			input3.disabled = true;
		}
	}
	
	function mailProtocolChange() 
	{
		change();
		if (select1.value == 0)
		{
			input1.disabled = false;
			inputIncomingMail.disabled = false;
			input1.value = '110';
		}
		else if (select1.value == 1)
		{
			input1.disabled = false;
			inputIncomingMail.disabled = false;
			input1.value = '143';
		}
		else
		{
			input1.disabled = true;
			inputIncomingMail.disabled = true;
		}
	}

//-->
</script>
<form action="?mode=save#foot" method="POST">
<input type="hidden" name="form_id" value="settings">
<table class="wm_admin_center" width="500" border="0">
	<tr>
		<td width="150"></td>
		<td width="160"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3" class="wm_admin_title">WebMail Settings</td>
	</tr>
	<tr><td colspan="2"><br /></td></tr>
	<tr>
		<td align="right">Site name: </td>
		<td colspan="2">
			<input type="text" name="txtSiteName" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote($settings->WindowTitle);?>" size="50" class="wm_input" maxlength="100">
		</td>
	</tr>
	
	<tr><td colspan="3"><br /></td></tr>
	<tr>
		<td colspan="3" class="wm_admin_title">Default Mail Server Settings</td>
	</tr>
	<tr><td colspan="3"><br /></td></tr>
	<tr>
		<td align="right">Incoming Mail: </td>

		<td>
			<input type="text" class="wm_input" name="txtIncomingMail"  id="txtIncomingMail" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote($settings->IncomingMailServer);?>" maxlength="100">
		</td>
		<td>
			<nobr>Port:&nbsp;<input type="text" class="wm_input" name="intIncomingMailPort" id="intIncomingMailPort" size="3" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote($settings->IncomingMailPort);?>" maxlength="5">
			&nbsp;<select name="intIncomingMailProtocol" id="intIncomingMailProtocol" onchange="mailProtocolChange();" class="wm_input">
				<option value="0" <?php echo ((int) $settings->IncomingMailProtocol == MAILPROTOCOL_POP3) ? 'selected="selected"' : '' ;?>> POP3</option>
				<option value="1" <?php echo ((int) $settings->IncomingMailProtocol == MAILPROTOCOL_IMAP4) ? 'selected="selected"' : '' ;?>> IMAP4</option>
			</select></nobr>
		</td>
	</tr>
	<tr>
		<td align="right">Outgoing Mail: </td>
		<td>
			<input type="text" class="wm_input" name="txtOutgoingMail" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote($settings->OutgoingMailServer);?>" maxlength="100">
		</td>
		<td>
			Port:&nbsp;<input type="text" class="wm_input" name="intOutgoingMailPort" onchange="change();" size="3" value="<?php echo ConvertUtils::AttributeQuote($settings->OutgoingMailPort);?>" maxlength="5">
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" name="intReqSmtpAuthentication" onchange="change();" id="intReqSmtpAuthentication" value="1" <?php echo ((int) $settings->ReqSmtpAuth == 1) ? 'checked="checked"' : '';?> />
			<label for="intReqSmtpAuthentication">Requires SMTP authentication</label>
		</td>

	</tr>
	<tr>
		<td align="right" >&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" name="intAllowDirectMode" onchange="change();" id="intAllowDirectMode" value="1" <?php echo ((int) $settings->AllowDirectMode == 1) ? 'checked="checked"' : '';?> onclick="DoIt();">
			<label for="intAllowDirectMode">Allow direct mode</label>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" name="intDirectModeIsDefault" onchange="change();" id="intDirectModeIsDefault" <?php echo ((int) $settings->DirectModeIsDefault == 1) ? 'checked="checked"' : '';?> value="1" />
			<label for="intDirectModeIsDefault">Direct mode is default</label>
		</td>
	</tr>
	<tr>
		<td align="right">Attachment size limit: </td>

		<td colspan="2">
			<input type="text" class="wm_input" name="intAttachmentSizeLimit" id="intAttachmentSizeLimit" onchange="change();" style="width: 85px" value="<?php echo ConvertUtils::AttributeQuote($settings->AttachmentSizeLimit);?>" maxlength="10"> bytes
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" style="vertical-align: middle" onchange="change();" onclick="DoIt();" name="intEnableAttachSizeLimit" id="intEnableAttachSizeLimit" value="1" <?php echo ((int) $settings->EnableAttachmentSizeLimit == 0) ? '' : 'checked="checked"';?> />
			<label for="intEnableAttachSizeLimit">Enable Attachment size limit</label>
		</td>
	</tr>
	<tr>
		<td align="right">Mailbox size limit: </td>
		<td colspan="2">
			<input type="text" class="wm_input" name="intMailboxSizeLimit" id="intMailboxSizeLimit" onchange="change();" style="width: 85px" value="<?php echo (int) $settings->MailboxSizeLimit;?>" maxlength="10"> bytes
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" style="vertical-align: middle" onchange="change();" onclick="DoIt();" name="intEnableMailboxSizeLimit" id="intEnableMailboxSizeLimit" value="1" <?php echo ((int) $settings->EnableMailboxSizeLimit == 0) ? '' : 'checked="checked"';?> />
			<label for="intEnableMailboxSizeLimit">Enable mailbox size limit</label>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" onchange="change();" name="intAllowUsersChangeEmailSettings" id="intAllowUsersChangeEmailSettings" value="1" <?php echo ((int) $settings->AllowUsersChangeEmailSettings == 1) ? 'checked="checked"' : '';?> />
			<label for="intAllowUsersChangeEmailSettings">Allow new users to change email settings</label>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" onchange="change();" name="intAllowNewUsersRegister" id="intAllowNewUsersRegister" value="1" <?php echo ((int) $settings->AllowNewUsersRegister == 1) ? 'checked="checked"' : '';?> />
			<label for="intAllowNewUsersRegister">Allow automatic registration of new users on first login</label>
		</td>
	</tr>

	<tr>
		<td align="right">&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" onchange="change();" name="intAllowUsersAddNewAccounts" id="intAllowUsersAddNewAccounts" value="1" <?php echo ((int) $settings->AllowUsersAddNewAccounts == 1) ? 'checked="checked"' : '';?> />
			<label for="intAllowUsersAddNewAccounts">Allow users to add new email accounts</label>
		</td>
	</tr>
	
	<tr><td colspan="2"><br /></td></tr>
	<tr>
		<td colspan="3" class="wm_admin_title">Internationalization Support</td>
	</tr>
	<tr><td colspan="3"><br /></td></tr>
	<tr>
		<td align="right">Default user charset </td>
		<td colspan="2">
			<select name="txtDefaultUserCharset" class="wm_input" onchange="change();" style="width: 320px;">
			<?php
				foreach ($CHARSETS As $value)
				{
					echo '<option ';
					echo ($settings->DefaultUserCharset == $value[0]) ? 'selected="selected"' : '';
					echo 'value="'.$value[0].'"> '.$value[1].'</option>';
				}
			?>
			</select> 
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" onchange="change();" name="intAllowUsersChangeCharset" id="intAllowUsersChangeCharset" value="1" <?php echo ((int) $settings->AllowUsersChangeCharset == 1) ? 'checked="checked"' : '';?> />
			<label for="intAllowUsersChangeCharset">Allow users to change charset</label>
		</td>
	</tr>
	<tr>
		<td align="right">Default user time offset</td>
		<td colspan="2">
			<select name="txtDefaultTimeZone" class="wm_input" onchange="change();" style="width: 320px;">
				<option <?php echo ($settings->DefaultTimeZone == 0) ? 'selected="selected"' : ''; ?> value="0"> Default</option>
				<option <?php echo ($settings->DefaultTimeZone == 1) ? 'selected="selected"' : ''; ?> value="1"> (GMT -12:00) Eniwetok, Kwajalein, Dateline Time</option>
				<option <?php echo ($settings->DefaultTimeZone == 2) ? 'selected="selected"' : ''; ?> value="2"> (GMT -11:00) Midway Island, Samoa</option>
				<option <?php echo ($settings->DefaultTimeZone == 3) ? 'selected="selected"' : ''; ?> value="3"> (GMT -10:00) Hawaii</option>
				<option <?php echo ($settings->DefaultTimeZone == 4) ? 'selected="selected"' : ''; ?> value="4"> (GMT -09:00) Alaska</option>
				<option <?php echo ($settings->DefaultTimeZone == 5) ? 'selected="selected"' : ''; ?> value="5"> (GMT -08:00) Pacific Time (US & Canada); Tijuana</option>
				<option <?php echo ($settings->DefaultTimeZone == 6) ? 'selected="selected"' : ''; ?> value="6"> (GMT -07:00) Arizona</option>
				<option <?php echo ($settings->DefaultTimeZone == 7) ? 'selected="selected"' : ''; ?> value="7"> (GMT -07:00) Mountain Time (US & Canada)</option>
				<option <?php echo ($settings->DefaultTimeZone == 8) ? 'selected="selected"' : ''; ?> value="8"> (GMT -06:00) Central America</option>
				<option <?php echo ($settings->DefaultTimeZone == 9) ? 'selected="selected"' : ''; ?> value="9"> (GMT -06:00) Central Time (US & Canada)</option>
				<option <?php echo ($settings->DefaultTimeZone == 10) ? 'selected="selected"' : ''; ?> value="10"> (GMT -06:00) Mexico City, Tegucigalpa</option>
				<option <?php echo ($settings->DefaultTimeZone == 11) ? 'selected="selected"' : ''; ?> value="11"> (GMT -06:00) Saskatchewan</option>
				<option <?php echo ($settings->DefaultTimeZone == 12) ? 'selected="selected"' : ''; ?> value="12"> (GMT -05:00) Indiana (East)</option>
				<option <?php echo ($settings->DefaultTimeZone == 13) ? 'selected="selected"' : ''; ?> value="13"> (GMT -05:00) Eastern Time (US & Canada)</option>
				<option <?php echo ($settings->DefaultTimeZone == 14) ? 'selected="selected"' : ''; ?> value="14"> (GMT -05:00) Bogota, Lima, Quito</option>
				<option <?php echo ($settings->DefaultTimeZone == 15) ? 'selected="selected"' : ''; ?> value="15"> (GMT -04:00) Santiago</option>
				<option <?php echo ($settings->DefaultTimeZone == 16) ? 'selected="selected"' : ''; ?> value="16"> (GMT -04:00) Caracas, La Paz</option>
				<option <?php echo ($settings->DefaultTimeZone == 17) ? 'selected="selected"' : ''; ?> value="17"> (GMT -04:00) Atlantic Time (Canada)</option>
				<option <?php echo ($settings->DefaultTimeZone == 18) ? 'selected="selected"' : ''; ?> value="18"> (GMT -03:30) Newfoundland</option>
				<option <?php echo ($settings->DefaultTimeZone == 19) ? 'selected="selected"' : ''; ?> value="19"> (GMT -03:00) Greenland</option>
				<option <?php echo ($settings->DefaultTimeZone == 20) ? 'selected="selected"' : ''; ?> value="20"> (GMT -03:00) Buenos Aires, Georgetown</option>
				<option <?php echo ($settings->DefaultTimeZone == 21) ? 'selected="selected"' : ''; ?> value="21"> (GMT -03:00) Brasilia</option>
				<option <?php echo ($settings->DefaultTimeZone == 22) ? 'selected="selected"' : ''; ?> value="22"> (GMT -02:00) Mid-Atlantic</option>
				<option <?php echo ($settings->DefaultTimeZone == 23) ? 'selected="selected"' : ''; ?> value="23"> (GMT -01:00) Cape Verde Is.</option>
				<option <?php echo ($settings->DefaultTimeZone == 24) ? 'selected="selected"' : ''; ?> value="24"> (GMT -01:00) Azores</option>
				<option <?php echo ($settings->DefaultTimeZone == 25) ? 'selected="selected"' : ''; ?> value="25"> (GMT) Casablanca, Monrovia</option>
				<option <?php echo ($settings->DefaultTimeZone == 26) ? 'selected="selected"' : ''; ?> value="26"> (GMT) Dublin, Edinburgh, Lisbon, London</option>
				<option <?php echo ($settings->DefaultTimeZone == 27) ? 'selected="selected"' : ''; ?> value="27"> (GMT +01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
				<option <?php echo ($settings->DefaultTimeZone == 28) ? 'selected="selected"' : ''; ?> value="28"> (GMT +01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
				<option <?php echo ($settings->DefaultTimeZone == 29) ? 'selected="selected"' : ''; ?> value="29"> (GMT +01:00) Brussels, Copenhagen, Madrid, Paris</option>
				<option <?php echo ($settings->DefaultTimeZone == 30) ? 'selected="selected"' : ''; ?> value="30"> (GMT +01:00) Sarajevo, Skopje, Sofija, Vilnius, Warsaw, Zagreb</option>
				<option <?php echo ($settings->DefaultTimeZone == 31) ? 'selected="selected"' : ''; ?> value="31"> (GMT +01:00) West Central Africa</option>
				<option <?php echo ($settings->DefaultTimeZone == 32) ? 'selected="selected"' : ''; ?> value="32"> (GMT +02:00) Athens, Istanbul, Minsk</option>
				<option <?php echo ($settings->DefaultTimeZone == 33) ? 'selected="selected"' : ''; ?> value="33"> (GMT +02:00) Bucharest</option>
				<option <?php echo ($settings->DefaultTimeZone == 34) ? 'selected="selected"' : ''; ?> value="34"> (GMT +02:00) Cairo</option>
				<option <?php echo ($settings->DefaultTimeZone == 35) ? 'selected="selected"' : ''; ?> value="35"> (GMT +02:00) Harare, Pretoria</option>
				<option <?php echo ($settings->DefaultTimeZone == 36) ? 'selected="selected"' : ''; ?> value="36"> (GMT +02:00) Helsinki, Riga, Tallinn</option>
				<option <?php echo ($settings->DefaultTimeZone == 37) ? 'selected="selected"' : ''; ?> value="37"> (GMT +02:00) Israel, Jerusalem Standard Time</option>
				<option <?php echo ($settings->DefaultTimeZone == 38) ? 'selected="selected"' : ''; ?> value="38"> (GMT +03:00) Baghdad</option>
				<option <?php echo ($settings->DefaultTimeZone == 39) ? 'selected="selected"' : ''; ?> value="39"> (GMT +03:00) Arab, Kuwait, Riyadh</option>
				<option <?php echo ($settings->DefaultTimeZone == 40) ? 'selected="selected"' : ''; ?> value="40"> (GMT +03:00) Moscow, St. Petersburg, Volgograd</option>
				<option <?php echo ($settings->DefaultTimeZone == 41) ? 'selected="selected"' : ''; ?> value="41"> (GMT +03:00) East Africa, Nairobi</option>
				<option <?php echo ($settings->DefaultTimeZone == 42) ? 'selected="selected"' : ''; ?> value="42"> (GMT +03:30) Tehran</option>
				<option <?php echo ($settings->DefaultTimeZone == 43) ? 'selected="selected"' : ''; ?> value="43"> (GMT +04:00) Abu Dhabi, Muscat</option>
				<option <?php echo ($settings->DefaultTimeZone == 44) ? 'selected="selected"' : ''; ?> value="44"> (GMT +04:00) Baku, Tbilisi, Yerevan</option>
				<option <?php echo ($settings->DefaultTimeZone == 45) ? 'selected="selected"' : ''; ?> value="45"> (GMT +04:30) Kabul</option>
				<option <?php echo ($settings->DefaultTimeZone == 46) ? 'selected="selected"' : ''; ?> value="46"> (GMT +05:00) Ekaterinburg</option>
				<option <?php echo ($settings->DefaultTimeZone == 47) ? 'selected="selected"' : ''; ?> value="47"> (GMT +05:00) Islamabad, Karachi, Sverdlovsk, Tashkent</option>
				<option <?php echo ($settings->DefaultTimeZone == 48) ? 'selected="selected"' : ''; ?> value="48"> (GMT +05:30) Calcutta, Chennai, Mumbai, New Delhi, India Standard Time</option>
				<option <?php echo ($settings->DefaultTimeZone == 49) ? 'selected="selected"' : ''; ?> value="49"> (GMT +05:45) Kathmandu, Nepal</option>
				<option <?php echo ($settings->DefaultTimeZone == 50) ? 'selected="selected"' : ''; ?> value="50"> (GMT +06:00) Almaty, Novosibirsk, North Central Asia</option>
				<option <?php echo ($settings->DefaultTimeZone == 51) ? 'selected="selected"' : ''; ?> value="51"> (GMT +06:00) Astana, Dhaka</option>
				<option <?php echo ($settings->DefaultTimeZone == 52) ? 'selected="selected"' : ''; ?> value="52"> (GMT +06:00) Sri Jayewardenepura, Sri Lanka</option>
				<option <?php echo ($settings->DefaultTimeZone == 53) ? 'selected="selected"' : ''; ?> value="53"> (GMT +06:30) Rangoon</option>
				<option <?php echo ($settings->DefaultTimeZone == 54) ? 'selected="selected"' : ''; ?> value="54"> (GMT +07:00) Bangkok, Hanoi, Jakarta</option>
				<option <?php echo ($settings->DefaultTimeZone == 55) ? 'selected="selected"' : ''; ?> value="55"> (GMT +07:00) Krasnoyarsk</option>
				<option <?php echo ($settings->DefaultTimeZone == 56) ? 'selected="selected"' : ''; ?> value="56"> (GMT +08:00) Beijing, Chongqing, Hong Kong SAR, Urumqi</option>
				<option <?php echo ($settings->DefaultTimeZone == 57) ? 'selected="selected"' : ''; ?> value="57"> (GMT +08:00) Irkutsk, Ulaan Bataar</option>
				<option <?php echo ($settings->DefaultTimeZone == 58) ? 'selected="selected"' : ''; ?> value="58"> (GMT +08:00) Kuala Lumpur, Singapore</option>
				<option <?php echo ($settings->DefaultTimeZone == 59) ? 'selected="selected"' : ''; ?> value="59"> (GMT +08:00) Perth, Western Australia</option>
				<option <?php echo ($settings->DefaultTimeZone == 60) ? 'selected="selected"' : ''; ?> value="60"> (GMT +08:00) Taipei</option>
				<option <?php echo ($settings->DefaultTimeZone == 61) ? 'selected="selected"' : ''; ?> value="61"> (GMT +09:00) Osaka, Sapporo, Tokyo</option>
				<option <?php echo ($settings->DefaultTimeZone == 62) ? 'selected="selected"' : ''; ?> value="62"> (GMT +09:00) Seoul, Korea Standard time</option>
				<option <?php echo ($settings->DefaultTimeZone == 63) ? 'selected="selected"' : ''; ?> value="63"> (GMT +09:00) Yakutsk</option>
				<option <?php echo ($settings->DefaultTimeZone == 64) ? 'selected="selected"' : ''; ?> value="64"> (GMT +09:30) Adelaide, Central Australia</option>
				<option <?php echo ($settings->DefaultTimeZone == 65) ? 'selected="selected"' : ''; ?> value="65"> (GMT +09:30) Darwin</option>
				<option <?php echo ($settings->DefaultTimeZone == 66) ? 'selected="selected"' : ''; ?> value="66"> (GMT +10:00) Brisbane, East Australia</option>
				<option <?php echo ($settings->DefaultTimeZone == 67) ? 'selected="selected"' : ''; ?> value="67"> (GMT +10:00) Canberra, Melbourne, Sydney, Hobart</option>
				<option <?php echo ($settings->DefaultTimeZone == 68) ? 'selected="selected"' : ''; ?> value="68"> (GMT +10:00) Guam, Port Moresby</option>
				<option <?php echo ($settings->DefaultTimeZone == 69) ? 'selected="selected"' : ''; ?> value="69"> (GMT +10:00) Hobart, Tasmania</option>
				<option <?php echo ($settings->DefaultTimeZone == 70) ? 'selected="selected"' : ''; ?> value="70"> (GMT +10:00) Vladivostok</option>
				<option <?php echo ($settings->DefaultTimeZone == 71) ? 'selected="selected"' : ''; ?> value="71"> (GMT +11:00) Magadan, Solomon Is., New Caledonia</option>
				<option <?php echo ($settings->DefaultTimeZone == 72) ? 'selected="selected"' : ''; ?> value="72"> (GMT +12:00) Auckland, Wellington</option>
				<option <?php echo ($settings->DefaultTimeZone == 73) ? 'selected="selected"' : ''; ?> value="73"> (GMT +12:00) Fiji Islands, Kamchatka, Marshall Is.</option>
				<option <?php echo ($settings->DefaultTimeZone == 74) ? 'selected="selected"' : ''; ?> value="74"> (GMT +13:00) Nuku'alofa, Tonga</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right" >&nbsp;</td>
		<td colspan="2">
			<input type="checkbox" style="vertical-align: middle" onchange="change();" name="intAllowUsersChangeTimeZone" id="intAllowUsersChangeTimeZone" value="1" <?php echo ((int) $settings->AllowUsersChangeTimeZone == 1) ? 'checked="checked"' : '';?> />
			<label for="intAllowUsersChangeTimeZone">Allow users to change time offset</label>
		</td>
	</tr>

	<tr><td colspan="3"><br /></td></tr>
	<tr>
		<td colspan="3" class="wm_admin_title">Password</td>
	</tr>
	<tr><td colspan="3"><br /></td></tr>
	<tr>
		<td align="right">New password: </td>
		<td colspan="2"><input type="password" class="wm_input" onchange="change();" name="txtPassword1" value="***" maxlength="100" /></td>
	</tr>
	<tr>
		<td align="right">Confirm password: </td>
		<td colspan="2"><input type="password" class="wm_input" onchange="change();" name="txtPassword2" value="***" maxlength="100" /></td>
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
		<td colspan="3" align="right">
			<input type="submit" name="save" class="wm_button" value="Save" style="width: 100px; font-weight: bold">&nbsp;
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
