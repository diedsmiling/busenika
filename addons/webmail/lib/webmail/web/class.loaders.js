/*
Classes:
	CScriptLoader
	CNetLoader
	CDictionary
*/

function CScriptLoader()
{
	this.onLoad = null;
	this.loadedCount = 0;
	this.scriptsCount = 0;
	this._onLoad = null;
	this._scripts = new CDictionary();
}

CScriptLoader.prototype = {
	Load: function(urlArray, loadHandler)
	{
		this.onLoad = loadHandler;
		this.loadedCount = 0;
		this.scriptsCount = urlArray.length;
		if (this.scriptsCount == 0)
			this.onLoad.call();
		for (var i in urlArray)
			this.LoadItem(urlArray[i], this.ScriptLoadHandler);
	},
	
	ScriptLoadHandler: function()
	{
		this.loadedCount++;
		if (this.loadedCount == this.scriptsCount)
			this.onLoad.call();
	},
	
	LoadItem: function(url, loadHandler)
	{
		this._onLoad = loadHandler;
		var script = document.createElement('script');
		script.setAttribute('type', 'text/javascript');
		var obj = this;
		if (Browser.IE) {
			script.onreadystatechange = function ()
			{
				if (obj._scripts.exists(this.src)) {
					if (this.readyState == 'complete' || this.readyState == 'loaded') {
						obj._scripts.remove(this.src)
						obj._onLoad.call(obj);
					}
				}
			};
		} else {
			script.onload = function ()
			{
				obj._scripts.remove(this.src)
				obj._onLoad.call(obj);
			}
		}
		var src = url + '?x=' + Math.random();
		this._scripts.add(src, true);
		script.src = src;
		var HeadElements = document.getElementsByTagName('head');
		HeadElements[0].appendChild(script);
	}
}

function CNetLoader()
{
		this.Url = null;
		this.onLoad = null;
		this.onError = null;
		this.responseXML = null;
		this.responseText = null;
		this.ErrorDesc = null;
		this.Request = null;
		this.Log = '';
}

CNetLoader.prototype = {
	GetTransport: function()
	{
		var transport = null;
		if(window.XMLHttpRequest) {
			transport = new XMLHttpRequest();
		} else {
			if(window.ActiveXObject) {
				try
				{
					transport = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch (err)
				{
					try
					{
						transport = new ActiveXObject("Microsoft.XMLHTTP");
					}
					catch (err2)
					{
					}
				}
			}
		}
		return transport;
	},

	LoadXMLDoc: function(Url, PostParams, onLoad, onError)
	{
		this.Url = Url;
		this.onLoad = onLoad;
		this.onError = onError;
		var Request = this.GetTransport();

		if(Request)
		{
			try
			{
				Request.open('POST', this.Url, true);
				Request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				var obj = this;
				Request.onreadystatechange = function() { obj.OnReadyState(Request); }
				Request.send(PostParams);
			}
			catch (err)
			{
				this.ErrorDesc = Lang.ErrorRequestFailed;
				this.onError.call(this);
			}
		} else {
			this.ErrorDesc = Lang.ErrorAbsentXMLHttpRequest;
			this.onError.call(this);
		}
		this.Request = Request;
	},
	
	OnReadyState: function(Request)
	{
		var Ready = Request.readyState;
		if(Ready == READY_STATE_COMPLETE)
		{
			var HttpStatus;
			try {
				if(Request.status != undefined) {
					HttpStatus = Request.status;
				} else {
					HttpStatus = 13030;
				}
			} catch(e) {
				// 13030 is the custom code to indicate the condition -- in Mozilla/FF --
				// when the o object's status and statusText properties are
				// unavailable, and a query attempt throws an exception.
				HttpStatus = 13030;
			}
			if(HttpStatus == 200 || HttpStatus == 0) {
				this.responseXML = Request.responseXML;
				this.responseText = Request.responseText;
				this.onLoad.call(this);
			} else if (HttpStatus != 13030) {
				this.ErrorDesc = Lang.ErrorConnectionFailed + '\n' + HttpStatus + '\n' + Request.responseText;
				this.onError.call(this);
			}
		}
	},
	
	CheckRequest: function ()
	{
		if (null != this.Request) {
			this.Request.onreadystatechange = null;
			this.Request.abort();
		}
	}
}

function CDictionary(){
  this.count = 0;
  this.Obj = new Object();
}

CDictionary.prototype = {
	exists: function(sKey){
		return (this.Obj[sKey])?true:false;
	},

	add: function(sKey, aVal){
		var K = String(sKey);
		if(this.exists(K)) return false;
		this.Obj[K] = aVal;
		this.count++;
		return true;
	},

	remove: function(sKey){
		var K = String(sKey);
		if(!this.exists(K)) return false;
		delete this.Obj[K];
		this.count--;
		return true;
	},

	removeAll: function(){
		for(var key in this.Obj) delete this.Obj[key];
		this.count = 0;
	},

	values: function(){
		var Arr = new Array();
		for(var key in this.Obj) Arr[Arr.length] = this.Obj[key];
		return Arr;
	},

	keys: function(){
		var Arr = new Array();
		for(var key in this.Obj) Arr[Arr.length] = key;
		return Arr;
	},

	items: function(){
		var Arr = new Array();
		for(var key in this.Obj){
			var A = new Array(key,this.Obj[key]);
			Arr[Arr.length] = A;
		}
		return Arr;
	},

	getVal: function(sKey){
		var K = String(sKey);
		return this.Obj[K];
	},

	setVal: function(sKey,aVal){
		var K = String(sKey);
		if(this.exists(K))
		this.Obj[K] = aVal;
		else
		this.add(K,aVal);
	},

	setKey: function(sKey,sNewKey){
		var K = String(sKey);
		var Nk = String(sNewKey);
		if(this.exists(K)){
			if(!this.exists(Nk)){
				this.add(Nk,this.getVal(K));
				this.remove(K);
			}
		}
		else if(!this.exists(Nk)) this.add(Nk,null);
	}
}
