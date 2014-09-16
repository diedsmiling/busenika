/*
 * Classes:
 *  CAccounts
 *  CMessage
 *  COperationMessages
 *  CMessageHeaders
 *  CMessages
 *  CFolder
 *  CFoldersList
 *  CSignature
 *  CUpdate
 * 
 *  CContact
 *  CContacts
 *  CGroups
 *  CGroup
 * 
 *  CSettings
 *  CAccountProperties
 *  CFilters
 *  CFilterProperties
 *  CXSpam
 *  CContactsSettings
 */

function CAccounts()
{
	this.Type = TYPE_ACCOUNTS_LIST;
	this.LastId = null;
	this.CurrId = null;
	this.Items = [];
	this.Count = 0;
}

CAccounts.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	HasAccount: function (id)
	{
		for (var i = this.Items.length - 1; i >= 0; i--) {
			if (this.Items[i].Id == id)
				return true;
		};
		return false;
	},

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('last_id');
		if (attr) this.LastId = attr - 0;
		attr = RootElement.getAttribute('curr_id');
		if (attr) this.CurrId = attr - 0;
		if (this.LastId == null || this.LastId == -1 ) this.LastId = this.CurrId;
		var AccountsParts = RootElement.childNodes;
		for (var i=0; i<AccountsParts.length; i++) {
			if (AccountsParts[i].tagName == 'account') {
				var id = -1;
				attr = AccountsParts[i].getAttribute('id');
				if (attr) id = attr - 0;
				var mailProtocol = 0;
				attr = AccountsParts[i].getAttribute('mail_protocol');
				if (attr) mailProtocol = attr - 0;
				var defOrder = 0;
				attr = AccountsParts[i].getAttribute('def_order');
				if (attr) defOrder = attr - 0;
				var useFriendlyNm = false;
				attr = AccountsParts[i].getAttribute('use_friendly_nm');
				if (attr) useFriendlyNm = (attr == 1) ? true : false;
				var defAcct = false;
				attr = AccountsParts[i].getAttribute('def_acct');
				if (attr) defAcct = (attr == 1) ? true : false;
				var email = ''; var friendlyName = '';
				var childs = AccountsParts[i].childNodes;
				var jCount = childs.length;
				if (jCount == 1) email = Trim(childs[0].nodeValue);
				for (var j=0; j<jCount; j++) {
					var part = childs[j].childNodes;
					if (part.length > 0) {
						switch (childs[j].tagName) {
							case 'email':
								email = Trim(part[0].nodeValue);
							break;
							case 'friendly_name':
								friendlyName = Trim(part[0].nodeValue);
							break;
						}
					}
				};
				this.Items.push({Id: id, Email: email, FriendlyName: friendlyName, UseFriendlyNm: useFriendlyNm, MailProtocol: mailProtocol, DefOrder: defOrder, DefAcct: defAcct});
			}//if
		};//for
		this.Count = this.Items.length;
	}//GetFromXML
};

// for message
function CMessage()
{
	this.Type = TYPE_MESSAGE;
	this.Parts = 0;
	//	0 - Common Headers
	//	1 - HtmlBody
	//	2 - PlainBody
	//	3 - FullHeaders
	//	4 - Attachments
	//	5 - ReplyHtml;
	//	6 - ReplyPlain;
	//	7 - ForwardHtml;
	//	8 - ForwardPlain;
	this.FolderId = -1;
	this.FolderFullName = '';

	this.Id = -1;
	this.Uid = '';
	this.HasHtml = true;
	this.HasPlain = false;
	this.IsReplyHtml = false;
	this.IsReplyPlain = false;
	this.IsForwardHtml = false;
	this.IsForwardPlain = false;
	this.Importance = 3;
	this.Charset = AUTOSELECT_CHARSET;
	this.HasCharset = true;
	this.Safety = 1;
	
	// Common Headers
	this.FromAddr = '';
	this.ToAddr = '';
	this.CCAddr = '';
	this.BCCAddr = '';
	this.SendersGroups = Array();//for auto-filling To, CC, BCC fields
	this.ReplyToAddr = '';//if it's equal with from, set empty value
	this.Subject = '';
	this.Date = '';

	// Body
	this.HtmlBody = '';
	this.PlainBody = '';
	this.ClearPlainBody = '';

	// Body for reply
	this.ReplyHtml = '';
	this.ReplyPlain = '';

	// Body for forward
	this.ForwardHtml = '';
	this.ForwardPlain = '';

	// FullHeaders
	this.FullHeaders = '';
	
	// Attachments - array of objects with fields FileName, Size[, Id, Download, View] (for getting) [, TempName, MimeType] (for sending)
	this.Attachments = [];
	
	this.SaveLink = '#';
	this.PrintLink = '#';
}	

