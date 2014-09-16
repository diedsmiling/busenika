/*
 * Classes:
 *  CVariableColumn
 *  CInboxTableController
 *  CContactsTableController
 *  CVariableTable
 *  CCheckBoxCell
 *  CImageCell
 *  CTextCell
 *  CVariableMsgLine
 *  CVariableContactLine
 * 
 *  CPopupAutoFilling
 *  CPopupContacts
 */

function CVariableColumn(id, params, selection)
{
	this.Id = -1;
	this.Field = '';
	this._langField = '';
	this._imgPath = '';
	this._langNumber = -1;

	this.SortField = SORT_FIELD_NOTHING;
	this.SortOrder = SORT_ORDER_ASC;
	this.Sorted = false;
	this._sortIconPlace = 2;
	this._sortHandler = null;
	this._freeSort = false;

	this.Align = 'center';
	this.Width = 100;
	this.MinWidth = 100;
	this._left = 0;
	this._padding = 2;

	this._htmlElem = null;
	this.LineElem = null;

	this._isResize = false;
	this.Resizer = null;
	this.isLast = false;
	this._resizerWidth = 3;

	this.filled = false;
	this.CheckBox = null;
	if (id == IH_CHECK || id == CH_CHECK) {
		this._isCheckBox = true;
		this._selection = selection;
	}
	else {
		this._isCheckBox = false;
		this._selection = null;
	};
	this.ChangeField(id, params);
}

