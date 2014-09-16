<?php
	define('PROC_ERROR_ACCT_CREATE', 'Hiba a felhasználói fiók létrehozása közben!');
	define('PROC_WRONG_ACCT_PWD', 'Hibás jelszó');
	define('PROC_CANT_LOG_NONDEF', 'Nem lehetséges a belépés a nem szabványos fiókba');
	define('PROC_CANT_INS_NEW_FILTER', 'Nem lehet új szűrőt létrehozni');
	define('PROC_FOLDER_EXIST', 'A könyvtár már létezik');
	define('PROC_CANT_CREATE_FLD', 'Nem lehet könyvtárat létrehozni');
	define('PROC_CANT_INS_NEW_GROUP', 'Nem lehet új csoportot létrehozni');
	define('PROC_CANT_INS_NEW_CONT', 'Nem lehet új névjegyet létrehozni');
	define('PROC_CANT_INS_NEW_CONTS', 'Nem lehet új névjegye(ke)t létrehozni');
	define('PROC_CANT_ADD_NEW_CONT_TO_GRP', 'Nem lehet a névjegyeket a csoportba helyezni');
	define('PROC_ERROR_ACCT_UPDATE', 'Hiba a fiók frissítése közben');
	define('PROC_CANT_UPDATE_CONT_SETTINGS', 'Nem lehet frissíteni a névjegyek beállításait');
	define('PROC_CANT_GET_SETTINGS', 'Nem lehet a beállításokat lekérni');
	define('PROC_CANT_UPDATE_ACCT', 'Nem lehet frissíteni a fiókot');
	define('PROC_ERROR_DEL_FLD', 'Hiba a mappa(ák) törlése közben');
	define('PROC_CANT_UPDATE_CONT', 'Nem lehet frissíteni a névjegyet');
	define('PROC_CANT_GET_FLDS', 'Nem lehet kiolvasni a mappák listáját');
	define('PROC_CANT_GET_MSG_LIST', 'Nem lehet lekérni az üzenetek listáját');
	define('PROC_MSG_HAS_DELETED', 'Ez az üzenet már törölve lett a szerverről');
	define('PROC_CANT_LOAD_CONT_SETTINGS', 'Nem lehet betölteni a névjegyek beállítását');
	define('PROC_CANT_LOAD_SIGNATURE', 'Nem lehet betölteni az aláírást');
	define('PROC_CANT_GET_CONT_FROM_DB', 'Nem lehet lekérdezi a névjegyet az adatbázisból');
	define('PROC_CANT_GET_CONTS_FROM_DB', 'Nem lehet lekérdezni a névjegye(ke)t az adatbázisból');
	define('PROC_CANT_DEL_ACCT_BY_ID', 'Nem lehet törölni a fiókot');
	define('PROC_CANT_DEL_FILTER_BY_ID', 'Nem lehet törölni a szűrőt');
	define('PROC_CANT_DEL_CONT_GROUPS', 'Nem lehet törölni a névjegye(ke)t és/vagy a csoportokat');
	define('PROC_WRONG_ACCT_ACCESS', 'Azonosítatlan hozzáféési kísérlet észlelve a fiókhoz.');
	define('PROC_SESSION_ERROR', 'Az előző munkamenet megszakítva időtúllépés miatt.');

	define('MailBoxIsFull', 'A postafiók megtelt.');
	define('WebMailException', 'WebMail kivételes hiba történt');
	define('InvalidUid', 'Érvénytelen üzenet azonosító');
	define('CantCreateContactGroup', 'Nem lehet létrehozni a csoportot');
	define('CantCreateUser', 'Nem lehet a felhasználót létrehozni');
	define('CantCreateAccount', 'Nem lehet a fiókot létrehozni');
	define('SessionIsEmpty', 'A munkamenet üres');
	define('FileIsTooBig', 'Túl nagy méretű fájl');

	define('PROC_CANT_MARK_ALL_MSG_READ', 'Nem lehet az összes üzenetet megjelölni olvasottként');
	define('PROC_CANT_MARK_ALL_MSG_UNREAD', 'Nem lehet az összes üzenetet megjelölni olvasatlanként');
	define('PROC_CANT_PURGE_MSGS', 'Nem lehet véglegesen törölni az üzenete(ke)t');
	define('PROC_CANT_DEL_MSGS', 'Nem lehet törölni az üzenete(ke)t');
	define('PROC_CANT_UNDEL_MSGS', 'Nem lehet visszavonni a törlését az üzenet(ek)nek');
	define('PROC_CANT_MARK_MSGS_READ', 'Nem lehet megjelölni ovasottként az üzenete(ke)t');
	define('PROC_CANT_MARK_MSGS_UNREAD', 'Nem lehet megjelölni olvasatlanként az üzenete(ke)t');
	define('PROC_CANT_SET_MSG_FLAGS', 'Nem lehet beállítani az üzenethez megjelölést');
	define('PROC_CANT_REMOVE_MSG_FLAGS', 'Nem lehet eltávolítani az üzenethez a megjelölést');
	define('PROC_CANT_CHANGE_MSG_FLD', 'Nem lehet az mappát váltani az üzenet(ek)hez');
	define('PROC_CANT_SEND_MSG', 'Nem lehet elküldeni az üzenetet.');
	define('PROC_CANT_SAVE_MSG', 'Nem lehet elmenteni az üzenetet');
	define('PROC_CANT_GET_ACCT_LIST', 'Nem lehet lekérni a mappák listáját');
	define('PROC_CANT_GET_FILTER_LIST', 'Nem lehet lekérni a szűrők listáját');

	define('PROC_CANT_LEAVE_BLANK', 'Nem hagyhatja üresen a *-al jelölt mezőket');
	
	define('PROC_CANT_UPD_FLD', 'Nem lehet frissíteni a mappát');
	define('PROC_CANT_UPD_FILTER', 'Nem lehet frissíteni a szűrőt');

	define('ACCT_CANT_ADD_DEF_ACCT', 'Nem lehet hozzáadni ezt a fiókot, mert másik felhasználhó már használja alapértelmezettként.');
	define('ACCT_CANT_UPD_TO_DEF_ACCT', 'Nem lehet beállítani a fiókot alapértelmezettnek.');
	define('ACCT_CANT_CREATE_IMAP_ACCT', 'Nem lehet új fiókot létrehozni (IMAP4 kapcsolódási hiba)');
	define('ACCT_CANT_DEL_LAST_DEF_ACCT', 'Nem lehet törölni az utolsó alapértelmezett fiókot');

	define('LANG_LoginInfo', 'Belépési információk');
	define('LANG_Email', 'E-mail cím');
	define('LANG_Login', 'Postafiók');
	define('LANG_Password', 'Jelszó');
	define('LANG_IncServer', 'Bejövő Üzenet');
	define('LANG_PopProtocol', 'POP3');
	define('LANG_ImapProtocol', 'IMAP4');
	define('LANG_IncPort', 'Port');
	define('LANG_OutServer', 'SMTP Szerver');
	define('LANG_OutPort', 'Port');
	define('LANG_UseSmtpAuth', 'SMTP hitelesítés használata');
	define('LANG_SignMe', 'Automatikus beléptetés');
	define('LANG_Enter', 'Belépés');

	define('JS_LANG_TitleLogin', 'Belépés');
	define('JS_LANG_TitleMessagesListView', 'Üzenetek listája');
	define('JS_LANG_TitleMessagesList', 'Üzenetek listája');
	define('JS_LANG_TitleViewMessage', 'Üzenet megtekintése');
	define('JS_LANG_TitleNewMessage', 'Új üzenet');
	define('JS_LANG_TitleSettings', 'Beállítások');
	define('JS_LANG_TitleContacts', 'Címjegyzék');

	define('JS_LANG_StandardLogin', 'Egyszerűsített&nbsp;Belépés');
	define('JS_LANG_AdvancedLogin', 'Bővített&nbsp;Belépés');

	define('JS_LANG_InfoWebMailLoading', 'Kérem várjon amíg a WebMail töltődik&hellip;');
	define('JS_LANG_Loading', 'Töltés&hellip;');
	define('JS_LANG_InfoMessagesLoad', 'Kérjem várjon amíg a WebMail az üzenetek listáját tölti');
	define('JS_LANG_InfoEmptyFolder', 'A mappa üres');
	define('JS_LANG_InfoPageLoading', 'Az oldal még töltődik...');
	define('JS_LANG_InfoSendMessage', 'Az üzenet elküldve');
	define('JS_LANG_InfoSaveMessage', 'Az üzenet elmentve');
