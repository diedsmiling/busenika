/*
Classes:
	CNewGroupScreenPart
*/

function CNewGroupScreenPart(skinName, parent)
{
	this._skinName = skinName;
	this._parent = parent;
	
	this._mainTbl = null;
	this._addContactsTbl = null;
	this._groupContactsTbl = null;
	this._buttonsTbl = null;
	this._GroupOrganizationTab = null;
	this._contacts = Array();
	this._isEditName = false;

	this._groupNameObj = null;
	this._newContactsObj = null;

	this._groupNameSpan = null;
	this._groupNameA = null;
	this._mailGroupCont = null;
	this._addCont = null;
	this._saveButton = null;
	this._createButton = null;
	
	this._contactsToMail = '';
	
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
		this._groupNameObj.onkeypress = function () { }
		this._groupNameObj.onblur = function () { }
	},
	
	
	Hide: function ()
	{
		this._mainTbl.className = 'wm_hide';
		this._addContactsTbl.className = 'wm_hide';
		this._groupContactsTbl.className = 'wm_hide';
		this._buttonsTbl.className = 'wm_hide';
		this._mailGroupCont.className = 'wm_hide';
		this._GroupOrganizationTab.className = 'wm_hide';
		this._tabs[0].Tbl.className = 'wm_hide';
	},
	
	CheckGroupUpdate: function ()
	{
		if (this.isCreateGroup)
		{
			WebMail.ShowReport(Lang.ReportGroupSuccessfulyAdded1 + ' "' + this._createdGroupName + '" ' + Lang.ReportGroupSuccessfulyAdded2);
			this.isCreateGroup = false;
		}
		else if (this.isSaveGroup)
		{
			WebMail.ShowReport(Lang.ReportGroupUpdatedSuccessfuly);
			this.isSaveGroup = false;
		}
	},
	
	EditName: function ()
	{
		var obj = this;
		this._groupNameObj.value = HtmlDecode(this._groupNameSpan.innerHTML);
		this._groupNameObj.onkeypress = function (ev) { if (isEnter(ev)) obj.SaveName(); }
		this._groupNameObj.onblur = function (ev) { obj.CloseNameEditor(); }
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
		this._groupNameObj.onkeypress = function () { }
		this._groupNameObj.onblur = function () { }
		this._groupNameObj.className = 'wm_hide';
		this._groupNameSpan.className = '';
		this._groupNameA.className = '';
		this._isEditName = false;
	},
	
	MailGroup: function ()
	{
		SetHistoryHandler(
			{
				ScreenId: SCREEN_NEW_MESSAGE,
				FromDrafts: false,
				ForReply: false,
				FromContacts: true,
				ToField: this._contactsToMail
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
			this._addCont.className = '';
			this._mailGroupCont.className = 'wm_hide';
			this._saveButton.className = 'wm_hide';
			this._createButton.className = 'wm_button';
		} else {
			this._groupNameSpan.innerHTML = group.Name;
			this._groupNameObj.className = 'wm_hide';
			this._groupNameSpan.className = '';
			this._groupNameA.className = '';
			this._addCont.className = 'wm_hide';
			this._mailGroupCont.className = 'wm_contacts_view';
			this._saveButton.className = 'wm_button';
			this._createButton.className = 'wm_hide';
		}

		this._groupOrganizationObj.checked = group.isOrganization;
		if (group.isOrganization)
		{
			this.ShowTab(0);
		}
		else
		{
			this.HideTab(0);
		}
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
			var tbl = this._groupContactsTbl;
			tbl.className = 'wm_contacts_in_group_lines';
			CleanNode(tbl);
			var tr = tbl.insertRow(0);
			tr.className = 'wm_inbox_read_item';
			var td = tr.insertCell(0);
			var inp = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox']]);
			inp.onclick = function () { obj.CheckAllLines(this.checked); }
			td = tr.insertCell(1);
			td.colSpan = 2;
			var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
			a.onclick = function () { obj.DeleteSelected(); return false; }
			var img = CreateChildWithAttrs(a, 'img', [['src', 'skins/' + this._skinName + '/menu/delete.gif']]);
			var span = CreateChild(a, 'span');
			span.innerHTML = ' ' + Lang.RemoveFromGroup;
			var contactsArray = [];
			for (var i=0; i<iCount; i++) {
				tr = tbl.insertRow(i+1);
				tr.className = 'wm_inbox_read_item';
				td = tr.insertCell(0);
				inp = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox'], ['id', group.Contacts[i].Id]]);
				inp.onclick = function () { obj.CheckLine(this.id, this.checked); }
				td = tr.insertCell(1);
				var nobr = CreateChild(td, 'nobr');
				nobr.innerHTML = group.Contacts[i].Name;
				td = tr.insertCell(2);
				nobr = CreateChild(td, 'nobr');
				nobr.innerHTML = group.Contacts[i].Email;
				contactsArray.push(HtmlDecode(group.Contacts[i].Email));
				this._contacts[i] = {Id: group.Contacts[i].Id, Tr: tr, Inp: inp, Deleted: false}
			}
			if (group.isOrganization && group.Email.length > 0)
			{
				this._contactsToMail = group.Email;
			}
			else
			{
				this._contactsToMail = contactsArray.join(', ');
			}
		} else {
			this._groupContactsTbl.className = 'wm_hide';
			this._contactsToMail = '';
		}
		if (this._contactsToMail.length == 0)
			this._mailGroupCont.className = 'wm_hide';
		this._newContactsObj.value = '';
	},
	
	CheckLine: function (id, checked)
	{
		var iCount = this._contacts.length;
		for (var i=0; i<iCount; i++) {
			if (this._contacts[i].Id == id && !this._contacts[i].Deleted)
				if (checked) {
					this._contacts[i].Tr.className = 'wm_inbox_read_item_select';
				} else {
					this._contacts[i].Tr.className = 'wm_inbox_read_item';
				}
		}
	},
	
	CheckAllLines: function (checked)
	{
		var iCount = this._contacts.length;
		for (var i=0; i<iCount; i++) {
			this._contacts[i].Inp.checked = checked;
			if (checked) {
				this._contacts[i].Tr.className = 'wm_inbox_read_item_select';
			} else {
				this._contacts[i].Tr.className = 'wm_inbox_read_item';
			}
		}
	},
	
	DeleteSelected: function ()
	{
		var iCount = this._contacts.length;
		var delCount = 0;
		for (var i=0; i<iCount; i++) {
			if (this._contacts[i].Inp.checked) {
				this._contacts[i].Tr.className = 'wm_hide';
				this._contacts[i].Deleted = true;
				delCount++;
			}
		}
		if (delCount == iCount)
			this._groupContactsTbl.className = 'wm_hide';
	},

	SaveChanges: function ()
	{
		/* validation */
		var val = new CValidate();
		var id = this.Group.Id;
		if (id == -1 || this._isEditName)
		{
			var name = Trim(this._groupNameObj.value);
		}
		else
		{
			var name = Trim(HtmlDecode(this._groupNameSpan.innerHTML));
		}
		if (val.IsEmpty(name))
		{
			alert(Lang.WarningGroupNotComplete);
			return;
		}
		
		/* saving */
		var group = new CGroup();
		group.Id = id;
		if (this._groupOrganizationObj.checked)
		{
			group.isOrganization = true;
		}
		else
		{
			group.isOrganization = false;
		}
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
		for (var i=0; i<iCount; i++)
		{
			if (this._contacts[i].Deleted == false)
			{
				group.Contacts.push({ Id: this._contacts[i].Id });
			}
		}
		var contactsAr = this._newContactsObj.value.split(',');
		var iCount = contactsAr.length;
		for (var i=0; i<iCount; i++)
		{
			var email = Trim(contactsAr[i]);
			if (email.length > 0)
			{
				if (val.HasEmailForbiddenSymbols(email))
				{
					alert(Lang.WarningCorrectEmail);
					return;
				}
				group.NewContacts.push({ Email: email });
			}
		}

		var xml = group.GetInXml(this._parent.GetXmlParams());
		if (id == -1)
		{
			RequestHandler('new', 'group', xml);
			this.isCreateGroup = true;
			this._createdGroupName = name;
		}
		else
		{
			RequestHandler('update', 'group', xml);
			this.isSaveGroup = true;
		}
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
		a.onclick = function () { obj.EditName(); return false; }
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
		inp.onclick = function ()
		{
			if (this.checked)
			{
				obj.ShowTab(0);
			}
			else
			{
				obj.HideTab(0);
			}
		}
		this._groupOrganizationObj = inp;
		
		this.BuildGroupOrganization(container);
		
		var tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = function () { obj.MailGroup(); return false; }
		a.innerHTML = Lang.MailGroup;
		WebMail.LangChanger.Register('innerHTML', a, 'MailGroup', '');
		this._mailGroupCont = tbl;

		/*------Group contacts------*/
		
		tbl = CreateChild(container, 'table');
		this._groupContactsTbl = tbl;
		tbl.className = 'wm_hide';

		/*------New contacts------*/
		
		tbl = CreateChild(container, 'table');
		this._addContactsTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		this._addCont = tr;
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
		inp.onclick = function () { obj.SaveChanges(); }
		this._saveButton = inp;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.CreateGroup]]);
		WebMail.LangChanger.Register('value', inp, 'CreateGroup', '');
		inp.onclick = function () { obj.SaveChanges(); }
		this._createButton = inp;
	},
	
	ShowTab: function (index)
	{
		this._GroupOrganizationTab.className = 'wm_contacts_tab';
		this.SetTabMode(index, this._tabs[index].Hide)
	},
	
	HideTab: function (index)
	{
		this._GroupOrganizationTab.className = 'wm_hide';
		this._tabs[index].Tbl.className = 'wm_hide';
	},
	
	SetTabMode: function (index, hideMode)
	{
		var tbl = this._tabs[index].Tbl;
		var img = this._tabs[index].Img;
		var hide = hideMode;
		if (hideMode)
		{
			tbl.className = 'wm_hide';
			img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';
		}
		else
		{
			tbl.className = 'wm_contacts_view';
			img.src = 'skins/' + this._skinName + '/menu/arrow_up.gif';
		}
		this._tabs[index] = { Tbl: tbl, Img: img, Hide: hide }
	},
	
	ChangeTabMode: function (index)
	{
		var hideMode = this._tabs[index].Hide ? false : true;
		this.SetTabMode(index, hideMode)
	},
	
	TextAreaLimit: function (ev)
	{
		return TextAreaLimit(ev, this, 85);
	},
	
	BuildGroupOrganization: function (container)
	{
		var obj = this;
		
		tbl = CreateChild(container, 'table');
		tbl.style.marginTop = '20px';
		this._GroupOrganizationTab = tbl;
		tbl.onclick = function () { obj.ChangeTabMode(0); }
		tbl.className = 'wm_contacts_tab';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_name';
		span.innerHTML = Lang.Organization;
		WebMail.LangChanger.Register('innerHTML', span, 'Organization', '');
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_mode';
		img = CreateChild(span, 'img');
		img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';

		tbl = CreateChild(container, 'table');
		this._tabs[0] = { Tbl: tbl, Img: img, Hide: false }
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
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Go]]);
		WebMail.LangChanger.Register('value', inp, 'Go', '');
		inp.onclick = function () { OpenURL(obj._webObj.value); }
	}
}