CVariableColumn.prototype = 
{
	ChangeField: function (id, params, skinName)
	{
		this.Id = id;
		this.Field = 'f' + params.DisplayField;
		this._langField = params.LangField;
		this._imgPath = params.Picture;
		this.SortField = params.SortField;
		this._sortIconPlace = params.SortIconPlace;
		if (params.Align == 'left' || params.Align == 'center' || params.Align == 'right') {
			this.Align = params.Align;
		}
		else {
			this.Align = 'center';
		};
		if (this.filled == false) {
			this.Width = params.Width;
			this.filled = true;
		};
		this.MinWidth = params.MinWidth;
		this._isResize = params.IsResize;
		if (skinName) {
			this.SetContent(skinName);
		}
	},
	
	SetContent: function (skinName)
	{
		var contentNode = null;
		if (this._isCheckBox) {
			contentNode = document.createElement('input');
			contentNode.type = 'checkbox';
			var obj = this;
			contentNode.onclick = function () {
				if (null != obj._selection && obj._selection.Length > 0) {
					if (contentNode.checked) {
						obj._selection.CheckAll();
					}
					else {
						obj._selection.UncheckAll();
					}
				}
			};
			this.CheckBox = contentNode;
		}
		else if (this._langField.length > 0) {
			contentNode = document.createElement('span');
			contentNode.innerHTML = Lang[this._langField];
			if (this._langNumber == -1) {
				this._langNumber = WebMail.LangChanger.Register('innerHTML', contentNode, this._langField, '', '');
			}
			else {
				this._langNumber = WebMail.LangChanger.Register('innerHTML', contentNode, this._langField, '', '', this._langNumber);
			}
		}
		else if (this._imgPath.length > 0) {
			contentNode = document.createElement('img');
			contentNode.src = 'skins/' + skinName + '/' + this._imgPath;
		};
		CleanNode(this._htmlElem);
		var nobr = CreateChild(this._htmlElem, 'nobr');
		if (this.Sorted) {
			var sortNode = document.createElement('img');
			if (SORT_ORDER_ASC == this.SortOrder) {
				sortNode.src = 'skins/' + skinName + '/menu/order_arrow_up.gif';
			}
			else {
				sortNode.src = 'skins/' + skinName + '/menu/order_arrow_down.gif';
			};
			switch (this._sortIconPlace) {
				case 0:
					nobr.appendChild(sortNode);
					if (null != contentNode) {
						nobr.appendChild(contentNode);
					};
				break;
				case 1:
					nobr.appendChild(sortNode);
				break;
				case 2:
					if (null != contentNode) {
						nobr.appendChild(contentNode);
					};
					nobr.appendChild(sortNode);
				break;
			}
		}
		else {
			if (null != contentNode) {
				nobr.appendChild(contentNode);
			}
		}
	},
	
	RemoveSort: function (skinName)
	{
		this.SortOrder = 1 - this.SortOrder;
		this.Sorted = false;
		this.SetContent(skinName);
	},
	
	SetSort: function (sortOrder, skinName)
	{
		this.SortOrder = sortOrder;
		this.Sorted = true;
		this.SetContent(skinName);
	},
	
	SetWidth: function (width)
	{
		var newWidth = width - 2*this._padding - this._resizerWidth;
		if (newWidth < 0) {
			this._htmlElem.className = 'wm_hide';
		}
		else {
			if (this._freeSort || this.SortField == SORT_FIELD_NOTHING) {
				this._htmlElem.className = '';
			}
			else {
				this._htmlElem.className = 'wm_control';
			}
			if (this.Width != width) {
				this.Width = width;
				this._htmlElem.style.width = newWidth + 'px';
				if (this.LineElem != null) {
					this.LineElem.style.width = newWidth + this._resizerWidth + 'px';
				}
				CreateCookie('wm_column_' + this.Id, width, COOKIE_STORAGE_DAYS);
			}
		}
	},
	
	ResizeWidth: function ()
	{
		var width = this.Resizer._leftPosition - this._left + this._resizerWidth;
		this.SetWidth(width);
		return this.Resizer._leftPosition + this._resizerWidth;
	},
	
	ResizeLeft: function (left)
	{
		if (this.isLast) {
			this.SetWidth(this.Width + this._left - left);
		}
		this._left = left;
		this._htmlElem.style.left = left + 'px';
		if (null != this.Resizer) {
			this.Resizer.updateLeftPosition(left + this.Width - this._resizerWidth);
		}
		if (this.isLast) {
			return left;
		}
		else {
			return left + this.Width;
		}
	},
	
	FreeSort: function ()
	{
		this._htmlElem.className = '';
		this._htmlElem.onclick = function () {};
		if (this.Sorted) {
			this.RemoveSort();
		};
		this._freeSort = true;
	},
	
	UseSort: function ()
	{
		if (this.SortField != SORT_FIELD_NOTHING) {
			this._htmlElem.className = 'wm_control';
		};
		if (this._sortHandler != null) {
			var obj = this;
			this._htmlElem.onclick = function () { obj._sortHandler.call({SortField: obj.SortField, SortOrder: 1-obj.SortOrder}); };
		};
		this._freeSort = false;
	},
	
	Build: function (parent, xleft, isLast, resizeHandler, skinName, sortHandler)
	{
		this.isLast = isLast;
		var child = CreateChild(parent, 'div');
		if (SORT_FIELD_NOTHING != this.SortField) {
			child.className = 'wm_control';
			var obj = this;
			this._sortHandler = sortHandler;
			child.onclick = function () { sortHandler.call({SortField: obj.SortField, SortOrder: 1-obj.SortOrder}); }
		};
		with (child.style) {
			textAlign = this.Align;
			if (this._isCheckBox) {
				paddingTop = '0';
			};
			paddingLeft = '2px';
			paddingRight = '2px';
			width = (this.Width - 2*this._padding - this._resizerWidth) + 'px';
			left = xleft + 'px';
			overflow = 'hidden';
		};
		this._left = xleft;
		this._htmlElem = child;
		this.SetContent(skinName);
		if (!isLast) {
			var child = CreateChild(parent, 'div');
			child.className = 'wm_inbox_headers_separate';
			with (child.style) {
				width = this._resizerWidth + 'px';
				left = (xleft + this.Width - this._resizerWidth) + 'px';
			};
			var child1 = CreateChild(child, 'div');
			if (this._isResize) {
				this.Resizer = new CVerticalResizer(child, parent, this._resizerWidth, xleft + this.MinWidth, 10, xleft + this.Width - this._resizerWidth, resizeHandler, 2);
			};
			return xleft + this.Width;
		};
		return xleft;
	}
};

