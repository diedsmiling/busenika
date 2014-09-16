/*
 * Classes:
 *  CHistoryStorage
 * 
 *  CDataType
 *  CDataSource
 *  CCache
 * 
 *  CDragNDrop
 * 
 *  CPageSwitcher
 *  CLanguageChanger
 *  CMessagePicturesController
 * 
 * Handlers
 */

function CHistoryStorage(SettingsStorage)
{
	// this for checking HistoryStorage can working
	this.Ready = false;
	// errors list
	this.Errors = [];
	// save input data
	if(SettingsStorage) {
		this.InputSettings = SettingsStorage;
	};
	// default value for steps limit
	this._DefaultMaxLimitSteps = 50;
	// maximum length for error list
	this._MaxErrorListLength = 20;
	// dictionary for save data
	this.Dictionary = new CDictionary();
	this.InStep = false;
	this.Queue = Array();
	this.KeysInStep = Array();
	this.PrevKey = '';
	
	this._historyKey = null;
	this._historyObjectName = null;
	this._form = null;

	// execute initialization
	this.Initialize();
}

CHistoryStorage.prototype = {
	AddError: function(StrError)
	{
		if (this.Errors.length >= this._MaxErrorListLength) {
			this.Errors.reverse().pop();
			this.Errors.reverse();
		};
		this.Errors[this.Errors.length] = StrError;
	},

	Initialize: function()
	{
		this.Ready = true;
		if (typeof(this.InputSettings.Document) == 'object' && this.InputSettings.Document != null) {
			this.Document = this.InputSettings.Document;
		}
		else {
			this.Ready = false;
		};
		if (typeof(this.InputSettings.Browser) == 'object' && this.InputSettings.Browser != null) {
			this.Browser = this.InputSettings.Browser;
		}
		else {
			this.Ready = false;
		};
		if (typeof(this.InputSettings.HistoryStorageObjectName) == 'string') {
			this.HistoryStorageObjectName = this.InputSettings.HistoryStorageObjectName;
		}
		else {
			this.Ready = false;
		};
		if (typeof(this.InputSettings.PathToPageInIframe) == 'string') {
			this.PathToPageInIframe = this.InputSettings.PathToPageInIframe;
		}
		else {
			this.Ready = false;
		};

		var _tempLimit = parseInt(this.InputSettings.MaxLimitSteps);
		if(isNaN(_tempLimit)) {
			this.AddError('The maximum number of steps that you specified is invalid. Default value 15 assigned.');
			_tempLimit = this._DefaultMaxLimitSteps;
		}
		else {
			if(_tempLimit < 1) {
				this.AddError('The maximum number of steps that you specified is invalid. Default value 15 assigned.');
				_tempLimit = this._DefaultMaxLimitSteps;
			}
		};
		this.MaxLimitSteps = _tempLimit;

		var iframe = CreateChildWithAttrs(document.body, 'iframe', [['id', 'HistoryStorageIframe'], ['name', 'HistoryStorageIframe'], ['src', EmptyHtmlUrl], ['class', 'wm_hide']]);
		var frm = CreateChildWithAttrs(document.body, 'form', [['action', this.PathToPageInIframe], ['target', 'HistoryStorageIframe'], ['method', 'post'], ['id', 'HistoryForm'], ['name', 'HistoryForm'], ['class', 'wm_hide']]);
		this._historyKey = CreateChildWithAttrs(frm, 'input', [['type', 'text'], ['name', 'HistoryKey']]);
		this._historyObjectName = CreateChildWithAttrs(frm, 'input', [['type', 'text'], ['name', 'HistoryStorageObjectName']]);
		this._form = frm;
	},

	ProcessHistory: function(HistoryKey) {
		this.InStep = false;
		if (this.KeysInStep[HistoryKey]) {
			delete this.KeysInStep[HistoryKey];
		}
		else {
			this.RestoreFromHistory(HistoryKey);
		}
	},
	
	RestoreFromHistory: function (HistoryKey) {
		if(this.Dictionary.exists(HistoryKey)) {
			var HistoryObject = this.Dictionary.getVal(HistoryKey);
			var args = HistoryObject.Args;
			var ExecuteCommand = 'window.' + HistoryObject.FunctionName + '(HistoryObject.Args)';
			eval(ExecuteCommand);
		}
		else {
			this.AddError('The specified key doesn\'t exists in history storage');
		};

		if (this.Queue.length > 0) {
			var key = this.Queue.shift();
			if (this.Dictionary.exists(key)) {
				this.DoStep(key);
			}
		}
	},

	AddStep: function(ObjectData){
		var newKey = String(new Date()) + Math.random();

		if( this.Dictionary.count >= this.MaxLimitSteps ) {
			//remove first step because steps count is more then limit
			var keys = this.Dictionary.keys();
			this.Dictionary.remove( keys[0] );
		};
		//add new step
		this.Dictionary.add( newKey, ObjectData );

		if (this.InStep) {
			//move step key to Queue because previouse step still not finished
			this.Queue.push(newKey);
		}
		else {
			//realize step
			this.DoStep(newKey);
		}
	},
	
	DoStep: function (newKey) {
		if (Browser.Mozilla) {
			WebMail.DataSource.NetLoader.CheckRequest();
			WebMail.HideInfo();
			//this.InStep = false;
		};
		if(this.Ready && !this.Browser.Opera) {
			if (this.KeysInStep[this.PrevKey]) {
				delete this.KeysInStep[this.PrevKey];
			};
			this._historyKey.value = newKey;
			this._historyObjectName.value = this.HistoryStorageObjectName;
			this._form.action = this.PathToPageInIframe + '?param=' + Math.random();
			this._form.submit();
			this.KeysInStep[newKey] = true;
			this.PrevKey = newKey;
			//this.InStep = true;
			this.RestoreFromHistory(newKey);
		}
		else {
			this.RestoreFromHistory(newKey);
			this.AddError('Couldn\'t processing action. See Errors list for details.');
		}
	}
};

