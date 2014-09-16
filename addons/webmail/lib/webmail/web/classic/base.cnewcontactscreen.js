/*
Classes:
	CNewContactScreenPart
*/

function CNewContactScreenPart(isEdit)
{
	this._moreInfo = document.getElementById("more_info");
	
	this._isMoreInfo = false;
	this._moreInfo = document.getElementById("more_info_div");
	this._showMoreInfo = document.getElementById("more_info_show");
	this._hideMoreInfo = document.getElementById("more_info_hide");

	this._defaultEmailSel = document.getElementById("select_default_email");
	this._defaultEmailInp = document.getElementById("input_default_email");
	this._notSpecified = document.getElementById("notSpecified");
	this._fullname = document.getElementById("c_fullname");
	
	this._bEmailObj = document.getElementById("business_email");
	this._hEmailObj = document.getElementById("personal_email");
	this._otherEmailObj = document.getElementById("other_email");
	this._defEmailType = document.getElementById("default_email_type");
	
	this._viewTbl = document.getElementById("viewTbl");
	this._editTbl = document.getElementById("editTbl");
	this._switch_to_edit = document.getElementById("switch_to_edit");
	
	this._contactScreen = isEdit; // 0 - view contact, 1- edir contact
	this._emailAdded = false;
	
}
		
