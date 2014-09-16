/*
 * Classes:
 *  CViewContactScreenPart
 *  CNewContactScreenPart
 */

function CViewContactScreenPart(skinName)
{
	this._skinName = skinName;
	this.Contact = null;

	this._mainTbl = null;
	this._fullnameObj = null;
	this._fullnameCont = null;
	this._defaultEmailObj = null;
	this._defaultEmailCont = null;
	this._birthdayObj = null;
	this._birthdayCont = null;

	this._personalTbl = null;
	this._hEmailObj = null;
	this._hEmailCont = null;
	this._hStreetObj = null;
	this._hStreetCont = null;
	this._hCityTitle = null;
	this._hCityObj = null;
	this._hFaxTitle = null;
	this._hFaxObj = null;
	this._hCityFaxCont = null;
	this._hStateTitle = null;
	this._hStateObj = null;
	this._hPhoneTitle = null;
	this._hPhoneObj = null;
	this._hStatePhoneCont = null;
	this._hZipTitle = null;
	this._hZipObj = null;
	this._hMobileTitle = null;
	this._hMobileObj = null;
	this._hZipMobileCont = null;
	this._hCountryObj = null;
	this._hCountryCont = null;
	this._hWebObj = null;
	this._hWebCont = null;

	this._businessTbl = null;
	this._bEmailObj = null;
	this._bEmailCont = null;
	this._bCompanyTitle = null;
	this._bCompanyObj = null;
	this._bJobTitleTitle = null;
	this._bJobTitleObj = null;
	this._bCompanyJobTitleCont = null;
	this._bDepartmentTitle = null;
	this._bDepartmentObj = null;
	this._bOfficeTitle = null;
	this._bOfficeObj = null;
	this._bDepartmentOfficeCont = null;
	this._bStreetObj = null;
	this._bStreetCont = null;
	this._bCityTitle = null;
	this._bCityObj = null;
	this._bFaxTitle = null;
	this._bFaxObj = null;
	this._bCityFaxCont = null;
	this._bStateTitle = null;
	this._bStateObj = null;
	this._bPhoneTitle = null;
	this._bPhoneObj = null;
	this._bStatePhoneCont = null;
	this._bZipTitle = null;
	this._bZipObj = null;
	this._bCountryTitle = null;
	this._bCountryObj = null;
	this._bZipCountryCont = null;
	this._bWebObj = null;
	this._bWebCont = null;

	this._otherTbl = null;
	this._otherEmailObj = null;
	this._otherEmailCont = null;
	this._notesObj = null;
	this._notesCont = null;

	this._groupsTbl = null;
	this._groupsObj = null;

	this._editTbl = null;
}

