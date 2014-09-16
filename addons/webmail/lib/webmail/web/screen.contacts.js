/*
 * Classes:
 *  CContactsScreen
 *  CContactTab
 *  CNewGroupScreenPart
 *  CImportContactsScreenPart
 *  CCalendarScreen
 */

function CContactsScreen(skinName)
{
	this.Id = SCREEN_CONTACTS;
	this.isBuilded = false;
	this.hasCopyright = false;
	this.BodyAutoOverflow = true;
	this._skinName = skinName;
	this.Contacts = null;
	this.Contact = null;
	this.Groups = null; 
	this._groupsOutOfDate = true;
	this._SEPARATOR = '#@%';
	this._groupsDeleted = false;
	this.HistoryArgs = null;
	
	this._showTextLabels = true;
	
	this._mainContainer = null;

	this._bigSearchForm = null;
	this._lookForBigObj = null;
	this._lookForSmallObj = null;
	this._searchIn = null;
	this.SearchFormObj = null;

	this._contactsToMenu = null;
	this._importContacts = null;

	this._page = 1;
	this._pageSwitcher = null;
	this._contactsPerPage = 20;
	this._sortOrder = SORT_ORDER_DESC;
	this._sortField = SORT_FIELD_EMAIL;
	this._searchGroup = -1;
	this._lookFor = '';
	this._contactsListTd = null;
	this._contactsLines = null;
	this._contactsListTable = null;
	this.IdAddrForEdit = -1;

	this._contactsController = null;
	this._contactsTable = null;
	this._selection = new CSelection();
	
	//logo + accountslist + toolbar + lowtoolbar
	this._externalHeight = 58 + 32 + 27 + 28 + 40;
	this._minListHeight = 150;//counted variable, depends on (contacts + groups) count on page
	this._listWidthPercent = 40;
	//objects for resizing
	this._logo = null;
	this._accountsBar = null;
	this._toolBar = null;
	this._lowToolBar = null;
	this._contactsCount = null;

	this._contactsListDiv = null;
	this._contactViewerDiv = null;
	this._cardMinWidth = null;
	
	var obj = this;
	this._newContactObj = new CNewContactScreenPart(skinName, obj);
	this._viewContactObj = new CViewContactScreenPart(skinName);
	this._newGroupObj = new CNewGroupScreenPart(skinName, obj);
	this._importContactsObj = new CImportContactsScreenPart(skinName);

	this._addContactsCount = 0;
	this._addGroupName = '';
	
	this._emptyCard = true;
}