function CDataType(Type, Caching, CacheLimit, CacheByParts, RequestParams, GetRequest)
{
	this.Type = Type;//int
	this.Caching = Caching;//bool
	this.CacheLimit = CacheLimit;//int
	this.CacheByParts = CacheByParts;//bool
	this.RequestParams = RequestParams;//obj
	/*
	ex. for messages list: {
			IdFolder: "id_folder",
			SortField: "sort_field",
			SortOrder: "sort_order",
			Page: "page"
		}
	*/
	this.GetRequest = GetRequest;//string; ex. for messages list: 'messages'
}

function CDataSource(DataTypes, ActionUrl, ErrorHandler, InfoHandler, LoadHandler, TakeDataHandler, RequestHandler)
{
	this._SEPARATOR = '#@%';

	this.Cache = new CCache(DataTypes);
	this.NetLoader = new CNetLoader();

	this.Data = null;

	this.ActionUrl = ActionUrl;

	this.Info = null;
	this.ErrorDesc = null;

	this.onInfo = InfoHandler;
	this.onError = ErrorHandler;
	this.onLoad = LoadHandler;
	this.onGet = TakeDataHandler;
	this.onRequest = RequestHandler;

	this.DataTypes = [];
	for (Key in DataTypes) {
		this.DataTypes[DataTypes[Key].Type] = DataTypes[Key];
	}
}

CDataSource.prototype = {
	Get: function( intDataType, objDataKeys, arDataParts, xml )
	{
		var Cache = this.Cache;
		var DataType = this.DataTypes[intDataType];
		var Caching = DataType.Caching;
		var CacheByParts = DataType.CacheByParts;

		var Mode = 0;
		if (CacheByParts) {
			for (Key in arDataParts) {
				Mode = (1 << arDataParts[Key]) | Mode;
			}
		};

		var arDataKeys = [];
		for(Key in objDataKeys) { arDataKeys.push( objDataKeys[Key] ); };
		if (Caching) {
			var StringDataKeys = DataType.GetRequest + this._SEPARATOR + arDataKeys.join(this._SEPARATOR);
		}
		else {
			var StringDataKeys = DataType.GetRequest;
		};

		this.Data = null;
		if (Caching && Cache.ExistsData( intDataType, StringDataKeys )) {// there is in the cache!
			this.Data = Cache.GetData( intDataType, StringDataKeys );
			if (CacheByParts) {
				Mode = (Mode | this.Data.Parts) ^ this.Data.Parts;
			}
		};

		if (!(Caching && Cache.ExistsData( intDataType, StringDataKeys )) || (CacheByParts && (Mode != 0))) {
			var Url = this.ActionUrl;
			var arParams = [];
			arParams['action'] = 'get';
			arParams['request'] = DataType.GetRequest;
			if (CacheByParts) arParams['mode'] = Mode;
			var objRequestParams = DataType.RequestParams;
			for(var Param in objRequestParams) {
				arParams[objRequestParams[Param]] = objDataKeys[Param];
			};
			var XMLParams = this.GetXML(arParams, xml);
			this.onRequest.call(this);
			//alert(XMLParams);//
			this.NetLoader.LoadXMLDoc( Url, 'xml=' + encodeURIComponent(XMLParams), this.onLoad, this.onError );
		}
		else {
			this.onGet.call(this);
		}
	},
	
	Set: function (messageParams, field, value, isAllMess)
	{
		this.Cache.SetData(TYPE_MESSAGES_LIST, messageParams, field, value, isAllMess);
	},

	Request: function( objParams, xml )
	{
		var Url = this.ActionUrl;
		var XMLParams = this.GetXML(objParams, xml);
		this.onRequest.call(this);
		//alert(XMLParams);//
		this.NetLoader.LoadXMLDoc( Url, 'xml=' + encodeURIComponent(XMLParams), this.onLoad, this.onError );
	},
	
	GetXML: function( arParams, xml )
	{
		var strResult = '';
		for(var ParamName in arParams) {
			strResult += '<param name="' + ParamName + '" value="' + arParams[ParamName] + '"/>';
		};
		strResult = '<?xml version="1.0" encoding="utf-8"?><webmail>' + strResult + xml + '</webmail>';
		return strResult;
	},
	
	ParseXML: function(XmlDoc, TextDoc)
	{
		if (XmlDoc && XmlDoc.documentElement && typeof(XmlDoc) == 'object' &&
		 typeof(XmlDoc.documentElement) == 'object') {
			var RootElement = XmlDoc.documentElement;
			if (RootElement && RootElement.tagName == 'webmail') {
				var Objects = RootElement.childNodes;
				if ( Objects.length == 0 ) {
					this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 4.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
					this.onError.call(this);
				}
				else {
					this.Data = null;
					var ObjectXML = null;
					var isObject = false;
					for (var key=Objects.length-1; key>=0; key--) {
						var ObjectName = Objects[key].tagName;
						switch (ObjectName) {
							case 'settings_list':
								this.Data = new CSettingsList();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'update':
								this.Data = new CUpdate();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'accounts':
								this.Data = new CAccounts();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'message':
								this.Data = new CMessage();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'messages':
								this.Data = new CMessages();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'operation_messages':
								this.Data = new COperationMessages();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'folders_list':
								this.Data = new CFoldersList();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'settings':
								this.Data = new CSettings();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'account':
								this.Data = new CAccountProperties();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'filters':
								this.Data = new CFilters();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'filter':
								this.Data = new CFilterProperties();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'x_spam':
								this.Data = new CXSpam();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'contacts_settings':
								this.Data = new CContactsSettings();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'signature':
								this.Data = new CSignature();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'contacts_groups':
								this.Data = new CContacts();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'contact':
								this.Data = new CContact();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'groups':
								this.Data = new CGroups();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'group':
								this.Data = new CGroup();
								ObjectXML = Objects[key]; isObject = true;
								break;
							case 'information':
								var Info = Objects[key].childNodes[0].nodeValue;
								if (Info && Info.length > 0) {
								    WebMail.ShowReport(Info, 10000);
								};
								break;
							case 'error':
								var attr = Objects[key].getAttribute('code');
								if (attr) {
									document.location = LoginUrl + '?error=' + attr;
								}
								else {
									var ErrorDesc = Objects[key].childNodes[0].nodeValue;
									if (ErrorDesc && ErrorDesc.length > 0) {
										this.ErrorDesc = ErrorDesc;
									}
									else {
										this.ErrorDesc = Lang.ErrorWithoutDesc;
									};
									this.onError.call(this);
								};
								break;
							case 'session_error':
								document.location = LoginUrl + '?error=1';
							break;
							case 'spellcheck':
								this.Data = new CSpellchecker();
								ObjectXML = Objects[key]; isObject = true;
							break;
						}//switch (ObjectName)
					}//for
					if (isObject == true) {
						if (this.Data && ObjectXML) {
							var Cache = this.Cache;
							var intDataType = this.Data.Type;
							var DataType = this.DataTypes[intDataType];
							if (typeof(DataType) == 'object') {
								var Caching = DataType.Caching;
								var CacheByParts = DataType.CacheByParts;
								this.Data.GetFromXML(ObjectXML);
								if (Caching) {
									StringDataKeys = DataType.GetRequest + this._SEPARATOR + this.Data.GetStringDataKeys(this._SEPARATOR);
									if (CacheByParts && Cache.ExistsData( intDataType, StringDataKeys)) {
										this.Data = Cache.GetData( intDataType, StringDataKeys );
										this.Data.GetFromXML(ObjectXML);
										Cache.ReplaceData(intDataType, StringDataKeys, this.Data);
									}
									else {
										Cache.AddData(intDataType, StringDataKeys, this.Data);
									}
								}
								if (this.Data.Type == TYPE_MESSAGE) {
									this.Set([[this.Data.Id], this.Data.FolderId, this.Data.FolderFullName], 'Read', true);
								}
							}
							else {
								this.Data.GetFromXML(ObjectXML);
							}
							this.onGet.call(this);
						}
						else {
							this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 3.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
							this.onError.call(this);
						}
					}//if (isObject == true)
				}// if (Objects.length == 0)
			}
			else {
				this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 2.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
				this.onError.call(this);
			}//if (RootElement.tagName == 'webmail')
		}
		else {
			this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 1.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
			this.onError.call(this);
		}//if (RootElement)
	}
};