CMessage.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Id, this.Charset, this.Uid, this.FolderId, this.FolderFullName ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetFromIdForList: function(_SEPARATOR, id)
	{
		var identifiers = id.split(_SEPARATOR);
		this.Id = identifiers[0];
		this.Uid = identifiers[1];
		this.FolderId = identifiers[2];
		this.FolderFullName = identifiers[3];
		this.Charset = identifiers[4];
	},

	GetIdForList: function(_SEPARATOR, id)
	{
		var identifiers = [this.Id, this.Uid, this.FolderId, this.FolderFullName, this.Charset, id];
		return identifiers.join(_SEPARATOR);
	},

	PrepareForEditing: function (msg)
	{
		this.Safety = msg.Safety;
		this.FolderId = msg.FolderId;
		this.FolderFullName = msg.FolderFullName;

		this.Id = msg.Id;
		this.Uid = HtmlDecode(msg.Uid);
		this.HasHtml = msg.HasHtml;
		this.HasPlain = msg.HasPlain;
		this.Importance = msg.Importance;
		
		this.FromAddr = HtmlDecode(msg.FromAddr);
		this.ToAddr = HtmlDecode(msg.ToAddr);
		this.CCAddr = HtmlDecode(msg.CCAddr);
		this.BCCAddr = HtmlDecode(msg.BCCAddr);
		this.Subject = HtmlDecode(msg.Subject);
		this.Date = HtmlDecode(msg.Date);

		this.HtmlBody = msg.HtmlBody;
		this.PlainBody = msg.ClearPlainBody;

		this.Attachments = msg.Attachments;
	},
	
	ParseEmailStr: function (recipients, fromAddr)
	{
		if (null == recipients)  return [];

		var arRecipients = Array();
		var sWorkingRecipients = HtmlDecode(recipients);
		var sWorkingRecipients = Trim(sWorkingRecipients);

		var emailStartPos = 0;
		var emailEndPos = 0;

		var isInQuotes = false;
		var chQuote = '"';
		var isInAngleBrackets = false;
		var isInBrackets = false;

		var currentPos = 0;
		
		var sWorkingRecipientsLen = sWorkingRecipients.length;
		
		while (currentPos < sWorkingRecipientsLen) {
			var currentChar = sWorkingRecipients.substring(currentPos, currentPos+1);
			switch (currentChar) {
				case '\'':
				case '"':
					if (!isInQuotes) {
						chQuote = currentChar;
						isInQuotes = true;
					}
					else if (chQuote == currentChar) {
						isInQuotes = false;
					};
				break;
				case '<':
					if (!isInAngleBrackets) {
						isInAngleBrackets = true;
					};
				break;
				case '>':
					if (isInAngleBrackets) {
						isInAngleBrackets = false;
					};
				break;
				case '(':
					if (!isInBrackets) {
						isInBrackets = true;
					};
				break;
				case ')':
					if (isInBrackets) {
						isInBrackets = false;
					};
				break;
				case ',':
				case ';':											
					if (!isInAngleBrackets && !isInBrackets && !isInQuotes) {
						emailEndPos = currentPos;
						var str = sWorkingRecipients.substring(emailStartPos, emailEndPos);
						if (Trim(str).length > 0) {
							sRecipient = GetEmailParts(str);
							var inList = false;
							var iCount = arRecipients.length;
							for (var i=0; i<iCount; i++) {
								if (arRecipients[i].Email == sRecipient.Email) inList = true;
							};
							if (!inList) {
								arRecipients.push(sRecipient);
							}
						};
						emailStartPos = currentPos + 1;
					};
				break;
			};
			currentPos++;
		};
		var iCount = arRecipients.length;
		Recipients = Array();
		fromRecipient = GetEmailParts(fromAddr);
		for (var i=0; i<iCount; i++) {
			if (iCount > 1) {
				if (fromRecipient.Email != arRecipients[i].Email) {
					Recipients.push(arRecipients[i].FullEmail);
				}
			}
			else {
				Recipients.push(arRecipients[i].FullEmail);
			}
		};
		return Recipients.join(', ');
	},

	PrepareForReply: function (msg, replyAction, fromAddr)
	{
		replyAction = replyAction - 0;
		this.Safety = msg.Safety;
		switch (replyAction) {
			case REPLY:
				this.HasHtml = msg.IsReplyHtml;
				this.HasPlain = msg.IsReplyPlain;
				this.HtmlBody = msg.ReplyHtml;
				this.PlainBody = msg.ReplyPlain;
				if (msg.ReplyToAddr.length > 0) {
					this.ToAddr = HtmlDecode(msg.ReplyToAddr);
				}
				else {
					this.ToAddr = HtmlDecode(msg.FromAddr);
				};
				this.FromAddr = '';
				this.CCAddr = '';
				this.BCCAddr = '';
				this.Subject = Lang.Re + ': ' + HtmlDecode(msg.Subject);
				this.Date = '';
				if (!msg.HasPlain && msg.HasHtml) {
					this.Attachments = [];
					var iCount = msg.Attachments.length;
					var j = 0;
					for (var i=0; i<iCount; i++) {
						if (msg.Attachments[i].Inline) {
							this.Attachments[j] = msg.Attachments[i];
							j++;
						}
					}
				};
			break;
			case REPLY_ALL:
				this.HasHtml = msg.IsReplyHtml;
				this.HasPlain = msg.IsReplyPlain;
				this.HtmlBody = msg.ReplyHtml;
				this.PlainBody = msg.ReplyPlain;
				if (msg.ReplyToAddr.length > 0) {
					this.ToAddr = this.ParseEmailStr(msg.ReplyToAddr + ',' + msg.ToAddr + ',' + msg.CCAddr + ',' + msg.BCCAddr, fromAddr);
				}
				else {
					this.ToAddr = this.ParseEmailStr(msg.FromAddr + ',' + msg.ToAddr + ',' + msg.CCAddr + ',' + msg.BCCAddr, fromAddr);
				};
				this.FromAddr = '';
				this.CCAddr = '';
				this.BCCAddr = '';
				this.Subject = Lang.Re + ': ' + HtmlDecode(msg.Subject);
				this.Date = '';
				if (!msg.HasPlain && msg.HasHtml) {
					this.Attachments = [];
					var iCount = msg.Attachments.length;
					var j = 0;
					for (var i=0; i<iCount; i++) {
						if (msg.Attachments[i].Inline) {
							this.Attachments[j] = msg.Attachments[i];
							j++;
						}
					}
				};
			break;
			case FORWARD:
				this.HasHtml = msg.IsForwardHtml;
				this.HasPlain = msg.IsForwardPlain;
				this.HtmlBody = msg.ForwardHtml;
				this.PlainBody = msg.ForwardPlain;
				this.ToAddr = '';
				this.FromAddr = '';
				this.CCAddr = '';
				this.BCCAddr = '';
				this.Subject = Lang.Fwd + ': ' + HtmlDecode(msg.Subject);
				this.Date = '';
				this.Attachments = msg.Attachments;
			break;
		}
	},//PrepareForReply
	
	GetInXML: function()
	{
		var strResult = '';
		var strHeaders = '';
		strHeaders += '<from>' + GetCData(this.FromAddr) + '</from>';
		strHeaders += '<to>' + GetCData(this.ToAddr) + '</to>';
		strHeaders += '<cc>' + GetCData(this.CCAddr) + '</cc>';
		strHeaders += '<bcc>' + GetCData(this.BCCAddr) + '</bcc>';
		strHeaders += '<subject>' + GetCData(this.Subject) + '</subject>';
		var strGroups = '';
		var iCount = this.SendersGroups.length;
		for (var i=0; i<iCount; i++) {
			strGroups += '<group id="' + this.SendersGroups[i] + '" />';
		}
		strHeaders += '<groups>' + strGroups + '</groups>';
		strHeaders = '<headers>' + strHeaders + '</headers>';
		var strBody = '';
		if (this.HasHtml) {
			strBody = '<body is_html="1">' + GetCData(this.HtmlBody, true) + '</body>';
		}
		else {
			strBody = '<body is_html="0">' + GetCData(this.PlainBody, true) + '</body>';
		}//if else
		var strAttachments = '';
		for (var j=0; j<this.Attachments.length; j++) {
			var Attachment = this.Attachments[j];
			var strAttachment = '';
			strAttachment += '<temp_name>' + GetCData(Attachment.TempName) + '</temp_name>';
			strAttachment += '<name>' + GetCData(Attachment.FileName) + '</name>';
			strAttachment += '<mime_type>' + GetCData(Attachment.MimeType) + '</mime_type>';
			var atAttrs = '';
			atAttrs += ' size="' + Attachment.Size + '"';
			if (Attachment.Inline)
				atAttrs += ' inline="1"';
			else
				atAttrs += ' inline="0"';
			strAttachments += '<attachment' + atAttrs + '>' + strAttachment + '</attachment>';
		}//for
		strAttachments = '<attachments>' + strAttachments + '</attachments>';
		var attrs = '';
		var uid = '';
		if (this.Id != -1) {
			attrs += ' id="' + this.Id + '"';
			uid = '<uid>' + GetCData(HtmlDecode(this.Uid)) + '</uid>';
		}
		else {
			attrs += ' id="-1"';
			uid = '<uid/>';
		};
		attrs += ' priority="' + this.Importance + '"';
		strResult = '<message' + attrs + '>' + uid + strHeaders + strBody + strAttachments + '</message>';
		return strResult;
	},//GetInXML
	
	ShowPictures: function ()
	{
		if (this.HasHtml) {
			this.HtmlBody = this.HtmlBody.ReplaceStr('wmx_src', 'src');
			this.HtmlBody = this.HtmlBody.ReplaceStr('wmx_background', 'background');
		};
		if (this.IsReplyHtml) {
			this.ReplyHtml = this.ReplyHtml.ReplaceStr('wmx_src', 'src');
			this.ReplyHtml = this.ReplyHtml.ReplaceStr('wmx_background', 'background');
		};
		if (this.IsForwardHtml) {
			this.ForwardHtml = this.ForwardHtml.ReplaceStr('wmx_src', 'src');
			this.ForwardHtml = this.ForwardHtml.ReplaceStr('wmx_background', 'background');
		}
	},
	
	GetFromXML: function(RootElement)
	{
		this.HasHtml = false;
		var attr = RootElement.getAttribute('id');      if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('html');        if (attr) this.HasHtml= (attr == 1) ? true : false;
		attr = RootElement.getAttribute('plain');       if (attr) this.HasPlain = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('priority');    if (attr) this.Importance = attr - 0;
		var safety = 0;
		attr = RootElement.getAttribute('safety');      if (attr) safety = attr - 0;
		var needShowPic = false;
		if (this.Parts == 0) {
		    this.Safety = safety;
		}
		else {
		    needShowPic = (safety == 0 && this.Safety > 0);
		};
		attr = RootElement.getAttribute('mode');        if (attr) this.Parts = this.Parts | (attr - 0);
		attr = RootElement.getAttribute('charset');     if (attr) this.Charset = attr - 0;
		attr = RootElement.getAttribute('has_charset'); if (attr) this.HasCharset = (attr == 1) ? true : false;
		var MessageParts = RootElement.childNodes;
		for (var i=0; i<MessageParts.length; i++) {
			var part = MessageParts[i].childNodes;
			if (part.length > 0) {
				switch (MessageParts[i].tagName) {
					case 'uid':
						this.Uid = Trim(part[0].nodeValue);
					break;
					case 'folder':
						attr = MessageParts[i].getAttribute('id');
						if (attr) this.FolderId = attr - 0;
						this.FolderFullName = HtmlDecode(part[0].nodeValue);
					break;
					case 'headers':
						var HeadersParts = MessageParts[i].childNodes;
						for (var j=0; j<HeadersParts.length; j++) {
							var part_ = HeadersParts[j].childNodes;
							if (part_.length > 0) {
								switch (HeadersParts[j].tagName) {
									case 'from':
										this.FromAddr = Trim(part_[0].nodeValue);
									break;
									case 'to':
										this.ToAddr = Trim(part_[0].nodeValue);
									break;
									case 'cc':
										this.CCAddr = Trim(part_[0].nodeValue);
									break;
									case 'bcc':
										this.BCCAddr = Trim(part_[0].nodeValue);
									break;
									case 'reply_to':
										this.ReplyToAddr = Trim(part_[0].nodeValue);
									break;
									case 'subject':
										this.Subject = Trim(part_[0].nodeValue);
									break;
									case 'date':
										this.Date = Trim(part_[0].nodeValue);
									break;
								}//switch
							}
						};//for
					break;
					case 'html_part':
						this.HtmlBody = Trim(part[0].nodeValue);
						if (this.HtmlBody.length > 0) this.HasHtml = true;
					break;
					case 'modified_plain_text':
						this.PlainBody = Trim(part[0].nodeValue);
						this.ClearPlainBody = this.PlainBody;
						if (this.PlainBody.length > 0) this.HasPlain = true;
					break;
					case 'unmodified_plain_text':
						this.ClearPlainBody = Trim(part[0].nodeValue);
					break;
					case 'reply_html':
						this.ReplyHtml = Trim(part[0].nodeValue);
						if (this.ReplyHtml.length > 0) this.IsReplyHtml = true;
					break;
					case 'reply_plain':
						this.ReplyPlain = Trim(part[0].nodeValue);
						if (this.ReplyPlain.length > 0) this.IsReplyPlain = true;
					break;
					case 'forward_html':
						this.ForwardHtml = Trim(part[0].nodeValue);
						if (this.ForwardHtml.length > 0) this.IsForwardHtml = true;
					break;
					case 'forward_plain':
						this.ForwardPlain = Trim(part[0].nodeValue);
						if (this.ForwardPlain.length > 0) this.IsForwardPlain = true;
					break;
					case 'full_headers':
						this.FullHeaders = Trim(part[0].nodeValue);
					break;
					case 'attachments':
						var Attachments = MessageParts[i].childNodes;
						this.Attachments = [];
						for (var j=0; j<Attachments.length; j++) {
							var id = -1;
							attr = Attachments[j].getAttribute('id');
							if (attr) id = attr - 0;
							var size = 0;
							attr = Attachments[j].getAttribute('size');
							if (attr) size = attr;
							var inline = false;
							attr = Attachments[j].getAttribute('inline');
							if (attr) inline = (attr == 1) ? true : false;
							var References = Attachments[j].childNodes;
							var fileName = ''; var tempName = '';
							var download = '#'; var view = '#';
							var mimeType = '';
							var refCount = References.length;
							for (var k = refCount-1; k >= 0; k--) {
								var ref = References[k].childNodes;
								if (ref.length > 0 )
									switch (References[k].tagName) {
										case 'filename':
											fileName = Trim(ref[0].nodeValue);
											break;
										case 'tempname':
											tempName = Trim(ref[0].nodeValue);
											break;
										case 'mime_type':
											mimeType = Trim(ref[0].nodeValue);
											break;
										case 'download':
											download = HtmlDecode(Trim(ref[0].nodeValue));
											if (download == '') download = '#';
											break;
										case 'view':
											view = HtmlDecode(Trim(ref[0].nodeValue));
											if (view == '') view = '#';
											break;
									}//switch
							};//for
							this.Attachments.push({Id: id, Inline: inline, FileName: fileName, Size: size, Download: download, View: view, TempName: tempName, MimeType: mimeType});
						};//for 
					break;
					case 'save_link':
						var links = MessageParts[i].childNodes;
						if (links.length > 0)
							this.SaveLink = HtmlDecode(Trim(links[0].nodeValue));
					case 'print_link':
						var links = MessageParts[i].childNodes;
						if (links.length > 0)
							this.PrintLink = HtmlDecode(Trim(links[0].nodeValue));
					break;
				}//switch
			}
		};//for
		if (needShowPic) {
		    this.ShowPictures();
		}
	}//GetFromXML
};

