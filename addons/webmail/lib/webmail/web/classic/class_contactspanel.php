<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	class ContactsPanel
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var 
		 */
		var $text = '';
		
		/**
		 * @var ContactsList
		 */
		var $contactList;
		
		/**
		 * @var ContactsViewer
		 */
		var $contactViewer;
		
		/**
		 * @var string
		 */
		var $footText = '';
		
		/**
		 * @param PageBuilder $pageBuilder
		 * @return ContactsPanel
		 */
		function ContactsPanel(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			
			$this->_pagebuilder->AddJSFile('./classic/base.contactsmain.js');
			
			$this->_pagebuilder->_top->AddOnResize('ResizeElements(\'all\');');

			$this->_pagebuilder->AddJSText('
function ViewAdressRecord(idstring)
{
	var record = ParseCId(idstring);
	var cmode = (record.type == "c") ? "'.C_VIEW.'" : "'.G_VIEW.'";
	document.location = "'.BASEFILE.'?'.CONTACT_MODE.'=" + cmode + "&'.CONTACT_ID.'=" + record.cid;
}
');
			
			$this->contactList = &new ContactsList($this->_pagebuilder);
			$this->contactViewer = &new ContactsViewer($this->_pagebuilder);
			
			$this->footText = '';
			if (isset($GLOBALS['contactCount']) && isset($GLOBALS['contactCount'][0]))
			{
				$this->footText .= ((int) $GLOBALS['contactCount'][0]).'&nbsp;'.JS_LANG_ContactsCount;
				//$this->footText .= ' / ';
				//$this->footText .= ((int) $GLOBALS['contactCount'][1]).'&nbsp;'.JS_LANG_GroupsCount;
			}
			
			$this->text .= 	'<div class="wm_contacts" id="main_contacts">'.
							$this->contactList->ToHTML().
							$this->contactViewer->ToHTML().
							'	
		<div id="lowtoolbar" class="wm_lowtoolbar">
		    <span class="wm_lowtoolbar_messages">
		        '.$this->footText.'
		    </span>
		</div>';
			
		}
		
		/**
		 * @return string
		 */
		function ToHTML()
		{
			return $this->text;
		}
	}
	
	class ContactsList
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var string
		 */
		var $text;
		
		/**
		 * @param PageBuilder $pagebuilder
		 * @return ContactsList
		 */
		function ContactsList(&$pagebuilder)
		{
				
			if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
			require_once(WM_ROOTPATH.'class_contacts.php');
			
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;	
			
			$this->_pagebuilder->AddJSFile('./classic/base.cpageswitcher.js');
			
			$this->_pagebuilder->AddJSText('
	var selection;
			
	function MailGroup(id)
	{
		var form = CreateChildWithAttrs(document.body, "form", [["method", "POST"]]);
		form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
		CreateChildWithAttrs(form, "input", [["type", "hidden"], ["name", "groupid"], ["value", id]]);
		form.submit();
	}
	
	function DoDeleteButton()
	{
		var i, hideinput, count;
		var obj = GetSelectedData();
		count = obj.contacts.length + obj.groups.length;
		
		if (count && count > 0) {
			if (!confirm(Lang.ConfirmAreYouSure)) {
				return false;
			}
			var form = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=delete&req=contacts"], ["method", "POST"]]);
			count = obj.groups.length;
			for(i = 0; i < count; i++) {
				hideinput = CreateChildWithAttrs(form, "input", [["type", "hidden"]]);
				hideinput.name = "groups[" + obj.groups[i] + "]";
			}
			
			count = obj.contacts.length;
			for(i = 0; i < count; i++) {
				hideinput = CreateChildWithAttrs(form, "input", [["type", "hidden"]]);
				hideinput.name = "contacts[" + obj.contacts[i] + "]";
			}
			form.submit();
		}
	}
	
	function AddContactsToGroup(groupid, groupname)
	{
		var obj = GetSelectedData();
		count = obj.contacts.length;
		if (count && count > 0) {
			var form = CreateChild(document.body, "form");
			form.action = "'.ACTIONFILE.'?action=move&req=contacts";
			form.method = "POST";
			hideinput = CreateChildWithAttrs(form, "input", [["type", "hidden"]]);
			hideinput.name = "groupId";
			hideinput.value = groupid;		
			hideinput2 = CreateChildWithAttrs(form, "input", [["type", "hidden"]]);
			hideinput2.name = "groupName";
			hideinput2.value = groupname;	
			for(i = 0; i < count; i++) {
				hideinput = CreateChildWithAttrs(form, "input", [["type", "hidden"]]);
				hideinput.name = "contacts[" + obj.contacts[i] + "]";
			}
			form.submit();
		}
	}
	
	function DoNewMessageButton(contactId)
	{
		if (contactId) {
			var cid = ParseCId(contactId);
			if (cid && cid.type == "c") {
				var form = CreateChild(document.body, "form");
				form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
				form.method = "POST";
				hideinput = CreateChildWithAttrs(form, "input", [["type", "hidden"]]);
				hideinput.name = "contacts[" + cid.cid + "]";
				form.submit();
			}
			
			return true;
		} else {
			var obj = GetSelectedData(true);
			count = obj.contacts.length;
			if (count && count > 0) {
				var form = CreateChild(document.body, "form");
				form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
				form.method = "POST";
				for(i = 0; i < count; i++) {
					hideinput = CreateChildWithAttrs(form, "input", [["type", "hidden"]]);
					hideinput.name = "contacts[" + obj.contacts[i] + "]";
				}
				form.submit();
			} else {
				document.location = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
			}
		}
	}
	
	function GetSelectedData(param)
	{
		if (!param) {
			param = false;
		}
		
		var groups = Array();
		var contacts = Array();
		var ch_array = selection.GetCheckedLines();
		if (!ch_array) return false;
		var count = ch_array.length;
		if (count < 1 && !param) {
			alert(Lang.AlertNoContactsGroupsSelected);
		}
		
		for (var i = 0; i < count; i++) {
			var record = ParseCId(ch_array[i]);
			if (record && record.type == "g") {
				groups.push(record.cid);
			} else {
				contacts.push(record.cid);
			}
		}
		return {groups: groups, contacts: contacts}
	}
			');

			$pageNumber = isset($this->_proc->sArray[CONTACT_PAGE]) ? $this->_proc->sArray[CONTACT_PAGE] : 1;
		
			$sortField = isset($this->_proc->sArray[CONTACT_FLD]) ? $this->_proc->sArray[CONTACT_FLD] : 1;
			$sortOrder = isset($this->_proc->sArray[CONTACT_ORD]) ? $this->_proc->sArray[CONTACT_ORD] : 1;
			$backOrder = (int) ! (bool) $sortOrder;
			
			if (isset($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) && strlen($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) > 0)
			{
				$GLOBALS['contactCount'] = $this->_proc->db->SelectAddressContactsAndGroupsCount(0, $this->_proc->account->IdUser, $this->_proc->sArray[SEARCH_ARRAY][S_TEXT], $this->_proc->sArray[CONTACT_ID]);
				$contacts = &$this->_proc->db->SearchContactsAndGroups($pageNumber, $this->_proc->sArray[SEARCH_ARRAY][S_TEXT], $this->_proc->sArray[CONTACT_ID], $sortField, $sortOrder, 0);
				if ($contacts == null) SetOnlineError(getGlobalError());
			}
			else 
			{
				$GLOBALS['contactCount'] = $this->_proc->db->SelectAddressContactsAndGroupsCount(0, $this->_proc->account->IdUser);
				$contacts = &$this->_proc->db->LoadContactsAndGroups($pageNumber, $sortField, $sortOrder);	
				if ($contacts == null) SetOnlineError(getGlobalError());
			}
			
			if (!$contacts || $contacts->Count() < 1)
			{
				if ($pageNumber != 1)
				{
					$pageNumber = floor(($GLOBALS['contactCount'][0] + $GLOBALS['contactCount'][1])/$this->_proc->account->ContactsPerPage);
					$pageNumber = ($pageNumber < 1) ? 1 : $pageNumber;
					$contacts = $this->_proc->db->LoadContactsAndGroups($pageNumber, $sortField, $sortOrder);
				}
			}
			
			$this->_pagebuilder->AddInitText('
PageSwitcher = new CPageSwitcher("'.$this->_pagebuilder->SkinName().'");
PageSwitcher.Build();
PageSwitcher.Show('.$pageNumber.', '.$this->_proc->account->ContactsPerPage.', '.($GLOBALS['contactCount'][0] + $GLOBALS['contactCount'][1]).', "document.location = \'?'.CONTACT_PAGE.'=", "\';");
CContactsList = new CContactsList();
			');
			
			$imgArray = array('&nbsp;&nbsp;&nbsp;&nbsp;','','');
			$imgArray[$sortField] = ($sortOrder == 0) ? 
					' <img src="./skins/'.$this->_pagebuilder->SkinName().'/menu/order_arrow_down.gif">' :
					' <img src="./skins/'.$this->_pagebuilder->SkinName().'/menu/order_arrow_up.gif">';

			$this->text = '
		
		<div id="contacts" class="wm_contacts_list">
			<div class = "wm_contact_list_div" id="contact_list_div">		
			<div class="wm_inbox_headers" id="contact_list_headers">
				<div style="left: 0px; width: 22px;" ><input type="checkbox" id="allcheck"></div>
				<div style="left: 23px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>
				
				<div class="wm_control" style="left: 25px; width: 22px;" 
					onclick="document.location=\''.BASEFILE.'?'.CONTACT_ORD.'='.$backOrder.'&'.CONTACT_FLD.'=0\'" >
					'.$imgArray[0].'
				</div>
				<div style="left: 48px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>
				
				<div style="left: 50px; width: 138px;" class="wm_inbox_headers_from_subject wm_control"
				onclick="document.location=\''.BASEFILE.'?'.CONTACT_ORD.'='.$backOrder.'&'.CONTACT_FLD.'=1\'" >
					'.JS_LANG_Name.' '.$imgArray[1].'
				</div>
				
				<div style="left: 188px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>
				
				<div style="left: 190px; width: 138px;" class="wm_inbox_headers_from_subject wm_control"
				onclick="document.location=\''.BASEFILE.'?'.CONTACT_ORD.'='.$backOrder.'&'.CONTACT_FLD.'=2\'" >
					'.JS_LANG_Email.' '.$imgArray[2].'
				</div>
			</div>
			<div class="wm_inbox_lines">
			<table id="list" style="width: 100%; text-align: center;">';

			if ($contacts != null && $contacts->Count() > 0)
			{
				foreach (array_keys($contacts->Instance()) as $key)
				{
					$contact = &$contacts->Get($key);
					$temp = ($contact->IsGroup) ? '<img src="skins/'.$this->_pagebuilder->SkinName().'/contacts/group.gif" />' : '&nbsp;';
					$recordType = ($contact->IsGroup) ? 'g' : 'c';

					if (isset($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) && strlen($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) > 0)
                    {
						$cname = preg_replace('/'.preg_quote($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]).'/i',
									'<font>$0</font>',
									ConvertUtils::WMHtmlSpecialChars($contact->Name));
						$cemail = preg_replace('/'.preg_quote($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]).'/i',
									'<font>$0</font>',
									ConvertUtils::WMHtmlSpecialChars($contact->Email));
					}
					else
					{
						$cname = ConvertUtils::WMHtmlSpecialChars($contact->Name);
						$cemail = ConvertUtils::WMHtmlSpecialChars($contact->Email);
					}
					
					$this->text .= '<tr class="wm_inbox_read_item" id="'.$recordType.'_'.$contact->Id.'">';
					$this->text .= '<td style="width: 22px; text-align: center;" id="none"><input type="checkbox"></td><td style="width: 24px; text-align: center;">'.$temp.'</td>';
					$this->text .= '<td class="wm_inbox_from_subject" style="width: 140px;"><nobr>'.$cname.'</nobr></td>';
					$this->text .= '<td class="wm_inbox_from_subject" style="width: 330px;"><nobr>'.$cemail.'</nobr></td></tr>';
				}
			}
			else
			{
				$this->text .= '<div class="wm_inbox_info_message" id="list">'.InfoNoContactsGroups.'<br /><div class="wm_view_message_info">'.InfoNewContactsGroups.'</div></div>';
			}

			$this->text .= '</table></div>
								</div>
							</div>';
		}
		
		function ToHTML()
		{
			return $this->text;
		}
	}
	
	class ContactsViewer
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var string
		 */
		var $text;
		
		/**
		 * @param PageBuilder $pagebuilder
		 * @return ContactsList
		 */
		function ContactsViewer(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;	
			
			$this->_pagebuilder->AddInitText('
selection = new CContactsSelection();
selection.FillContacts();
');	
			$this->text = '';
			switch (Get::val(CONTACT_MODE, C_NONE))
			{
				case C_IMPORT:
	
					$this->_pagebuilder->AddJSText('

function ImportContactsHandler(code, count) {
	switch (code) {
		case 0:
			InfoPanel.SetInfo(Lang.ErrorImportContacts);
			InfoPanel.Class("wm_error_information");
			InfoPanel.Show();
			InfoPanel.Resize();
			break;
		case 1: 
			document.location = "'.BASEFILE.'";
			break;
		case 2:
			InfoPanel.SetInfo(Lang.ErrorNoContacts);
			InfoPanel.Class("wm_error_information");
			InfoPanel.Show();
			InfoPanel.Resize();
			break;
		case 3:
			InfoPanel.SetInfo(Lang.ErrorInvalidCSV);
			InfoPanel.Class("wm_error_information");
			InfoPanel.Show();
			InfoPanel.Resize();
			break;
	}
}

function submitForm()
{
	var app1 = document.getElementById("app1");
	var app2 = document.getElementById("app2");
	var fileField = document.getElementById("fileField");
	
	if (app1 && app2 && app1.checked == false && app2.checked == false) {
		alert(Lang.WarningImportFileType);
		return false;
	}
		
	if (fileField) {
		if (Trim(fileField.value).length < 1) {
			alert(Lang.WarningEmptyImportFile);
			return false;		
		} else {
			if (GetExtension(fileField.value) != "csv") {
				alert(Lang.WarningCsvExtention);
				return false;
			}			
		}
	}
	return true;
}
');
					$this->text .= '	
		<div class="wm_contacts_view_edit" id="contacts_viewer">			
			<div id="contacts_viewer_div">
				<table class="wm_contacts_card" id="wm_contacts_card">
					<tr>
						<td class="wm_contacts_card_top_left"><div class="wm_contacts_card_corner"></div></td>
						<td class="wm_contacts_card_top"></td>
						<td class="wm_contacts_card_top_right"><div class="wm_contacts_card_corner"></div></td>
					</tr>
					<tr>
						<td class="wm_contacts_card_left"></td>
						<td>';
				
						$this->text .= '<table class="wm_contacts_view">
								<tr>
									<td><b>'.JS_LANG_UseImportTo.'</b></td>
								</tr>
								<tr>
									<td>
										<input type="radio" class="wm_checkbox" name="app" id="app1" value="1" />
										<label for="app1">'.JS_LANG_Outlook1.'</label><br />
										<input type="radio" class="wm_checkbox" name="app" id="app2" value="2" />
										<label for="app2">'.JS_LANG_Outlook2.'</label><br />
									</td>
								</tr>
								<tr>
									<td>
										'.JS_LANG_SelectImportFile.':
									</td>
								</tr>
								<tr>
									<td>
										<form target="importIframe" enctype="multipart/form-data" method="post" action="import.php" onsubmit="return submitForm()">
										<iframe class="wm_hide" name="importIframe" id="importIframe"></iframe>
										<input class="wm_file" type="file" name="fileupload" size="30" value="" id="fileField" />
									</td>
								</tr>
							</table>
							<table class="wm_contacts_view" style="width: 90%;">
								<tr>
									<td style="text-align: right; border-top: solid 1px #8D8C89;">
										<input type="submit" class="wm_button" value="'.ConvertUtils::AttributeQuote(JS_LANG_Import).'" />
										</form>
									</td>
								</tr>
							</table>';			
					
					$this->text .= '</td>
						<td class="wm_contacts_card_right"></td>
					</tr>
					<tr>
						<td class="wm_contacts_card_bottom_left"><div class="wm_contacts_card_corner"></div></td>
						<td class="wm_contacts_card_bottom"></td>
						<td class="wm_contacts_card_bottom_right"><div class="wm_contacts_card_corner"></div></td>
					</tr>
				</table>

			</div>
		</div>
	</div>';	
					
					break;
				
				case G_NEW:
					$this->_pagebuilder->AddInitText('
OrgTab = document.getElementById("orgTab");
OrgTabImg = document.getElementById("orgTabImg");
OrgTable = document.getElementById("orgTable");
OrgCheckBox = document.getElementById("isorganization");
OrgDiv = document.getElementById("orgDiv");

ShowHideOrgDiv();
ShowHideOrgForm();
					');
					
					$this->_pagebuilder->AddJSText('
					var OrgTab, OrgTable, OrgCheckBox, OrgDiv;
					var isOrg = false;
					
					function ShowHideOrgDiv() 
					{
						if (!OrgDiv || !OrgCheckBox) {
							return false;
						}
						OrgDiv.className = (OrgCheckBox.checked == true) ? "" : "wm_hide";
						ResizeElements("all");
					}
					
					function dolocation(idurl)
					{
						var url = document.getElementById(idurl);
						if (url && url.value.length > 2) {
							OpenURL(url.value);
						}
					}
					
					function ShowHideOrgForm()
					{
						if (isOrg) {
							OrgTable.className = "wm_hide";
							OrgTabImg.src="skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_down.gif";
						} else {
							OrgTable.className = "wm_contacts_view";
							OrgTabImg.src="skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_up.gif";
						}
						
						isOrg = !isOrg;
						ResizeElements("all");
					}
					
					function submitForm()
					{
						var groupnameField = document.getElementById("groupname");
						if (groupnameField && Trim(groupnameField.value).length < 1) {
							alert(Lang.WarningGroupNotComplete);
							return false;		
						}	
						return true;
					}
					');
					
					
					$this->text .= '	
		<div class="wm_contacts_view_edit" id= "contacts_viewer">
			<div id="contacts_viewer_div">
				<table class="wm_contacts_card" id="wm_contacts_card">
					<tr>
						<td class="wm_contacts_card_top_left"><div class="wm_contacts_card_corner"></div></td>
						<td class="wm_contacts_card_top"></td>
						<td class="wm_contacts_card_top_right"><div class="wm_contacts_card_corner"></div></td>
					</tr>
					<tr>
						<td class="wm_contacts_card_left"></td>
						<td>';
					
					$this->text .= '
							<form action="'.ACTIONFILE.'?action=new&req=group" method="POST" onsubmit="return submitForm()">		
							<table class="wm_contacts_view">
								<tr>
									<td>'.JS_LANG_GroupName.':</td>
									<td>
										<input type="text" name="groupname" id="groupname" class="wm_input wm_group_name_input" maxlength="85"	/>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<input type="checkbox" class="wm_checkbox" name="isorganization" id="isorganization" class="wm_input" onclick="ShowHideOrgDiv()"/>
										<label for="isorganization">'.TreatAsOrganization.'</label>
									</td>
								</tr>
							</table>

							<div id="orgDiv">
							<table class="wm_contacts_tab" style="margin-top: 20px;" id="orgTab">
								<tr onclick="ShowHideOrgForm()">
									<td>
										<span class="wm_contacts_tab_name">'.Organization.'</span>
										<span class="wm_contacts_tab_mode">
											<img id="orgTabImg" src="skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_up.gif"/>
										</span>
									</td>
								</tr>
							</table>
														
							<table class="wm_contacts_view" id="orgTable">
								<tr>
									<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_Email.':</td>
									<td style="width: 80%;" colspan="4">
										<input class="wm_input" type="text" maxlength="255" size="45" name="gemail"/>
									</td>
								</tr>
								<tr>
									<td class="wm_contacts_view_title">'.JS_LANG_Company.':</td>
									<td colspan="4">
										<input class="wm_input" type="text" maxlength="65" size="18" name="gcompany"/>
									</td>
								</tr>
								<tr>
									<td class="wm_contacts_view_title">'.JS_LANG_StreetAddress.':</td>
									<td colspan="4">
										<textarea class="wm_input" rows="2" cols="35" name="gstreet"></textarea>
									</td>
								</tr>
								<tr>
									<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_City.':</td>
									<td style="width: 30%;">
										<input class="wm_input" type="text" maxlength="65" size="18" name="gcity"/>
									</td>
									<td style="width: 5%;"></td>
									<td class="wm_contacts_view_title" style="width: 15%;">'.JS_LANG_Fax.':</td>
									<td style="width: 30%;">
										<input class="wm_input" type="text" maxlength="50" size="18" name="gfax"/>
									</td>
								</tr>
								<tr>
									<td class="wm_contacts_view_title">'.JS_LANG_StateProvince.':</td>
									<td>
										<input class="wm_input" type="text" maxlength="65" size="18" name="gstate"/>
									</td>
									<td></td>		
									<td class="wm_contacts_view_title">'.JS_LANG_Phone.':</td>
									<td>
										<input class="wm_input" type="text" maxlength="50" size="18" name="gphone"/>
									</td>
								</tr>
								<tr>
									<td class="wm_contacts_view_title">'.JS_LANG_ZipCode.':</td>
									<td>
										<input class="wm_input" type="text" maxlength="10" size="18" name="gzip"/>
									</td>
									<td></td>
									<td class="wm_contacts_view_title">'.JS_LANG_CountryRegion.':</td>
									<td>
										<input class="wm_input" type="text" maxlength="65" size="18" name="gcountry"/>
									</td>
								</tr>
								<tr>
									<td class="wm_contacts_view_title">'.JS_LANG_WebPage.':</td>
									<td colspan="4">
										<input class="wm_input" type="text" maxlength="255" size="45" name="gweb" id="gweb"/>
										<input class="wm_button" type="button" value="'.ConvertUtils::AttributeQuote(JS_LANG_Go).'" onclick="dolocation(\'gweb\');"/>
									</td>
								</tr>
							</table>						
							</div>			
							
							<table class="wm_contacts_view wm_add_contacts">
								<tr>
									<td>
										'.JS_LANG_AddContacts.':
									</td>
								</tr>
								<tr>
									<td class="wm_secondary_info">
										<textarea name="contactsEmails" rows="2" style="width: 100%; height: 70px;"></textarea>
										'.JS_LANG_CommentAddContacts.'
									</td>
								</tr>
							</table>
							<table class="wm_contacts_view" style="width: 90%;">
								<tr>
									<td class="wm_contacts_save_button">
										<input type="submit" class="wm_button" value="'.ConvertUtils::AttributeQuote(JS_LANG_CreateGroup).'" />
									</td>
								</tr>
							</table>
							
							</form>
						';
					
					$this->text .= '</td>
						<td class="wm_contacts_card_right"></td>
					</tr>
					<tr>
						<td class="wm_contacts_card_bottom_left"><div class="wm_contacts_card_corner"></div></td>
						<td class="wm_contacts_card_bottom"></td>
						<td class="wm_contacts_card_bottom_right"><div class="wm_contacts_card_corner"></div></td>
					</tr>
				</table>

			</div>
		</div>
	</div>';	
					
					break;
				case G_VIEW:
					$this->_pagebuilder->AddInitText('
OrgTab = document.getElementById("orgTab");
OrgTabImg = document.getElementById("orgTabImg");
OrgTable = document.getElementById("orgTable");
OrgCheckBox = document.getElementById("isorganization");
OrgDiv = document.getElementById("orgDiv");

ShowHideOrgDiv();
ShowHideOrgForm();
					');
					
					$this->_pagebuilder->AddJSText('
					var OrgTab, OrgTable, OrgCheckBox, OrgDiv;
					var isOrg = false;
					
					function ShowHideOrgDiv()
					{
						if (!OrgDiv || !OrgCheckBox) {
							return false;
						}
						OrgDiv.className = (OrgCheckBox.checked == true) ? "" : "wm_hide";
						ResizeElements("all");
					}
					
					function ShowHideOrgForm()
					{
						if (isOrg) {
							OrgTable.className = "wm_hide";
							OrgTabImg.src="skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_down.gif";
						} else {
							OrgTable.className = "wm_contacts_view";
							OrgTabImg.src="skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_up.gif";
						}
						
						isOrg = !isOrg;
						ResizeElements("all");
					}
					
					');
					
					$contact_id = Get::val(CONTACT_ID, -1);
					if ($contact_id > -1) 
					{
						$group = &$this->_proc->db->SelectGroupById($contact_id);
						
						$groupname = $group->Name;
						$contacts = &$this->_proc->db->SelectAddressGroupContacts($contact_id);
						
						if ($group && $contacts)
						{
							$this->_pagebuilder->AddJSText('
							
	function dolocation(idurl)
	{
		var url = document.getElementById(idurl);
		if (url && url.value.length > 2) {
			OpenURL(url.value);
		}
	}
					
	function RenameGroup()
	{
		var gcontrol = document.getElementById("control_1");
		var gspan = document.getElementById("span_gname");
		var ginput = document.getElementById("editor_1");
		
		gspan.className = "wm_hide";
		gcontrol.className = "wm_hide";
		ginput.value = gspan.innerHTML;
		ginput.className = "wm_input";
		
		ginput.onkeydown = function(ev)
		{
			if (isEnter(ev)) {
				var g_control = document.getElementById("control_1");
				var g_span = document.getElementById("span_gname");
				var g_input = document.getElementById("editor_1");
		
				g_span.innerHTML = ginput.value;
				g_input.className = "wm_hide";
				g_control.className = "";
				g_span.className = "";
				return false;
			}	
			
		}
		ginput.focus();
	}			
	
	function SelectAllInputs(obj)
	{
		var table = document.getElementById("contacts_in_group");
		if (table)
		{
			var inputs = table.getElementsByTagName("input");
			var i, c;
			for (i = 0, c = inputs.length; i < c; i++) {
				if (inputs[i].type == "checkbox" && !inputs[i].disabled) {
					inputs[i].checked = obj.checked;
				}
			}
		}
		return false;
	}
	
	function DeleteContactsFromGroup()
	{
		var inputs = document.getElementsByTagName("input");
		var i, c, t = 0;
		for (i = 0, c = inputs.length; i < c; i++) {
			if (inputs[i].type == "checkbox" && inputs[i].name == "cont_check[]") { 
				t++;
				if  (inputs[i].checked) {
					t--;
					var tr = document.getElementById("in_group_" + inputs[i].value);
					var imp = document.getElementById("inp_" + inputs[i].value);
					if (tr) tr.className = "wm_hide";
					if (imp) imp.value = "-1";
				}
			}
		}
		if (t == 0) {
			var table = document.getElementById("contacts_in_group");
			if (table) table.className = "wm_hide";
			var mail_group = document.getElementById("mail_group");
			if (mail_group) mail_group.className = "wm_hide";		
			
		}
	}
						');						
							$this->_pagebuilder->AddInitText('selection.CheckLine("g_'.$contact_id.'");');
						
							$isOrg = ($group->IsOrganization) ? 'checked="checked"' : '';
							
							$html_2 = '';
							if ($contacts && $contacts->Count() > 0)
							{
								$html_2 = '<table id="contacts_in_group" class="wm_inbox_lines" style="margin: 20px 20px 0px 20px; width: 300px;">
									<tr class="wm_inbox_read_item" id="in_group_0">
										<td>
											<input type="checkbox" class="wm_checkbox" onclick="SelectAllInputs(this);" id="CheckAll" name="CheckAll"/>
										</td>
										<td>			
											'.JS_LANG_Name.'
										</td>
										<td>
											'.JS_LANG_Email.'
										</td>
									</tr>';
								
								foreach (array_keys($contacts->Instance()) as $key)
								{
									$contact = &$contacts->Get($key);
									$html_2 .= '
									<tr class="wm_inbox_read_item" id="in_group_'.$contact->Id.'">
										<td>
											<input type="checkbox" class="wm_checkbox" id="ch_'.$contact->Id.'" name="cont_check[]" value="'.$contact->Id.'" />
											<input type="hidden" id="inp_'.$contact->Id.'" value="'.$contact->Id.'" name="contactsIds[]" />
										</td>
										<td class="wm_inbox_from_subject">'.ConvertUtils::WMHtmlSpecialChars($contact->Name).'</td>
										<td class="wm_inbox_from_subject">'.ConvertUtils::WMHtmlSpecialChars($contact->Email).'</td>
									</tr>';
								}
								
								$html_2 .= '<tr id="mail_group">
													<td colspan="2">
														<a href="#" onclick="MailGroup('.$contact_id.')">'.JS_LANG_MailGroup.'</a>
													</td>
													<td style="text-align: right;">
														<a href="#" onclick="DeleteContactsFromGroup();">'.JS_LANG_RemoveFromGroup.'</a>
													</td>
												</tr>
											</table>';
							}
							
							
							$this->text .= '			
			<div class="wm_contacts_view_edit" id= "contacts_viewer">
				<div id="contacts_viewer_div">
					<form action="'.ACTIONFILE.'?action=update&req=group" method="POST">
					<table class="wm_contacts_card" id="wm_contacts_card">
						<tr>
							<td class="wm_contacts_card_top_left"><div class="wm_contacts_card_corner"></div></td>
							<td class="wm_contacts_card_top"></td>
							<td class="wm_contacts_card_top_right"><div class="wm_contacts_card_corner"></div></td>
						</tr>
						<tr>
							<td class="wm_contacts_card_left"></td>
							<td>';
							
							$this->text .= '
								<table class="wm_contacts_view">
									<tr>
										<td>'.JS_LANG_GroupName.':</td>
										<td class="wm_contacts_name">
											<input type="hidden" name="gid" value="'.$contact_id.'">
											<span id="span_gname">'.$groupname.'</span>
											<a id="control_1" href="#" onclick="return RenameGroup();">'.JS_LANG_Rename.'</a>
											<input style="width: 240px;" id="editor_1" type="text" value="'.ConvertUtils::AttributeQuote($groupname).'" class="wm_hide" name="gname" />
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="checkbox" class="wm_checkbox" name="isorganization" id="isorganization" class="wm_input" onclick="ShowHideOrgDiv()" '.$isOrg.'/>
											<label for="isorganization">'.TreatAsOrganization.'</label>
										</td>
									</tr>									
																
								</table>
								
								<div id="orgDiv">
								<table class="wm_contacts_tab" style="margin-top: 20px;" id="orgTab">
									<tr onclick="ShowHideOrgForm()">
										<td>
											<span class="wm_contacts_tab_name">'.Organization.'</span>
											<span class="wm_contacts_tab_mode">
												<img id="orgTabImg" src="skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_up.gif"/>
											</span>
										</td>
									</tr>
								</table>
															
								<table class="wm_contacts_view" id="orgTable">
									<tr>
										<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_Email.':</td>
										<td style="width: 80%;" colspan="4">
											<input class="wm_input" type="text" maxlength="255" size="45" name="gemail" value="'.ConvertUtils::AttributeQuote($group->Email).'"/>
										</td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_Company.':</td>
										<td colspan="4">
											<input class="wm_input" type="text" maxlength="65" size="18" name="gcompany" value="'.ConvertUtils::AttributeQuote($group->Company).'"/>
										</td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_StreetAddress.':</td>
										<td colspan="4">
											<textarea class="wm_input" rows="2" cols="35" name="gstreet">'.ConvertUtils::WMHtmlSpecialChars($group->Street).'</textarea>
										</td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title" style="width: 20%;">'.JS_LANG_City.':</td>
										<td style="width: 30%;">
											<input class="wm_input" type="text" maxlength="65" size="18" name="gcity" value="'.ConvertUtils::AttributeQuote($group->City).'"/>
										</td>
										<td style="width: 5%;"></td>
										<td class="wm_contacts_view_title" style="width: 15%;">'.JS_LANG_Fax.':</td>
										<td style="width: 30%;">
											<input class="wm_input" type="text" maxlength="50" size="18" name="gfax" value="'.ConvertUtils::AttributeQuote($group->Fax).'"/>
										</td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_StateProvince.':</td>
										<td>
											<input class="wm_input" type="text" maxlength="65" size="18" name="gstate" value="'.ConvertUtils::AttributeQuote($group->State).'"/>
										</td>
										<td></td>		
										<td class="wm_contacts_view_title">'.JS_LANG_Phone.':</td>
										<td>
											<input class="wm_input" type="text" maxlength="50" size="18" name="gphone" value="'.ConvertUtils::AttributeQuote($group->Phone).'"/>
										</td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_ZipCode.':</td>
										<td>
											<input class="wm_input" type="text" maxlength="10" size="18" name="gzip" value="'.ConvertUtils::AttributeQuote($group->Zip).'"/>
										</td>
										<td></td>
										<td class="wm_contacts_view_title">'.JS_LANG_CountryRegion.':</td>
										<td>
											<input class="wm_input" type="text" maxlength="65" size="18" name="gcountry" value="'.ConvertUtils::AttributeQuote($group->Country).'"/>
										</td>
									</tr>
									<tr>
										<td class="wm_contacts_view_title">'.JS_LANG_WebPage.':</td>
										<td colspan="4">
											<input class="wm_input" type="text" maxlength="255" size="45" name="gweb" id="gweb" value="'.ConvertUtils::AttributeQuote($group->Web).'"/>
											<input class="wm_button" type="button" value="'.ConvertUtils::AttributeQuote(JS_LANG_Go).'" onclick="dolocation(\'gweb\');"/>
										</td>
									</tr>
								</table>						
								</div>										
								
									'.$html_2.'	

								<table class="wm_contacts_view wm_add_contacts" style="width: 300px;">
									<tr>
										<td>
											'.JS_LANG_AddContacts.':
										</td>
									</tr>
									<tr>
										<td colspan="2" class="wm_secondary_info">
											<textarea rows="2" style="width: 100%; height: 70px;" name="emails"></textarea>
											'.JS_LANG_CommentAddContacts.'
										</td>
									</tr>
								</table>
								<table class="wm_contacts_view" style="width: 90%;">
									<tr>
										<td class="wm_contacts_save_button">
											<input type="submit" class="wm_button" value="'.ConvertUtils::AttributeQuote(JS_LANG_Save).'" />
										</td>
									</tr>
								</table>
								
								
								</form>';
							
							$this->text .= '
							</td>
							<td class="wm_contacts_card_right"></td>	
						</tr>
						<tr>
							<td class="wm_contacts_card_bottom_left"><div class="wm_contacts_card_corner"></div></td>
							<td class="wm_contacts_card_bottom"></td>
							<td class="wm_contacts_card_bottom_right"><div class="wm_contacts_card_corner"></div></td>
						</tr>
					</table>
	
				</div>
			</div>
		</div>';								
						}
						else 
						{
							$this->text = '<div class="wm_contacts_view_edit" id="contacts_viewer"><div id="contacts_viewer_div"></div></div>';
						}
					}
				
					break;
				
				case C_VIEW:
					
					require_once(WM_ROOTPATH.'classic/base_contactedit.php');
					
					$contact_id = Get::val(CONTACT_ID, -1);
					$text = WriteContactEdit($this->_pagebuilder, $contact_id);
					if ($text)
					{
						$this->_pagebuilder->AddJSFile('./classic/base.cnewcontactscreen.js');
						$this->_pagebuilder->AddInitText('
						selection.CheckLine("c_'.$contact_id.'");
						
						newContact = new CNewContactScreenPart(0);
						newContact.InitEditContacts(isOpenContact);
						');
						
						$this->_pagebuilder->AddJSText('

			function ChangeTabVisibility(tab_name)
			{
				var tab = document.getElementById(tab_name);
				if (!tab) {
					return false;
				}
				if(tab.className == "wm_contacts_view") {
					tab.className = "wm_hide";
					document.getElementById("button_" + tab_name).src = "skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_down.gif";
				}
				else
				{
					tab.className = "wm_contacts_view";
					document.getElementById("button_" + tab_name).src = "skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_up.gif";
				}
				ResizeElements("all");
			}
	');
							$this->text .= '			
			<div class="wm_contacts_view_edit" id= "contacts_viewer">
				<div id="contacts_viewer_div">
					<table class="wm_contacts_card" id="wm_contacts_card">
						<tr>
							<td class="wm_contacts_card_top_left"><div class="wm_contacts_card_corner"></div></td>
							<td class="wm_contacts_card_top"></td>
							<td class="wm_contacts_card_top_right"><div class="wm_contacts_card_corner"></div></td>
						</tr>
						<tr>
							<td class="wm_contacts_card_left"></td>
							<td>';
	
							$this->text .= $text;
							
							$this->text .= '</td>
							<td class="wm_contacts_card_right"></td>
						</tr>
						<tr>
							<td class="wm_contacts_card_bottom_left"><div class="wm_contacts_card_corner"></div></td>
							<td class="wm_contacts_card_bottom"></td>
							<td class="wm_contacts_card_bottom_right"><div class="wm_contacts_card_corner"></div></td>
						</tr>
					</table>
	
				</div>
			</div>
		</div>';	
						}
						else 
						{
							$this->text = '<div class="wm_contacts_view_edit" id="contacts_viewer"><div id="contacts_viewer_div"></div></div></div>';		
						}
					break;
					
				case C_NEW:
					
					require_once(WM_ROOTPATH.'classic/base_contactedit.php');
					
					$this->_pagebuilder->AddJSFile('./classic/base.cnewcontactscreen.js');
					$this->_pagebuilder->AddInitText('
				newContact = new CNewContactScreenPart(1);
				newContact.InitEditContacts();
					');
					
					$this->_pagebuilder->AddJSText('

		function ChangeTabVisibility(tab_name)
		{
			var tab = document.getElementById(tab_name);
			if (!tab) {
				return false;
			}
			if(tab.className == "wm_contacts_view")
			{
				tab.className = "wm_hide";
				document.getElementById("button_" + tab_name).src = "skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_down.gif";
			}
			else
			{
				tab.className = \'wm_contacts_view\';
				document.getElementById("button_" + tab_name).src = "skins/'.$this->_pagebuilder->SkinName().'/menu/arrow_up.gif";
			}
			ResizeElements("all");
		}
');
					$contact_id = -1;
					$this->text .= '			
			<div class="wm_contacts_view_edit" id="contacts_viewer">
				<div id="contacts_viewer_div">
					<table class="wm_contacts_card" id="wm_contacts_card">
						<tr>
							<td class="wm_contacts_card_top_left"><div class="wm_contacts_card_corner"></div></td>
							<td class="wm_contacts_card_top"></td>
							<td class="wm_contacts_card_top_right"><div class="wm_contacts_card_corner"></div></td>
						</tr>
						<tr>
							<td class="wm_contacts_card_left"></td>
							<td>';
	
							$this->text .= WriteContactEdit($this->_pagebuilder, $contact_id, true);
							
							$this->text .= '</td>
							<td class="wm_contacts_card_right"></td>
						</tr>
						<tr>
							<td class="wm_contacts_card_bottom_left"><div class="wm_contacts_card_corner"></div></td>
							<td class="wm_contacts_card_bottom"></td>
							<td class="wm_contacts_card_bottom_right"><div class="wm_contacts_card_corner"></div></td>
						</tr>
					</table>
				</div>
			</div>
		</div>';	

					break;
				
				default:
				case C_NONE:
						$this->text = '<div class="wm_contacts_view_edit" id="contacts_viewer"><div id="contacts_viewer_div"></div></div></div>';		
					break;				
			}
		}

		/**
		 * @return string
		 */
		function ToHTML()
		{
			return $this->text;			
		}
		
	}