function CCache(DataTypes)
{
	this.DataTypes = [];
	this.Dictionaries = [];
	for(a in DataTypes) {
		this.AddDataType(DataTypes[a]);
	}
}

CCache.prototype = {
	AddDataType: function(ObjectDataType)
	{
		this.DataTypes[ObjectDataType.Type] = ObjectDataType;
		this.Dictionaries[ObjectDataType.Type] = new CDictionary();
	},

	ExistsData: function(DataType, Key)
	{
		if( typeof( this.DataTypes[DataType] ) == 'object' && typeof( this.Dictionaries[DataType] ) == 'object' ) {
			return this.Dictionaries[DataType].exists( Key );
		}
		else {
			return false;
		}
	},

	AddData: function(DataType, Key, Value)
	{
		if (this.Dictionaries[DataType].count >= this.DataTypes[DataType].CacheLimit) {
			var Keys = this.Dictionaries[DataType].keys();
			this.Dictionaries[DataType].remove(Keys[0]);
		};
		this.Dictionaries[DataType].add( Key, Value );
	},
	
	SetMessageSafety: function(msgId, msgUid, folderId, folderFullName, safety, isAll)
	{
		var dict = this.Dictionaries[TYPE_MESSAGE];
		var keys = dict.keys();
		for (var i in keys) {
			var msg = dict.getVal(keys[i]);
			if (isAll || msg.Id == msgId && msg.Uid == msgUid && msg.FolderId == folderId &&
			 msg.FolderFullName == folderFullName) {
				msg.ShowPictures();
				msg.Safety = safety;
				dict.setVal(keys[i], msg);
				if (!isAll) break;
			}
		}
	},
	
	SetSenderSafety: function(fromAddr, safety)
	{
		var fromParts = GetEmailParts(HtmlDecode(fromAddr));
		var fromEmail = fromParts.Email;
		var dict = this.Dictionaries[TYPE_MESSAGE];
		var keys = dict.keys();
		for (var i in keys) {
			var msg = dict.getVal(keys[i]);
			var fParts = GetEmailParts(HtmlDecode(msg.FromAddr));
			var fEmail = fParts.Email;
			if (fromEmail == fEmail) {
				msg.ShowPictures();
				msg.Safety = safety;
				dict.setVal(keys[i], msg);
			}
		}
	},
	
	SetMessagesCount: function(folderId, folderFullName, count, countNew)
	{
		var dict = this.Dictionaries[TYPE_MESSAGES_LIST];
		var keys = dict.keys();
		for (var i in keys) {
			var messages = dict.getVal(keys[i]);
			if (messages.FolderId == folderId && messages.FolderFullName == folderFullName && messages._lookFor.length == 0) {
				if (messages.MessagesCount != count) {
				    dict.remove(keys[i]);
				}
				else {
				    messages.NewMsgsCount = countNew;
				    dict.setVal(keys[i], messages);
				}
			}
		}
	},
	
	ClearMessagesList: function(folderId, folderFullName, byFlag)
	{
		var dict = this.Dictionaries[TYPE_MESSAGES_LIST];
		var keys = dict.keys();
		if (folderId == '-1' && folderFullName == '') {
			dict.removeAll();
		}
		else {
			for (var i in keys) {
				var messages = dict.getVal(keys[i]);
				var remove = true;
				if (byFlag && messages.SortField != SORT_FIELD_FLAG) {
					remove = false;
				};
				if (remove && messages.FolderId == folderId && messages.FolderFullName == folderFullName ||
					messages.FolderId == '-1' && messages.FolderFullName == '') {
						dict.remove(keys[i]);
				}
			}
		}
	},
	
	ClearAllMessages: function()
	{
		this.Dictionaries[TYPE_MESSAGES_LIST].removeAll();
		this.Dictionaries[TYPE_MESSAGE].removeAll();
	},
	
	ClearMessage: function(id, uid, folderId, folderFullName, charset)
	{
		var deleted = false;
		var dict = this.Dictionaries[TYPE_MESSAGE];
		var keys = dict.keys();
		for (var i in keys) {
			var msg = dict.getVal(keys[i]);
			if (msg.Id == id && msg.Uid == uid &&
			 msg.FolderId == folderId && msg.FolderFullName == folderFullName &&
			 msg.Charset != charset) {
				dict.remove(keys[i]);
				deleted = true;
			}
		};
		return deleted;
	},
	
	SetData: function (type, messageParams, field, value, isAllMess)
	{
		var dict = this.Dictionaries[type];
		var keys = dict.keys();
		for (var i in keys) {
			var messages = dict.getVal(keys[i]);
			if (messages.FolderId == messageParams[1] && messages.FolderFullName == messageParams[2] ||
				(messages.FolderId == '-1' && messages.FolderFullName == '' && !isAllMess)) {
				var idArray = messageParams[0];
				for (var j in messages.List) {
					data = messages.List[j];
					if (isAllMess) {
						data[field] = value;
						messages.List[j] = data;
					}
					else {
						for (var k in idArray) {
							if (data.Id == idArray[k].Id && data.Uid == idArray[k].Uid && data.FolderId == messageParams[1] && data.FolderFullName == messageParams[2]) {
								data[field] = value;
								messages.List[j] = data;
							}
						}
					}
				};
				dict.setVal(keys[i], messages);
			}
		}
	},

	GetData: function(DataType, Key)
	{
		return this.Dictionaries[DataType].getVal( Key );
	},
	
	ReplaceData: function(DataType, Key, Value)
	{
		this.Dictionaries[DataType].setVal( Key, Value );
	}
};