function COperationMessages()
{
	this.Type = TYPE_MESSAGES_OPERATION;
	this.OperationType = '';
	this.OperationField = '';
	this.OperationValue = true;
	this.OperationInt = -1;
	this.isAllMess = false;
	this.FolderId = -1;
	this.FolderFullName = '';
	this.ToFolderId = -1;
	this.ToFolderFullName = '';
	this.Messages = new CDictionary();
}

COperationMessages.prototype = {
	GetInXML: function ()
	{
		var nodes = '<messages>';
		nodes += '<look_for fields="0">' + GetCData('') + '</look_for>';
		nodes += '<to_folder id="' + this.ToFolderId + '"><full_name>' + GetCData(this.ToFolderFullName) + '</full_name></to_folder>';
		nodes += '<folder id="' + this.FolderId + '"><full_name>' + GetCData(this.FolderFullName) + '</full_name></folder>';
		var keys = this.Messages.keys();
		var iCount = keys.length;
		for (var i=0; i<iCount; i++) {
			var msg = this.Messages.getVal(keys[i]);
			var jCount = msg.IdArray.length;
			for (var j=0; j<jCount; j++) {
				nodes += '<message id="' + msg.IdArray[j].Id + '">';
				nodes += '<uid>' + GetCData(msg.IdArray[j].Uid) + '</uid>';
				nodes += '<folder id="' + msg.FolderId + '"><full_name>' + GetCData(msg.FolderFullName) + '</full_name></folder>';
				nodes += '</message>';
			}
		};
		nodes += '</messages>';
		return nodes;
	},
	
	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('type');
		if (attr) this.OperationType = attr;
		this.GetOperation();
		this.isAllMess = (this.OperationInt == MARK_ALL_READ || this.OperationInt == MARK_ALL_UNREAD);
		var OperationElements = RootElement.childNodes;
		var elemCount = OperationElements.length;
		for (var j=0; j<elemCount; j++) {
			var part = OperationElements[j].childNodes;
			if (part.length > 0) {
				switch (OperationElements[j].tagName) {
					case 'to_folder':
						attr = OperationElements[j].getAttribute('id');
						if (attr) this.ToFolderId = attr - 0;
						this.ToFolderFullName = HtmlDecode(part[0].nodeValue);
					break;
					case 'folder':
						attr = OperationElements[j].getAttribute('id');
						if (attr) this.FolderId = attr - 0;
						this.FolderFullName = HtmlDecode(part[0].nodeValue);
					break;
					case 'messages':
						messagesElement = OperationElements[j];
						var messagesArray = messagesElement.childNodes;
						var messCount = messagesArray.length;
						for (var i=0; i<messCount; i++) {
							if (typeof(messagesArray[i]) == 'object') {
								var id = -1;
								var uid = '';
								var folderId = '';
								var folderFullName = '';
								attr = messagesArray[i].getAttribute('id');
								if (attr) id = attr - 0;
								var messageParts = messagesArray[i].childNodes;
								var messPartsCount = messageParts.length;
								for (var k=0; k<messPartsCount; k++) {
									var part_ = messageParts[k].childNodes;
									if (part_.length > 0) {
										switch (messageParts[k].tagName) {
											case 'uid':
												uid = Trim(part_[0].nodeValue);
												break;
											case 'folder':
												attr = messageParts[k].getAttribute('id');
												if (attr) folderId = attr - 0;
												folderFullName = HtmlDecode(part_[0].nodeValue);
												break;
										}
									}
								};
								if (this.Messages.exists(folderId + folderFullName)) {
									var folder = this.Messages.getVal(folderId + folderFullName);
									var idArray = folder.IdArray;
								}
								else {
									var idArray = Array();
								};
								idArray.push({Id: id, Uid: uid});
								this.Messages.setVal(folderId + folderFullName, {IdArray: idArray, FolderId: folderId, FolderFullName: folderFullName});
							}
						};
					break;
				}//switch
			}//if
		}
	},//GetFromXML
	
	GetOperation: function ()
	{
		switch (this.OperationType) {
			case OperationTypes[DELETE]:
				this.OperationField = 'Deleted';
				this.OperationValue = true;
				this.OperationInt = DELETE;
			break;
			case OperationTypes[UNDELETE]:
				this.OperationField = 'Deleted';
				this.OperationValue = false;
				this.OperationInt = UNDELETE;
			break;
			case OperationTypes[PURGE]:
				this.OperationInt = PURGE;
			break;
			case OperationTypes[MARK_AS_READ]:
				this.OperationField = 'Read';
				this.OperationValue = true;
				this.OperationInt = MARK_AS_READ;
			break;
			case OperationTypes[MARK_AS_UNREAD]:
				this.OperationField = 'Read';
				this.OperationValue = false;
				this.OperationInt = MARK_AS_UNREAD;
			break;
			case OperationTypes[FLAG]:
				this.OperationField = 'Flagged';
				this.OperationValue = true;
				this.OperationInt = FLAG;
			break;
			case OperationTypes[UNFLAG]:
				this.OperationField = 'Flagged';
				this.OperationValue = false;
				this.OperationInt = UNFLAG;
			break;
			case OperationTypes[MARK_ALL_READ]:
				this.OperationField = 'Read';
				this.OperationValue = true;
				this.OperationInt = MARK_ALL_READ;
			break;
			case OperationTypes[MARK_ALL_UNREAD]:
				this.OperationField = 'Read';
				this.OperationValue = false;
				this.OperationInt = MARK_ALL_UNREAD;
			break;
			case OperationTypes[MOVE_TO_FOLDER]:
				this.OperationInt = MOVE_TO_FOLDER;
			break;
		}
	}
};

