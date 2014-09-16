/*
 * Classes:
 * 	CWebMail
 * 	CSettingsList
 */

function HideCalendar(link, helpParam)
{
	switch (link) {
		case 'account':
			WebMail.ShowMail(helpParam);
		break;
		case 'contacts':
			WebMail.ShowContacts();
		break;
		case 'settings':
			WebMail.ShowSettings();
		break;
		case 'logout':
			WebMail.LogOut();
		break;
		case 'error':
			WebMail.LogOut(helpParam);
		break;
	}
}

function CreateAccountActionFunc(id)
{
	return function() {
		SetHistoryHandler(
			{
				ScreenId: WebMail.ListScreenId,
				IdAcct: id
			}
		);
	}
}

function CheckShownItems()
{
	WebMail.PopupMenus.checkShownItems();
}

function CWebMail(Title, SkinName){
	this.isBuilded = false;
	this.shown = false;
	
	this.FoldersList = null;
	this.Accounts = null;
	this.SectionId = -1;
	this.Sections = Array();
	this.ScreenId = -1;
	this.Screens = Array();
	this.DataSource = null;
	this.ScriptLoader = new CScriptLoader();
	this.Settings = null;
	this.ListScreenId = -1;
	this.StartScreen = -1;
	this.ScreenIdForLoad = this.ListScreenId;
	this._message = null;
	this._replyAction = -1;
	this.forEditParams = [];
	this.fromDraftsParams = [];

	this._title = Title;
	this._skinName = SkinName;
	this.LangChanger = new CLanguageChanger();
	this._allowContacts = true;
	this._allowCalendar = true;
	this._fromField = '';
	this._email = '';
	this._isDemo = false;
	this._mailProtocol = POP3_PROTOCOL;
	this._defOrder = SORT_ORDER_ASC;

	this._html = document.getElementById('html');
	this._content = document.getElementById('content');
	this._copyright = document.getElementById('copyright');
	this._popupMenus = null;
	this._skinLink = document.getElementById('skin');
	this._newSkinLink = null;
	this._head = document.getElementsByTagName('head')[0];
	
	this._accountsBar = null;
	var _controlImg = null;
	var _replaceDiv = null;
	var _accountsList = null;
	var _accountNameObject = null;
	var _contactsObj = null;
	var _calendarObj = null;

	this._fadeEffect = new CFadeEffect('WebMail._fadeEffect');
	this._infoMessage = null;
	this._infoObj = null;
	this._infoCount = 0;
	this._errorObj = null;
	this._reportObj = null;
	this.BuildInformation();
	
	this._idAcct = -1;
	this._signature = null;
	this.HistoryArgs = null;
	this.HistoryObj = null;
	this.MailHistoryArgs = null;
	
	this._msgsPerPage = 20;
	this._allowDhtmlEditor = false;
	this._allowChangeSettings = true;
	this._timeOffset = 0;
	this._dateFormat = '';
	this._timeFormat = 0;
	this._viewMode = VIEW_MODE_WITH_PANE;
	this._useImapTrash = false;
	
	this._pageSwitcher = new CPageSwitcher(SkinName);
	this._htmlEditorField = null;
	this._htmlEditorScreen = null;
	
	this._messagesList = null;
	
	this._spellchecker = new CSpellchecker();
}