function CInboxTableController(skinName, clickHandler, dblClickHandler, withLinks)
{
	this._skinName = skinName;
	this._clickHandler = clickHandler;
	this._dblClickHandler = dblClickHandler;
	this._doFlag = true;
	this._withLinks = withLinks;
	this.ResizeHandler = 'ResizeMessagesTab';
	this.ListContanerClass = 'wm_inbox';
	
	this.ChangeSkin = function (newSkin)
	{
		this._skinName = newSkin;
	};
	
	this.SetDoFlag = function (doFlag)
	{
		this._doFlag = doFlag;
	};
	
	this.CreateLine = function (obj, tr, separator, screenId)
	{
		tr.id = obj.GetIdForList(separator, screenId);
		return new CVariableMsgLine(this._skinName, obj, tr, this._doFlag, this._withLinks);
	};

	this.SetEventsHandlers = function (obj, tr)
	{
		var objController = this;
		tr.onclick = function(e) {
			if (null != obj._dragNDrop) obj._dragNDrop.EndDrag();
			e = e ? e : window.event;
			var clickElem;
			if (Browser.Mozilla) {
				clickElem = e.target
			}
			else {
				clickElem = e.srcElement;
			};
			var clickTagName = clickElem ? clickElem.tagName : 'NOTHING';
			if (objController._doFlag && clickTagName == 'IMG' && clickElem.id.substr(0,8) == 'flag_img') {
				obj._selection.FlagLine(this.id);
			}
			else if (clickTagName == 'INPUT' || e.ctrlKey) {
				obj._selection.CheckCtrlLine(this.id);
			}
			else if (e.shiftKey) {
				obj._selection.CheckShiftLine(this.id);
			}
			else if (objController._withLinks) {
				if (clickTagName == 'NOBR' && clickElem.id.substr(0,12) == 'view_message') {
					objController._dblClickHandler.call(this);
				}
			}
			else {
				var tdElem = clickElem;
				while (tdElem && tdElem.tagName != 'TD') {
					tdElem = tdElem.parentNode;
				};
				if (tdElem.name != 'not_view') {
					obj._selection.CheckLine(this.id);
					if (obj._lastClickLineId != this.id) {
						obj._lastClickLineId = this.id;
						obj._timer = setTimeout(objController._clickHandler + "('" + this.id + "')", 200);
					}
				}
			}
		};
		tr.ondblclick = function (e) {
			e = e ? e : window.event;
			if (Browser.Mozilla) {var elem = e.target;}
			else {var elem = e.srcElement;}
			if (!(elem && elem.tagName == 'INPUT')) {
				if (null != obj._dragNDrop) obj._dragNDrop.EndDrag();
				if (null != obj._timer) clearTimeout(obj._timer);
				objController._dblClickHandler.call(this);
			}
		}
	}
};

function CContactsTableController(skinName, separator)
{
	this._skinName = skinName;
	this._separator = separator;
	this.ResizeHandler = 'ResizeContactsTab';
	this.ListContanerClass = 'wm_contact_list_div';
	
	this.ChangeSkin = function (newSkin)
	{
		this._skinName = newSkin;
	};
	
	this.CreateLine = function (obj, tr)
	{
		tr.id = obj.Id + this._separator + obj.IsGroup + this._separator + obj.Name + this._separator + obj.ClearEmail;
		tr.Email = (!obj.IsGroup && obj.Name.length > 0) ? '"' + obj.Name + '" ' + obj.Email : obj.Email;
		return new CVariableContactLine(this._skinName, obj, tr);
	};

	this.SetEventsHandlers = function (obj, tr)
	{
		var objController = this;
		tr.onclick = function(e) {
			e = e ? e : window.event;
			var clickElem = (Browser.Mozilla) ? e.target : e.srcElement;
			var clickTagName = clickElem ? clickElem.tagName : 'NOTHING';
			if (clickTagName == 'INPUT' || e.ctrlKey) {
				obj._selection.CheckCtrlLine(this.id);
			}
			else if (e.shiftKey) {
				obj._selection.CheckShiftLine(this.id);
			}
			else {
				var tdElem = clickElem;
				while (tdElem && tdElem.tagName != 'TD') {
					tdElem = tdElem.parentNode;
				}
				if (tdElem.name != 'not_view') {
					obj._selection.CheckLine(this.id);
					var params = this.id.split(objController._separator);
					if (params.length == 4)
						if (params[1] == '0') {
							SetHistoryHandler(
								{
									ScreenId: SCREEN_CONTACTS,
									Entity: PART_VIEW_CONTACT,
									IdAddr: params[0]
								}
							);
						}
						else {
							SetHistoryHandler(
								{
									ScreenId: SCREEN_CONTACTS,
									Entity: PART_VIEW_GROUP,
									IdGroup: params[0]
								}
							);
						}
				}
			}
		};
		tr.ondblclick = function (e) {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_NEW_MESSAGE,
					FromDrafts: false,
					ForReply: false,
					FromContacts: true,
					ToField: HtmlDecode(this.Email)
				}
			);
			return false;
		}
	}
};

function CVariableTable(skinName, sortHandler, selection, dragNDrop, controller)
{
	this._skinName = skinName;
	this._sortHandler = sortHandler;
	
	this._columnsCount = 0;
	this._columnsArr = Array();
	this._sortedColumn = null;
	this.isSortFree = false;
	this._width = 0;
	
	this._headers = null;
	this._lines = null;
	this._linesTbl = null;
	
	this._selection = selection;
	this._dragNDrop = dragNDrop;
	this._timer = null;
	this._lastClickLineId = '';
	
	this._controller = controller;
}