// for message in messages list
function CMessageHeaders()
{
	this.Id = -1;
	this.Uid = '';
	this.HasAttachments = false;
	this.Importance = 3;

	this.FolderId = -1;
	this.FolderFullName = '';
	this.Charset = AUTOSELECT_CHARSET;
	this.Random = Math.random;

	this.Read = false;
	this.Replied = false;
	this.Forwarded = false;
	this.Flagged = false;
	this.Deleted = false;
	this.Gray = false;

	this.FromAddr = '';
	this.ToAddr = '';
	this.CCAddr = '';
	this.BCCAddr = '';
	this.ReplyToAddr = '';
	this.Size = '';
	this.Subject = '';
	this.Date = '';
}

CMessageHeaders.prototype = {
	GetIdForList: function(_SEPARATOR, id)
	{
		var identifiers = [this.Id, this.Uid, this.FolderId, this.FolderFullName, this.Charset, id];
		return identifiers.join(_SEPARATOR);
	},
	
	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('id');
		if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('has_attachments');
		this.HasAttachments = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('priority');
		if (attr) this.Importance = attr - 0;
		attr = RootElement.getAttribute('size');
		if (attr) this.Size = attr - 0;
		attr = RootElement.getAttribute('flags');
		if (attr) {
			var Flags = attr - 0;
			if (Flags & 1) this.Read = true;
			if (Flags & 2) this.Replied = true;
			if (Flags & 4) this.Flagged = true;
			if (Flags & 8) this.Deleted = true;
			if (Flags & 256) this.Forwarded = true;
			if (Flags & 512) this.Gray = true;
		};
		attr = RootElement.getAttribute('charset');
		if (attr) this.Charset = attr - 0;
		var HeadersParts = RootElement.childNodes;
		for (var i=0; i<HeadersParts.length; i++) {
			var part = HeadersParts[i].childNodes;
			if (part.length > 0) {
				switch (HeadersParts[i].tagName) {
					case 'folder':
						attr = HeadersParts[i].getAttribute('id');
						if (attr) this.FolderId = attr - 0;
						this.FolderFullName = HtmlDecode(part[0].nodeValue);
					break;
					case 'from':
						this.FromAddr = Trim(part[0].nodeValue);
					break;
					case 'to':
						this.ToAddr = Trim(part[0].nodeValue);
					break;
					case 'cc':
						this.CCAddr = Trim(part[0].nodeValue);
					break;
					case 'bcc':
						this.BCCAddr = Trim(part[0].nodeValue);
					break;
					case 'reply_to':
						this.ReplyToAddr = Trim(part[0].nodeValue);
					break;
					case 'subject':
						this.Subject = Trim(part[0].nodeValue);
						break;
					case 'date':
						this.Date = Trim(part[0].nodeValue);
					break;
					case 'uid':
						this.Uid = Trim(part[0].nodeValue);
					break;
				}//switch
			}
		}//for
	},//GetFromXML
	
	MakeSearchResult: function (searchString)
	{
		this.FromAddr = this.FromAddr.ReplaceStr(searchString, HighlightMessageLine);
		this.Subject = this.Subject.ReplaceStr(searchString, HighlightMessageLine);
	}
};

function CMessages()
{
	this.Type = TYPE_MESSAGES_LIST;
	this.FolderId = -1;
	this.FolderFullName = '';
	this.SortField = 0;//0=from, 1=date, 2=size, 3=subject
	this.SortOrder = 0;//0=ASC, 1=DESC
	this.Page = 1;
	this.MessagesCount = 0;
	this.NewMsgsCount = 0;
	this._lookFor = '';
	this._searchFields = 0;
	this.List = [];
	this._SEPARATOR = "!@#!";
}

CMessages.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Page, this.SortField, this.SortOrder, this.FolderId, this.FolderFullName, this._lookFor, this._searchFields ];
		return arDataKeys.join(_SEPARATOR);
	},
	
	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('page');
		if (attr) this.Page = attr - 0;
		attr = RootElement.getAttribute('sort_field');
		if (attr) this.SortField = attr - 0;
		attr = RootElement.getAttribute('sort_order');
		if (attr) this.SortOrder = attr - 0;
		attr = RootElement.getAttribute('count');
		if (attr) this.MessagesCount = attr - 0;
		attr = RootElement.getAttribute('count_new');
		if (attr) this.NewMsgsCount = attr - 0;
		var MessagesXML = RootElement.childNodes;
		var MHeaders = null;
		var msgsCount = 0;
		for (var i=0; i<MessagesXML.length; i++) {
			var part = MessagesXML[i].childNodes;
			if (part.length > 0) {
				switch (MessagesXML[i].tagName) {
					case 'folder':
						attr = MessagesXML[i].getAttribute('id');
						if (attr) this.FolderId = attr - 0;
						this.FolderFullName = HtmlDecode(part[0].nodeValue);
					break;
					case 'look_for':
						attr = MessagesXML[i].getAttribute('fields');
						if (attr) this._searchFields = attr - 0;
						this._lookFor = Trim(part[0].nodeValue);
					break;
					case 'message':
						MHeaders = new CMessageHeaders();
						MHeaders.GetFromXML(MessagesXML[i]);
						if (this._lookFor != '') {
							MHeaders.MakeSearchResult(this._lookFor);
						}
						this.List[msgsCount++] = MHeaders;
					break;
				}
			}
		}//for
	},//GetFromXML
	
	GetMessageIndex: function (msg)
	{
		var index = -1;
		for (var i=0; i<this.List.length; i++) {
			var lMsg = this.List[i];
			if (lMsg.Id == msg.Id && lMsg.Uid == msg.Uid && lMsg.FolderId == msg.FolderId &&
			 lMsg.FolderFullName == msg.FolderFullName) {
				index = i;
				lMsg.Charset = msg.Charset;
				this.List[i] = lMsg;
			}
		};
		return index;
	},
	
	MakeMessageRead: function (messageParams)
	{
		for (var i=0; i<this.List.length; i++) {
			if (this.List[i].Id == messageParams[0] && this.FolderId == messageParams[1] && this.FolderFullName == messageParams[2]) {
				this.List[i].Read = true;
			}
		}
	}
};

function CFolder(level, listHide)
{
	this.Id = 0;
	this.IdParent = 0;
	this.Type = 0;
	this.SentDraftsType = false;
	this.SyncType = 0;
	this.Hide = false;
	this.ListHide = listHide;
	this.FldOrder = 0;
	this.MsgCount = 0;
	this.NewMsgCount = 0;
	this.Size = 0;
	this.Name = '';
	this.FullName = '';
	this.Level = level;
	this.hasChilds = false;
	this.Folders = new Array();
}

