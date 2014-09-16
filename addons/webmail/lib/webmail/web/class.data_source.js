/*
Classes:
	CDataType
	CDataSource
	CCache
*/

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
	this._SEPARATOR = '@$%';

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
	for (Key in DataTypes)
	{
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
		}

		var arDataKeys = [];
		for(Key in objDataKeys) { arDataKeys.push( objDataKeys[Key] ); }
		if (Caching) {
			var StringDataKeys = DataType.GetRequest + this._SEPARATOR + arDataKeys.join(this._SEPARATOR);
		} else {
			var StringDataKeys = DataType.GetRequest;
		}

		this.Data = null;
		if (Caching && Cache.ExistsData( intDataType, StringDataKeys )) {// there is in the cache!
			this.Data = Cache.GetData( intDataType, StringDataKeys );
			if (CacheByParts) {
				Mode = (Mode | this.Data.Parts) ^ this.Data.Parts;
			}
		}

		if (!(Caching && Cache.ExistsData( intDataType, StringDataKeys )) || (CacheByParts && (Mode != 0))) {
			var Url = this.ActionUrl;
			var arParams = [];
			arParams['action'] = 'get';
			arParams['request'] = DataType.GetRequest;
			if (CacheByParts) arParams['mode'] = Mode;
			var objRequestParams = DataType.RequestParams;
			for(var Param in objRequestParams)
			{
				arParams[objRequestParams[Param]] = objDataKeys[Param];
			}
			var XMLParams = this.GetXML(arParams, xml);
			this.onRequest.call(this);
			//alert(XMLParams);//
			this.NetLoader.LoadXMLDoc( Url, 'xml=' + encodeURIComponent(XMLParams), this.onLoad, this.onError );
		} else {
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
		for(var ParamName in arParams)
		{
			strResult += '<param name="' + ParamName + '" value="' + encodeURIComponent(arParams[ParamName]) + '"/>';
		}
		strResult = '<?xml version="1.0" encoding="utf-8"?><webmail>' + strResult + xml + '</webmail>';
		return strResult;
	},
	
	ParseXML: function(XmlDoc, TextDoc)
	{
		if (XmlDoc && XmlDoc.documentElement && typeof(XmlDoc) == 'object' && typeof(XmlDoc.documentElement) == 'object')
		{
			var RootElement = XmlDoc.documentElement;
			if (RootElement && RootElement.tagName == 'webmail') {
				var Objects = RootElement.childNodes;
				if ( Objects.length == 0 ) {
					this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 4.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
					this.onError.call(this);
				} else {
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
									this.Info = Info;
									this.onInfo.call(this);
								}
								break;
							case 'error':
								var attr = Objects[key].getAttribute('code');
								if (attr) {
									document.location = LoginUrl + '?error=' + attr;
								} else {
									var ErrorDesc = Objects[key].childNodes[0].nodeValue;
									if (ErrorDesc && ErrorDesc.length > 0) {
										this.ErrorDesc = ErrorDesc;
									} else {
										this.ErrorDesc = Lang.ErrorWithoutDesc;
									}
									this.onError.call(this);
								}
								break;
							case 'session_error':
								document.location = LoginUrl + '?error=1';
							break;
						}//switch (ObjectName)
					}//for
					if (isObject == true) {
						if (this.Data && ObjectXML) {
							var Cache = this.Cache;
							var intDataType = this.Data.Type;
							var DataType = this.DataTypes[intDataType]
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
									} else {
										Cache.AddData(intDataType, StringDataKeys, this.Data);
									}
								}
								if (this.Data.Type == TYPE_MESSAGE) {
									this.Set([[this.Data.Id], this.Data.FolderId, this.Data.FolderFullName], 'Read', true);
								}
							} else {
								this.Data.GetFromXML(ObjectXML);
							}
							this.onGet.call(this);
						} else {
							this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 3.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
							this.onError.call(this);
						}
					}//if (isObject == true)
				}// if (Objects.length == 0)
			} else {
				this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 2.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
				this.onError.call(this);
			}//if (RootElement.tagName == 'webmail')
		} else {
			this.ErrorDesc = Lang.ErrorParsing + '<br/>Error code 1.<br/>' + Lang.ResponseText + '<br/>' + TextDoc;
			this.onError.call(this);
		}//if (RootElement)
	}
}

function CCache(DataTypes)
{
	this.DataTypes = [];
	this.Dictionaries = [];
	for(a in DataTypes)
	{
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
		} else {
			return false;
		}
	},

	AddData: function(DataType, Key, Value)
	{
		if (this.Dictionaries[DataType].count >= this.DataTypes[DataType].CacheLimit) {
			var Keys = this.Dictionaries[DataType].keys();
			this.Dictionaries[DataType].remove(Keys[0]);
		}
		this.Dictionaries[DataType].add( Key, Value );
	},
	
	SetMessageSafety: function(msgId, msgUid, folderId, folderFullName, safety, isAll)
	{
		var dict = this.Dictionaries[TYPE_MESSAGE];
		var keys = dict.keys();
		for (var i in keys) {
			var msg = dict.getVal(keys[i]);
			if (isAll || msg.Id == msgId && msg.Uid == msgUid && msg.FolderId == folderId && msg.FolderFullName == folderFullName) {
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
		for (var i in keys)
		{
			var msg = dict.getVal(keys[i]);
			var fParts = GetEmailParts(HtmlDecode(msg.FromAddr));
			var fEmail = fParts.Email;
			if (fromEmail == fEmail)
			{
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
				messages.MessagesCount = count;
				messages.NewMsgsCount = countNew;
				dict.setVal(keys[i], messages);
			}
		}
	},
	
	ClearMessagesList: function(folderId, folderFullName, byFlag)
	{
		var dict = this.Dictionaries[TYPE_MESSAGES_LIST];
		var keys = dict.keys();
		if (folderId == '-1' && folderFullName == '') {
			dict.removeAll();
		} else {
			for (var i in keys) {
				var messages = dict.getVal(keys[i]);
				var remove = true;
				if (byFlag && messages.SortField != SORT_FIELD_FLAG) {
					remove = false;
				}
				if (remove && messages.FolderId == folderId && messages.FolderFullName == folderFullName ||
					messages.FolderId == '-1' && messages.FolderFullName == '') {
						dict.remove(keys[i]);
				}
			}
		}
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
		}
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
					} else {
						for (var k in idArray) {
							if (data.Id == idArray[k].Id && data.Uid == idArray[k].Uid && data.FolderId == messageParams[1] && data.FolderFullName == messageParams[2]) {
								data[field] = value;
								messages.List[j] = data;
							}
						}
					}
				}
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
}