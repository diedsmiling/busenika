/*
 * Classes:
 *  CMessagesListScreen
 */

function CMessagesListScreen(skinName)
{
	this.Id = SCREEN_MESSAGES_LIST;
	this.isBuilded = false;
	this.hasCopyright = true;
	this.BodyAutoOverflow = true;
	this.SearchFormId = 'search_form' + this.Id;
	this.shown = false;

	this._checkMail = new CCheckMail();

	this._showTextLabels = true;
	this._enableMailboxSizeLimit = true;
	this._mailboxLimit = 0;
	this._mailboxSize = 0;
	this._isDisplayFolders = true;
	this._defaultFoldersWidth = 115;
	this._markNumber = MARK_AS_READ;
	this._skinName = skinName;
	this._timeOffset = 0;
	this._dateFormat = '';
	this._messagesPerPage = 20;
	this.isDirectMode = false;
	this._useImapTrash = false;
	
	this._page = 1;
	this._lookForStr = '';
	this._searchMode = 0;
	this._folderId = -1;
	this._folderFullName = '';
	this._sortField = SORT_FIELD_DATE;
	this._sortOrder = SORT_ORDER_DESC;
	this._deleteNumber = DELETE;
	this._removeCount = 0;

	this._SEPARATOR = '#@%';
	this.Protocol = POP3_PROTOCOL;
	this.isInboxDirectMode = false;

	this._mainContainer = null;

	this.SearchFormObj = null;
	this._lookForSmallObj = null;
	this._bigSearchForm = null;
	this._lookForBigObj = null;
	this._searchButtonImg = null;
	this._searchIn = null;
	this._quickSearch = null;
	this._slowSearch = null;

	this._toolBar = null;
	this._pop3DeleteTool = null;
	this._emptyTrashTool = null;
	this._reloadFoldersTool = null;
	this._imap4DeleteTool = null;
	this._markTool = null;
	this._moveTool = null;
	this._moveMenu = null;
	this._inboxMoveItem = null;

	this._foldersContainer = null;
	this._foldersHide = null;
	this._foldersHideImg = null;
	this._foldersList = null;
	this._manageFolders = null;
	this._foldersWidth = this._defaultFoldersWidth;
	this._foldersBordersWidth = 1;
	this._foldersObj = null;
	this._foldersObjWait = null;//folders list, wich was update in manage folders, so it's necessary to fill it
	this._foldersParam = Array();
	this.InboxId = -1;
	this.InboxFullName = '';
	this.SentId = -1;
	this.SentFullName = '';
	this.DraftsId = -1;
	this.DraftsFullName = '';
	this.TrashId = -1;
	this.TrashFullName = '';
	this._currFolder = null;

	this._vResizerCont = null;
	this._vertResizerObj = null;

	this._pageSwitcher = null;

	this._inboxContainer = null;
	this._inboxController = null;
	this._inboxTable = null;
	this._fromToColumn = null;
	this._inboxLines = null;
	this._selection = new CSelection();
	this._dragNDrop = new CDragNDrop('Messages', 'msg_drop.gif', 'msg_not_drop.gif', skinName);
	this._dragNDrop.SetSelection(this._selection);
	this._inboxWidth = 361;
	this._inboxBordersWidth = 1;
	this._messagesObj = null;

	this._messagesInFolder = null;
	this._spaceInfo = null;
	this._spaceProgressBar = null;

	this.PlaceData = function (Data)
	{
		var Type = Data.Type;
		switch (Type) {
			case TYPE_FOLDERS_LIST:
				this.PlaceFoldersList(Data);
				break;
			case TYPE_MESSAGES_LIST:
				this.PlaceMessagesList(Data);
				break;
			case TYPE_MESSAGES_OPERATION:
				this.PlaceMessagesOperation(Data);
				break;
		}
	};
	
	this.ResizeBody = function (mode)
	{
		if (this.isBuilded) {
			if (mode == 'width' && this._isDisplayFolders == true)
				CreateCookie('wm_vert_resizer', this._vertResizerObj._leftPosition, COOKIE_STORAGE_DAYS);

			var resizerWidth = this._vResizerCont.offsetWidth + 2;
			if (resizerWidth == 2) resizerWidth = 6;
			var width = GetWidth();
			var fpWidth = this._foldersWidth;
			if (this._isDisplayFolders == true)
				fpWidth = this._vertResizerObj._leftPosition;
			var ipWidth = width - fpWidth - resizerWidth;
			if (ipWidth < 550) {
				ipWidth = 550;
				fpWidth = width - ipWidth - resizerWidth;
				if (this._isDisplayFolders == true) {
					if (fpWidth < 80) {
						fpWidth = 80;
					}
				}
				else {
					if (fpWidth < 18) {
						fpWidth = 18;
					}
				}
			}
			else {
				fpWidth = width - ipWidth - resizerWidth;
			}
			this._foldersWidth = fpWidth;
			if (this._isDisplayFolders == true) this._defaultFoldersWidth = fpWidth;
			this.ResizeInboxContainerWidth(ipWidth);
			if (this._isDisplayFolders == true)
				this._vertResizerObj._leftPosition = fpWidth;
			this.ResizeFoldersWidth(fpWidth);

			this.UpdateResizerHeight();
			this.ResizeInboxWidth();
			if (null != this._pageSwitcher) this._pageSwitcher.Replace(this._inboxLines);
			this._dragNDrop.Resize();
		}
	};
	
	this.UpdateResizerHeight = function ()
	{
		var foldersHeight = this._foldersContainer.offsetHeight;
		var inboxHeight = this._inboxTable.GetHeight();
		if (foldersHeight && foldersHeight > inboxHeight) {
			this._vertResizerObj.updateVerticalSize(foldersHeight);
		}
		else {
			this._vertResizerObj.updateVerticalSize(inboxHeight);
		}
	};
	
	this.ParseSettings = function (settings)
	{
		this._useImapTrash = settings.UseImapTrash;
		this._showTextLabels = settings.ShowTextLabels;
		this._enableMailboxSizeLimit = settings.EnableMailboxSizeLimit;
		this._mailboxLimit = settings.MailBoxLimit;
		this._mailboxSize = settings.MailBoxSize;
		this._isDisplayFolders = (settings.HideFolders) ? false : true;
		this._defaultFoldersWidth = settings.VertResizer;
		this._markNumber = settings.Mark;
		this.ChangeSkin(settings.DefSkin);
		if (this.isBuilded) {
			this.FillSpaceInfo(settings.MailBoxLimit, settings.MailBoxSize);
		};
		if (this._timeOffset != settings.TimeOffset ||
		this._dateFormat != settings.DateFormat ||
		this._messagesPerPage != settings.MsgsPerPage) {
			if (this._messagesPerPage != settings.MsgsPerPage) {
				this._page = 1;
			};
			this._timeOffset = settings.TimeOffset;
			this._dateFormat = settings.DateFormat;
			this._messagesPerPage = settings.MsgsPerPage;
			if (this.isBuilded && (this._folderId != -1 || this._folderFullName != '')) {
				SetHistoryHandler(
					{
						ScreenId: this.Id,
						FolderId: this._folderId,
						FolderFullName: this._folderFullName,
						Page: this._page,
						SortField: this._sortField,
						SortOrder: this._sortOrder,
						LookForStr: '',
						SearchMode: 0,
						RedrawType: REDRAW_NOTHING,
						RedrawObj: null,
						MsgId: null,
						MsgUid: null,
						MsgFolderId: null,
						MsgFolderFullName: null,
						MsgCharset: null,
						MsgParts: null,
						ForcedRequest: true
					}
				);
			}
			else {
				this.RedrawPages(this._page);
			}
		}
		else {
			this.RedrawPages(this._page);
		}
	};
	
	this.ChangeSkin = function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			if (this.isBuilded) {
				this._toolBar.ChangeSkin(newSkin);
				if (null != this.SearchFormObj) {
					this.SearchFormObj.ChangeSkin(newSkin);
				};
				if (this._isDisplayFolders)
					this._foldersHideImg.src = './skins/' + newSkin + '/folders/hide_folders.gif';
				else
					this._foldersHideImg.src = './skins/' + newSkin + '/folders/show_folders.gif';
				this._searchButtonImg.src = 'skins/' + newSkin + '/menu/search_button_big.gif';
				this.CleanFoldersList();
				this.FillByFolders();
				this._inboxTable.SetSort(this._sortField, this._sortOrder);
				this._inboxTable.ChangeSkin(newSkin);
				this.RedrawPages(this._page);
				this.FillByMessages();
			}
		}
	};
	
	this.Build = function (container, accountsBar, PopupMenus, settings)
	{
		var obj = this;
		this.ParseSettings(settings);
		this._mainContainer = CreateChild(container, 'div');
		this._mainContainer.className = 'wm_hide';

		this.BuildAdvancedSearchForm();
		this.BuildToolBar(PopupMenus);

		var div = CreateChild(this._mainContainer, 'div');
		div.className = 'wm_background';
		var tbl = CreateChild(div, 'table');
		tbl.className = 'wm_mail_container';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		this.BuildFoldersPart(td);

		td = tr.insertCell(1);
		td.rowSpan = 3;
		td.className = 'wm_vresizer_part';
		this._vResizerCont = td;
		var VResizer = CreateChild(td, 'div');
		VResizer.className = 'wm_vresizer';
		div = CreateChild(td, 'div');
		div.className = 'wm_vresizer_width';

		this._inboxContainer = tr.insertCell(2);
		this._inboxContainer.onmousedown = function (ev) {
			if (obj._selection.Length > 0 && isRightClick(ev)) {
				obj._selection.UncheckAll();
			}
			return false;
		};
		this.BuildInboxTable();

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.colSpan = 3;
		td.className = 'wm_lowtoolbar';
		var span = CreateChild(td, 'span');
		this._messagesInFolder = span;
		span.className = 'wm_lowtoolbar_messages';
		this.WriteMsgsCountInFolder(0);
		
		if (this._enableMailboxSizeLimit) {
			this._spaceInfo = CreateChild(td, 'span');
			this._spaceInfo.className = 'wm_lowtoolbar_space_info';
			div = CreateChild(this._spaceInfo, 'div');
			div.className = 'wm_progressbar';
			this._spaceProgressBar = CreateChild(div, 'div');
			this._spaceProgressBar.className = 'wm_progressbar_used';
			this.FillSpaceInfo(settings.MailBoxLimit, settings.MailBoxSize);
		};

		this._vertResizerObj = new CVerticalResizer(VResizer, this._mainContainer, 2, 81, 551, this._defaultFoldersWidth, 'WebMail.ResizeBody(\'width\');');
		if (this._isDisplayFolders == false)
			this._vertResizerObj.free();

		this.isBuilded = true;
	};//Build
	
	this.RedrawFolderControls = function (redrawElement, id, fullName)
	{
		if (redrawElement) {
			if (this._currFolder) this._currFolder.className = '';
			redrawElement.className = 'wm_select_folder';
			this._currFolder = redrawElement;
		};
		if (id && fullName) {
			if (id == -1 && fullName == '') {
				if (this._currFolder) this._currFolder.className = '';
			};
			this.ChangeFolder(id, fullName);
			if (this.IsTrash()) {
				this._emptyTrashTool.className = 'wm_toolbar_item';
			}
			else {
				this._emptyTrashTool.className = 'wm_hide';
			}
		}
	};
	
	this.ChangeFolder = function (id, fullName)
	{
		this._folderId = id;
		this._folderFullName = fullName;
		this._searchIn.value = this._folderId + this._SEPARATOR + this._folderFullName;
	};
	
	this.BuildToolBar = function (PopupMenus)
	{
		var obj = this;
		var toolbar = new CToolBar(this._mainContainer, this._skinName);
		//new message tool
		var item = toolbar.AddItem(TOOLBAR_NEW_MESSAGE, function () { SetHistoryHandler({ ScreenId: SCREEN_NEW_MESSAGE }); }, false);
		//check mail tool
		item = toolbar.AddItem(TOOLBAR_CHECK_MAIL, function () { obj._checkMail.Start(); }, false);
		//reload folders list tool; only imap
		this._reloadFoldersTool = toolbar.AddItem(TOOLBAR_RELOAD_FOLDERS, function () { GetFoldersListHandler(WebMail.Accounts.CurrId, 2); }, true);
		//mark tool; absent in inbox in direct mode in pop3
		this._markTool = toolbar.AddMarkItem(this._markNumber, PopupMenus, true);
		//move to folder tool; absent in inbox in direct mode in pop3
		var div = CreateChild(document.body, 'div');
		this._moveMenu = div;
		div.className = 'wm_hide';
		this._moveTool = toolbar.AddMoveItem(PopupMenus, 'MoveToFolder', div, true, 'move_to_folder.gif', 'wm_menu_move_to_folder_img');
		//delete tools
		this._pop3DeleteTool = toolbar.AddItem(TOOLBAR_DELETE, function () { RequestMessagesOperationHandler(DELETE, [], 0); }, true);
		this._emptyTrashTool = toolbar.AddItem(TOOLBAR_EMPTY_TRASH, function () { RequestMessagesOperationHandler(PURGE, [], 0); }, true);
		this._imap4DeleteTool = toolbar.AddDeleteItem(this._deleteNumber, PopupMenus, true);

		var searchParts = toolbar.AddSearchItems();
		this.SearchFormObj = new CSearchForm(this._bigSearchForm, searchParts.SmallForm, searchParts.Control, searchParts.ControlImg, this.SearchFormId, this._lookForBigObj, searchParts.LookFor, this._skinName);
		if (null != this._searchIn) {
			this.SearchFormObj.SetSearchIn(this._searchIn);
		};
		this._lookForSmallObj = searchParts.LookFor;
		var obj = this;
		searchParts.LookFor.onkeypress = function (ev) {
			if (isEnter(ev)) {
				obj.GetSearchResultsFromSmall();
			}
		};
		searchParts.ActionImg.onclick = function () {
			obj.GetSearchResultsFromSmall();
		};
		this._toolBar = toolbar;
	}
};