function CDragNDrop(langField, dropImg, notDropImg, skinName) {
	this._selection = null;
	this._langField = langField;
	this._dropImg = dropImg;
	this._notDropImg = notDropImg;
	this._skinName = skinName;
	this._dragObjects = [];
	this._dragCount = 0;
	this._dropObjects = [];
	this._dropCount = 0;
	this._dropContainer = null;
	this._scrollY = 0;
	this._handle = CreateChildWithAttrs(document.body, 'div', [['class', 'wm_hide']]);
	this._handleImg = CreateChildWithAttrs(this._handle, 'img', [['src', 'skins/' + this._skinName + '/' + this._notDropImg]]);
	this._dropHandleImg = CreateChildWithAttrs(this._handle, 'img', [['src', 'skins/' + this._skinName + '/' + this._dropImg], ['class', 'wm_hide']]);
	this._handleText = CreateChild(this._handle, 'span');
	this._dragId = '';
	this._dropId = '';
	this._dropElem = null;
	this.doMoveToInbox = true;
	this._inboxId = '';
	this._x1 = 0;
	this._y1 = 0;
	this._x2 = 0;
	this._y2 = 0;
	this.first = true;
}

CDragNDrop.prototype = {
	SetMoveToInbox: function (doMoveToInbox)
	{
		this.doMoveToInbox = doMoveToInbox;
	},
	
	SetDropContainer: function (dropContainer)
	{
		this._dropContainer = dropContainer;
	},
	
	SetInboxId: function (id)
	{
		this._inboxId = id;
	},
	
	SetSelection: function (selection)
	{
		this._selection = selection;
		if (null == selection) {
			this._dragObjects = [];
			this._dragCount = 0;
		}
	},
	
	AddDragObject: function (element)
	{
		var obj = this;
		element.onmousedown = function(e) {
			e = e ? e : event;
			if (e.button != 2 && e.button != 3) {
				obj.RequestDrag(e, this);
			};
			return false;
		};
		this._dragObjects[this._dragCount] = element;
		this._dragCount++;
	},
	
	SetCoordinates: function (element)
	{
		var bounds = GetBounds(element);
		element._x1 = bounds.Left;
		element._y1 = bounds.Top - this._scrollY;
		element._x2 = bounds.Left + bounds.Width;
		element._y2 = bounds.Top - this._scrollY + bounds.Height;
		if (this._x1 == 0 && this._y1 == 0 && this._x2 == 0 && this._y2 == 0) {
			this._x1 = element._x1;
			this._y1 = element._y1 + this._scrollY;
			this._x2 = element._x2;
			this._y2 = element._y2 + this._scrollY;
		}
		else {
			if (this._x1 > element._x1) {
				this._x1 = element._x1;
			};
			if (this._y1 > element._y1) {
				this._y1 = element._y1;
			};
			if (this._x2 < element._x2) {
				this._x2 = element._x2;
			};
			if (this._y2 < element._y2) {
				this._y2 = element._y2;
			}
		}
	},
	
	AddDropObject: function (element)
	{
		this.SetCoordinates(element);
		this._dropObjects[this._dropCount] = element;
		this._dropCount++;
	},
	
	Resize: function ()
	{
		this._x1 = 0;
		this._y1 = 0;
		this._x2 = 0;
		this._y2 = 0;
		for (var i=0; i<this._dropCount; i++) {
			this.SetCoordinates(this._dropObjects[i]);
		}
	},
	
	CleanDropObjects: function ()
	{
		this._dropObjects = [];
		this._dropCount = 0;
	},
	
	Ready: function ()
	{
		if (null == this._selection) return false;
		if (0 == this._dragCount) return false;
		if (0 == this._dragId.length) return false;
		return true;
	},
	
	RequestDrag: function (e, element)
	{
		if (!e.ctrlKey && !e.shiftKey) {
			this._dragId = element.id;
			element.blur();
			var obj = this;
			element.onmouseout = function (e) {
				e = e ? e : event;
				obj.StartDrag(e, this);
			}
		}
	},
	
	StartDrag: function (e, element)
	{
		element.onmouseout = function () {};
		if (this.Ready()) {
			var number = this._selection.DragItemsNumber(this._dragId);
			var handle = this._handle;
			handle.className = 'wm_drag_handle';
			handle.style.top = (e.clientY + 5) + 'px';
			handle.style.left = (e.clientX + 5) + 'px';
			this._handleText.innerHTML = number + ' ' + Lang[this._langField];
			this._handleImg.src = 'skins/' + this._skinName + '/' + this._notDropImg;
			var obj = this;
			document.body.onmousemove = function(e) {
	   			e = e ? e : event;
	   			obj.ProcessDrag(e); 
			};
			document.body.onmouseup = function() {
				obj.EndDrag();
			}
		}
	},
	
	ProcessDrag: function (e)
	{
		var x = e.clientX;
		var y = e.clientY;
		with (this._handle.style) {
			top = (e.clientY + 5) + 'px';
			left = (e.clientX + 5) + 'px';
		};
		if (null != this._dropElem) {
			this._dropElem.className = '';
		};
		var scrollY = GetScrollY(this._dropContainer);
		if (scrollY != this._scrollY) {
		    this._scrollY = scrollY;
		    this.Resize();
		};
		if (x > this._x1 && x < this._x2 && y > this._y1 && y < this._y2) {
			for (var i=0; i<this._dropCount; i++) {
				var element = this._dropObjects[i];
				if (x > element._x1 && x < element._x2 && y > element._y1 && y < element._y2) {
					if (-1 == this._dragId.indexOf(element.id) && (this.doMoveToInbox || this._inboxId != element.id)) {
						this._dropId = element.id;
						this._dropElem = element;
						this._handleImg.src = 'skins/' + this._skinName + '/' + this._dropImg;
						document.body.style.cursor = 'pointer';
						element.className = 'wm_folder_over';
					}
					else {
						this._dropId = '';
						this._dropElem = null;
						this._handleImg.src = 'skins/' + this._skinName + '/' + this._notDropImg;
						document.body.style.cursor = 'auto';
					}
				}
			}
		}
		else {
			this._dropId = '';
			this._handleImg.src = 'skins/' + this._skinName + '/' + this._notDropImg;
			document.body.style.cursor = 'auto';
		}
	},
	
	EndDrag: function ()
	{
		if (this._dropId.length > 0) {
			MoveToFolderHandler(this._dropId);
			this.first = false;
		};
		document.body.style.cursor = 'auto'; 
		this._handle.className = 'wm_hide';
		this._dragId = '';
		this._dropId = '';
		document.body.onmousemove = function () { };
		document.body.onmouseup = function () { }
	}
};