CViewContactScreenPart.prototype = {
	Show: function ()
	{
		this.Fill();
	},
	
	Hide: function ()
	{
		this._mainTbl.className = 'wm_hide';
		this._personalTbl.className = 'wm_hide';
		this._businessTbl.className = 'wm_hide';
		this._otherTbl.className = 'wm_hide';
		this._groupsTbl.className = 'wm_hide';
		this._editTbl.className = 'wm_hide';
	},
	
	UpdateContact: function (Data)
	{
		this.Contact = Data;
	},
	
	Fill: function ()
	{
		var cont = this.Contact;
		
		var emptySection = true;
		if (cont.Name.length > 0) {
			this._fullnameObj.innerHTML = cont.Name;
			this._fullnameCont.className = '';
			emptySection = false;
		}
		else {
			this._fullnameCont.className = 'wm_hide';
		};
		var defEmail = '';
		switch (cont.PrimaryEmail) {
			case (0): defEmail = cont.hEmail; break;
			case (1): defEmail = cont.bEmail; break;
			case (2): defEmail = cont.OtherEmail; break;
		};
		if (defEmail.length > 0) {
			this._defaultEmailObj.innerHTML = defEmail;
			if (cont.UseFriendlyNm && cont.Name.length > 0) {
				var contactEmail = '"' + cont.Name + '" <' + HtmlDecode(defEmail) + '>';
			}
			else {
				var contactEmail = HtmlDecode(defEmail);
			};
			this._defaultEmailObj.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_NEW_MESSAGE,
						FromDrafts: false,
						ForReply: false,
						FromContacts: true,
						ToField: contactEmail
					}
				);
				return false;
			};
			this._defaultEmailCont.className = '';
			emptySection = false;
		}
		else {
			this._defaultEmailCont.className = 'wm_hide';
		};
		var birthDay = GetBirthDay(cont.Day, cont.Month, cont.Year);
		if (birthDay.length > 0) {
			this._birthdayObj.innerHTML = birthDay;
			this._birthdayCont.className = '';
			emptySection = false;
		}
		else {
			this._birthdayCont.className = 'wm_hide';
		};
		if (emptySection) this._mainTbl.className = 'wm_hide';
		else this._mainTbl.className = 'wm_contacts_view';

		emptySection = true;
		if (cont.hEmail.length > 0 && cont.PrimaryEmail != 0) {
			this._hEmailObj.innerHTML = cont.hEmail;
			if (cont.UseFriendlyNm && cont.Name.length > 0) {
				var hEmail = '"' + cont.Name + '" <' + HtmlDecode(cont.hEmail) + '>';
			}
			else {
				var hEmail = HtmlDecode(cont.hEmail);
			};
			this._hEmailObj.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_NEW_MESSAGE,
						FromDrafts: false,
						ForReply: false,
						FromContacts: true,
						ToField: hEmail
					}
				);
				return false;
			};
			this._hEmailCont.className = '';
			emptySection = false;
		}
		else {
			this._hEmailCont.className = 'wm_hide';
		};
		if (cont.hStreet.length > 0) {
			this._hStreetObj.innerHTML = cont.hStreet;
			this._hStreetCont.className = '';
			emptySection = false;
		}
		else {
			this._hStreetCont.className = 'wm_hide';
		};
		if (cont.hCity.length > 0 || cont.hFax.length > 0) {
			this._hCityObj.innerHTML = cont.hCity;
			this._hCityTitle.innerHTML = (cont.hCity.length > 0) ? Lang.City + ':' : '';
			this._hFaxObj.innerHTML = cont.hFax;
			this._hFaxTitle.innerHTML = (cont.hFax.length > 0) ? Lang.Fax + ':' : '';
			this._hCityFaxCont.className = '';
			emptySection = false;
		}
		else {
			this._hCityFaxCont.className = 'wm_hide';
		};
		if (cont.hState.length > 0 || cont.hPhone.length > 0) {
			this._hStateObj.innerHTML = cont.hState;
			this._hStateTitle.innerHTML = (cont.hState.length > 0) ? Lang.StateProvince + ':' : '';
			this._hPhoneObj.innerHTML = cont.hPhone;
			this._hPhoneTitle.innerHTML = (cont.hPhone.length > 0) ? Lang.Phone + ':' : '';
			this._hStatePhoneCont.className = '';
			emptySection = false;
		}
		else {
			this._hStatePhoneCont.className = 'wm_hide';
		};
		if (cont.hZip.length > 0 || cont.hMobile.length > 0) {
			this._hZipObj.innerHTML = cont.hZip;
			this._hZipTitle.innerHTML = (cont.hZip.length > 0) ? Lang.ZipCode + ':' : '';
			this._hMobileObj.innerHTML = cont.hMobile;
			this._hMobileTitle.innerHTML = (cont.hMobile.length > 0) ? Lang.Mobile + ':' : '';
			this._hZipMobileCont.className = '';
			emptySection = false;
		}
		else {
			this._hZipMobileCont.className = 'wm_hide';
		};
		if (cont.hCountry.length > 0) {
			this._hCountryObj.innerHTML = cont.hCountry;
			this._hCountryCont.className = '';
			emptySection = false;
		}
		else {
			this._hCountryCont.className = 'wm_hide';
		};
		if (cont.hWeb.length > 0) {
			this._hWebObj.innerHTML = cont.hWeb;
			this._hWebObj.href = cont.hWeb;
			this._hWebObj.target = 'web page';
			this._hWebCont.className = '';
			emptySection = false;
		}
		else {
			this._hWebCont.className = 'wm_hide';
		};
		if (emptySection) this._personalTbl.className = 'wm_hide';
		else this._personalTbl.className = 'wm_contacts_view';

		emptySection = true;
		if (cont.bEmail.length > 0 && cont.PrimaryEmail != 1) {
			this._bEmailObj.innerHTML = cont.bEmail;
			if (cont.UseFriendlyNm && cont.Name.length > 0) {
				var bEmail = '"' + cont.Name + '" <' + HtmlDecode(cont.bEmail) + '>';
			}
			else {
				var bEmail = HtmlDecode(cont.bEmail);
			};
			this._bEmailObj.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_NEW_MESSAGE,
						FromDrafts: false,
						ForReply: false,
						FromContacts: true,
						ToField: bEmail
					}
				);
				return false;
			};
			this._bEmailCont.className = '';
			emptySection = false;
		}
		else {
			this._bEmailCont.className = 'wm_hide';
		};
		if (cont.bCompany.length > 0 || cont.bJobTitle.length > 0) {
			this._bCompanyObj.innerHTML = cont.bCompany;
			this._bCompanyTitle.innerHTML = (cont.bCompany.length > 0) ? Lang.Company + ':' : '';
			this._bJobTitleObj.innerHTML = cont.bJobTitle;
			this._bJobTitleTitle.innerHTML = (cont.bJobTitle.length > 0) ? Lang.JobTitle + ':' : '';
			this._bCompanyJobTitleCont.className = '';
			emptySection = false;
		}
		else {
			this._bCompanyJobTitleCont.className = 'wm_hide';
		};
		if (cont.bDepartment.length > 0 || cont.bOffice.length > 0) {
			this._bDepartmentObj.innerHTML = cont.bDepartment;
			this._bDepartmentTitle.innerHTML = (cont.bDepartment.length > 0) ? Lang.Department + ':' : '';
			this._bOfficeObj.innerHTML = cont.bOffice;
			this._bOfficeTitle.innerHTML = (cont.bOffice.length > 0) ? Lang.Office + ':' : '';
			this._bDepartmentOfficeCont.className = '';
			emptySection = false;
		}
		else {
			this._bDepartmentOfficeCont.className = 'wm_hide';
		};
		if (cont.bStreet.length > 0) {
			this._bStreetObj.innerHTML = cont.bStreet;
			this._bStreetCont.className = '';
			emptySection = false;
		}
		else {
			this._bStreetCont.className = 'wm_hide';
		};
		if (cont.bCity.length > 0 || cont.bFax.length > 0) {
			this._bCityObj.innerHTML = cont.bCity;
			this._bCityTitle.innerHTML = (cont.bCity.length > 0) ? Lang.City + ':' : '';
			this._bFaxObj.innerHTML = cont.bFax;
			this._bFaxTitle.innerHTML = (cont.bFax.length > 0) ? Lang.Fax + ':' : '';
			this._bCityFaxCont.className = '';
			emptySection = false;
		}
		else {
			this._bCityFaxCont.className = 'wm_hide';
		};
		if (cont.bState.length > 0 || cont.bPhone.length > 0) {
			this._bStateObj.innerHTML = cont.bState;
			this._bStateTitle.innerHTML = (cont.bState.length > 0) ? Lang.StateProvince + ':' : '';
			this._bPhoneObj.innerHTML = cont.bPhone;
			this._bPhoneTitle.innerHTML = (cont.bPhone.length > 0) ? Lang.Phone + ':' : '';
			this._bStatePhoneCont.className = '';
			emptySection = false;
		}
		else {
			this._bStatePhoneCont.className = 'wm_hide';
		};
		if (cont.bZip.length > 0 || cont.bCountry.length > 0) {
			this._bZipObj.innerHTML = cont.bZip;
			this._bZipTitle.innerHTML = (cont.bZip.length > 0) ? Lang.ZipCode + ':' : '';
			this._bCountryObj.innerHTML = cont.bCountry;
			this._bCountryTitle.innerHTML = (cont.bCountry.length > 0) ? Lang.CountryRegion + ':' : '';
			this._bZipCountryCont.className = '';
			emptySection = false;
		}
		else {
			this._bZipCountryCont.className = 'wm_hide';
		};
		if (cont.bWeb.length > 0) {
			this._bWebObj.innerHTML = cont.bWeb;
			this._bWebObj.onclick = function () {
				OpenURL(cont.bWeb);
				return false;
			};
			this._bWebCont.className = '';
			emptySection = false;
		}
		else {
			this._bWebCont.className = 'wm_hide';
		};
		if (emptySection) this._businessTbl.className = 'wm_hide';
		else this._businessTbl.className = 'wm_contacts_view';

		emptySection = true;
		if (cont.OtherEmail.length > 0 && cont.PrimaryEmail != 2) {
			this._otherEmailObj.innerHTML = cont.OtherEmail;
			if (cont.UseFriendlyNm && cont.Name.length > 0) {
				var otherEmail = '"' + cont.Name + '" <' + HtmlDecode(cont.OtherEmail) + '>';
			}
			else {
				var otherEmail = HtmlDecode(cont.OtherEmail);
			};
			this._otherEmailObj.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_NEW_MESSAGE,
						FromDrafts: false,
						ForReply: false,
						FromContacts: true,
						ToField: otherEmail
					}
				);
				return false;
			};
			this._otherEmailCont.className = '';
			emptySection = false;
		}
		else {
			this._otherEmailCont.className = 'wm_hide';
		};
		if (cont.Notes.length > 0) {
			this._notesObj.innerHTML = cont.Notes;
			this._notesCont.className = '';
			emptySection = false;
		}
		else {
			this._notesCont.className = 'wm_hide';
		};
		if (emptySection) this._otherTbl.className = 'wm_hide';
		else this._otherTbl.className = 'wm_contacts_view';

		emptySection = true;
		CleanNode(this._groupsObj);
		var groups = cont.Groups;
		var iCount = groups.length;
		var span;
		for (var i=0; i<iCount; i++) {
			var a = CreateChildWithAttrs(this._groupsObj, 'a', [['href', '#']]);
			a.onclick = function () { return false; };
			a.innerHTML = groups[i].Name;
			a.id = groups[i].Id;
			a.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_CONTACTS,
						Entity: PART_VIEW_GROUP,
						IdGroup: this.id
					}
				);
				return false;
			};
			emptySection = false;
			span = CreateChild(this._groupsObj, 'span');
			span.innerHTML = ',&nbsp;';
		};
		if (iCount > 0) span.innerHTML = '';
		if (emptySection) this._groupsTbl.className = 'wm_hide';
		else this._groupsTbl.className = 'wm_contacts_view';

		this._editTbl.className = 'wm_contacts_view';
	},
	
	Build: function (container)
	{
		var obj = this;
		var tbl = CreateChild(container, 'table');
		this._mainTbl = tbl;
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Name + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Name', ':');
		td = tr.insertCell(1);
		td.className = 'wm_contacts_name';
		this._fullnameObj = td;
		this._fullnameCont = tr;

		var tr = tbl.insertRow(1);
		var td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Email + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Email', ':');
		td = tr.insertCell(1);
		td.className = 'wm_contacts_email';
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = function () { return false; };
		this._defaultEmailObj = a;
		this._defaultEmailCont = tr;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Birthday + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Birthday', ':');
		td = tr.insertCell(1);
		this._birthdayObj = td;
		this._birthdayCont = tr;

		/*------Personal------*/
		
		tbl = CreateChild(container, 'table');
		this._personalTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_section_name';
		td.colSpan = 4;
		td.innerHTML = Lang.Home;
		WebMail.LangChanger.Register('innerHTML', td, 'Home', '');
		
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.PersonalEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'PersonalEmail', ':');
		td = tr.insertCell(1);
		td.colSpan = 3;
		//td.className = 'wm_contacts_email';
		var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = function () { return false; };
		this._hEmailObj = a;
		this._hEmailCont = tr;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StreetAddress + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StreetAddress', ':');
		td = tr.insertCell(1);
		td.colSpan = 3;
		this._hStreetObj = td;
		this._hStreetCont = tr;

		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.City + ':';
		this._hCityTitle = td;
		td = tr.insertCell(1);
		this._hCityObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Fax + ':';
		this._hFaxTitle = td;
		td = tr.insertCell(3);
		this._hFaxObj = td;
		this._hCityFaxCont = tr;

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StateProvince + ':';
		this._hStateTitle = td;
		td = tr.insertCell(1);
		this._hStateObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Phone + ':';
		this._hPhoneTitle = td;
		td = tr.insertCell(3);
		this._hPhoneObj = td;
		this._hStatePhoneCont = tr;

		tr = tbl.insertRow(5);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.ZipCode + ':';
		this._hZipTitle = td;
		td = tr.insertCell(1);
		this._hZipObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Mobile + ':';
		this._hMobileTitle = td;
		td = tr.insertCell(3);
		this._hMobileObj = td;
		this._hZipMobileCont = tr;

		tr = tbl.insertRow(6);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.CountryRegion + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'CountryRegion', ':');
		td = tr.insertCell(1);
		td.colSpan = 3;
		this._hCountryObj = td;
		this._hCountryCont = tr;

		tr = tbl.insertRow(7);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.WebPage + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'WebPage', ':');
		td = tr.insertCell(1);
		td.colSpan = 3;
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		this._hWebObj = a;
		this._hWebCont = tr;

		/*------Business------*/
		
		tbl = CreateChild(container, 'table');
		this._businessTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_section_name';
		td.colSpan = 4;
		td.innerHTML = Lang.Business;
		WebMail.LangChanger.Register('innerHTML', td, 'Business', '');
		
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.BusinessEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'BusinessEmail', ':');
		td = tr.insertCell(1);
		td.colSpan = 3;
		//td.className = 'wm_contacts_email';
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = function () { return false; };
		this._bEmailObj = a;
		this._bEmailCont = tr;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Company + ':';
		this._bCompanyTitle = td;
		td = tr.insertCell(1);
		this._bCompanyObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.JobTitle + ':';
		this._bJobTitleTitle = td;
		td = tr.insertCell(3);
		this._bJobTitleObj = td;
		this._bCompanyJobTitleCont = tr;

		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Department + ':';
		this._bDepartmentTitle = td;
		td = tr.insertCell(1);
		this._bDepartmentObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Office + ':';
		this._bOfficeTitle = td;
		td = tr.insertCell(3);
		this._bOfficeObj = td;
		this._bDepartmentOfficeCont = tr;

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StreetAddress + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StreetAddress', ':');
		td = tr.insertCell(1);
		td.colSpan = 3;
		this._bStreetObj = td;
		this._bStreetCont = tr;

		tr = tbl.insertRow(5);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.City + ':';
		this._bCityTitle = td;
		td = tr.insertCell(1);
		this._bCityObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Fax + ':';
		this._bFaxTitle = td;
		td = tr.insertCell(3);
		this._bFaxObj = td;
		this._bCityFaxCont = tr;

		tr = tbl.insertRow(6);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StateProvince + ':';
		this._bStateTitle = td;
		td = tr.insertCell(1);
		this._bStateObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Phone + ':';
		this._bPhoneTitle = td;
		td = tr.insertCell(3);
		this._bPhoneObj = td;
		this._bStatePhoneCont = tr;

		tr = tbl.insertRow(7);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.ZipCode + ':';
		this._bZipTitle = td;
		td = tr.insertCell(1);
		this._bZipObj = td;
		td = tr.insertCell(2);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.CountryRegion + ':';
		this._bCountryTitle = td;
		td = tr.insertCell(3);
		this._bCountryObj = td;
		this._bZipCountryCont = tr;

		tr = tbl.insertRow(8);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.WebPage + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'WebPage', ':');
		td = tr.insertCell(1);
		td.colSpan = 3;
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		this._bWebObj = a;
		this._bWebCont = tr;

		/*------Other------*/
		
		tbl = CreateChild(container, 'table');
		this._otherTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_section_name';
		td.colSpan = 2;
		td.innerHTML = Lang.Other;
		WebMail.LangChanger.Register('innerHTML', td, 'Other', '');

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.OtherEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'OtherEmail', ':');
		td = tr.insertCell(1);
		//td.className = 'wm_contacts_email';
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = function () { return false;};
		this._otherEmailObj = a;
		this._otherEmailCont = tr;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Notes + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Notes', ':');
		td = tr.insertCell(1);
		this._notesObj = td;
		this._notesCont = tr;

		/*------Groups------*/
		
		tbl = CreateChild(container, 'table');
		this._groupsTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title wm_contacts_section_name';
		td.innerHTML = Lang.Groups;
		WebMail.LangChanger.Register('innerHTML', td, 'Groups', '');
		td = tr.insertCell(1);
		td.className = 'wm_contacts_groups';
		this._groupsObj = td;

		/*------Edit------*/

		tbl = CreateChild(container, 'table');
		this._editTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_EDIT_CONTACT,
					IdAddr: obj.Contact.Id
				}
			);
			return false;
		};
		a.innerHTML = Lang.EditContact;
		WebMail.LangChanger.Register('innerHTML', a, 'EditContact', '');
	}
};