// You have imported 3 new contact(s) into your contacts list.
	define('JS_LANG_InfoHaveImported', 'Ön ');
	define('JS_LANG_InfoNewContacts', ' új névjegyet sikeresen importált.');
	define('JS_LANG_InfoToDelete', 'A(z) ');
	define('JS_LANG_InfoDeleteContent', ' mappa törléséhez először törölnie kell a teljes tartalmát.');
	define('JS_LANG_InfoDeleteNotEmptyFolders', 'Nem üres mappák törlése nem lehetséges. A törléshez először ürítse a tartalmukat.');
	define('JS_LANG_InfoRequiredFields', '* kötelező mezők');

	define('JS_LANG_ConfirmAreYouSure', 'Biztos benne?');
	define('JS_LANG_ConfirmDirectModeAreYouSure', 'A kijelölt üzenetek VÉGLEGESEN törlődnek! Biztos benne?');
	define('JS_LANG_ConfirmSaveSettings', 'A beállítások még nem kerültek mentésre. Kattintson az OK-ra a mentéshez.');
	define('JS_LANG_ConfirmSaveContactsSettings', 'A névjegy beállítások még nem kerültek mentésre. Kattintson az OK-ra a mentéshez.');
	define('JS_LANG_ConfirmSaveAcctProp', 'A fiók tulajdonságai még nem kerültek mentésre. Kattintson az OK-ra a mentéshez.');
	define('JS_LANG_ConfirmSaveFilter', 'A szűrő tulajdonságai még nem merültek mentésre. Kattintson az OK-ra a mentéshez.');
	define('JS_LANG_ConfirmSaveSignature', 'Az aláírás nem lett elmentve. Kattintson az OK-ra a mentéshez.');
	define('JS_LANG_ConfirmSavefolders', 'A mappák nem lettek elmentve. Kattintson az OK-ra a mentéshez.');
	define('JS_LANG_ConfirmHtmlToPlain', 'Figyelmeztetés: azzal, hogy megváltoztatja a formátumát az üzenetnek HTML-ről sima szövegre, az formátum elveszik. Kattintson az OK-ra a folytatáshoz.');
	define('JS_LANG_ConfirmAddFolder', 'Mielőtt mappát adna hozzá a változásokat mentenie kell. Kattintson az OK-ra a mentéshez.');
	define('JS_LANG_ConfirmEmptySubject', 'A tárgy mező üres, biztosan folytatja?');

	define('JS_LANG_WarningEmailBlank', 'Nem hagyhatja az<br />E-mail cím: mezőt üresen');
	define('JS_LANG_WarningLoginBlank', 'Nem hagyhatja a<br />Postafiók: mezőt üresen');
	define('JS_LANG_WarningToBlank', 'Nem hagyhatja a Címzett: mezőt üresen');
	define('JS_LANG_WarningServerPortBlank', 'Nem hagyhatja a POP3<br />SMTP szerver/port mezőket üresen');
	define('JS_LANG_WarningEmptySearchLine', 'Üres keresés. Kérjük adja meg a keresett szöveget');
	define('JS_LANG_WarningMarkListItem', 'Kérjük jelöljön meg legalább egy üzenetet a listában');
	define('JS_LANG_WarningFolderMove', 'A mappa nem helyezhető át, mert más szinten van');
	define('JS_LANG_WarningContactNotComplete', 'Kérjük adjon meg e-mail címet vagy nevet');
	define('JS_LANG_WarningGroupNotComplete', 'Kérjük adjon meg egy csoportnevet');

	define('JS_LANG_WarningEmailFieldBlank', 'Nem hagyhatja üresen az E-mail mezőt');
	define('JS_LANG_WarningIncServerBlank', 'Nem hagyhatja üresen a POP3(IMAP4) Szerver mezőt');
	define('JS_LANG_WarningIncPortBlank', 'Nem hagyhatja üresen a POP3(IMAP4) Szerver Port mezőt');
	define('JS_LANG_WarningIncLoginBlank', 'Nem hagyhatja üresen a POP3(IMAP4) Azonosító mezőt');
	define('JS_LANG_WarningIncPortNumber', 'Kérjük adjon meg pozitív számot a POP3(IMAP4) port mezőben.');
	define('JS_LANG_DefaultIncPortNumber', 'Alapértelmezett POP3(IMAP4) portszám a 110(143).');
	define('JS_LANG_WarningIncPassBlank', 'Nem hagyhatja üresen a POP3(IMAP4) Jelszó mezőt');
	define('JS_LANG_WarningOutPortBlank', 'Nem hagyhatja üresen a SMTP Szerver Port mezőt');
	define('JS_LANG_WarningOutPortNumber', 'Kérjük adjon meg pozitív számot az SMTP port mezőben.');
	define('JS_LANG_WarningCorrectEmail', 'Kérjük adjon meg valós e-mail címet.');
	define('JS_LANG_DefaultOutPortNumber', 'Az alapértelmezett SMTP port a 25.');

	define('JS_LANG_WarningCsvExtention', 'A kiterjesztésnek .csv-nek kell lennie');
	define('JS_LANG_WarningImportFileType', 'Kérjük válassza ki azt az alkalmazást ahonnan az adatokat importálni szeretné');
	define('JS_LANG_WarningEmptyImportFile', 'Kérjük válassza ki a fájt a Tallózás gombra kattintva');
	
	define('JS_LANG_WarningContactsPerPage', 'A névjegyek száma oldalanként értékének pozitívnak kell lennie');
	define('JS_LANG_WarningMessagesPerPage', 'Az üzenetek száma oldalanként értékének pozitívnak kell lennie');
	define('JS_LANG_WarningMailsOnServerDays', 'Kérjük adjon meg pozitív számot az Üzenetek tárolása a szervere mezőben.');
	define('JS_LANG_WarningEmptyFilter', 'Adjon meg egy karatkerláncot');
	define('JS_LANG_WarningEmptyFolderName', 'Adjon meg a mappa nevét');

	define('JS_LANG_ErrorConnectionFailed', 'Sikertelen kapcsolódás');
	define('JS_LANG_ErrorRequestFailed', 'Az adatok lekérése sikertelen');
	define('JS_LANG_ErrorAbsentXMLHttpRequest', 'Az XMLHttpRequest objektum hibás');
	define('JS_LANG_ErrorWithoutDesc', 'Leírás nélküli hiba történt');
	define('JS_LANG_ErrorParsing', 'Hiba az XML fájl olvasása közben.');
	define('JS_LANG_ResponseText', 'Válasz szöveg:');
	define('JS_LANG_ErrorEmptyXmlPacket', 'Üres XML csomag');
	define('JS_LANG_ErrorImportContacts', 'Hiba a névjegyek importálása közben');
	define('JS_LANG_ErrorNoContacts', 'Nincs importálandó névjegy.');
	define('JS_LANG_ErrorCheckMail', 'Az üzenetek fogadása hiba miatt megszakadt. Lehetséges, hogy nem minden üzenet lett lekérve.');

	define('JS_LANG_LoggingToServer', 'Kapcsolódás a szerverhez&hellip;');
	define('JS_LANG_GettingMsgsNum', 'Az üzenetek számának lekérése');
	define('JS_LANG_RetrievingMessage', 'Üzenetek fogadása');
	define('JS_LANG_DeletingMessage', 'Üzenet törlése');
	define('JS_LANG_DeletingMessages', 'Üzenet(ek) törlése');
	define('JS_LANG_Of', ' ');
	define('JS_LANG_Connection', 'Kapcsolat');
	define('JS_LANG_Charset', 'Karakterkészlet');
	define('JS_LANG_AutoSelect', 'Automatikus választás');

	define('JS_LANG_Contacts', 'Címjegyzék');
	define('JS_LANG_ClassicVersion', 'Klasszikus nézet');
	define('JS_LANG_Logout', 'Kilépés');
	define('JS_LANG_Settings', 'Beállítások');

	define('JS_LANG_LookFor', 'Keresés');
	define('JS_LANG_SearchIn', 'Keresés');
	define('JS_LANG_QuickSearch', 'Csak a Feladó, Címzett, Tárgy mezőkben keressen (gyorsabb).');
	define('JS_LANG_SlowSearch', 'Keresés a teljes üzenetben');
	define('JS_LANG_AllMailFolders', 'Össszes mappa');
	define('JS_LANG_AllGroups', 'Összes csoport');

	define('JS_LANG_NewMessage', 'Új üzenet');
	define('JS_LANG_CheckMail', 'Fogadás');
	define('JS_LANG_ReloadFolders', 'A mappák újraolvasása');
	define('JS_LANG_EmptyTrash', 'Szemetesláda ürítése');
	define('JS_LANG_MarkAsRead', 'Megjelöl olvasottként');
	define('JS_LANG_MarkAsUnread', 'Megjelöl olvasatlanként');
	define('JS_LANG_MarkFlag', 'Megjelöl');
	define('JS_LANG_MarkUnflag', 'Megjelölés törlése');
	define('JS_LANG_MarkAllRead', 'Az összes megjelölése olvasottként');
	define('JS_LANG_MarkAllUnread', 'Az összes megjelölése olvasatlanként');
	define('JS_LANG_Reply', 'Válasz');
	define('JS_LANG_ReplyAll', 'Válasz mindenkinek');
	define('JS_LANG_Delete', 'Törlés');
	define('JS_LANG_Undelete', 'Visszaállítás');
	define('JS_LANG_PurgeDeleted', 'A törölt üzenetek megsemmisítése');
	define('JS_LANG_MoveToFolder', 'Mozgatás másik mappába');
	define('JS_LANG_Forward', 'Továbbítás');

	define('JS_LANG_HideFolders', 'Mappák elrejtése');
	define('JS_LANG_ShowFolders', 'Mappák megjelenítése');
	define('JS_LANG_ManageFolders', 'Mappák kezelése');
	define('JS_LANG_SyncFolder', 'Szinkronizált mappa');
	define('JS_LANG_NewMessages', 'Új üzenetek');
	define('JS_LANG_Messages', 'Üzenet(ek)');

	define('JS_LANG_From', 'Feladó');
	define('JS_LANG_To', 'Címzett');
	define('JS_LANG_Date', 'Dátum');
	define('JS_LANG_Size', 'Méret');
	define('JS_LANG_Subject', 'Tárgy');

	define('JS_LANG_FirstPage', 'Első oldal');
	define('JS_LANG_PreviousPage', 'Előző oldal');
	define('JS_LANG_NextPage', 'Következő oldal');
	define('JS_LANG_LastPage', 'Utolsó oldal');

	define('JS_LANG_SwitchToPlain', 'Váltás sima szövegre');
	define('JS_LANG_SwitchToHTML', 'Váltás HTML-re');
	define('JS_LANG_AddToAddressBokk', 'Hozzáadás a címjegyzékhez');
	define('JS_LANG_ClickToDownload', 'Kattintson ide a letöltéshez');
	define('JS_LANG_View', 'Megtekint');
	define('JS_LANG_ShowFullHeaders', 'Üzenet fejléc megtekintése');
	define('JS_LANG_HideFullHeaders', 'Üzenet fejléc elrejtése');

	define('JS_LANG_MessagesInFolder', 'üzenet a mappában');
	define('JS_LANG_YouUsing', 'Felhasznált adat: ');
	define('JS_LANG_OfYour', ', a teljeses rendelkezésre állóból: ');
	define('JS_LANG_Mb', 'MB');
	define('JS_LANG_Kb', 'KB');
	define('JS_LANG_B', 'B');

	define('JS_LANG_SendMessage', 'Elküld');
	define('JS_LANG_SaveMessage', 'Mentés');
	define('JS_LANG_Print', 'Nyomtatás');
	define('JS_LANG_PreviousMsg', 'Előző üzenet');
	define('JS_LANG_NextMsg', 'Következő üzenet');
	define('JS_LANG_AddressBook', 'Címlista');
	define('JS_LANG_ShowBCC', 'Titkos másolat megjelenítése');
	define('JS_LANG_HideBCC', 'Titkos másolat elrejtése');
	define('JS_LANG_CC', 'Másolatot kap');
	define('JS_LANG_BCC', 'Titkos másolat');
	define('JS_LANG_ReplyTo', 'Válasz mint');
	define('JS_LANG_AttachFile', 'Fájl csatolása');
	define('JS_LANG_Attach', 'Csatolás');
	define('JS_LANG_Re', 'Re');
	define('JS_LANG_OriginalMessage', 'Eredeti üzenet');
	define('JS_LANG_Sent', 'Elküldve');
	define('JS_LANG_Fwd', 'Továbbítva');
	define('JS_LANG_Low', 'Alacsony');
	define('JS_LANG_Normal', 'Normál');
	define('JS_LANG_High', 'Magas');
	define('JS_LANG_Importance', 'Fontosság');
	define('JS_LANG_Close', 'Bezár');
	
	define('JS_LANG_Common', 'Általános');
	define('JS_LANG_EmailAccounts', 'E-mail fiókok');
	
	define('JS_LANG_MsgsPerPage', 'Üzenet oldalanként');
	define('JS_LANG_DisableRTE', 'A szövegszerkesztő kikapcsolása');
	define('JS_LANG_Skin', 'Téma');
	define('JS_LANG_DefCharset', 'Alapértelmezett karakterkészlet');
	define('JS_LANG_DefCharsetInc', 'Alapértelmezett karakterkészlet fogadásnál');
	define('JS_LANG_DefCharsetOut', 'Alapértelmezett karakterkészlet küldésnél');
	define('JS_LANG_DefTimeOffset', 'Alapértelmezett időzóna');
	define('JS_LANG_DefLanguage', 'Alapértelmezett nyelv');
	define('JS_LANG_DefDateFormat', 'Alapértelmezett dátum formátum');
	define('JS_LANG_ShowViewPane', 'Betekintő nézet használata');
	define('JS_LANG_Save', 'Mentés');
	define('JS_LANG_Cancel', 'Mégsem');
	define('JS_LANG_OK', 'OK');
	
	define('JS_LANG_Remove', 'Eltávolít');
	define('JS_LANG_AddNewAccount', 'Új fiók létrehozása');
	define('JS_LANG_Signature', 'Aláírás');
	define('JS_LANG_Filters', 'Szűrők');
	define('JS_LANG_Properties', 'Tulajdonságok');
	define('JS_LANG_UseForLogin', 'Ennek a mappának a tulajdonságainak használata (felhasználónév és jelszó) belépéshez');
	define('JS_LANG_MailFriendlyName', 'Az Ön neve');
	define('JS_LANG_MailEmail', 'E-mail');
	define('JS_LANG_MailIncHost', 'Beérkező üzenet');
	define('JS_LANG_Imap4', 'IMAP4');
	define('JS_LANG_Pop3', 'POP3');
	define('JS_LANG_MailIncPort', 'Port');
	define('JS_LANG_MailIncLogin', 'Postafiók');
	define('JS_LANG_MailIncPass', 'Jelszó');
	define('JS_LANG_MailOutHost', 'SMTP Szerver');
	define('JS_LANG_MailOutPort', 'Port');
	define('JS_LANG_MailOutLogin', 'SMTP azonosító');
	define('JS_LANG_MailOutPass', 'SMTP jelszó');
	define('JS_LANG_MailOutAuth1', 'SMTP kiszolgáló hitelesítést igényel');
	define('JS_LANG_MailOutAuth2', '(Az SMTP azonosító/jelszó mezőket üresen hagyhatja ha azok megegyeznek a POP3/IMAP4 beállításokkal)');
	define('JS_LANG_UseFriendlyNm1', 'Felhasználóbarát megjelenés a "Feladó:" mezőben');
	define('JS_LANG_UseFriendlyNm2', '(Az Ön Neve &lt;emailcim@mail.com&gt;)');
	define('JS_LANG_GetmailAtLogin', 'Levelek letöltése bejelentkezéskor');
	define('JS_LANG_MailMode0', 'A letöltött üzenetek törlése a szerverről');
	define('JS_LANG_MailMode1', 'Az üzenetek tárolása a szerveren');
	define('JS_LANG_MailMode2', 'Az üzenetek megőrzése a szerveren');
	define('JS_LANG_MailsOnServerDays', 'napig');
	define('JS_LANG_MailMode3', 'Az üzenet törlése a szerverről amennyiben törölésre kerül a Lomtárból');
	define('JS_LANG_InboxSyncType', 'A Beérkezett üzenetek mappa szinkronizálásának típusa');
	
	define('JS_LANG_SyncTypeNo', 'Ne szinkronizáljon');
	define('JS_LANG_SyncTypeNewHeaders', 'Új fejlécek');
	define('JS_LANG_SyncTypeAllHeaders', 'Összes fejléc');
	define('JS_LANG_SyncTypeNewMessages', 'Új üzenetek');
	define('JS_LANG_SyncTypeAllMessages', 'Összes üzenet');
	define('JS_LANG_SyncTypeDirectMode', 'Direkt mód');
	
	define('JS_LANG_Pop3SyncTypeEntireHeaders', 'Csak a fejléceket');
	define('JS_LANG_Pop3SyncTypeEntireMessages', 'Teljes üzeneteket');
	define('JS_LANG_Pop3SyncTypeDirectMode', 'Direkt mód');
	
	define('JS_LANG_DeleteFromDb', 'Az üzenet törlése az adatbázisból amennyiben már nem létezik a szervern');

	define('JS_LANG_EditFilter', 'Szűrő&nbsp;szerkesztése');
	define('JS_LANG_NewFilter', 'Új szűrő létrehozása');
	define('JS_LANG_Field', 'Mező');
	define('JS_LANG_Condition', 'Feltétel');
	define('JS_LANG_ContainSubstring', 'Szövegrészt tartalmaz');
	define('JS_LANG_ContainExactPhrase', 'Pontos szövegrész');
	define('JS_LANG_NotContainSubstring', 'Nem tartalmazza a szövegrészt');
	define('JS_LANG_FilterDesc_At', '');
	define('JS_LANG_FilterDesc_Field', 'mező');
	define('JS_LANG_Action', 'Tevékenység');
	define('JS_LANG_DoNothing', 'Ne csináljon semmit');
	define('JS_LANG_DeleteFromServer', 'Azonall törölje a szerverről');
	define('JS_LANG_MarkGrey', 'Jelenítse meg szürkén');
	define('JS_LANG_Add', 'Hozzáadás');
	define('JS_LANG_OtherFilterSettings', 'Egyéb szűrő beállítás');
	define('JS_LANG_ConsiderXSpam', 'Vegye figyelembe az X-Spam fejlécet');
	define('JS_LANG_Apply', 'Alkalmaz');

	define('JS_LANG_InsertLink', 'Link beszúrása');
	define('JS_LANG_RemoveLink', 'Link törlése');
	define('JS_LANG_Numbering', 'Számozás');
	define('JS_LANG_Bullets', 'Felsorolás');
	define('JS_LANG_HorizontalLine', 'Vízszintes vonal');
	define('JS_LANG_Bold', 'Félkövér');
	define('JS_LANG_Italic', 'Dőlt');
	define('JS_LANG_Underline', 'Aláhúzs');
	define('JS_LANG_AlignLeft', 'Balra rendezett');
	define('JS_LANG_Center', 'Középre rendezett');
	define('JS_LANG_AlignRight', 'Jobbra rendezett');
	define('JS_LANG_Justify', 'Sorkizárt');
	define('JS_LANG_FontColor', 'Betű színe');
	define('JS_LANG_Background', 'Háttér');
	define('JS_LANG_SwitchToPlainMode', 'Normál szöveg nézetre váltás');
	define('JS_LANG_SwitchToHTMLMode', 'HTML szövegre váltás');
	define('JS_LANG_AddSignatures', 'Aláírás hozzáadása minden kimenő üzenethez');
	define('JS_LANG_DontAddToReplies', 'Az aláírást ne adja hozzá a válaszokhoz és a továbbításhoz');

	define('JS_LANG_Folder', 'Mappa');
	define('JS_LANG_Msgs', 'Üzenetek');
	define('JS_LANG_Synchronize', 'Szinkronizálás');
	define('JS_LANG_ShowThisFolder', 'A mappa megjelenítése');
	define('JS_LANG_Total', 'Összes');
	define('JS_LANG_DeleteSelected', 'Kijelöltek törlése');
	define('JS_LANG_AddNewFolder', 'Új mappa hozzáadása');
	define('JS_LANG_NewFolder', 'Új mappa');
	define('JS_LANG_ParentFolder', 'Szülő mappa');
	define('JS_LANG_NoParent', 'Nincs szülő');
	define('JS_LANG_OnMailServer', 'Ezt a mappát hozza létre a WebMailban és a szerveren');
	define('JS_LANG_InWebMail', 'Ezt a mappát hozza létre csak a WebMailban');
	define('JS_LANG_FolderName', 'Mappa neve');

	define('JS_LANG_ContactsPerPage', 'Névjegyek oldalanként');
	define('JS_LANG_WhiteList', 'Címjegyzék mint fehér-lista');

	define('JS_LANG_CharsetDefault', 'Alapértelmezett');
	define('JS_LANG_CharsetArabicAlphabetISO', 'Arabic Alphabet (ISO)');
	define('JS_LANG_CharsetArabicAlphabet', 'Arabic Alphabet (Windows)');
	define('JS_LANG_CharsetBalticAlphabetISO', 'Baltic Alphabet (ISO)');
	define('JS_LANG_CharsetBalticAlphabet', 'Baltic Alphabet (Windows)');
	define('JS_LANG_CharsetCentralEuropeanAlphabetISO', 'Central European Alphabet (ISO)');
	define('JS_LANG_CharsetCentralEuropeanAlphabet', 'Central European Alphabet (Windows)');
	define('JS_LANG_CharsetChineseSimplifiedEUC', 'Chinese Simplified (EUC)');
	define('JS_LANG_CharsetChineseSimplifiedGB', 'Chinese Simplified (GB2312)');
	define('JS_LANG_CharsetChineseTraditional', 'Chinese Traditional (Big5)');
	define('JS_LANG_CharsetCyrillicAlphabetISO', 'Cyrillic Alphabet (ISO)');
	define('JS_LANG_CharsetCyrillicAlphabetKOI8R', 'Cyrillic Alphabet (KOI8-R)');
	define('JS_LANG_CharsetCyrillicAlphabet', 'Cyrillic Alphabet (Windows)');
	define('JS_LANG_CharsetGreekAlphabetISO', 'Greek Alphabet (ISO)');
	define('JS_LANG_CharsetGreekAlphabet', 'Greek Alphabet (Windows)');
	define('JS_LANG_CharsetHebrewAlphabetISO', 'Hebrew Alphabet (ISO)');
	define('JS_LANG_CharsetHebrewAlphabet', 'Hebrew Alphabet (Windows)');
	define('JS_LANG_CharsetJapanese', 'Japanese');
	define('JS_LANG_CharsetJapaneseShiftJIS', 'Japanese (Shift-JIS)');
	define('JS_LANG_CharsetKoreanEUC', 'Korean (EUC)');
	define('JS_LANG_CharsetKoreanISO', 'Korean (ISO)');
	define('JS_LANG_CharsetLatin3AlphabetISO', 'Latin 3 Alphabet (ISO)');
	define('JS_LANG_CharsetTurkishAlphabet', 'Turkish Alphabet');
	define('JS_LANG_CharsetUniversalAlphabetUTF7', 'Universal Alphabet (UTF-7)');
	define('JS_LANG_CharsetUniversalAlphabetUTF8', 'Universal Alphabet (UTF-8)');
	define('JS_LANG_CharsetVietnameseAlphabet', 'Vietnamese Alphabet (Windows)');
	define('JS_LANG_CharsetWesternAlphabetISO', 'Western Alphabet (ISO)');
	define('JS_LANG_CharsetWesternAlphabet', 'Western Alphabet (Windows)');

	define('JS_LANG_TimeDefault', 'Alapértelmezett');
	define('JS_LANG_TimeEniwetok', 'Eniwetok, Kwajalein, Dateline Time');
	define('JS_LANG_TimeMidwayIsland', 'Midway Island, Samoa');
	define('JS_LANG_TimeHawaii', 'Hawaii');
	define('JS_LANG_TimeAlaska', 'Alaska');
	define('JS_LANG_TimePacific', 'Pacific Time (US & Canada); Tijuana');
	define('JS_LANG_TimeArizona', 'Arizona');
	define('JS_LANG_TimeMountain', 'Mountain Time (US & Canada)');
	define('JS_LANG_TimeCentralAmerica', 'Central America');
	define('JS_LANG_TimeCentral', 'Central Time (US & Canada)');
	define('JS_LANG_TimeMexicoCity', 'Mexico City, Tegucigalpa');
	define('JS_LANG_TimeSaskatchewan', 'Saskatchewan');
	define('JS_LANG_TimeIndiana', 'Indiana (East)');
	define('JS_LANG_TimeEastern', 'Eastern Time (US & Canada)');
	define('JS_LANG_TimeBogota', 'Bogota, Lima, Quito');
	define('JS_LANG_TimeSantiago', 'Santiago');
	define('JS_LANG_TimeCaracas', 'Caracas, La Paz');
	define('JS_LANG_TimeAtlanticCanada', 'Atlantic Time (Canada)');
	define('JS_LANG_TimeNewfoundland', 'Newfoundland');
	define('JS_LANG_TimeGreenland', 'Greenland');
	define('JS_LANG_TimeBuenosAires', 'Buenos Aires, Georgetown');
	define('JS_LANG_TimeBrasilia', 'Brasilia');
	define('JS_LANG_TimeMidAtlantic', 'Mid-Atlantic');
	define('JS_LANG_TimeCapeVerde', 'Cape Verde Is.');
	define('JS_LANG_TimeAzores', 'Azores');
	define('JS_LANG_TimeMonrovia', 'Casablanca, Monrovia');
	define('JS_LANG_TimeGMT', 'Dublin, Edinburgh, Lisbon, London');
	define('JS_LANG_TimeBerlin', 'Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna');
	define('JS_LANG_TimePrague', 'Belgrade, Bratislava, Budapest, Ljubljana, Prague');
	define('JS_LANG_TimeParis', 'Brussels, Copenhagen, Madrid, Paris');
	define('JS_LANG_TimeSarajevo', 'Sarajevo, Skopje, Sofija, Vilnius, Warsaw, Zagreb');
	define('JS_LANG_TimeWestCentralAfrica', 'West Central Africa');
	define('JS_LANG_TimeAthens', 'Athens, Istanbul, Minsk');
	define('JS_LANG_TimeEasternEurope', 'Bucharest');
	define('JS_LANG_TimeCairo', 'Cairo');
	define('JS_LANG_TimeHarare', 'Harare, Pretoria');
	define('JS_LANG_TimeHelsinki', 'Helsinki, Riga, Tallinn');
	define('JS_LANG_TimeIsrael', 'Israel, Jerusalem Standard Time');
	define('JS_LANG_TimeBaghdad', 'Baghdad');
	define('JS_LANG_TimeArab', 'Arab, Kuwait, Riyadh');
	define('JS_LANG_TimeMoscow', 'Moscow, St. Petersburg, Volgograd');
	define('JS_LANG_TimeEastAfrica', 'East Africa, Nairobi');
	define('JS_LANG_TimeTehran', 'Tehran');
	define('JS_LANG_TimeAbuDhabi', 'Abu Dhabi, Muscat');
	define('JS_LANG_TimeCaucasus', 'Baku, Tbilisi, Yerevan');
	define('JS_LANG_TimeKabul', 'Kabul');
	define('JS_LANG_TimeEkaterinburg', 'Ekaterinburg');
	define('JS_LANG_TimeIslamabad', 'Islamabad, Karachi, Sverdlovsk, Tashkent');
	define('JS_LANG_TimeBombay', 'Calcutta, Chennai, Mumbai, New Delhi, India Standard Time');
	define('JS_LANG_TimeNepal', 'Kathmandu, Nepal');
	define('JS_LANG_TimeAlmaty', 'Almaty, Novosibirsk, North Central Asia');
	define('JS_LANG_TimeDhaka', 'Astana, Dhaka');
	define('JS_LANG_TimeSriLanka', 'Sri Jayawardenepura, Sri Lanka');
	define('JS_LANG_TimeRangoon', 'Rangoon');
	define('JS_LANG_TimeBangkok', 'Bangkok, Hanoi, Jakarta');
	define('JS_LANG_TimeKrasnoyarsk', 'Krasnoyarsk');
	define('JS_LANG_TimeBeijing', 'Beijing, Chongqing, Hong Kong SAR, Urumqi');
	define('JS_LANG_TimeIrkutsk', 'Irkutsk, Ulaan Bataar');
	define('JS_LANG_TimeSingapore', 'Kuala Lumpur, Singapore');
	define('JS_LANG_TimePerth', 'Perth, Western Australia');
	define('JS_LANG_TimeTaipei', 'Taipei');
	define('JS_LANG_TimeTokyo', 'Osaka, Sapporo, Tokyo');
	define('JS_LANG_TimeSeoul', 'Seoul, Korea Standard time');
	define('JS_LANG_TimeYakutsk', 'Yakutsk');
	define('JS_LANG_TimeAdelaide', 'Adelaide, Central Australia');
	define('JS_LANG_TimeDarwin', 'Darwin');
	define('JS_LANG_TimeBrisbane', 'Brisbane, East Australia');
	define('JS_LANG_TimeSydney', 'Canberra, Melbourne, Sydney, Hobart');
	define('JS_LANG_TimeGuam', 'Guam, Port Moresby');
	define('JS_LANG_TimeHobart', 'Hobart, Tasmania');
	define('JS_LANG_TimeVladivostock', 'Vladivostok');
	define('JS_LANG_TimeMagadan', 'Magadan, Solomon Is., New Caledonia');
	define('JS_LANG_TimeWellington', 'Auckland, Wellington');
	define('JS_LANG_TimeFiji', 'Fiji Islands, Kamchatka, Marshall Is.');
	define('JS_LANG_TimeTonga', 'Nuku\'alofa, Tonga,');

	define('LanguageEnglish', 'English');
	define('LanguageCatala', 'Catalan');
	define('LanguageNederlands', 'Dutch');
	define('LanguageFrench', 'French');
	define('LanguageGerman', 'German');
	define('LanguageItaliano', 'Italian');
	define('LanguagePortuguese', 'Portuguese (BR)');
	define('LanguageEspanyol', 'Spanish');
	define('LanguageSwedish', 'Swedish');
	define('LanguageTurkish', 'Turkish');

	define('JS_LANG_DateDefault', 'Alapértelmezett');
	define('JS_LANG_DateDDMMYY', 'DD/MM/YY');
	define('JS_LANG_DateMMDDYY', 'MM/DD/YY');
	define('JS_LANG_DateDDMonth', 'DD Month (01 Jan)');
	define('JS_LANG_DateAdvanced', 'Bővített');
	
	define('JS_LANG_NewContact', 'Új névjegy');
	define('JS_LANG_NewGroup', 'Új csoport');
	define('JS_LANG_AddContactsTo', 'A névjegy hozzáadása');
	define('JS_LANG_ImportContacts', 'Névjegyek importálása');
	
	define('JS_LANG_Name', 'Név');
	define('JS_LANG_Email', 'E-mail');
	define('JS_LANG_DefaultEmail', 'Alapértelmezett e-mail');
	define('JS_LANG_NotSpecifiedYet', 'Nem megadott');
	define('JS_LANG_ContactName', 'Név');
	define('JS_LANG_Birthday', 'Születésnap');
	define('JS_LANG_Month', 'Hónap');
	define('JS_LANG_January', 'Január');
	define('JS_LANG_February', 'Február');
	define('JS_LANG_March', 'Március');
	define('JS_LANG_April', 'Április');
	define('JS_LANG_May', 'Május');
	define('JS_LANG_June', 'Június');
	define('JS_LANG_July', 'Július');
	define('JS_LANG_August', 'Augusztus');
	define('JS_LANG_September', 'Szeptember');
	define('JS_LANG_October', 'Október');
	define('JS_LANG_November', 'November');
	define('JS_LANG_December', 'December');
	define('JS_LANG_Day', 'Nap');
	define('JS_LANG_Year', 'Év');
	define('JS_LANG_UseFriendlyName1', 'Olvasható név használata');
	define('JS_LANG_UseFriendlyName2', '(például, John Doe &lt;johndoe@mail.com&gt;)');
	define('JS_LANG_Personal', 'Személyes');
	define('JS_LANG_PersonalEmail', 'Személyes e-mail');
	define('JS_LANG_StreetAddress', 'Utca');
	define('JS_LANG_City', 'Város');
	define('JS_LANG_Fax', 'Fax');
	define('JS_LANG_StateProvince', 'Megye/Tartomány');
	define('JS_LANG_Phone', 'Telefon');
	define('JS_LANG_ZipCode', 'Irányítószám');
	define('JS_LANG_Mobile', 'Mobil');
	define('JS_LANG_CountryRegion', 'Ország/Régió');
	define('JS_LANG_WebPage', 'Weboldal');
	define('JS_LANG_Go', 'Ugrás');
	define('JS_LANG_Home', 'Otthon');
	define('JS_LANG_Business', 'Céges');
	define('JS_LANG_BusinessEmail', 'Céges e-mail');
	define('JS_LANG_Company', 'Cégnév');
	define('JS_LANG_JobTitle', 'Beosztás');
	define('JS_LANG_Department', 'Részegység');
	define('JS_LANG_Office', 'Iroda');
	define('JS_LANG_Pager', 'Személyhívó');
	define('JS_LANG_Other', 'Egyéb');
	define('JS_LANG_OtherEmail', 'Egyéb e-mail');
	define('JS_LANG_Notes', 'Megjegyzések');
	define('JS_LANG_Groups', 'Csoportok');
	define('JS_LANG_ShowAddFields', 'További mezők megjelenítése');
	define('JS_LANG_HideAddFields', 'További mezők elrejtése');
	define('JS_LANG_EditContact', 'Névjegy szerkesztése');
	define('JS_LANG_GroupName', 'Csoport neve');
	define('JS_LANG_AddContacts', 'Névjegyek hozzáadása');
	define('JS_LANG_CommentAddContacts', '(Ha több címet kíván megadni, használjon vesszőt az elválasztáshoz)');
	define('JS_LANG_CreateGroup', 'Csoport hozzáadása');
	define('JS_LANG_Rename', 'átnevezés');
	define('JS_LANG_MailGroup', 'Levelező csoport');
	define('JS_LANG_RemoveFromGroup', 'Eltávolitás a csoportból');
	define('JS_LANG_UseImportTo', 'Az Importálás használata elősegíti, hogy a címjegyzékét áthozza Microsoft Outlook, Microsoft Outlook Express levelezőkből a WebMail címjegyzékébe.');
	define('JS_LANG_Outlook1', 'Microsoft Outlook 2000/XP/2003');
	define('JS_LANG_Outlook2', 'Microsoft Outlook Express 6');
	define('JS_LANG_SelectImportFile', 'Válassza ki a fájlt (.CSV formátumban) melyet importálni szeretne');
	define('JS_LANG_Import', 'Importálás');
	define('JS_LANG_ContactsMessage', 'Ez a névjegyek oldala!!!');
	define('JS_LANG_ContactsCount', 'névjegy');
	define('JS_LANG_GroupsCount', 'csoport');
	