function CPageSwitcher(skinName)
{
	this._skinName = skinName;
	this._mainCont = null;
	this._pagesCont = null;
	this._count = 0;
	this._perPage = 0;
	this.PagesCount = 0;
}

CPageSwitcher.prototype = {
	Show: function (page, perPage, count, beginOnclick, endOnclick)
	{
		this.PagesCount = 0;
		if (page == 0) {
			this._mainCont.className = 'wm_inbox_page_switcher';
		}
		else {
			this.Hide();
			this._count = count;
			this._perPage = perPage;
			if (count > perPage) {
				var strPages = '';
				var pagesCount = Math.ceil(count/perPage);
				this.PagesCount = pagesCount;
				if (pagesCount > 4) {
					var firstPage = page - 2;
					if (firstPage < 1) firstPage = 1;
					var lastPage = firstPage + 4;
					if (lastPage > pagesCount) {
						lastPage = pagesCount;
						firstPage = lastPage - 4;
					}
				}
				else {
					var firstPage = 1;
					var lastPage = pagesCount;
				};
				if (firstPage != lastPage) {
					if (firstPage > 1) {
						strPages += '<a href="#" onclick="' + beginOnclick + '1' + endOnclick + ' return false;"><img title="' + Lang.FirstPage + '" style="width: 8px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_first_page.gif" /></a>';
						strPages += '<a href="#" onclick="' + beginOnclick + (firstPage - 1) + endOnclick + ' return false;"><img title="' + Lang.PreviousPage + '" style="width: 5px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_prev_page.gif" /></a>';
					}
					for (var i = firstPage; i <= lastPage; i++) {
						if (page == i)
							strPages += '<font>' + i + '</font>';
						else
							strPages += '<a href="#" onclick="' + beginOnclick + i + endOnclick + ' return false;">' + i + '</a>';
					}
					if (pagesCount > lastPage) {
						strPages += '<a href="#" onclick="' + beginOnclick + (lastPage + 1) + endOnclick + ' return false;"><img title="' + Lang.NextPage + '" style="width: 5px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_next_page.gif" /></a>';
						strPages += '<a href="#" onclick="' + beginOnclick + pagesCount + endOnclick + ' return false;"><img title="' + Lang.LastPage + '" style="width: 8px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_last_page.gif" /></a>';
					}
					this._mainCont.className = 'wm_inbox_page_switcher';
					this._pagesCont.innerHTML = strPages;
				}
			}
		}
	},
	
	GetLastPage: function (removeCount, perPage)
	{
		var count = this._count - removeCount;
		if (perPage) this._perPage = perPage;
		var page = Math.ceil(count/this._perPage);
		if (page < 1) page = 1;
		return page;
	},
	
	Hide: function ()
	{
		this._mainCont.className = 'wm_hide';
	},

	Replace: function (obj)
	{
		var oBounds = GetBounds(obj);
		var ps = this._mainCont;
		ps.style.top = (oBounds.Top - ps.offsetHeight) + 'px';
		ps.style.left = (oBounds.Left + oBounds.Width - ps.offsetWidth - 18) + 'px';
	},
	
	ChangeSkin: function (skinName)
	{
		this._skinName = skinName;
	},
	
	Build: function ()
	{
		var tbl = CreateChild(document.body, 'table');
		this._mainCont = tbl;
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		var div = CreateChild(td, 'div');
		div.className = 'wm_inbox_page_switcher_left';
		td = tr.insertCell(1);
		this._pagesCont = td;
		td.className = 'wm_inbox_page_switcher_pages';
		td = tr.insertCell(2);
		div = CreateChild(td, 'div');
		div.className = 'wm_inbox_page_switcher_right';
	}
};

function CLanguageChanger()
{
	this._innerHTML = Array();
	this._iCount = 0;
	this._value = Array();
	this._vCount = 0;
	this._title = Array();
	this._tCount = 0;
}