CNewContactScreenPart.prototype = {		
		
		FillContact: function(mode, isOpen)
		{
			var obj = this;
			if (mode == 0)
			{
				obj._viewTbl.className = "";	
				obj._editTbl.className = "wm_hide";

				this._switch_to_edit.onclick = function() {
					obj._contactScreen = 1;
					obj.FillContact(1);
					if (isOpen) obj.ShowMoreInfo();
					ResizeElements("all");
					return false;
				}
			}
			else if (mode == 1)
			{
				obj._viewTbl.className = "wm_hide";			
				obj._editTbl.className = "";	
				
					switch (parseInt(this._defEmailType.value)) {
					case 0:
						this._defaultEmailInp.value = this._hEmailObj.value;
						break;
					case 1:
						this._defaultEmailInp.value = this._bEmailObj.value;
						break;
					case 2:
						this._defaultEmailInp.value = this._otherEmailObj.value;
						break;
					default:
						this._defaultEmailInp.value = this._hEmailObj.value;
						break;
					}

				this._defaultEmailInp.onchange = function()
				{
					obj.FillMailFromDefault();
				}
					
				obj.FillDefaultEmailSel();		
				this._hEmailObj.onchange = function()
				{
					obj.FillDefaultEmailSel();
				}
				this._bEmailObj.onchange = function()
				{
					obj.FillDefaultEmailSel();
				}
				this._otherEmailObj.onchange = function()
				{
					obj.FillDefaultEmailSel();
				}
				this._showMoreInfo.onclick = function()
				{
					obj.ShowMoreInfo();
					ResizeElements("all");
					return false;
				}
				this._hideMoreInfo.onclick = function()
				{
					obj.HideMoreInfo();
					ResizeElements("all");
					return false;
				}
				
				if (this._isMoreInfo == false)
				{
					this._notSpecified.className = 'wm_hide';	
					this._defaultEmailSel.className = 'wm_hide';
					this._defaultEmailInp.className = 'wm_input';				
				}
				else
				{
					if (this._hEmailObj.length == 0 && this._bEmailObj.length == 0 && this._otherEmailObj.length == 0)
					{
						this._notSpecified.className = '';
						this._defaultEmailSel.className = 'wm_hide';
					}
					else
					{	
						this._notSpecified.className = 'wm_hide';
						this._defaultEmailSel.className = '';
					}
					
					this._defaultEmailInp = 'wm_hide';
				}
			
			} 
			
		},
		
		FillMailFromDefault: function()
		{
			switch (parseInt(this._defEmailType.value)) {
				case 0:
					this._hEmailObj.value = this._defaultEmailInp.value;
					break;
				case 1:
					this._bEmailObj.value = this._defaultEmailInp.value;
					break;
				case 2:
					this._otherEmailObj.value = this._defaultEmailInp.value;
					break;
				default:
					this._hEmailObj.value = this._defaultEmailInp.value;
					break;
			}			
		},
		
		FillDefaultByOption: function()
		{
			this._defEmailType.value = this._defaultEmailSel.value;
			switch (parseInt(this._defEmailType.value)) {
			case 0:
				this._defaultEmailInp.value = this._hEmailObj.value;
				break;
			case 1:
				this._defaultEmailInp.value = this._bEmailObj.value;
				break;
			case 2:
				this._defaultEmailInp.value = this._otherEmailObj.value;
				break;
			default:
				this._defaultEmailInp.value = this._hEmailObj.value;
				break;
			}
		},
		
		InitEditContacts: function(isOpen)
		{
			if (this._contactScreen == 0) //view contact
			{
				this.FillContact(0, isOpen);
				
			} else if (this._contactScreen == 1) { //edit contact

				this.FillContact(1, isOpen);
			}
			var obj = this;
			this._defaultEmailSel.onchange = function()
			{
			
				obj._defEmailType.value = obj._defaultEmailSel.value;
			}
		},//Init contacts
		
		SubmitContact: function()
		{
			if ((this._fullname && this._fullname.value.length > 0) ||
				(this._hEmailObj && this._hEmailObj.value.length > 0) ||
				(this._bEmailObj && this._bEmailObj.value.length > 0) ||
				(this._otherEmailObj && this._otherEmailObj.value.length > 0) ||
				(this._defaultEmailInp && this._defaultEmailInp.value.length > 0))
			{
				return true;
			}
			return false;
		},
				
		FillDefaultEmailSel: function()
		{
			var emails = Array(); 
			var titles = Array();
			emails[0] = HtmlEncode(this._hEmailObj.value);     titles[0] = Lang.Personal;
			emails[1] = HtmlEncode(this._bEmailObj.value);     titles[1] = Lang.Business;
			emails[2] = HtmlEncode(this._otherEmailObj.value); titles[2] = Lang.Other;
			if (emails[0].length == 0 && emails[1].length == 0 && emails[2].length == 0)
			{
				this._notSpecified.className = '';
				this._defaultEmailSel.className = 'wm_hide';
			} 
			else
			{
				this._notSpecified.className = 'wm_hide';
				var sel = this._defaultEmailSel;
				CleanNode(sel);
				sel.className = '';
				var opts = Array();
				var existsEmail = -1;
				for (var i=0; i<=2; i++)
				{
					if (emails[i].length != 0)
					{
						opts[i] = CreateChildWithAttrs(sel, 'option', [['value', i]]);
						opts[i].innerHTML = titles[i] + ': ' + emails[i];
						if (existsEmail == -1) existsEmail = i;
					}
				}
				if (opts[this._defEmailType.value]) 
				{
					opts[this._defEmailType.value].selected = true;
				}
				else if (existsEmail != -1) 
				{
					opts[existsEmail].selected = true;
					this._defEmailType.value = existsEmail;
				}
			}
			
		}, //FillDefaultEmailSel
		
		addOption: function(parent, text, value, isDefaultSelected, isSelected)
		{
			var oOption = document.createElement("option");
			oOption.appendChild(document.createTextNode(text));
			oOption.setAttribute("value", value);

			if (isDefaultSelected) oOption.defaultSelected = true;
		  	else if (isSelected) oOption.selected = true;
		
		  	parent.appendChild(oOption);
		},
		
		removeAll: function(parent)
		{
			parent.options.length = 0;
		},
		
		ShowMoreInfo: function()
		{
			this._moreInfo.className = '';
			this._showMoreInfo.className = 'wm_hide';

			if (this._hEmailObj.value.length == 0 && this._bEmailObj.value.length == 0 && this._otherEmailObj.value.length == 0)
			{
				if (this._defaultEmailInp.value.length != 0)
				{
					this._defaultEmailSel.className = '';
					this.addOption(this._defaultEmailSel, Lang.Personal + ": " + this._defaultEmailInp.value, 0); //******
					this._emailAdded = true;
					this._hEmailObj.value = this._defaultEmailInp.value;
					this._notSpecified.className = 'wm_hide';
					
				} else {
					this._notSpecified.className = '';
					this._defaultEmailSel.className = 'wm_hide';
				}
			}
			else
			{	
				var obj = this.TakeEmailAccordingHiddenField(this._defaultEmailSel.options[this._defaultEmailSel.selectedIndex].value);
				obj.value = this._defaultEmailInp.value;
				this.FillDefaultEmailSel();
			
				this._notSpecified.className = 'wm_hide';
				this._defaultEmailSel.className = '';
			}
			this._defaultEmailInp.className = 'wm_hide';
			this._isMoreInfo = true;
		},
		
		HideMoreInfo: function()
		{
			this.FillDefaultByOption();
			this._moreInfo.className = 'wm_hide';
			this._defaultEmailSel.className = 'wm_hide';
			this._showMoreInfo.className = '';
			this._defaultEmailInp.className = 'wm_input';
			if (this._emailAdded == true)
			{
				this._defEmailType.value = this._defaultEmailSel.options[this._defaultEmailSel.selectedIndex].value;
				if (this._defaultEmailSel.value.length != 0) this._defaultEmailInp.value = this.TakeEmailAccordingHiddenField(this._defEmailType.value).value;
			}
			this._notSpecified.className = 'wm_hide';
			this._isMoreInfo = false;
		},
		
		TakeEmailAccordingHiddenField: function(index)
		{
			if (index == 0) return this._hEmailObj;
			else if (index == 1) return this._bEmailObj;
			else if (index == 2) return this._otherEmailObj;
		}
		
}