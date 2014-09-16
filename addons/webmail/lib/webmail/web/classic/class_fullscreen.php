<?php

	if (!isset($_POST['m_uid']))
	{
		header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);
		exit();
	}
	
	class FullScreenPanel
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
		 * @param PageBuilder $pageBuilder
		 * @return ContactsPanel
		 */
		function FullScreenPanel(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			$this->_pagebuilder->_top->AddOnResize('ResizeElements(\'all\');');
			
			$this->_pagebuilder->AddJSText('
var sep = "-----";
var logo, accountlist, toolbar, iframe;

function ResizeElements(mode) 
{
	var height = GetHeight();
	var exx = 0; var offsetHeight = 0;
	offsetHeight = (logo) ? logo.offsetHeight : 0; exx += (offsetHeight) ? offsetHeight : 0;
	offsetHeight = (accountlist) ? accountlist.offsetHeight : 0; exx += (offsetHeight) ? offsetHeight : 0;
	offsetHeight = (toolbar) ? toolbar.offsetHeight : 0; exx += (offsetHeight) ? offsetHeight : 0;
	iframe.height = height - exx;		
}

function LoadMessage(newcharset, newplain)
{
	BaseForm.Charset.value = newcharset;
	BaseForm.Plain.value = newplain;
	BaseForm.Form.submit();
}

function DoNewMessageButton()
{
	document.location = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
}

function DoPrintButton()
{
	var src = "print.php?msg_id='.Post::val('m_id', '').'&msg_uid='.urlencode(Post::val('m_uid', '')).'&folder_id='.Post::val('f_id', '').'&folder_fname='.urlencode(Post::val('f_name', '')).'&charset=" + BaseForm.Charset.value;
	var allHeight = GetHeight();
	var allWidth = GetWidth();
	var height = 480; if (height >= allHeight) height = Math.ceil(allHeight*2/3);
	var width = 640; if (width >= allWidth) width = Math.ceil(allWidth*2/3);
	var top = Math.ceil((allHeight - height)/2)
	var left = Math.ceil((allWidth - width)/2)
	var win = window.open(src, "Popup", "toolbar=yes,status=no,scrollbars=yes,resizable=yes,width=" + width + ",height=" + height + ",top=" + top + ",left=" + left);
}

function ChangeCharset(newCharset)
{
	var idline = BaseForm.MessId.value + sep + BaseForm.MessUid.value + sep + BaseForm.FolderId.value + sep + BaseForm.Charset.value + sep;
	var newidline = BaseForm.MessId.value + sep + BaseForm.MessUid.value + sep + BaseForm.FolderId.value + sep + newCharset + sep;
	BaseForm.Charset.value = newCharset;
} 

function DoForwardButton()
{
		BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
		BaseForm.Form.target = "_self";
		BaseForm.Plain.value = "-1";
		BaseForm.Type.value = "forward";
		BaseForm.Form.submit();
}

function DoReplyButton()
{
		BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
		BaseForm.Form.target = "_self";
		BaseForm.Plain.value = "-1";
		BaseForm.Type.value = "reply";
		BaseForm.Form.submit();
}

function DoReplyAllButton()
{
		BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
		BaseForm.Form.target = "_self";
		BaseForm.Plain.value = "-1";
		BaseForm.Type.value = "replytoall";
		BaseForm.Form.submit();
}

function DoSaveButton()
{
	document.location = "attach.php?msg_id='.Post::val('m_id', '').'&msg_uid='.urlencode(Post::val('m_uid', '')).'&folder_id='.Post::val('f_id', '').'&folder_fname='.urlencode(Post::val('f_name', '')).'";
}


function AddContact(contactString)
{
	var obj = GetEmailParts(HtmlDecode(contactString));
	var hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.BASEFILE.'?'.SCREEN.'='.SCREEN_CONTACTS.'&'.CONTACT_MODE.'='.C_NEW.'"], ["target", "_self"], ["method", "POST"]]);
	CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "cdata"], ["value",  "1"]]);
	CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "cemail"], ["value",  obj.Email]]);
	CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "cfullname"], ["value",  obj.Name]]);
	hiddenform.submit();
}


function DoDeleteButton()
{
	if (!confirm("'.ConvertUtils::ClearJavaScriptString(JS_LANG_ConfirmAreYouSure, '"').'")) {
		return false;
	}
	var hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=delete&req=message"], ["target", "_self"], ["method", "POST"]]);
	CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "messageId"], ["value",  "'.ConvertUtils::ClearJavaScriptString(Post::val('m_id', -1), '"').'"]]);
	CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "messageUid"], ["value",  "'.ConvertUtils::ClearJavaScriptString(Post::val('m_uid', ''), '"').'"]]);
	CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "folderId"], ["value",  "'.ConvertUtils::ClearJavaScriptString(Post::val('f_id', -1), '"').'"]]);
	hiddenform.submit();
}

function CBaseForm()
{
	this.Form = document.getElementById("messform");
	this.MessId = document.getElementById("m_id");
	this.MessUid = document.getElementById("m_uid");
	this.FolderId = document.getElementById("f_id");
	this.FolderName = document.getElementById("f_name");
	this.Charset = document.getElementById("charset");
	this.Plain = document.getElementById("plain");
	this.Type = document.getElementById("mtype");
}
');
			$this->_pagebuilder->AddInitText('
BaseForm = new CBaseForm();
logo = document.getElementById("logo");
accountlist = document.getElementById("accountslist");
toolbar = document.getElementById("toolbar");
iframe = document.getElementById("iframe_container");
SetBodyAutoOverflow(false);
ResizeElements("all");
LoadMessage("'.ConvertUtils::ClearJavaScriptString(Post::val('charset', ''), '"').'", "'.Post::val('plain', '-1').'");
			');
		}
		
		function ToHTML()
		{
			return '
	<table id="iftare_table" width="100%">
		<tr>
			<td>
				<iframe name="iframe_container" width="100%" frameborder="0" id="iframe_container"></iframe>
			</td>
		</tr>
	</table>
<form name="messform" id="messform" action="base-iframe.php?mode=full" target="iframe_container" method="POST">
<input type="hidden" name="m_id" id="m_id" value="'.ConvertUtils::AttributeQuote(Post::val('m_id', '')).'" />
<input type="hidden" name="m_uid" id="m_uid" value="'.ConvertUtils::AttributeQuote(Post::val('m_uid', '')).'" />
<input type="hidden" name="f_id" id="f_id" value="'.ConvertUtils::AttributeQuote(Post::val('f_id', '')).'" />
<input type="hidden" name="f_name" id="f_name" value="'.ConvertUtils::AttributeQuote(Post::val('f_name', '')).'" />
<input type="hidden" name="charset" id="charset" value="'.ConvertUtils::AttributeQuote(Post::val('charset', '')).'" />
<input type="hidden" name="plain" id="plain" value="'.ConvertUtils::AttributeQuote(Post::val('plain', '-1')).'" />
<input type="hidden" name="mtype" id="mtype" value="msg" />
</form>

			';
		}
		
	}