CLanguageChanger.prototype = {
	Register: function (type, obj, field, end, start, number)
	{
		if (!start) start = '';
		switch (type) {
			case 'innerHTML':
				if (!number) {
					number = this._iCount;
					this._iCount++;
				};
				this._innerHTML[number] = {Elem: obj, Field: field, End: end, Start: start};
				return number;
			break;
			case 'value':
				if (!number) {
					number = this._vCount;
					this._vCount++;
				};
				this._value[number] = {Elem: obj, Field: field, End: end, Start: start};
				return number;
			break;
			case 'title':
				if (!number) {
					number = this._tCount;
					this._tCount++;
				};
				this._title[number] = {Elem: obj, Field: field, End: end, Start: start};
				return number;
			break;
		}
	},
	
	Go: function ()
	{
		var i = 0;
		var iCount = this._innerHTML.length;
		for (i=0; i<iCount; i++) {
			var obj = this._innerHTML[i];
			if (obj) obj.Elem.innerHTML = obj.Start + Lang[obj.Field] + obj.End;
		};

		iCount = this._value.length;
		for (i=0; i<iCount; i++) {
			var obj = this._value[i];
			if (obj) obj.Elem.value = Lang[obj.Field] + obj.End;
		};

		iCount = this._title.length;
		for (i=0; i<iCount; i++) {
			var obj = this._title[i];
			if (obj) obj.Elem.title = Lang[obj.Field] + obj.End;
		}
	}
};

function CMessagePicturesController(showPicturesHandler, parent)
{
	this._showPicturesHandler = showPicturesHandler;
	this._parent = parent;
	this._fromAddr = '';
	this._safety = 0;
	
	this._showPicturesText = null;
	this._showPicturesTbl = null;
}

CMessagePicturesController.prototype =
{
	SetSafety: function (safety)
	{
		this._safety = safety;
		if (this._safety == 2) {
			this._showPicturesText.className = 'wm_hide';
		}
		else {
			this._showPicturesText.className = '';
		}
	},
	
	SetFromAddr: function (fromAddr)
	{
		this._fromAddr = fromAddr;
		if (this._fromAddr.length > 0) {
			this._showAlwaysPicturesText.className = '';
		}
		else {
			this._showAlwaysPicturesText.className = 'wm_hide';
		}
	},
	
	Show: function ()
	{
		this._showPicturesTbl.className = 'wm_view_message';
		if (this._safety == 2) {
			this._showPicturesText.className = 'wm_hide';
		}
		else {
			this._showPicturesText.className = '';
		};
		if (this._fromAddr.length > 0) {
			this._showAlwaysPicturesText.className = '';
		}
		else {
			this._showAlwaysPicturesText.className = 'wm_hide';
		}
	},
	
	Hide: function ()
	{
		this._showPicturesTbl.className = 'wm_hide';
	},
	
	ShowPictures: function ()
	{
		if (this._fromAddr.length > 0) {
			this._showPicturesText.className = 'wm_hide';
		}
		else {
			this.Hide();
		};
		this._showPicturesHandler.call(this._parent);
		if (this._parent.Id != SCREEN_NEW_MESSAGE) {
			var msg = this._parent._msgObj;
			WebMail.DataSource.Cache.SetMessageSafety(msg.Id, msg.Uid, msg.FolderId, msg.FolderFullName, 2);
		}
	},
	
	ShowPicturesFromSender: function ()
	{
		var xml = '<param name="safety" value="1"/>';
		xml += '<param name="sender">' + GetCData(HtmlDecode(this._fromAddr)) + '</param>';
		RequestHandler('set', 'sender', xml);
		this.Hide();
		this._showPicturesHandler.call(this._parent);
		if (this._parent.Id != SCREEN_NEW_MESSAGE) {
			var msg = this._parent._msgObj;
			WebMail.DataSource.Cache.SetSenderSafety(msg.FromAddr, 1);
		}
	},
	
	Build: function (container)
	{
		var obj = this;
		var tbl = CreateChild(container, 'table');
		this._showPicturesTbl = tbl;
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_safety_info';
		var span = CreateChild(td, 'span');
		this._showPicturesText = span;
		var text = CreateChild(span, 'span');
		text.innerHTML = Lang.PicturesBlocked + '&nbsp;';
		WebMail.LangChanger.Register('innerHTML', text, 'PicturesBlocked', '&nbsp;');
		var a = CreateChild(span, 'a');
		a.innerHTML = Lang.ShowPictures;
		a.href = '#';
		a.onclick = function () {
			obj.ShowPictures();
			return false;
		};
		WebMail.LangChanger.Register('innerHTML', a, 'ShowPictures', '');
		text = CreateTextChild(span, '. ');
		span = CreateChild(td, 'span');
		a = CreateChild(span, 'a');
		a.innerHTML = Lang.ShowPicturesFromSender;
		a.href = '#';
		a.onclick = function () {
			obj.ShowPicturesFromSender();
			return false;
		};
		WebMail.LangChanger.Register('innerHTML', a, 'ShowPicturesFromSender', '');
		text = CreateTextChild(span, '.');
		this._showAlwaysPicturesText = span;
		return tbl;
	}
};

/* Handlers */
function SetHistoryHandler(args)
{
	args = WebMail.CheckHistoryObject(args);
	if (null != args) {
		HistoryStorage.AddStep({FunctionName: 'WebMail.RestoreFromHistory', Args: args});
	}
}

/**********************************************/
function DblClickHandler()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen) {
		if (screen.Id == SCREEN_MESSAGES_LIST_VIEW || screen.Id == SCREEN_MESSAGES_LIST) {
			var msg = new CMessage();
			msg.GetFromIdForList(screen._SEPARATOR, this.id);
			if (screen.IsDrafts()) {
				var screenId = SCREEN_NEW_MESSAGE;
				var parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_UNMODIFIED_PLAIN_TEXT, PART_MESSAGE_ATTACHMENTS];
				var fromDrafts = true;
			}
			else {
				var screenId = SCREEN_VIEW_MESSAGE;
				var parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_ATTACHMENTS];
				var fromDrafts = false;
			}
			SetHistoryHandler(
				{
					ScreenId: screenId,
					FromDrafts: fromDrafts,
					MsgId: msg.Id,
					MsgUid: msg.Uid,
					MsgFolderId: msg.FolderId,
					MsgFolderFullName: msg.FolderFullName,
					MsgCharset: msg.Charset,
					MsgParts: parts
				}
			);
		}
	}
}