function CNewContactScreenPart(skinName, parent)
{
	this._skinName = skinName;
	this._parent = parent;
	this._primaryEmail = 0;
	this.shown = false;
	
	this.Contact = new CContact();
	this.Groups = null;
	this._groupsObjs = Array();
	
	this._mainTbl = null;
	this._moreInfo = null;
	this._buttonsTbl = null;
	this._showMoreInfo = null;
	this._isMoreInfo = false;

	this._notSpecified = null;
	this._defaultEmailSel = null;
	this._defaultEmailObj = null;

	this._hEmailObj = null;
	this._fullnameObj = null;
	this._notesObj = null;
	this._useFriendlyNmObj = null;
	this._hStreetObj = null;
	this._hCityObj = null;
	this._hStateObj = null;
	this._hZipObj = null;
	this._hCountryObj = null;
	this._hPhoneObj = null;
	this._hFaxObj = null;
	this._hMobileObj = null;
	this._hWebObj = null;
	this._bEmailObj = null;
	this._bCompanyObj = null;
	this._bStreetObj = null;
	this._bCityObj = null;
	this._bStateObj = null;
	this._bZipObj = null;
	this._bCountryObj = null;
	this._bJobTitleObj = null;
	this._bDepartmentObj = null;
	this._bOfficeObj = null;
	this._bPhoneObj = null;
	this._bFaxObj = null;
	this._bWebObj = null;
	this._dayObj = null;
	this._yearObj = null;
	this._monthObj = null;
	this._otherEmailObj = null;
	this._groupsObj = null;
	
	this._tabs = Array();

	this.isCreateContact = false;
	this.isSaveContact = false;
}