CFolder.prototype = {
	GetFromXML: function(RootElement, parentSentDraftsType)
	{
		var attr, part, FoldersXML, jCount, j, folder, childFolders;
		attr = RootElement.getAttribute('id');        if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('id_parent'); if (attr) this.IdParent = attr - 0;
		attr = RootElement.getAttribute('type');      if (attr) this.Type = attr - 0;
		this.SentDraftsType = (parentSentDraftsType || this.Type == FOLDER_TYPE_SENT || this.Type == FOLDER_TYPE_DRAFTS);
		attr = RootElement.getAttribute('sync_type'); if (attr) this.SyncType = attr - 0;
		attr = RootElement.getAttribute('hide');      if (attr) this.Hide = (attr == '1') ? true : false;
		this.ListHide = (this.Hide) ? this.Hide : this.ListHide;
		attr = RootElement.getAttribute('fld_order'); if (attr) this.FldOrder = attr - 0;
		attr = RootElement.getAttribute('count');     if (attr) this.MsgCount = attr - 0;
		attr = RootElement.getAttribute('count_new'); if (attr) this.NewMsgCount = attr - 0;
		attr = RootElement.getAttribute('size');      if (attr) this.Size = attr - 0;
		var FolderNames = RootElement.childNodes;
		var iCount = FolderNames.length;
		for (var i=0; i<iCount; i++) {
			part = FolderNames[i].childNodes;
			if (part.length > 0) {
				switch (FolderNames[i].tagName) {
					case 'name':
						this.Name = Trim(HtmlDecode(part[0].nodeValue));
					break;
					case 'full_name':
						this.FullName = HtmlDecode(part[0].nodeValue);
					break;
					case 'folders':
						FoldersXML = FolderNames[i].childNodes;
						jCount = FoldersXML.length;
						for (j=0; j<jCount; j++) {
							folder = new CFolder(this.Level + 1, this.ListHide);
							folder.GetFromXML(FoldersXML[j], this.SentDraftsType);
							childFolders = folder.Folders;
							if (childFolders.length > 0) folder.hasChilds = true;
							delete folder.Folders;
							this.Folders.push(folder);
							this.Folders = this.Folders.concat(childFolders);
						};//for
					break;
				}//switch
			}
		};//for
		if (this.Type != FOLDER_TYPE_INBOX && this.Type != FOLDER_TYPE_SENT && this.Type != FOLDER_TYPE_DRAFTS && this.Type != FOLDER_TYPE_TRASH){
			this.Type = FOLDER_TYPE_DEFAULT;
		}
	}//GetFromXML
};

function CFoldersList()
{
	this.Type = TYPE_FOLDERS_LIST;
	this.Folders = new Array();
	this.IdAcct = -1;
	this.Sync = 0;
}

CFoldersList.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},

	GetFromXML: function(RootElement)
	{
		var attr, folder, childFolders;
		attr = RootElement.getAttribute('id_acct'); if (attr) this.IdAcct = attr - 0;
		attr = RootElement.getAttribute('sync');    if (attr) this.Sync = attr - 0;
		var FoldersXML = RootElement.childNodes;
		var iCount = FoldersXML.length;
		for (var i=0; i<iCount; i++) {
			folder = new CFolder(0, false);
			folder.GetFromXML(FoldersXML[i], false);
			childFolders = folder.Folders;
			if (childFolders.length > 0) folder.hasChilds = true;
			delete folder.Folders;
			this.Folders.push(folder);
			this.Folders = this.Folders.concat(childFolders);
		}//for
	}
};

function CSignature() {
	this.Type = TYPE_SIGNATURE;
	this.IdAcct = -1;
	this.isHtml = false;
	this.Opt = 0;
	this.Value = '';
}

CSignature.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},

	GetInXML: function ()
	{
		var attrs = '';
		if (this.isHtml) {
			attrs += ' type="1"';
		}
		else {
			attrs += ' type="0"';
		};
		attrs += ' opt="' + this.Opt + '"';
		var xml = '<param name="id_acct" value="' + this.IdAcct + '"/>';
		xml += '<signature' + attrs + '>' + GetCData(this.Value) + '</signature>';
		return xml;
	},
	
	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('id');  if (attr) this.IdAcct = attr - 0;
		attr = RootElement.getAttribute('id_acct'); if (attr) this.IdAcct = attr - 0;
		attr = RootElement.getAttribute('type');    if (attr) this.isHtml = (attr == '1') ? true : false;
		attr = RootElement.getAttribute('opt');     if (attr) this.Opt = attr - 0;
		var signatureNodes = RootElement.childNodes;
		if (signatureNodes.length > 0) {
			var value = signatureNodes[0].nodeValue;
			value = HtmlDecode(value);
			this.Value = value;
		}
	}
};

function CUpdate()
{
	this.Type = TYPE_UPDATE;
	this.Value = '';
	this.Id = '-1';
	this.Uid = '';
}

CUpdate.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('value');
		if (attr) {
			this.Value = attr;
		    if (attr == 'save_message') {
		        attr = RootElement.getAttribute('id');
		        if (attr) this.Id = attr;
				var UpdateParts = RootElement.childNodes;
				if (UpdateParts.length > 0) {
					var part = UpdateParts[0].childNodes;
					if (part.length > 0 && UpdateParts[0].tagName == 'uid') {
						this.Uid = Trim(part[0].nodeValue);
					}
				}
		    }
		}
	}
};

function CContact()
{
	this.Type = TYPE_CONTACT;
	this.Id = -1;
	this.PrimaryEmail = 0;
	this.UseFriendlyNm = false;

	this.Name = '';
	this.Day = 0;
	this.Month = 0;
	this.Year = 0;
	
	this.hEmail = '';
	this.hStreet = '';
	this.hCity = '';
	this.hState = '';
	this.hZip = '';
	this.hCountry = '';
	this.hFax = '';
	this.hPhone = '';
	this.hMobile = '';
	this.hWeb = '';

	this.bEmail = '';
	this.bCompany = '';
	this.bJobTitle = '';
	this.bDepartment = '';
	this.bOffice = '';
	this.bStreet = '';
	this.bCity = '';
	this.bState = '';
	this.bZip = '';
	this.bCountry = '';
	this.bFax = '';
	this.bPhone = '';
	this.bWeb = '';
	
	this.OtherEmail = '';
	this.Notes = '';
	
	this.Groups = [];
	this.onlyMainData = true;
	this.hasHomeData = false;
	this.hasBusinessData = false;
	this.hasOtherData = false;
}