function ClickMessageHandler(id)
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen) {
		if (screen.Id == SCREEN_MESSAGES_LIST_VIEW) {
			screen._needPlain = false;
			var msg = new CMessage();
			msg.GetFromIdForList(screen._SEPARATOR, id);
			if (null == screen._msgObj || msg.Id != screen._msgObj.Id || msg.Uid != screen._msgObj.Uid ||
			 msg.FolderId != screen._msgObj.FolderId || msg.FolderFullName != screen._msgObj.FolderFullName ||
			 msg.Charset != screen._msgObj.Charset) {
				screen.CleanMessageBody(false);
				var parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_ATTACHMENTS];
				if (screen.IsDrafts()) {
					parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_UNMODIFIED_PLAIN_TEXT, PART_MESSAGE_ATTACHMENTS]
				}
				var args = {
					ScreenId: screen.Id,
					FolderId: screen._folderId,
					FolderFullName: screen._folderFullName,
					Page: screen._page,
					SortField: screen._sortField,
					SortOrder: screen._sortOrder,
					LookForStr: screen._lookForStr,
					SearchMode: screen._searchMode,
					RedrawType: REDRAW_NOTHING,
					RedrawObj: null,
					MsgId: msg.Id,
					MsgUid: msg.Uid,
					MsgFolderId: msg.FolderId,
					MsgFolderFullName: msg.FolderFullName,
					MsgCharset: msg.Charset,
					MsgParts: parts
				};
				var check = WebMail.CheckHistoryObject(args, true);
				if (check != null) {
					SetHistoryHandler(args);
				}
				else if (null == screen._msgObj) {
					GetMessageHandler(args.MsgId, args.MsgUid, args.MsgFolderId, args.MsgFolderFullName, args.MsgParts, args.MsgCharset);
				}
			}
		}
	}
}

function GetPageMessagesHandler(page)
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen && (screen.Id == SCREEN_MESSAGES_LIST_VIEW || screen.Id == SCREEN_MESSAGES_LIST)) {
		screen.GetPage(page);
	}
}

function SortMessagesHandler()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen && (screen.Id == SCREEN_MESSAGES_LIST_VIEW || screen.Id == SCREEN_MESSAGES_LIST)) {
		SetHistoryHandler(
			{
				ScreenId: screen.Id,
				FolderId: screen._folderId,
				FolderFullName: screen._folderFullName,
				Page: screen._page,
				SortField: this.SortField,
				SortOrder: this.SortOrder,
				LookForStr: screen._lookForStr,
				SearchMode: screen._searchMode,
				RedrawType: REDRAW_HEADER,
				RedrawObj: null,
				MsgId: null,
				MsgUid: null,
				MsgFolderId: null,
				MsgFolderFullName: null,
				MsgCharset: null,
				MsgParts: null
			}
		);
	}
}

function SortContactsHandler()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen && screen.Id == SCREEN_CONTACTS) {
	    SetHistoryHandler(
		    {
			    ScreenId: SCREEN_CONTACTS,
			    Entity: PART_CONTACTS,
			    Page: screen._page,
				SortField: this.SortField,
				SortOrder: this.SortOrder,
			    SearchIn: screen._searchGroup,
			    LookFor: screen._lookFor
		    }
	    );
	}
}

function ResizeMessagesTab(number)
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen && (screen.Id == SCREEN_MESSAGES_LIST_VIEW || screen.Id == SCREEN_MESSAGES_LIST)) {
		screen._inboxTable.ResizeColumnsWidth(number);
	}
}

function ResizeContactsTab(number)
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen && screen.Id == SCREEN_CONTACTS) {
		screen._contactsTable.ResizeColumnsWidth(number);
	}
}
/**********************************************/
function SetCookieSettingsHandler(hideFolders, horizResizer, vertResizer, mark, reply, columns) {
	var xml = '';
	var iCount = columns.length;
	for (var i=0; i<iCount; i++) {
		xml += '<column id="' + i + '" value="' + columns[i] + '"/>';
	}
	xml = '<columns>' + xml + '</columns>';
	var hf = hideFolders ? '1' : '0';
	WebMail.DataSource.Request({action: 'update', request: 'cookie_settings', hide_folders: hf,
		horiz_resizer: horizResizer, vert_resizer: vertResizer, mark: mark, reply: reply}, xml);
}

function ShowMessagesListHandler() {
	//WebMail.ResizeBody('all');
	WebMail.DataSource.Get(TYPE_ACCOUNTS_LIST, { }, [], '');
}

function GetFoldersListHandler(idAcct, sync) {
	WebMail.DataSource.Get(TYPE_FOLDERS_LIST, { IdAcct: idAcct, Sync: sync }, [], '' );
}

function GetMessagesListHandler(redrawIndex, redrawElement, folderId, folderFullName, sortField, sortOrder, page, lookFor, searchFields) {
	HistoryStorage.Log = '';
	var xml = '<folder id="' + folderId + '"><full_name>' + GetCData(folderFullName) + '</full_name></folder>';
	xml += '<look_for fields="' + searchFields + '">' + GetCData(lookFor) + '</look_for>';
	var screen = WebMail.Screens[WebMail.ListScreenId];
	if (screen) screen.RedrawControls(redrawIndex, redrawElement, sortField, sortOrder, page);
	WebMail.DataSource.Get(TYPE_MESSAGES_LIST, { Page: page, SortField: sortField, SortOrder: sortOrder, 
		FolderId: folderId, FolderFullName: folderFullName, LookFor: lookFor, SearchFields: searchFields }, [], xml );
}

function GetMessageHandler(messageId, messageUid, folderId, folderFullName, messageParts, charset) {
	var screen = WebMail.Screens[WebMail.ListScreenId];
	if (screen && null != screen._selection) {
		var msg = new CMessage();
		msg.Id = messageId;
		msg.Uid = messageUid;
		msg.FolderId = folderId;
		msg.FolderFullName = folderFullName;
		msg.Charset = charset;
		var msgId = msg.GetIdForList(screen._SEPARATOR, screen.Id);
		var readed = screen._selection.SetParams([msgId], 'Read', true, false);
		if (readed != 0) {
			var paramIndex = screen._folderId + screen._folderFullName;
			var params = screen._foldersParam[paramIndex];
			if (params) {
				params.Read(readed);
				WebMail.DataSource.Cache.SetMessagesCount(screen._folderId, screen._folderFullName, params.MsgsCount, params._newMsgsCount);
			}
		}
		screen._selection.CheckLine(msgId);
		if (screen._inboxTable != null && screen._inboxTable._lastClickLineId != msgId) {
			screen._inboxTable._lastClickLineId = msgId;
		}
	};
	charset = charset ? charset : AUTOSELECT_CHARSET;
	var xml = '<param name="uid">' + GetCData(HtmlDecode(messageUid)) + '</param>';
	xml += '<folder id="' + folderId + '"><full_name>' + GetCData(folderFullName) + '</full_name></folder>';
	WebMail.DataSource.Get(TYPE_MESSAGE, {Id: messageId, Charset: charset, Uid: messageUid, FolderId: folderId, FolderFullName: folderFullName}, messageParts, xml );
}