CVariableTable.prototype = 
{
	CleanLines: function (msg)
	{
		this._selection.Free();
		if (null != this._dragNDrop) this._dragNDrop.SetSelection(null);
		CleanNode(this._lines);
		this._linesTbl = null;
		var div = CreateChild(this._lines, 'div');
		div.className = 'wm_inbox_info_message';
		div.innerHTML = msg;
	},
	
	ChangeSkin: function (newSkin)
	{
		this._skinName = newSkin;
		this._controller.ChangeSkin(newSkin);
		for (var i=0; i<this._columnsCount; i++) {
			var column = this._columnsArr[i];
			if (column.Sorted) {
				column.SetSort(column.SortOrder, newSkin);
			}
			else {
				column.RemoveSort(newSkin);
			}
		}
	},
	
	ResizeColumnsHeight: function ()
	{
		var hOffsetHeight = this._headers.offsetHeight;
		var lOffsetHeight = this._lines.offsetHeight;
		var minRightWidth = 0;
		for (var i=this._columnsCount-1; i>=0; i--) {
			if (this._columnsArr[i].Resizer != null) {
				this._columnsArr[i].Resizer.updateVerticalSize(hOffsetHeight - 1, hOffsetHeight + lOffsetHeight - 2);
				this._columnsArr[i].Resizer.updateMinRightWidth(minRightWidth);
			};
			if (i == this._columnsCount-1) {
				minRightWidth += this._columnsArr[i].MinWidth;
			}
			else {
				minRightWidth += this._columnsArr[i].Width;
			}
		}
	},
	
	ResizeColumnsWidth: function (number)
	{
		var left = this._columnsArr[number].ResizeWidth();
		for (var i=number+1; i<this._columnsCount; i++) {
			left = this._columnsArr[i].ResizeLeft(left);
		};
		this._width = left;
		this.ResizeColumnsHeight();
	},
	
	Resize: function (width)
	{
		this._headers.style.width = width + 'px';
		this._lines.style.width = width + 'px';
		if (this._linesTbl != null) {
			this._linesTbl.style.width = width + 'px';
		};

		var lastCell = this._columnsArr[this._columnsCount - 1];
		if (lastCell != null) {
			lastCell.SetWidth(width - this._width + lastCell._resizerWidth);
		};
		
		this.ResizeColumnsHeight();
	},
	
	GetHeight: function ()
	{
		var height = 0;
		var offsetHeight = this._headers.offsetHeight;
		if (offsetHeight) height += offsetHeight;
		offsetHeight = this._lines.offsetHeight;
		if (offsetHeight) height += offsetHeight;
		return height;
	},
	
	GetLines: function ()
	{
		return this._lines;
	},
	
	SetLinesHeight: function (height)
	{
		this._lines.style.height = height +'px';
	},
	
	AddColumn: function (id, params)
	{
		var column = new CVariableColumn(id, params, this._selection);
		this._columnsArr[this._columnsCount++] = column;
		return column;
	},
	
	SetSort: function (sortField, sortOrder)
	{
		if (!this.isSortFree) {
			if (this._sortedColumn != null) {
				this._sortedColumn.RemoveSort(this._skinName);
			};
			for (var i=0; i<this._columnsCount; i++) {
				var column = this._columnsArr[i];
				if (column.SortField == sortField) {
					column.SetSort(sortOrder, this._skinName);
					this._sortedColumn = column;
				}
			}
		}
	},

	FreeSort: function ()
	{
		this.isSortFree = true;
		for (var i=0; i<this._columnsCount; i++) {
			this._columnsArr[i].FreeSort();
		}
	},
	
	UseSort: function ()
	{
		this.isSortFree = false;
		for (var i=0; i<this._columnsCount; i++) {
			this._columnsArr[i].UseSort();
		}
	},
	
	Fill: function (objsArr, separator, screenId)
	{
		this._selection.Free();
		CleanNode(this._lines);
		this._lastClickLineId = '';
		if (null != this._dragNDrop) this._dragNDrop.SetSelection(this._selection);
		var tbl = CreateChild(this._lines, 'table');
		this._linesTbl = tbl;
		var tr;
		for (var i=0; i<objsArr.length; i++) {
			tr = tbl.insertRow(i);
			var obj = objsArr[i];
			var line = this._controller.CreateLine(obj, tr, separator, screenId);
			for (var j=0; j<this._columnsCount; j++) {
				var column = this._columnsArr[j];
				var td = tr.insertCell(j);
				line.SetContainer(column.Field, td);
				with (td.style) {
					textAlign = column.Align;
					paddingLeft = column._padding + 'px';
					paddingRight = column._padding + 'px';
				};
				if (i == 0) {
					column.LineElem = td;
					td.style.width = column.Width - 2*column._padding + 'px';
				}
			};
			this._selection.AddLine(line);
			if (null != this._dragNDrop) this._dragNDrop.AddDragObject(tr);
			this._controller.SetEventsHandlers(this, tr);
		};
		if (null != this._selection.Checkbox) {
			this._selection.Checkbox.checked = false;
		}
	},
	
	Build: function (parent)
	{
		var div = CreateChild(parent, 'div');
		div.className = this._controller.ListContanerClass;

		var headers = CreateChild(div, 'div');
		headers.className = 'wm_inbox_headers';
		this._headers = headers;

		var left = 0;
		for (var i=0; i<this._columnsCount; i++) {
			var column = this._columnsArr[i];
			left = column.Build(headers, left, (i == this._columnsCount-1), this._controller.ResizeHandler + '(' + i + ');', this._skinName, this._sortHandler);
			if (null != column.CheckBox) {
				this._selection.SetCheckBox(column.CheckBox);
			}
		};
		this._width = left;
		
		var lines = CreateChild(div, 'div');
		lines.className = 'wm_inbox_lines';
		this._lines = lines;
		return div;
	}
};

