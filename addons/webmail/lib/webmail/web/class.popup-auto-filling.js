/*
Classes:
	CPopupAutoFilling
*/

function CPopupAutoFilling(requestHandler, selectHandler)
{
	this._suggestInput = null;

	this._requestHandler = requestHandler;
	this._selectHandler = selectHandler;

	this._popup = null;
	this._shown = false;

	this._keyword = '';
	this._requestKeyword = '';
	this._pickPos = -1;
	this._lines = Array();

	this._timeOut = null;

	this.Build();
}

CPopupAutoFilling.prototype =
{
	Show: function ()
	{
		this._popup.className = 'wm_auto_filling_cont';
		this._shown = true;
		this.Replace();
	},
	
	Hide: function ()
	{
		this._keyword = '';
		this._popup.className = 'wm_hide';
		this._shown = false;
	},
	
	SetSuggestInput: function (suggestInput)
	{
		this.Hide();
		if (this._suggestInput != null)
		{
			this._suggestInput.onkeyup = function () {}
		}
		this._suggestInput = suggestInput;
		suggestInput.setAttribute("autocomplete", "off");  
		var obj = this;
		this._suggestInput.onkeyup = function (ev)
		{
			obj.KeyUpHandler(ev);
		}
	},
	
	Replace: function ()
	{
		if (this._shown)
		{
			var siBounds = GetBounds(this._suggestInput);
			this._popup.style.top = siBounds.Top + siBounds.Height + 'px';
			this._popup.style.left = siBounds.Left + 'px';
			this._popup.style.width = 'auto';
			/*get borders' width to set correct popup width and height*/
			var popupBorders = GetBorders(this._popup);
			var vertBordersWidth = popupBorders.Top + popupBorders.Bottom;
			var horizBordersWidth = popupBorders.Left + popupBorders.Right;
			var pWidth = this._popup.offsetWidth;
			/*set popup width in absolute value for hiding select under popup in ie6*/
			if (siBounds.Width > pWidth)
			{
				this._popup.style.width = (siBounds.Width - horizBordersWidth) + 'px';
			}
			else
			{
				this._popup.style.width = (pWidth - horizBordersWidth) + 'px';
			}
			this._popup.style.height = 'auto';
			var pHeight = this._popup.offsetHeight;
			/*set popup height in absolute value for hiding select under popup in ie6*/
			this._popup.style.height = (pHeight - vertBordersWidth) + 'px';
		}
	},
	
	ClickBody: function (ev)
	{
		var ev = ev ? ev : window.event;
		if (Browser.Mozilla)
		{
			elem = ev.target;
		}
		else
		{
			elem = ev.srcElement;
		}
		if (elem && elem.tagName == 'IMG' && elem.parentNode)
		{
			elem = elem.parentNode;
		}
		if (elem && isNaN(elem.Number) && elem.tagName != 'INPUT')
		{
			this.Hide();
		}
		else if (elem && elem.tagName == 'DIV')
		{
			this.SelectLine(elem);
		}
	},

	Fill: function (itemsArr, keywordStr, lastPhrase)
	{
		var obj = this;
		this._keyword = keywordStr;
		this._requestKeyword = '';
		CleanNode(this._popup);
		MakeOpaqueOnSelect(this._popup);
		this._pickPos = -1;
		this._lines = Array();
		var iCount = itemsArr.length;
		for (var i=0; i<iCount; i++)
		{
			var div = CreateChild(this._popup, 'div');
			var innerHtml = '';
			if (itemsArr[i].ImgSrc.length > 0)
			{
				innerHtml = '<img src="' + itemsArr[i].ImgSrc + '" class="wm_auto_filling_img_group"/>';
			}
			div.innerHTML = innerHtml + itemsArr[i].DisplayText;
			div.ContactGroup = itemsArr[i];
			div.Number = i;
			div.onmouseover = function ()
			{
				obj.PickLine(this.Number);
			}
			div.onmouseout = function ()
			{
				this.className = '';
				if (obj._pickPos == this.Number)
				{
					obj._pickPos = -1;
				}
			}
			this._lines[i] = div;
		}
		if (lastPhrase && lastPhrase.length > 0)
		{
			var div = CreateChild(this._popup, 'div');
			div.className = 'wm_settings_info_cell';
			div.innerHTML = lastPhrase;
		}
		this.Show();
	},
	
	GetKeyword: function ()
	{
		var arr = this._suggestInput.value.split(',');
		return Trim(arr[arr.length - 1]);
	},
	
	SetSuggestions: function (suggestionStr)
	{
		var arr = this._suggestInput.value.split(',');
		var iCount = arr.length;
		for (var i=0; i<iCount-1; i++)
		{
			arr[i] = Trim(arr[i]);
		}
		arr[iCount - 1] = Trim(suggestionStr);
		this._suggestInput.value = arr.join(', ');
	},
	
	SelectLine: function (obj)
	{
		this.Hide();
		this.SetSuggestions(obj.ContactGroup.ReplaceText) ;
		this._pickPos = -1;
		this._suggestInput.focus();
		if (Browser.IE)
		{
			var textRange = this._suggestInput.createTextRange();
			textRange.collapse(false);
			textRange.select();
		}
		this._selectHandler.call(obj);
	},
	
	PickLine: function (posInt)
	{
		if (this._pickPos != -1)
		{
			this._lines[this._pickPos].className = '';
		}
		this._pickPos = posInt;
		if (this._pickPos != -1)
		{
			this._lines[this._pickPos].className = 'wm_auto_filling_chosen';
		}
	},
	
	KeyUpHandler: function (ev)
	{
		ev = ev ? ev : window.event;
		var key = -1;
		if (window.event)
		{
			key = window.event.keyCode;
		}
		else if (ev)
		{
			key = ev.which;
		}
		if (key == 13) //enter
		{
			if (this._pickPos != -1)
			{
				var td = this._lines[this._pickPos];
				this.SelectLine(td);
			}
		}
		else if (key == 38) //up
		{
			if (this._pickPos > -1)
			{
				this.PickLine(this._pickPos - 1);
			}
		}
		else if (key == 40) //down
		{
			if (this._pickPos < (this._lines.length - 1))
			{
				this.PickLine(this._pickPos + 1);
			}
		}
		else
		{
			var keyword = this.GetKeyword();
			if (this.CheckRequestKeyword(keyword))
			{
				if (this._timeOut != null) clearTimeout(this._timeOut);
				var obj = this;
				this._timeOut = setTimeout ( function () { obj.RequestKeyword(); }, 500 );
			} else if (keyword.length == 0) {
				this.Hide();
			}
		}
	},
	
	CheckRequestKeyword: function (keyword)
	{
		if (keyword.length > 0 && this._keyword != keyword)
		{
			if (this._requestKeyword.length > 0)
			{
				var search = PrepareForSearch(this._requestKeyword);
				var reg = new RegExp(search, "gi");
				var res = reg.exec(keyword);
				if (res != null && res.index == 0) {
					return false;
				}
				else
				{
					return true;
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	},
	
	RequestKeyword: function ()
	{
		var keyword = this.GetKeyword();
		if (this.CheckRequestKeyword(keyword))
		{
			this._requestKeyword = keyword;
			this._requestHandler.call({ Keyword: keyword });
		}
	},

	Build: function ()
	{
		this._popup = CreateChild(document.body, 'div');
		this._popup.style.position = 'absolute';
		this.Hide();
	}
}