CContactsScreen.prototype = {
	PlaceData: function(Data)
	{
		var Type = Data.Type;
		switch (Type){
			case TYPE_CONTACTS:
				if (this.HistoryArgs.Entity == PART_CONTACTS || null != this.Contacts) {
					this.ShowEmpty();
				};
				this._newContactObj.CheckContactUpdate();
				this.Contacts = Data;
				if (this._groupsOutOfDate || this._groupsDeleted) {
					GetHandler(TYPE_GROUPS, {}, [], '');
				};
				this.Fill();
			break;
			case TYPE_CONTACT:
				this.Contact = Data;
				if (this.IdAddrForEdit == -1) {
					this._viewContactObj.UpdateContact(Data);
					this.ShowViewContact();
				}
				else {
					this._newContactObj.Fill(this.Contact);
					this.ShowNewContact();
					this.IdAddrForEdit = -1;
				};
			break;
			case TYPE_GROUPS:
				this._newGroupObj.CheckGroupUpdate();
				this.Groups = Data;
				this._groupsOutOfDate = false;
				this._groupsDeleted = false;
				this.FillGroups();
				this._newContactObj.FillGroups(Data);
			break;
			case TYPE_GROUP:
				this.Group = Data;
				this._newGroupObj.Fill(Data);
				this.ShowNewGroup();
			break;
			case TYPE_UPDATE:
				if (Data.Value == 'group') {
					if (this._addContactsCount > 0) {
						WebMail.ShowReport(Lang.ReportContactAddedToGroup + ' "' + this._addGroupName + '".');
						this._addContactsCount = 0;
						this._addGroupName = '';
					}
				};
			break;
		}
	},
	
	ClickBody: function(ev)
	{
		if (null != this.SearchFormObj) {
			this.SearchFormObj.checkVisibility(ev, Browser.Mozilla);
		}
	},

	ResizeBody: function(mode)
	{
		if (!Browser.IE || Browser.Version >= 7) {
			var listBorderHeight = 1;
			var height = GetHeight() - this.GetExternalHeight();
			if (height < this._minListHeight) height = this._minListHeight;
			var tableHeight = this._contactsTable.GetHeight();
			var cardHeight = this._cardTable.offsetHeight;
			if (height < tableHeight) height = tableHeight;
			if (height < cardHeight) height = cardHeight;
			this._mainDiv.style.height = height + 'px';
			this._contactsListDiv.style.height = height - listBorderHeight + 'px';

			var listWidth = this._leftDiv.offsetWidth;
			this._contactsTable.Resize(listWidth);
			this._cardTable.style.width = 'auto';
			var cardWidth = this._cardTable.offsetWidth;
			var rightWidth = this._rightDiv.offsetWidth;
			if (cardWidth < rightWidth) cardWidth = rightWidth;
			this._cardTable.style.width = cardWidth - 1 + 'px';
		}
		else {
			this._mainDiv.style.width = ((document.documentElement.clientWidth || document.body.clientWidth) < 850) && (!this._emptyCard) ? '850px' : '100%';
			var listWidth = this._leftDiv.offsetWidth;
			this._contactsTable.Resize(listWidth);

			var width = GetWidth();
			if (width < 850) width = 850;
			this._cardTable.style.width = width - listWidth - 4 + 'px';
		};
		this._pageSwitcher.Replace(this._contactsLines);
	},
	
	GetExternalHeight: function()
	{
		var res = 0;
		var offsetHeight = this._logo.offsetHeight;		if (offsetHeight) { res += offsetHeight; };
		offsetHeight = this._accountsBar.offsetHeight;	if (offsetHeight) { res += offsetHeight; } else { return this._externalHeight; };
		offsetHeight = this._toolBar.table.offsetHeight;if (offsetHeight) { res += offsetHeight; } else { return this._externalHeight; };
		offsetHeight = this._lowToolBar.offsetHeight;	if (offsetHeight) { res += offsetHeight; } else { return this._externalHeight; };
		this._externalHeight = res;
		return this._externalHeight;
	},

	Show: function(settings, historyArgs)
	{
		this.ParseSettings(settings);
		this._mainContainer.className = '';
		if (this._showTextLabels) {
			this._toolBar.ShowTextLabels();
		}
		else {
			this._toolBar.HideTextLabels();
		};
		if (null != this.SearchFormObj) {
			this.SearchFormObj.Show();
		};
		if (this.Groups == null || this._groupsOutOfDate || this._contactsPerPage != settings.ContactsPerPage) {
			this._contactsPerPage = settings.ContactsPerPage;
			if (null != historyArgs && 'undefined' != historyArgs.Page && null != historyArgs.Page) {
				historyArgs.Page = 1;
			};
			GetHandler(TYPE_GROUPS, {}, [], '');
		};
		if (null == historyArgs) {
			historyArgs = { Entity: PART_CONTACTS, Page: 1, SearchIn: 0, LookFor: '', SortField: CH_NAME, SortOrder: SORT_ORDER_DESC }
		};
		this.RestoreFromHistory(historyArgs);
		this.ResizeBody();
	},
	
	RestoreFromHistory: function (historyArgs)
	{
		this.HistoryArgs = historyArgs;
		if (historyArgs.Entity != PART_CONTACTS && this._pageSwitcher.PagesCount > 0) this._pageSwitcher.Show(0);
		switch (historyArgs.Entity) {
			case PART_CONTACTS:
				var contactsGroups = new CContacts();
				if ('undefined' == historyArgs.Page || null == historyArgs.Page) {
					contactsGroups.IdGroup = this._searchGroup;
					contactsGroups.LookFor = (typeof(historyArgs.LookFor) == 'string') ? historyArgs.LookFor : this._lookFor;
					var xml = contactsGroups.GetInXml();
					var lastPage = this._pageSwitcher.GetLastPage(0, this._contactsPerPage);
					var page = (lastPage < this._page) ? lastPage : this._page;
					GetHandler(TYPE_CONTACTS, { Page: page, SortField: this._sortField, SortOrder: this._sortOrder, IdGroup: this._searchGroup, LookFor: this._lookFor }, [], xml);
				}
				else {
					var sortField = !isNaN(historyArgs.SortField) ? historyArgs.SortField : this._sortField;
					var sortOrder = !isNaN(historyArgs.SortOrder) ? historyArgs.SortOrder : this._sortOrder;
					var searchIn = !isNaN(historyArgs.SearchIn) ? historyArgs.SearchIn : this._searchGroup;
					var lookFor = (typeof(historyArgs.LookFor) == 'string') ? historyArgs.LookFor : '';
					contactsGroups.IdGroup = searchIn;
					contactsGroups.LookFor = lookFor;
					var xml = contactsGroups.GetInXml();
					GetHandler(TYPE_CONTACTS, { Page: historyArgs.Page, SortField: sortField, SortOrder: sortOrder, IdGroup: searchIn, LookFor: lookFor }, [], xml);
				};
			break;
			case PART_NEW_CONTACT:
				if (null == this.Contacts) {
					var contactsGroups = new CContacts();
					var xml = contactsGroups.GetInXml();
					GetHandler(TYPE_CONTACTS, { Page: this._page, SortField: this._sortField, SortOrder: this._sortOrder, IdGroup: -1, LookFor: '' }, [], xml);
				};
				var contact = new CContact();
				if (historyArgs.Name) {
					contact.Name = historyArgs.Name;
				};
				if (historyArgs.Email) {
					contact.hEmail = historyArgs.Email;
				};
				contact.UseFriendlyNm = true;
				this._newContactObj.Fill(contact);
				this.ShowNewContact();
			break;
			case PART_EDIT_CONTACT:
				if (this.Contact.Id == historyArgs.IdAddr) {
					this._newContactObj.Fill(this.Contact);
					this.ShowNewContact();
				}
				else {
					this.IdAddrForEdit = historyArgs.IdAddr;
					GetHandler(TYPE_CONTACT, { IdAddr: historyArgs.IdAddr }, [], '');
				};
			break;
			case PART_VIEW_CONTACT:
				GetHandler(TYPE_CONTACT, { IdAddr: historyArgs.IdAddr }, [], '');
			break;
			case PART_NEW_GROUP:
				var group = new CGroup();
				if (null != historyArgs.Contacts) group.Contacts = historyArgs.Contacts;
				this._newGroupObj.Fill(group);
				this.ShowNewGroup();
			break;
			case PART_VIEW_GROUP:
				GetHandler(TYPE_GROUP, { IdGroup: historyArgs.IdGroup }, [], '');
			break;
			case PART_IMPORT_CONTACT:
				this.ShowImportContacts();
			break;
		}
	},
	
	ParseSettings: function (settings)
	{
		this._showTextLabels = settings.ShowTextLabels;
		this.ChangeSkin(settings.DefSkin);
	},

	ChangeSkin: function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			if (this.isBuilded) {
				this._toolBar.ChangeSkin(newSkin);
				this._contactsTable.ChangeSkin(newSkin);
			}
		}
	},

	ContactsImported: function (count)
	{
		SetHistoryHandler(
			{
				ScreenId: SCREEN_CONTACTS,
				Entity: PART_CONTACTS,
				Page: this._page,
				SortField: this._sortField,
				SortOrder: this._sortOrder,
				SearchIn: this._searchGroup,
				LookFor: this._lookFor
			}
		);
	},
	
	ShowEmpty: function ()
	{
		this._contactViewerDiv.className = 'wm_hide';
		this._newContactObj.Hide();
		this._viewContactObj.Hide();
		this._newGroupObj.Hide();
		this._importContactsObj.Hide();
		this._emptyCard = true;
		this.ResizeBody();
	},

	ShowNewContact: function ()
	{
		this._contactViewerDiv.className = '';
		this._newContactObj.Show();
		this._viewContactObj.Hide();
		this._newGroupObj.Hide();
		this._importContactsObj.Hide();
		this._emptyCard = false;
		this.ResizeBody();
	},
	
	ShowViewContact: function ()
	{
		this._contactViewerDiv.className = '';
		this._newContactObj.Hide();
		this._viewContactObj.Show();
		this._newGroupObj.Hide();
		this._importContactsObj.Hide();
		this._emptyCard = false;
		this.ResizeBody();
	},
	
	ShowNewGroup: function ()
	{
		this._contactViewerDiv.className = '';
		this._newContactObj.Hide();
		this._viewContactObj.Hide();
		this._newGroupObj.Show();
		this._importContactsObj.Hide();
		this._emptyCard = false;
		this.ResizeBody();
	},

	ShowImportContacts: function ()
	{
		this._contactViewerDiv.className = '';
		this._newContactObj.Hide();
		this._viewContactObj.Hide();
		this._newGroupObj.Hide();
		this._importContactsObj.Show();
		this._emptyCard = false;
		this.ResizeBody();
	},

	Hide: function()
	{
		this._mainContainer.className = 'wm_hide';
		if (null != this.SearchFormObj) {
			this.SearchFormObj.Hide();
		};
		this._pageSwitcher.Hide();
	},
	
	GetXmlParams: function ()
	{
		var params = '';
		params += '<param name="page" value="' + this._page + '"/>';
		params += '<param name="sort_field" value="' + this._sortField + '"/>';
		params += '<param name="sort_order" value="' + this._sortOrder + '"/>';
		return params;
	},
	
	GetSearchResultsFromBig: function ()
	{
		SetHistoryHandler(
			{
				ScreenId: SCREEN_CONTACTS,
				Entity: PART_CONTACTS,
				Page: this._page,
				SortField: this._sortField,
				SortOrder: this._sortOrder,
				SearchIn: this._searchIn.value,
				LookFor: this._lookForBigObj.value
			}
		);
	},
	
	GetSearchResultsFromSmall: function ()
	{
		SetHistoryHandler(
			{
				ScreenId: SCREEN_CONTACTS,
				Entity: PART_CONTACTS,
				Page: this._page,
				SortField: this._sortField,
				SortOrder: this._sortOrder,
				SearchIn: this._searchIn.value,
				LookFor: this._lookForSmallObj.value
			}
		);
	},
	
	DeleteSelected: function ()
	{
		if (this._selection != null) {
			var idArray = this._selection.GetCheckedLines().IdArray;
			var iCount = idArray.length;
			if (iCount > 0) {
				var contacts = '';
				var groups = '';
				for (var i=0; i<iCount; i++) {
					var params = idArray[i].split(this._SEPARATOR);
					if (params.length == 4)
						if (params[1] == '0') {
							contacts += '<contact id="' + params[0] + '"/>';
						}
						else {
							groups += '<group id="' + params[0] + '"/>';
						}
				};
				if (contacts.length != 0 || groups.length != 0) {
					if (groups.length != 0) this._groupsDeleted = true;
					var lastPage = this._pageSwitcher.GetLastPage(iCount);
					if (this._page > lastPage) this._page = lastPage;
					var xml = this.GetXmlParams();
					xml += '<contacts>' + contacts + '</contacts>';
					xml += '<groups>' + groups + '</groups>';
					if (confirm(Lang.ConfirmAreYouSure)) {
						RequestHandler('delete', 'contacts', xml);
					}
				}
			}
			else {
				alert(Lang.AlertNoContactsGroupsSelected);
			}
		}
	},
	
	AddContacts: function (id, name)
	{
		if (null != this._selection) {
			if (id == '-1') {
				var contacts = Array();
			}
			else {
				var contacts = '';
			};
			var idArray = this._selection.GetCheckedLines().IdArray;
			var iCount = idArray.length;
			var contactsCount = 0;
			for (var i=0; i<iCount; i++) {
				var params = idArray[i].split(this._SEPARATOR);
				if (params.length == 4)
					if (params[1] == '0') {
						if (id == '-1') {
							contacts[i] = { Id: params[0], Name: params[2], Email: params[3] }
						}
						else {
							contacts += '<contact id="' + params[0] + '"/>';
							contactsCount++;
						}
					}
			};
			if (contacts.length > 0) {
				if (id == '-1') {
					SetHistoryHandler(
						{
							ScreenId: SCREEN_CONTACTS,
							Entity: PART_NEW_GROUP,
							Contacts: contacts
						}
					);
				}
				else {
					var param = '<param name="id_group" value="' + id + '"/>';
					if (contacts.length > 0) {
						var xml = param + '<contacts>' + contacts + '</contacts>';
						RequestHandler('add', 'contacts', xml);
						this._addContactsCount = contactsCount;
						this._addGroupName = name;
					}
				};
			}
			else {
				alert(Lang.AlertNoContactsGroupsSelected);
			}
		}
	},
	
	MailContacts: function ()
	{
		var idArray = [];
		if (null != this._selection) {
			idArray = this._selection.GetCheckedLines().IdArray;
		};
		var iCount = idArray.length;
		var emailArray = [];
		for (var i=0; i<iCount; i++) {
			var params = idArray[i].split(this._SEPARATOR);
			if (params.length == 4 && params[3].length != 0) {
				emailArray.push(HtmlDecode(params[3]));
			}
		};
		SetHistoryHandler(
			{
				ScreenId: SCREEN_NEW_MESSAGE,
				FromDrafts: false,
				ForReply: false,
				FromContacts: true,
				ToField: emailArray.join(', ')
			}
		);
	},
	
	FillGroups: function ()
	{
		var sel = this._searchIn;
		CleanNode(sel);
		var opt = CreateChildWithAttrs(sel, 'option', [['value', '-1']]);
		opt.innerHTML = Lang.AllGroups;
		opt.selected = true;

		var obj = this;
		var menu = this._contactsToMenu;
		CleanNode(menu);
		var groups = this.Groups.Items;
		var iCount = groups.length;
		for (var i=0; i<iCount; i++) {
			var div = CreateChild(menu, 'div');
			div.className = 'wm_menu_item';
			div.onmouseover = function () { this.className='wm_menu_item_over'; };
			div.onmouseout = function () { this.className='wm_menu_item'; };
			div.id = groups[i].Id;
			div.innerHTML = groups[i].Name;
			div.onclick = function () { obj.AddContacts(this.id, this.innerHTML); };

			opt = CreateChildWithAttrs(sel, 'option', [['value', groups[i].Id]]);
			opt.innerHTML = groups[i].Name;
		};
		var div = CreateChild(menu, 'div');
		div.className = 'wm_menu_item_spec';
		div.onmouseover = function () { this.className='wm_menu_item_over_spec'; };
		div.onmouseout = function () { this.className='wm_menu_item_spec'; };
		div.id = '-1';
		div.innerHTML = '- ' + Lang.NewGroup + ' -';
		div.onclick = function () { obj.AddContacts(this.id); };
		if (null != this.SearchFormObj && this.SearchFormObj.isShown == 0) {
			this._searchIn.className = 'wm_hide';
		}
	},
	
	Fill: function ()
	{
		if (this.HistoryArgs.Entity != PART_NEW_CONTACT) {
			this.ShowEmpty();
		};
		this._sortField = this.Contacts.SortField;
		this._sortOrder = this.Contacts.SortOrder;
		this._searchGroup = this.Contacts.IdGroup;
		this._lookFor = this.Contacts.LookFor;
		
		if (this.Contacts.Count > 0) {
			this._contactsTable.UseSort();
			this._contactsTable.SetSort(this._sortField, this._sortOrder);
			this._contactsTable.Fill(this.Contacts.List, this._SEPARATOR);
		}
		else {
			this._contactsTable.FreeSort();
			this._contactsTable.CleanLines(Lang.InfoNoContactsGroups + 
			'<br /><div class="wm_view_message_info">' + Lang.InfoNewContactsGroups + '</div>');
		};
		
		this._page = this.Contacts.Page;
		var beginHandler = "SetHistoryHandler( { ScreenId: SCREEN_CONTACTS, Entity: PART_CONTACTS, Page: ";
		var endHandler = ", SortField: " + this._sortField + ", SortOrder: " + this._sortOrder + ", SearchIn: " + this._searchGroup + ", LookFor: '" + this._lookFor.replace(/'/g, '\\\'') + "'} );";
		this._pageSwitcher.Show(this._page, this._contactsPerPage, this.Contacts.Count, beginHandler, endHandler);
		this._pageSwitcher.Replace(this._contactsLines);

		this.SetContactsCount(this.Contacts.ContactsCount);
		this.ResizeBody();
	},
	
	BuildAdvancedSearchForm: function()
	{
		this._bigSearchForm = CreateChildWithAttrs(document.body, 'div', [['id', 'contacts_search_form']]);
		this._bigSearchForm.className = 'wm_hide';
		var frm = CreateChild(this._bigSearchForm, 'form');
		frm.onsubmit = function () { return false; };
		var tbl = CreateChild(frm, 'table');
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_search_title';
		td.innerHTML = Lang.LookFor;
		WebMail.LangChanger.Register('innerHTML', td, 'LookFor', '');
		td = tr.insertCell(1);
		td.className = 'wm_search_value';
		this._lookForBigObj = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['maxlength', '255']]);
		this._lookForBigObj.className = 'wm_search_input';
		var img = CreateChildWithAttrs(td, 'img', [['src', 'skins/' + this._skinName + '/menu/search_button_big.gif']]);
		img.className = 'wm_menu_big_search_img';
		var obj = this;
		this._lookForBigObj.onkeypress = function (ev) {
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
		}
	},
	
	BuildToolBar: function(PopupMenus)
	{
		var obj = this;
		var toolBar = new CToolBar(this._mainContainer, this._skinName);
		var item = toolBar.AddItem(TOOLBAR_BACK_TO_LIST, function () {
			SetHistoryHandler(
				{
					ScreenId: WebMail.ListScreenId,
					FolderId: null
				}
			);
		});
		item = toolBar.AddItem(TOOLBAR_NEW_MESSAGE, function () { obj.MailContacts(); });
		item = toolBar.AddItem(TOOLBAR_NEW_CONTACT, function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_NEW_CONTACT
				}
			);
		});
		item = toolBar.AddItem(TOOLBAR_NEW_GROUP, function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_NEW_GROUP,
					Contacts: null
				}
			);
		});
		this._contactsToMenu = CreateChild(document.body, 'div');
		this._contactsToMenu.className = 'wm_hide';
		toolBar.AddMoveItem(PopupMenus, 'AddContactsTo', this._contactsToMenu, false, 'add_contacts_to.gif', 'wm_menu_add_contacts_img');
		item = toolBar.AddItem(TOOLBAR_DELETE, function () { obj.DeleteSelected(); });
		this._importContacts = toolBar.AddItem(TOOLBAR_IMPORT_CONTACTS, function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_IMPORT_CONTACT
				}
			);
		});
		var searchParts = toolBar.AddSearchItems();
		this.SearchFormObj = new CSearchForm(this._bigSearchForm, searchParts.SmallForm, searchParts.Control, searchParts.ControlImg, 'contacts_search_form', this._lookForBigObj, searchParts.LookFor, this._skinName);
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
		this._toolBar = toolBar;
	},

	SetContactsCount: function (count)
	{
		this._contactsCount.innerHTML = count + '&nbsp;' + Lang.ContactsCount;
	},

	ClearContactsLines: function(msg)
	{
		this._selection = null;
		CleanNode(_inboxLines);
		this._isInboxLinesAdded = false;
		var nobr = CreateChild(_inboxLines, 'nobr');
		nobr.innerHTML = msg;
		this._pageSwitcher.Hide();
	},
	
	Build: function(container, accountsBar, popupMenus, settings)
	{
		this._logo = document.getElementById('logo');
		this._accountsBar = accountsBar;

		this.ParseSettings(settings);

		this._mainContainer = CreateChild(container, 'div');
		this._mainContainer.className = 'wm_hide';
		this.BuildAdvancedSearchForm();
		this.BuildToolBar(popupMenus);
		
		this._pageSwitcher = new CPageSwitcher(this._skinName);
		this._pageSwitcher.Build();
		
		var mainDiv = CreateChild(this._mainContainer, 'div');
		mainDiv.className = 'wm_contacts';
		this._mainDiv = mainDiv;
		var leftDiv = CreateChild(mainDiv, 'div');
		leftDiv.className = 'wm_contacts_list';
		this._leftDiv = leftDiv;
		
		//contacts list
		this._contactsController = new CContactsTableController(this._skinName, this._SEPARATOR);
		var contactsTable = new CVariableTable(this._skinName, SortContactsHandler, this._selection, null, this._contactsController);
		contactsTable.AddColumn(CH_CHECK, ContactsHeaders[CH_CHECK]);
		contactsTable.AddColumn(CH_GROUP, ContactsHeaders[CH_GROUP]);
		contactsTable.AddColumn(CH_NAME, ContactsHeaders[CH_NAME]);
		contactsTable.AddColumn(CH_EMAIL, ContactsHeaders[CH_EMAIL]);
		this._contactsListDiv = contactsTable.Build(leftDiv);
		this._contactsLines = contactsTable.GetLines();
		this._contactsTable = contactsTable;
		
		//contact's card on the left part of screen
		var rightDiv = CreateChild(mainDiv, 'div');
		rightDiv.className = 'wm_contacts_view_edit';
		this._rightDiv = rightDiv;

		this._contactViewerDiv = CreateChild(rightDiv, 'div');
		this._contactViewerDiv.className = 'wm_hide';
		var tbl_ = CreateChild(this._contactViewerDiv, 'table');
		this._cardTable = tbl_;
		tbl_.className = 'wm_contacts_card';
		var tr_ = tbl_.insertRow(0);
		var td_ = tr_.insertCell(0);
		td_.className = 'wm_contacts_card_top_left';
		td_.innerHTML = '<div class="wm_contacts_card_corner"/>';//insert div to hard setting border size
		td_ = tr_.insertCell(1);
		td_.className = 'wm_contacts_card_top';
		td_ = tr_.insertCell(2);
		td_.className = 'wm_contacts_card_top_right';
		td_.innerHTML = '<div class="wm_contacts_card_corner"/>';//insert div to hard setting border size
		
		tr_ = tbl_.insertRow(1);
		td_ = tr_.insertCell(0);
		td_.className = 'wm_contacts_card_left';
		td_ = tr_.insertCell(1);
		//----------
		this._newContactObj.Build(td_);
		this._viewContactObj.Build(td_);
		this._newGroupObj.Build(td_);
		this._importContactsObj.Build(td_);
		td_ = tr_.insertCell(2);
		td_.className = 'wm_contacts_card_right';

		tr_ = tbl_.insertRow(2);
		td_ = tr_.insertCell(0);
		td_.className = 'wm_contacts_card_bottom_left';
		td_.innerHTML = '<div class="wm_contacts_card_corner"/>';//insert div to hard setting border size
		td_ = tr_.insertCell(1);
		td_.className = 'wm_contacts_card_bottom';
		td_ = tr_.insertCell(2);
		td_.className = 'wm_contacts_card_bottom_right';
		td_.innerHTML = '<div class="wm_contacts_card_corner"/>';//insert div to hard setting border size

		var lowDiv = CreateChild(this._mainContainer, 'div');
		lowDiv.className = 'wm_lowtoolbar';
		this._lowToolBar = lowDiv;
		this._contactsCount = CreateChildWithAttrs(lowDiv, 'span', [['class', 'wm_lowtoolbar_messages']]);
		this.SetContactsCount(0);

		this.isBuilded = true;
	}//Build
};

