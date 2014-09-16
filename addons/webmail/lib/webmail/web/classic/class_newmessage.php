<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'classic/class_getmessagebase.php');

	class CNewMessagePanel
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
		var $From = '';
		var $To = '';
		var $CC = '';
		var $BCC = '';
		var $Subject = '';
		var $Body = '';
		var $Type;
		var $attacmentsHtml = '';
		var $isSafety = true;
		
		/**
		 * @var string
		 */
		var $inputs;
				
		function _getFromEmail()
		{
			$this->From = ($this->_proc->account->UseFriendlyName && strlen(trim($this->_proc->account->FriendlyName)) > 0)
				? '&quot;'.trim($this->_proc->account->FriendlyName).'&quot; <'.$this->_proc->account->Email.'>'
				: $this->_proc->account->Email;
			return $this->From;
		}
		
		/**
		 * @param PageBuilder $pageBuilder
		 * @return ContactsPanel
		 */
		function CNewMessagePanel(&$pagebuilder)
		{
			$this->Type = Post::val('mtype','mes');
			$this->To = '';
			
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			$this->From = $this->_getFromEmail();
			$this->_pagebuilder->_top->AddOnResize('ResizeElements(\'all\');');
			
			if ($this->_proc->account->AllowDhtmlEditor)
			{
				$editorResize = 'HTMLEditor.Resize(width - 1, height - 2);';
				$editorReplace = 'HTMLEditor.Replace();';
			} else {
				$editorResize = '
						plainEditor.style.height = (height - 1) + "px";
						plainEditor.style.width = (width - 2) + "px";
					';
				$editorReplace = '';
			}
			
			$this->inputs = '';
			$contacts = null;
			if (Post::has('contacts') && is_array(Post::val('contacts')))
			{
				$contactsArray = array_keys(Post::val('contacts'));
				$contacts = &$this->_proc->db->LoadContactsById($contactsArray);
			}
			
			if (Post::has('groupid'))
			{
				$group = &$this->_proc->db->SelectGroupById(Post::val('groupid', -1));
				$contacts = &$this->_proc->db->SelectAddressGroupContacts(Post::val('groupid', -1));
			}
			
			if ($contacts)
			{
				foreach ($contacts->Instance() as $contact)
				{
					if (!$contact->Email) continue;
					
					$this->To .= ($contact->Name)
						? '"'.$contact->Name.'" <'.$contact->Email.'>, '
						: $contact->Email.',';
				}
				$this->To = trim(trim($this->To), ',');	
			}
			
			if (Post::has('mailto'))
			{
				$this->To = Post::val('mailto', '');
			}
			
			if (Get::has('to'))
			{
				$this->To = (string) trim(Get::val('to', ''));
			}
			
			$message = null;
			$isHtml = $this->_proc->account->AllowDhtmlEditor;
			$this->attacmentsHtml = '';

			$this->_pagebuilder->AddJSText('
			
var bcc, bcc_mode, bcc_mode_switcher;

var plainCont = null;
var plainEditor = null;
var HTMLEditor = null;
var EditAreaUrl = "edit-area.php";
var prevWidth = 0;
var prevHeight = 0;
var rowIndex = 0;

function ResizeElements(mode) 
{
	var width = GetWidth();
	if (width < 684)
		width = 684;
	width = width - 40;
	var height = Math.ceil(width/3);
	
	if (prevWidth != width && prevHeight != height) {
		prevWidth = width;
		prevHeight = height;
		if (plainCont != null) {
			plainCont.style.height = height + "px";
			plainCont.style.width = width + "px";
			'.$editorResize.'
		}
	}
}

function WriteEmails(str, field)
{
	var mailInput;
	if (field == 2) {
		mailInput = document.getElementById("toCC");
	} else if (field == 3) {
		mailInput = document.getElementById("toBCC");
	} else {
		mailInput = document.getElementById("toemail");
	}
	if (mailInput) {
		mailInput.value = (mailInput.value == "") ? str : mailInput.value + ", " + str;
		mailInput.focus();
	}
}

function LoadAttachmentHandler(attachObj)
{
	var attachtable = document.getElementById("attachmentTable");
	if (attachObj)
	{
		var imageLink = GetFileParams(attachObj.FileName);
		var tr = attachtable.insertRow(rowIndex++);
		tr.id = "tr_" + attachObj.TempName;
		var td = tr.insertCell(0);
		td.className = "wm_attachment";
		var innerHtml = \'<img src="./images/icons/\' + imageLink.image + \'" />\';
		innerHtml += \'<input type="hidden" name="attachments[\' + attachObj.TempName + \']" value="\' + attachObj.FileName + \'">\';
		innerHtml += HtmlEncode(attachObj.FileName) + \' (\' + GetFriendlySize(attachObj.Size) + \') <a href="#" id="\' + attachObj.TempName + \'" onclick="return  DeleteAttach(this.id);">'.JS_LANG_Delete.'</a>\';
		td.innerHTML = innerHtml;
	}
}

function ChangeBCCMode()
{
	if (bcc_mode == "hide") {
		bcc_mode = "show";
		bcc.className = "";
		bcc_mode_switcher.innerHTML = Lang.HideBCC;
	} else {
		bcc_mode = "hide";
		bcc.className = "wm_hide";
		bcc_mode_switcher.innerHTML = Lang.ShowBCC;
	}
	'.$editorReplace.'
	return false;
}

function UpdateIdUid(id, uid)
{
	var idf = document.getElementById("m_id");
	var uidf = document.getElementById("m_uid");
	if (idf && uidf) {
		idf.value = id;
		uidf.value = uid;
	}
}

var Rep_m, Err_m;
var hiddensaveiframe;
var pop3Pr = '.(($pagebuilder->_proc->account->MailProtocol == MAILPROTOCOL_POP3) ? 'true' : 'false').';
function DoSaveButton()
{
	if (pop3Pr)
	{ 
		if (!hiddensaveiframe) {
			hiddensaveiframe = CreateChildWithAttrs(document.body, "iframe", [["name", "hiddensaveiframe"], ["class", "wm_hide"]]);
		}
	}
	
	var form = document.getElementById("messageForm");
	form.action = "'.ACTIONFILE.'?action=save&req=message";
	form.target = (pop3Pr) ? "hiddensaveiframe" : "";

	if (submitSaveMessage()) {
		form.submit();
	}
}

function DoSendButton()
{
	var toemail = document.getElementById("toemail");
	var ccemail = document.getElementById("toCC");
	var bccemail = document.getElementById("toBCC");
	var subject = document.getElementById("subject");
	var mailIsCorrect = false;
	
	if ((toemail && toemail.value.length > 3) || (ccemail && ccemail.value.length > 3) || (bccemail && bccemail.value.length > 3)) { 
		mailIsCorrect = true;
	}
	
	if (mailIsCorrect) {
		if (subject && subject.value.length < 1 && !confirm(Lang.ConfirmEmptySubject)) {
			return false;
		}
		
		var form = document.getElementById("messageForm");
		form.action = "'.ACTIONFILE.'?action=send&req=message";
		form.target = "";
		if (submitSaveMessage()) {
			form.submit();
		}
	} else {
		alert(Lang.WarningToBlank);
	}
}

function DeleteAttach(idline)
{
	var trtable = document.getElementById("tr_" + idline);
	if (trtable)
	{
		trtable.className = "wm_hide";
		CleanNode(trtable);
	}
	return false;
}

function ShowPictures()
{
	var showPictureTable = document.getElementById("showpicturestable");

	if (HTMLEditor) {
		var temp = HTMLEditor.GetText().ReplaceStr("wmx_src", "src");
		temp = temp.ReplaceStr("wmx_background", "background");
		HTMLEditor.SetHtml(temp);
		if (showPictureTable) {
			showPictureTable.className = "wm_hide";
		}
		HTMLEditor.Replace();
	}
}

');
			$this->_pagebuilder->AddInitText('

bcc_mode = "hide";
bcc = document.getElementById("bcc");
bcc_mode_switcher = document.getElementById("bcc_mode_switcher");

plainEditor = document.getElementById("editor_area");
plainCont = document.getElementById("editor_cont");

Rep_m = new CReport("Rep_m");
Rep_m.Build();

Err_m = new CError("Err_m", "'.ConvertUtils::ClearJavaScriptString($this->_pagebuilder->SkinName(), '"').'");
Err_m.Build();
');
			
			$m_id = -1;
			$m_uid = '';
			if (Post::has('m_id'))
			{
				$mes_id = Post::val('m_id');
				$mes_uid = Post::val('m_uid');
				$folder_id = Post::val('f_id');
				$folder_name = Post::val('f_name'); $folder_name = 'defaultname';
				$mes_charset = Post::val('charset', -1);
				
				$message = new GetMessageBase($this->_proc->account,
												$mes_id,
												$mes_uid,
												$folder_id ,
												$folder_name,
												$mes_charset);

				$m_id = (int) $mes_id;
				$m_uid = $mes_uid;
			}
			
			$this->inputs = '<input type="hidden" id="m_id" name="m_id" value="'.ConvertUtils::AttributeQuote($m_id).'"><input type="hidden" id="m_uid" name="m_uid" value="'.ConvertUtils::AttributeQuote($m_uid).'">';
			
			$withSignature = false;
			switch ($this->_proc->account->SignatureOptions)
			{
				case SIGNATURE_OPTION_AddToAll:
					$withSignature = true;
					break;
				case SIGNATURE_OPTION_AddToNewOnly:
					$withSignature = ($this->Type == 'mes');
					break;
				default:
				case SIGNATURE_OPTION_DontAdd:
					$withSignature = false;
					break;
			}
			
			if ($message)
			{
				if ($this->Type != 'forward' && $this->Type != 'reply' && $this->Type != 'replytoall') 
				{
					$withSignature = false;
				}
				
				$this->_pagebuilder->AddInitText('SetPriority('.$message->msg->GetPriorityStatus().');');

				switch ($this->Type)
				{
					default:
						$this->To = $message->PrintTo(true);
						$this->CC = $message->PrintCC(true);
						$this->BCC = '';
						$this->Subject = $message->PrintSubject(true);						
						break;
					case 'forward': 
						$this->To = '';
						$this->CC = '';
						$this->BCC = '';
						$this->Subject = JS_LANG_Fwd.': '.$message->PrintSubject(true); 
						break;
					case 'reply':
						$replyto = trim($message->PrintReplyTo(true));
						$this->To = (strlen($replyto) > 0) ? $replyto : $message->PrintFrom(true);
						$this->CC = '';
						$this->BCC = '';
						$this->Subject = JS_LANG_Re.': '.$message->PrintSubject(true); 
						break;
					case 'replytoall':
						$emailCollection = &$message->msg->GetAllRecipients(false, true);
						$temp = '';
						if ($emailCollection)
						{
							foreach ($emailCollection->Instance() as $value)
							{
								$email = &$value;
								if ($email->Email != $this->_proc->account->Email)
								{
									$temp .= $email->Email.', ';
								}
							}
						}
						
						$this->To = trim(trim($temp),',');
						$this->CC = '';
						$this->BCC = '';
						$this->Subject = JS_LANG_Re.': '.$message->PrintSubject(true); 
						break;
				}
				
				if ($this->_proc->account->AllowDhtmlEditor)
				{
					switch ($this->Type)
					{
						case 'forward':
						case 'reply':
						case 'replytoall':
							if ($message->account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG || $message->account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE_NO_IMG)
							{
								$isHtml = true;
								$this->Body = ConvertUtils::HtmlBodyWithoutImages($message->msg->GetRelpyAsHtml(true));
								if (isset($GLOBALS[GL_WITHIMG]) && $GLOBALS[GL_WITHIMG])
								{
									$GLOBALS[GL_WITHIMG] = false;
									$this->isSafety	= false;
								}
							}
							else 
							{
								$isHtml = true;
								$this->Body = ConvertUtils::HtmlBodyWithoutImages($message->msg->GetRelpyAsHtml(true));
							}
							break;
							
						default:
							if ($message->account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG || $message->account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE_NO_IMG)
							{
								if ($message->msg->HasHtmlText())
								{
									$isHtml = true;
									$this->Body = ConvertUtils::HtmlBodyWithoutImages($message->msg->GetCensoredHtmlWithImageLinks(true));
									if (isset($GLOBALS[GL_WITHIMG]) && $GLOBALS[GL_WITHIMG])
									{
										$GLOBALS[GL_WITHIMG] = false;
										$this->isSafety	= false;
									}
								} 
								elseif ($message->msg->HasPlainText())
								{
									$isHtml = false;
									$this->Body = $message->msg->GetNotCensoredTextBody(true);
								}					
							}
							else 
							{
								if ($message->msg->HasHtmlText())
								{
									$isHtml = true;
									$this->Body = $message->msg->GetCensoredHtmlWithImageLinks(true);
								} 
								elseif ($message->msg->HasPlainText())
								{
									$isHtml = false;
									$this->Body = $message->msg->GetNotCensoredTextBody(true);
								}								
							}
							break;
					}
				}
				else 
				{
					$isHtml = false;
					switch ($this->Type)
					{
						case 'forward':
						case 'reply':
						case 'replytoall':
							$this->Body = $message->msg->GetRelpyAsPlain(true);
							break;
						default:
							$this->Body = $message->msg->GetNotCensoredTextBody(true);
							break;
					}
				}	
				
				
				if ($message->HasAttachments() && $this->Type != 'reply' && $this->Type != 'replytoall')
				{
					$attachments = &$message->msg->Attachments;
					if ($attachments != null && $attachments->Count() > 0)
					{
						foreach (array_keys($attachments->Instance()) as $key)
						{
							$attachment = &$attachments->Get($key);
							$tempname = $message->msg->IdMsg.'-'.$key.'_'.$attachment->GetTempName();
							//$filename = ConvertUtils::ConvertEncoding($attachment->GetFilenameFromMime(), $GLOBALS[MailInputCharset], $message->account->GetUserCharset());
							$filename = ConvertUtils::WMHtmlSpecialChars($attachment->GetFilenameFromMime());
							$filesize = GetFriendlySize(strlen($attachment->MimePart->GetBinaryBody()));
												
							$fs = &new FileSystem(INI_DIR.'/temp', $message->account->Email, $message->account->Id);
							$attfolder = &new Folder($message->account->Id, -1, Session::val('attachtempdir', md5(session_id())));
							$fs->SaveAttach($attachment, $attfolder, $tempname);
									
							$this->attacmentsHtml .= '
<tr id="tr_'.ConvertUtils::AttributeQuote($tempname).'"><td class="wm_attachment"><img src="./images/icons/'.GetAttachImg($filename).'" />
<input type="hidden" name="attachments['.ConvertUtils::AttributeQuote($tempname).']" value="'.ConvertUtils::AttributeQuote($filename).'"> '.$filename.'
 (' .$filesize.') 						
<a href="#" id="'.ConvertUtils::AttributeQuote($tempname).'" onClick="return  DeleteAttach(this.id);">'.JS_LANG_Delete.'</a></td></tr>';							
							
						}
					}		
				}
			}
			else 
			{
				$this->_pagebuilder->AddInitText('SetPriority(3);');
			}
			
			$signature = '';

			if ($withSignature)
			{
				if ($this->_proc->account->AllowDhtmlEditor)
				{
					$signature = ($this->_proc->account->SignatureType == 0)
							? nl2br($this->_proc->account->Signature)
							: $this->_proc->account->Signature;
					$signature = ($isHtml) ? $signature : strip_tags(nl2br($signature));
				}
				else 
				{
					$signature = ($this->_proc->account->SignatureType == 0) 
							? strip_tags($this->_proc->account->Signature)
							: strip_tags($this->_proc->account->Signature);
				}
			}
			
			$this->Body = $signature . $this->Body;
			
			if ($this->_proc->account->AllowDhtmlEditor)
			{
				$this->_pagebuilder->AddJSFile('class.html-editor.js');
				$setText  = ($isHtml) ? 'HTMLEditor.SetHtml(mess);' : 'HTMLEditor.SetText(mess);';
				$this->_pagebuilder->AddJSText(
				'
		function submitSaveMessage()
		{
			var hiddenkey = document.getElementById("ishtml");
			
			if (HTMLEditor._htmlMode) {
				plainEditor.value = HTMLEditor.GetText();
				hiddenkey.value = "1";
			} else {
				hiddenkey.value = "0";
			}
			if (bcc_mode == "hide")
			{
				document.getElementById("toBCC").value = "";
			}
			return true;
		}
		
		function EditAreaLoadHandler() { HTMLEditor.LoadEditArea();	}
		function CreateLinkHandler(url) { HTMLEditor.CreateLinkFromWindow(url); }
		function DesignModeOnHandler(rer) {
			HTMLEditor.Show();
			var mess = "'.ConvertUtils::ReBuildStringToJavaScript($this->Body, '"').'";
			if (mess.length == 0) {
				mess = "<br />";
			}
			'.$setText.'
		}
				');
				
				$this->_pagebuilder->AddInitText('
		HTMLEditor = new CHtmlEditorField(true);
		HTMLEditor.SetPlainEditor(plainEditor, document.getElementById("mode_switcher"));
		HTMLEditor.Show();'
		);
			}
			else 
			{
				$this->_pagebuilder->AddJSText('
		function submitSaveMessage()
		{
			var hiddenkey = document.getElementById("ishtml");
			hiddenkey.value = "0";
			if (bcc_mode == "hide") {
				document.getElementById("toBCC").value = "";
			}
			return true;
		}
				');
			}
		}
		
		function ToHTML()
		{
			$swicther = ($this->_proc->account->AllowDhtmlEditor) ? '
		<tr>
			<td></td>
			<td class="wm_html_editor_switcher">
				<a href="#" id="mode_switcher">'.JS_LANG_SwitchToPlainMode.'</a>
			</td>
		</tr>' : '';
			
			$senders = (!$this->isSafety) ? 
'<table class="wm_view_message" id="showpicturestable">
	<tr>
		<td class="wm_safety_info">
			<span class="">
				<span>'.PicturesBlocked.' </span>
				<a href="#" onclick="ShowPictures()">'.ShowPictures.'</a>.</span>
			</span>
		</td>
	</tr>
</table>' : '';
			

			$ahref = array('','','');
			$aend = '';
			if ($this->_proc->settings->AllowContacts)
			{
				$ahref[0] = '<a href="#" onclick="PopupContacts(\'contactlist.php?f=1\');">';
				$ahref[1] = '<a href="#" onclick="PopupContacts(\'contactlist.php?f=2\');">';
				$ahref[2] = '<a href="#" onclick="PopupContacts(\'contactlist.php?f=3\');">';
				$aend = '</a>';
			}
			
			return $senders.'
<form action="" method="POST" id="messageForm">
<table class="wm_new_message">
		<tr>
			<td class="wm_new_message_title">'.JS_LANG_From.': </td>
			<td>
				<input class="wm_input" tabindex="1" type="text" size="93" name="from" value="'.ConvertUtils::AttributeQuote($this->From).'" />
				<input type="hidden" name="priority_input" id="priority_input" value="" />
				<input type="hidden" name="ishtml" id="ishtml" value="">
				'.$this->inputs.'
			</td>
		</tr>
		<tr>
			<td class="wm_new_message_title">'.$ahref[0].JS_LANG_To.$aend.': </td>
			<td>
				<input class="wm_input" autocomplete="on" tabindex="2" type="text" size="93" id="toemail" name="toemail" value="'.ConvertUtils::AttributeQuote($this->To).'" />
			</td>
		</tr>
		<tr>
			<td class="wm_new_message_title">'.$ahref[1].JS_LANG_CC.$aend.': </td>
			<td><nobr>
				<input class="wm_input" tabindex="3" type="text" size="93" id="toCC" name="toCC" value="'.ConvertUtils::AttributeQuote($this->CC).'" /><span>&nbsp;</span>
				<a href="#" onClick="ChangeBCCMode(); return false;" id="bcc_mode_switcher">'.JS_LANG_ShowBCC.'</a><nobr>
			</td>
		</tr>
		<tr class="wm_hide" id="bcc">
			<td class="wm_new_message_title">'.$ahref[2].JS_LANG_BCC.$aend.': </td>
			<td>
				<input class="wm_input" tabindex="4" type="text" size="93" name="toBCC" id="toBCC" value="" />
			</td>
		</tr>
		<tr>
			<td class="wm_new_message_title">'.JS_LANG_Subject.': </td>
			<td>
				<input class="wm_input" tabindex="5" type="text" size="93" name="subject" id="subject" value="'.ConvertUtils::AttributeQuote($this->Subject).'" />
			</td>
		</tr>
		<tr id="plain_mess">
			<td colspan="2">
				<div id="editor_cont" class="wm_input" style="width: 684px; height: 330px;">
					<textarea id="editor_area" style="width: 680px; height: 328px; border: 0px; border-left: solid 1px white;" name="message">'.$this->Body.'</textarea>
				</div>
			</td>
		</tr>
		'.$swicther.'
	</table>
	<table class="wm_new_message" id="attachmentTable">
	'.$this->attacmentsHtml.'
	</table>
	</form>
<table class="wm_new_message">
		<tr>
			<td colspan="2" class="wm_attach">
			<iframe class="wm_hide" src="" id="uploadIframe" name="uploadIframe"></iframe>
			<form action="upload.php" target="uploadIframe" method="POST" enctype="multipart/form-data">
				'.JS_LANG_AttachFile.': 
				<input class="wm_file" type="file" name="fileupload" />
				<input class="wm_button" type="submit" name="attachbtn" value="'.ConvertUtils::AttributeQuote(JS_LANG_Attach).'" />
			</form>
			</td>
		</tr>
  	</table>
';
		}
		
	}