// webmail 4.1 constants
	define('PicturesBlocked', 'A képek ebben az üzenetben blokkolva vannak az Ön biztonsága érdekében.');
	define('ShowPictures', 'Képek megjelenítése');
	define('ShowPicturesFromSender', 'A képek megjelenítése minden esetben ettől a feladótól');
	define('AlwaysShowPictures', 'A képek megjelenítése minden esetben');

	define('TreatAsOrganization', 'Kezelés szervezetként');

	define('WarningGroupAlreadyExist', 'Ilyen nevű csoport már létezik. Kérjük adjon meg más nevet.');
	define('WarningCorrectFolderName', 'Kérjük adjon meg helyes mappa nevet.');
	define('WarningLoginFieldBlank', 'Nem hagyhatja a Belépés mezőt üresen.');
	define('WarningCorrectLogin', 'Kérjük töltse ki helyesen a Belépés mezőt.');
	define('WarningPassBlank', 'Nem hagyhatja a Jelszó mezőt üresen.');
	define('WarningCorrectIncServer', 'Kérjük adjon meg helyes POP3(IMAP) szerver címet.');
	define('WarningCorrectSMTPServer', 'Kérjük adjon meg helyes SMTP szerver címet.');
	define('WarningFromBlank', 'Nem hagyhatja a Feladó mezőt üresen.');
	define('WarningAdvancedDateFormat', 'Kérjük adja meg a dátum/idő formátumát.');

	define('AdvancedDateHelpTitle', 'Bővített dátum formátum');
	define('AdvancedDateHelpIntro', 'Amikor a &quot;Bővített formátum&quot; mezőt kiválasztja, megadhatja a dátum megjelenítésének formátumát szabadon. A következő lehetőségeket használhatjah \':\' vagy \'/\' elválasztó karakterekkel:');
	define('AdvancedDateHelpConclusion', 'Például ha ezt adja meg a szöveg mezőben &quot;mm/dd/yyyy&quot;, a dátum ebben a formában fog megjelenni: hónap/nap/év (pl.: 11/23/2005)');
	define('AdvancedDateHelpDayOfMonth', 'A hónap napja (1-től 31-ig)');
	define('AdvancedDateHelpNumericMonth', 'Hónap (1-től 12-ig)');
	define('AdvancedDateHelpTextualMonth', 'Hónap (Jan..Dec)');
	define('AdvancedDateHelpYear2', 'Év, 2 számjegy');
	define('AdvancedDateHelpYear4', 'Év, 4 számjegy');
	define('AdvancedDateHelpDayOfYear', 'Az év napja (1-től 366-ig)');
	define('AdvancedDateHelpQuarter', 'Negyedév');
	define('AdvancedDateHelpDayOfWeek', 'A hét napja (Hét..Vas)');
	define('AdvancedDateHelpWeekOfYear', 'Hét (1..53)');

	define('InfoNoMessagesFound', 'Nincs új üzenet.');
	define('ErrorSMTPConnect', 'Nem lehet csatlakozni az SMTP kiszolgálóhoz. Kérjük ellenőrizze a beállításokat.');
	define('ErrorSMTPAuth', 'Hibás felhasználónév vagy jelszó. Sikertelen SMTP hitelesítés.');
	define('ReportMessageSent', 'Az üzenet elküldve.');
	define('ReportMessageSaved', 'Az üzenet elmentve.');
	define('ErrorPOP3Connect', 'Sikertelen kapcsolódás a POP3 kiszolgálóhoz. Kérjük ellenőrizze a beállításokat.');
	define('ErrorIMAP4Connect', 'Sikertelen kapcsolódás az IMAP4 kiszolgálóhoz. Kérjük ellenőrizze a beállításokat.');
	define('ErrorPOP3IMAP4Auth', 'Hibás e-mail cím/postafiók és/vagy jelszó. Sikertelen belépés.');
	define('ErrorGetMailLimit', 'A postafiókja megtelt.');

	define('ReportSettingsUpdatedSuccessfuly', 'A beállítások sikeresen elmentve.');
	define('ReportAccountCreatedSuccessfuly', 'A fiók sikeresen létrehozva.');
	define('ReportAccountUpdatedSuccessfuly', 'A fiók sikeresen frissítve.');
	define('ConfirmDeleteAccount', 'Biztosan törli a fiókot?');
	define('ReportFiltersUpdatedSuccessfuly', 'A szűrő beállításai sikeresen elmentve.');
	define('ReportSignatureUpdatedSuccessfuly', 'Az aláírás sikeresen elmentve.');
	define('ReportFoldersUpdatedSuccessfuly', 'A mappa beállítások sikeresen elmentve.');
	define('ReportContactsSettingsUpdatedSuccessfuly', 'Címjegyzék beállítások sikeresen elmentve.');

	define('ErrorInvalidCSV', 'A kiválasztott CSV fájl formátuma hibás.');