function CCheckBoxCell()
{
	this.Node = document.createElement('input');
	this.Node.type = 'checkbox';

	this.SetContainer = function (container) {
		container.appendChild(this.Node);
	}
}

function CImageCell(className, id, content)
{
	this.Node = document.createElement('img');
	if (className.length > 0) this.Node.className = className;
	if (id.length > 0) this.Node.id = id;
	this.Node.src = content;

	this.SetContainer = function (container) {
		container.appendChild(this.Node);
	};
	
	this.SetContent = function (content) {
		this.Node.src = content;
	}
}

function CTextCell(text)
{
	this.Content = text;
	this.Node = null;
	
	this.SetContainer = function (container, viewMessage) {
		this.Node = container;
		this.ApplyContentToContainer(viewMessage);
	};

	this.SetContent = function (content, viewMessage) {
		this.Content = content;
		this.ApplyContentToContainer(viewMessage);
	};
	
	this.ApplyContentToContainer = function (viewMessage) {
		if (viewMessage) {
			this.Node.innerHTML = '<a href="javascript:void(0)" onclick="return false;"><nobr id="view_message_' + 
			Math.random() + '">' + this.Content + '</nobr></a>';
		}
		else {
			this.Node.innerHTML = '<nobr>' + this.Content + '</nobr>';
		}
	}
};

function CVariableMsgLine(skinName, msg, tr, doFlag, withLinks)
{
	tr.onmousedown = function() { return false; };//don't select content in Opera
	tr.onselectstart = function() { return false; };//don't select content in IE
	tr.onselect = function() { return false; };//don't select content in IE
	this._skinName = skinName;

	this._className = '';
	this.Flagged = msg.Flagged;
	this.Replied = msg.Replied;
	this.Forwarded = msg.Forwarded;
	this.Deleted = msg.Deleted;
	this.Read = msg.Read;
	this.Checked = false;
	this.Gray = msg.Gray;
	this.withLinks = withLinks;

	this.Node = tr;
	this.Id = tr.id;
	this.SetClassName();
	this.ApplyClassName();
	
	this.fCheck = new CCheckBoxCell();
	
	var content = msg.HasAttachments ? 'skins/' + this._skinName + '/menu/attachment.gif' : 'images/1x1.gif';
	this.fHasAttachments = new CImageCell('', '', content);

	var className = doFlag ? 'wm_control_img' : '';
	content = 'skins/' + this._skinName + ((msg.Flagged) ? '/menu/flag.gif' : '/menu/unflag.gif');
	this.fFlagged = new CImageCell(className, 'flag_img' + Math.random(), content);

	this.fFromAddr = new CTextCell(msg.FromAddr);
	this.fToAddr = new CTextCell(msg.ToAddr);
	this.fDate = new CTextCell(msg.Date);
	this.fSize = new CTextCell(GetFriendlySize(msg.Size));
	this.fSubject = new CTextCell(msg.Subject);

	this.MsgFromAddr = msg.FromAddr;
	this.MsgDate = msg.Date;
	this.MsgSize = msg.Size;
	this.MsgSubject = msg.Subject;
	this.MsgId = msg.Id;
	this.MsgUid = msg.Uid;
	this.MsgFolderId = msg.FolderId;
	this.MsgFolderFullName = msg.FolderFullName;
}

