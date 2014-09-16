<?php

function GetBirthDay($d, $m, $y)
{
	$res = '';
	if ($y != 0) {
		$res .= $y;
		if ($d != 0 || $m != 0) $res .= ',';
	}
	if ($d != 0) $res .= ' '.$d;
	switch ($m) {
		case 1: $res .= ' Jan'; break;
		case 2: $res .= ' Feb'; break;
		case 3: $res .= ' Mar'; break;
		case 4: $res .= ' Apr'; break;
		case 5: $res .= ' May'; break;
		case 6: $res .= ' Jun'; break;
		case 7: $res .= ' Jul'; break;
		case 8: $res .= ' Aug'; break;
		case 9: $res .= ' Sep'; break;
		case 10: $res .= ' Oct'; break;
		case 11: $res .= ' Nov'; break;
		case 12: $res .= ' Dec'; break;
	}
	return $res;
}

/**
 * @param PageBuilder $pagebuilder
 * @return string
 */
function WriteContactEdit(&$pagebuilder, $contact_id, $isNew = false)
{
	$out = '';
	$isCorrect = false;
	$pagebuilder->AddJSText('
	
function dolocation(idurl)
{
	var url = document.getElementById(idurl).value;
	if (url.length > 2)	OpenURL(url);
}

function MessageToMail(email)
{
	if (!email) return false;
	var form = CreateChildWithAttrs(document.body, \'form\', [[\'action\', \''.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'\'], [\'method\', \'POST\']]);
	CreateChildWithAttrs(form, \'input\', [[\'type\', \'hidden\'], [\'name\', \'mailto\'], [\'value\', email]]);
	form.submit();
}
	');
	
	if ($isNew)
	{
		$contact = new AddressBookRecord();
		$contact->PrimaryEmail = 0;
		$groupsArray = array();
		if (Post::val('cdata') == 1)
		{
			$contact->FullName = Post::val('cfullname', '');
			$contact->HomeEmail = Post::val('cemail', '');
		}
	}
	else 
	{
		$contact = &$pagebuilder->_proc->db->SelectAddressBookRecord($contact_id);	
		$groupsArray = &$pagebuilder->_proc->db->SelectAddressGroupContact($contact_id);
	}
	$allGroups = &$pagebuilder->_proc->db->SelectUserAddressGroupNames();
	$skinName = $pagebuilder->SkinName();
	
	
	if ($contact && is_object($contact))
	{
		$isCorrect = true;
	}
	
	if ($isCorrect)
	{
		$data = array();
		$data = get_object_vars($contact);
		
		foreach ($data as $key => $value)
		{
			$data[$key] = ($value && strlen($value) > 0) ?
				array('', $value) : array(' class="wm_hide"', '');
		}
		
		$Birthday[0] = ($data['BirthdayDay'][1] || $data['BirthdayMonth'][1] || $data['BirthdayYear'][1]) ? '' : ' class="wm_hide"';
		$Birthday[1] = GetBirthDay($data['BirthdayDay'][1], $data['BirthdayMonth'][1], $data['BirthdayYear'][1]);
	
		$Email = array('', '');
		switch ($contact->PrimaryEmail)
		{
			case PRIMARYEMAIL_Home: $Email[1] = $contact->HomeEmail; break;
			case PRIMARYEMAIL_Business: $Email[1] = $contact->BusinessEmail; break;
			case PRIMARYEMAIL_Other: $Email[1] = $contact->OtherEmail; break;
		}
		$Email[0] = ($Email[1]) ? '' : ' class="wm_hide"';
		
		$class_00 = ($data['HomeEmail'][1] || $data['HomeStreet'][1] || $data['HomeCity'][1] || 
				$data['HomeFax'][1] ||  $data['HomeState'][1] || $data['HomePhone'][1] ||
				$data['HomeZip'][1] ||  $data['HomeMobile'][1] || $data['HomeCountry'][1] || $data['HomeWeb'][1]
				) ? ' class="wm_contacts_view"' : ' class="wm_hide"';
				
		$class_01 = ($data['HomeCity'][1] || $data['HomeFax'][1]) ? '' : ' class="wm_hide"';
		$data['HomeCity'][0] = ($data['HomeCity'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['HomeCity'][2] = ($data['HomeCity'][1] != '') ? '' : ' class="wm_hide"';
		$data['HomeFax'][0] = ($data['HomeFax'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['HomeFax'][2] = ($data['HomeFax'][1] != '') ? '' : ' class="wm_hide"';
		
		$class_02 = ($data['HomeState'][1] || $data['HomePhone'][1]) ? '' : ' class="wm_hide"';
		$data['HomeState'][0] = ($data['HomeState'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['HomeState'][2] = ($data['HomeState'][1] != '') ? '' : ' class="wm_hide"';
		$data['HomePhone'][0] = ($data['HomePhone'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['HomePhone'][2] = ($data['HomePhone'][1] != '') ? '' : ' class="wm_hide"';
		
		$class_03 = ($data['HomeZip'][1] || $data['HomeMobile'][1]) ? '' : ' class="wm_hide"';
		$data['HomeZip'][0] = ($data['HomeZip'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['HomeZip'][2] = ($data['HomeZip'][1] != '') ? '' : ' class="wm_hide"';
		$data['HomeMobile'][0] = ($data['HomeMobile'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['HomeMobile'][2] = ($data['HomeMobile'][1] != '') ? '' : ' class="wm_hide"';
		
		
		$class_10 = ($data['BusinessCompany'][1] || $data['BusinessJobTitle'][1] || $data['BusinessDepartment'][1] || 
				$data['BusinessOffice'][1] ||  $data['BusinessCity'][1] || $data['BusinessFax'][1] ||
				$data['BusinessState'][1] ||  $data['BusinessPhone'][1] || $data['BusinessZip'][1] || $data['BusinessCountry'][1] || 
				$data['BusinessEmail'][1] ||  $data['BusinessStreet'][1] || $data['BusinessWeb'][1]
				) ? ' class="wm_contacts_view"' : ' class="wm_hide"';
				
		$class_11 = ($data['BusinessCompany'][1] || $data['BusinessJobTitle'][1]) ? '' : ' class="wm_hide"';
		$data['BusinessCompany'][0] = ($data['BusinessCompany'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessCompany'][2] = ($data['BusinessCompany'][1] != '') ? '' : ' class="wm_hide"';
		$data['BusinessJobTitle'][0] = ($data['BusinessJobTitle'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessJobTitle'][2] = ($data['BusinessJobTitle'][1] != '') ? '' : ' class="wm_hide"';
		
		$class_12 = ($data['BusinessDepartment'][1] || $data['BusinessOffice'][1]) ? '' : ' class="wm_hide"';
		$data['BusinessDepartment'][0] = ($data['BusinessDepartment'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessDepartment'][2] = ($data['BusinessDepartment'][1] != '') ? '' : ' class="wm_hide"';
		$data['BusinessOffice'][0] = ($data['BusinessOffice'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessOffice'][2] = ($data['BusinessOffice'][1] != '') ? '' : ' class="wm_hide"';
		
		$class_13 = ($data['BusinessCity'][1] || $data['BusinessFax'][1]) ? '' : ' class="wm_hide"';
		$data['BusinessCity'][0] = ($data['BusinessCity'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessCity'][2] = ($data['BusinessCity'][1] != '') ? '' : ' class="wm_hide"';
		$data['BusinessFax'][0] = ($data['BusinessFax'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessFax'][2] = ($data['BusinessFax'][1] != '') ? '' : ' class="wm_hide"';
		
		$class_14 = ($data['BusinessState'][1] || $data['BusinessPhone'][1]) ? '' : ' class="wm_hide"';
		$data['BusinessState'][0] = ($data['BusinessState'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessState'][2] = ($data['BusinessState'][1] != '') ? '' : ' class="wm_hide"';
		$data['BusinessPhone'][0] = ($data['BusinessPhone'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessPhone'][2] = ($data['BusinessPhone'][1] != '') ? '' : ' class="wm_hide"';
		
		$class_15 = ($data['BusinessZip'][1] || $data['BusinessCountry'][1]) ? '' : ' class="wm_hide"';
		$data['BusinessZip'][0] = ($data['BusinessZip'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessZip'][2] = ($data['BusinessZip'][1] != '') ? '' : ' class="wm_hide"';
		$data['BusinessCountry'][0] = ($data['BusinessCountry'][1] != '') ? ' class="wm_contacts_view_title"' : ' class="wm_hide"';
		$data['BusinessCountry'][2] = ($data['BusinessCountry'][1] != '') ? '' : ' class="wm_hide"';
		
		$class_20 = ($data['OtherEmail'][1] || $data['Notes'][1]) ? ' class="wm_contacts_view"' : ' class="wm_hide"';
		
		$daysSelect = '';
		for ($i = 1; $i < 32; $i++)
		{
			$daysSelect .= ($data['BirthdayDay'][1] == $i) ?
				'<option value="'.$i.'" selected="selected">'.$i.'</option>'."\r\n" :
				'<option value="'.$i.'">'.$i.'</option>'."\r\n";
		}
		
		$selectedArray = array('','','','','','','','','','','','','','');									
		if (isset($data['BirthdayMonth'][1]) && $data['BirthdayMonth'][1] > 0 && $data['BirthdayMonth'][1] <13)
		{
			$selectedArray[(int) $data['BirthdayMonth'][1]] = ' selected="selected"';
		}
		$monthsSelect = '<option value="1"'.$selectedArray[1].'>'.JS_LANG_January.'</option>
						<option value="2"'.$selectedArray[2].'>'.JS_LANG_February.'</option>
						<option value="3"'.$selectedArray[3].'>'.JS_LANG_March.'</option>
						<option value="4"'.$selectedArray[4].'>'.JS_LANG_April.'</option>
						<option value="5"'.$selectedArray[5].'>'.JS_LANG_May.'</option>
						<option value="6"'.$selectedArray[6].'>'.JS_LANG_June.'</option>
						<option value="7"'.$selectedArray[7].'>'.JS_LANG_July.'</option>
						<option value="8"'.$selectedArray[8].'>'.JS_LANG_August.'</option>
						<option value="9"'.$selectedArray[9].'>'.JS_LANG_September.'</option>
						<option value="10"'.$selectedArray[10].'>'.JS_LANG_October.'</option>
						<option value="11"'.$selectedArray[11].'>'.JS_LANG_November.'</option>
						<option value="12"'.$selectedArray[12].'>'.JS_LANG_December.'</option>';
		
		$yearsSelect = '';
		$lastyear = ((int) date('Y', time()) > 1900) ? (int) date('Y', time()) : 2007;
		for ($i = $lastyear; $i > 1899; $i--)
		{
			$yearsSelect .= ($data['BirthdayYear'][1] == $i) ?
				'<option value="'.$i.'" selected="selected">'.$i.'</option>'."\r\n" :
				'<option value="'.$i.'">'.$i.'</option>'."\r\n";
		}
		
		$groupsHtml = '';
		
		if ($groupsArray && count($groupsArray) > 0)
		{
			$groupsHtml = '<table class="wm_contacts_view">
									<tr>
									<td class="wm_contacts_view_title wm_contacts_section_name">'.JS_LANG_Groups.':</td>';
			$k = 0;
			$cnt = count($groupsArray);
			foreach ($groupsArray as $key => $value)
			{
				$k++;
				$tempstr = ($cnt > $k) ? ',' : '';
				$groupsHtml .= '<td class="wm_contacts_groups"><a href="'.BASEFILE.'?'.CONTACT_MODE.'='.G_VIEW.'&'.CONTACT_ID.'='.$key.'">'.$value.'</a>'.$tempstr.'</td>'."\r\n";
			}
			
			$groupsHtml .= '</tr></table>';
		}
		
		
		$groupsHtml2 = '';
		
		if (count($allGroups) > 0)
		{
			$groupsHtml2 = '
							<table class="wm_contacts_tab" onclick="ChangeTabVisibility(\'street_addresses\');">
									<tr>
										<td>
											<span class="wm_contacts_tab_name">
												'.JS_LANG_Groups.'
											</span>
											<span class="wm_contacts_tab_mode">
												<img id="button_street_addresses" src="skins/'.$skinName.'/menu/arrow_down.gif" />
											</span>
										</td>
									</tr>
								</table>
								<table class="wm_hide" id="street_addresses">
									<tr><td>';
			
			foreach ($allGroups as $id => $name)
			{
				$inGroup = key_exists($id, $groupsArray);
				$inGroup = ($inGroup) ? 'checked="checked"' : '';
				$groupsHtml2 .= '<input id="inp_g_'.$id.'" class="wm_checkbox" '.$inGroup.' type="checkbox" value="'.$id.'" name="groupsIds[]"/>
					<label for="inp_g_'.$id.'">'.$name.'</label><br />';
			}			
			
			$groupsHtml2 .= '
									</td></tr>
								</table>';
		}
		
		$useFrName = ($contact->UseFriendlyName) ? ' checked="checked"' : '';
		$isNewHidden = ($isNew) ? '1' : '0';						
									
		$out .= '<form action="'.ACTIONFILE.'?action=update&req=contact" method="POST">
							<input type="hidden" name="isNewContact" value="'.$isNewHidden.'" />
							<input type="hidden" name="contactId" value="'.$contact_id.'" />
							<div id="viewTbl">
								<table class="wm_contacts_view">
									<tr'.$data['FullName'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_Name.':</td>
										<td class="wm_contacts_name">'.ConvertUtils::WMHtmlSpecialChars($data['FullName'][1]).'</td>
									</tr>
									<tr'.$Email[0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_Email.':</td>
										<td class="wm_contacts_email"><a href="#" onclick="return MessageToMail(\''.ConvertUtils::WMHtmlSpecialChars($Email[1]).'\')">'.ConvertUtils::WMHtmlSpecialChars($Email[1]).'</a></td>
									</tr>
									<tr'.$Birthday[0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_Birthday.':</td>
										<td>'.$Birthday[1].'</td>
									</tr>
								</table>
								<table'.$class_00.'>
									<tr>
										<td class="wm_contacts_section_name" colspan="4">'.JS_LANG_Home.'</td>
									</tr>
									<tr'.$data['HomeEmail'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_PersonalEmail.':</td>
										<td colspan="3">
											<a href="#" onclick="return MessageToMail(\''.ConvertUtils::WMHtmlSpecialChars($data['HomeEmail'][1]).'\')">'.ConvertUtils::WMHtmlSpecialChars($data['HomeEmail'][1]).'</a>
										</td>
									</tr>
									<tr'.$data['HomeStreet'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_StreetAddress.':</td>
										<td colspan="3">'.ConvertUtils::WMHtmlSpecialChars($data['HomeStreet'][1]).'</td>
									</tr>
									<tr'.$class_01.'>
										<td'.$data['HomeCity'][0].'>'.JS_LANG_City.':</td>
										<td'.$data['HomeCity'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['HomeCity'][1]).'</td>
										<td'.$data['HomeFax'][0].'>'.JS_LANG_Fax.':</td>
										<td'.$data['HomeFax'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['HomeFax'][1]).'</td>
									</tr>
									<tr'.$class_02.'>
										<td'.$data['HomeState'][0].'>'.JS_LANG_StateProvince.':</td>
										<td'.$data['HomeState'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['HomeState'][1]).'</td>
										<td'.$data['HomePhone'][0].'>'.JS_LANG_Phone.':</td>
										<td'.$data['HomePhone'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['HomePhone'][1]).'</td>
									</tr>
									<tr'.$class_03.'>
										<td'.$data['HomeZip'][0].'>'.JS_LANG_ZipCode.':</td>
										<td'.$data['HomeZip'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['HomeZip'][1]).'</td>
										<td'.$data['HomeMobile'][0].'>'.JS_LANG_Mobile.':</td>
										<td'.$data['HomeMobile'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['HomeMobile'][1]).'</td>
									</tr>
									<tr'.$data['HomeCountry'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_CountryRegion.':</td>
										<td colspan="3">
											'.ConvertUtils::WMHtmlSpecialChars($data['HomeCountry'][1]).'
										</td>
									</tr>
									<tr'.$data['HomeWeb'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_WebPage.':</td>
										<td colspan="3">
											'.ConvertUtils::WMHtmlSpecialChars($data['HomeWeb'][1]).'
										</td>
									</tr>
								</table>
								<table'.$class_10.'>
									<tr>
										<td class="wm_contacts_section_name" colspan="4">'.JS_LANG_Business.'</td>
									</tr>
									<tr'.$data['BusinessEmail'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_BusinessEmail.':</td>
										<td colspan="3">
											<a href="#" onclick="return MessageToMail(\''.ConvertUtils::WMHtmlSpecialChars($data['BusinessEmail'][1]).'\')">'.ConvertUtils::WMHtmlSpecialChars($data['BusinessEmail'][1]).'</a>
										</td>
									</tr>
									<tr'.$class_11.'>
										<td'.$data['BusinessCompany'][0].'>'.JS_LANG_Company.':</td>
										<td'.$data['BusinessCompany'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessCompany'][1]).'</td>
										<td'.$data['BusinessJobTitle'][0].'">'.JS_LANG_JobTitle.':</td>
										<td'.$data['BusinessJobTitle'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessJobTitle'][1]).'</td>
									</tr>
									<tr'.$class_12.'>
										<td'.$data['BusinessDepartment'][0].'>'.JS_LANG_Department.':</td>
										<td'.$data['BusinessDepartment'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessDepartment'][1]).'</td>
										<td'.$data['BusinessOffice'][0].'>'.JS_LANG_Office.':</td>
										<td'.$data['BusinessOffice'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessOffice'][1]).'</td>
									</tr>
									<tr'.$data['BusinessStreet'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_StreetAddress.':</td>
										<td colspan="3">'.ConvertUtils::WMHtmlSpecialChars($data['BusinessStreet'][1]).'</td>
									</tr>
									<tr'.$class_13.'>
										<td'.$data['BusinessCity'][0].'>'.JS_LANG_City.':</td>
										<td'.$data['BusinessCity'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessCity'][1]).'</td>
										<td'.$data['BusinessFax'][0].'>'.JS_LANG_Fax.':</td>
										<td'.$data['BusinessFax'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessFax'][1]).'</td>
									</tr>
									<tr'.$class_14.'>
										<td'.$data['BusinessState'][0].'>'.JS_LANG_StateProvince.':</td>
										<td'.$data['BusinessState'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessState'][1]).'</td>
										<td'.$data['BusinessPhone'][0].'>'.JS_LANG_Phone.':</td>
										<td'.$data['BusinessPhone'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessPhone'][1]).'</td>
									</tr>
									<tr'.$class_15.'>
										<td'.$data['BusinessZip'][0].'>'.JS_LANG_ZipCode.':</td>
										<td'.$data['BusinessZip'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessZip'][1]).'</td>
										<td'.$data['BusinessCountry'][0].'>'.JS_LANG_CountryRegion.':</td>
										<td'.$data['BusinessCountry'][2].'>'.ConvertUtils::WMHtmlSpecialChars($data['BusinessCountry'][1]).'</td>
									</tr>
									<tr'.$data['BusinessWeb'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_WebPage.':</td>
										<td colspan="3">
											'.ConvertUtils::WMHtmlSpecialChars($data['BusinessWeb'][1]).'
										</td>
									</tr>
								</table>
								<table'.$class_20.'>
									<tr>
										<td class="wm_contacts_section_name" colspan="2">'.JS_LANG_Other.'</td>
									</tr>
									<tr'.$data['OtherEmail'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_OtherEmail.':</td>
										<td><a href="#" onclick="return MessageToMail(\''.ConvertUtils::WMHtmlSpecialChars($data['OtherEmail'][1]).'\')">'.ConvertUtils::WMHtmlSpecialChars($data['OtherEmail'][1]).'</a></td>
									</tr>
									<tr'.$data['Notes'][0].'>
										<td class="wm_contacts_view_title">'.JS_LANG_Notes.':</td>
										<td>'.ConvertUtils::WMHtmlSpecialChars($data['Notes'][1]).'</td>
									</tr>
								</table>
								'.$groupsHtml.'
								<table class="wm_contacts_view">
									<tr>
										<td>
											<a href="#" id="switch_to_edit">'.JS_LANG_EditContact.'</a>
										</td>
									</tr>
								</table>
							</div>
							
	<!---->
							<div id="editTbl" class="wm_hide">
								<table class="wm_contacts_view">
									<tr>
										<td class="wm_contacts_view_title" style="width: 25%;">'.JS_LANG_DefaultEmail.':</td>
										<td style="width: 75%;">
											<span id="notSpecified" class="wm_hide">'.JS_LANG_NotSpecifiedYet.'</span>
											<select id="select_default_email" class="wm_hide" style="width: 200px;"></select>
											<input id="input_default_email" name="input_default_email" type="text" value="'.dequote($Email[1]).'" class="wm_input" maxlength="255"/>
											<input id="default_email_type" type="hidden" name="default_email_type" value="'.dequote($contact->PrimaryEmail).'" />
										</td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_Name.':</td>
										<td><input class="wm_input" type="text" name="c_fullname" value="'.dequote($data['FullName'][1]).'" maxlength="85" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_Birthday.':</td>
										<td>
											<select name="birthday_month">
												<option value="0">'.JS_LANG_Month.'</option>
												'.$monthsSelect.'
											</select>
											<select name="birthday_day">
												<option value="0">'.JS_LANG_Day.'</option>
												'.$daysSelect.'
											</select>
											<select name="birthday_year">
												<option value="0">'.JS_LANG_Year.'</option>
												'.$yearsSelect.'
											</select>
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input class="wm_checkbox" type="checkbox" name="use_friendly_name" id="use_friendly_name" value="1"'.$useFrName.'>
											<label for="use_friendly_name">'.JS_LANG_UseFriendlyName.'</label>
										</td>
									</tr>
								</table>
								<div class="wm_hide" id="more_info_div">
								<table class="wm_contacts_view" style="width: 94%; margin: 0px 15px 2px 15px;">
									<tr>
										<td style="text-align: right; border-top: solid 1px #8D8C89;">
											<a href="" id="more_info_hide">'.JS_LANG_HideAddFields.'</a>
										</td>
									</tr>
								</table>
								<table class="wm_contacts_tab" onclick="ChangeTabVisibility(\'access\');">
									<tr>
										<td>
											<span class="wm_contacts_tab_name">
												'.JS_LANG_Home.'
											</span>
											<span class="wm_contacts_tab_mode">
												<img id="button_access" src="skins/'.$skinName.'/menu/arrow_up.gif">
											</span>
										</td>
									</tr>
								</table>
								<table class="wm_contacts_view" id="access">
									<tr>
										<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_PersonalEmail.':</td>
										<td style="width: 80%;" colspan="4"><input class="wm_input" type="text" size="45" id="personal_email" name="personal_email" value="'.dequote($data['HomeEmail'][1]).'" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_StreetAddress.':</td>
										<td colspan="4"><textarea class="wm_input" rows="2" cols="35" name="personal_street" rows="4">'.$data['HomeStreet'][1].'</textarea></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_City.':</td>
										<td style="width: 30%;"><input class="wm_input" type="text" size="18" name="personal_city" value="'.dequote($data['HomeCity'][1]).'" maxlength="65" /></td>
										<td style="width: 10%;"></td>
										<td class="wm_contacts_view_title" style="width: 10%;">'.JS_LANG_Fax.':</td>
										<td style="width: 30%;"><input class="wm_input" type="text" size="18" name="personal_fax" value="'.dequote($data['HomeFax'][1]).'" maxlength="50"/></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_StateProvince.':</td>
										<td><input class="wm_input" type="text" size="18" name="personal_state" value="'.dequote($data['HomeState'][1]).'" maxlength="65" /></td>
										<td></td>
										<td class="wm_contacts_view_title">'.JS_LANG_Phone.':</td>
										<td><input class="wm_input" type="text" size="18" name="personal_phone" value="'.dequote($data['HomePhone'][1]).'" maxlength="50" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_ZipCode.':</td>
										<td><input class="wm_input" type="text" size="18" name="personal_zip" value="'.dequote($data['HomeZip'][1]).'" maxlength="10" /></td>
										<td></td>
										<td class="wm_contacts_view_title">'.JS_LANG_Mobile.':</td>
										<td><input class="wm_input" type="text" size="18" name="personal_mobile" value="'.dequote($data['HomeMobile'][1]).'" maxlength="50" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_CountryRegion.':</td>
										<td colspan="4"><input class="wm_input" type="text" size="18" name="personal_country" value="'.dequote($data['HomeCountry'][1]).'" maxlength="65" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_WebPage.':</td>
										<td colspan="4">
											<input class="wm_input" type="text" size="45" name="personal_web" id="personal_web" value="'.dequote($data['HomeWeb'][1]).'" maxlength="255" />
											<input class="wm_button" type="button" value="'.JS_LANG_Go.'" onClick="dolocation(\'personal_web\');" />
										</td>
									</tr>
								</table>
								<table class="wm_contacts_tab" onclick="ChangeTabVisibility(\'online_addresses\');">
									<tr>
										<td>
											<span class="wm_contacts_tab_name">
												'.JS_LANG_Business.'
											</span>
											<span class="wm_contacts_tab_mode">
												<img id="button_online_addresses" src="skins/'.$skinName.'/menu/arrow_down.gif">
											</span>
										</td>
									</tr>
								</table>
								<table class="wm_hide" id="online_addresses">
									<tr>
										<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_BusinessEmail.':</td>
										<td style="width: 80%;" colspan="4"><input class="wm_input" type="text" id="business_email" name="business_email" size="45" value="'.dequote($data['BusinessEmail'][1]).'" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_Company.':</td>
										<td style="width: 30%;"><input class="wm_input" type="text" size="18" name="business_company" value="'.dequote($data['BusinessCompany'][1]).'" maxlength="65" /></td>
										<td style="width: 5%;"></td>
										<td class="wm_contacts_view_title" style="width: 15%;">'.JS_LANG_JobTitle.':</td>
										<td style="width: 30%;"><input class="wm_input" type="text" size="18" name="business_job" value="'.dequote($data['BusinessJobTitle'][1]).'" maxlength="30" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_Department.':</td>
										<td><input class="wm_input" type="text" size="18" name="business_departament" value="'.dequote($data['BusinessDepartment'][1]).'" maxlength="65" /></td>
										<td></td>
										<td class="wm_contacts_view_title">'.JS_LANG_Office.':</td>
										<td><input class="wm_input" type="text" size="18" name="business_office" value="'.dequote($data['BusinessOffice'][1]).'" maxlength="65" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_StreetAddress.':</td>
										<td colspan="4"><textarea rows="2" class="wm_input" cols="35" name="business_street" rows="4">'.dequote($data['BusinessStreet'][1]).'</textarea></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_City.':</td>
										<td><input class="wm_input" type="text" size="18" name="business_city" value="'.dequote($data['BusinessCity'][1]).'" maxlength="65" /></td>
										<td></td>
										<td class="wm_contacts_view_title">'.JS_LANG_Fax.':</td>
										<td><input class="wm_input" type="text" size="18" name="business_fax" value="'.dequote($data['BusinessFax'][1]).'" maxlength="50" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_StateProvince.':</td>
										<td><input class="wm_input" type="text" size="18" name="business_state" value="'.dequote($data['BusinessState'][1]).'" maxlength="65" /></td>
										<td></td>
										<td class="wm_contacts_view_title">'.JS_LANG_Phone.':</td>
										<td><input class="wm_input" type="text" size="18" name="business_phone" value="'.dequote($data['BusinessPhone'][1]).'" maxlength="50" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_ZipCode.':</td>
										<td><input class="wm_input" type="text" size="18" name="business_zip" value="'.dequote($data['BusinessZip'][1]).'" maxlength="10" /></td>
										<td></td>
										<td class="wm_contacts_view_title">'.JS_LANG_CountryRegion.':</td>
										<td colspan="4"><input class="wm_input" type="text" name="business_country" size="18" value="'.dequote($data['BusinessCountry'][1]).'" maxlength="65" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_WebPage.':</td>
										<td colspan="4">
											<input class="wm_input" type="text" size="45" id="business_web" name="business_web" value="'.dequote($data['BusinessWeb'][1]).'" maxlength="255" />
											<input class="wm_button" type="button" value="'.JS_LANG_Go.'" onClick="dolocation(\'business_web\');" />
										</td>
									</tr>
								</table>
								<table class="wm_contacts_tab" onclick="ChangeTabVisibility(\'phone_numbers\');">
									<tr>
										<td>
											<span class="wm_contacts_tab_name">
												'.JS_LANG_Other.'
											</span>
											<span class="wm_contacts_tab_mode">
												<img id="button_phone_numbers" src="skins/'.$skinName.'/menu/arrow_down.gif">
											</span>
										</td>
									</tr>
								</table>
								<table class="wm_hide" id="phone_numbers">
									<tr>
										<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_OtherEmail.':</td>
										<td style="width: 80%;"><input class="wm_input" id="other_email" name="other_email" type="text" size="45" value="'.dequote($data['OtherEmail'][1]).'" maxlength="255" /></td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_Notes.':</td>
										<td><textarea rows="2" class="wm_input" cols="35" rows="4" name="other_notes">'.$data['Notes'][1].'</textarea></td>
									</tr>
								</table>
										'.$groupsHtml2.'
								</div>
								<table class="wm_contacts_view" style="width: 94%; margin: 0px 15px 2px 15px;">
									<tr>
										<td style="text-align: right;">
											<a href="" id="more_info_show">'.JS_LANG_ShowAddFields.'</a>
										</td>
									</tr>
									<tr>
										<td style="text-align: right; border-top: solid 1px #8D8C89;">
											<input type="submit" class="wm_button" value="'.JS_LANG_Save.'" />
										</td>
									</tr>
								</table>
							</div></form>';
	}
	else 
	{
		$out = '';
	}
	
	return $out;
	
}

?>