/*
Classes:
	CContact
	CContacts
	CGroups
	CGroup
*/

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
					break;
				case 'personal':
					var PersonalParts = ContactParts[i].childNodes;
					var jCount = PersonalParts.length;
					for (var j=0; j<jCount; j++) {
						var parts = PersonalParts[j].childNodes;
						if (parts.length > 0)
							switch (PersonalParts[j].tagName) {
								case 'email':
									this.hEmail = Trim(parts[0].nodeValue);
									break;
								case 'street':
									this.hStreet = Trim(parts[0].nodeValue);
									break;
								case 'city':
									this.hCity = Trim(parts[0].nodeValue);
									break;
								case 'state':
									this.hState = Trim(parts[0].nodeValue);
									break;
								case 'zip':
									this.hZip = Trim(parts[0].nodeValue);
									break;
								case 'country':
									this.hCountry = Trim(parts[0].nodeValue);
									break;
								case 'fax':
									this.hFax = Trim(parts[0].nodeValue);
									break;
								case 'phone':
									this.hPhone = Trim(parts[0].nodeValue);
									break;
								case 'mobile':
									this.hMobile = Trim(parts[0].nodeValue);
									break;
								case 'web':
									this.hWeb = Trim(parts[0].nodeValue);
									break;
							}//switch
					}//for
					break;
				case 'business':
					var BusinessParts = ContactParts[i].childNodes;
					var jCount = BusinessParts.length;
					for (var j=0; j<jCount; j++) {
						var parts = BusinessParts[j].childNodes;
						if (parts.length > 0)
							switch (BusinessParts[j].tagName) {
								case 'email':
									this.bEmail = Trim(parts[0].nodeValue);
									break;
								case 'company':
									this.bCompany = Trim(parts[0].nodeValue);
									break;
								case 'job_title':
									this.bJobTitle = Trim(parts[0].nodeValue);
									break;
								case 'department':
									this.bDepartment = Trim(parts[0].nodeValue);
									break;
								case 'office':
									this.bOffice = Trim(parts[0].nodeValue);
									break;
								case 'street':
									this.bStreet = Trim(parts[0].nodeValue);
									break;
								case 'city':
									this.bCity = Trim(parts[0].nodeValue);
									break;
								case 'state':
									this.bState = Trim(parts[0].nodeValue);
									break;
								case 'zip':
									this.bZip = Trim(parts[0].nodeValue);
									break;
								case 'country':
									this.bCountry = Trim(parts[0].nodeValue);
									break;
								case 'fax':
									this.bFax = Trim(parts[0].nodeValue);
									break;
								case 'phone':
									this.bPhone = Trim(parts[0].nodeValue);
									break;
								case 'web':
									this.bWeb = Trim(parts[0].nodeValue);
									break;
							}//switch
					}//for
					break;
				case 'other':
					var otherParts = ContactParts[i].childNodes;
					var jCount = otherParts.length;
					for (var j=0; j<jCount; j++) {
						var parts = otherParts[j].childNodes;
						if (parts.length > 0)
							switch (otherParts[j].tagName) {
								case 'email':
									this.OtherEmail = Trim(parts[0].nodeValue);
									break;
								case 'notes':
									this.Notes = Trim(parts[0].nodeValue);
									break;
							}//switch
					}//for
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
								}
								this.Groups.push({Id: groupId, Name: groupName});
								break;
						}//switch
					}//for
					break;
			}//switch
		}//for
	}//GetFromXML
}

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
					for (var j=0; j<ContactParts.length; j++) {
						var parts = ContactParts[j].childNodes;
						if (parts.length > 0)
							switch (ContactParts[j].tagName){
								case 'name':
									if (this.LookFor.length > 0 && this.SearchType == 0) {
										var name = MakeSearchResult(Trim(parts[0].nodeValue), this.LookFor, this.SearchType);
									} else {
										var name = Trim(parts[0].nodeValue);
									}
									break;
								case 'email':
									if (this.LookFor.length > 0 && this.SearchType == 0) {
										var clearEmail = Trim(parts[0].nodeValue);
										var email = MakeSearchResult(clearEmail, this.LookFor, this.SearchType);
									} else {
										var clearEmail = Trim(parts[0].nodeValue);
										var email = clearEmail;
									}
									break;
							}//switch
					}//for
					if (this.SearchType == 1) {
						var displayText = '';
						var replaceText = '';
						if (isGroup) {
							displayText = MakeSearchResult(name, this.LookFor, this.SearchType);
							replaceText = HtmlDecode(email);
						} else if (name.length > 0) {
							displayText = '"' + MakeSearchResult(name, this.LookFor, this.SearchType) + '" &lt;' + MakeSearchResult(email, this.LookFor, this.SearchType) + '&gt;';
							replaceText = HtmlDecode('"' + name + '" <' + email + '>');
						} else {
							displayText = MakeSearchResult(email, this.LookFor, this.SearchType);
							replaceText = HtmlDecode(email);
						}
						this.List.push({Id: id, IsGroup: isGroup, DisplayText: displayText, ReplaceText: replaceText});
					} else {
						this.List.push({Id: id, IsGroup: isGroup, Name: name, Email: email, ClearEmail: clearEmail});
					}
				break;
				case 'look_for':
					attr = ContactsXML[i].getAttribute('type'); if (attr) this.SearchType = attr - 0;
					var LookForParts = ContactsXML[i].childNodes;
					if (LookForParts.length > 0) {
						this.LookFor = Trim(LookForParts[0].nodeValue);
					}
				break;
			}//switch
		}//for
	}//GetFromXML
}

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
					}
					this.Items.push({Id: id, Name: name});
					break;
			}//switch
		}//for
	}//GetFromXML
}

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
		if (this.Id != -1)
		{
			attrs += ' id="' + this.Id + '"';
		}
		if (this.isOrganization)
		{
			attrs += ' organization="1"';
		}
		else
		{
			attrs += ' organization="0"';
		}

		var contacts = '';
		var iCount = this.Contacts.length;
		for (var i=0; i<iCount; i++)
		{
			contacts += '<contact id="' + this.Contacts[i].Id + '"/>';
		}
		var newContacts = '';
		var iCount = this.NewContacts.length;
		for (var i=0; i<iCount; i++)
		{
			newContacts += '<contact><personal><email>' + GetCData(this.NewContacts[i].Email) + '</email></personal></contact>';
		}
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
							var id = -1
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
}