function MoveToFolderHandler(id)
{
	var screenId = WebMail.ScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen && screenId == WebMail.ListScreenId) {
		var folderParams = id.split(screen._SEPARATOR);
		if (2 == folderParams.length)
			RequestMessagesOperationHandler(MOVE_TO_FOLDER, [], 0, folderParams[0], folderParams[1]);
	}
}

function RequestMessagesOperationHandler(type, idArray, fromMenu, toFolderId, toFolderFullName) {
	var screenId = WebMail.ScreenId;
	var screen = WebMail.Screens[screenId];
	if (type != -1 && screenId == WebMail.ListScreenId) {
		var xml = screen.GetXmlMessagesOperation(type, idArray, toFolderId, toFolderFullName);
	}//if (type != -1 && screenId == WebMail.ListScreenId)
}

function GetHandler(type, params, parts, xml) {
	WebMail.DataSource.Get(type, params, parts, xml);
}
/**********************************************/
function SelectScreenHandler(screenId) {
	WebMail.ScreenIdForLoad = screenId;
	ShowScreenHandler();
}

function ShowScreenHandler() {
	WebMail.ShowScreen(ShowScreenHandler);
}

function LoadHandler() {
	WebMail.HideInfo();
	WebMail.DataSource.ParseXML(this.responseXML, this.responseText);
}

function ErrorHandler() {
	WebMail.ShowError(this.ErrorDesc);
}

function InfoHandler() {
	WebMail.ShowInfo(this.Info);
	setTimeout("WebMail.HideInfo();", 10000);
}

function ShowLoadingInfoHandler() {
	WebMail.ShowInfo(Lang.Loading);
}

function TakeDataHandler() {
	if (this.Data) {
		WebMail.PlaceData(this.Data);
	}
}

function RequestHandler(action, request, xml) {
	WebMail.DataSource.Request({action: action, request: request}, xml);
}

function RequestUserSettings(xml) {
	WebMail.DataSource.Request({action: 'update', request: 'settings'}, xml);
}

function RequestAccountProperties(xml) {
	WebMail.DataSource.Request({action: 'update', request: 'account'}, xml);
}

function RequestAddAccountProperties(xml) {
	WebMail.DataSource.Request({action: 'new', request: 'account'}, xml);
}

function RemoveAccountHandle(id) {
	WebMail.DataSource.Request({action: 'delete', request: 'account', 'id_acct': id}, '');
}

function ResizeBodyHandler() {
	if (WebMail) {
		WebMail.ResizeBody('all');
	}
}

function ClickBodyHandler(ev) {
	if (WebMail) {
		WebMail.ClickBody(ev);
	}
}

/* html editor handlers */
function EditAreaLoadHandler() {
	if (WebMail)
		WebMail.LoadEditArea();
}

function CreateLinkHandler(url) {
	WebMail.CreateLink(url);
}

function DesignModeOnHandler(mode) {
	WebMail.DesignModeOn(mode);
}
/*-- html editor handlers */

function LoadAttachmentHandler(attachment) {
	WebMail.LoadAttachment(attachment);
}

function ImportContactsHandler(code, count) {
	switch (code) {
		case 0:
			this.ErrorDesc = Lang.ErrorImportContacts;
			ErrorHandler();
			break;
		case 2:
			count = 0;
		case 1:
			WebMail.ContactsImported(count);
			break;
		case 3:
			this.ErrorDesc = Lang.ErrorInvalidCSV;
			ErrorHandler();
			break;
	}
}

/* check mail handlers */
function SetStateTextHandler(text) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.SetText(text);
	}
}

function SetCheckingFolderHandler(folder, count) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.SetFolder(folder, count);
	}
}

function SetRetrievingMessageHandler(number) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.SetMsgNumber(number);
	}
}

function SetDeletingMessageHandler(number) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.DeleteMsg(number);
	}
}

function EndCheckMailHandler(error) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.End();
		GetFoldersListHandler(WebMail.Accounts.CurrId, 1);
	}
	if (error.length > 0) {
		if (error == 'session_error') {
			document.location = LoginUrl + '?error=1';
		}
		else {
			this.ErrorDesc = error;
			ErrorHandler.call(this);
		}
	}
}

function CheckEndCheckMailHandler() {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen && screen._checkMail.started) {
		screen._checkMail.End();
		GetFoldersListHandler(WebMail.Accounts.CurrId, 1);
		this.ErrorDesc = Lang.ErrorCheckMail;
		ErrorHandler.call(this);
	}
}
/*-- check mail handlers */

/* auto filling handlers */
function GetAutoFillingContactsHandler()
{
	var contactsGroups = new CContacts();
	contactsGroups.LookFor = this.Keyword;
	contactsGroups.SearchType = 1;
	GetHandler(TYPE_CONTACTS, 
	{
		Page: 1,
		SortField: SORT_FIELD_USE_FREQ,
		SortOrder: SORT_ORDER_ASC,
		IdGroup: -1,
		LookFor: this.Keyword
	}, [], contactsGroups.GetInXml());
}

function SelectSuggestionHandler()
{
	if (this.ContactGroup.IsGroup) {
		var screen = WebMail.Screens[SCREEN_NEW_MESSAGE];
		if (screen) {
			screen.AddSenderGroup(this.ContactGroup.Id);
		}
	}
}
/*-- auto filling handlers */

/* backsave timer handler */
function TTick()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen && screen.Id == SCREEN_NEW_MESSAGE)
	{
		screen.TimerTick();
	}
}
/*-- backsave timer handler */

function DisplayCalendarHandler()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen && screen.Id == SCREEN_CALENDAR)
	{
		screen.Display();
	}
}