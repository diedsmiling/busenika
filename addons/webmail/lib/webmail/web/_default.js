var WebMail;
var LoginScreen;
var ScriptLoader;
var infoObj, infoMessage;

var Browser = new CBrowser();
if (!Browser.Allowed)
	isAjax = false;

var NetLoader = new CNetLoader();
var transport = NetLoader.GetTransport();
if (!transport)
	isAjax = false;

var checkMail = new CCheckMail(1);

function AjaxInit()
{
	infoObj = document.getElementById('info');
	infoMessage = document.getElementById('info_message');
	LoginScreen.AjaxInit(TryLoginHandler);
}

function Init()
{
	LoginScreen = new CLoginScreen();
	if (isAjax) {
		ScriptLoader = new CScriptLoader();
		ScriptLoader.Load(Sections[SECTION_LOGIN].Scripts, AjaxInit);
	}
}

function TryLoginHandler() {
	infoObj.className = 'wm_information';
	infoMessage.innerHTML = Lang.Loading;
	NetLoader.LoadXMLDoc(ActionUrl, 'xml=' + encodeURIComponent(this.Xml), LoginHandler, LoginErrorHandler);
}

function LoginErrorHandler() {
	infoObj.className = 'wm_hide';
	LoginScreen.ShowError(this.ErrorDesc);
}

function LoginHandler() {
	infoObj.className = 'wm_hide';
	var XmlDoc = this.responseXML;
	if (XmlDoc && XmlDoc.documentElement && typeof(XmlDoc) == 'object' && typeof(XmlDoc.documentElement) == 'object')
	{
		var RootElement = XmlDoc.documentElement;
		if (RootElement && RootElement.tagName == 'webmail') {
			var ErrorTag = RootElement.getElementsByTagName('error')[0];
			if (ErrorTag) {
				var ErrorDesc = ErrorTag.childNodes[0].nodeValue;
				if (ErrorDesc && ErrorDesc.length > 0) {
					LoginScreen.ShowError(ErrorDesc);
				} else {
					LoginScreen.ShowError(Lang.ErrorWithoutDesc);
				}
			} else {
				var Objects = RootElement.childNodes;
				if ( Objects.length == 0 ) {
					LoginScreen.ShowError(Lang.ErrorEmptyXmlPacket);
				} else {
					var iCount = Objects.length;
					for (var i=iCount-1; i>=0; i--) {
						if (Objects[i].tagName == 'login') {
							hash = '';
							id = -1;
							var attr = Objects[i].getAttribute('id_acct'); if (attr) id = attr - 0;
							var hashParts = Objects[i].childNodes;
							var jCount = hashParts.length;
							for (var j = jCount-1; j >= 0; j--) {
								var part = hashParts[j].childNodes;
								if (part.length > 0 && hashParts[j].tagName == 'hash')
									hash = part[0].nodeValue;
							}//for
							if (id != -1 && hash != '') {
								CreateCookie('awm_autologin_data', hash, 14);
								CreateCookie('awm_autologin_id', id, 14);
							}
							checkMail.Start();
						}
					}//for
				}
			}//if (ErrorTag)
		} else {
			LoginScreen.ShowError('2. '+ Lang.ErrorParsing + this.responseText);
		}//if (RootElement)
	} else {
		LoginScreen.ShowError('1. '+ Lang.ErrorParsing + this.responseText);
	}//if (XmlDoc)
}

function SetCheckingAccountHandler(accountName)
{
	LoginScreen.Hide();
	checkMail.SetAccount(accountName);
}

function SetStateTextHandler(text) {
	checkMail.SetText(text);
}

function SetCheckingFolderHandler(folder, count) {
	checkMail.SetFolder(folder, count);
}

function SetRetrievingMessageHandler(number) {
	checkMail.SetMsgNumber(number);
}

function SetDeletingMessageHandler(number) {
	checkMail.DeleteMsg(number);
}

function EndCheckMailHandler(error) {
	if (error == 'session_error') {
		document.location = LoginUrl + '?error=1';
	} else {
		document.location = WebMailUrl;
	}
}

function CheckEndCheckMailHandler() {
	if (checkMail.started) {
		document.location = WebMailUrl;
	}
}