// The group "guies" was successfully added.
	define('ReportGroupSuccessfulyAdded1', 'A csoport');
	define('ReportGroupSuccessfulyAdded2', 'sikeresen hozzáadva.');
	define('ReportGroupUpdatedSuccessfuly', 'A csoport adatai sikeresen elmentve.');
	define('ReportContactSuccessfulyAdded', 'A névjegy sikeresen hozzáadva.');
	define('ReportContactUpdatedSuccessfuly', 'A névjegy változásai sikeresen elmentve.');
// Contact(s) was added to group "friends".
	define('ReportContactAddedToGroup', 'A névjegy hozzáadva a következő csoporthoz');
	define('AlertNoContactsGroupsSelected', 'Nincs kiválasztva névjegy vagy csoport.');

	define('InfoListNotContainAddress', 'Ha a lista nem tartalmazz a névjegyet amit keres, folytassa a kezdőbetűk begépelését');
	
	define('DirectAccess', 'D');
	define('DirectAccessTitle', 'Direkt mód. WebMail direktben használja a szerver üzeneteket.');

	define('FolderInbox', 'Beérkezett üzenetek');
	define('FolderSentItems', 'Elküldött elemek');
	define('FolderDrafts', 'Piszkozatok');
	define('FolderTrash', 'Lomtár');

	define('LanguageDanish', 'Danish');
	define('LanguagePolish', 'Polish');

	define('FileLargerAttachment', 'A csatolás mérete túl nagy.');
	define('FilePartiallyUploaded', 'A csatolás csak egy része került feltöltésre hiba miatt.');
	define('NoFileUploaded', 'A csatolás nem lett feltöltve.');
	define('MissingTempFolder', 'Az átmeneti tároló könyvtár hiányzik.');
	define('MissingTempFile', 'Az átmeneti fájl hiányzik.');
	define('UnknownUploadError', 'Ismeretlen fájl feltöltési hiba.');
	define('FileLargerThan', 'Fájl feltöltési hiba. Valószínű, hogy a fájl mérete nagyobb, mint ');
	define('PROC_CANT_LOAD_DB', 'Nem lehet csatlakozni az adatbázishoz.');
	define('PROC_CANT_LOAD_LANG', 'Nem létező nyelvi fájl.');
	define('PROC_CANT_LOAD_ACCT', 'A fiók nem létezik, valószínűleg törölésre került.');
	
	define('DomainDosntExist', 'Ilyen domain név nem létezik a szerveren.');
	define('ServerIsDisable', 'A levelező kiszolgáló használatát az adminisztrátor megtiltotta.');
	
	define('PROC_ACCOUNT_EXISTS', 'A fiók nem hozható létre, mert már létezik.');
	define('PROC_CANT_GET_MESSAGES_COUNT', 'Nem lehet lekérdezni a mappában található üzenetek számát.');
	define('PROC_CANT_MAIL_SIZE', 'Nem lehet lekérdezni a levél méretét.');

	define('Organization', 'Szervezet');
	define('WarningOutServerBlank', 'Nem hagyhatja az SMTP Szerver mezőt üresen');
	
