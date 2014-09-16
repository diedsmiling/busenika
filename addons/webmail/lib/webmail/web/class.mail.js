/*
Classes:
	CAccounts
	CMessage
	COperationMessages
	CMessageHeaders
	CMessages
	CFolder
	CFoldersList
	CSignature
	CUpdate
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
		}
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
				}
				this.Items.push({Id: id, Email: email, FriendlyName: friendlyName, UseFriendlyNm: useFriendlyNm, MailProtocol: mailProtocol, DefOrder: defOrder, DefAcct: defAcct});
			}//if
		}//for
		this.Count = this.Items.length;
	}//GetFromXML
}

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
	this.IsForwardHtml = false;
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
		this.PlainBody = ReplaceStr(ReplaceStr(HtmlDecode(msg.ClearPlainBody), '&quot;', '"'), '<br>', '');

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
					} else if (chQuote == currentChar) {
						isInQuotes = false;
					}
				break;
				case '<':
					if (!isInAngleBrackets) {
						isInAngleBrackets = true;
					}
				break;
				case '>':
					if (isInAngleBrackets) {
						isInAngleBrackets = false;
					}
				break;
				case '(':
					if (!isInBrackets) {
						isInBrackets = true;
					}
				break;
				case ')':
					if (isInBrackets) {
						isInBrackets = false;
					}
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
							}
							if (!inList) {
								arRecipients.push(sRecipient);
							}
						}
						emailStartPos = currentPos + 1;
					}
				break;
			}
			currentPos++;
		}
		var iCount = arRecipients.length;
		Recipients = Array();
		fromRecipient = GetEmailParts(fromAddr);
		for (var i=0; i<iCount; i++) {
			if (iCount > 1) {
				if (fromRecipient.Email != arRecipients[i].Email) {
					Recipients.push(arRecipients[i].FullEmail);
				}
			} else {
				Recipients.push(arRecipients[i].FullEmail);
			}
		}
		return Recipients.join(', ');
	},

	PrepareForReply: function (msg, replyAction, fromAddr)
	{
		this.Safety = msg.Safety;
		switch (replyAction) {
			case REPLY:
				this.HasHtml = msg.IsReplyHtml;
				this.HasPlain = msg.IsReplyHtml ? false : true;
				if (this.HasHtml) this.HtmlBody = msg.ReplyHtml;
				else this.PlainBody = msg.ReplyPlain;
				if (msg.ReplyToAddr.length > 0)
				{
					this.ToAddr = HtmlDecode(msg.ReplyToAddr);
				}
				else
				{
					this.ToAddr = HtmlDecode(msg.FromAddr);
				}
				this.FromAddr = '';
				this.CCAddr = '';
				this.BCCAddr = '';
				this.Subject = Lang.Re + ': ' + HtmlDecode(msg.Subject);
				this.Date = '';
				this.Attachments = [];
				var iCount = msg.Attachments.length;
				var j = 0;
				for (var i=0; i<iCount; i++) {
					if (msg.Attachments[i].Inline) {
						this.Attachments[j] = msg.Attachments[i];
						j++;
					}
				}
			break;
			case REPLY_ALL:
				this.HasHtml = msg.IsReplyHtml;
				this.HasPlain = msg.IsReplyHtml ? false : true;
				if (this.HasHtml) this.HtmlBody = msg.ReplyHtml;
				else this.PlainBody = msg.ReplyPlain;
				if (msg.ReplyToAddr.length > 0)
				{
					this.ToAddr = this.ParseEmailStr(msg.ReplyToAddr + ',' + msg.ToAddr + ',' + msg.CCAddr + ',' + msg.BCCAddr, fromAddr);
				}
				else
				{
					this.ToAddr = this.ParseEmailStr(msg.FromAddr + ',' + msg.ToAddr + ',' + msg.CCAddr + ',' + msg.BCCAddr, fromAddr);
				}
				this.FromAddr = '';
				this.CCAddr = '';
				this.BCCAddr = '';
				this.Subject = Lang.Re + ': ' + HtmlDecode(msg.Subject);
				this.Date = '';
				this.Attachments = [];
				var iCount = msg.Attachments.length;
				var j = 0;
				for (var i=0; i<iCount; i++) {
					if (msg.Attachments[i].Inline) {
						this.Attachments[j] = msg.Attachments[i];
						j++;
					}
				}
			break;
			case FORWARD:
				this.HasHtml = msg.IsForwardHtml;
				this.HasPlain = msg.IsForwardHtml ? false : true;
				if (this.HasHtml) this.HtmlBody = msg.ForwardHtml;
				else this.PlainBody = msg.ForwardPlain;
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
			strBody = '<body is_html="1">' + GetCData(this.HtmlBody) + '</body>';
		} else {
			strBody = '<body is_html="0">' + GetCData(this.PlainBody) + '</body>';
		}//if else
		var strAttachments = ''
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
		} else {
			attrs += ' id="-1"';
			uid = '<uid/>';
		}
		attrs += ' priority="' + this.Importance + '"';
		strResult = '<message' + attrs + '>' + uid + strHeaders + strBody + strAttachments + '</message>';
		return strResult;
	},//GetInXML
	
	ShowPictures: function ()
	{
		if (this.HasHtml)
		{
			this.HtmlBody = ReplaceStr(this.HtmlBody, 'wmx_src', 'src');
			this.HtmlBody = ReplaceStr(this.HtmlBody, 'wmx_background', 'background');
		}
		if (this.IsReplyHtml)
		{
			this.ReplyHtml = ReplaceStr(this.ReplyHtml, 'wmx_src', 'src');
			this.ReplyHtml = ReplaceStr(this.ReplyHtml, 'wmx_background', 'background');
		}
		if (this.IsForwardHtml)
		{
			this.ForwardHtml = ReplaceStr(this.ForwardHtml, 'wmx_src', 'src');
			this.ForwardHtml = ReplaceStr(this.ForwardHtml, 'wmx_background', 'background');
		}
	},
	
	GetFromXML: function(RootElement)
	{
		this.HasHtml = false;
		var attr = RootElement.getAttribute('id');      if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('html');        if (attr) this.HasHtml= (attr == 1) ? true : false;
		attr = RootElement.getAttribute('plain');       if (attr) this.HasPlain = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('priority');    if (attr) this.Importance = attr - 0;
		attr = RootElement.getAttribute('mode');        if (attr) this.Parts = this.Parts | (attr - 0);
		attr = RootElement.getAttribute('charset');     if (attr) this.Charset = attr - 0;
		attr = RootElement.getAttribute('has_charset'); if (attr) this.HasCharset = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('safety');      if (attr) this.Safety = attr - 0;
		this.GoodCharset = true;
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
						}//for
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
					break;
					case 'forward_html':
						this.ForwardHtml = Trim(part[0].nodeValue);
						if (this.ForwardHtml.length > 0) this.IsForwardHtml = true;
					break;
					case 'forward_plain':
						this.ForwardPlain = Trim(part[0].nodeValue);
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
											break;
										case 'view':
											view = HtmlDecode(Trim(ref[0].nodeValue));
											break;
									}//switch
							}//for
							this.Attachments.push({Id: id, Inline: inline, FileName: fileName, Size: size, Download: download, View: view, TempName: tempName, MimeType: mimeType});
						}//for 
					break;
					case 'save_link':
						var links = MessageParts[i].childNodes;
						if (links.length > 0)
							this.SaveLink = HtmlDecode(Trim(links[0].nodeValue));
					break;
				}//switch
			}
		}//for
	}//GetFromXML
}

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
//idArray.push({Id: id, Uid: uid});
//this.Messages.setVal(folderId + folderFullName, {IdArray: idArray, FolderId: folderId, FolderFullName: folderFullName});
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
		}
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
								}
								if (this.Messages.exists(folderId + folderFullName)) {
									var folder = this.Messages.getVal(folderId + folderFullName);
									var idArray = folder.IdArray;
								} else {
									var idArray = Array();
								}
								idArray.push({Id: id, Uid: uid});
								this.Messages.setVal(folderId + folderFullName, {IdArray: idArray, FolderId: folderId, FolderFullName: folderFullName});
							}
						}
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
}

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
		}
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
		this.FromAddr = MakeSearchResult(this.FromAddr, searchString);
		this.Subject = MakeSearchResult(this.Subject, searchString);
	}//MakeSearchResult
}

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
	},//GetStringDataKeys
	
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
			 lMsg.FolderFullName == msg.FolderFullName && lMsg.Charset == msg.Charset) {
				index = i;
			}
		}//for
		return index;
	},
	
	MakeMessageRead: function (messageParams)
	{
		for (var i=0; i<this.List.length; i++) {
			if (this.List[i].Id == messageParams[0] && this.FolderId == messageParams[1] && this.FolderFullName == messageParams[2]) {
				this.List[i].Read = true;
			}
		}//for
	}//MakeMessageRead
}

function CFolder(level, listHide)
{
	this.Id = 0;
	this.IdParent = 0;
	this.Type = 0;
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
	GetFromXML: function(RootElement)
	{
		var attr, part, FoldersXML, jCount, j, folder, childFolders;
		attr = RootElement.getAttribute('id');        if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('id_parent'); if (attr) this.IdParent = attr - 0;
		attr = RootElement.getAttribute('type');      if (attr) this.Type = attr - 0;
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
							folder.GetFromXML(FoldersXML[j]);
							childFolders = folder.Folders;
							if (childFolders.length > 0) folder.hasChilds = true;
							delete folder.Folders;
							this.Folders.push(folder);
							this.Folders = this.Folders.concat(childFolders);
						}//for
					break;
				}//switch
			}
		}//for
		if (this.Type != FOLDER_TYPE_INBOX && this.Type != FOLDER_TYPE_SENT && this.Type != FOLDER_TYPE_DRAFTS && this.Type != FOLDER_TYPE_TRASH){
			this.Type = FOLDER_TYPE_DEFAULT;
		}
	}//GetFromXML
}

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
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr, folder, childFolders;
		attr = RootElement.getAttribute('id_acct'); if (attr) this.IdAcct = attr - 0;
		attr = RootElement.getAttribute('sync');    if (attr) this.Sync = attr - 0;
		var FoldersXML = RootElement.childNodes;
		var iCount = FoldersXML.length;
		for (var i=0; i<iCount; i++) {
			folder = new CFolder(0, false);
			folder.GetFromXML(FoldersXML[i]);
			childFolders = folder.Folders;
			if (childFolders.length > 0) folder.hasChilds = true;
			delete folder.Folders;
			this.Folders.push(folder);
			this.Folders = this.Folders.concat(childFolders);
		}//for
	}//GetFromXML
}

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
	},//GetStringDataKeys

	GetInXML: function ()
	{
		var attrs = '';
		if (this.isHtml) {
			attrs += ' type="1"';
		} else {
			attrs += ' type="0"';
		}
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
	}//GetFromXML
}

function CUpdate()
{
	this.Type = TYPE_UPDATE;
	this.Value = '';
}

CUpdate.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('value');
		if (attr) this.Value = attr;
	}//GetFromXML
}