CWebMail.prototype = {
	PlaceData: function(Data)
	{
		var screen;
		var Type = Data.Type;
		switch (Type) {
			case TYPE_ACCOUNTS_LIST:
				if (this.Accounts != null && Data.Items.length == 0) {
					document.location = LoginUrl;
				}
				else {
					if (this.Accounts != null && Data.Items.length != this.Accounts.Items.length ||
					 this._idAcct != Data.CurrId) {
						screen = this.Screens[SCREEN_CALENDAR];
						if (screen) screen.NeedReload();
					};
					this.Accounts = Data;
					this._idAcct = Data.CurrId;
					this.FillAccountsList();
					GetFoldersListHandler(this.Accounts.CurrId, 0);
					if (screen = this.Screens[SCREEN_USER_SETTINGS]) screen.PlaceData(Data);
					if (this.ScreenId == SCREEN_NEW_MESSAGE) {
						screen = this.Screens[SCREEN_NEW_MESSAGE];
						if (screen) {
							screen.SetIdAccount(this._idAcct);
							screen.SetFromField(this._fromField);
						}
					}
				};
				break;
			case TYPE_CONTACTS_SETTINGS:
				this.Settings.ContactsPerPage = Data.ContactsPerPage;
				if (screen = this.Screens[SCREEN_USER_SETTINGS]) screen.PlaceData(Data);
				break;
			case TYPE_SETTINGS_LIST:
				Data = this.CheckWmCookies(Data);
				this.Settings = Data;
				this._allowContacts = Data.AllowContacts;
				this._allowCalendar = Data.AllowCalendar;
				if (this.isBuilded) {
					if (this._allowContacts)
						_contactsObj.className = 'wm_accountslist_contacts';
					else
						_contactsObj.className = 'wm_hide';
					if (this._allowCalendar)
						_calendarObj.className = 'wm_accountslist_contacts';
					else
						_calendarObj.className = 'wm_hide';
				};
				this.ParseSettings();
				if (screen = this.Screens[this.ListScreenId]) screen.ParseSettings(Data);
				if (this.ScreenId == -1) {
					if (this.StartScreen != -1) {
						SelectScreenHandler(this.StartScreen);
					}
					else {
						if (this.ListScreenId != -1) {
							SelectScreenHandler(this.ListScreenId);
						}
						else {
							SelectScreenHandler(SCREEN_MESSAGES_LIST_VIEW);
						}
					}
				};
				break;
			case TYPE_UPDATE:
				switch (Data.Value) {
					case 'cookie_settings':
						this.EraseWmCookies();
						break;
					case 'settings':
						this.ShowReport(Lang.ReportSettingsUpdatedSuccessfuly);
						if (screen = this.Screens[SCREEN_USER_SETTINGS]) {
							var settings = screen.GetNewSettings();
							this.UpdateSettings(settings);
							if (SCREEN_USER_SETTINGS == this.ScreenId ) {
								this.SetHtmlEditorField(screen);
							}
							if (this.ScreenId != -1)
								this.Screens[this.ScreenId].ParseSettings(this.Settings);
						}
						break;
					case 'account':
						this.ShowReport(Lang.ReportAccountUpdatedSuccessfuly);
						screen = this.Screens[SCREEN_USER_SETTINGS];
						if (screen) {
							var res = screen.UpdateAccountProperties();
							var listScreen = this.Screens[this.ListScreenId];
							if (listScreen) {
								if (res.AcctId == this._idAcct && listScreen.Protocol != IMAP4_PROTOCOL &&
								 res.IsDirectMode != listScreen.isInboxDirectMode) {
									GetFoldersListHandler(this._idAcct, 1);
									listScreen.isInboxDirectMode = res.IsDirectMode;
									listScreen.RepairToolBar();
								}
							}
						}
						break;
					case 'signature':
						this.ShowReport(Lang.ReportSignatureUpdatedSuccessfuly);
						screen = this.Screens[SCREEN_USER_SETTINGS];
						if (screen) {
							var signature = screen.GetNewSignature();
							this._signature = signature;
							screen = this.Screens[SCREEN_NEW_MESSAGE];
							if (screen) screen.PlaceData(signature);
						}
						break;
					
					
					case 'send_message':
						this.ShowReport(Lang.ReportMessageSent);
					
						screen = this.Screens[this.ListScreenId];
						if (screen) {
							this.DataSource.Cache.ClearMessagesList(screen.SentId, screen.SentFullName);
							this.DataSource.Cache.ClearMessagesList(screen.DraftsId, screen.DraftsFullName);
						}
						SetHistoryHandler(
							{
								ScreenId: this.ListScreenId,
								FolderId: null
							}
						);
						break;
					case 'save_message':
						var newMsgScreen = this.Screens[SCREEN_NEW_MESSAGE];
						if (newMsgScreen) {
							newMsgScreen.SetMessageId(Data.Id, Data.Uid);
							this.ShowReport(Lang.ReportMessageSaved);
						}
						var listScreen = this.Screens[this.ListScreenId];
						if (listScreen) {
							this.DataSource.Cache.ClearMessagesList(listScreen.DraftsId, listScreen.DraftsFullName);
						}
						if (this._mailProtocol == IMAP4_PROTOCOL) {
							SetHistoryHandler(
								{
									ScreenId: this.ListScreenId,
									FolderId: null
								}
							);
						}
						else {
							this.DataSource.Cache.ClearMessage(Data.Id, Data.Uid, listScreen.DraftsId, listScreen.DraftsFullName, '')
						}
						break;
					case 'group':
						screen = this.Screens[SCREEN_CONTACTS];
						if (screen) screen.PlaceData(Data);
					break;
				};
			break;
			case TYPE_FOLDERS_LIST:
				if (Data.IdAcct == this._idAcct) {
					this.FoldersList = Data;
					if (Data.Sync != 2) {
						this.DataSource.Cache.ClearMessagesList(-1, '');
					};
					if (Data.Sync == 1) {
						GetHandler(TYPE_SETTINGS_LIST, {}, [], '');
					};
					if (screen = this.Screens[SCREEN_MESSAGES_LIST_VIEW]) screen.PlaceData(Data);
					if (screen = this.Screens[SCREEN_MESSAGES_LIST]) screen.PlaceData(Data);
				};
				if (screen = this.Screens[SCREEN_USER_SETTINGS]) screen.PlaceData(Data);
				break;
			case TYPE_MESSAGES_LIST:
				this._messagesList = Data;
				if (Data._lookFor.length == 0) {
					this.DataSource.Cache.SetMessagesCount(Data.FolderId, Data.FolderFullName, Data.MessagesCount, Data.NewMsgsCount);
				};
				if (screen = this.Screens[SCREEN_MESSAGES_LIST_VIEW]) screen.PlaceData(Data);
				if (screen = this.Screens[SCREEN_MESSAGES_LIST]) screen.PlaceData(Data);
				if (screen = this.Screens[SCREEN_VIEW_MESSAGE]) screen.PlaceData(Data);
				break;
			case TYPE_MESSAGE:
				this._message = Data;
				var id = Data.Id; var uid = Data.Uid;
				var fId = Data.FolderId; var fName = Data.FolderFullName;
				if (this.DataSource.Cache.ClearMessage(id , uid, fId, fName, Data.Charset)) {
					this.DataSource.Cache.ClearMessagesList(fId, fName);
					var screen = this.Screens[this.ListScreenId];
					if (screen) {
						if (screen._selection) {
							var newId = this._message.GetIdForList(screen._SEPARATOR, screen.Id);
							screen._selection.ChangeLineId(this._message, newId);
						}
					}
				};
				this.DataSource.Set([[{Id: id, Uid: uid}], fId, fName], 'Read', true, false);
				if (4 == this.forEditParams.length && id == this.forEditParams[0] && uid == this.forEditParams[1] &&
				 fId == this.forEditParams[2] && fName == this.forEditParams[3]) {
					if (SCREEN_NEW_MESSAGE == this.ScreenId) {
						this.Screens[SCREEN_NEW_MESSAGE].UpdateMessage(this._message);
					}
					else {
						Screens[SCREEN_NEW_MESSAGE].ShowHandler = 'screen.UpdateMessage(this._message);';
						SelectScreenHandler(SCREEN_NEW_MESSAGE);
					};
					this.forEditParams = [];
				};
                if (this._replyAction != -1) {
                     var screen = this.Screens[SCREEN_NEW_MESSAGE];
                     if (screen) {
                          screen.UpdateMessageForReply(this._message, this._replyAction);
                     }
                     this._replyAction = -1;
                }
				else if (this.ListScreenId == SCREEN_MESSAGES_LIST) {
					if (this.ScreenId != SCREEN_VIEW_MESSAGE) {
						SelectScreenHandler(SCREEN_VIEW_MESSAGE);
					}
				};
				if (this.ScreenId == SCREEN_VIEW_MESSAGE || null != this.HistoryArgs &&
				 this.HistoryArgs.ScreenId == SCREEN_VIEW_MESSAGE) {
					this.Screens[SCREEN_VIEW_MESSAGE].Fill(this._message);
				};
				if (this.ScreenId == SCREEN_MESSAGES_LIST_VIEW)
					this.Screens[SCREEN_MESSAGES_LIST_VIEW].PlaceData(Data);
				break;
			case TYPE_MESSAGES_OPERATION:
				screen = this.Screens[this.ListScreenId];
				if (Data.OperationInt == FLAG || Data.OperationInt == UNFLAG) {
					this.DataSource.Cache.ClearMessagesList(Data.FolderId, Data.FolderFullName, true);
				};
				if (Data.OperationInt == DELETE && screen && (screen.Protocol != IMAP4_PROTOCOL || this._useImapTrash)) {
					if (screen.TrashId != -1 || screen.TrashFullName != '') {
						this.DataSource.Cache.ClearMessagesList(Data.ToFolderId, Data.ToFolderFullName);
						this.DataSource.Cache.ClearMessagesList(screen.TrashId, screen.TrashFullName);
					}
				}
				else if (Data.OperationInt == PURGE || Data.OperationInt == MOVE_TO_FOLDER) {
					this.DataSource.Cache.ClearMessagesList(Data.FolderId, Data.FolderFullName);
					if (Data.ToFolderId != -1 || Data.ToFolderFullName != '') {
						this.DataSource.Cache.ClearMessagesList(Data.ToFolderId, Data.ToFolderFullName);
					}
					if (Data.OperationInt == PURGE) {
						GetHandler(TYPE_SETTINGS_LIST, {}, [], '');
					}
				}
				else {
					if (Data.OperationField != '') {
						if (Data.isAllMess) {
							this.DataSource.Set([[], Data.FolderId, Data.FolderFullName], Data.OperationField, Data.OperationValue, Data.isAllMess);
						}
						else {
							var dict = Data.Messages;
							var keys = dict.keys();
							for (var i in keys) {
								var folder = dict.getVal(keys[i]);
								this.DataSource.Set([folder.IdArray, folder.FolderId, folder.FolderFullName], Data.OperationField, Data.OperationValue, Data.isAllMess);
							}
						}
					}
				};
				if (Data.OperationInt == DELETE && this.ScreenId == SCREEN_VIEW_MESSAGE) {
					var screen = this.Screens[SCREEN_VIEW_MESSAGE];
					if (screen) screen.PlaceData(Data);
				};
				if (screen = this.Screens[SCREEN_MESSAGES_LIST_VIEW]) screen.PlaceData(Data);
				if (screen = this.Screens[SCREEN_MESSAGES_LIST]) screen.PlaceData(Data);
				break;
			case TYPE_USER_SETTINGS:
				if (screen = this.Screens[SCREEN_USER_SETTINGS]) screen.PlaceData(Data);
				break;
			case TYPE_SIGNATURE:
				if (this._idAcct == Data.IdAcct) {
					this._signature = Data;
					if (screen = this.Screens[SCREEN_NEW_MESSAGE]) screen.PlaceData(Data);
				};
				if (screen = this.Screens[SCREEN_USER_SETTINGS]) screen.PlaceData(Data);
				break;
			default:
				if (this.ScreenId != -1)
					this.Screens[this.ScreenId].PlaceData(Data);
				break;
		}
	},
	
	ShowMail: function (idAcct)
	{
		if (idAcct) {
			SetHistoryHandler(
				{
					ScreenId: WebMail.ListScreenId,
					IdAcct: idAcct
				}
			);
			return;
		}
		var hasHistArgs = this.MailHistoryArgs != null;
		var screen = Screens[this.ScreenId];
		var isMailSection = screen && (screen.SectionId == SECTION_MAIL);
		if (hasHistArgs && !isMailSection) {
			var args = this.MailHistoryArgs;
			if (!this.Accounts.HasAccount(args.IdAcct)) {
				args = { ScreenId: WebMail.ListScreenId };
			}
			else {
				if (args.ScreenId == SCREEN_MESSAGES_LIST_VIEW || args.ScreenId == SCREEN_MESSAGES_LIST) {
					args.ScreenId = WebMail.ListScreenId;
				}
				if (undefined != args.IdAcct) delete args.IdAcct;
				if (undefined != args.AcctChanged) delete args.AcctChanged;
			}
			SetHistoryHandler(args);
			return;
		}
		SetHistoryHandler(
			{
				ScreenId: WebMail.ListScreenId,
				FolderId: null
			}
		);
	},
	
	ShowContacts: function ()
	{
		var screen = Screens[this.ScreenId];
		var isContactsSection = screen && (screen.SectionId == SECTION_CONTACTS);
		screen = this.Screens[SCREEN_CONTACTS];
		if (screen && screen.HistoryArgs != null && !isContactsSection) {
			var args = screen.HistoryArgs;
			if (undefined != args.IdAcct) delete args.IdAcct;
			if (undefined != args.AcctChanged) delete args.AcctChanged;
			SetHistoryHandler(args);
		}
		else {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_CONTACTS,
					LookFor: ''
				}
			);
		}
	},
	
	ShowCalendar: function ()
	{
		SetHistoryHandler(
			{
				ScreenId: SCREEN_CALENDAR
			}
		);
	},
	
	ShowSettings: function ()
	{
		var screen = this.Screens[SCREEN_USER_SETTINGS];
		if (screen && screen.HistoryArgs != null) {
			var args = screen.HistoryArgs;
			if (!this.Accounts.HasAccount(args.SelectIdAcct)) {
				args.SelectIdAcct = -1;
			}
			if (undefined != args.IdAcct) delete args.IdAcct;
			if (undefined != args.AcctChanged) delete args.AcctChanged;
			SetHistoryHandler(args);
		}
		else {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					SelectIdAcct: -1,
					Entity: PART_COMMON_SETTINGS,
					NewMode: false
				}
			);
		}
	},
	
	LogOut: function (errorCode)
	{
		if (errorCode) {
			document.location = LoginUrl + '?error=' + errorCode;
		}
		else {
			EraseCookie('awm_autologin_data');
			EraseCookie('awm_autologin_id');
			document.location = LoginUrl + '?mode=logout';
		}
	},
	
	SetStartScreen: function (start)
	{
		switch (start) {
			case (1):
				this.StartScreen = SCREEN_NEW_MESSAGE;
				if (ToAddr && ToAddr.length > 0) {
					Screens[SCREEN_NEW_MESSAGE].ShowHandler = "screen.UpdateMessageFromContacts('" + ToAddr + "')";
				}
			break;
			case (2): this.StartScreen = SCREEN_USER_SETTINGS; break;
			case (3): this.StartScreen = SCREEN_CONTACTS; break;
			case (4): this.StartScreen = SCREEN_CALENDAR; break;
			default: this.StartScreen = this.ListScreenId; break;
		}
	},
	
	CheckHistoryObject: function (args, onlyCheck)
	{
		if (!args.IdAcct) args.IdAcct = this._idAcct;
		var checked = false; //parameters' set is such as previouse one
		if (null == this.HistoryObj) {
			checked = true;  //another
		};
		if (!checked) {
			switch (args.ScreenId) {
				case SCREEN_MESSAGES_LIST_VIEW:
				case SCREEN_VIEW_MESSAGE:
					if (args.MsgId != 'undefined' && args.MsgId != null) {
						if (args.IdAcct == this.HistoryObj.IdAcct && args.MsgFolderId == this.HistoryObj.MsgFolderId &&
						 args.MsgFolderFullName == this.HistoryObj.MsgFolderFullName && args.MsgId == this.HistoryObj.MsgId &&
						 args.MsgUid == this.HistoryObj.MsgUid && args.MsgCharset == this.HistoryObj.MsgCharset && 
						 args.ScreenId == this.HistoryObj.ScreenId) {
							checked = false;
						}
						else {
							checked = true;
						}
					}
					else {
						checked = true;
					};
				break;
				case SCREEN_USER_SETTINGS:
					if (args.SelectIdAcct == this.HistoryObj.SelectIdAcct &&
					 args.Entity == this.HistoryObj.Entity && args.NewMode == this.HistoryObj.NewMode &&
					 args.ScreenId == this.ScreenId) {
						checked = false;
					}
					else {
						checked = true;
					};
				break;
				default:
					checked = true;
				break;
			}
		};
		if (checked) {
			if (!onlyCheck) {
				this.HistoryObj = args;
			};
			return args;
		}
		else {
			return null;
		}
	},
	
	RestoreFromHistory: function (args)
	{
		if (!this._allowChangeSettings && args.ScreenId == SCREEN_USER_SETTINGS && args.Entity == PART_ACCOUNT_PROPERTIES) {
			args.Entity = PART_FILTERS;
		}
		if (args.IdAcct != this._idAcct) {
			var screen = this.Screens[SCREEN_CALENDAR];
			if (screen) screen.NeedReload();
			args.AcctChanged = true;
			this._idAcct = args.IdAcct;
			this.Accounts.CurrId = args.IdAcct;
			this.Accounts.LastId = args.IdAcct;
			this.FillAccountsList();
		}
		else {
			args.AcctChanged = false;
		};
		this.HistoryArgs = args;
		if (Screens[args.ScreenId] && Screens[args.ScreenId].SectionId == SECTION_MAIL) {
			this.MailHistoryArgs = args;
		};
		switch (args.ScreenId) {
			case SCREEN_NEW_MESSAGE:
				if (args.FromDrafts) {
					this.forEditParams = [args.MsgId, args.MsgUid, args.MsgFolderId, args.MsgFolderFullName];
					GetMessageHandler(args.MsgId, args.MsgUid, args.MsgFolderId, args.MsgFolderFullName, args.MsgParts, args.MsgCharset);
				}
				else if (args.ForReply) {
					this._replyAction = args.ReplyType;
					GetMessageHandler(args.MsgId, args.MsgUid, args.MsgFolderId, args.MsgFolderFullName, args.MsgParts, args.MsgCharset);
				}
				else if (args.FromContacts) {
					if (this.ScreenId == SCREEN_NEW_MESSAGE) {
						this.Screens[SCREEN_NEW_MESSAGE].UpdateMessageFromContacts(args.ToField);
					}
					else {
						Screens[SCREEN_NEW_MESSAGE].ShowHandler = "screen.UpdateMessageFromContacts('" + args.ToField + "')";
					}
				};
			break;
			case SCREEN_VIEW_MESSAGE:
				if (SCREEN_MESSAGES_LIST_VIEW == this.ScreenId) {
					var listScreen = this.Screens[SCREEN_MESSAGES_LIST_VIEW];
					if (listScreen) {
						var msg = new CMessage();
						msg.Id = args.MsgId;
						msg.Uid = args.MsgUid;
						msg.FolderId = args.MsgFolderId;
						msg.FolderFullName = args.MsgFolderFullName;
						msg.Charset = args.MsgCharset;
						listScreen.msgForView = msg;
					}
				};
				GetMessageHandler(args.MsgId, args.MsgUid, args.MsgFolderId, args.MsgFolderFullName, args.MsgParts, args.MsgCharset);
			break;
			case SCREEN_USER_SETTINGS:
			    if (args.Entity == PART_MANAGE_FOLDERS && args.SetIdAcct == true) {
			        args.SelectIdAcct = this._idAcct;
			    };
			break;
		};
		if (this.ScreenId != args.ScreenId) {
			var isSectionMail = Screens[this.ScreenId] && Screens[this.ScreenId].SectionId == SECTION_MAIL;
			if (SCREEN_VIEW_MESSAGE != args.ScreenId || !isSectionMail) {
				SelectScreenHandler(args.ScreenId);
			}
		}
		else {
			var screen;
			if (screen = this.Screens[this.ScreenId]) {
				screen.RestoreFromHistory(args);
				this.HistoryArgs = null;
			}
			else {
				SelectScreenHandler(args.ScreenId);
			}
		}
	},
	
	ContactsImported: function (count)
	{
		if (count == 0) {
			this.ShowReport(Lang.ErrorNoContacts);
		};
		if (count > 0) {
			this.ShowReport(Lang.InfoHaveImported + ' ' + count + ' ' + Lang.InfoNewContacts);
			var screen;
			if (screen = this.Screens[SCREEN_CONTACTS])
				screen.ContactsImported(count);
		}
	},
	
	LoadAttachment: function (attachment)
	{
		var screen;
		if (screen = this.Screens[SCREEN_NEW_MESSAGE])
			screen.LoadAttachment(attachment);
	},
	
	DesignModeOn: function (mode)
	{
		if (mode == 0) {
			this._htmlEditorField.DesignModeOn();
		}
		else {
			var screen;
			if (screen = this.Screens[this._htmlEditorScreen]) {
				screen.DesignModeOn();
			}
		}
	},
	
	LoadEditArea: function ()
	{
		if (null != this._htmlEditorField) {
			this._htmlEditorField.LoadEditArea();
		}
	},

	CreateLink: function (url)
	{
		if (null != this._htmlEditorField) {
			this._htmlEditorField.CreateLinkFromWindow(url);
		}
	},

	CheckWmCookies: function (settings)
	{
		var setCookies = false;
		var cookie = ReadCookie('wm_hide_folders');

		if (cookie != null && cookie != '') {
			if (cookie == '1')
				settings.HideFolders = true;
			else
				settings.HideFolders = false;
			setCookies = true;
		};

		cookie = ReadCookie('wm_horiz_resizer');
		if (cookie != null && cookie != '') {
			settings.HorizResizer = cookie - 0;
			setCookies = true;
		};

		cookie = ReadCookie('wm_vert_resizer');
		if (cookie != null && cookie != '') {
			settings.VertResizer = cookie - 0;
			setCookies = true;
		};

		cookie = ReadCookie('wm_mark');
		if (cookie != null && cookie != '') {
			settings.Mark = cookie - 0;
			setCookies = true;
		};

		cookie = ReadCookie('wm_reply');
		if (cookie != null && cookie != '') {
			settings.Reply = cookie - 0;
			setCookies = true;
		};
		
		var columns = Array();
		var iCount = InboxHeaders.length;
		for (var i=0; i<iCount; i++) {
			var cookie = ReadCookie('wm_column_'+i);
			if (cookie != null && cookie != '') {
				InboxHeaders[i].Width = cookie*1;
				setCookies = true;
			}
			else if (settings.Columns[i]) {
				InboxHeaders[i].Width = settings.Columns[i]*1;
			};
			columns[i] = InboxHeaders[i].Width;
		};
		settings.Columns = columns;
		
		if (setCookies)
			SetCookieSettingsHandler(settings.HideFolders, settings.HorizResizer, settings.VertResizer, settings.Mark, settings.Reply, settings.Columns);
		return settings;
	},
	
	EraseWmCookies: function ()
	{
		EraseCookie('wm_hide_folders');
		EraseCookie('wm_horiz_resizer');
		EraseCookie('wm_vert_resizer');
		EraseCookie('wm_mark');
		EraseCookie('wm_reply');
		var iCount = InboxHeaders.length;
		for (var i=0; i<iCount; i++) {
			EraseCookie('wm_column_'+i);
		}
	},
	
	ParseSettings: function ()
	{
		var settings = this.Settings;
		this._useImapTrash = settings.UseImapTrash;
		if (this._timeOffset != settings.TimeOffset) {
			this._timeOffset = settings.TimeOffset;
			this.DataSource.Cache.ClearAllMessages();
		};
		if (this._msgsPerPage != settings.MsgsPerPage || this._dateFormat != settings.DateFormat ||
		 this._timeFormat != settings.TimeFormat) {
			this._msgsPerPage = settings.MsgsPerPage;
			this._dateFormat = settings.DateFormat;
			this._timeFormat = settings.TimeFormat;
			this.DataSource.Cache.ClearMessagesList(-1, '');
		};
		this._allowDhtmlEditor = settings.AllowDhtmlEditor;
		this._allowChangeSettings = settings.AllowChangeSettings;
		if (this._viewMode != settings.ViewMode || this.ListScreenId == -1) {
			this._viewMode = settings.ViewMode;
			if (this._viewMode & VIEW_MODE_WITH_PANE == VIEW_MODE_WITH_PANE) {
				this.ListScreenId = SCREEN_MESSAGES_LIST_VIEW;
			}
			else {
				this.ListScreenId = SCREEN_MESSAGES_LIST;
			}
		};
		this.ChangeSkin(settings.DefSkin);
	},

	UpdateSettings: function (newSettings)
	{
		if (null != newSettings.MsgsPerPage) {
			this.Settings.MsgsPerPage = newSettings.MsgsPerPage;
		};
		if (null != newSettings.DisableRte) {
			this.Settings.AllowDhtmlEditor = newSettings.DisableRte ? false : true;
		};
		if ((null != newSettings.TimeOffset) && (newSettings.TimeOffset != this.Settings.TimeOffset)) {
			this.Settings.TimeOffset = newSettings.TimeOffset;
		};
		if (null != newSettings.ViewMode) {
			this.Settings.ViewMode = newSettings.ViewMode;
		};
		if (null != newSettings.DefSkin) {
			this.Settings.DefSkin = newSettings.DefSkin;
		};
		if (null != newSettings.DefLang) {
			if (this.Settings.DefLang != newSettings.DefLang) {
				this.Settings.DefLang = newSettings.DefLang;
				var obj = this;
				this.ScriptLoader.Load([LanguageUrl + '?lang=' +newSettings.DefLang], function () { obj.LoadFromLang(); });
			}
		};
		if ((null != newSettings.DateFormat) && (newSettings.DateFormat != this.Settings.DateFormat)) {
			this.Settings.DateFormat = newSettings.DateFormat;
		};
		this.ParseSettings();
		if (this.ScreenId != this.ListScreenId && (this.ScreenId == SCREEN_MESSAGES_LIST || this.ScreenId == SCREEN_MESSAGES_LIST_VIEW))
			SelectScreenHandler(this.ListScreenId);
	},
	
	LoadFromLang: function ()
	{
		var obj = this;
		this.ScriptLoader.Load(['_defines.js'], function () { obj.ChangeLang(); });
	},
	
	ChangeLang: function ()
	{
		this.SetTitle();
		var screen = this.Screens[SCREEN_MESSAGES_LIST_VIEW];
		if (screen && null != screen._toolBar) { screen._toolBar.ChangeLang(); };
		screen = this.Screens[SCREEN_MESSAGES_LIST];
		if (screen && null != screen._toolBar) { screen._toolBar.ChangeLang(); };
		screen = this.Screens[SCREEN_VIEW_MESSAGE];
		if (screen && null != screen._toolBar) { screen._toolBar.ChangeLang(); };
		screen = this.Screens[SCREEN_NEW_MESSAGE];
		if (screen && null != screen._toolBar) { screen._toolBar.ChangeLang(); };
		screen = this.Screens[SCREEN_CONTACTS];
		if (screen && null != screen._toolBar) { screen._toolBar.ChangeLang(); };
		this.LangChanger.Go();
		this._htmlEditorField.ChangeLang();
	},

	SetTitle: function (strTitle)
	{
		if (typeof(strTitle) == 'string') {
			document.title = this._title + ' - ' + Lang.Title[this.ScreenId] + ' - ' + strTitle;
		}
		else {
			document.title = this._title + ' - ' + Lang.Title[this.ScreenId];
		}
	},
	
	ChangeSkin: function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			this.AddNewSkinLink(newSkin);
			if (this.isBuilded) {
				_controlImg.src = 'skins/' + newSkin + '/menu/accounts_arrow.gif';
				this._errorObj.ChangeSkin(newSkin);
			};
			this._pageSwitcher.ChangeSkin(newSkin);
		}
		else
			this._newSkinLink = null;
	},

	AddNewSkinLink: function (newSkin)
	{
		var newLink = document.createElement('link');
		newLink.setAttribute('type', 'text/css');
		newLink.setAttribute('rel', 'stylesheet');
		newLink.href = 'skins/' + newSkin + '/styles.css';
		this._head.appendChild(newLink);
		this.RemoveOldSkinLink();
		this._newSkinLink = newLink;
	},	
	
	/*
	 * don't delete old skin immediately because of screen tweak in ff
	 */
	RemoveOldSkinLink: function ()
	{
		if (this._newSkinLink != null) {
			this._head.removeChild(this._skinLink);
			this._skinLink = this._newSkinLink;
			this._newSkinLink = null;
		};
	},

	Build: function()
	{
		this._popupMenus = new CPopupMenus();
		this.BuildAccountsList();
		document.body.onclick = ClickBodyHandler;
		this._pageSwitcher.Build();
		this._htmlEditorField = new CHtmlEditorField(false);
		this.isBuilded = true;
	},
	
	ResizeBody: function (mode)
	{
		if (this.isBuilded) {
		    if (this.ScreenId != SCREEN_CONTACTS) {
			    var width = GetWidth();
			    if (Browser.IE && Browser.Version < 7)
				    document.body.style.width = width + 'px';
			}
			else {
			    if (Browser.IE && Browser.Version < 7)
				    document.body.style.width = 'auto';
			};
			if (this.ScreenId != -1)
				this.Screens[this.ScreenId].ResizeBody(mode);
			this._errorObj.Resize();
			this._infoObj.Resize();
			this._reportObj.Resize();
		}
	},
	
	ClickBody: function (ev)
	{
		if (this.isBuilded) {
			this._popupMenus.checkShownItems();
			if (this.ScreenId != -1)
				this.Screens[this.ScreenId].ClickBody(ev);
			if (this._spellchecker.popupVisible()) 
				this._spellchecker.popupHide('document');
		}
	},
	
	ReplyClick: function (type)
	{
		var msg = null;
		if (this.ListScreenId == SCREEN_MESSAGES_LIST_VIEW) {
			var screen;
			if (screen = this.Screens[this.ListScreenId]) {
				msg = screen._msgObj;
			}
			if (msg == null) {
				var checkedLinesObj = screen._selection.GetCheckedLines();
				if (checkedLinesObj.IdArray.length > 0) {
					msg = new CMessage();
					msg.GetFromIdForList(screen._SEPARATOR, checkedLinesObj.IdArray[0]);
				}
			};
			if (msg == null) {
				alert(Lang.WarningMarkListItem);
			}
		}
		else {
			msg = this._message;
		};
		if (msg == null) return;
		var parts = [];
		if (type == FORWARD) {
			if (this._allowDhtmlEditor) {
				parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_FORWARD_HTML, PART_MESSAGE_ATTACHMENTS];
			}
			else {
				parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_FORWARD_PLAIN, PART_MESSAGE_ATTACHMENTS];
			}
		}
		else {
			if (this._allowDhtmlEditor) {
				parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_REPLY_HTML, PART_MESSAGE_ATTACHMENTS];
			}
			else {
				parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_REPLY_PLAIN, PART_MESSAGE_ATTACHMENTS];
			}
		};
		SetHistoryHandler(
			{
				ScreenId: SCREEN_NEW_MESSAGE,
				FromDrafts: false,
				ForReply: true,
				ReplyType: type,
				MsgId: msg.Id,
				MsgUid: msg.Uid,
				MsgFolderId: msg.FolderId,
				MsgFolderFullName: msg.FolderFullName,
				MsgParts: parts
			}
		);
	},
	
	ShowScreen: function(loadHandler)
	{
		var screenId = this.ScreenIdForLoad;
		var screen, section;
		if (screen = this.Screens[screenId]) {
			this.RemoveOldSkinLink();
			if (this.ScreenId != -1)
				this.Screens[this.ScreenId].Hide();
			this.ScreenId = screenId;
			this.SectionId = Screens[screenId].SectionId;
			if (!screen.isBuilded)
				screen.Build(this._content, this._accountsBar, this._popupMenus, this.Settings);
			if (screen.hasCopyright) {
				this._copyright.className = 'wm_copyright';
			}
			else {
				this._copyright.className = 'wm_hide';
			};
			SetBodyAutoOverflow(screen.BodyAutoOverflow);
			this.Show();
			document.title = this._title + ' - ' + Lang.Title[screenId];
			if (this.Accounts == null) WebMail.DataSource.Get(TYPE_ACCOUNTS_LIST, { }, [], '');
			eval(Screens[screenId].ShowHandler);
			switch (screen.Id) {
				case SCREEN_MESSAGES_LIST_VIEW:
					screen.SetPageSwitcher(this._pageSwitcher);
					screen.ChangeProtocol(this._mailProtocol);
					screen.ChangeDefOrder(this._defOrder);
					break;
				case SCREEN_MESSAGES_LIST:
					screen.SetPageSwitcher(this._pageSwitcher);
					screen.ChangeProtocol(this._mailProtocol);
					screen.ChangeDefOrder(this._defOrder);
					break;
				case SCREEN_NEW_MESSAGE:
					this.SetHtmlEditorField(screen);
					if (null != this._signature && this._idAcct == this._signature.IdAcct) {
						screen.PlaceData(this._signature);
					}
					screen.SetIdAccount(this._idAcct);
					screen.SetFromField(this._fromField);
					break;
				case SCREEN_VIEW_MESSAGE:
					screen.ParseSettings(this.Settings);
					if (!(Browser.IE && Browser.Version > 6)) {
						if (null != this._message)
							screen.PlaceData(this._message);
					};
					if (null != this._messagesList)
						screen.PlaceData(this._messagesList);
					var listScreen = this.Screens[this.ListScreenId];
					if (listScreen) {
						screen.SetTrashParams(listScreen.TrashId, listScreen.TrashFullName, listScreen.Protocol);
					};
					break;
				case SCREEN_USER_SETTINGS:
					screen.PlaceData(this.Accounts);
					this.SetHtmlEditorField(screen);
					if (null != this._signature) {
						screen.PlaceData(this._signature);
					}
					break;
			};
			if (null != this.HistoryArgs && screen.Id == this.HistoryArgs.ScreenId) {
				screen.Show(this.Settings, this.HistoryArgs);
			}
			else {
				screen.Show(this.Settings, null);
			};
			this.HistoryArgs = null;
		}
		else {
			if (!this.isBuilded) {
				this.Hide();
				this._copyright.className = 'wm_hide';
				this.Build();
				this.ShowInfo(Lang.InfoWebMailLoading);
				this.DataSource.onError = ErrorHandler;
				this.DataSource.onGet = TakeDataHandler;
			};
			var sectionId = Screens[screenId].SectionId;
			if (section = this.Sections[sectionId]) {
				var sectionScreens = Sections[sectionId].Screens;
				for (var i in sectionScreens) {
					if (!(screen = this.Screens[i])) {
						eval(sectionScreens[i]);
						if (Screens[screenId].PreRender)
							screen.Build(this._content, this._accountsBar, this._popupMenus, this.Settings);
						this.Screens[i] = screen;
					}
				};
				loadHandler.call(this);
			}
			else {
				this.Sections[sectionId] = true;
				this.ScriptLoader.Load(Sections[sectionId].Scripts, loadHandler);
			}
		}
	},
	
	Show: function ()
	{
		if (!this.shown) {
			this.shown = true;
			this.HideInfo();
			this._content.className = 'wm_content';
		}
	},

	Hide: function ()
	{
		this.shown = false;
		this._content.className = 'wm_hide';
	},
	
	SetHtmlEditorField: function (screen)
	{
		if (this._allowDhtmlEditor) {
			if (null == this._htmlEditorField) {
				this._htmlEditorField = new CHtmlEditorField(false);
			};
			this._htmlEditorScreen = screen.Id;
			this._htmlEditorField.Hide();
			screen.SetHtmlEditorField(this._htmlEditorField);
		}
	},

	BuildAccountsList: function()
	{
		this._accountsBar = CreateChild(this._content, 'table');
		this._accountsBar.className = 'wm_accountslist';
		var tr = this._accountsBar.insertRow(0);
		var td = tr.insertCell(0);

		_replaceDiv = CreateChild(td, 'div');
		_replaceDiv.className = 'wm_accountslist_email';
		_accountNameObject = CreateChild(_replaceDiv, 'a');
		_accountNameObject.href = '#';
		_accountNameObject.onclick = function() {return false;};
		var div = CreateChild(td, 'div');
		div.className = 'wm_accountslist_selection';
		_controlImg = CreateChild(div, 'img');
		_controlImg.className = 'wm_hide';
		_controlImg.src = 'skins/'+this._skinName+'/menu/accounts_arrow.gif';
		var obj = this;
		_controlImg.onmouseover = function() { this.src = 'skins/' + obj._skinName + '/menu/accounts_arrow_over.gif'; };
		_controlImg.onmousedown = function() { this.src = 'skins/' + obj._skinName + '/menu/accounts_arrow_down.gif'; };
		_controlImg.onmouseup = function() { this.src = 'skins/' + obj._skinName + '/menu/accounts_arrow_over.gif'; };
		_controlImg.onmouseout = function() { this.src = 'skins/' + obj._skinName + '/menu/accounts_arrow.gif'; };
		_contactsObj = CreateChild(td, 'div');
		if (this._allowContacts)
			_contactsObj.className = 'wm_accountslist_contacts';
		else
			_contactsObj.className = 'wm_hide';
		var a = CreateChild(_contactsObj, 'a'); a.href = '#'; a.innerHTML = Lang.Contacts;
		WebMail.LangChanger.Register('innerHTML', a, 'Contacts', '');
		a.onclick = function() {
			obj.ShowContacts();
			return false;
		};

		_calendarObj = CreateChild(td, 'div');
		if (this._allowCalendar)
			_calendarObj.className = 'wm_accountslist_contacts';
		else
			_calendarObj.className = 'wm_hide';
		var a = CreateChild(_calendarObj, 'a'); a.href = '#'; a.innerHTML = Lang.Calendar;
		WebMail.LangChanger.Register('innerHTML', a, 'Calendar', '');
		a.onclick = function() {
			obj.ShowCalendar();
			return false;
		};



		div = CreateChild(td, 'div'); div.className = 'wm_accountslist_logout';
		a = CreateChild(div, 'a'); a.href = '#'; a.innerHTML = Lang.Logout;
		WebMail.LangChanger.Register('innerHTML', a, 'Logout', '');
		var obj = this;
		a.onclick = function () {
			obj.LogOut();
			return false;
		};

		var div = CreateChild(td, 'div'); div.className = 'wm_accountslist_settings';
		a = CreateChild(div, 'a'); a.href = '#'; a.innerHTML = Lang.Settings;
		this.LangChanger.Register('innerHTML', a, 'Settings', '');
		a.onclick = function() {
			obj.ShowSettings();
			return false;
		};

		_accountsList = CreateChild(document.body, 'div');
		_accountsList.className = 'wm_hide';
		this._popupMenus.addItem(_accountsList, _controlImg, 'wm_account_menu', _replaceDiv, _replaceDiv, '', '', '', '');
	},
	
	FillAccountsList: function()
	{
		CleanNode(_accountsList);
		var arrAccounts = this.Accounts.Items;
		for(var key in arrAccounts) {
			var Id = arrAccounts[key].Id;
			if (Id != this.Accounts.CurrId) {
				div = CreateChild(_accountsList, 'div');
				div.className = 'wm_account_item';
				div.onmouseover = function() {this.className = 'wm_account_item_over';};
				div.onmouseout = function() {this.className = 'wm_account_item';};
				div.onclick = CreateAccountActionFunc(Id);
				div.innerHTML = arrAccounts[key].Email;
			}
			else {
				var screen = this.Screens[this.ListScreenId];
				if (screen) {
					screen.ChangeProtocol(arrAccounts[key].MailProtocol);
					screen.ChangeDefOrder(arrAccounts[key].DefOrder);
				};
				this._mailProtocol = arrAccounts[key].MailProtocol;
				this._defOrder = arrAccounts[key].DefOrder;
				this._email = arrAccounts[key].Email;
				
				if (arrAccounts[key].UseFriendlyNm && arrAccounts[key].FriendlyName.length > 0)
					this._fromField = '"' + arrAccounts[key].FriendlyName + '" <' + arrAccounts[key].Email + '>';
				else
					this._fromField = arrAccounts[key].Email;
				_accountNameObject.innerHTML = arrAccounts[key].Email;
				var obj = this;
				_accountNameObject.onclick = function () {
					obj.ShowMail();
					return false;
				}
			}
		};
		if (_accountsList.firstChild) {
			_controlImg.className = 'wm_accounts_arrow';
			_accountsList.style.width = 'auto';
		}
		else {
			_controlImg.className = 'wm_hide';
		};
		this._popupMenus.hideAllItems();
	},

	HideInfo: function()
	{
		if (this.shown) {
			if (this._infoCount > 0) {
				this._infoCount--;
			};
			if (this._infoCount == 0) {
				this._infoObj.Hide();
			}
		}
	},
	
	ShowError: function(errorDesc)
	{
		this._errorObj.Show(errorDesc);
		if (this.ScreenId == SCREEN_NEW_MESSAGE) {
			var screen = this.Screens[SCREEN_NEW_MESSAGE];
			if (screen) {
				screen.SetErrorHappen();
			}
		}
	},

	ShowInfo: function(Info)
	{
		if (this.shown) {
			this._infoMessage.innerHTML = Info;
			this._infoObj.Show();
			this._infoCount++;
			this._infoObj.Resize();
		}
	},

	ShowReport: function(report, priorDelay)
	{
		if (this.shown) {
			this._reportObj.Show(report, priorDelay);
		}
	},

	BuildInformation: function()
	{
		var tbl = document.getElementById('info_cont');
		this._infoMessage = document.getElementById('info_message');
		this._infoObj = new CInformation(tbl, 'wm_information');

		this._errorObj = new CError('WebMail._errorObj', this._skinName);
		this._errorObj.Build();
		this._errorObj.SetFade(this._fadeEffect);

		this._reportObj = new CReport('WebMail._reportObj');
		this._reportObj.Build();
		this._reportObj.SetFade(this._fadeEffect);
	}	
};