//
	define('JS_LANG_Refresh', 'Frissítés');
	define('JS_LANG_MessagesInInbox', 'Üzenet a beérkezett üzenetekben');
	define('JS_LANG_InfoEmptyInbox', 'Nincs üzenet');

// webmail 4.2 constants
	define('LanguagePortugueseBrazil', 'Portuguese-Brazil');
	define('LanguageHungarian', 'Magyar');

	define('BackToList', 'Vissza a listához');
	define('InfoNoContactsGroups', 'Nincsenek névjegyek vagy csoportok.');
	define('InfoNewContactsGroups', 'Létrehozhat új névjegyeket/csoportokat vagy beimportálhatja azokat .CSV fájlból MS Outlook formátumban.');
	define('DefTimeFormat', 'Alapértelmezett idő formátum');
	define('SpellNoSuggestions', 'Nincs javaslat');
	define('SpellWait', 'Kérem várjon&hellip;');

	define('InfoNoMessageSelected', 'Nincs kiválasztott üzenet.');
	define('InfoSingleDoubleClick', 'Kattintson akármelyik üzenetre egyet az előnézetért, kettőt pedig a teljes megjelenítésért.');
	
// calendar
	define('TitleDay', 'Napi nézet');
	define('TitleWeek', 'Heti nézet');
	define('TitleMonth', 'Havi nézet');

	define('ErrorNotSupportBrowser', 'AfterLogic Calendar nem támogatja az Ön böngészőjét. Kérem használjon FireFox 2.0 vagy jobb, Opera 9.0 vagy jobb, Internet Explorer 6.0 vagy jobb, Safari 3.0.2 vagy jobb böngészőket');
	define('ErrorTurnedOffActiveX', 'ActiveX támogatás kikapcsolva . <br/>Ennek bekapcsolása szükséges a program használatához.');

	define('Calendar', 'Naptár');
	
	define('TabDay', 'Nap');
	define('TabWeek', 'Hét');
	define('TabMonth', 'Hónap');
	
	define('ToolNewEvent', 'Új&nbsp;Esemény');
	define('ToolBack', 'Vissza');
	define('ToolToday', 'Ma');
	define('AltNewEvent', 'Új esemény');
	define('AltBack', 'Vissza');
	define('AltToday', 'Ma');
	define('CalendarHeader', 'Naptár');
	define('CalendarsManager', 'Naptár kezelő');
	
	define('CalendarActionNew', 'Új naptár');
	define('EventHeaderNew', 'Új esemény');
	define('CalendarHeaderNew', 'Új Naptár');
	
	define('EventSubject', 'Tárgy');
	define('EventCalendar', 'Naptár');
	define('EventFrom', 'Kezdete');
	define('EventTill', 'Vége');
	define('CalendarDescription', 'Leírás');
	define('CalendarColor', 'Szín');
	define('CalendarName', 'Naptár neve');
	define('CalendarDefaultName', 'Az én naptáram');
	
	define('ButtonSave', 'Ment');
	define('ButtonCancel', 'Mégsem');
	define('ButtonDelete', 'Törlés');

	define('AltPrevMonth', 'Előző Hónap');
	define('AltNextMonth', 'Köv. Hónap');

	define('CalendarHeaderEdit', 'Naptár szerkesztés');
	define('CalendarActionEdit', 'Naptár szerkesztés');
	define('ConfirmDeleteCalendar', 'Biztosan törli a naptárat');
	define('InfoDeleting', 'Törlés...');
	define('WarningCalendarNameBlank', 'Nem hagyhatja a naptár nevét üresen.');
	define('ErrorCalendarNotCreated', 'A naptár nem lett létrehozva');
	define('WarningSubjectBlank', 'Nem hagyhatja a tárgyat üresen.');
	define('WarningIncorrectTime', 'A megadott időpont hibás karaktereket is tarmalaz.');
	define('WarningIncorrectFromTime', 'A kezdete időpont hibás.');
	define('WarningIncorrectTillTime', 'A vége időpont hibás.');
	define('WarningStartEndDate', 'A vége dátumnak nagyobbnak vagy egyenlőnek kell lenni a kezdetnél.');
	define('WarningStartEndTime', 'A végének később kell lennie a kezdetnél.');
	define('WarningIncorrectDate', 'A dátumnak valósnak kell lennie.');
	define('InfoLoading', 'Töltés...');
	define('EventCreate', 'Új esemény');
	define('CalendarHideOther', 'A többi naptár elrejtése');
	define('CalendarShowOther', 'A többi naptár megjelenítése');
	define('CalendarRemove', 'Naptár eltávolítása');
	define('EventHeaderEdit', 'Esemény szerkesztése');

	define('InfoSaving', 'Mentés...');
	define('SettingsDisplayName', 'Név megjelenítése');
	define('SettingsTimeFormat', 'Idő formátum');
	define('SettingsDateFormat', 'Dátum formátum');
	define('SettingsShowWeekends', 'Hétvégék megjelenítése');
	define('SettingsWorkdayStarts', 'A munkanapok kezdődnek');
	define('SettingsWorkdayEnds', 'végződnek');
	define('SettingsShowWorkday', 'Munkanap megjelenítése');
	define('SettingsWeekStartsOn', 'A hét kezdődik');
	define('SettingsDefaultTab', 'Alapértelmezett fül');
	define('SettingsCountry', 'Ország');
	define('SettingsTimeZone', 'Időzóna');
	define('SettingsAllTimeZones', 'Összes időzóna');

	define('WarningWorkdayStartsEnds', 'A \'Munkanapok végződnek\' időpontnak későbbinek kell lennie, mint a \'Munkanapok kezdődnek\'');
	define('ReportSettingsUpdated', 'A beállítások sikeresen elmentve.');

	define('SettingsTabCalendar', 'Naptár');

	define('FullMonthJanuary', 'Január');
	define('FullMonthFebruary', 'Február');
	define('FullMonthMarch', 'Március');
	define('FullMonthApril', 'Április');
	define('FullMonthMay', 'Május');
	define('FullMonthJune', 'Június');
	define('FullMonthJuly', 'Július');
	define('FullMonthAugust', 'Augusztus');
	define('FullMonthSeptember', 'Szeptember');
	define('FullMonthOctober', 'Október');
	define('FullMonthNovember', 'November');
	define('FullMonthDecember', 'December');
	
	define('ShortMonthJanuary', 'Jan');
	define('ShortMonthFebruary', 'Feb');
	define('ShortMonthMarch', 'Már');
	define('ShortMonthApril', 'Ápr');
	define('ShortMonthMay', 'Máj');
	define('ShortMonthJune', 'Jún');
	define('ShortMonthJuly', 'Júl');
	define('ShortMonthAugust', 'Aug');
	define('ShortMonthSeptember', 'Szep');
	define('ShortMonthOctober', 'Okt');
	define('ShortMonthNovember', 'Nov');
	define('ShortMonthDecember', 'Dec');

	define('FullDayMonday', 'Hétfő');
	define('FullDayTuesday', 'Kedd');
	define('FullDayWednesday', 'Szerda');
	define('FullDayThursday', 'Csütörtök');
	define('FullDayFriday', 'Péntek');
	define('FullDaySaturday', 'Szombat');
	define('FullDaySunday', 'Vasárnap');

	define('DayToolMonday', 'Hét');
	define('DayToolTuesday', 'Ked');
	define('DayToolWednesday', 'Szer');
	define('DayToolThursday', 'Csüt');
	define('DayToolFriday', 'Pén');
	define('DayToolSaturday', 'Szo');
	define('DayToolSunday', 'Vas');

	define('CalendarTableDayMonday', 'H');
	define('CalendarTableDayTuesday', 'K');
	define('CalendarTableDayWednesday', 'SZ');
	define('CalendarTableDayThursday', 'CS');
	define('CalendarTableDayFriday', 'P');
	define('CalendarTableDaySaturday', 'SZo');
	define('CalendarTableDaySunday', 'V');

	define('ErrorParseJSON', 'A JSON rendszer kimenetének értelmezése közben hiba lépett fel.');

	define('ErrorLoadCalendar', 'Nem lehetséges a naptárak betöltése');
	define('ErrorLoadEvents', 'Nem lehetséges az események betöltése');
	define('ErrorUpdateEvent', 'Nem lehetséges az esemény mentése');
	define('ErrorDeleteEvent', 'Nem lehetséges az esemény törlése');
	define('ErrorUpdateCalendar', 'Nem lehetséges a naptár elmentése');
	define('ErrorDeleteCalendar', 'Nem lehetséges a naptár törlése');
	define('ErrorGeneral', 'Hiba történt a szerveren, próbálkozzon később.');

define('BackToCart', 'Back to administration panel');
define('StoreWebmail', 'Store webmail');