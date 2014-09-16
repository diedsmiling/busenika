/*
 * Classes:
 *  CHtmlEditorField
 *  CSpellchecker
 */

var Fonts = ['Arial', 'Arial Black', 'Courier New', 'Tahoma', 'Times New Roman', 'Verdana'];

function CHtmlEditorField(isClassic)
{
	this._mainTbl = null;
	this._header = null;
	this._iframesContainer = null;
	this._colorPalette = null;
	this._colorTable = null;

	this._btnFontColor = null;
	this._btnBgColor = null;
	this._btnInsertLink = null;
	this._fontFaceSel = null;
	this._fontSizeSel = null;

	this._editor = null;
	this._area = null;
	
	this._loaded = false;
	this._designMode = false;

	this._colorMode = -1;
	this._colorChoosing = 0;
	this._currentColor = null;

	this._tr = null;
	this.shown = false;
	this._buttons = Array();
	
	this._plainEditor = null;
	this._htmlSwitcher = null;
	this._htmlMode = true;
	this._waitHtml = null;
	
	this._width = 0;
	this._height = 0;

	this.Build(isClassic);
}

CHtmlEditorField.prototype = {
	SetPlainEditor: function (plainEditor, htmlSwitcher)
	{
		this._plainEditor = plainEditor;
		this._htmlSwitcher = htmlSwitcher;
		this._htmlSwitcher.innerHTML = Lang.SwitchToPlainMode;
		this._htmlMode = true;
		this.Replace();
		var obj = this;
		this._htmlSwitcher.onclick = function () {
			obj.SwitchHtmlMode(true);
			return false;
		}
	},
	
	SwitchHtmlMode: function (needConfirm)
	{
		if (this._htmlMode) {
			if (this._designMode) {
				var value = this.GetText();
				if (Browser.IE && value.length > 0) {
					value = value.ReplaceStr('<style> p { margin-top: 0px; margin-bottom: 0px; } </style>\n', '');
					value = value.ReplaceStr('<style> .misspel { background: url(images/redline.gif) repeat-x bottom;	display: inline; } </style>\n', '');
				}
			}
			else {
				var value = this._waitHtml;
			};
			if( !needConfirm || confirm(Lang.ConfirmHtmlToPlain) ) {
				this._plainEditor.value = HtmlDecode(value.replace(/<br *\/{0,1}>/gi, '\n').replace(/<[^>]*>/g, ''));
				this._htmlSwitcher.innerHTML = Lang.SwitchToHTMLMode;
				this._htmlMode = false;
				this.Hide();
			}
		}
		else {
			var value = HtmlEncode(this._plainEditor.value).replace(/\n/g, '<br/>');
			this.Show();
			this.SetHtml(value);
			this.Resize(this._width, this._height);
			this._htmlSwitcher.innerHTML = Lang.SwitchToPlainMode;
			this._htmlMode = true;
		}
	},
	
	LoadEditArea: function ()
	{
		this._loaded = true;
		this.DesignModeOn();
	},
	
	DesignModeOn: function ()
	{
		if (this._loaded && this.shown) {
			if (Browser.IE) {
				this._area.document.designMode = 'on';
			}
			else {
				this._area.contentDocument.designMode = 'on';
			};
			if (!this._designMode) {
				this._designMode = true;
				setTimeout('DesignModeOnHandler(1);',99);
			};
			if (Browser.IE) {
				var obj = this;
				var SetFontFunction = function () {
					var fontName = obj.ComValue('FontName');
					var fontSize = obj.ComValue('FontSize');
					if (fontName) obj._fontFaceSel.value = fontName;
					if (fontSize) obj._fontSizeSel.value = fontSize;
				};
				this._area.document.onclick = SetFontFunction;
				this._area.document.onkeyup = SetFontFunction;
			}
		}
	},

	Show: function (tabindex)
	{
		this._colorMode = -1;
		this._mainTbl.className = 'wm_html_editor';
		if (this._editor == null) {
			var editor = CreateChildWithAttrs(this._iframesContainer, 'iframe', [['src', EditAreaUrl], ['frameborder', '0px'], ['id', 'EditorFrame']]);
			editor.className = 'wm_editor';
			this._editor = editor;
			this._editor.style.width = '100px';
			if (Browser.IE)
				this._area = frames('EditorFrame');
			else
				this._area = editor;
		}
		if (tabindex) this._editor.tabIndex = tabindex;
		this.shown = true;
		this.DesignModeOn();
	},
	
	Hide: function ()
	{
		if (this.shown) {
			this._mainTbl.focus();
			this._editor.tabIndex = -1;
		}
		this.shown = false;
		this._mainTbl.className = 'wm_hide';
		this._colorPalette.className = 'wm_hide';
	},
	
	Replace: function ()
	{
		if (this._plainEditor != null) {
			var bounds = GetBounds(this._plainEditor);
			var left = bounds.Left;
			var top = bounds.Top;
			this._mainTbl.style.position = 'absolute';
			this._mainTbl.style.left = (left - 1) + 'px';
			this._mainTbl.style.top = (top - 1) + 'px';
		}
	},
	
	Resize: function (width, height)
	{
		this._width = width;
		this._height = height;
		if (null != this._plainEditor) {
			this._plainEditor.style.width = (width - 2) + 'px';
			this._plainEditor.style.height = (height - 1) + 'px';
		}
		this._mainTbl.style.width = (width + 4) + 'px';
		this._mainTbl.style.height = (height + 5) + 'px';
		if (null != this._editor) {
			this._editor.style.width = width + 'px';
			var offsetHeight = this._header.offsetHeight;
			if (offsetHeight) {
				this._editor.style.height = (height - offsetHeight) + 'px';
			}
		}
		this.Replace();
	},

	SetText: function (txt) {
		this._plainEditor.value = txt;
		this._htmlMode = false;
		this._htmlSwitcher.innerHTML = Lang.SwitchToHTMLMode;
		this.Hide();
	},
	
	SetWaitHtml: function () {
		this.DesignModeOn();
		if (this._waitHtml != null) {
			this.SetHtml(this._waitHtml);
		}
	},
	
	SetHtml: function (txt) {
		if (this._designMode) {
			if (Browser.IE) {
				var styles = '<style> p { margin-top: 0px; margin-bottom: 0px; } </style>\n'+
					'<style> .misspel { background: url(images/redline.gif) repeat-x bottom; } </style>\n';
				this._area.document.open();
				this._area.document.writeln(styles + txt);
				this._area.document.close();
			}
			else {
				this._area.contentDocument.body.innerHTML = txt;
			}
			this._waitHtml = null;
		}
		else {
			this._waitHtml = txt;
		}
	},
	
	GetText: function () {
		if (this._designMode) {
			if (Browser.IE) {
				var value = this._area.document.body.innerHTML;
				return value.replace(/<\/p>/gi, '<br />').replace(/<p>/gi, '');
			}
			else {
				return this._area.contentDocument.body.innerHTML;
			}
		};
		return false;
	},
	
	ComValue: function (cmd) {
		if (this._designMode) {
			if (Browser.IE) {
				return this._area.document.queryCommandValue(cmd);
			}
			else {
				return this._area.contentDocument.queryCommandValue(cmd, false, null);
			}
		}
	},

	ExecCom: function (cmd, param) {
		if (this._designMode) {
			this._area.focus();
			if (Browser.IE) {
				if (param) {
					this._area.document.execCommand(cmd, false, param);
				}
				else {
					this._area.document.execCommand(cmd);
				}
			}
			else {
				if (param) {
					var res = this._area.contentDocument.execCommand(cmd, false, param);
				}
				else {
					this._area.contentDocument.execCommand(cmd, false, null);
				}
			}
			this._area.focus();
		}
	},

	CreateLink: function () {
		if (Browser.IE) {
			this.ExecCom('CreateLink');
		}
		else if (this._designMode) {
			var bounds = GetBounds(this._btnInsertLink);
			var top = bounds.Top + bounds.Height;
			window.open('linkcreator.html', 'ha_fullscreen', 
				'toolbar=no,menubar=no,personalbar=no,width=380,height=100,left=' + bounds.Left + ',top=' + top + 
				'scrollbars=no,resizable=no,modal=yes,status=no');
		}
	},

	CreateLinkFromWindow: function (url) {
		this.ExecCom('createlink', url);
	},
	
	Unlink: function () {
		if (Browser.IE) {
			this.ExecCom('Unlink');
		}
		else if (this._designMode) {
			this.ExecCom('unlink');
		}
	},

	InsertOrderedList: function () {
		this.ExecCom('InsertOrderedList');
	},

	InsertUnorderedList: function () {
		this.ExecCom('InsertUnorderedList');
	},

	InsertHorizontalRule: function () {
		this.ExecCom('InsertHorizontalRule');
	},

	FontName: function (name) {
		this.ExecCom('FontName', name);
		this._fontFaceSel.value = name;
	},

	FontSize: function (size) {
		this.ExecCom('FontSize', size);
		this._fontSizeSel.value = size;
	},

	Bold: function () {
		this.ExecCom('Bold');
	},

	Italic: function () {
		this.ExecCom('Italic');
	},

	Underline: function () {
		this.ExecCom('Underline');
	},

	JustifyLeft: function () {
		this.ExecCom('JustifyLeft');
	},

	JustifyCenter: function () {
		this.ExecCom('JustifyCenter');
	},

	JustifyRight: function () {
		this.ExecCom('JustifyRight');
	},

	JustifyFull: function () {
		this.ExecCom('JustifyFull');
	},

	ChooseColor: function (mode)
	{
		if (this._designMode) {
			if (this._colorMode == mode) {
				this._colorPalette.className = 'wm_hide';
				this._colorChoosing = 0;
				this._colorMode = -1;
			}
			else {
				this._colorMode = mode;

				if (mode == 0) {
					var bounds = GetBounds(this._btnFontColor);
				}
				else {
					var bounds = GetBounds(this._btnBgColor);
				};
				this._colorPalette.style.left = bounds.Left + 'px';
				this._colorPalette.style.top = bounds.Top + bounds.Height + 'px';
				this._colorPalette.className = 'wm_color_palette';

				if (Browser.IE) {
					this._tr = this._area.document.selection.createRange();
					this._colorPalette.style.height = this._colorTable.offsetHeight + 8 + 'px';
					this._colorPalette.style.width = this._colorTable.offsetWidth + 8 + 'px';
				}
				else {
					this._colorPalette.style.height = this._colorTable.offsetHeight + 'px';
					this._colorPalette.style.width = this._colorTable.offsetWidth + 'px';
				};
				this._colorChoosing = 2;
			}
		}
	},

	SelectFontColor: function (color)
	{
		if (this._designMode) {
			if (Browser.IE) {
				this._tr.select();
				if (this._colorMode == 0) {
					this._tr.execCommand('ForeColor', false, color);
				}
				else {
					this._tr.execCommand('BackColor', false, color);
				}
			}
			else {
				if (this._colorMode == 0) {
					this._area.contentDocument.execCommand('ForeColor', false, color);
				}
				else {
					this._area.contentDocument.execCommand('hilitecolor', false, color);
				}
			};
			this._area.focus();
			this._colorPalette.className = 'wm_hide';
			this._colorMode = -1;
		}
	},
	
	ChangeLang: function ()
	{
		var iCount = this._buttons.length;
		for (var i=0; i<iCount; i++) {
			var but = this._buttons[i];
			but.Img.title = Lang[but.LangField];
		}
	},
	
	AddToolBarItem: function (parent, image, title, langField)
	{
		var child = CreateChild(parent, 'div');
		child.className = 'wm_toolbar_item';
		child.onmouseover = function () { this.className='wm_toolbar_item_over'; };
		child.onmouseout = function () { this.className='wm_toolbar_item'; };
		var img = CreateChildWithAttrs(child, 'img', [['src', 'images/html_editor/' + image], ['title', title]]);
		this._buttons.push({ Img: img, LangField: langField });
		return child;
	},
	
	AddToolBarSeparate: function (parent)
	{
		var child = CreateChild(parent, 'div');
		child.className = 'wm_toolbar_separate';
		return child;
	},
	
	ClickBody: function ()
	{
		switch (this._colorChoosing) {
			case (2):
				this._colorChoosing = 1;
			break;
			case (1):
				this._colorChoosing = 0;
				this._colorPalette.className = 'wm_hide';
				this._colorMode = -1;
			break;
		}
	},
	
	SetCurrentColor: function (color) {
		this._currentColor.style.backgroundColor = color;
	},

	BuildColorPalette: function ()
	{
		var div = CreateChild(document.body, 'div');
		div.className = 'wm_hide';
		this._colorPalette = div;
		var tbl = CreateChild(div, 'table');
		this._colorTable = tbl;
		var rowIndex = 0;
		var colors = ['#000000', '#333333', '#666666', '#999999', '#CCCCCC', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#00FFFF', '#FF00FF'];
		var colorIndex = 0;
		var symbols = ['00', '33', '66', '99', 'CC', 'FF'];
		var obj = this;
		for (var jStart=0; jStart<6; jStart+=3) {
			for (var i=0; i<6; i++) {
				var tr = tbl.insertRow(rowIndex++);
				var cellIndex = 0;
				if (rowIndex == 1) {
					var td = tr.insertCell(cellIndex++);
					td.rowSpan = 12;
					td.className = 'wm_current_color_td';
					this._currentColor = CreateChild(td, 'div');
					this._currentColor.className = 'wm_current_color';
				};
				var td = tr.insertCell(cellIndex++);
				td.className = 'wm_palette_color';
				td = tr.insertCell(cellIndex++);
				td.bgColor = colors[colorIndex++];
				td.className = 'wm_palette_color';
				td.onmouseover = function () { obj.SetCurrentColor(this.bgColor); };
				td.onclick = function () { obj.SelectFontColor(this.bgColor); };
				td = tr.insertCell(cellIndex++);
				td.className = 'wm_palette_color';
				for (var j=jStart; j<jStart+3; j++) {
					for (var k=0; k<6; k++) {
						td = tr.insertCell(cellIndex++);
						td.bgColor = '#' + symbols[j] + symbols[k] + symbols[i];
						td.className = 'wm_palette_color';
						td.onmouseover = function () { obj.SetCurrentColor(this.bgColor); };
						td.onclick = function () { obj.SelectFontColor(this.bgColor); };
					}
				}
			}
		}
	},
	
	Build: function (isClassic)
	{
		var obj = this;
		var tbl = CreateChild(document.body, 'table');
		this._mainTbl = tbl;
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		this._header = tr;
		tr.className = 'wm_html_editor_toolbar';
		var td = tr.insertCell(0);
		this._btnInsertLink = this.AddToolBarItem(td, 'link.gif', Lang.InsertLink, 'InsertLink');
		this._btnInsertLink.onclick = function () { obj.CreateLink(); };
		var div = this.AddToolBarItem(td, 'unlink.gif', Lang.RemoveLink, 'RemoveLink');
		div.onclick = function () { obj.Unlink(); };
		div = this.AddToolBarItem(td, 'number.gif', Lang.Numbering, 'Numbering');
		div.onclick = function () { obj.InsertOrderedList(); };
		div = this.AddToolBarItem(td, 'list.gif', Lang.Bullets, 'Bullets');
		div.onclick = function () { obj.InsertUnorderedList(); };
		div = this.AddToolBarItem(td, 'hrule.gif', Lang.HorizontalLine, 'HorizontalLine');
		div.onclick = function () { obj.InsertHorizontalRule(); };
		div = this.AddToolBarSeparate(td);

		div = CreateChild(td, 'div');
		div.className = 'wm_toolbar_item';
		var fontFaceSel = CreateChild(div, 'select');
		fontFaceSel.className = 'wm_input wm_html_editor_select';
		var i, opt;
		for (i in Fonts) {
			opt = CreateChildWithAttrs(fontFaceSel, 'option', [['value', Fonts[i]]]);
			opt.innerHTML = Fonts[i];
			if ('Times New Roman' == Fonts[i]) opt.selected = true;
		}
		fontFaceSel.onchange = function () { obj.FontName(this.value); };
		this._fontFaceSel = fontFaceSel;
		div.style.margin = '0px';
		
		div = CreateChild(td, 'div');
		div.className = 'wm_toolbar_item';
		var fontSizeSel = CreateChild(div, 'select');
		fontSizeSel.className = 'wm_input wm_html_editor_select';
		for (i=1; i<8; i++) {
			opt = CreateChildWithAttrs(fontSizeSel, 'option', [['value', i]]);
			opt.innerHTML = i;
			if (3 == i) opt.selected = true;
		}
		fontSizeSel.onchange = function () { obj.FontSize(this.value); };
		this._fontSizeSel = fontSizeSel;
		div.style.margin = '0px';
		
		div = this.AddToolBarSeparate(td);
		div = this.AddToolBarItem(td, 'bld.gif', Lang.Bold, 'Bold');
		div.onclick = function () { obj.Bold(); };
		div = this.AddToolBarItem(td, 'itl.gif', Lang.Italic, 'Italic');
		div.onclick = function () { obj.Italic(); };
		div = this.AddToolBarItem(td, 'undrln.gif', Lang.Underline, 'Underline');
		div.onclick = function () { obj.Underline(); };
		div = this.AddToolBarItem(td, 'lft.gif', Lang.AlignLeft, 'AlignLeft');
		div.onclick = function () { obj.JustifyLeft(); };
		div = this.AddToolBarItem(td, 'cnt.gif', Lang.Center, 'Center');
		div.onclick = function () { obj.JustifyCenter(); };
		div = this.AddToolBarItem(td, 'rt.gif', Lang.AlignRight, 'AlignRight');
		div.onclick = function () { obj.JustifyRight(); };
		div = this.AddToolBarItem(td, 'full.gif', Lang.Justify, 'Justify');
		div.onclick = function () { obj.JustifyFull(); };
		this._btnFontColor = this.AddToolBarItem(td, 'font_color.gif', Lang.FontColor, 'FontColor');
		this._btnFontColor.onclick = function () { obj.ChooseColor(0); };
		this._btnBgColor = this.AddToolBarItem(td, 'bg_color.gif', Lang.Background, 'Background');
		this._btnBgColor.onclick = function () { obj.ChooseColor(1); };
		
		if (!isClassic) {
			div = this.AddToolBarSeparate(td);
			div = this.AddToolBarItem(td, 'spell.gif', 'Spellcheck', 'Spellcheck');
			div.onclick = function () { SpellCheck(); };
		}
		
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_html_editor_cell';
		td.colSpan = 1;
		this._iframesContainer = td;
		
		this.BuildColorPalette();
	}, //Build
	
	CleanSpell_IE: function ()
	{
	    if (this._area.document.selection && this._area.document.selection.createRange()) {
			var range = this._area.document.selection.createRange();
			
			// getting a cursor position
			var cursorRange = range.duplicate();
			cursorRange.moveStart('textedit', -1);
			var cursorPos = cursorRange.text.length; 

			range.pasteHTML('<span id="#31337" />');
			var ghostElement = this._area.document.getElementById('#31337');
			var element = ghostElement.parentNode;
			element.removeChild(ghostElement);
			if (element.className == 'misspel') {
				var textNode = this._area.document.createTextNode(element.innerHTML);
				var elParent = element.parentNode;
				if (element.nextSibling != null) elParent.insertBefore(textNode, element.nextSibling);
				elParent.removeChild(element);
				
				// moving cursor to last position
				range.moveStart('textedit', -1);
				var zeta = cursorPos - range.text.length;
				range = this._area.document.selection.createRange();
				range.move('character', zeta);
				range.select();
			}
	    }
	},
	
	CleanSpell_Gecko: function() 
	{
	var sel = this._area.contentWindow.getSelection();
		var range = sel.getRangeAt(0);
		var focusOffset = sel.focusOffset; 
		if (range.collapsed) {
			var element = range.commonAncestorContainer;
			if (element && element.parentNode) {
				var parent = element.parentNode;
				if (parent.className == 'misspel') {
					var newText = this._area.contentDocument.createTextNode(element.nodeValue); 
					var repIn = parent.parentNode;
					repIn.replaceChild(newText, parent);
					sel.collapse(newText, focusOffset);
				}
			}
		}
	}
};

function CSpellchecker() 
{
	this.misspelPos = new Array();
	this.misspelWait = false;
	this.suggestion = new Array();
	this.misElement;
	this.suggestWait = false;
	this.misGetWords = new Array();
	this.currentWord = '';
	this.popupDiv = document.getElementById('spell_popup_menu');
	this.DataSource = new CDataSource( Array(), SpellcheckerUrl, ErrorHandler, InfoHandler, LoadHandler, TakeDataHandler, ShowLoadingHandler );

}

CSpellchecker.prototype = {
	//	return HTML-Stroked word
	GetRawCData: function(source)	
	{
		return '<![CDATA[' + source + ']]>';
	},
	
	StrokeIt: function (word) 
	{ 
		if (word) {	return '<span class="misspel">' + word + '</span>' } 
		else { return ''; }
	},
	
	// misspel is Array
	StrokeText: function (misspel, text) 
	{
		var newText = '';
		var span = ' ';
		var lastPos = 0;
		if (text && misspel) {
			for (i=0; i < misspel.length; i++) {
				var misPos = misspel[i][0];
				var misLen = misspel[i][1];
				var begin = text.substring(lastPos, misPos);
				var misWord = text.substring(misPos, misPos + misLen);
				var newText = newText + begin + this.StrokeIt(misWord);
				lastPos = misPos + misLen;
			};
			newText += text.substring(lastPos, text.length);
		};
		return newText;
	},
	
	StripMissTags: function(text) {
		var resText = text;
		var rep = /<span class="misspel">(.*?)<\/span>/i;
		var repIE = /<span class=misspel>(.*?)<\/span>/i;
		if (Browser.IE) {
			var inText = repIE.exec(resText);
			while (inText != null) {
				resText = resText.replace(repIE, inText[1]);
				inText = repIE.exec(resText);
			}
		}
		else {
			var inText = rep.exec(resText);
			while (inText != null) {
				resText = resText.replace(rep, inText[1]);
				inText = rep.exec(resText);
			}
		};
		return resText;
	},
	
	popupHide: function(caller) {
		if (caller && caller == 'document') {
			if (this.popupVisible()) {
				if (this.suggestWait) {
					this.DataSource.NetLoader.CheckRequest();
					this.suggestWait = false;
					this.currentWord = '';
				};
				this.popupDiv.className = "spell_popup_hide";
			}
		}
		else {
			if (!this.suggestWait && this.popupVisible()) {
				this.popupDiv.className = "spell_popup_hide";
			} 
		}
	},
	
	popupShow: function(text) {
		if (text) {
			CleanNode(this.popupDiv);
			var textNode = document.createElement('div');
			textNode.innerHTML = text;
			textNode.className = 'spell_spanDeactive';
			this.popupDiv.appendChild(textNode);	
			this.popupDiv.className = "spell_popup_show";
		}
		else {
			if (!this.popupVisible()) 
				this.popupDiv.className = "spell_popup_show";
		}
	},
	
	popupVisible: function() {
		return (this.popupDiv.className == 'spell_popup_show' ? true : false)
	},
	
	popupRecalcCoords: function () {

		if (this.misElement) {
			var browserDoc = Browser.IE ? WebMail._htmlEditorField._area.document : WebMail._htmlEditorField._area.contentDocument;
			var scrollY = this.getScrollY(browserDoc);
			var bounds = GetBounds(this);
			var ifr_bounds = GetBounds(WebMail._htmlEditorField._area);
			var popX = bounds.Left + ifr_bounds.Left;
			var popY = bounds.Top + ifr_bounds.Top - scrollY + 20;
			if (Browser.IE) {
			this.popupDiv.style.Top = popY;
			this.popupDiv.style.Left = popX;
			}
			else {
				this.popupDiv.style.top = popY + "px";	
				this.popupDiv.style.left = popX + "px";
			}
		}
	},
	
	suggestionTable: function (suggestions) {
		var sugTable = document.createElement('TABLE');
		sugTable.style.width = '180px';
		var sugTBody = document.createElement('TBODY');
		sugTable.appendChild(sugTBody);
		for (var i = 0; i < suggestions.length; i++) {
			var sugTRow =  document.createElement('TR');
			var sugNode = document.createElement('TD');
			if(Browser.IE) {
				sugNode.innerText = suggestions[i];
				sugNode.onclick = ReplaceWord;
				sugNode.onmouseover = this.Menu_hightlight_on;
				sugNode.onmouseout = this.Menu_hightlight_off;
			}
			else {
				sugNode.textContent = suggestions[i];
				sugNode.addEventListener("mouseover", this.Menu_hightlight_on, false);
				sugNode.addEventListener("mouseout", this.Menu_hightlight_off, false);
				sugNode.addEventListener("click", ReplaceWord, false);
			};
			sugNode.className = 'spell_spanDeactive';
			sugTBody.appendChild(sugTRow).appendChild(sugNode);
		};
		return sugTable;
	},
	
	getScrollY: function (doc) {
		var scrollY = 0;
		if (doc.body && typeof doc.body.scrollTop != "undefined") {
			 scrollY += doc.body.scrollTop;
			 if (scrollY == 0 && doc.body.parentNode && typeof doc.body.parentNode != "undefined") {
                    scrollY += doc.body.parentNode.scrollTop;
             }
        }
        else if (typeof window.pageXOffset != "undefined") {
             scrollY += window.pageYOffset;
        };
		return scrollY;
	},
	
	GetFromXML: function(RootElement) {
		var HtmlEditor = WebMail._htmlEditorField;
		var action = RootElement.getAttribute('action');
		var SpellParts = RootElement.childNodes;
		if (action == "spellcheck") {
			var text = HtmlEditor.GetText();
			text = this.StripMissTags(text);
			
			this.misspelPos = new Array();
			for (i=0; i < SpellParts.length; i++) {
				mispNode = SpellParts.item(i);
				if (mispNode.nodeName == 'misp') {
					misPos = mispNode.getAttribute('pos');
					misLen = mispNode.getAttribute('len');
					this.misspelPos[this.misspelPos.length] = new Array((misPos-0), (misLen-0));
				}
			};
			var newText = this.StrokeText(this.misspelPos, text);
			HtmlEditor.SetHtml(newText);
			WebMail._spellchecker.misspelWait = false;
			AddMisspelEvents();
		}
		else if (action == "suggest") {
			this.suggestion = new Array();
			var suggestNode = new Array();
			for (i=0; i < SpellParts.length; i++) {
				suggestNode = SpellParts.item(i);
				if (suggestNode.nodeName == 'param') {
					this.suggestion[this.suggestion.length] =  suggestNode.getAttribute('value');
				}
			};
			var s = '';
			var SuggestWords = new Array();
			for (i=0; i < this.suggestion.length; i++) {
				s = s +  this.suggestion[i] + ' ';
				SuggestWords[i] = this.suggestion[i];
			};
			WebMail._spellchecker.misGetWords[WebMail._spellchecker.currentWord] = SuggestWords;
			
			CleanNode(this.popupDiv);
			if (this.suggestion.length > 0) {
				this.popupDiv.appendChild(this.suggestionTable(this.suggestion));
			}
			else {
				this.popupShow(Lang.SpellNoSuggestions);
			};
			this.popupShow();
			WebMail._spellchecker.suggestWait = false;  
		} else if (action == "error") {
			var errorStr = RootElement.getAttribute('errorstr');
			WebMail._errorObj.Show(errorStr);
			WebMail._spellchecker.suggestWait = false;
			WebMail._spellchecker.misspelWait = false;			
		}
	}, 
	
	Menu_hightlight_on: function () {
		this.className = 'spell_spanActive';
	},
	
	Menu_hightlight_off: function () {
		this.className = 'spell_spanDeactive';
	}
};

function AddMisspelEvents() 
{
	var HtmlEditor = WebMail._htmlEditorField;
	if (Browser.IE) {
		var childs = HtmlEditor._area.document.getElementsByTagName('span');
		HtmlEditor._area.document.onmousedown = function () { WebMail._spellchecker.popupHide() };
		HtmlEditor._area.document.body.onscroll = function() {};
		HtmlEditor._area.document.body.onscroll = function () { WebMail._spellchecker.popupRecalcCoords() };
	}
	else {
		var childs = HtmlEditor._area.contentDocument.getElementsByTagName('span');
		HtmlEditor._area.contentDocument.addEventListener("mousedown", function() { WebMail._spellchecker.popupHide() }, false);
		HtmlEditor._area.contentDocument.addEventListener('scroll', function() { WebMail._spellchecker.popupRecalcCoords() }, false);
	};	
	var node;
	for (i=0; i < childs.length; i++) {
		node = childs.item(i);
		if (node.className && node.className == "misspel") {
			if (Browser.IE) {
				node.onclick = MisspelCliq;
			}
			else {
				node.addEventListener("click", MisspelCliq, false);
			}
		}
	};
	
	var doc = Browser.IE ? HtmlEditor._area.document : HtmlEditor._area.contentDocument;
	if (doc.addEventListener) {
		doc.addEventListener('keypress', EditKeyHandle, true); 
	}
	else if (doc.attachEvent) {
		doc.attachEvent('onkeydown', EditKeyHandle);
	}
}

function MisspelCliq() 
{
	var spell = WebMail._spellchecker;
	var lastWord = spell.currentWord;
	spell.currentWord = this.innerHTML;
	spell.misElement = this;
	var popupDiv = WebMail._spellchecker.popupDiv;
	if (spell.suggestWait) { 
		WebMail._spellchecker.DataSource.NetLoader.CheckRequest(); 
	};
	if (spell.misGetWords[spell.currentWord]) {
		CleanNode(popupDiv);
		if (spell.misGetWords[spell.currentWord].length == 0) {
			spell.popupShow(Lang.SpellNoSuggestions);
		}
		else {
			popupDiv.appendChild(spell.suggestionTable(spell.misGetWords[spell.currentWord]));
		};
		spell.suggestWait = false;
	}
	else {
		if (spell.currentWord != lastWord) {
			spell.suggestWait = true;
			var xml='<param name="action" value="spellcheck"/><param name="request" value="suggest"/><param name="word" value="' + spell.currentWord + '"/>';
			WebMail._spellchecker.DataSource.Request( Array(), xml );
			spell.popupShow(Lang.SpellWait);
		}
	};
	WebMail._spellchecker.popupDiv.className = "spell_popup_show";
	var bounds = GetBounds(this);
	var ifr_bounds = GetBounds(WebMail._htmlEditorField._editor);
	var browserDoc = Browser.IE ? WebMail._htmlEditorField._area.document : WebMail._htmlEditorField._area.contentDocument;
	var scrollY = WebMail._spellchecker.getScrollY(browserDoc);
	var popX = bounds.Left + ifr_bounds.Left;
	var popY = bounds.Top + ifr_bounds.Top - scrollY + 20;
	if (Browser.IE) {
		popupDiv.style.top = popY;
		popupDiv.style.left = popX;
	}
	else {
		popupDiv.style.top = popY + "px";	
		popupDiv.style.left = popX + "px";
	}
}

function ReplaceWord() {
    var strWord = Browser.IE ? this.innerText : this.textContent;
    var doc = Browser.IE ? WebMail._htmlEditorField._area.document : WebMail._htmlEditorField._area.contentDocument;
    var newTextNode = doc.createTextNode(strWord);
    var misElement = WebMail._spellchecker.misElement;
    var elParent = misElement.parentNode;
   
    elParent.replaceChild(newTextNode, misElement);
   
    var text = WebMail._htmlEditorField.GetText ();
    WebMail._spellchecker.suggestWait = false;
    WebMail._spellchecker.popupHide();
}

function SpellCheck() { 
	var HtmlEditor = WebMail._htmlEditorField;
	if (!WebMail._spellchecker.misspelWait) {
		WebMail._spellchecker.misGetWords = new Array();
		WebMail._spellchecker.misspelWait = true;
		var text = WebMail._htmlEditorField.GetText();
		var stripText = WebMail._spellchecker.StripMissTags(text);
		var textNode = WebMail._spellchecker.GetRawCData(stripText);
		var xml='<param name="action" value="spellcheck"/><param name="request" value="spell"/><text>' + textNode + '</text>';
		WebMail._spellchecker.DataSource.Request( Array(), xml );
	}
	else {
		alert(Lang.SpellWait);
	}
}

function EditKeyHandle(ev) {
	if (isTextChanged(ev)) {
		if (Browser.IE) {
			WebMail._htmlEditorField.CleanSpell_IE();
		}
		else {
			WebMail._htmlEditorField.CleanSpell_Gecko();
		}
	}
}

function isTextChanged(ev) {
	var key = -1;
	if  (Browser.IE) { 
		var inst = WebMail._htmlEditorField._area; 
		if (inst.window.event) {
			key = inst.window.event.keyCode;
			which = key;
		}
	}
	else if (ev) {
		key = ev.keyCode;
		which = ev.which;
	};
	if (key != 16 //shift
	&& key != 17 //ctrl
	&& key != 18 //alt
	&& key != 35 //end
	&& key != 36 //home
	&& key != 37 //to the right
	&& key != 38 //up
	&& key != 39 //to the left
	&& key != 40 //down
	|| (key == 0 && which != 0) // FireFox
	) {
		return true;
	}
	else {
		return false;
	}
}

function ShowLoadingHandler() {
	if (!WebMail._spellchecker.suggestWait)
		WebMail.ShowInfo(Lang.Loading);
}