CVariableMsgLine.prototype = 
{
	Check: function()
	{
		this.Checked = true;
		this.fCheck.Node.checked = true;
		this.ApplyClassName();
	},

	Uncheck: function()
	{
		this.Checked = false;
		this.fCheck.Node.checked = false;
		this.ApplyClassName();
	},
	
	Flag: function ()
	{
		RequestMessagesOperationHandler(FLAG, [this.Id], 0);
	},
	
	Unflag: function ()
	{
		RequestMessagesOperationHandler(UNFLAG, [this.Id], 0);
	},
	
	SetClassName: function ()
	{
		if (this.Deleted)
			this._className = 'wm_inbox_deleted_item';
		else if (this.Read)
			this._className = 'wm_inbox_read_item';
		else
			this._className = 'wm_inbox_item';
	},
	
	ApplyClassName: function ()
	{
		var className = this._className;
		if (this.Checked)
			className += '_select';
		else if (this.Gray)
			className += ' wm_inbox_grey_item';
		this.Node.className = className;
	},
	
	SetContainer: function (field, container)
	{
		if (field == 'fCheck' || field == 'fHasAttachments')
			container.name = 'not_view';
		this[field].SetContainer(container, ((field == 'fFromAddr' || field == 'fSubject') && this.withLinks));
	},

	ApplyFlagImg: function ()
	{
		var content = 'skins/' + this._skinName + ((this.Flagged) ? '/menu/flag.gif' : '/menu/unflag.gif');
		this.fFlagged.SetContent(content);
	},

	ApplyFromSubj: function ()
	{
		this.fFromAddr.SetContent(this.MsgFromAddr, this.withLinks);
		this.fSubject.SetContent(this.MsgSubject, this.withLinks);
	}
};

function CVariableContactLine(skinName, contact, tr)
{
	tr.onmousedown = function() { return false; };//don't select content in Opera
	tr.onselectstart = function() { return false; };//don't select content in IE
	tr.onselect = function() { return false; };//don't select content in IE
	this._skinName = skinName;

	this.Node = tr;
	this.Id = tr.id;
	this._className = 'wm_inbox_read_item';
	this.Checked = false;
	this.ApplyClassName();
	
	this.fCheck = new CCheckBoxCell();
	
	var content = contact.IsGroup ? 'skins/' + skinName + '/contacts/group.gif' : 'images/1x1.gif';
	this.fIsGroup = new CImageCell('', '', content);

	this.fName = new CTextCell(contact.Name);
	this.fEmail = new CTextCell(contact.Email);
}