function CContactTab(container, img, tab)
{
	this._container = container;
	this._controlImg = img;
	this.isHidden = false;
	this._tab = tab;
}

CContactTab.prototype = 
{
	Show: function ()
	{
		this._tab.className = 'wm_contacts_tab';
		if (this.isHidden) {
			this._container.className = 'wm_hide';
		}
		else {
			this._container.className = 'wm_contacts_view';
		}
	},
	
	Hide: function ()
	{
		this._tab.className = 'wm_hide';
		this._container.className = 'wm_hide';
	},

	ChangeTabMode: function (skinName)
	{
		if (this.isHidden) {
			this.Open(skinName);
		}
		else {
			this.Close(skinName);
		}
	},
	
	Open: function (skinName)
	{
		this._container.className = 'wm_contacts_view';
		this._controlImg.src = 'skins/' + skinName + '/menu/arrow_up.gif';
		this.isHidden = false;
	},
	
	Close: function (skinName)
	{
		this._container.className = 'wm_hide';
		this._controlImg.src = 'skins/' + skinName + '/menu/arrow_down.gif';
		this.isHidden = true;
	}
};

function CNewGroupScreenPart(skinName, parent)
{
	this._skinName = skinName;
	this._parent = parent;
	
	this._mainTbl = null;
	this._addContactsTbl = null;
	this._groupContactsCont = null;
	this._buttonsTbl = null;
	this._GroupOrganizationTab = null;
	this._contacts = Array();
	this._isEditName = false;

	this._groupNameObj = null;
	this._newContactsObj = null;

	this._groupNameSpan = null;
	this._groupNameA = null;
	this._saveButton = null;
	this._createButton = null;
	
	this.isCreateGroup = false;
	this._createdGroupName = '';
	this.isSaveGroup = false;
	
	this._tabs = Array();
	this._groupOrganizationObj = null;
	this._emailObj = null;
	this._companyObj = null;
	this._streetObj = null;
	this._cityObj = null;
	this._faxObj = null;
	this._stateObj = null;
	this._phoneObj = null;
	this._zipObj = null;
	this._countryObj = null;
	this._webObj = null;
}

