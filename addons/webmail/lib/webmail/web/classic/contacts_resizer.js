
function ResizeElements(mode)
{
	CContactsList.ResizeBody();
} 

function CContactsList()
{
	this.logo		= document.getElementById("logo");
	this.accountsBar = document.getElementById("accountslist");
	this.toolBar	= document.getElementById("toolbar");
	this.lowToolBar = document.getElementById("lowtoolbar");
	this.copyright	= document.getElementById("copyright");
	this.contacts_viewer_div = document.getElementById("contacts_viewer_div");
	this.contacts_viewer = document.getElementById("contacts_viewer");
//	this.contacts_card_min_width = document.getElementById("contacts_card_min_width");
	this.page_switcher = PageSwitcher;
	this.emailObj = document.getElementById("emailobj");

//logo + accountslist + toolbar + lowtoolbar + copyright
	this.copyrightMargin = 30;
	this.externalHeight = 58 + 32 + 27 + 28 + 40 + this.copyrightMargin;
	this.listWidthPercent = 40;
	this.contactsHeadersWidth = 175;
	
	this.container			= document.getElementById('contacts');
	this.contact_list		= document.getElementById('list');
	this.contact_list_div	= document.getElementById('contact_list_div');
	this.contact_list_headers	= document.getElementById("contact_list_headers");

}
CContactsList.prototype =
{
	ResizeBody: function(mode)
	{
		var listBorderHeight = 1;

		var height = GetHeight() - this.GetExternalHeight() - listBorderHeight;
		this.minListHeight = this.contact_list.offsetHeight + this.contact_list_headers.offsetHeight;
		if (height < this.minListHeight) height = this.minListHeight;
		this.contact_list_div.style.height = height + 'px';
		
		var width = GetWidth();
		var listWidth = Math.ceil(width*this.listWidthPercent/100);
		if (listWidth < this.contactsHeadersWidth + 150) {
			listWidth = this.contactsHeadersWidth + 150;
		}
		this.container.style.width = listWidth + 'px';
		this.contact_list_div.style.width = listWidth + 'px';
		this.contact_list.style.width = listWidth + 'px';
		if (this.emailObj != null) {
			var emailWidth = listWidth - this.contactsHeadersWidth;
			this.emailObj.style.width = emailWidth + 'px';
		}
		this.ReplacePageSwitcher();

		var listBorderWidth = 1;
		var viewerWidth = width - listWidth - listBorderWidth;
		
		this.contacts_viewer_div.style.width = viewerWidth + 'px';
		//this.contacts_card_min_width.style.width = viewerWidth + 'px';
	},
	
	ReplacePageSwitcher: function()
	{
		this.page_switcher.Replace(this.contact_list_headers);
	},
	
	GetExternalHeight: function()
	{
		var res = 0;
		var offsetHeight = this.logo.offsetHeight;      if (offsetHeight) { res += offsetHeight; } else { return this.externalHeight; }
		offsetHeight = this.accountsBar.offsetHeight;   if (offsetHeight) { res += offsetHeight; } else { return this.externalHeight; }
		offsetHeight = this.toolBar.offsetHeight; if (offsetHeight) { res += offsetHeight; } else { return this.externalHeight; }
		offsetHeight = this.lowToolBar.offsetHeight;    if (offsetHeight) { res += offsetHeight; } else { return this.externalHeight; }
		offsetHeight = this.copyright.offsetHeight;     if (offsetHeight) { res += offsetHeight; } else { return this.externalHeight; }
		this.externalHeight = res + this.copyrightMargin;
		return this.externalHeight;
	}
}