CContact.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Id ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('id');            if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('primary_email');     if (attr) this.PrimaryEmail = attr - 0;
		attr = RootElement.getAttribute('use_friendly_name'); if (attr) this.UseFriendlyNm = (attr == 1) ? true : false;
		var ContactParts = RootElement.childNodes;
		for (var i=0; i<ContactParts.length; i++) {
			switch (ContactParts[i].tagName) {
				case 'fullname':
					var parts = ContactParts[i].childNodes;
					if (parts.length > 0) this.Name = Trim(parts[0].nodeValue);
					break;
				case 'birthday':
					attr = ContactParts[i].getAttribute('day');   this.Day = attr - 0;
					attr = ContactParts[i].getAttribute('month'); this.Month = attr - 0;
					attr = ContactParts[i].getAttribute('year');  this.Year = attr - 0;
					if (this.Day > 0 || this.Month > 0 || this.Year > 0) {
					    this.hasOtherData = true;
					    this.onlyMainData = false;
					};
					break;
				case 'personal':
					var PersonalParts = ContactParts[i].childNodes;
					var jCount = PersonalParts.length;
					for (var j=0; j<jCount; j++) {
						var parts = PersonalParts[j].childNodes;
						var nodeValue = '';
						if (parts.length > 0) {
						    nodeValue = parts[0].nodeValue;
						};
						if (nodeValue.length > 0) {
						    this.hasHomeData = true;
							switch (PersonalParts[j].tagName) {
								case 'email':
									this.hEmail = nodeValue;
									if (this.PrimaryEmail != 0) this.onlyMainData = false;
									break;
								case 'street':
									this.hStreet = nodeValue;
									this.onlyMainData = false;
									break;
								case 'city':
									this.hCity = nodeValue;
									this.onlyMainData = false;
									break;
								case 'state':
									this.hState = nodeValue;
									this.onlyMainData = false;
									break;
								case 'zip':
									this.hZip = nodeValue;
									this.onlyMainData = false;
									break;
								case 'country':
									this.hCountry = nodeValue;
									this.onlyMainData = false;
									break;
								case 'fax':
									this.hFax = nodeValue;
									this.onlyMainData = false;
									break;
								case 'phone':
									this.hPhone = nodeValue;
									this.onlyMainData = false;
									break;
								case 'mobile':
									this.hMobile = nodeValue;
									this.onlyMainData = false;
									break;
								case 'web':
									this.hWeb = nodeValue;
									this.onlyMainData = false;
									break;
							}//switch
						}
					};//for
					break;
				case 'business':
					var BusinessParts = ContactParts[i].childNodes;
					var jCount = BusinessParts.length;
					for (var j=0; j<jCount; j++) {
						var parts = BusinessParts[j].childNodes;
						var nodeValue = '';
						if (parts.length > 0) {
						    nodeValue = parts[0].nodeValue;
						};
						if (nodeValue.length > 0) {
						    this.hasBusinessData = true;
							switch (BusinessParts[j].tagName) {
								case 'email':
									this.bEmail = nodeValue;
									if (this.PrimaryEmail != 1) this.onlyMainData = false;
									break;
								case 'company':
									this.bCompany = nodeValue;
									this.onlyMainData = false;
									break;
								case 'job_title':
									this.bJobTitle = nodeValue;
									this.onlyMainData = false;
									break;
								case 'department':
									this.bDepartment = nodeValue;
									this.onlyMainData = false;
									break;
								case 'office':
									this.bOffice = nodeValue;
									this.onlyMainData = false;
									break;
								case 'street':
									this.bStreet = nodeValue;
									this.onlyMainData = false;
									break;
								case 'city':
									this.bCity = nodeValue;
									this.onlyMainData = false;
									break;
								case 'state':
									this.bState = nodeValue;
									this.onlyMainData = false;
									break;
								case 'zip':
									this.bZip = nodeValue;
									this.onlyMainData = false;
									break;
								case 'country':
									this.bCountry = nodeValue;
									this.onlyMainData = false;
									break;
								case 'fax':
									this.bFax = nodeValue;
									this.onlyMainData = false;
									break;
								case 'phone':
									this.bPhone = nodeValue;
									this.onlyMainData = false;
									break;
								case 'web':
									this.bWeb = nodeValue;
									this.onlyMainData = false;
									break;
							}//switch
						}
					};//for
					break;
				case 'other':
					var otherParts = ContactParts[i].childNodes;
					var jCount = otherParts.length;
					for (var j=0; j<jCount; j++) {
						var parts = otherParts[j].childNodes;
						var nodeValue = '';
						if (parts.length > 0) {
						    nodeValue = parts[0].nodeValue;
						};
						if (nodeValue.length > 0) {
						    this.hasOtherData = true;
							switch (otherParts[j].tagName) {
								case 'email':
									this.OtherEmail = nodeValue;
									if (this.PrimaryEmail != 2) this.onlyMainData = false;
									break;
								case 'notes':
									this.Notes = nodeValue;
									this.onlyMainData = false;
									break;
							}//switch
						}
					};//for
					break;
				case 'groups':
					this.Groups = [];
					var GroupsParts = ContactParts[i].childNodes;
					var len = GroupsParts.length;
					for (var j=0; j<len; j++) {
						switch (GroupsParts[j].tagName) {
							case 'group':
								var groupId = -1;
								var groupName = '';
								attr = GroupsParts[j].getAttribute('id');
								if (attr) groupId = attr - 0;
								var parts = GroupsParts[j].childNodes;
								if (parts.length > 0) {
									var parts2 = parts[0].childNodes;
									if (parts2.length > 0) groupName = Trim(parts2[0].nodeValue);
								};
								this.Groups.push({Id: groupId, Name: groupName});
								break;
						}//switch
					};//for
					break;
			}//switch
		}//for
	}//GetFromXML
};

function CContacts()
{
	this.Type = TYPE_CONTACTS;
	this.GroupsCount = 0;
	this.ContactsCount = 0;
	this.Count = 0;
	this.SortField = null;
	this.SortOrder = null;
	this.Page = null;
	this.IdGroup = -1;
	this.LookFor = '';
	this.SearchType = 0;
	this.List = [];
}

CContacts.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.SortField, this.SortOrder, this.Page, this.IdGroup, this.LookFor ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXml: function ()
	{
		var xml = '<param name="id_group" value="' + this.IdGroup + '"/>';
		xml += '<look_for type="' + this.SearchType + '">' + GetCData(this.LookFor) + '</look_for>';
		return xml;
	},//GetInXml

	GetFromXML: function(RootElement)
	{
		var encodeLookFor = '';
		var attr = RootElement.getAttribute('groups_count'); if (attr) this.GroupsCount = attr - 0;
		attr = RootElement.getAttribute('contacts_count');   if (attr) this.ContactsCount = attr - 0;
		this.Count = this.GroupsCount + this.ContactsCount;
		attr = RootElement.getAttribute('page');             if (attr) this.Page = attr - 0;
		attr = RootElement.getAttribute('sort_field');       if (attr) this.SortField = attr - 0;
		attr = RootElement.getAttribute('sort_order');       if (attr) this.SortOrder = attr - 0;
		attr = RootElement.getAttribute('id_group');         if (attr) this.IdGroup = attr - 0;
		var ContactsXML = RootElement.childNodes;
		for (var i=0; i<ContactsXML.length; i++) {
			switch (ContactsXML[i].tagName) {
				case 'contact_group':
					var id = -1; var isGroup = 0; var name = ''; var email = '';
					attr = ContactsXML[i].getAttribute('id');       if (attr) id = attr - 0;
					attr = ContactsXML[i].getAttribute('is_group'); if (attr) isGroup = attr - 0;
					var ContactParts = ContactsXML[i].childNodes;
					var clearEmail = '';
					for (var j=0; j<ContactParts.length; j++) {
						var parts = ContactParts[j].childNodes;
						if (parts.length > 0)
							switch (ContactParts[j].tagName){
								case 'name':
									if (encodeLookFor.length > 0 && this.SearchType == 0) {
										name = Trim(parts[0].nodeValue).ReplaceStr(encodeLookFor, HighlightMessageLine);
									}
									else {
										name = Trim(parts[0].nodeValue);
									};
									break;
								case 'email':
									if (encodeLookFor.length > 0 && this.SearchType == 0) {
										clearEmail = Trim(parts[0].nodeValue);
										email = clearEmail.ReplaceStr(encodeLookFor, HighlightMessageLine);
									}
									else {
										clearEmail = Trim(parts[0].nodeValue);
										email = clearEmail;
									};
									break;
							}//switch
					};//for
					if (this.SearchType == 1) {
						var displayText = '';
						var replaceText = '';
						if (isGroup) {
							displayText = name.ReplaceStr(encodeLookFor, HighlightContactLine);
							replaceText = HtmlDecode(email);
						}
						else if (name.length > 0) {
							displayText = '"' + name.ReplaceStr(encodeLookFor, HighlightContactLine) + '" &lt;' + email.ReplaceStr(encodeLookFor, HighlightContactLine) + '&gt;';
							replaceText = HtmlDecode('"' + name + '" <' + email + '>');
						}
						else {
							displayText = email.ReplaceStr(encodeLookFor, HighlightContactLine);
							replaceText = HtmlDecode(email);
						};
						this.List.push({Id: id, IsGroup: isGroup, DisplayText: displayText, ReplaceText: replaceText});
					}
					else {
						this.List.push({Id: id, IsGroup: isGroup, Name: name, Email: email, ClearEmail: clearEmail});
					};
				break;
				case 'look_for':
					attr = ContactsXML[i].getAttribute('type'); if (attr) this.SearchType = attr - 0;
					var LookForParts = ContactsXML[i].childNodes;
					if (LookForParts.length > 0) {
						this.LookFor = Trim(LookForParts[0].nodeValue);
						encodeLookFor = HtmlEncode(this.LookFor);
					};
				break;
			}//switch
		}//for
	}//GetFromXML
};

function CGroups()
{
	this.Type = TYPE_GROUPS;
	this.Items = [];
}

CGroups.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var groupParts = RootElement.childNodes;
		for (var i=0; i<groupParts.length; i++) {
			switch (groupParts[i].tagName) {
				case 'group':
					var id = -1;
					var attr = groupParts[i].getAttribute('id');
					if (attr) id = attr - 0;
					var groupContent = groupParts[i].childNodes;
					if (groupContent.length > 0) {
						var name = '';
						if (groupContent[0].tagName == 'name') {
							var parts = groupContent[0].childNodes;
							if (parts.length > 0)
								name = Trim(parts[0].nodeValue);
						}
					};
					this.Items.push({Id: id, Name: name});
					break;
			}//switch
		}//for
	}//GetFromXML
};

function CGroup()
{
	this.Type = TYPE_GROUP;
	this.Id = -1;
	this.Name = '';
	this.Contacts = [];
	this.NewContacts = [];
	this.isOrganization = false;
	this.Email = '';
	this.Company = '';
	this.Street = '';
	this.City = '';
	this.State = '';
	this.Zip = '';
	this.Country = '';
	this.Fax = '';
	this.Phone = '';
	this.Web = '';
}