CNewGroupScreenPart.prototype = {
	Show: function ()
	{
		this._mainTbl.className = 'wm_contacts_view';
		this._addContactsTbl.className = 'wm_contacts_view wm_add_contacts';
		this._buttonsTbl.className = 'wm_contacts_view';
		this._groupNameObj.onkeypress = function () { };
		this._groupNameObj.onblur = function () { };
	},
	
	
	Hide: function ()
	{
		this._mainTbl.className = 'wm_hide';
		this._addContactsTbl.className = 'wm_hide';
		this._groupContactsCont.className = 'wm_hide';
		this._buttonsTbl.className = 'wm_hide';
		this._GroupOrganizationTab.className = 'wm_hide';
		this._tabs[0].Hide();
	},
	
	CheckGroupUpdate: function ()
	{
		if (this.isCreateGroup) {
			WebMail.ShowReport(Lang.ReportGroupSuccessfulyAdded1 + ' "' + this._createdGroupName + '" ' + Lang.ReportGroupSuccessfulyAdded2);
			this.isCreateGroup = false;
		}
		else if (this.isSaveGroup) {
			WebMail.ShowReport(Lang.ReportGroupUpdatedSuccessfuly);
			this.isSaveGroup = false;
		}
	},
	
	EditName: function ()
	{
		var obj = this;
		this._groupNameObj.value = HtmlDecode(this._groupNameSpan.innerHTML);
		this._groupNameObj.onkeypress = function (ev) { if (isEnter(ev)) obj.SaveName(); };
		this._groupNameObj.onblur = function (ev) { obj.CloseNameEditor(); };
		this._groupNameObj.className = 'wm_input wm_group_name_input';
		this._groupNameSpan.className = 'wm_hide';
		this._groupNameA.className = 'wm_hide';
		this._isEditName = true;
	},
	
	SaveName: function ()
	{
		this._groupNameSpan.innerHTML = HtmlEncode(this._groupNameObj.value);
		this.CloseNameEditor();
	},
	
	CloseNameEditor: function ()
	{
		this._groupNameObj.onkeypress = function () { };
		this._groupNameObj.onblur = function () { };
		this._groupNameObj.className = 'wm_hide';
		this._groupNameSpan.className = '';
		this._groupNameA.className = '';
		this._isEditName = false;
	},
	
	MailGroup: function ()
	{
		var iCount = this._contacts.length;
		var selected = Array();
		for (var i=0; i<iCount; i++) {
			var cont = this._contacts[i];
			if (cont.Inp.checked && cont.Email.length > 0) {
				selected.push(cont.Email);
			}
		};
		SetHistoryHandler(
			{
				ScreenId: SCREEN_NEW_MESSAGE,
				FromDrafts: false,
				ForReply: false,
				FromContacts: true,
				ToField: selected.join(', ')
			}
		);
	},
	
	Fill: function (group)
	{
		var obj = this;
		this.Show();
		this.Group = group;
		if (group.Id == -1) {
			this._groupNameObj.value = '';
			this._groupNameObj.className = 'wm_input wm_group_name_input';
			this._groupNameSpan.className = 'wm_hide';
			this._groupNameA.className = 'wm_hide';
			this._saveButton.className = 'wm_hide';
			this._createButton.className = 'wm_button';
		}
		else {
			this._groupNameSpan.innerHTML = group.Name;
			this._groupNameObj.className = 'wm_hide';
			this._groupNameSpan.className = '';
			this._groupNameA.className = '';
			this._saveButton.className = 'wm_button';
			this._createButton.className = 'wm_hide';
		};

		this._groupOrganizationObj.checked = group.isOrganization;
		if (group.isOrganization) {
			this._tabs[0].Show();
		}
		else {
			this._tabs[0].Hide();
		};
		this._emailObj.value = HtmlDecode(group.Email);
		this._companyObj.value = HtmlDecode(group.Company);
		this._streetObj.value = HtmlDecode(group.Street);
		this._cityObj.value = HtmlDecode(group.City);
		this._faxObj.value = HtmlDecode(group.Fax);
		this._stateObj.value = HtmlDecode(group.State);
		this._phoneObj.value = HtmlDecode(group.Phone);
		this._zipObj.value = HtmlDecode(group.Zip);
		this._countryObj.value = HtmlDecode(group.Country);
		this._webObj.value = HtmlDecode(group.Web);

		var iCount = group.Contacts.length;
		this._contacts = Array();
		if (iCount > 0) {
			this._groupContactsCont.className = '';
			CleanNode(this._groupContactsCont);
			var tbl = CreateChild(this._groupContactsCont, 'table');
			tbl.className = 'wm_contacts_in_group_lines';
			var rowIndex = 0;
			
			var tr = tbl.insertRow(rowIndex++);
			tr.className = 'wm_inbox_read_item';
			var td = tr.insertCell(0);
			var inp = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox']]);
			inp.onclick = function () { obj.CheckAllLines(this.checked); };
			td = tr.insertCell(1);
			td.innerHTML = Lang.Name;
			td = tr.insertCell(2);
			td.innerHTML = Lang.Email;
			for (var i=0; i<iCount; i++) {
				tr = tbl.insertRow(rowIndex++);
				tr.className = 'wm_inbox_read_item';
				td = tr.insertCell(0);
				inp = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox'], ['id', group.Contacts[i].Id]]);
				inp.onclick = function () { obj.CheckLine(this.id, this.checked); };
				td = tr.insertCell(1);
				td.innerHTML = group.Contacts[i].Name;
				td = tr.insertCell(2);
				td.innerHTML = group.Contacts[i].Email;
				this._contacts[i] = {Id: group.Contacts[i].Id, Email: group.Contacts[i].Email, Tr: tr, Inp: inp, Deleted: false};
			};
			tr = tbl.insertRow(rowIndex++);
			td = tr.insertCell(0);
			td.colSpan = 2;
			var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
			a.onclick = function () { obj.MailGroup(); return false; };
			a.innerHTML = Lang.MailGroup;
			td = tr.insertCell(1);
			td.style.textAlign = 'right';
			a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
			a.onclick = function () { obj.DeleteSelected(); return false; };
			a.innerHTML = Lang.RemoveFromGroup;
		}
		else {
			this._groupContactsCont.className = 'wm_hide';
		};
		this._newContactsObj.value = '';
	},
	
	CheckLine: function (id, checked)
	{
		var iCount = this._contacts.length;
		for (var i=0; i<iCount; i++) {
			var cont = this._contacts[i];
			if (cont.Id == id && !cont.Deleted)
				if (checked) {
					cont.Tr.className = 'wm_inbox_read_item_select';
				}
				else {
					cont.Tr.className = 'wm_inbox_read_item';
				}
		}
	},
	
	CheckAllLines: function (checked)
	{
		var iCount = this._contacts.length;
		for (var i=0; i<iCount; i++) {
			var cont = this._contacts[i];
			cont.Inp.checked = checked;
			if (checked) {
				cont.Tr.className = 'wm_inbox_read_item_select';
			}
			else {
				cont.Tr.className = 'wm_inbox_read_item';
			}
		}
	},
	
	DeleteSelected: function ()
	{
		var iCount = this._contacts.length;
		var delCount = 0;
		for (var i=0; i<iCount; i++) {
			var cont = this._contacts[i];
			if (cont.Inp.checked) {
				cont.Tr.className = 'wm_hide';
				cont.Deleted = true;
				delCount++;
			}
		};
		if (delCount == iCount)
			this._groupContactsCont.className = 'wm_hide';
	},

	SaveChanges: function ()
	{
		/* validation */
		var val = new CValidate();
		var id = this.Group.Id;
		if (id == -1 || this._isEditName) {
			var name = Trim(this._groupNameObj.value);
		}
		else {
			var name = Trim(HtmlDecode(this._groupNameSpan.innerHTML));
		};
		if (val.IsEmpty(name)) {
			alert(Lang.WarningGroupNotComplete);
			return;
		};
		
		/* saving */
		var group = new CGroup();
		group.Id = id;
		if (this._groupOrganizationObj.checked) {
			group.isOrganization = true;
		}
		else {
			group.isOrganization = false;
		};
		group.Name = name;
		group.Email = this._emailObj.value;
		group.Company = this._companyObj.value;
		group.Street = this._streetObj.value;
		group.City = this._cityObj.value;
		group.Fax = this._faxObj.value;
		group.State = this._stateObj.value;
		group.Phone = this._phoneObj.value;
		group.Zip = this._zipObj.value;
		group.Country = this._countryObj.value;
		group.Web = this._webObj.value;
		var iCount = this._contacts.length;
		for (var i=0; i<iCount; i++) {
			var cont = this._contacts[i];
			if (cont.Deleted == false) {
				group.Contacts.push({ Id: cont.Id });
			}
		};
		var contactsAr = this._newContactsObj.value.split(',');
		var iCount = contactsAr.length;
		for (var i=0; i<iCount; i++) {
			var email = Trim(contactsAr[i]);
			if (email.length > 0) {
				if (val.HasEmailForbiddenSymbols(email)) {
					alert(Lang.WarningCorrectEmail);
					return;
				};
				group.NewContacts.push({ Email: email });
			}
		};

		var xml = group.GetInXml(this._parent.GetXmlParams());
		if (id == -1) {
			RequestHandler('new', 'group', xml);
			this.isCreateGroup = true;
			this._createdGroupName = name;
		}
		else {
			RequestHandler('update', 'group', xml);
			this.isSaveGroup = true;
		};
		this._parent._groupsOutOfDate = true;
	},
	
	Build: function (container)
	{
		var obj = this;
		var tbl = CreateChild(container, 'table');
		this._mainTbl = tbl;
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.innerHTML = Lang.GroupName + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'GroupName', ':');
		td = tr.insertCell(1);
		td.className = 'wm_contacts_name';
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input wm_group_name_input'], ['maxlength', '85']]);
		this._groupNameObj = inp;
		var span = CreateChild(td, 'span');
		span.className = 'wm_hide';
		this._groupNameSpan = span;
		span = CreateChild(td, 'span');
		span.innerHTML = '&nbsp;';
		var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = function () { obj.EditName(); return false; };
		a.innerHTML = Lang.Rename;
		WebMail.LangChanger.Register('innerHTML', a, 'Rename', '');
		a.className = 'wm_hide';
		this._groupNameA = a;
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox'], ['id', 'group-organization']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'group-organization']]);
		lbl.innerHTML = Lang.TreatAsOrganization;
		WebMail.LangChanger.Register('innerHTML', lbl, 'TreatAsOrganization', '');
		inp.onclick = function () {
			if (this.checked) {
				obj._tabs[0].Show();
			}
			else {
				obj._tabs[0].Hide();
			}
		};
		this._groupOrganizationObj = inp;
		
		this.BuildGroupOrganization(container);
		
		/*------Group contacts------*/
		
		div = CreateChild(container, 'div');
		this._groupContactsCont = div;
		div.className = 'wm_hide';

		/*------New contacts------*/
		
		var tbl = CreateChild(container, 'table');
		this._addContactsTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		var span = CreateChild(td, 'span');
		span.innerHTML = Lang.AddContacts + ':<br/>';
		WebMail.LangChanger.Register('innerHTML', span, 'AddContacts', ':<br/>');

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		var txt = CreateChild(td, 'textarea');
		txt.className = 'wm_add_contacts_area';
		this._newContactsObj = txt;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_secondary_info';
		td.innerHTML = Lang.CommentAddContacts;
		WebMail.LangChanger.Register('innerHTML', td, 'CommentAddContacts', '');

		tbl = CreateChild(container, 'table');
		this._buttonsTbl = tbl;
		tbl.className = 'wm_hide';
		tbl.style.width = '90%';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_save_button';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '');
		inp.onclick = function () { obj.SaveChanges(); };
		this._saveButton = inp;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.CreateGroup]]);
		WebMail.LangChanger.Register('value', inp, 'CreateGroup', '');
		inp.onclick = function () { obj.SaveChanges(); };
		this._createButton = inp;
	},
	
	TextAreaLimit: function (ev)
	{
		return TextAreaLimit(ev, this, 85);
	},
	
	BuildGroupOrganization: function (container)
	{
		var obj = this;
		
		var tabTbl = CreateChild(container, 'table');
		tabTbl.style.marginTop = '20px';
		this._GroupOrganizationTab = tabTbl;
		tabTbl.onclick = function () {
			obj._tabs[0].ChangeTabMode(obj._skinName);
		};
		tabTbl.className = 'wm_contacts_tab';
		tr = tabTbl.insertRow(0);
		td = tr.insertCell(0);
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_name';
		span.innerHTML = Lang.Organization;
		WebMail.LangChanger.Register('innerHTML', span, 'Organization', '');
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_mode';
		img = CreateChild(span, 'img');
		img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';

		var tbl = CreateChild(container, 'table');
		this._tabs[0] = new CContactTab(tbl, img, tabTbl);
		tbl.className = 'wm_contacts_view';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.style.width = '20%';
		td.innerHTML = Lang.Email + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Email', ':');
		td = tr.insertCell(1);
		td.style.width = '80%';
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '45'], ['maxlength', '255']]);
		this._emailObj = inp;

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Company + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Company', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._companyObj = inp;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StreetAddress + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StreetAddress', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		txt = CreateChildWithAttrs(td, 'textarea', [['class', 'wm_input'], ['cols', '35'], ['rows', '2']]);
		txt.onkeydown = this.TextAreaLimit;
		this._streetObj = txt;

		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.style.width = '20%';
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.City + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'City', ':');
		td = tr.insertCell(1);
		td.style.width = '30%';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._cityObj = inp;
		td = tr.insertCell(2);
		td.style.width = '5%';
		td = tr.insertCell(3);
		td.style.width = '15%';
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Fax + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Fax', ':');
		td = tr.insertCell(4);
		td.style.width = '30%';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '50']]);
		this._faxObj = inp;

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StateProvince + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StateProvince', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._stateObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Phone + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Phone', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '50']]);
		this._phoneObj = inp;

		tr = tbl.insertRow(5);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.ZipCode + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'ZipCode', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '10']]);
		this._zipObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.CountryRegion + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'CountryRegion', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._countryObj = inp;

		tr = tbl.insertRow(6);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.WebPage + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'WebPage', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '45'], ['maxlength', '85']]);
		this._webObj = inp;
		CreateTextChild(td, ' ');
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Go]]);
		WebMail.LangChanger.Register('value', inp, 'Go', '');
		inp.onclick = function () { OpenURL(obj._webObj.value); };
	}
};