MessagesListPrototype = {
	GetSortField: function (folderId, folderFullName)
	{
		if (this._sortField == SORT_FIELD_FROM && (this.DraftsId == folderId && this.DraftsFullName == folderFullName ||
		 this.SentId == folderId && this.SentFullName == folderFullName)) {
			return SORT_FIELD_TO;
		};
		if (this._sortField == SORT_FIELD_TO && (this.DraftsId != folderId || this.DraftsFullName != folderFullName) &&
		 (this.SentId != folderId || this.SentFullName != folderFullName)) {
			return SORT_FIELD_FROM;
		};
		return this._sortField;
	},
	
	PlaceFoldersList: function (Data)
	{
		this._foldersObj = Data;
		if (this.shown) {
			this.CleanFoldersList();
			this._foldersParam = Array();
			this.FillByFolders();
			if (Data.Sync != 2) {
				if (Data.Sync == 1) {
					GetMessagesListHandler(REDRAW_NOTHING, null, this._folderId, this._folderFullName, this.GetSortField(this._folderId, this._folderFullName), this._sortOrder, this._page, this._lookForStr, this._searchMode);
				}
				else if (this._lookForStr.length == 0) {
					SetHistoryHandler(
						{
							ScreenId: this.Id,
							FolderId: this._folderId,
							FolderFullName: this._folderFullName,
							Page: this._page,
							SortField: this.GetSortField(this._folderId, this._folderFullName),
							SortOrder: this._sortOrder,
							LookForStr: '',
							SearchMode: 0,
							RedrawType: REDRAW_NOTHING,
							RedrawObj: null,
							MsgId: null,
							MsgUid: null,
							MsgFolderId: null,
							MsgFolderFullName: null,
							MsgCharset: null,
							MsgParts: null
						}
					);
				}
			};
			this.ResizeBody('all');
		}
		else {
			//if screen is hidden, folders list is saving 
			this._foldersObjWait = this._foldersObj;
		}
	},
	
	PlaceMessagesList: function (Data)
	{
		this._messagesObj = Data;
		if (this.shown) {
			this._sortField = Data.SortField;
			this._sortOrder = Data.SortOrder;
			this._lookForStr = Data._lookFor;
			this._searchMode = Data._searchFields;
			this._page = Data.Page;
			if (this._folderId != Data.FolderId || this._folderFullName != Data.FolderFullName) {
				var paramIndex = Data.FolderId + Data.FolderFullName;
				var params = this._foldersParam[paramIndex];
				if(params) {
					params.ChangeMsgsCounts(Data.MessagesCount, Data.NewMsgsCount);
					this.ChangeCurrFolder(Data.FolderId, Data.FolderFullName, params._div, Data.MessagesCount, params._syncType, params._type);
				}
				else {
					this.RedrawFolderControls(null, Data.FolderId, Data.FolderFullName);
					this.WriteMsgsCountInFolder(Data.MessagesCount);
				}
			}
			else {
				var paramIndex = this._folderId + this._folderFullName;
				var params = this._foldersParam[paramIndex];
				if(params) {
					params.SetPage(Data.Page);
					params.ChangeMsgsCounts(Data.MessagesCount, Data.NewMsgsCount);
					this.RedrawFolderControls(params._div, Data.FolderId, Data.FolderFullName);
					this.WriteMsgsCountInFolder(Data.MessagesCount);
				}
				else {
					this.WriteMsgsCountInFolder(Data.MessagesCount);
				}
			};
			this.FillByMessages();
			this.RepairToolBar();
		}
	},

	PlaceMessagesOperation: function (Data)
	{
		if (this.shown) {
			var fId = this._folderId; var fName = this._folderFullName;
			var params = this._foldersParam[fId + fName];
			if (!params && this._lookForStr.length == 0) {
				var dict = Data.Messages;
				var keys = dict.keys();
				if (keys.length == 1) {
					var folder = dict.getVal(keys[0]);
					fId = folder.FolderId; fName = folder.FolderFullName;
					params = this._foldersParam[fId + fName];
				}
			};
			if (params) {
				switch (Data.OperationInt) {
					case MARK_ALL_READ:
					case MARK_AS_READ:
						params.Read();
						break;
					case MARK_ALL_UNREAD:
					case MARK_AS_UNREAD:
						params.Unread();
						break;
					case MOVE_TO_FOLDER:
						params.Remove();
						var paramIndex = Data.ToFolderId + Data.ToFolderFullName;
						if (this._foldersParam[paramIndex]) {
							this._foldersParam[paramIndex].Append();
						};
						if (null != this._pageSwitcher) {
							var page = this._pageSwitcher.GetLastPage(this._removeCount);
							if (page < this._page)
								this._page = page;
						};
						break;
					case DELETE:
						if (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash) {
							params.Remove();
							var paramIndexT = this.TrashId + this.TrashFullName;
							if (this._foldersParam[paramIndexT]) {
								this._foldersParam[paramIndexT].Append();
							}
							if (null != this._pageSwitcher) {
								var page = this._pageSwitcher.GetLastPage(this._removeCount);
								if (page < this._page)
									this._page = page;
							}
						}
						break;
					case PURGE:
						if (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash) {
							params.ChangeMsgsCounts(0, 0);
						}
						break;
				};
				WebMail.DataSource.Cache.SetMessagesCount(fId, fName, params.MsgsCount, params._newMsgsCount);
			}
			else if (this._lookForStr.length > 0) {
				WebMail.DataSource.Cache.ClearMessagesList(fId, fName);
				GetFoldersListHandler(WebMail._idAcct, 0);
			};
					
			if (Data.OperationInt == PURGE && (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash) && this.IsTrash()) {
				this.CleanInboxLines(Lang.InfoEmptyFolder);
				this.WriteMsgsCountInFolder(0);
			}
			else if (Data.OperationInt == PURGE && this.Protocol == IMAP4_PROTOCOL && !this._useImapTrash ||
			 Data.OperationInt == MOVE_TO_FOLDER || Data.OperationInt == DELETE &&
			 (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash)) {
				GetMessagesListHandler(REDRAW_NOTHING, null, this._folderId, this._folderFullName, this._sortField, this._sortOrder, this._page, this._lookForStr, this._searchMode);
			}
			else {
				if (Data.OperationField != '') {
					var dict = Data.Messages;
					var keys = dict.keys();
					var idArray = Array();
					for (var i in keys) {
						var folder = dict.getVal(keys[i]);
						for (var j in folder.IdArray) {
							var msg = folder.IdArray[j];
							var msgH = new CMessageHeaders();
							msgH.Id = msg.Id;
							msgH.Uid = msg.Uid;
							msgH.FolderId = folder.FolderId;
							msgH.FolderFullName = folder.FolderFullName;
							idArray.push(msgH.GetIdForList(this._SEPARATOR, this.Id));
						}
					};
					this._selection.SetParams(idArray, Data.OperationField, Data.OperationValue, Data.isAllMess);
				}
			}
		}
	},

	Show: function (settings, historyArgs)
	{
		this.shown = true;
		this._mainContainer.className = '';
		if (null != this.SearchFormObj) {
			this.SearchFormObj.Show();
		};
		this.ParseSettings(settings);
		this.ResizeBody('all');
		if (null != historyArgs) {
			this.RestoreFromHistory(historyArgs);
		};
		if (this._foldersObj == null && -1 != WebMail._idAcct) {
			GetFoldersListHandler(WebMail._idAcct, 0);
		};
		if (null != this._foldersObjWait) {
			this.PlaceFoldersList(this._foldersObjWait);
			this._foldersObjWait = null; 
		};
		if (this._showTextLabels) {
			this._toolBar.ShowTextLabels();
		}
		else {
			this._toolBar.HideTextLabels();
		};
		if (this.Id == SCREEN_MESSAGES_LIST_VIEW) {
			if (this._allowContacts) {
				this._addToAddressBookImg.className = 'wm_add_address_book_img';
			}
			else {
				this._addToAddressBookImg.className = 'wm_hide';
			}
		}
	},
	
	FolderClick: function (id, fullName, newFolder)
	{
		var paramIndex = id + fullName;
		var params = this._foldersParam[paramIndex];
		if (params) {
			if (params.SentDraftsType) {
				this._fromToColumn.ChangeField(IH_TO, InboxHeaders[IH_TO], this._skinName);
			}
			else {
				this._fromToColumn.ChangeField(IH_FROM, InboxHeaders[IH_FROM], this._skinName);
			};
			SetHistoryHandler(
				{
					ScreenId: this.Id,
					FolderId: id,
					FolderFullName: fullName,
					Page: params.Page,
					SortField: this.GetSortField(id, fullName),
					SortOrder: this._sortOrder,
					LookForStr: '',
					SearchMode: 0,
					RedrawType: REDRAW_FOLDER,
					RedrawObj: newFolder,
					MsgId: null,
					MsgUid: null,
					MsgFolderId: null,
					MsgFolderFullName: null,
					MsgCharset: null,
					MsgParts: null
				}
			);
		}
	},
	
	RestoreFromHistory: function (args)
	{
		if (null != args) {
			if (args.AcctChanged) {
				this._folderId = -1;
				this._folderFullName = '';
				this.InboxId = -1;
				this.InboxFullName = '';
				this.SentId = -1;
				this.SentFullName = '';
				this.DraftsId = -1;
				this.DraftsFullName = '';
				this.TrashId = -1;
				this.TrashFullName = '';
				this._page = 1;
				this._messagesObj = null;
				this._foldersObjWait = null;
				this.CleanInboxLines(Lang.InfoMessagesLoad);
				if (this.Id == SCREEN_MESSAGES_LIST_VIEW) {
					this.CleanMessageBody(true);
				};
				GetFoldersListHandler(args.IdAcct, 0);
			}
			else {
				var needMsg = null != args.MsgId && null != args.MsgUid && null != args.MsgFolderId &&
				 null != args.MsgFolderFullName && null != args.MsgCharset && null != args.MsgParts;
				if (null == args.FolderId && (this._folderId != -1 || this._lookForStr.length != 0)) {
					if (!needMsg && this.Id == SCREEN_MESSAGES_LIST_VIEW) {
						this.CleanMessageBody(false);
					}
					GetMessagesListHandler(REDRAW_NOTHING, null, this._folderId, this._folderFullName, this._sortField, this._sortOrder, this._page, this._lookForStr, this._searchMode);
				}
				else if (args.ForcedRequest || null == this._messagesObj || this._folderId != args.FolderId || this._folderFullName != args.FolderFullName ||
				 this._sortField != args.SortField || this._sortOrder != args.SortOrder || this._page != args.Page ||
				 this._lookForStr != args.LookForStr || this._searchMode != args.SearchMode) {
				 	if (args.FolderId != null && args.FolderId != -1 || args.LookForStr != null && args.LookForStr.length != 0) {
						var paramIndex = args.FolderId + args.FolderFullName;
						var params = this._foldersParam[paramIndex];
						if (params) {
							this.ChangeCurrFolder(args.FolderId, args.FolderFullName, args.RedrawObj, params.MsgsCount, params._syncType, params._type)
						};
						if (!needMsg && this.Id == SCREEN_MESSAGES_LIST_VIEW) {
							this.CleanMessageBody(false);
						}
						GetMessagesListHandler(args.RedrawType, args.RedrawObj, args.FolderId, args.FolderFullName, args.SortField, args.SortOrder, args.Page, args.LookForStr, args.SearchMode);
				 	}
				};
				if (needMsg) {
					GetMessageHandler(args.MsgId, args.MsgUid, args.MsgFolderId, args.MsgFolderFullName, args.MsgParts, args.MsgCharset);
				}
			}
		}
	},
	
	Hide: function ()
	{
		this.shown = false;
		this._mainContainer.className = 'wm_hide';
		if (null != this.SearchFormObj) {
			this.SearchFormObj.Hide();
		};
		if (null != this._pageSwitcher) this._pageSwitcher.Hide();
	},

	ShowFolders: function ()
	{
		var obj = this;
		this._foldersWidth = this._defaultFoldersWidth;
		this._isDisplayFolders = true;
		CreateCookie('wm_hide_folders', 0, COOKIE_STORAGE_DAYS);
		this._foldersHideImg.src = './skins/' + this._skinName + '/folders/hide_folders.gif';
		this._foldersHideImg.title = Lang.HideFolders;
		this._foldersList.className = 'wm_folders';
		this._manageFolders.className = 'wm_manage_folders';
		this._foldersHide.onclick = function() { obj.HideFolders(); };
		this._vertResizerObj.busy(this._foldersWidth);
		this.ResizeBody('width');
	},
	
	HideFolders: function ()
	{
		var obj = this;
		this._foldersWidth = 18;
		this._isDisplayFolders = false;
		CreateCookie('wm_hide_folders', 1, COOKIE_STORAGE_DAYS);
		this._foldersHideImg.src = './skins/' + this._skinName + '/folders/show_folders.gif';
		this._foldersHideImg.title = Lang.ShowFolders;
		if (SCREEN_MESSAGES_LIST_VIEW == this.Id) {
			this._foldersList.className = 'wm_hide';
		}
		else {
			this._foldersList.className = 'wm_unvisible';
		}
		this._manageFolders.className = 'wm_hide';
		this._foldersHide.onclick = function() { obj.ShowFolders(); };
		this._vertResizerObj.free();
		this.ResizeBody('width');
	},
	
	ClickBody: function(ev)
	{
		if (null != this.SearchFormObj) {
			this.SearchFormObj.checkVisibility(ev, Browser.Mozilla);
		}
	},

	ResizeFoldersWidth: function (width)
	{
		var validator = new CValidate();
		if (validator.IsPositiveNumber(width) && width >=18) {
			this._foldersWidth = width;
			var innerWidth = this._foldersWidth - this._foldersBordersWidth;
			this._foldersContainer.style.width = innerWidth + 'px';
			this._foldersList.style.width = innerWidth + 'px';
			this._foldersHide.style.width = innerWidth + 'px';
			this._manageFolders.style.width = innerWidth + 'px';
		}
	},
	
	ResizeInboxContainerWidth: function (width)
	{
		var validator = new CValidate();
		if (validator.IsPositiveNumber(width) && width >=400) {
			this._inboxWidth = width;
			this._inboxContainer.style.width = width + 'px';
		}
	},
	
	ResizeInboxWidth: function ()
	{
		var offsetWidth;
		if (offsetWidth = this._inboxContainer.offsetWidth) {
			var width = this._inboxWidth - this._inboxBordersWidth;
			if (offsetWidth > width) {
				this._inboxTable.Resize(width);
			}
			else {
				this._inboxTable.Resize(offsetWidth);
			}
		}
	},
	
	IsInbox: function ()
	{
		return (this.InboxId == this._folderId && this.InboxFullName == this._folderFullName);
	},
	
	IsSent: function ()
	{
		return (this.SentId == this._folderId && this.SentFullName == this._folderFullName);
	},

	IsDrafts: function ()
	{
		return (this.DraftsId == this._folderId && this.DraftsFullName == this._folderFullName);
	},
	
	IsTrash: function ()
	{
		return (this.TrashId == this._folderId && this.TrashFullName == this._folderFullName);
	},

	CleanFoldersList: function ()
	{
		CleanNode(this._foldersList);
		CleanNode(this._moveMenu);
		this._inboxMoveItem = null;
		CleanNode(this._searchIn);
		var option = CreateChild(this._searchIn, 'option');
		option.value = '-1' + this._SEPARATOR;
		option.innerHTML = Lang.AllMailFolders;
		if (null != this.SearchFormObj && this.SearchFormObj.isShown == 0) {
			this._searchIn.className = 'wm_hide';
		}
	},
	
	CleanInboxLines: function (msg)
	{
		this._inboxTable.CleanLines(msg);
		if (null != this._pageSwitcher) this._pageSwitcher.Hide();
	},

	RedrawControls: function (redrawIndex, redrawElement, sortField, sortOrder, page)
	{
		switch (redrawIndex - 0) {
			case REDRAW_FOLDER:
				this.RedrawFolderControls(redrawElement);
				this._inboxTable.SetSort(sortField, sortOrder);
				this.RedrawPages(page);
				break;
			case REDRAW_HEADER:
				this._inboxTable.SetSort(sortField, sortOrder);
				break;
			case REDRAW_PAGE:
				this.RedrawPages(page);
				break;
		};
		this.CleanInboxLines(Lang.InfoMessagesLoad);
	},
	
	SetPageSwitcher: function (pageSwitcher)
	{
		this._pageSwitcher = pageSwitcher;
		this._pageSwitcher.Hide();
	},
	
	ChangeProtocol: function (protocol)
	{
		this.Protocol = protocol;
		this.RepairToolBar();
	},
	
	RepairToolBar: function ()
	{
		if (this.isBuilded) {
			if (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash) {
				this._pop3DeleteTool.className = 'wm_toolbar_item';
				this._imap4DeleteTool.className = 'wm_hide';
				if (this.IsTrash()) {
					this._emptyTrashTool.className = 'wm_toolbar_item';
				}
				else {
					this._emptyTrashTool.className = 'wm_hide';
				}
			}
			else {
				this._pop3DeleteTool.className = 'wm_hide';
				this._emptyTrashTool.className = 'wm_hide';
				this._imap4DeleteTool.className = 'wm_tb';
			}
			if (this.Protocol != IMAP4_PROTOCOL) {
				if (this.isInboxDirectMode) {
					if (this.IsInbox()) {
						this._markTool.className = 'wm_hide';
						if (null != this.SearchFormObj) {
							this.SearchFormObj.Hide();
						}
						if (null != this._inboxMoveItem) this._inboxMoveItem.className = 'wm_menu_item';
					}
					else {
						this._markTool.className = 'wm_tb';
						if (null != this.SearchFormObj) {
							this.SearchFormObj.Show();
						}
						if (null != this._inboxMoveItem) this._inboxMoveItem.className = 'wm_hide';
					}
					this._moveTool.className = 'wm_toolbar_item';
					this._dragNDrop.SetMoveToInbox(false);
				}
				else {
					this._markTool.className = 'wm_tb';
					this._moveTool.className = 'wm_toolbar_item';
					if (null != this.SearchFormObj) {
						this.SearchFormObj.Show();
					}
					if (null != this._inboxMoveItem) this._inboxMoveItem.className = 'wm_menu_item';
					this._dragNDrop.SetMoveToInbox(true);
				}
				this._reloadFoldersTool.className = 'wm_hide';
			}
			else {
				if (null != this.SearchFormObj && this.shown) {
					if (this.isDirectMode) {
						this.SearchFormObj.Hide();
					}
					else {
						this.SearchFormObj.Show();
					}
				};
				this._markTool.className = 'wm_tb';
				this._moveTool.className = 'wm_toolbar_item';
				this._reloadFoldersTool.className = 'wm_toolbar_item';
			}
			if (this.Id == SCREEN_MESSAGES_LIST_VIEW) {
				if (this.IsDrafts()) {
					this._replyTool.className = 'wm_hide';
					this._forwardTool.className = 'wm_hide';
				}
				else {
					this._replyTool.className = 'wm_tb';
					this._forwardTool.className = 'wm_toolbar_item';
				}
			}
			if (this._folderId == -1 && this._folderFullName.length == 0 && this._lookForStr.length > 0) {
				this._toolBar.DisableInSearch(true);
			}
			else {
				this._toolBar.DisableInSearch(false);
			}
		}
	},

	ChangeDefOrder: function (defOrder)
	{
		if((defOrder % 2) == 1) {
			this._sortField = defOrder - SORT_ORDER_ASC;
			this._sortOrder = SORT_ORDER_ASC;
		}
		else {
			this._sortField = defOrder;
			this._sortOrder = SORT_ORDER_DESC;
		}
	},
	
	ChangeCurrFolder: function (id, fullName, div, count, syncType, type)
	{
		if (count == 0) {
			if (this._lookForStr.length > 0) {
				this.CleanInboxLines(Lang.InfoNoMessagesFound);
			}
			else {
				this.CleanInboxLines(Lang.InfoEmptyFolder);
			}
		};
		if (div) {
			this.RedrawFolderControls(div, id, fullName);
		}
		else {
			this.ChangeFolder(id, fullName);
		};
		this.WriteMsgsCountInFolder(count);
		if (syncType == SYNC_TYPE_DIRECT_MODE) {
			this._inboxTable.FreeSort();
			this.isDirectMode = true;
		}
		else {
			if (count > 0) {
				this._inboxTable.UseSort();
			}
			else {
				this._inboxTable.FreeSort();
			};
			this.isDirectMode = false;
		};
		this.RepairToolBar();
	},
	
	WriteMsgsCountInFolder: function (count)
	{
		if (this._lookForStr.length > 0) {
			this._messagesInFolder.innerHTML = count + ' ' + Lang.Messages;
		}
		else {
			this._messagesInFolder.innerHTML = count + ' ' + Lang.MessagesInFolder;
		}
	},
	
	FillSpaceInfo: function (limit, size)
	{
		if (!this._enableMailboxSizeLimit) return;
		if (limit > 0) {
			var percent = Math.round(size / limit * 100);
		}
		else {
			var percent = 0;
		};
		if (percent > 100) percent = 100;
		if (percent < 0) percent = 0;
		this._spaceInfo.title = Lang.YouUsing + ' ' + percent + '% ' + Lang.OfYour + ' ' + GetFriendlySize(limit);
		this._spaceProgressBar.style.width = percent + 'px';
	},
	
	GetSearchResultsFromBig: function()
	{
		var searchIn = this._searchIn.value.split(this._SEPARATOR);
		if (this._quickSearch.checked) {
			var sMode = 0;
		}
		else {
			var sMode = 1;
		};
		var sStr = this._lookForBigObj.value;
		if (sStr.length == 0 && searchIn[0] == -1) {
			if (this._folderId != -1) {
				searchIn[0] = this._folderId;
				searchIn[1] = this._folderFullName;
			}
			else {
				searchIn[0] = this.InboxId;
				searchIn[1] = this.InboxFullName;
			};
			var paramIndex = searchIn[0] + searchIn[1];
			var params = this._foldersParam[paramIndex];
			if(params) {
				var redrawType = REDRAW_FOLDER;
				var redrawObj = params._div;
			}
		}
		else {
			var redrawType = REDRAW_NOTHING;
			var redrawObj = null;
		};
		SetHistoryHandler(
			{
				ScreenId: this.Id,
				FolderId: searchIn[0],
				FolderFullName: searchIn[1],
				Page: 1,
				SortField: this._sortField,
				SortOrder: this._sortOrder,
				LookForStr: sStr,
				SearchMode: sMode,
				RedrawType: redrawType,
				RedrawObj: redrawObj,
				MsgId: null,
				MsgUid: null,
				MsgFolderId: null,
				MsgFolderFullName: null,
				MsgCharset: null,
				MsgParts: null
			}
		);
		if (null != this.SearchFormObj) {
			this.SearchFormObj.HideBigForm();
		}
	},
	
	GetSearchResultsFromSmall: function ()
	{
		this._lookForBigObj.value = this._lookForSmallObj.value;
		this.GetSearchResultsFromBig();
	},
	
	GetXmlMessagesOperation: function (type, idArray, toFolderId, toFolderFullName)
	{
		var xml = '';
		if (type == MOVE_TO_FOLDER){
			if (toFolderId == this._folderId && toFolderFullName == this._folderFullName) return false;
		}
		else {
			var toFolderId = -1;
			var toFolderFullName = '';
		};
		var unreaded = 0;
		var messagesXml = '<messages>';
		messagesXml += '<look_for fields="' + this._searchMode + '">' + GetCData(this._lookForStr) + '</look_for>';
		messagesXml += '<to_folder id="' + toFolderId + '"><full_name>' + GetCData(toFolderFullName) + '</full_name></to_folder>';
		messagesXml += '<folder id="' + this._folderId + '"><full_name>' + GetCData(this._folderFullName) + '</full_name></folder>';
		if (type == MARK_ALL_READ || type == MARK_ALL_UNREAD || type == PURGE) {
			xml = messagesXml + '</messages>';
		}
		else {
			if (this._selection.Length > 0) {
				if (idArray.length == 0) {
					var res = this._selection.GetCheckedLines();
					idArray = res.IdArray;
					unreaded = res.Unreaded;
				};
				var moveMessage = false;
				for (var i in idArray) {
					var msg = new CMessage();
					msg.GetFromIdForList(this._SEPARATOR, idArray[i]);
					xml += '<message id="' + msg.Id + '">';
					xml += '<uid>' + GetCData(HtmlDecode(msg.Uid)) + '</uid>';
					xml += '<folder id="' + msg.FolderId + '"><full_name>' + GetCData(msg.FolderFullName) + '</full_name></folder>';
					xml += '</message>';
					if (this.Id == SCREEN_MESSAGES_LIST_VIEW) {
						if (null != this._msgObj && this._msgObj.Id == msg.Id &&
						this._msgObj.Uid == msg.Uid &&
						this._msgObj.FolderId == msg.FolderId &&
						this._msgObj.FolderFullName == msg.FolderFullName) {
							moveMessage = true;//message in preview pane is message for operation
						}
					}
				};
				if (xml != '') {
					xml = messagesXml + xml + '</messages>';
				}
			}
		}
		if (xml == '') {
			alert(Lang.WarningMarkListItem);
			return false;
		}
		var count = idArray.length;
		if (type == MOVE_TO_FOLDER){
			var paramIndex = toFolderId + toFolderFullName;
			if (this._foldersParam[paramIndex]) {
				this._foldersParam[paramIndex].AddToAppend(count, unreaded);
			}
		};
		var paramIndex = this._folderId + this._folderFullName;
		if (this._foldersParam[paramIndex]) {
			switch (type) {
				case MARK_ALL_READ:
					this._foldersParam[paramIndex].AddAllToRead();
					break;
				case MARK_ALL_UNREAD:
					this._foldersParam[paramIndex].AddAllToUnread();
					break;
				case MARK_AS_READ:
					this._foldersParam[paramIndex].AddToRead(unreaded);
					break;
				case MARK_AS_UNREAD:
					this._foldersParam[paramIndex].AddToUnread(count - unreaded);
					break;
				case MOVE_TO_FOLDER:
					this._foldersParam[paramIndex].AddToRemove(count, unreaded);
					this._removeCount = count;
					break;
				case DELETE:
					if (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash) {
						this._foldersParam[paramIndex].AddToRemove(count, unreaded);
						this._removeCount = count;
						paramIndexT = this.TrashId + this.TrashFullName;
						if (this._foldersParam[paramIndexT]) {
							this._foldersParam[paramIndexT].AddToAppend(count, unreaded);
						}
					}
					break;
			}
		};
		if (xml.length > 0) {
			if (type == DELETE && this.Protocol != IMAP4_PROTOCOL && this.IsInbox() && this.isInboxDirectMode) {
				if (!confirm(Lang.ConfirmDirectModeAreYouSure)) xml = '';
			}
			if (type == PURGE || type == DELETE && (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash) && this.IsTrash()) {
				if (!confirm(Lang.ConfirmAreYouSure)) xml = '';
			}
			if (xml.length > 0) {
				WebMail.DataSource.Request({action: 'operation_messages', request: OperationTypes[type]}, xml);
				if (this.Id == SCREEN_MESSAGES_LIST_VIEW && ((type == MOVE_TO_FOLDER || 
				type == DELETE && (this.Protocol != IMAP4_PROTOCOL || this._useImapTrash)) && moveMessage ||
				type == PURGE)) {
					//message in preview pane will remove
					this.CleanMessageBody(true);
				}
			}
		}
	},
	
	FillByFolders: function ()
	{
		this._dragNDrop.CleanDropObjects();
		var arFolders = this._foldersObj.Folders;
		for (var key in arFolders) {
			var thisFolder = arFolders[key];
			if (!thisFolder.ListHide) {
				var intIndent = thisFolder.Level * FOLDERS_TREES_INDENT;
				var strIndent = '';
				for (var j=0; j<thisFolder.Level; j++) strIndent += '&nbsp;&nbsp;&nbsp;&nbsp;';
				if (this._folderId == -1 && thisFolder.Type == FOLDER_TYPE_INBOX && this._lookForStr.length == 0) {
					this.ChangeFolder(thisFolder.Id, thisFolder.FullName);
				}
				if (this.InboxId == -1 && thisFolder.Type == FOLDER_TYPE_INBOX) {
					this.InboxId = thisFolder.Id;
					this.InboxFullName = thisFolder.FullName;
					if (thisFolder.SyncType == SYNC_TYPE_DIRECT_MODE) {
						this.isInboxDirectMode = true;
					}
					else {
						this.isInboxDirectMode = false;
					}
				}
				if (this.SentId == -1 && thisFolder.Type == FOLDER_TYPE_SENT) {
					this.SentId = thisFolder.Id;
					this.SentFullName = thisFolder.FullName;
				}
				if (this.DraftsId == -1 && thisFolder.Type == FOLDER_TYPE_DRAFTS) {
					this.DraftsId = thisFolder.Id;
					this.DraftsFullName = thisFolder.FullName;
				}
				if (this.TrashId == -1 && thisFolder.Type == FOLDER_TYPE_TRASH) {
					this.TrashId = thisFolder.Id;
					this.TrashFullName = thisFolder.FullName;
				}

				var fName = thisFolder.Name;
				switch (thisFolder.Type) {
					case FOLDER_TYPE_INBOX:
						fName = Lang.FolderInbox;
					break;
					case FOLDER_TYPE_SENT:
						fName = Lang.FolderSentItems;
					break;
					case FOLDER_TYPE_DRAFTS:
						fName = Lang.FolderDrafts;
					break;
					case FOLDER_TYPE_TRASH:
						fName = Lang.FolderTrash;
					break;
				}
				
				if (thisFolder.SyncType != SYNC_TYPE_DIRECT_MODE || this.Protocol != POP3_PROTOCOL) {
					var item = CreateChild(this._moveMenu, 'div');
					item.onmouseover = function() {this.className = "wm_menu_item_over";};
					item.onmouseout = function() {this.className = "wm_menu_item";};
					item.onclick = CreateMoveActionFunc(thisFolder.Id, thisFolder.FullName);
					item.className = 'wm_menu_item';
					item.innerHTML = strIndent + fName;
					if (thisFolder.Id == this.InboxId && thisFolder.FullName == this.InboxFullName) {
						this._inboxMoveItem = item;
					}
	
					var option = CreateChild(this._searchIn, 'option');
					option.innerHTML = strIndent + fName;
					option.value = thisFolder.Id + this._SEPARATOR + thisFolder.FullName;
				}

				var paramIndex = thisFolder.Id + thisFolder.FullName;
				var params = this._foldersParam[paramIndex];
				if (!params) {
					params = new CFolderParams(thisFolder.SentDraftsType, thisFolder.Type, thisFolder.SyncType, thisFolder.MsgCount, thisFolder.NewMsgCount, fName);
					if (this.Protocol == IMAP4_PROTOCOL) {
						params.ChangeImgType();
					}
				}
				
				var div = CreateChild(this._foldersList, 'div');
				div.style.paddingLeft = intIndent + 'px';
				var obj = this;
				var clickHandler = CreateFolderClickFunc(thisFolder.Id, thisFolder.FullName, obj, div);
				params.SetDiv(div, this._skinName, clickHandler);
				if (this._folderId == thisFolder.Id && this._folderFullName == thisFolder.FullName) {
					this.ChangeCurrFolder(thisFolder.Id, thisFolder.FullName, div, params.MsgsCount, thisFolder.SyncType, thisFolder.Type);
				}
				div.id = thisFolder.Id + this._SEPARATOR + thisFolder.FullName;
				if (thisFolder.Type == FOLDER_TYPE_INBOX) {
					this._dragNDrop.SetInboxId(div.id);
				}
				this._dragNDrop.AddDropObject(div);
				this._foldersParam[paramIndex] = params;
			}
		}//for
		this.RepairToolBar();
		if (null != this.SearchFormObj && this.SearchFormObj.isShown == 0) {
			this._searchIn.className = 'wm_hide';
		}
	},//FillByFolders
	
	FillByMessages: function ()
	{
		var msgsArray = new Array();
		var msgsObj = this._messagesObj;
		if (msgsObj != null) {
			msgsArray = msgsObj.List;
		}
		if (msgsArray.length == 0) {
			if (this._lookForStr.length > 0) {
				this.CleanInboxLines(Lang.InfoNoMessagesFound);
			}
			else {
				this.CleanInboxLines(Lang.InfoEmptyFolder);
			}
		}
		else {
			this._inboxTable.SetSort(this._sortField, this._sortOrder);
			var doFlag = !(this.Protocol == POP3_PROTOCOL && this.isInboxDirectMode == true && this.IsInbox());
			this._inboxController.SetDoFlag(doFlag);
			this._inboxTable.Fill(msgsArray, this._SEPARATOR, this.Id);
			this.RedrawPages(this._page);
			if (this.Id == SCREEN_MESSAGES_LIST_VIEW) {
				if (this.IsDrafts()) {
					this._replyTool.className = 'wm_hide';
					this._forwardTool.className = 'wm_hide';
				}
				else {
					this._replyTool.className = 'wm_tb';
					this._forwardTool.className = 'wm_toolbar_item';
				}
				if (null != this._pageSwitcher) this._pageSwitcher.Replace(this._inboxLines);
				this.ResizeInboxWidth();
			}
			else {
				WebMail.ResizeBody('all');
			}
		}
		if (this.Id == SCREEN_MESSAGES_LIST)
			this.UpdateResizerHeight();
	},//FillByMessages
	
	GetPage: function (page)
	{
		SetHistoryHandler( { ScreenId: this.Id,
			FolderId: this._folderId,
			FolderFullName: this._folderFullName,
			Page: page,
			SortField: this._sortField,
			SortOrder: this._sortOrder,
			LookForStr: this._lookForStr,
			SearchMode: this._searchMode,
			RedrawType: REDRAW_PAGE,
			RedrawObj: null,
			MsgId: null,
			MsgUid: null,
			MsgFolderId: null,
			MsgFolderFullName: null,
			MsgCharset: null,
			MsgParts: null } 
		);
	},
	
	RedrawPages: function (page)
	{
		if (this._messagesObj && this._pageSwitcher) {
			var perPage = this._messagesPerPage;
			var count = this._messagesObj.MessagesCount;
			if (this.shown && null != this._pageSwitcher) {
				this._pageSwitcher.Show(page, perPage, count, 'GetPageMessagesHandler(', ');');
				this._pageSwitcher.Replace(this._inboxLines);
			}
		}
	},
	
	HeaderClickFunc: function (sortField, sortOrder)
	{
		SetHistoryHandler(
			{
				ScreenId: this.Id,
				FolderId: this._folderId,
				FolderFullName: this._folderFullName,
				Page: this._page,
				SortField: sortField,
				SortOrder: sortOrder,
				LookForStr: this._lookForStr,
				SearchMode: this._searchMode,
				RedrawType: REDRAW_HEADER,
				RedrawObj: null,
				MsgId: null,
				MsgUid: null,
				MsgFolderId: null,
				MsgFolderFullName: null,
				MsgCharset: null,
				MsgParts: null
			}
		);
	},

	BuildInboxTable: function ()
	{
		this._inboxController = new CInboxTableController(this._skinName, 'ClickMessageHandler', DblClickHandler, SCREEN_MESSAGES_LIST == this.Id);
		var inboxTable = new CVariableTable(this._skinName, SortMessagesHandler, this._selection, this._dragNDrop, this._inboxController);
		inboxTable.AddColumn(IH_CHECK, InboxHeaders[IH_CHECK]);
		inboxTable.AddColumn(IH_ATTACHMENTS, InboxHeaders[IH_ATTACHMENTS]);
		inboxTable.AddColumn(IH_FLAGGED, InboxHeaders[IH_FLAGGED]);
		this._fromToColumn = inboxTable.AddColumn(IH_FROM, InboxHeaders[IH_FROM]);
		inboxTable.AddColumn(IH_DATE, InboxHeaders[IH_DATE]);
		inboxTable.AddColumn(IH_SIZE, InboxHeaders[IH_SIZE]);
		inboxTable.AddColumn(IH_SUBJECT, InboxHeaders[IH_SUBJECT]);
		inboxTable.Build(this._inboxContainer);
		this._inboxLines = inboxTable.GetLines();
		this._inboxTable = inboxTable;
	},
	
	BuildAdvancedSearchForm: function()
	{
		var obj = this;
		var div = CreateChildWithAttrs(document.body, 'div', [['id', this.SearchFormId]]);
		this._bigSearchForm = div;
		div.className = 'wm_hide';
		var frm = CreateChild(div, 'form');
		frm.onsubmit = function () { return false; };
		var tbl = CreateChild(frm, 'table');
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_search_title';
		td.innerHTML = Lang.LookFor;
		WebMail.LangChanger.Register('innerHTML', td, 'LookFor', '');
		td = tr.insertCell(1);
		td.className = 'wm_search_value';
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['maxlength', '255']]);
		this._lookForBigObj = inp;
		inp.className = 'wm_search_input';
		var img = CreateChildWithAttrs(td, 'img', [['src', 'skins/' + this._skinName + '/menu/search_button_big.gif']]);
		this._searchButtonImg = img;
		img.className = 'wm_menu_big_search_img';
		inp.onkeypress = function (ev) {
			if (isEnter(ev)) {
				obj.GetSearchResultsFromBig();
			}
		};
		img.onclick = function () {
			obj.GetSearchResultsFromBig()
		};
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_search_title';
		td.innerHTML = Lang.SearchIn;
		WebMail.LangChanger.Register('innerHTML', td, 'SearchIn', '');
		td = tr.insertCell(1);
		td.className = 'wm_search_value';
		this._searchIn = CreateChild(td, 'select');
		if (null != this.SearchFormObj) {
			this.SearchFormObj.SetSearchIn(this._searchIn);
		};
		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_search_value';
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['name', 'qsmode' + this.Id], ['id', 'qmode' + this.Id]]);
		this._quickSearch = inp;
		inp.className = 'wm_checkbox';
		inp.checked = true;	
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'qmode' + this.Id]]);
		lbl.innerHTML =	Lang.QuickSearch;
		WebMail.LangChanger.Register('innerHTML', lbl, 'QuickSearch', '');
		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.className = 'wm_search_value';
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['name', 'qsmode' + this.Id], ['id', 'smode' + this.Id]]);
		this._slowSearch = inp;
		inp.className = 'wm_checkbox';
		inp.checked = false;
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'smode' + this.Id]]);	
		lbl.innerHTML =	Lang.SlowSearch;
		WebMail.LangChanger.Register('innerHTML', lbl, 'SlowSearch', '');
	},

	BuildFoldersPart: function (td)
	{
		var obj = this;
		var cont = CreateChild(td, 'div');
		this._foldersContainer = cont;
		cont.className = 'wm_folders_part';
		
		var hide = CreateChild(cont, 'div');
		this._foldersHide = hide;
		hide.className = 'wm_folders_hide_show';
		var img = CreateChildWithAttrs(hide, 'img', [['src', './skins/' + this._skinName + '/folders/hide_folders.gif'], ['title', Lang.HideFolders]]);
		WebMail.LangChanger.Register('title', img, 'HideFolders', '');
		this._foldersHideImg = img;
		img.className = 'wm_control_img';

		var list = CreateChild(cont, 'div');
		this._foldersList = list;
		this._dragNDrop.SetDropContainer(list);

		var mf = CreateChildWithAttrs(cont, 'div', [['align', 'center']]);
		this._manageFolders = mf;
		var a = CreateChildWithAttrs(mf, 'a', [['href', '#']]);
		a.innerHTML = Lang.ManageFolders;
		WebMail.LangChanger.Register('innerHTML', a, 'ManageFolders', '');
		a.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					Entity: PART_MANAGE_FOLDERS,
					SetIdAcct: true,
					NewMode: false
				}
			);
			return false;
		};

		if (this._isDisplayFolders) {
			list.className = 'wm_folders';
			mf.className = 'wm_manage_folders';
			hide.onclick = function() { obj.HideFolders(); };
		}
		else {
			this._foldersWidth = 18;
			img.src = './skins/' + this._skinName + '/folders/show_folders.gif';
			img.title = Lang.ShowFolders;
			WebMail.LangChanger.Register('title', img, 'ShowFolders', '');
			list.className = 'wm_hide';
			mf.className = 'wm_hide';
			hide.onclick = function() { obj.ShowFolders(); };
		}
	}
};

CMessagesListScreen.prototype = MessagesListPrototype;

