/*
Classes:
	CPopupMenu
	CPopupMenus
	CVerticalResizer
	CHorizontalResizer
	CSearchForm
*/

function CPopupMenu(popup_menu, popup_control, menu_class, popup_move, popup_title, move_class, move_press_class, title_class, title_over_class)
{
	this.popup = popup_menu;
	this.control = popup_control;
	this.move = popup_move;
	this.title = popup_title;
	this.menu_class = menu_class;
	this.move_class = move_class;
	this.move_press_class = move_press_class;
	this.title_class = title_class;
	this.title_over_class = title_over_class;
}

function CPopupMenus()
{
	this.items = Array();
	this.isShown = 0;
}

CPopupMenus.prototype = {
	getLength: function()
	{
		return this.items.length;
	},
	
	addItem: function(popup_menu, popup_control, menu_class, popup_move, popup_title, move_class, move_press_class, title_class, title_over_class)
	{
		this.items.push(new CPopupMenu(popup_menu, popup_control, menu_class, popup_move, popup_title, move_class, move_press_class, title_class, title_over_class));
		this.hideItem(this.getLength() - 1);
	},
	
	showItem: function(item_id)
	{
		this.hideAllItems();

		var bounds = GetBounds(this.items[item_id].move);
		this.items[item_id].popup.style.left = bounds.Left + 'px';
		this.items[item_id].popup.style.top = bounds.Top + bounds.Height + 'px';

		this.items[item_id].popup.className = this.items[item_id].menu_class;
		if (this.items[item_id].title_class && this.items[item_id].title_class != ''){
			this.items[item_id].control.className = this.items[item_id].title_class;
			this.items[item_id].title.className = this.items[item_id].title_class;
		}
		if (this.items[item_id].move_press_class && this.items[item_id].move_press_class != '')
			this.items[item_id].move.className = this.items[item_id].move_press_class;
		var obj = this;
		this.items[item_id].control.onclick = function()
		{
			obj.hideItem(item_id);
		}
		if (obj.items[item_id].title_over_class != ''){
			this.items[item_id].control.onmouseover = function(){}
			this.items[item_id].control.onmouseout = function(){}
			this.items[item_id].title.onmouseover = function(){}
			this.items[item_id].title.onmouseout = function(){}
		}
		this.isShown = 2;
		var pOffsetWidth = this.items[item_id].popup.offsetWidth;
		var cOffsetWidth = this.items[item_id].control.offsetWidth;
		if (this.items[item_id].control == this.items[item_id].title) {
			var tOffsetWidth = 0;
		} else {
			var tOffsetWidth = this.items[item_id].title.offsetWidth;
		}
		if (pOffsetWidth < (cOffsetWidth + tOffsetWidth)) {
			this.items[item_id].popup.style.width = (cOffsetWidth + tOffsetWidth) + 'px';
		}
	},
	
	hideItem: function(item_id)
	{
		this.items[item_id].popup.className = 'wm_hide';
		if (this.items[item_id].move_class && this.items[item_id].move_class != '' && this.items[item_id].move.className != 'wm_hide')
			this.items[item_id].move.className = this.items[item_id].move_class;
		var obj = this;
		this.items[item_id].control.onclick = function()
		{
			obj.showItem(item_id);
		}
		if (obj.items[item_id].title_over_class != ''){
			this.items[item_id].control.onmouseover = function()
			{
				obj.items[item_id].title.className = obj.items[item_id].title_over_class; 
				obj.items[item_id].control.className = obj.items[item_id].title_over_class;
			}
			this.items[item_id].control.onmouseout = function()
			{
				obj.items[item_id].title.className = obj.items[item_id].title_class; 
				obj.items[item_id].control.className = obj.items[item_id].title_class; 
			}
			this.items[item_id].title.onmouseover = function()
			{
				obj.items[item_id].title.className = obj.items[item_id].title_over_class; 
			}
			this.items[item_id].title.onmouseout = function()
			{
				obj.items[item_id].title.className = obj.items[item_id].title_class; 
			}
		}
	},
	
	hideAllItems: function()
	{
		for (var i = this.getLength() - 1; i >= 0; i--) {
			this.hideItem(i);
		}
		this.isShown = 0;
	},
	
	checkShownItems: function()
	{
		if (this.isShown == 1){
			this.hideAllItems();
		}
		if (this.isShown == 2){
			this.isShown = 1;
		}
	}
}

function CSearchForm(BigSearchForm, SmallSearchForm, SearchControl, SearchControlImg, bigFormId, bigLookFor, smallLookFor, skinName)
{
	this.form = BigSearchForm;
	this._bigFormId = bigFormId;
	this.small_form = SmallSearchForm;
	this.control = SearchControl;
	this.control_img = SearchControlImg;
	this.isShown = 0;
	this._bigLookFor = bigLookFor;
	this._smallLookFor = smallLookFor;
	this.shown = false;
	this._skinName = skinName;
}


CSearchForm.prototype = 
{
	Show: function ()
	{
		if (!this.shown) {
			this.shown = true;
			this.small_form.className = 'wm_toolbar_search_item';
			this.control.className = 'wm_toolbar_search_item';
			var obj = this;
			this.control.onclick = function() {
				obj.ShowBigForm();
			}
			this.control.onmouseover = function() {
				obj.control.className = 'wm_toolbar_search_item_over';
				obj.small_form.className = 'wm_toolbar_search_item_over';
			}
			this.control.onmouseout = function() {
				obj.control.className = 'wm_toolbar_search_item';
				obj.small_form.className = 'wm_toolbar_search_item';
			}
		}
	},
	
	Hide: function ()
	{
		this.shown = false;
		this.small_form.className = 'wm_hide';
		this.control.className = 'wm_hide';
		this.form.className = 'wm_hide';
	},
	
	ShowBigForm: function()
	{
		var bounds = GetBounds(this.small_form);
		this.form.style.top = bounds.Top + 'px';
		this.form.style.right = (GetWidth() - bounds.Left - bounds.Width) + 'px';
		this.form.className = 'wm_search_form';
		this.control.onclick = function() {}
		this.control_img.src = 'skins/' + this._skinName + '/menu/arrow_up.gif';
		this.isShown = 2;
		this._bigLookFor.value = this._smallLookFor.value;
	},
	
	HideBigForm: function()
	{
		this.form.className = 'wm_hide';
		var obj = this;
		this.control.onclick = function() {
			obj.ShowBigForm();
		}
		this.control_img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';
		this.isShown = 0;
		this._smallLookFor.value = this._bigLookFor.value;
	},
	
	checkVisibility: function(ev, isM)
	{
		if (this.isShown == 1) {
			var ev = ev ? ev : window.event;
			if (isM) {elem = ev.target;}
			else {elem = ev.srcElement;}
			while(elem && elem.tagName != 'DIV')
			{
				if(elem.parentNode) {elem = elem.parentNode;}
				else {break;}
			}
			if (elem.id != this._bigFormId) {this.HideBigForm();}
		}
		if (this.isShown == 2)
			this.isShown = 1;
	}
}