function CImportContactsScreenPart(skinName, isClassic)
{
	this._skinName = skinName;
	if (isClassic) {
		this.isClassic = true;
	}
	else {
		this.isClassic = false;
	};
	this._mainFrm = null;
	this._importFile = null;
	this._fileType1 = null;
	this._fileType2 = null;
}

CImportContactsScreenPart.prototype = {
	Show: function ()
	{
		this._mainFrm.className = 'wm_contacts_view';
	},
	
	Hide: function ()
	{
		this._mainFrm.className = 'wm_hide';
	},
	
	Build: function (container)
	{
		var obj = this;
		
		var importFrame = CreateChildWithAttrs(container, 'iframe', [['src', EmptyHtmlUrl], ['style', 'width:1px; height:1px; border:0px; display:none;'], ['name', 'ImportFrame'], ['id', 'ImportFrame']]);

		var frm = CreateChildWithAttrs(container, 'form', [['action', ImportUrl], ['method', 'post'], ['enctype', 'multipart/form-data'], ['target', 'ImportFrame']]);
		frm.onsubmit = function () {
			var val = new CValidate();
			if (!obj._fileType1.checked && !obj._fileType2.checked ) {
				alert(Lang.WarningImportFileType);
				return false;
			};
			if (val.IsEmpty(obj._importFile.value)) {
				alert(Lang.WarningEmptyImportFile);
				return false;
			};
			if (!val.HasFileExtention(obj._importFile.value, 'csv')) {
				alert(Lang.WarningCsvExtention);
				return false;
			}
		};
		frm.className = 'wm_hide';
		this._mainFrm = frm;

		var tbl = CreateChild(frm, 'table');
		tbl.className = 'wm_contacts_view';
		var rowIndex = 0;
		var tr = tbl.insertRow(rowIndex++);
		var td = tr.insertCell(0);
		var b = CreateChild(td, 'b');
		if (this.isClassic) {
			if (Browser.IE) b.innerText = Lang.UseImportTo;
			else b.innerHTML = Lang.UseImportTo;
		}
		else {
			b.innerHTML = Lang.UseImportTo;
			WebMail.LangChanger.Register('innerHTML', b, 'UseImportTo', '');
		};
		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		this._fileType1 = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['class', 'wm_checkbox'], ['id', 'file_type_1'], ['name', 'file_type'], ['value', '0']]);
		if (Browser.Mozilla)
		{
		    this._fileType1.style.margin = '4px 0 4px 0';
		}
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'file_type_1']]);
		if (this.isClassic) {
			if (Browser.IE) lbl.innerText = Lang.Outlook1;
			else lbl.innerHTML = Lang.Outlook1;
		}
		else {
			lbl.innerHTML = Lang.Outlook1;
			WebMail.LangChanger.Register('innerHTML', lbl, 'Outlook1', '');
		};
		var br = CreateChild(td, 'br');
		this._fileType2 = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['class', 'wm_checkbox'], ['id', 'file_type_2'], ['name', 'file_type'], ['value', '1']]);
		if (Browser.Mozilla) {
		    this._fileType2.style.margin = '4px 0 4px 0';
		};
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'file_type_2']]);
		if (this.isClassic) {
			if (Browser.IE) lbl.innerText = Lang.Outlook2;
			else lbl.innerHTML = Lang.Outlook2;
		}
		else {
			lbl.innerHTML = Lang.Outlook2;
			WebMail.LangChanger.Register('innerHTML', lbl, 'Outlook2', '');
		};
		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		if (this.isClassic) {
			if (Browser.IE) td.innerText = Lang.SelectImportFile + ':';
			else td.innerHTML = Lang.SelectImportFile + ':';
		}
		else {
			td.innerHTML = Lang.SelectImportFile + ':';
			WebMail.LangChanger.Register('innerHTML', td, 'SelectImportFile', ':');
		};
		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		this._importFile = CreateChildWithAttrs(td, 'input', [['type', 'file'], ['class', 'wm_file'], ['size', '30'], ['name', 'fileupload']]);
		
		tbl = CreateChild(frm, 'table');
		tbl.className = 'wm_contacts_view';
		tbl.style.width = '90%';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.style.textAlign = 'right';
		td.style.borderTop = 'solid 1px #8D8C89';
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'submit'], ['class', 'wm_file'], ['value', Lang.Import]]);
		if (!this.isClassic) WebMail.LangChanger.Register('value', inp, 'Import', '');
	}
};