CGroup.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Id ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetInXml: function (params)
	{
		var attrs = '';
		if (this.Id != -1) {
			attrs += ' id="' + this.Id + '"';
		};
		if (this.isOrganization) {
			attrs += ' organization="1"';
		}
		else {
			attrs += ' organization="0"';
		};

		var contacts = '';
		var iCount = this.Contacts.length;
		for (var i=0; i<iCount; i++) {
			contacts += '<contact id="' + this.Contacts[i].Id + '"/>';
		};
		var newContacts = '';
		var iCount = this.NewContacts.length;
		for (var i=0; i<iCount; i++) {
			newContacts += '<contact><personal><email>' + GetCData(this.NewContacts[i].Email) + '</email></personal></contact>';
		};
		var xml = params + '<group' + attrs + '>';
		xml += '<name>' + GetCData(this.Name) + '</name>';
		xml += '<email>' + GetCData(this.Email) + '</email>';
		xml += '<company>' + GetCData(this.Company) + '</company>';
		xml += '<street>' + GetCData(this.Street) + '</street>';
		xml += '<city>' + GetCData(this.City) + '</city>';
		xml += '<state>' + GetCData(this.State) + '</state>';
		xml += '<zip>' + GetCData(this.Zip) + '</zip>';
		xml += '<country>' + GetCData(this.Country) + '</country>';
		xml += '<fax>' + GetCData(this.Fax) + '</fax>';
		xml += '<phone>' + GetCData(this.Phone) + '</phone>';
		xml += '<web>' + GetCData(this.Web) + '</web>';
		xml += '<contacts>' + contacts + '</contacts>';
		xml += '<new_contacts>' + newContacts + '</new_contacts>';
		xml += '</group>';
		return xml;
	},
	
	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('id');
		if (attr) this.Id = attr - 0;
		var attr = RootElement.getAttribute('organization');
		if (attr) this.isOrganization = (attr == 1) ? true : false;
		var GroupParts = RootElement.childNodes;
		for (var i=0; i<GroupParts.length; i++) {
			switch (GroupParts[i].tagName) {
				case 'name':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Name = Trim(parts[0].nodeValue);
				break;
				case 'email':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Email = Trim(parts[0].nodeValue);
				break;
				case 'company':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Company = Trim(parts[0].nodeValue);
				break;
				case 'street':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Street = Trim(parts[0].nodeValue);
				break;
				case 'city':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.City = Trim(parts[0].nodeValue);
				break;
				case 'state':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.State = Trim(parts[0].nodeValue);
				break;
				case 'zip':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Zip = Trim(parts[0].nodeValue);
				break;
				case 'country':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Country = Trim(parts[0].nodeValue);
				break;
				case 'fax':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Fax = Trim(parts[0].nodeValue);
				break;
				case 'phone':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Phone = Trim(parts[0].nodeValue);
				break;
				case 'web':
					var parts = GroupParts[i].childNodes;
					if (parts.length > 0) this.Web = Trim(parts[0].nodeValue);
				break;
				case 'contacts':
					var contacts = GroupParts[i].childNodes;
					var jCount = contacts.length;
					for (var j=0; j<jCount; j++) {
						if (contacts[j].tagName == 'contact') {
							var id = -1;
							attr = contacts[j].getAttribute('id');
							if (attr) id = attr - 0;
							var contContent = contacts[j].childNodes;
							var name = '';
							var email = '';
							var kCount = contContent.length;
							for (var k=0; k<kCount; k++) {
								var parts = contContent[k].childNodes;
								if (parts.length > 0)
									switch (contContent[k].tagName) {
										case 'fullname':
											name = Trim(parts[0].nodeValue);
											break;
										case 'email':
											email = Trim(parts[0].nodeValue);
											break;
									}//switch
							}//for
							this.Contacts.push({Id: id, Name: name, Email: email});
						}
					}
					break;
			}//switch
		}//for
	}//GetFromXML
};

function CSettings()
{
	this.Type = TYPE_USER_SETTINGS;
	this.MsgsPerPage = null;
	this.DisableRte = null;
	this.CharsetInc = null;
	this.CharsetOut = null;
	this.TimeOffset = null;
	this.ViewMode = null;
	this.DefSkin = null;
	this.Skins = Array();
	this.DefLang = null;
	this.Langs = Array();
	this.DateFormat = null;
	this.TimeFormat = null;
}

CSettings.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function ()
	{
		var attrs = '';
		if (this.MsgsPerPage != null) {
			attrs += ' msgs_per_page="' + this.MsgsPerPage + '"';
		};
		if (this.DisableRte != null) {
			if (this.DisableRte) {
				attrs += ' allow_dhtml_editor="0"';
			}
			else {
				attrs += ' allow_dhtml_editor="1"';
			}
		};
		if (this.CharsetInc != null) {
			attrs += ' def_charset_inc="' + this.CharsetInc + '"';
		};
		if (this.CharsetOut != null) {
			attrs += ' def_charset_out="' + this.CharsetOut + '"';
		};
		if (this.TimeOffset != null) {
			attrs += ' def_timezone="' + this.TimeOffset + '"';
		};
		if (this.ViewMode != null) {
			attrs += ' view_mode="' + this.ViewMode + '"';
		};
		if (this.TimeFormat != null) {
			attrs += ' time_format="' + this.TimeFormat + '"';
		};
		var nodes = '';
		if (this.DefSkin != null) {
			nodes += '<def_skin>' + GetCData(this.DefSkin) + '</def_skin>';
		};
		if (this.DefLang != null) {
			nodes += '<def_lang>' + GetCData(this.DefLang) + '</def_lang>';
		};
		if (this.DateFormat != null) {
			nodes += '<def_date_fmt>' + GetCData(this.DateFormat) + '</def_date_fmt>';
		};

		return '<settings' + attrs + '>' + nodes + '</settings>';
	},
	
	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('msgs_per_page');
		if (attr) this.MsgsPerPage = attr - 0;
		attr = RootElement.getAttribute('allow_dhtml_editor');
		if (attr) this.DisableRte = (attr == 1) ? false : true;
		var attr = RootElement.getAttribute('def_charset_inc');
		if (attr) this.CharsetInc = attr - 0;
		var attr = RootElement.getAttribute('def_charset_out');
		if (attr) this.CharsetOut = attr - 0;
		var attr = RootElement.getAttribute('def_timezone');
		if (attr) this.TimeOffset = attr - 0;
		attr = RootElement.getAttribute('view_mode');
		if (attr) this.ViewMode = attr - 0;
		attr = RootElement.getAttribute('time_format');
		if (attr) this.TimeFormat = attr - 0;
		var SettingsParts = RootElement.childNodes;
		var count = SettingsParts.length;
		for (var i=count-1; i>=0; i--) {
			var parts = SettingsParts[i].childNodes;
			var partsCount = parts.length;
			switch (SettingsParts[i].tagName) {
				case 'skins':
					var defSkin = '';
					for (var j=0; j<partsCount; j++) {
						var def = false;
						var skin = '';
						attr = parts[j].getAttribute('def');
						if (attr) def = (attr == 1) ? true : false;
						var part = parts[j].childNodes;
						if (part.length > 0 && parts[j].tagName == 'skin')
							skin = part[0].nodeValue;
						if (skin.length > 0) {
							this.Skins.push(skin);
							if (def) this.DefSkin = skin;
							if (skin.toLowerCase() == 'hotmail_style') defSkin = skin;
						}
					};
					if (this.DefSkin == null && defSkin.length > 0)
						this.DefSkin = defSkin;
					break;
				case 'langs':
					defLang = '';
					for (var j=0; j<partsCount; j++) {
						var def = false;
						var lang = '';
						attr = parts[j].getAttribute('def');
						if (attr) def = (attr == 1) ? true : false;
						var part = parts[j].childNodes;
						if (part.length > 0 && parts[j].tagName == 'lang')
							lang = part[0].nodeValue;
						if (lang.length > 0) {
							this.Langs.push(lang);
							if (def) this.DefLang = lang;
							if (lang.toLowerCase() == 'english') defLang = lang;
						}
					};
					if (this.DefLang == null && defLang.length > 0)
						this.DefLang = defLang;
					break;
				case 'def_date_fmt':
					if (partsCount > 0) {
						this.DateFormat = parts[0].nodeValue;
					}
					else {
						this.DateFormat = '';
					};
					break;
			}//switch
		}//for
	}//GetFromXML
};