CNewContactScreenPart.prototype = {
	Show: function ()
	{
		this._mainTbl.className = 'wm_contacts_view';
		if (this._isMoreInfo) {
			this._moreInfo.className = '';
		}
		else {
			this._moreInfo.className = 'wm_hide';
		};
		this._buttonsTbl.className = 'wm_contacts_view';
		this.shown = true;
	},
	
	FillDefaultEmailSel: function ()
	{
		var emails = Array(); var titles = Array();
		emails[0] = HtmlEncode(this._hEmailObj.value);		titles[0] = Lang.Personal;
		emails[1] = HtmlEncode(this._bEmailObj.value);		titles[1] = Lang.Business;
		emails[2] = HtmlEncode(this._otherEmailObj.value);	titles[2] = Lang.Other;
		if (emails[0].length == 0 && emails[1].length == 0 && emails[2].length == 0) {
			this._notSpecified.className = '';
			this._defaultEmailSel.className = 'wm_hide';
		}
		else {
			this._notSpecified.className = 'wm_hide';
			var sel = this._defaultEmailSel;
			CleanNode(sel);
			sel.className = '';
			var opts = Array();
			var existsEmail = -1;
			for (var i=0; i<=2; i++) {
				if (emails[i].length != 0) {
					opts[i] = CreateChildWithAttrs(sel, 'option', [['value', i]]);
					opts[i].innerHTML = titles[i] + ': ' + emails[i];
					if (existsEmail == -1) existsEmail = i;
				}
			};
			if (opts[this._primaryEmail]) opts[this._primaryEmail].selected = true;
			else if (existsEmail != -1) {
				opts[existsEmail].selected = true;
				this._primaryEmail = existsEmail;
			}
		}
	},
	
	ShowMoreInfo: function ()
	{
		var dEmail = this._defaultEmailObj.value;
		switch (this._primaryEmail) {
			case (0): this._hEmailObj.value = dEmail; break;
			case (1): this._bEmailObj.value = dEmail; break;
			case (2): this._otherEmailObj.value = dEmail; break;
		};
		this.FillDefaultEmailSel();
		if (this.shown) {
			this._moreInfo.className = '';
		}
		else {
			this._moreInfo.className = 'wm_hide';
		};
		this._defaultEmailObj.className = 'wm_hide';
		this._showMoreInfo.className = 'wm_hide';
		this._isMoreInfo = true;
		this._parent.ResizeBody();
	},
	
	Hide: function ()
	{
		this._mainTbl.className = 'wm_hide';
		this._moreInfo.className = 'wm_hide';
		this._buttonsTbl.className = 'wm_hide';
		this.shown = false;
	},
	
	HideMoreInfo: function ()
	{
		var emails = Array();
		emails[0] = this._hEmailObj.value;
		emails[1] = this._bEmailObj.value;
		emails[2] = this._otherEmailObj.value;
		if (emails[this._primaryEmail].length == 0) {
			if (emails[0].length != 0)
				this._primaryEmail = 0;
			else if (emails[1].length != 0)
				this._primaryEmail = 1;
			else if (emails[2].length != 0)
				this._primaryEmail = 2;
		};
		this._defaultEmailObj.value = emails[this._primaryEmail];
		this._notSpecified.className = 'wm_hide';
		this._defaultEmailSel.className = 'wm_hide';
		this._moreInfo.className = 'wm_hide';
		this._defaultEmailObj.className = 'wm_input';
		this._showMoreInfo.className = '';
		this._isMoreInfo = false;
		this._parent.ResizeBody();
	},
	
	ChangeTabMode: function (index)
	{
		this._tabs[index].ChangeTabMode(this._skinName);
		this._parent.ResizeBody();
	},
	
	CheckContactUpdate: function ()
	{
		if (this.isCreateContact) {
			WebMail.ShowReport(Lang.ReportContactSuccessfulyAdded);
			this.isCreateContact = false;
		}
		else if (this.isSaveContact) {
			WebMail.ShowReport(Lang.ReportContactUpdatedSuccessfuly);
			this.isSaveContact = false;
		}
	},
	
	FillGroups: function (groups)
	{
		this.Groups = groups;
		this._groupsObjs = Array();
		CleanNode(this._groupsObj);
		var groups = this.Groups.Items;
		var iCount = groups.length;
		for (var i=0; i<iCount; i++) {
			var inp = CreateChildWithAttrs(this._groupsObj, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox'], ['id', 'check_group_' + groups[i].Id]]);
			var lbl = CreateChildWithAttrs(this._groupsObj, 'label', [['for', 'check_group_' + groups[i].Id]]);
			lbl.innerHTML = groups[i].Name;
			var br = CreateChild(this._groupsObj, 'br');
			this._groupsObjs[i] = inp;
		}
	},
	
	Fill: function (cont)
	{
		this.Contact = cont;
		this._primaryEmail = cont.PrimaryEmail;
		switch (cont.PrimaryEmail) {
			case (1): this._defaultEmailObj.value = HtmlDecode(cont.bEmail); break;
			case (2): this._defaultEmailObj.value = HtmlDecode(cont.OtherEmail); break;
			default: this._defaultEmailObj.value = HtmlDecode(cont.hEmail); break;
		};
		this._useFriendlyNmObj.checked = cont.UseFriendlyNm;
		this._fullnameObj.value = HtmlDecode(cont.Name);
		this.FillDay(cont.Day);
		this.FillMonth(cont.Month);
		this.FillYear(cont.Year);
		this._hEmailObj.value = HtmlDecode(cont.hEmail);
		this._hStreetObj.value = HtmlDecode(cont.hStreet);
		this._hCityObj.value = HtmlDecode(cont.hCity);
		this._hStateObj.value = HtmlDecode(cont.hState);
		this._hZipObj.value = HtmlDecode(cont.hZip);
		this._hCountryObj.value = HtmlDecode(cont.hCountry);
		this._hFaxObj.value = HtmlDecode(cont.hFax);
		this._hPhoneObj.value = HtmlDecode(cont.hPhone);
		this._hMobileObj.value = HtmlDecode(cont.hMobile);
		this._hWebObj.value = HtmlDecode(cont.hWeb);

		this._bEmailObj.value = HtmlDecode(cont.bEmail);
		this._bCompanyObj.value = HtmlDecode(cont.bCompany);
		this._bJobTitleObj.value = HtmlDecode(cont.bJobTitle);
		this._bDepartmentObj.value = HtmlDecode(cont.bDepartment);
		this._bOfficeObj.value = HtmlDecode(cont.bOffice);
		this._bStreetObj.value = HtmlDecode(cont.bStreet);
		this._bCityObj.value = HtmlDecode(cont.bCity);
		this._bStateObj.value = HtmlDecode(cont.bState);
		this._bZipObj.value = HtmlDecode(cont.bZip);
		this._bCountryObj.value = HtmlDecode(cont.bCountry);
		this._bFaxObj.value = HtmlDecode(cont.bFax);
		this._bPhoneObj.value = HtmlDecode(cont.bPhone);
		this._bWebObj.value = HtmlDecode(cont.bWeb);

		this._otherEmailObj.value = HtmlDecode(cont.OtherEmail);
		this._notesObj.value = HtmlDecode(cont.Notes);

		var iCount = this._groupsObjs.length;
		if (iCount > 0) {
			this._tabs[3].Show();
		}
		else {
			this._tabs[3].Hide();
		};
		for (var i=0; i<iCount; i++) {
			var id = this._groupsObjs[i].id.substring(12) - 0;
			var checked = false;
			var jCount = cont.Groups.length;
			for (var j=0; j<jCount; j++) {
				if (cont.Groups[j].Id == id)
					checked = true;
			}
			this._groupsObjs[i].checked = checked;
		};

		if (cont.onlyMainData) {
			this.HideMoreInfo();
		}
		else {
			this.ShowMoreInfo();
		};
		
		this._tabs[0].Close(this._skinName);
		this._tabs[1].Close(this._skinName);
		this._tabs[2].Close(this._skinName);
		this._tabs[3].Close(this._skinName);
		if (cont.hasHomeData || !cont.hasBusinessData && !cont.hasOtherData) {
			this._tabs[0].Open(this._skinName);
		};
		if (cont.hasBusinessData) {
			this._tabs[1].Open(this._skinName);
		};
		if (cont.hasOtherData) {
			this._tabs[2].Open(this._skinName);
		}
	},
	
	CancelChanges: function ()
	{
		var id = this.Contact.Id;
		if (id != -1) {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_VIEW_CONTACT,
					IdAddr: id
				}
			);
		}
		else {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_CONTACTS
				}
			);
		}
	},
	
	SaveChanges: function ()
	{
		/* validation */
		var val = new CValidate();
		var fullNameValue = this._fullnameObj.value;
		var defEmailValue = this._defaultEmailObj.value;
		var hEmailValue = this._hEmailObj.value;
		var bEmailValue = this._bEmailObj.value;
		var oEmailValue = this._otherEmailObj.value;
		if (this._isMoreInfo) {
			if (val.IsEmpty(fullNameValue) && val.IsEmpty(hEmailValue) && val.IsEmpty(bEmailValue) &&
			 val.IsEmpty(oEmailValue)) {
				alert(Lang.WarningContactNotComplete);
				return;
			}
		}
		else {
			if (val.IsEmpty(fullNameValue) && val.IsEmpty(defEmailValue)) {
				alert(Lang.WarningContactNotComplete);
				return;
			}
		};
		if (val.HasEmailForbiddenSymbols(defEmailValue) || val.HasEmailForbiddenSymbols(hEmailValue) ||
		 val.HasEmailForbiddenSymbols(bEmailValue) || val.HasEmailForbiddenSymbols(oEmailValue)) {
			alert(Lang.WarningCorrectEmail);
			return;
		};
		var hWebValue = val.CorrectWebPage(this._hWebObj.value);
		var bWebValue = val.CorrectWebPage(this._bWebObj.value);

		/* saving */
		var params = this._parent.GetXmlParams();
		var attrs = '';
		var id = this.Contact.Id;
		if (id != -1) attrs += ' id="' + id + '"';
		attrs += ' primary_email="' + this._primaryEmail + '"';
		if (this._useFriendlyNmObj.checked)
			attrs += ' use_friendly_nm="1"';
		else
			attrs += ' use_friendly_nm="0"';
		var nodes = '';
		nodes += '<fullname>' + GetCData(fullNameValue) + '</fullname>';
		nodes += '<birthday day="' + this._dayObj.value + '" month="' + this._monthObj.value + '" year="' + this._yearObj.value + '"/>';
		var personal = '';
		if (!this._isMoreInfo && this._primaryEmail == 0) {
			personal += '<email>' + GetCData(defEmailValue) + '</email>';
		}
		else {
			personal += '<email>' + GetCData(hEmailValue) + '</email>';
		};
		personal += '<street>' + GetCData(this._hStreetObj.value) + '</street>';
		personal += '<city>' + GetCData(this._hCityObj.value) + '</city>';
		personal += '<state>' + GetCData(this._hStateObj.value) + '</state>';
		personal += '<zip>' + GetCData(this._hZipObj.value) + '</zip>';
		personal += '<country>' + GetCData(this._hCountryObj.value) + '</country>';
		personal += '<fax>' + GetCData(this._hFaxObj.value) + '</fax>';
		personal += '<phone>' + GetCData(this._hPhoneObj.value) + '</phone>';
		personal += '<mobile>' + GetCData(this._hMobileObj.value) + '</mobile>';
		personal += '<web>' + GetCData(hWebValue) + '</web>';
		nodes += '<personal>' + personal + '</personal>';
		var business = '';
		if (!this._isMoreInfo && this._primaryEmail == 1) {
			business += '<email>' + GetCData(defEmailValue) + '</email>';
		}
		else {
			business += '<email>' + GetCData(bEmailValue) + '</email>';
		};
		business += '<company>' + GetCData(this._bCompanyObj.value) + '</company>';
		business += '<job_title>' + GetCData(this._bJobTitleObj.value) + '</job_title>';
		business += '<department>' + GetCData(this._bDepartmentObj.value) + '</department>';
		business += '<office>' + GetCData(this._bOfficeObj.value) + '</office>';
		business += '<street>' + GetCData(this._bStreetObj.value) + '</street>';
		business += '<city>' + GetCData(this._bCityObj.value) + '</city>';
		business += '<state>' + GetCData(this._bStateObj.value) + '</state>';
		business += '<zip>' + GetCData(this._bZipObj.value) + '</zip>';
		business += '<country>' + GetCData(this._bCountryObj.value) + '</country>';
		business += '<fax>' + GetCData(this._bFaxObj.value) + '</fax>';
		business += '<phone>' + GetCData(this._bPhoneObj.value) + '</phone>';
		business += '<web>' + GetCData(bWebValue) + '</web>';
		nodes += '<business>' + business + '</business>';
		var other = '';
		if (!this._isMoreInfo && this._primaryEmail == 2) {
			other += '<email>' + GetCData(defEmailValue) + '</email>';
		}
		else {
			other += '<email>' + GetCData(oEmailValue) + '</email>';
		};
		other += '<notes>' + GetCData(this._notesObj.value) + '</notes>';
		nodes += '<other>' + other + '</other>';

		var groups = '';
		var iCount = this._groupsObjs.length;
		for (var i=0; i<iCount; i++) {
			if (this._groupsObjs[i].checked)
				groups += '<group id="' + this._groupsObjs[i].id.substring(12) + '"/>';
		};

		nodes += '<groups>' + groups + '</groups>';
		var xml = params + '<contact' + attrs + '>' + nodes + '</contact>';
		if (id == -1) {
			RequestHandler('new', 'contact', xml);
			this.isCreateContact = true;
		}
		else {
			RequestHandler('update', 'contact', xml);
			this.isSaveContact = true;
		}
	},
	
	FillMonth: function (month)
	{
		var obj = this;
		var sel = this._monthObj;
		CleanNode(sel);
		var opt;
		var iCount = Lang.Monthes.length;
		for (var i=0; i<iCount; i++) {
			opt = CreateChildWithAttrs(sel, 'option', [['value', i]]);
			opt.innerHTML = Lang.Monthes[i];
			if (month == i) opt.selected = true;
		};
		sel.onchange = function () {
			obj.FillDay(obj._dayObj.value);
		}
	},
	
	FillDay: function (day)
	{
		var daysInMonth = [31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		var year = this._yearObj.value;
		if (year == 0 || (year % 4) == 0 && (year % 100) != 0 || (year % 400) == 0) 
			daysInMonth[2] = 29;
		var month = this._monthObj.value;
		var sel = this._dayObj;
		CleanNode(sel);
		var opt = CreateChildWithAttrs(sel, 'option', [['value', 0]]);
		opt.innerHTML = Lang.Day;
		for (var i=1; i<=daysInMonth[month]; i++) {
			opt = CreateChildWithAttrs(sel, 'option', [['value', i]]);
			opt.innerHTML = i;
			if (day == i) opt.selected = true;
		};
		if (day > daysInMonth[month]) {
			opt.selected = true;
		}
	},
	
	FillYear: function (year)
	{
		var obj = this;
		var sel = this._yearObj;
		CleanNode(sel);
		var opt = CreateChildWithAttrs(sel, 'option', [['value', 0]]);
		opt.innerHTML = Lang.Year;
		var now = new Date;
		var firstYear = now.getYear();
		if (!Browser.IE)
			firstYear = firstYear + 1900;
		var lastYear = firstYear - 100;
		for (var i=firstYear; i>=lastYear; i--) {
			opt = CreateChildWithAttrs(sel, 'option', [['value', i]]);
			opt.innerHTML = i;
			if (year == i) opt.selected = true;
		};
		sel.onchange = function () {
			if (obj._monthObj.value == '2')
				obj.FillDay(obj._dayObj.value);
		}
	},
	
	TextAreaLimit: function (ev)
	{
		return TextAreaLimit(ev, this, 85);
	},
	
	Build: function (container)
	{
		var obj = this;
		var tbl = CreateChild(container, 'table');
		this._mainTbl = tbl;
		tbl.className = 'wm_hide';
		var rowIndex = 0;
		var tr = tbl.insertRow(rowIndex++);
		var td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.style.width = '25%';
		td.innerHTML = Lang.DefaultEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'DefaultEmail', ':');
		td = tr.insertCell(1);
		td.style.width = '75%';
		var span = CreateChild(td, 'span');
		span.innerHTML = Lang.NotSpecifiedYet;
		WebMail.LangChanger.Register('innerHTML', span, 'NotSpecifiedYet', ':');
		span.className = 'wm_hide';
		this._notSpecified = span;
		var sel = CreateChild(td, 'select');
		sel.className = 'wm_hide';
		sel.onchange = function () { obj._primaryEmail = this.value - 0; };
		sel.style.width = '200px';
		this._defaultEmailSel = sel;
		this._defaultEmailObj = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['maxlength', '255']]);

		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.ContactName + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'ContactName', ':');
		td = tr.insertCell(1);
		this._fullnameObj = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['maxlength', '85']]);

		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		td = tr.insertCell(1);
		this._useFriendlyNmObj = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox'], ['id', 'use_friendly_nm_contacts']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'use_friendly_nm_contacts']]);
		lbl.innerHTML = Lang.UseFriendlyName1;
		WebMail.LangChanger.Register('innerHTML', lbl, 'UseFriendlyName1', '');
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'use_friendly_nm_contacts']]);
		lbl.innerHTML = Lang.UseFriendlyName2;
		lbl.className = 'wm_secondary_info wm_inline_info';
		WebMail.LangChanger.Register('innerHTML', lbl, 'UseFriendlyName2', '');
		
		var div = CreateChild(container, 'div');
		div.className = 'wm_hide';
		this._moreInfo = div;
		tbl = CreateChild(div, 'table');
		tbl.className = 'wm_contacts_view';
		tbl.style.width = '90%';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_hide_section';
		var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.innerHTML = Lang.HideAddFields;
		WebMail.LangChanger.Register('innerHTML', a, 'HideAddFields', '');
		a.onclick = function () { obj.HideMoreInfo(); return false; };

		/*------Personal------*/
		
		var tabTbl = CreateChild(div, 'table');
		tabTbl.onclick = function () { obj.ChangeTabMode(0); };
		tabTbl.className = 'wm_contacts_tab';
		tr = tabTbl.insertRow(0);
		td = tr.insertCell(0);
		var span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_name';
		span.innerHTML = Lang.Home;
		WebMail.LangChanger.Register('innerHTML', span, 'Home', '');
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_mode';
		var img = CreateChild(span, 'img');
		img.src = 'skins/' + this._skinName + '/menu/arrow_up.gif';
		
		tbl = CreateChild(div, 'table');
		this._tabs[0] = new CContactTab(tbl, img, tabTbl);
		tbl.className = 'wm_contacts_view';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.style.width = '20%';
		td.innerHTML = Lang.PersonalEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'PersonalEmail', ':');
		td = tr.insertCell(1);
		td.style.width = '80%';
		td.colSpan = 4;
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '45'], ['maxlength', '255']]);
		inp.onchange = function () { obj.FillDefaultEmailSel(); };
		this._hEmailObj = inp;

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StreetAddress + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StreetAddress', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		var txt = CreateChildWithAttrs(td, 'textarea', [['class', 'wm_input'], ['cols', '35'], ['rows', '2']]);
		txt.onkeydown = this.TextAreaLimit;
		this._hStreetObj = txt;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.style.width = '20%';
		td.innerHTML = Lang.City + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'City', ':');
		td = tr.insertCell(1);
		td.style.width = '30%';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._hCityObj = inp;
		td = tr.insertCell(2);
		td.style.width = '10%';
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.style.width = '10%';
		td.innerHTML = Lang.Fax + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Fax', ':');
		td = tr.insertCell(4);
		td.style.width = '30%';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '50']]);
		this._hFaxObj = inp;

		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StateProvince + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StateProvince', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._hStateObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Phone + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Phone', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '50']]);
		this._hPhoneObj = inp;

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.ZipCode + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'ZipCode', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '10']]);
		this._hZipObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Mobile + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Mobile', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '50']]);
		this._hMobileObj = inp;

		tr = tbl.insertRow(5);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.CountryRegion + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'CountryRegion', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._hCountryObj = inp;

		tr = tbl.insertRow(6);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.WebPage + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'WebPage', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '45'], ['maxlength', '85']]);
		this._hWebObj = inp;
		CreateTextChild(td, ' ');
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Go]]);
		WebMail.LangChanger.Register('value', inp, 'Go', '');
		inp.onclick = function () { OpenURL(obj._hWebObj.value); };

		/*------Business------*/
		
		tabTbl = CreateChild(div, 'table');
		tabTbl.onclick = function () { obj.ChangeTabMode(1); };
		tabTbl.className = 'wm_contacts_tab';
		tr = tabTbl.insertRow(0);
		td = tr.insertCell(0);
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_name';
		span.innerHTML = Lang.Business;
		WebMail.LangChanger.Register('innerHTML', span, 'Business', '');
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_mode';
		img = CreateChild(span, 'img');
		img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';
		
		tbl = CreateChild(div, 'table');
		this._tabs[1] = new CContactTab(tbl, img, tabTbl);
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.style.width = '20%';
		td.innerHTML = Lang.BusinessEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'BusinessEmail', ':');
		td = tr.insertCell(1);
		td.style.width = '80%';
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '45'], ['maxlength', '255']]);
		inp.onchange = function () { obj.FillDefaultEmailSel(); };
		this._bEmailObj = inp;

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.style.width = '20%';
		td.innerHTML = Lang.Company + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Company', ':');
		td = tr.insertCell(1);
		td.style.width = '30%';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._bCompanyObj = inp;
		td = tr.insertCell(2);
		td.style.width = '5%';
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.style.width = '15%';
		td.innerHTML = Lang.JobTitle + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'JobTitle', ':');
		td = tr.insertCell(4);
		td.style.width = '30%';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '30']]);
		this._bJobTitleObj = inp;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Department + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Department', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._bDepartmentObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Office + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Office', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._bOfficeObj = inp;

		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StreetAddress + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StreetAddress', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		txt = CreateChildWithAttrs(td, 'textarea', [['class', 'wm_input'], ['cols', '35'], ['rows', '2']]);
		txt.onkeydown = this.TextAreaLimit;
		this._bStreetObj = txt;

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.City + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'City', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._bCityObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Fax + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Fax', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '50']]);
		this._bFaxObj = inp;

		tr = tbl.insertRow(5);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.StateProvince + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'StateProvince', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._bStateObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Phone + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Phone', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '50']]);
		this._bPhoneObj = inp;

		tr = tbl.insertRow(6);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.ZipCode + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'ZipCode', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '10']]);
		this._bZipObj = inp;
		td = tr.insertCell(2);
		td = tr.insertCell(3);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.CountryRegion + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'CountryRegion', ':');
		td = tr.insertCell(4);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '18'], ['maxlength', '65']]);
		this._bCountryObj = inp;

		tr = tbl.insertRow(7);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.WebPage + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'WebPage', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '45'], ['maxlength', '85']]);
		this._bWebObj = inp;
		CreateTextChild(td, ' ');
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Go]]);
		WebMail.LangChanger.Register('value', inp, 'Go', '');
		inp.onclick = function () { OpenURL(obj._bWebObj.value); };

		/*------Other------*/
		
		tabTbl = CreateChild(div, 'table');
		tabTbl.onclick = function () { obj.ChangeTabMode(2); };
		tabTbl.className = 'wm_contacts_tab';
		tr = tabTbl.insertRow(0);
		td = tr.insertCell(0);
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_name';
		span.innerHTML = Lang.Other;
		WebMail.LangChanger.Register('innerHTML', span, 'Other', '');
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_mode';
		img = CreateChild(span, 'img');
		img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';
		
		tbl = CreateChild(div, 'table');
		this._tabs[2] = new CContactTab(tbl, img, tabTbl);
		tbl.className = 'wm_hide';
		rowIndex = 0;
		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Birthday + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Birthday', ':');
		td = tr.insertCell(1);
		this._monthObj = CreateChild(td, 'select');
		this._dayObj = CreateChild(td, 'select');
		this._yearObj = CreateChild(td, 'select');
		this.FillMonth(0);
		this.FillDay(0);
		this.FillYear(0);
		
		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.style.width = '20%';
		td.innerHTML = Lang.OtherEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'OtherEmail', ':');
		td = tr.insertCell(1);
		td.style.width = '80%';
		td.colSpan = 4;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '45'], ['maxlength', '255']]);
		inp.onchange = function () { obj.FillDefaultEmailSel(); };
		this._otherEmailObj = inp;

		tr = tbl.insertRow(rowIndex++);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_view_title';
		td.innerHTML = Lang.Notes + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Notes', ':');
		td = tr.insertCell(1);
		td.colSpan = 4;
		txt = CreateChildWithAttrs(td, 'textarea', [['class', 'wm_input'], ['cols', '35'], ['rows', '2']]);
		txt.onkeydown = this.TextAreaLimit;
		this._notesObj = txt;

		/*------Groups------*/
		
		var tabTbl = CreateChild(div, 'table');
		tabTbl.onclick = function () { obj.ChangeTabMode(3); };
		tabTbl.className = 'wm_contacts_tab';
		tr = tabTbl.insertRow(0);
		td = tr.insertCell(0);
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_name';
		span.innerHTML = Lang.Groups;
		WebMail.LangChanger.Register('innerHTML', span, 'Groups', '');
		span = CreateChild(td, 'span');
		span.className = 'wm_contacts_tab_mode';
		img = CreateChild(span, 'img');
		img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';
		
		tbl = CreateChild(div, 'table');
		this._tabs[3] = new CContactTab(tbl, img, tabTbl);
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		this._groupsObj = td;

		/*------Buttons------*/

		tbl = CreateChild(container, 'table');
		this._buttonsTbl = tbl;
		tbl.className = 'wm_hide';
		tbl.style.width = '90%';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.style.textAlign = 'right';
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.innerHTML = Lang.ShowAddFields;
		WebMail.LangChanger.Register('innerHTML', a, 'ShowAddFields', '');
		a.onclick = function () { obj.ShowMoreInfo(); return false; };
		this._showMoreInfo = a;
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_contacts_save_button';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '');
		inp.onclick = function () { obj.SaveChanges(); };
		CreateTextChild(td, ' ');
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Cancel]]);
		WebMail.LangChanger.Register('value', inp, 'Cancel', '');
		inp.onclick = function () { obj.CancelChanges(); };
	}
};