function CCalendarScreen(skinName)
{
	this.Id = SCREEN_CALENDAR;
	this.isBuilded = false;
	this.hasCopyright = false;
	this.BodyAutoOverflow = false;
	this._skinName = skinName;
	this._defLang = 'English';
	this._showTextLabels = true;
	this._needReload = false;
	this._wasShown = false;
	
	this._ifrCalendar = null;
}

CCalendarScreen.prototype = {
	PlaceData: function(Data)
	{
	},
	
	ClickBody: function(ev)
	{
	},

	ResizeBody: function(mode)
	{
		if (this._ifrCalendar == null) return;
		this._ifrCalendar.style.width = GetWidth() + 'px';
		this._ifrCalendar.style.height = GetHeight() + 'px';
	},
	
	Create: function ()
	{
		this._ifrCalendar = CreateChildWithAttrs(document.body, 'iframe', [['src', CalendarUrl], ['frameborder', '0']]);
		with (this._ifrCalendar.style) {
			padding = '0';
			margin = '0';
			border = 'none';
			position = 'absolute';
			left = '0';
			top = '0';
			zIndex = '10';
		}
		var obj = this;
		this._ifrCalendar.onresize = function() {
			obj._ifrCalendar.style.width = (GetWidth() + 1) + 'px';
		};
		this.Hide();
	},
	
	Reload: function ()
	{
		/* FireFox2 and IE6,7 can reload iframe without parameter
		 * Opera9 can't reload iframe without parameter
		 */
		this._ifrCalendar.src = CalendarUrl + '?p=' + Math.random();
		this._needReload = false;
		this._wasShown = false;
	},
	
	Show: function(settings, historyArgs)
	{
		this.ParseSettings(settings);
		if (this._ifrCalendar == null) {
			this.Create();
		}
		else if (this._needReload) {
			this.Reload();
		}
		this.ResizeBody();
		if (this._wasShown) {
			this.Display();
		}
		else {
			WebMail.ShowInfo(Lang.Loading);
		}
	},
	
	Display: function ()
	{
		this._ifrCalendar.className = '';
		this._wasShown = true;
		WebMail.HideInfo();
	},
	
	RestoreFromHistory: function (historyArgs)
	{
	},
	
	ParseSettings: function (settings)
	{
		if (this._skinName != settings.DefSkin || this._defLang != settings.DefLang) {
			this._showTextLabels = settings.ShowTextLabels;
			this._skinName = settings.DefSkin;
			this._defLang = settings.DefLang;
			this._needReload = true;
		}
	},
	
	NeedReload: function ()
	{
		this._needReload = true;
	},

	Hide: function()
	{
		if (this._ifrCalendar == null) return;
		if (Browser.Mozilla) {
			// IE7 make iframe unvisible dirty
			this._ifrCalendar.className = 'wm_unvisible';
		}
		else {
			// FireFox2 reload iframe if set "display: none;" 
			this._ifrCalendar.className = 'wm_hide';
		}
	},
	
	Build: function(container, accountsBar, popupMenus, settings)
	{
		this.ParseSettings(settings);
		this._needReload = false;
		this.isBuilded = true;
	}//Build
};