function CAccountProperties()
{
	this.Type = TYPE_ACCOUNT_PROPERTIES;
	this.Id = -1;
	this.DefAcct = false;
	this.MailProtocol = POP3_PROTOCOL;
	this.MailIncPort = POP3_PORT;
	this.MailOutPort = SMTP_PORT;
	this.MailOutAuth = false;
	this.UseFriendlyNm = false;
	this.MailsOnServerDays = 1;
	this.MailMode = 1;
	this.GetMailAtLogin = true;
	this.InboxSyncType = SYNC_TYPE_NEW_MSGS;
	this.FriendlyNm = '';
	this.Email = '';
	this.MailIncHost = '';
	this.MailIncLogin = '';
	this.MailIncPass = '';
	this.MailOutHost = '';
	this.MailOutLogin = '';
	this.MailOutPass = '';
}

CAccountProperties.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Id ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function ()
	{
		var attrs = '';
		attrs += ' mail_inc_port="' + this.MailIncPort + '"';
		attrs += ' mail_out_port="' + this.MailOutPort + '"';
		if (this.Id != -1) attrs += ' id="' + this.Id + '"';
		if (this.DefAcct) attrs += ' def_acct="1"';
		else attrs += ' def_acct="0"';
		attrs += ' mail_protocol="' + this.MailProtocol + '"';
		if (this.MailOutAuth) attrs += ' mail_out_auth="1"';
		else attrs += ' mail_out_auth="0"';
		if (this.UseFriendlyNm) attrs += ' use_friendly_nm="1"';
		else attrs += ' use_friendly_nm="0"';
		attrs += ' mails_on_server_days="' + this.MailsOnServerDays + '"';
		attrs += ' mail_mode="' + this.MailMode + '"';
		if (this.GetMailAtLogin) attrs += ' getmail_at_login="1"';
		else attrs += ' getmail_at_login="0"';
		attrs += ' inbox_sync_type="' + this.InboxSyncType + '"';

		var nodes = '';
		nodes += '<friendly_nm>' + GetCData(this.FriendlyNm) + '</friendly_nm>';
		nodes += '<mail_out_host>' + GetCData(this.MailOutHost) + '</mail_out_host>';
		nodes += '<mail_out_login>' + GetCData(this.MailOutLogin) + '</mail_out_login>';
		nodes += '<mail_out_pass>' + GetCData(this.MailOutPass) + '</mail_out_pass>';
		nodes += '<email>' + GetCData(this.Email) + '</email>';
		nodes += '<mail_inc_host>' + GetCData(this.MailIncHost) + '</mail_inc_host>';
		nodes += '<mail_inc_login>' + GetCData(this.MailIncLogin) + '</mail_inc_login>';
		nodes += '<mail_inc_pass>' + GetCData(this.MailIncPass) + '</mail_inc_pass>';

		var xml = '<account' + attrs + '>' + nodes + '</account>';
		return xml;
	},

	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('id');
		if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('def_acct');
		if (attr) this.DefAcct = (attr == 1) ? true : false;
		var attr = RootElement.getAttribute('mail_protocol');
		if (attr) this.MailProtocol = attr - 0;
		var attr = RootElement.getAttribute('mail_inc_port');
		if (attr) this.MailIncPort = attr - 0;
		var attr = RootElement.getAttribute('mail_out_port');
		if (attr) this.MailOutPort = attr - 0;
		attr = RootElement.getAttribute('mail_out_auth');
		if (attr) this.MailOutAuth = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('use_friendly_nm');
		if (attr) this.UseFriendlyNm = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('mails_on_server_days');
		if (attr) this.MailsOnServerDays = attr - 0;
		attr = RootElement.getAttribute('mail_mode');
		if (attr) this.MailMode = attr - 0;
		attr = RootElement.getAttribute('getmail_at_login');
		if (attr) this.GetMailAtLogin = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('inbox_sync_type');
		if (attr) this.InboxSyncType = attr - 0;
		var SettingsParts = RootElement.childNodes;
		var count = SettingsParts.length;
		for (var i=count-1; i>=0; i--) {
			var parts = SettingsParts[i].childNodes;
			var partsCount = parts.length;
			if (partsCount > 0) {
				switch (SettingsParts[i].tagName) {
					case 'friendly_name':
						this.FriendlyNm = parts[0].nodeValue;
						break;
					case 'email':
						this.Email = parts[0].nodeValue;
						break;
					case 'mail_inc_host':
						this.MailIncHost = parts[0].nodeValue;
						break;
					case 'mail_inc_login':
						this.MailIncLogin = parts[0].nodeValue;
						break;
					case 'mail_inc_pass':
						this.MailIncPass = parts[0].nodeValue;
						break;
					case 'mail_out_host':
						this.MailOutHost = parts[0].nodeValue;
						break;
					case 'mail_out_login':
						this.MailOutLogin = parts[0].nodeValue;
						break;
					case 'mail_out_pass':
						this.MailOutPass = parts[0].nodeValue;
						break;
				}//switch
			}
		}//for
	}//GetFromXML
};

function CFilters() {
	this.Type = TYPE_FILTERS;
	this.Id = -1;
	this.Items = Array();
}

CFilters.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('id');
		if (attr) this.Id = attr - 0;
		var filters = RootElement.childNodes;
		var iCount = filters.length;
		for (var i=0; i<iCount; i++) {
			var filterProp = new CFilterProperties();
			filterProp.GetFromXML(filters[i]);
			this.Items.push(filterProp);
		}//for
	}//GetFromXML
};

function CFilterProperties() {
	this.Type = TYPE_FILTER_PROPERTIES;
	this.Id = -1;
	this.Field = 0;
	this.Condition = 0;
	this.Action = 2;
	this.IdFolder = -1;
	this.Value = '';
	this.Desc = '';
}

CFilterProperties.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Id ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function (idAcct)
	{
		var attrs = '';
		attrs += ' id_acct="' + idAcct + '"';
		if (this.Id != -1 ) attrs += ' id="' + this.Id + '"';
		attrs += ' field="' + this.Field + '"';
		attrs += ' condition="' + this.Condition + '"';
		attrs += ' action="' + this.Action + '"';
		attrs += ' id_folder="' + this.IdFolder + '"';

		var xml = '<filter' + attrs + '>' + GetCData(this.Value) + '</filter>';
		return xml;
	},

	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('id');    if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('field');     if (attr) this.Field = attr - 0;
		attr = RootElement.getAttribute('condition'); if (attr) this.Condition = attr - 0;
		attr = RootElement.getAttribute('action');    if (attr) this.Action = attr - 0;
		attr = RootElement.getAttribute('id_folder'); if (attr) this.IdFolder = attr - 0;
		var filterNodes = RootElement.childNodes;
		if (filterNodes.length > 0)
			this.Value = filterNodes[0].nodeValue;
		var srtField = '';
		switch (this.Field) {
			case 0: srtField = Lang.From; break;
			case 1: srtField = Lang.To; break;
			case 2: srtField = Lang.Subject; break;
		};
		var srtCondition = '';
		switch (this.Condition) {
			case 0: srtCondition = Lang.ContainSubstring; break;
			case 1: srtCondition = Lang.ContainExactPhrase; break;
			case 2: srtCondition = Lang.NotContainSubstring; break;
		};
		this.Desc = srtCondition + ' <b>' + this.Value + '</b> ' + Lang.FilterDesc_At + ' ' + srtField + ' ' + Lang.FilterDesc_Field;
	}//GetFromXML
};

function CXSpam() {
	this.Type = TYPE_X_SPAM;
	this.Value = false;
}

CXSpam.prototype = {
	GetStringDataKeys: function (_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function ()
	{
		if (this.Value) var xml = '<param name="x_spam" value="1"/>';
		else var xml = '<param name="x_spam" value="0"/>';
		return xml;
	},

	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('value') - 0;
		if (attr) this.Value = (attr == 1) ? true : false;
	}//GetFromXML
};

function CContactsSettings() {
	this.Type = TYPE_CONTACTS_SETTINGS;
	this.WhiteListing = false;
	this.ContactsPerPage = -1;
}

CContactsSettings.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('white_listing');
		if (attr) this.WhiteListing = (attr == '1') ? true : false;

		var attr = RootElement.getAttribute('contacts_per_page');
		if (attr) this.ContactsPerPage = attr - 0;
	}//GetFromXML
};