function CSettingsList()
{
	this.Type = TYPE_SETTINGS_LIST;
	this.ShowTextLabels = false;
	this.AllowChangeSettings = false;
	this.AllowDhtmlEditor = false;
	this.AllowAddAccount = false;
	this.MsgsPerPage = 20;
	this.ContactsPerPage = 20;
	this.DefSkin = 'Hotmail_Style';
	this.DefLang = '';
	this.EnableMailboxSizeLimit = true;
	this.MailBoxLimit = 0;
	this.MailBoxSize = 0;
	this.HideFolders = false;
	this.HorizResizer = 150;
	this.VertResizer = 115;
	this.Mark = MARK_AS_READ;
	this.Reply = REPLY;
	this.ViewMode = VIEW_MODE_WITH_PANE;
	this.FoldersPerPage = 20;
	this.TimeOffset = 0;
	this.DateFormat = '';
	this.TimeFormat = 0;
	this.AllowDirectMode = true;
	this.DirectModeIsDefault = false;
	this.AllowContacts = true;
	this.AllowCalendar = true;
	this.Columns = Array();
	this.UseImapTrash = false;
}

CSettingsList.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('show_text_labels');
		if (attr) this.ShowTextLabels = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('allow_change_settings');
		if (attr) this.AllowChangeSettings = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('allow_dhtml_editor');
		if (attr) this.AllowDhtmlEditor = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('allow_add_account');
		if (attr) this.AllowAddAccount = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('msgs_per_page');
		if (attr) this.MsgsPerPage = attr - 0;

		attr = RootElement.getAttribute('contacts_per_page');
		if (attr) this.ContactsPerPage = attr - 0;

		attr = RootElement.getAttribute('enable_mailbox_size_limit');
		if (attr) this.EnableMailboxSizeLimit = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('mailbox_limit');
		if (attr) this.MailBoxLimit = attr - 0;

		attr = RootElement.getAttribute('mailbox_size');
		if (attr) this.MailBoxSize = attr - 0;

		attr = RootElement.getAttribute('hide_folders');
		if (attr) this.HideFolders = attr - 0;

		attr = RootElement.getAttribute('horiz_resizer');
		if (attr) this.HorizResizer = attr - 0;

		attr = RootElement.getAttribute('vert_resizer');
		if (attr) this.VertResizer = attr - 0;

		attr = RootElement.getAttribute('mark');
		if (attr) this.Mark = attr - 0;
		if (this.Mark < MARK_AS_READ || this.Mark > MARK_ALL_UNREAD)
			this.Mark = MARK_AS_READ;

		attr = RootElement.getAttribute('reply');
		if (attr) this.Reply = attr - 0;
		if (this.Reply != REPLY && this.Reply != REPLY_ALL)
			this.Reply = REPLY;

		attr = RootElement.getAttribute('view_mode');
		if (attr) this.ViewMode = attr - 0;

		attr = RootElement.getAttribute('def_timezone');
		if (attr) this.TimeOffset = attr - 0;

		attr = RootElement.getAttribute('folders_per_page');
		if (attr) this.FoldersPerPage = attr - 0;

		attr = RootElement.getAttribute('allow_direct_mode');
		if (attr) this.AllowDirectMode = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('direct_mode_is_default');
		if (attr) this.DirectModeIsDefault = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('allow_contacts');
		if (attr) this.AllowContacts = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('allow_calendar');
		if (attr) this.AllowCalendar = (attr == 1) ? true : false;

		attr = RootElement.getAttribute('time_format');
		if (attr) this.TimeFormat = attr - 0;

		var settingsParts = RootElement.childNodes;
		var settCount = settingsParts.length;
		for (var i = settCount-1; i >= 0; i--) {
			var part = settingsParts[i].childNodes;
			if (part.length > 0)
				switch (settingsParts[i].tagName) {
					case 'def_skin':
						this.DefSkin = part[0].nodeValue;
						break;
					case 'def_lang':
						this.DefLang = part[0].nodeValue;
						break;
					case 'def_date_fmt':
						this.DateFormat = part[0].nodeValue;
						break;
					case 'columns':
						var jCount = part.length;
						for (var j = jCount-1; j >= 0; j--) {
							if (part[j].tagName == 'column') {
								var id, value;
								attr = part[j].getAttribute('id');
								if (attr) id = attr - 0;
								attr = part[j].getAttribute('value');
								if (attr) value = attr - 0;
								if (id && value) {
									this.Columns[id] = value;
								}
							}
						}//for
					break;
				}//switch
		}//for
	}//GetFromXML
};
