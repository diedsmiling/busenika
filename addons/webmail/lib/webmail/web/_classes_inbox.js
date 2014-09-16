function CMainContainer()
{
		this.table = document.getElementById("main_container");

		this.info = document.getElementById("info");
		this.hider = document.getElementById("hider");

		this.logo = document.getElementById("logo");
		this.accountslist = document.getElementById("accountslist");
		this.toolbar = document.getElementById("toolbar");
		this.lowtoolbar = document.getElementById("lowtoolbar");
		//logo + accountslist + toolbar + lowtoolbar
		
		this.external_height = 56 + 32 + 26 + 24;
		this.inner_height = 362;
}

CMainContainer.prototype =
{
	getExternalHeight: function()
	{
		var res = 0;
		var offsetHeight = 0;
		offsetHeight = this.logo.offsetHeight;         if (offsetHeight) { res += offsetHeight; }
		offsetHeight = this.accountslist.offsetHeight; if (offsetHeight) { res += offsetHeight; } else { res += 32; }
		offsetHeight = this.toolbar.offsetHeight;      if (offsetHeight) { res += offsetHeight; } else { res += 26; }
		offsetHeight = this.lowtoolbar.offsetHeight;   if (offsetHeight) { res += offsetHeight; } else { res += 24; }
		this.external_height = res;
		return this.external_height;
	},
	
	showContent: function()
	{
		this.info.className = 'wm_hide';
		this.hider.className = '';
	},
	
	hideContent: function()
	{
		this.info.className = 'wm_information';
		this.hider.className = 'wm_hide';
	}
}

function CFoldersPart(mode, skinName)
{
	this.isPreviewPane = mode;
	this.container = document.getElementById('folders_part');
	this.width = 115;
	this.height = 100;
	this.realwidth = this.width;
	this.folders = document.getElementById('folders');
	this.folders_hide = document.getElementById('folders_hide');
	this.folders_hide_img = document.getElementById('folders_hide_img');
	this.manage_folders = document.getElementById('manage_folders');
	
	this.skinName = skinName;

	//border + manage folders + hide folders
	this.external_height = 1 + 22 + 20;
	
}
CFoldersPart.prototype =
{
	resizeElementsWidth: function(width)
	{
		this.width = width;
		//MovableVerticalDiv._leftPosition = width;
		this.container.style.width = this.width + 'px';
		this.folders.style.width = this.width + 'px';
		this.folders_hide.style.width = this.width + 'px';
		this.manage_folders.style.width = this.width + 'px';
	},
	
	resizeElementsHeight: function(height)
	{
		this.height = height;
		var bordersHeight = 1;
		this.container.style.height = (this.height - bordersHeight) + 'px';

		var hOffsetHeight = this.folders_hide.offsetHeight;   if (hOffsetHeight == 0) hOffsetHeight = 22;
		var mOffsetHeight = this.manage_folders.offsetHeight; if (mOffsetHeight == 0) mOffsetHeight = 21;
		this.external_height = hOffsetHeight + mOffsetHeight;
		this.folders.style.height = (this.height - this.external_height - bordersHeight) + 'px';
	},
	
	show: function()
	{
		this.width = this.realwidth;
		this.folders_hide_img.src = './skins/' + this.skinName + '/folders/hide_folders.gif';
		this.folders_hide_img.title = Lang.HideFolders;
		this.folders.className = 'wm_folders';
		this.manage_folders.className = 'wm_manage_folders';
		CreateCookie('wm_hide_folders', 0, 20);
	},
	
	hide: function()
	{
		this.realwidth = this.width;
		this.width = 18;
		this.folders_hide_img.src = './skins/' + this.skinName + '/folders/show_folders.gif';
		this.folders_hide_img.title = Lang.ShowFolders;
		if (this.isPreviewPane) {
			this.folders.className = 'wm_hide';
			this.manage_folders.className = 'wm_hide';
		} else {
			this.folders.className = 'wm_unvisible';
			this.manage_folders.className = 'wm_unvisible';
		}
		CreateCookie('wm_hide_folders', 1, 20);
	}
}

function CMessageList(mode)
{
	this.isPreviewPane = mode;
	this.page_switcher = PageSwitcher._mainCont;
	this.parent_table = document.getElementById('inbox_part');
	this.parent_div = document.getElementById('inbox_div');

	this.mess_container = document.getElementById('list_container');
	this.mess_table = document.getElementById('list');
	this.subject = document.getElementById('subject');
	
	this.inbox_headers = document.getElementById('inbox_headers');
	
	this.width = 350;
	this.height = 300;
}

CMessageList.prototype =
{
	resizeElementsHeight: function(height)
	{
		this.height = height;
		this.parent_table.style.height = this.height + 'px';
		this.parent_div.style.height = this.height + 'px';
		var ihHeight = this.inbox_headers.offsetHeight; if (ihHeight == 0) ihHeight = 22;
		var bordersHeight = 1;
		this.mess_container.style.height = (this.height - ihHeight - bordersHeight) + 'px';
	},
	
	resizeElementsWidth: function(width)
	{
		this.width = width;
		this.parent_table.style.width = this.width + 'px';
		this.parent_div.style.width = this.width + 'px';
		this.mess_container.style.width = this.width + 'px';
		this.mess_table.style.width = this.width + 'px';
		this.subject.style.width = (this.width - 404) + 'px';
	}
}

function CMessageIframe(mode)
{
	this.isPreviewPane = mode;
	this.container = document.getElementById('message_container_iframe');
	this.table = document.getElementById('iframe_td');
	this.iframe = document.getElementById('iframe_container');
	//this.iframe.style.scrolling = 'yes';
	this.iframe.style.scrolling = 'no';

	this.document = (Browser.IE) ? this.iframe.document : this.iframe.contentDocument;
	
	
	this.width = 361;
	this.height = 361;
		
	//31 + infobar + border + inbox headers
	this.min_upper = 21 + 2;
	//2 borders + resizer + message headers + message
	this.min_lower = 2 + 4 + 45 + 100;
	
	if (Browser.Mozilla || Browser.Opera) 
	{
		this.document = this.iframe.contentDocument;
	}
	if (Browser.IE) {
		this.document = document.frames('iframe_container').document;
	}
	
	if (!mode)
	{
		this.table.className = 'wm_hide';
		this.iframe.src = 'empty.html';
	}
}

CMessageIframe.prototype =
{
	resizeElementsHeight: function(height)
	{
		this.height = height;
		var bordersHeight = 2;
		this.table.style.height = (this.height - bordersHeight) + 'px';
		this.container.style.height = (this.height - 5 - bordersHeight) + 'px';
		this.iframe.style.height = (this.height - 5 - bordersHeight) + 'px';
	},
	
	resizeElementsWidth: function(width)
	{
		this.width = width;
		this.table.style.width = this.width + 'px';
		this.container.style.width = this.width + 'px';
		this.iframe.style.width = this.width + 'px';
	},
	
	hide: function()
	{
		if(Browser.IE) {this.iframe.className = 'wm_hide';}
	},
	
	show: function()
	{
		if(Browser.IE) {this.iframe.className = '';}
	}
}