function CHistoryStorage(SettingsStorage)
{
	// this for checking HistoryStorage can working
	this.Ready = false;
	// errors list
	this.Errors = [];
	// save input data
	if(SettingsStorage) {
		this.InputSettings = SettingsStorage;
	}
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
	this.Log = '';
	
	this._historyKey = null;
	this._historyObjectName = null;
	this._form = null;
	this.iframe = null;
	
	this._iframe = null;

	// execute initialization
	this.Initialize();
}

CHistoryStorage.prototype = {
	AddError: function(StrError){
		if(this.Errors.length >= this._MaxErrorListLength)
		{
			this.Errors.reverse().pop();
			this.Errors.reverse();
		}
		this.Errors[this.Errors.length] = StrError;
	},

	Initialize: function(){
		this.Ready = true;
		if (typeof(this.InputSettings.Document) == 'object' && this.InputSettings.Document != null) {
			this.Document = this.InputSettings.Document;
		} else {
			this.Ready = false;
		}
		if (typeof(this.InputSettings.Browser) == 'object' && this.InputSettings.Browser != null) {
			this.Browser = this.InputSettings.Browser;
		} else {
			this.Ready = false;
		}
		if (typeof(this.InputSettings.HistoryStorageObjectName) == 'string') {
			this.HistoryStorageObjectName = this.InputSettings.HistoryStorageObjectName;
		} else {
			this.Ready = false;
		}
		if (typeof(this.InputSettings.PathToPageInIframe) == 'string') {
			this.PathToPageInIframe = this.InputSettings.PathToPageInIframe;
		} else {
			this.Ready = false;
		}

		var _tempLimit = parseInt(this.InputSettings.MaxLimitSteps);
		if(isNaN(_tempLimit)) {
			this.AddError('The maximum number of steps that you specified is invalid. Default value 15 assigned.');
			_tempLimit = this._DefaultMaxLimitSteps;
		} else {
			if(_tempLimit < 1) {
				this.AddError('The maximum number of steps that you specified is invalid. Default value 15 assigned.');
				_tempLimit = this._DefaultMaxLimitSteps;
			}
		}
		this.MaxLimitSteps = _tempLimit;

		this.iframe = CreateChildWithAttrs(document.body, 'iframe', [['id', 'HistoryStorageIframe'], ['name', 'HistoryStorageIframe'], ['src', EmptyHtmlUrl], ['class', 'wm_hide']]);
		var frm = CreateChildWithAttrs(document.body, 'form', [['action', this.PathToPageInIframe], ['target', 'HistoryStorageIframe'], ['method', 'post'], ['id', 'HistoryForm'], ['name', 'HistoryForm'], ['class', 'wm_hide']]);
		this._historyKey = CreateChildWithAttrs(frm, 'input', [['type', 'text'], ['name', 'HistoryKey']]);
		this._historyObjectName = CreateChildWithAttrs(frm, 'input', [['type', 'text'], ['name', 'HistoryStorageObjectName']]);
		this._form = frm;
		/*with (iframe.style) {
			position = 'absolute';
			top = '0px';
			left = '0px';
			width = '500px';
			height = '500px';
			zIndex = '5';
		}*/
	},

	ProcessHistory: function(HistoryKey) {
		this.InStep = false;
		if (this.KeysInStep[HistoryKey]) {
			delete this.KeysInStep[HistoryKey];
		} else {
			this.RestoreFromHistory(HistoryKey);
		}
	},
	
	RestoreFromHistory: function (HistoryKey) {
		if(this.Dictionary.exists(HistoryKey)) {
			var HistoryObject = this.Dictionary.getVal(HistoryKey);
			var args = HistoryObject.Args;
			this.Log += args.IdAcct+', '+args.ScreenId+', '+args.MsgFolderId+', '+args.MsgFolderFullName+', '+args.MsgId+', '+args.MsgUid+'\n';
			var ExecuteCommand = 'window.' + HistoryObject.FunctionName + '(HistoryObject.Args)';
			eval(ExecuteCommand);
		} else {
			this.AddError('The specified key doesn\'t exists in history storage');
		}

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
		}
		//add new step
		this.Dictionary.add( newKey, ObjectData );

		if (this.InStep) {
			//move step key to Queue because previouse step still not finished
			this.Queue.push(newKey);
		} else {
			//realize step
			this.DoStep(newKey);
		}
	},
	
	DoStep: function (newKey) {
		if (Browser.Mozilla) {
			WebMail.DataSource.NetLoader.CheckRequest();
			WebMail.HideInfo();
		}
		if(this.Ready && !this.Browser.Opera) {
			if (this.KeysInStep[this.PrevKey]) {
				delete this.KeysInStep[this.PrevKey];
			}
			this._historyKey.value = newKey;
			this._historyObjectName.value = this.HistoryStorageObjectName;
			this._form.action = this.PathToPageInIframe + '?param=' + Math.random();
			this._form.submit();
			this.KeysInStep[newKey] = true;
			this.PrevKey = newKey;
			//this.InStep = true;
			this.RestoreFromHistory(newKey);
		} else {
			this.RestoreFromHistory(newKey);
			this.AddError('Couldn\'t processing action. See Errors list for details.');
		}
	}
}