CVariableContactLine.prototype = 
{
	Check: function()
	{
		this.Checked = true;
		this.fCheck.Node.checked = true;
		this.ApplyClassName();
	},

	Uncheck: function()
	{
		this.Checked = false;
		this.fCheck.Node.checked = false;
		this.ApplyClassName();
	},
	
	ApplyClassName: function ()
	{
		if (this.Checked)
			this.Node.className = this._className + '_select';
		else
			this.Node.className = this._className;
	},
	
	SetContainer: function (field, container)
	{
		if (field == 'fCheck' || field == 'fIsGroup')
			container.name = 'not_view';
		this[field].SetContainer(container, false);
	}
};

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
		if (this._suggestInput != null) {
			this._suggestInput.onkeyup = function () {}
		};
		this._suggestInput = suggestInput;
		suggestInput.setAttribute("autocomplete", "off");  
		var obj = this;
		this._suggestInput.onkeyup = function (ev) {
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
			if (siBounds.Width > pWidth) {
				this._popup.style.width = (siBounds.Width - horizBordersWidth) + 'px';
			}
			else {
				this._popup.style.width = (pWidth - horizBordersWidth) + 'px';
			};
			this._popup.style.height = 'auto';
			var pHeight = this._popup.offsetHeight;
			/*set popup height in absolute value for hiding select under popup in ie6*/
			this._popup.style.height = (pHeight - vertBordersWidth) + 'px';
		}
	},
	
	ClickBody: function (ev)
	{
	    if (this._shown) {
		    var ev = ev ? ev : window.event;
		    if (Browser.Mozilla) {
			    elem = ev.target;
		    }
		    else {
			    elem = ev.srcElement;
		    }
		    if (elem && elem.tagName == 'IMG' && elem.parentNode) {
			    elem = elem.parentNode;
		    }
		    if (elem && elem.tagName == 'B' && elem.parentNode) {
			    elem = elem.parentNode;
		    }
		    if (elem && isNaN(elem.Number) && elem.tagName != 'INPUT') {
			    this.Hide();
		    }
		    else if (elem && elem.tagName == 'DIV') {
			    this.SelectLine(elem);
		    }
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
		for (var i=0; i<iCount; i++) {
			var div = CreateChild(this._popup, 'div');
			var innerHtml = '';
			if (itemsArr[i].ImgSrc.length > 0) {
				innerHtml = '<img src="' + itemsArr[i].ImgSrc + '" class="wm_auto_filling_img_group"/>';
			};
			div.innerHTML = innerHtml + itemsArr[i].DisplayText;
			div.ContactGroup = itemsArr[i];
			div.Number = i;
			div.onmouseover = function () {
				obj.PickLine(this.Number);
			};
			div.onmouseout = function () {
				this.className = '';
				if (obj._pickPos == this.Number) {
					obj._pickPos = -1;
				}
			};
			this._lines[i] = div;
		};
		if (lastPhrase && lastPhrase.length > 0) {
			var div = CreateChild(this._popup, 'div');
			div.className = 'wm_secondary_info';
			div.innerHTML = lastPhrase;
		};
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
        if (iCount > 0) {
            arr[iCount - 1] = Trim(suggestionStr);
        }
        else {
            arr[0] = ' ' + Trim(suggestionStr);
        };
        this._suggestInput.value = arr.join(',');
	},
	
	SelectLine: function (obj)
	{
		this.Hide();
		this.SetSuggestions(obj.ContactGroup.ReplaceText) ;
		this._pickPos = -1;
		this._suggestInput.focus();
		if (Browser.IE) {
			var textRange = this._suggestInput.createTextRange();
			textRange.collapse(false);
			textRange.select();
		};
		this._selectHandler.call(obj);
	},
	
	PickLine: function (posInt)
	{
		if (this._pickPos != -1) {
			this._lines[this._pickPos].className = '';
		};
		this._pickPos = posInt;
		if (this._pickPos != -1) {
			this._lines[this._pickPos].className = 'wm_auto_filling_chosen';
		}
	},
	
	KeyUpHandler: function (ev)
	{
		ev = ev ? ev : window.event;
		var key = -1;
		if (window.event) {
			key = window.event.keyCode;
		}
		else if (ev) {
			key = ev.which;
		};
		if (key == 13) { //enter
			if (this._pickPos != -1) {
				var td = this._lines[this._pickPos];
				this.SelectLine(td);
			}
		}
		else if (key == 38) { //up
			if (this._pickPos > -1) {
				this.PickLine(this._pickPos - 1);
			}
		}
		else if (key == 40) { //down
			if (this._pickPos < (this._lines.length - 1)) {
				this.PickLine(this._pickPos + 1);
			}
		}
		else {
			var keyword = this.GetKeyword();
			if (this.CheckRequestKeyword(keyword)) {
				if (this._timeOut != null) clearTimeout(this._timeOut);
				var obj = this;
				this._timeOut = setTimeout ( function () { obj.RequestKeyword(); }, 500 );
			}
			else if (keyword.length == 0) {
				this.Hide();
			}
		}
	},
	
	CheckRequestKeyword: function (keyword)
	{
		if (keyword.length > 0 && this._keyword != keyword) {
			if (this._requestKeyword.length > 0) {
				var reg = new RegExp(this._requestKeyword.PrepareForRegExp(), "gi");
				var res = reg.exec(keyword);
				if (res != null && res.index == 0) {
					return false;
				}
				else {
					return true;
				}
			};
			return true;
		}
		else {
			return false;
		}
	},
	
	RequestKeyword: function ()
	{
		var keyword = this.GetKeyword();
		if (this.CheckRequestKeyword(keyword)) {
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
};

function CPopupContacts(requestHandler, selectHandler)
{
	this._suggestInput = null;
	this._suggestControl = null;
	this._closeImage = null;

    this._requestHandler = requestHandler;
	this._selectHandler = selectHandler;

	this._popup = null;
	this._shown = false;

	this._pickPos = -1;
	this._lines = Array();

	this._timeOut = null;

	this.Build();
}

CPopupContacts.prototype =
{
    ControlClick: function (suggestInput, suggestControl)
    {
	    if (this._shown && this._suggestInput == suggestInput) {
	        this.Hide();
	        return;
	    }
	    else {
		    this._suggestInput = suggestInput;
		    this._suggestControl = suggestControl;
	        this._requestHandler.call({ Keyword: '' });
	    }
    },
    
	Show: function ()
	{
		this._popup.className = 'wm_popular_contacts_cont';
		this._shown = true;
		this.Replace();
	},
	
	Hide: function ()
	{
		this._popup.style.width = 'auto';//for Opera
		this._popup.style.height = 'auto';//for Opera
		this._popup.className = 'wm_hide';
		this._shown = false;
	},
	
	Replace: function ()
	{
		if (this._shown)
		{
			var siBounds = GetBounds(this._suggestInput);
			this._popup.style.top = siBounds.Top + siBounds.Height + 1 + 'px';
			var scBounds = GetBounds(this._suggestControl);
			this._popup.style.left = scBounds.Left + 'px';

			this._popup.style.width = 'auto';
			this._popup.style.height = 'auto';
			var pWidth = this._popup.offsetWidth;
			var pHeight = this._popup.offsetHeight;
			var bordersHeight = 2;
			var bordersWidth = 2;
			var paddingHeight = 16;
			/*set popup width and height in absolute value for hiding select under popup in ie6*/
			this._popup.style.width = pWidth - bordersWidth + 'px';
			this._popup.style.height = pHeight - bordersHeight - paddingHeight + 'px';
			
			if (null != this._closeImage) {
			    this._closeImage.style.top = '4px';
			    this._closeImage.style.left = pWidth - 14 + 'px';
			}
		}
	},
	
	ClickBody: function (ev)
	{
	    if (this._shown) {
		    var ev = ev ? ev : window.event;
		    if (Browser.Mozilla) {
			    elem = ev.target;
		    }
		    else {
			    elem = ev.srcElement;
		    };
		    while (elem && elem.tagName != 'DIV' && elem.parentNode) {
			    elem = elem.parentNode;
		    };
		    if (elem && elem.className != 'wm_popular_contacts_cont' && elem.parentNode) {
		        elem = elem.parentNode;
		    };
		    if (elem && elem.className != 'wm_popular_contacts_cont') {
			    this.Hide();
		    }
	    }
	},

	Fill: function (itemsArr, closeImageSrc)
	{
		var obj = this;
		CleanNode(this._popup);
		MakeOpaqueOnSelect(this._popup);
		var img = CreateChildWithAttrs(this._popup, 'img', [['src', closeImageSrc], ['class', 'wm_popular_contacts_image wm_control']]);
		img.onclick = function () { obj.Hide(); };
		this._closeImage = img;
		this._pickPos = -1;
		this._lines = Array();
		var iCount = itemsArr.length;
		for (var i=0; i<iCount; i++) {
			var div = CreateChild(this._popup, 'div');
			var innerHtml = '';
			if (itemsArr[i].ImgSrc.length > 0) {
				innerHtml = '<img src="' + itemsArr[i].ImgSrc + '" class="wm_auto_filling_img_group"/>';
			}
			div.innerHTML = innerHtml + itemsArr[i].DisplayText;
			div.ContactGroup = itemsArr[i];
			div.Number = i;
			div.onmouseover = function () {
				obj.PickLine(this.Number);
			};
			div.onmouseout = function () {
				this.className = '';
				if (obj._pickPos == this.Number) {
					obj._pickPos = -1;
				}
			};
			div.onclick = function () {
			    obj.SelectLine(this);
			};
	        div.onmousedown = function() { return false; };//don't select content in Opera
	        div.onselectstart = function() { return false; };//don't select content in IE
	        div.onselect = function() { return false; };//don't select content in IE
			this._lines[i] = div;
		};
		this.Show();
	},
	
	SetSuggestions: function (suggestionStr)
	{
	    var inputValue = this._suggestInput.value;
	    if (inputValue.length > 0) {
		    this._suggestInput.value = this._suggestInput.value + ', ' + suggestionStr;
		}
		else {
		    this._suggestInput.value = suggestionStr;
		}
	},
	
	SelectLine: function (obj)
	{
		this.SetSuggestions(obj.ContactGroup.ReplaceText) ;
		this._selectHandler.call(obj);
	},
	
	PickLine: function (posInt)
	{
		if (this._pickPos != -1) {
			this._lines[this._pickPos].className = '';
		};
		this._pickPos = posInt;
		if (this._pickPos != -1) {
			this._lines[this._pickPos].className = 'wm_auto_filling_chosen';
		}
	},
	
	Build: function ()
	{
		this._popup = CreateChild(document.body, 'div');
		this._popup.style.position = 'absolute';
		this.Hide();
	}
};