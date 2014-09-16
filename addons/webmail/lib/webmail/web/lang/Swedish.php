<?php
// MailBee Webmail 4.x Swedish Resource strings
// Translation: Peter Strömblad, http://webbhotell.praktit.se
// Revision: 2007-10-31

// core strings
	define('PROC_ERROR_ACCT_CREATE', 'Ett fel uppstod vid skapandet av kontot');
	define('PROC_WRONG_ACCT_PWD', 'Fel lösenord');
	define('PROC_CANT_LOG_NONDEF', 'Kan ej logga in på annat än default konto');
	define('PROC_CANT_INS_NEW_FILTER', 'Kan ej infoga nytt filter');
	define('PROC_FOLDER_EXIST', 'Mapp finns redan');
	define('PROC_CANT_CREATE_FLD', 'Kan ej skapa mapp');
	define('PROC_CANT_INS_NEW_GROUP', 'Kan ej skapa ny grupp');
	define('PROC_CANT_INS_NEW_CONT', 'Kan ej infoga ny kontakt');
	define('PROC_CANT_INS_NEW_CONTS', 'Kan ej infoga nya kontakter');
	define('PROC_CANT_ADD_NEW_CONT_TO_GRP', 'Kan ej skapa nya kontakt/er i grupp');
	define('PROC_ERROR_ACCT_UPDATE', 'Ett fel uppstod vid uppdatering av kontot');
	define('PROC_CANT_UPDATE_CONT_SETTINGS', 'Kunde ej uppdatera kontakts inställningar');
	define('PROC_CANT_GET_SETTINGS', 'Kunde ej hämta inställningar');
	define('PROC_CANT_UPDATE_ACCT', 'Kunde ej uppdatera kontot');
	define('PROC_ERROR_DEL_FLD', 'Ett fel uppstod vid radering av mapp');
	define('PROC_CANT_UPDATE_CONT', 'Kunde ej uppdatera kontakt');
	define('PROC_CANT_GET_FLDS', 'Kunde ej hämta mappstruktur');
	define('PROC_CANT_GET_MSG_LIST', 'Kunde ej hämta meddelandelista');
	define('PROC_MSG_HAS_DELETED', 'Detta meddelande har redan raderats från e-postservern');
	define('PROC_CANT_LOAD_CONT_SETTINGS', 'Kan ej hämta kontakts inställningar');
	define('PROC_CANT_LOAD_SIGNATURE', 'Kan ej hämta kontosignatur');
	define('PROC_CANT_GET_CONT_FROM_DB', 'Kan ej hämta kontakt från databas');
	define('PROC_CANT_GET_CONTS_FROM_DB', 'Kan ej hämta kontakter från databas');
	define('PROC_CANT_DEL_ACCT_BY_ID', 'Kan ej radera konto med ID');
	define('PROC_CANT_DEL_FILTER_BY_ID', 'Kan ej radera filter med id');
	define('PROC_CANT_DEL_CONT_GROUPS', 'Kan ej radera kontakt/er och/eller grupper');
	define('PROC_WRONG_ACCT_ACCESS', 'Ett intrångsförsök mot annans konto upptäcktes.');
	define('PROC_SESSION_ERROR', 'Föregående session avbröts pga tidsgräns.');

	define('MailBoxIsFull', 'Brevlådan är full');
	define('WebMailException', 'WebbMail undantagsfel uppstod');
	define('InvalidUid', 'Ogiltigt meddelande UID (unik identifierare)');
	define('CantCreateContactGroup', 'Kan ej skapa kontaktgrupp');
	define('CantCreateUser', 'Kan ej skapa användare');
	define('CantCreateAccount', 'Kan ej skapa konto');
	define('SessionIsEmpty', 'Sessionen är tom');
	define('FileIsTooBig', 'Filen är för stor');

	define('PROC_CANT_MARK_ALL_MSG_READ', 'Kan ej markera alla meddelanden som lästa');
	define('PROC_CANT_MARK_ALL_MSG_UNREAD', 'Kan ej markera alla meddelanden som ej lästa');
	define('PROC_CANT_PURGE_MSGS', 'Kan ej radera meddelande/n');
	define('PROC_CANT_DEL_MSGS', 'Kan ej ta bort meddelande/n');
	define('PROC_CANT_UNDEL_MSGS', 'Kan ej återta meddelande/n');
	define('PROC_CANT_MARK_MSGS_READ', 'Kan ej markera meddelande/n som lästa');
	define('PROC_CANT_MARK_MSGS_UNREAD', 'Kan ej markera meddelande/n som olästa');
	define('PROC_CANT_SET_MSG_FLAGS', 'Kan ej sätta meddelandeflagga');
	define('PROC_CANT_REMOVE_MSG_FLAGS', 'Kan ej ta bort meddelandeflagga');
	define('PROC_CANT_CHANGE_MSG_FLD', 'Kan ej ändra meddelandemapp');
	define('PROC_CANT_SEND_MSG', 'kan ej skicka meddelande.');
	define('PROC_CANT_SAVE_MSG', 'Kan ej spara meddelande.');
	define('PROC_CANT_GET_ACCT_LIST', 'Kan ej hämta kontoförteckning');
	define('PROC_CANT_GET_FILTER_LIST', 'Kan ej hämta filterförteckning');

	define('PROC_CANT_LEAVE_BLANK', 'Fält med * måste fyllas i');

	define('PROC_CANT_UPD_FLD', 'Kan ej uppdatera mapp');
	define('PROC_CANT_UPD_FILTER', 'Kan ej uppdatera filter');

	define('ACCT_CANT_ADD_DEF_ACCT', 'Detta konto kan ej läggas till eftersom det används som standardkonto av annan användare.');
	define('ACCT_CANT_UPD_TO_DEF_ACCT', 'Detta kontos status kan ej ändras till standardkonto.');
	define('ACCT_CANT_CREATE_IMAP_ACCT', 'Kan ej skapa nytt konto (IMAP4 förbindelsefel)');
	define('ACCT_CANT_DEL_LAST_DEF_ACCT', 'Kan ej radera standardkonto');

	define('LANG_LoginInfo', 'Login information');
	define('LANG_Email', 'Epostadress');
	define('LANG_Login', 'Login');
	define('LANG_Password', 'Lösenord');
	define('LANG_IncServer', 'Inkommande Epostserver');
	define('LANG_PopProtocol', 'POP3');
	define('LANG_ImapProtocol', 'IMAP4');
	define('LANG_IncPort', 'Port');
	define('LANG_OutServer', 'Utgående Epostserver (SMTP)');
	define('LANG_OutPort', 'Port');
	define('LANG_UseSmtpAuth', 'Använd SMTP autentisering');
	define('LANG_SignMe', 'Logga in mig automatiskt');
	define('LANG_Enter', 'Enter');

// interface strings
	define('JS_LANG_TitleLogin', 'Login');
	define('JS_LANG_TitleMessagesListView', 'Meddelandelista');
	define('JS_LANG_TitleMessagesList', 'Meddelandelista');
	define('JS_LANG_TitleViewMessage', 'Visa Meddelande');
	define('JS_LANG_TitleNewMessage', 'Nytt Meddelande');
	define('JS_LANG_TitleSettings', 'Inställningar');
	define('JS_LANG_TitleContacts', 'Kontakter');

	define('JS_LANG_StandardLogin', 'Standard Inloggning');
	define('JS_LANG_AdvancedLogin', 'Avancerad Inloggning');

	define('JS_LANG_InfoWebMailLoading', 'Vänligen vänta, laddar&hellip;');
	define('JS_LANG_Loading', 'Laddar&hellip;');
	define('JS_LANG_InfoMessagesLoad', 'Vänligen vänta, laddar meddelandelista');
	define('JS_LANG_InfoEmptyFolder', 'Mappen är tom');
	define('JS_LANG_InfoPageLoading', 'Sidan laddas...');
	define('JS_LANG_InfoSendMessage', 'Meddelandet har skickats');
	define('JS_LANG_InfoSaveMessage', 'Meddelandet har sparats');
	define('JS_LANG_InfoHaveImported', 'Du har importerat');
	define('JS_LANG_InfoNewContacts', 'nya kontakt/er i din kontaktlista.');
	define('JS_LANG_InfoToDelete', 'För att radera ');
	define('JS_LANG_InfoDeleteContent', ' mappen måste du tömma dess innehåll först.');
	define('JS_LANG_InfoDeleteNotEmptyFolders', 'Att radera mappar med innehåll tillåts ej. För att radera omarkerade mappar, töm deras innehåll först.');
	define('JS_LANG_InfoRequiredFields', '* fält som krävs');

	define('JS_LANG_ConfirmAreYouSure', 'Är du säker?');
	define('JS_LANG_ConfirmDirectModeAreYouSure', 'Valda meddelande/n kommer att raderas permanent! Är du säker?');
	define('JS_LANG_ConfirmSaveSettings', 'Inställningarna sparades ej. Välj OK för att spara.');
	define('JS_LANG_ConfirmSaveContactsSettings', 'Kontaktinställningarna sparades ej. Välj OK för att spara.');
	define('JS_LANG_ConfirmSaveAcctProp', 'Kontots inställningar sparades ej. Välj OK för att spara.');
	define('JS_LANG_ConfirmSaveFilter', 'Filterinställningarna sparades ej. Välj OK för att spara.');
	define('JS_LANG_ConfirmSaveSignature', 'Signaturen sparades ej. Välj OK för att spara.');
	define('JS_LANG_ConfirmSavefolders', 'Mappen/arna sparades ej. Välj OK för att spara.');
	define('JS_LANG_ConfirmHtmlToPlain', 'Varning: Genom att ändra meddelandeformatet från HTML till text, så förloras nuvarande utformning. Välj OK för att verkställa.');
	define('JS_LANG_ConfirmAddFolder', 'Före mapp kan läggas till är det nödvändigt att verkställa förändringar. Välj OK för att spara.');
	define('JS_LANG_ConfirmEmptySubject', 'Titelraden är tom. Vill du fortsätta?');

	define('JS_LANG_WarningEmailBlank', 'Avsändarfältet får ej vara tomt');
	define('JS_LANG_WarningLoginBlank', 'Inloggningsfältet får ej vara tomt');
	define('JS_LANG_WarningToBlank', 'Till-fältet får ej vara tomt');
	define('JS_LANG_WarningServerPortBlank', 'POP3 och SMTP/Port fälten får ej vara tomma');
	define('JS_LANG_WarningEmptySearchLine', 'Söksträng tom. Vänligen fyll i söksträng');
	define('JS_LANG_WarningMarkListItem', 'Vänligen markera minst en i listan');
	define('JS_LANG_WarningFolderMove', 'Mappen kan ej flyttas pga nivå');
	define('JS_LANG_WarningContactNotComplete', 'Fyll i namn eller epostadress');
	define('JS_LANG_WarningGroupNotComplete', 'Fyll i gruppens namn');

	define('JS_LANG_WarningEmailFieldBlank', 'Fältet Epost kan ej vara tomt');
	define('JS_LANG_WarningIncServerBlank', 'Fältet POP3(IMAP4) Server får ej vara tomt');
	define('JS_LANG_WarningIncPortBlank', 'Fältet POP3(IMAP4) Serverport får ej vara tomt');
	define('JS_LANG_WarningIncLoginBlank', 'Fältet POP3(IMAP4) inloggning kan ej vara tomt');
	define('JS_LANG_WarningIncPortNumber', 'Fältet POP3(IMAP4) serverport måste vara positivt heltal.');
	define('JS_LANG_DefaultIncPortNumber', 'Standardport för POP3(IMAP4) är 110(143).');
	define('JS_LANG_WarningIncPassBlank', 'Fältet POP3(IMAP4) lösenord får ej vara tomt.');
	define('JS_LANG_WarningOutPortBlank', 'Fältet SMTP Server Port får ej vara blankt.');
	define('JS_LANG_WarningOutPortNumber', 'Fältet SMTP Server Port måste vara positivt heltal.');
	define('JS_LANG_WarningCorrectEmail', 'Du måste ange korrekt epostadress.');
	define('JS_LANG_DefaultOutPortNumber', 'Standardport för SMTP är 25.');

	define('JS_LANG_WarningCsvExtention', 'Filändelsen ska vara .csv');
	define('JS_LANG_WarningImportFileType', 'Välj det program som du vill kopiera dina kontakter från.');
	define('JS_LANG_WarningEmptyImportFile', 'välj en fil genom att klicka på sök-knappen');

	define('JS_LANG_WarningContactsPerPage', 'Kontakter per sida ska vara ett positivt heltal');
	define('JS_LANG_WarningMessagesPerPage', 'Meddelanden per sida ska vara ett positivt heltal');
	define('JS_LANG_WarningMailsOnServerDays', 'Du måste ange ett positivt heltal för Meddelanden på servern per dag.');
	define('JS_LANG_WarningEmptyFilter', 'Ange substräng');
	define('JS_LANG_WarningEmptyFolderName', 'Ange mappens namn');

	define('JS_LANG_ErrorConnectionFailed', 'Förbindelsen fallerade');
	define('JS_LANG_ErrorRequestFailed', 'Dataöverföringen har inte fullförts');
	define('JS_LANG_ErrorAbsentXMLHttpRequest', 'Objektet XMLHttpRequest saknas');
	define('JS_LANG_ErrorWithoutDesc', 'Okänt fel');
	define('JS_LANG_ErrorParsing', 'Fel vid tolkning av XML.');
	define('JS_LANG_ResponseText', 'Svarsmeddelande:');
	define('JS_LANG_ErrorEmptyXmlPacket', 'Tomt XML paket');
	define('JS_LANG_ErrorImportContacts', 'Fel vid import av kontakter');
	define('JS_LANG_ErrorNoContacts', 'Inga kontakter att importera');
	define('JS_LANG_ErrorCheckMail', 'Hämtning av meddelanden avbröts pga ett fel. Förmodligen hämtades ej alla meddelanden.');

	define('JS_LANG_LoggingToServer', 'Loggar in på server&hellip;');
	define('JS_LANG_GettingMsgsNum', 'Hämtar antal meddelanden');
	define('JS_LANG_RetrievingMessage', 'Hämtar meddelande');
	define('JS_LANG_DeletingMessage', 'Raderar meddelande');
	define('JS_LANG_DeletingMessages', 'Raderar meddelanden');
	define('JS_LANG_Of', 'av');
	define('JS_LANG_Connection', 'Förbindelse');
	define('JS_LANG_Charset', 'Charset');
	define('JS_LANG_AutoSelect', 'Auto-val');

	define('JS_LANG_Contacts', 'Kontakter');
	define('JS_LANG_ClassicVersion', 'Klassisk Version');
	define('JS_LANG_Logout', 'Logga ut');
	define('JS_LANG_Settings', 'Inställningar');

	define('JS_LANG_LookFor', 'Sök efter: ');
	define('JS_LANG_SearchIn', 'Sök i: ');
	define('JS_LANG_QuickSearch', 'Sök enbart i fälten Från, Till och ämnesrad (snabbast).');
	define('JS_LANG_SlowSearch', 'Sök hela meddelanden');
	define('JS_LANG_AllMailFolders', 'Alla mappar');
	define('JS_LANG_AllGroups', 'Alla grupper');

	define('JS_LANG_NewMessage', 'Nytt Meddelande');
	define('JS_LANG_CheckMail', 'Hämta Meddelanden');
	define('JS_LANG_ReloadFolders', 'Hämta Mappstruktur på nytt');
	define('JS_LANG_EmptyTrash', 'Töm papperskorgen');
	define('JS_LANG_MarkAsRead', 'Markera som läst');
	define('JS_LANG_MarkAsUnread', 'Markera som ej läst');
	define('JS_LANG_MarkFlag', 'Flagga');
	define('JS_LANG_MarkUnflag', 'Ta bort flagga');
	define('JS_LANG_MarkAllRead', 'Markera alla som lästa');
	define('JS_LANG_MarkAllUnread', 'Markera alla som olästa');
	define('JS_LANG_Reply', 'Svara');
	define('JS_LANG_ReplyAll', 'Svara alla');
	define('JS_LANG_Delete', 'Radera');
	define('JS_LANG_Undelete', 'Återta');
	define('JS_LANG_PurgeDeleted', 'Ta bort raderade');
	define('JS_LANG_MoveToFolder', 'Flytta till mapp');
	define('JS_LANG_Forward', 'Framåt');

	define('JS_LANG_HideFolders', 'Göm mappar');
	define('JS_LANG_ShowFolders', 'visa mappar');
	define('JS_LANG_ManageFolders', 'Hantera mappar');
	define('JS_LANG_SyncFolder', 'Synkroniserad mapp');
	define('JS_LANG_NewMessages', 'Nya Meddelanden');
	define('JS_LANG_Messages', 'Meddelande/n');

	define('JS_LANG_From', 'Från');
	define('JS_LANG_To', 'Till');
	define('JS_LANG_Date', 'Datum');
	define('JS_LANG_Size', 'Storlek');
	define('JS_LANG_Subject', 'Ämne');

	define('JS_LANG_FirstPage', 'Första sidan');
	define('JS_LANG_PreviousPage', 'Föregående sida');
	define('JS_LANG_NextPage', 'Nästa sida');
	define('JS_LANG_LastPage', 'Sista sidan');

	define('JS_LANG_SwitchToPlain', 'Visa som Oformaterad Text');
	define('JS_LANG_SwitchToHTML', 'Visa som HTML');
	define('JS_LANG_AddToAddressBokk', 'Lägg till i adressboken');
	define('JS_LANG_ClickToDownload', 'Klicka för att hämta');
	define('JS_LANG_View', 'Visa');
	define('JS_LANG_ShowFullHeaders', 'Visa fullständigt brevhuvud');
	define('JS_LANG_HideFullHeaders', 'Dölj fullständigt brevhuvud');

	define('JS_LANG_MessagesInFolder', 'Meddelanden i mapp');
	define('JS_LANG_YouUsing', 'Du använder');
	define('JS_LANG_OfYour', 'av dina');
	define('JS_LANG_Mb', 'MB');
	define('JS_LANG_Kb', 'KB');
	define('JS_LANG_B', 'B');

	define('JS_LANG_SendMessage', 'Skicka');
	define('JS_LANG_SaveMessage', 'Spara');
	define('JS_LANG_Print', 'Skriv ut');
	define('JS_LANG_PreviousMsg', 'Föregående meddelande');
	define('JS_LANG_NextMsg', 'Nästa meddelande');
	define('JS_LANG_AddressBook', 'Adressbok');
	define('JS_LANG_ShowBCC', 'Visa Hemlig kopia');
	define('JS_LANG_HideBCC', 'Dölj Hemlig kopia');
	define('JS_LANG_CC', 'Kopia');
	define('JS_LANG_BCC', 'Hemlig Kopia');
	define('JS_LANG_ReplyTo', 'Svara till');
	define('JS_LANG_AttachFile', 'Bifoga fil');
	define('JS_LANG_Attach', 'Bifoga');
	define('JS_LANG_Re', 'Sv');
	define('JS_LANG_OriginalMessage', 'Ursprungligt meddelande');
	define('JS_LANG_Sent', 'Skickat');
	define('JS_LANG_Fwd', 'Vidarebefordra');
	define('JS_LANG_Low', 'Låg');
	define('JS_LANG_Normal', 'Normal');
	define('JS_LANG_High', 'Hög');
	define('JS_LANG_Importance', 'Prioritet');
	define('JS_LANG_Close', 'Stäng');

	define('JS_LANG_Common', 'Vanliga');
	define('JS_LANG_EmailAccounts', 'Epostkonton');

	define('JS_LANG_MsgsPerPage', 'Meddelanden per sida');
	define('JS_LANG_DisableRTE', 'Deaktivera rich-text editor');
	define('JS_LANG_Skin', 'Utseende');
	define('JS_LANG_DefCharset', 'Ordinarie typsnitt');
	define('JS_LANG_DefCharsetInc', 'Ordinarie inkommande typsnitt');
	define('JS_LANG_DefCharsetOut', 'Ordinarie utgående typsnitt');
	define('JS_LANG_DefTimeOffset', 'Ordinare tidszon');
	define('JS_LANG_DefLanguage', 'Ordinarie språk');
	define('JS_LANG_DefDateFormat', 'Ordinarie datumformat');
	define('JS_LANG_ShowViewPane', 'Meddelanden visas med förhandsgranskning');
	define('JS_LANG_Save', 'Spara');
	define('JS_LANG_Cancel', 'Ångra');
	define('JS_LANG_OK', 'OK');

	define('JS_LANG_Remove', 'Ta bort');
	define('JS_LANG_AddNewAccount', 'Lägg till nytt konto');
	define('JS_LANG_Signature', 'Signatur');
	define('JS_LANG_Filters', 'Filter');
	define('JS_LANG_Properties', 'Inställningar');
	define('JS_LANG_UseForLogin', 'Använd detta kontos inställningar (login och lösenord) för inloggning');
	define('JS_LANG_MailFriendlyName', 'Ditt namn');
	define('JS_LANG_MailEmail', 'Epost');
	define('JS_LANG_MailIncHost', 'Inkommande Epost');
	define('JS_LANG_Imap4', 'Imap4');
	define('JS_LANG_Pop3', 'Pop3');
	define('JS_LANG_MailIncPort', 'Port');
	define('JS_LANG_MailIncLogin', 'Login');
	define('JS_LANG_MailIncPass', 'Lösenord');
	define('JS_LANG_MailOutHost', 'SMTP Server');
	define('JS_LANG_MailOutPort', 'Port');
	define('JS_LANG_MailOutLogin', 'SMTP Login');
	define('JS_LANG_MailOutPass', 'SMTP Lösenord');
	define('JS_LANG_MailOutAuth1', 'Använd SMTP autentisering');
	define('JS_LANG_MailOutAuth2', '(Lämna fälten login/lösen för att använda samma som POP3/IMAP4 login/lösen)');
	define('JS_LANG_UseFriendlyNm1', 'Använd ditt namn för att forma Från:');
	define('JS_LANG_UseFriendlyNm2', '(Ditt namn &lt;adress@din_doman.se&gt;)');
	define('JS_LANG_GetmailAtLogin', 'Hämta/Synkronisera meddelanden vid inloggning');
	define('JS_LANG_MailMode0', 'Radera hämtade meddelande från servern');
	define('JS_LANG_MailMode1', 'Låt meddelanden vara kvar på servern');
	define('JS_LANG_MailMode2', 'Låt meddelanden vara kvar på servern i ');
	define('JS_LANG_MailsOnServerDays', 'dag/ar');
	define('JS_LANG_MailMode3', 'Radera meddelanden från servern när papperskorgen töms.');
	define('JS_LANG_InboxSyncType', 'Inkorgens synkroniseringsmetod');

	define('JS_LANG_SyncTypeNo', 'Synkronisera ej');
	define('JS_LANG_SyncTypeNewHeaders', 'Nya meddelanderubriker');
	define('JS_LANG_SyncTypeAllHeaders', 'Alla meddelanderubriker');
	define('JS_LANG_SyncTypeNewMessages', 'Nya meddelanden');
	define('JS_LANG_SyncTypeAllMessages', 'Alla meddelanden');
	define('JS_LANG_SyncTypeDirectMode', 'Transparent läge');

	define('JS_LANG_Pop3SyncTypeEntireHeaders', 'Fullständiga brevhuvuden');
	define('JS_LANG_Pop3SyncTypeEntireMessages', 'Hela meddelanden');
	define('JS_LANG_Pop3SyncTypeDirectMode', 'Transparent läge');

	define('JS_LANG_DeleteFromDb', 'Radera meddelanden från databasen om de ej finns kvar på servern');

	define('JS_LANG_EditFilter', 'Redigera filter');
	define('JS_LANG_NewFilter', 'Skapa nytt filter');
	define('JS_LANG_Field', 'Fält');
	define('JS_LANG_Condition', 'Villkor');
	define('JS_LANG_ContainSubstring', 'Innehåller');
	define('JS_LANG_ContainExactPhrase', 'Exakt fras');
	define('JS_LANG_NotContainSubstring', 'Ej innehåller');
	define('JS_LANG_FilterDesc_At', 'vid');
	define('JS_LANG_FilterDesc_Field', 'fält');
	define('JS_LANG_Action', 'Utför');
	define('JS_LANG_DoNothing', 'Gör ingenting');
	define('JS_LANG_DeleteFromServer', 'Radera från servern omedelbart');
	define('JS_LANG_MarkGrey', 'Gråmarkera');
	define('JS_LANG_Add', 'Lägg till');
	define('JS_LANG_OtherFilterSettings', 'Andra filterinställningar');
	define('JS_LANG_ConsiderXSpam', 'Ta hänsyn till X-Spam flaggor');
	define('JS_LANG_Apply', 'Verkställ');

	define('JS_LANG_InsertLink', 'Infoga länk');
	define('JS_LANG_RemoveLink', 'Ta bort länk');
	define('JS_LANG_Numbering', 'Numrering');
	define('JS_LANG_Bullets', 'Punkter');
	define('JS_LANG_HorizontalLine', 'Horisontell linje');
	define('JS_LANG_Bold', 'Fet');
	define('JS_LANG_Italic', 'Kursiv');
	define('JS_LANG_Underline', 'Stryk under');
	define('JS_LANG_AlignLeft', 'Vänsterjustera');
	define('JS_LANG_Center', 'Centrera');
	define('JS_LANG_AlignRight', 'Högerjustera');
	define('JS_LANG_Justify', 'Anpassa');
	define('JS_LANG_FontColor', 'Fontfärg');
	define('JS_LANG_Background', 'Bakgrund');
	define('JS_LANG_SwitchToPlainMode', 'Byt till oformaterat text läge');
	define('JS_LANG_SwitchToHTMLMode', 'Byt till HTML läge');
	define('JS_LANG_AddSignatures', 'Infoga signatur till alla utgående brev');
	define('JS_LANG_DontAddToReplies', 'Infoga ej signatur till svarsbrev eller vidarebefordrade brev');

	define('JS_LANG_Folder', 'Mapp');
	define('JS_LANG_Msgs', 'Meddelanden,');
	define('JS_LANG_Synchronize', 'Synkronisera');
	define('JS_LANG_ShowThisFolder', 'Visa mapp');
	define('JS_LANG_Total', 'Totalt');
	define('JS_LANG_DeleteSelected', 'Radera markerade');
	define('JS_LANG_AddNewFolder', 'Lägg till mapp');
	define('JS_LANG_NewFolder', 'Ny mapp');
	define('JS_LANG_ParentFolder', 'Överordnad mapp');
	define('JS_LANG_NoParent', 'Överordnad mapp saknas');
	define('JS_LANG_OnMailServer', 'Skapa denna mapp i både webbmailklienten och på mailservern');
	define('JS_LANG_InWebMail', 'Skapa denna mapp enbart i webbmailklienten');
	define('JS_LANG_FolderName', 'Mappnamn');

	define('JS_LANG_ContactsPerPage', 'Kontakter per sida');
	define('JS_LANG_WhiteList', 'Adressbok som "vitlistad"');

	define('JS_LANG_CharsetDefault', 'Default');
	define('JS_LANG_CharsetArabicAlphabetISO', 'Arabiskt alfabet (ISO)');
	define('JS_LANG_CharsetArabicAlphabet', 'Arabiskt alfabet (Windows)');
	define('JS_LANG_CharsetBalticAlphabetISO', 'Baltiskt alfabet (ISO)');
	define('JS_LANG_CharsetBalticAlphabet', 'Baltiskt alfabet (Windows)');
	define('JS_LANG_CharsetCentralEuropeanAlphabetISO', 'Central Europeiskt alfabet (ISO)');
	define('JS_LANG_CharsetCentralEuropeanAlphabet', 'Central Europeiskt alfabet (Windows)');
	define('JS_LANG_CharsetChineseSimplifiedEUC', 'Chinese Simplified (EUC)');
	define('JS_LANG_CharsetChineseSimplifiedGB', 'Chinese Simplified (GB2312)');
	define('JS_LANG_CharsetChineseTraditional', 'Kinesiskt traditionellt (Big5)');
	define('JS_LANG_CharsetCyrillicAlphabetISO', 'Cyrilliskt alfabet (ISO)');
	define('JS_LANG_CharsetCyrillicAlphabetKOI8R', 'Cyrilliskt alfabet (KOI8-R)');
	define('JS_LANG_CharsetCyrillicAlphabet', 'Cyrilliskt alfabet (Windows)');
	define('JS_LANG_CharsetGreekAlphabetISO', 'Grekiskt alfabet (ISO)');
	define('JS_LANG_CharsetGreekAlphabet', 'Grekiskt alfabet (Windows)');
	define('JS_LANG_CharsetHebrewAlphabetISO', 'Hebreeiskt alfabet (ISO)');
	define('JS_LANG_CharsetHebrewAlphabet', 'Hebreeiskt alfabet (Windows)');
	define('JS_LANG_CharsetJapanese', 'Japanese');
	define('JS_LANG_CharsetJapaneseShiftJIS', 'Japanese (Shift-JIS)');
	define('JS_LANG_CharsetKoreanEUC', 'Koreanskt (EUC)');
	define('JS_LANG_CharsetKoreanISO', 'Koreanskt (ISO)');
	define('JS_LANG_CharsetLatin3AlphabetISO', 'Latin 3 alfabet (ISO)');
	define('JS_LANG_CharsetTurkishAlphabet', 'Turkiskt alfabet');
	define('JS_LANG_CharsetUniversalAlphabetUTF7', 'Universal alfabet (UTF-7)');
	define('JS_LANG_CharsetUniversalAlphabetUTF8', 'Universal alfabet (UTF-8)');
	define('JS_LANG_CharsetVietnameseAlphabet', 'Vietnamesiskt alfabet (Windows)');
	define('JS_LANG_CharsetWesternAlphabetISO', 'Western alfabet (ISO)');
	define('JS_LANG_CharsetWesternAlphabet', 'Western alfabet (Windows)');

	define('JS_LANG_TimeDefault', 'Standard');
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
	define('JS_LANG_TimeTonga', 'Nuku\'alofa, Tonga');

	define('JS_LANG_LanguageEnglish', 'Engelska');
	define('JS_LANG_LanguageCatala', 'Catalan');
	define('JS_LANG_LanguageNederlands', 'Holländska');
	define('JS_LANG_LanguageFrench', 'Franska');
	define('JS_LANG_LanguageGerman', 'Tyska');
	define('JS_LANG_LanguageItaliano', 'Italienska');
	define('JS_LANG_LanguagePortuguese', 'Portugisiska (BR)');
	define('JS_LANG_LanguageEspanyol', 'Spanska');
	define('JS_LANG_LanguageSwedish', 'Svenska');
	define('JS_LANG_LanguageTurkish', 'Turkiska');

	define('JS_LANG_DateDefault', 'Standard');
	define('JS_LANG_DateDDMMYY', 'DD/MM/YY');
	define('JS_LANG_DateMMDDYY', 'MM/DD/YY');
	define('JS_LANG_DateDDMonth', 'DD Month (01 Jan)');
	define('JS_LANG_DateAdvanced', 'Advanced');

	define('JS_LANG_NewContact', 'Ny kontakt');
	define('JS_LANG_NewGroup', 'Ny grupp');
	define('JS_LANG_AddContactsTo', 'Lägg till kontakter till');
	define('JS_LANG_ImportContacts', 'Importera kontakter');

	define('JS_LANG_Name', 'Namn');
	define('JS_LANG_Email', 'Epost');
	define('JS_LANG_DefaultEmail', 'Ordinarie epost');
	define('JS_LANG_NotSpecifiedYet', 'Ej angiven');
	define('JS_LANG_ContactName', 'Namn');
	define('JS_LANG_Birthday', 'Födelsedag');
	define('JS_LANG_Month', 'Månad');
	define('JS_LANG_January', 'Januari');
	define('JS_LANG_February', 'Februari');
	define('JS_LANG_March', 'Mars');
	define('JS_LANG_April', 'April');
	define('JS_LANG_May', 'Maj');
	define('JS_LANG_June', 'Juni');
	define('JS_LANG_July', 'Juli');
	define('JS_LANG_August', 'Augusti');
	define('JS_LANG_September', 'September');
	define('JS_LANG_October', 'Oktober');
	define('JS_LANG_November', 'November');
	define('JS_LANG_December', 'December');
	define('JS_LANG_Day', 'Dag');
	define('JS_LANG_Year', 'År');
	define('JS_LANG_UseFriendlyName1', 'Använd personens nanm');
	define('JS_LANG_UseFriendlyName2', '(t.ex. Peter S &lt;peter@din_doman.se&gt;)');
	define('JS_LANG_Personal', 'Privat');
	define('JS_LANG_PersonalEmail', 'Privat Epostadress');
	define('JS_LANG_StreetAddress', 'Gata');
	define('JS_LANG_City', 'Stad');
	define('JS_LANG_Fax', 'Fax');
	define('JS_LANG_StateProvince', 'Stat/Provins');
	define('JS_LANG_Phone', 'Telefno');
	define('JS_LANG_ZipCode', 'Postnr');
	define('JS_LANG_Mobile', 'Mobil');
	define('JS_LANG_CountryRegion', 'Land');
	define('JS_LANG_WebPage', 'Hemsida');
	define('JS_LANG_Go', 'Kör');
	define('JS_LANG_Home', 'Hem');
	define('JS_LANG_Business', 'Arbete');
	define('JS_LANG_BusinessEmail', 'Företags Epostadress');
	define('JS_LANG_Company', 'Företag');
	define('JS_LANG_JobTitle', 'Titel');
	define('JS_LANG_Department', 'Avdelning');
	define('JS_LANG_Office', 'Kontor');
	define('JS_LANG_Pager', 'Sökare');
	define('JS_LANG_Other', 'Annat');
	define('JS_LANG_OtherEmail', 'Annan Epostadress');
	define('JS_LANG_Notes', 'Anteckningar');
	define('JS_LANG_Groups', 'Grupper');
	define('JS_LANG_ShowAddFields', 'Visa ytterligare fält');
	define('JS_LANG_HideAddFields', 'Dölj ytterligare fält');
	define('JS_LANG_EditContact', 'redigera kontaktinformation');
	define('JS_LANG_GroupName', 'Gruppnamn');
	define('JS_LANG_AddContacts', 'Lägg till kontakter');
	define('JS_LANG_CommentAddContacts', '(Om du vill ange mer än en adress, separera dem med kommatecken)');
	define('JS_LANG_CreateGroup', 'Skapa grupp');
	define('JS_LANG_Rename', 'Döp om');
	define('JS_LANG_MailGroup', 'Epostgrupp');
	define('JS_LANG_RemoveFromGroup', 'Ta bort från grupp');
	define('JS_LANG_UseImportTo', 'Använd import för att läsa in dina kontakter från Microsoft Outlook, Microsoft Outlook Express till din kontaktlista.');
	define('JS_LANG_Outlook1', 'Microsoft Outlook 2000/XP/2003');
	define('JS_LANG_Outlook2', 'Microsoft Outlook Express 6');
	define('JS_LANG_SelectImportFile', 'Välj fil (.CSV format) som du vill importera från');
	define('JS_LANG_Import', 'Importera');
	define('JS_LANG_ContactsMessage', 'Detta är kontaktsidan.');
	define('JS_LANG_ContactsCount', 'kontakt/er');
	define('JS_LANG_GroupsCount', 'grupp/er');

//webmail 4.1 constants
	define('PicturesBlocked', 'Bilder har i detta meddelande blockerats för din säkerhet');
	define('ShowPictures', 'Visa bilder');
	define('ShowPicturesFromSender', 'Visa alltid bilder i meddelanden från denna avsändare');
	define('AlwaysShowPictures', 'Visa alltid bilder i meddelanden');

	define('TreatAsOrganization', 'Behandla som en organisation');

	define('WarningGroupAlreadyExist', 'Grupp med detta namn finns redan. Uppge ett annat namn.');
	define('WarningCorrectFolderName', 'Du måste ange ett korrekt namn för mappen.');
	define('WarningLoginFieldBlank', 'You cannot leave Login field blank.');
	define('WarningCorrectLogin', 'Du måste ange en korrekt inloggning');
	define('WarningPassBlank', 'Du kan inte låta lösenordsfältet vara tomt.');
	define('WarningCorrectIncServer', 'Du måste ange en giltig POP3(IMAP) serveradress.');
	define('WarningCorrectSMTPServer', 'Du måste ange en korrekt SMTP serveradress.');
	define('WarningFromBlank', 'Du kan inte lämna fältet Från tomt.');
	define('WarningAdvancedDateFormat', 'Uppge ett tids och datumformat.');

	define('AdvancedDateHelpTitle', 'Avancerade datuminställningar');
	define('AdvancedDateHelpIntro', 'När &quot;avancerade datuminställningar&quot; är valda, kan du ange ett eget datumformat, vilket anges som med \':\' och \'/\' som avskiljare:');
	define('AdvancedDateHelpConclusion', 'Till exempel, om du anger &quot;yyyy/mm/dd&quot; visas datum som år/månad/dag (ex.vis 2007/10/30).');
	define('AdvancedDateHelpDayOfMonth', 'Dag i månaden (1 till 31)');
	define('AdvancedDateHelpNumericMonth', 'Månad (1 till 12)');
	define('AdvancedDateHelpTextualMonth', 'Månad (Jan till Dec)');
	define('AdvancedDateHelpYear2', 'År, 2 siffror');
	define('AdvancedDateHelpYear4', 'År, 4 siffror');
	define('AdvancedDateHelpDayOfYear', 'Dag på året (1 till 366)');
	define('AdvancedDateHelpQuarter', 'Kvartal');
	define('AdvancedDateHelpDayOfWeek', 'Veckodag (1 till 7)');
	define('AdvancedDateHelpWeekOfYear', 'Kalendervecka (1 till 53)');

	define('InfoNoMessagesFound', 'Inga meddelanden funna');
	define('ErrorSMTPConnect', 'Kan ej ansluta till SMTP-server. Kontrollera SMTP-inställningarna.');
	define('ErrorSMTPAuth', 'Fel användarnanm och/eller lösenord. Autentisering misslyckades.');
	define('ReportMessageSent', 'Ditt meddelande har skickats.');
	define('ReportMessageSaved', 'Ditt meddelande har sparats.');
	define('ErrorPOP3Connect', 'Kan ej ansluta till POP3-servern. Kontrollera POP3-inställningarna.');
	define('ErrorIMAP4Connect', 'Kan ej ansluta till IMAP4-servern. Kontrollera IMAP4-inställningarna.');
	define('ErrorPOP3IMAP4Auth', 'Fel epost/login och/eller lösenord. Autentisering misslyckades.');
	define('ErrorGetMailLimit', 'Förlåt, din brevlåda är full.');

	define('ReportSettingsUpdatedSuccessfuly', 'Inställningarna har uppdaterats.');
	define('ReportAccountCreatedSuccessfuly', 'Kontot har skapats.');
	define('ReportAccountUpdatedSuccessfuly', 'Kontot har uppdaterats.');
	define('ConfirmDeleteAccount', 'Är du säker på att du vill ta bort kontot?');
	define('ReportFiltersUpdatedSuccessfuly', 'Filterinställningar har uppdaterats.');
	define('ReportSignatureUpdatedSuccessfuly', 'Signaturen har uppdaterats.');
	define('ReportFoldersUpdatedSuccessfuly', 'Mappar har uppdaterats.');
	define('ReportContactsSettingsUpdatedSuccessfuly', 'Kontaktens inställningar har uppdaterats.');

	define('ErrorInvalidCSV', '.CSV filen du angav har felaktigt format.');
//The group "guies" was successfully added.
	define('ReportGroupSuccessfulyAdded1', 'Gruppen');
	define('ReportGroupSuccessfulyAdded2', 'lades till.');
	define('ReportGroupUpdatedSuccessfuly', 'Gruppen lades till.');
	define('ReportContactSuccessfulyAdded', 'Kontakt lades till.');
	define('ReportContactUpdatedSuccessfuly', 'Kontakten har uppdaterats.');
//Contact(s) was added to group "friends".
	define('ReportContactAddedToGroup', 'Kontakt/er lades till gruppen');
	define('AlertNoContactsGroupsSelected', 'Inga kontakter eller grupper valda.');

	define('InfoListNotContainAddress', 'Om listan inte innehåller adressen du letar efter, försök med de inledande bokstäverna.');

	define('DirectAccess', 'T');
	define('DirectAccessTitle', 'Transparent läge. Webbmail hanterar meddelanden direkt på e-postservern.');

	define('FolderInbox', 'Inkorgen');
	define('FolderSentItems', 'Skickat');
	define('FolderDrafts', 'Utkast');
	define('FolderTrash', 'Papperskorgen');

	define('LanguageDanish', 'Danska');
	define('LanguagePolish', 'Polska');

	define('FileLargerAttachment', 'Filen är större än tillåten storlek för bilagor.');
	define('FilePartiallyUploaded', 'Filen bifogades inte i sin helhet pga ett okänt fel.');
	define('NoFileUploaded', 'Ingen fil bifogades.');
	define('MissingTempFolder', 'Temporär katalog saknas.');
	define('MissingTempFile', 'Temporär fil saknas.');
	define('UnknownUploadError', 'Ett okänt fel inträffade vid hämtning av bifogad fil.');
	define('FileLargerThan', 'Fel vid bifoga fil. Troligen pga att filen är större än');
	define('PROC_CANT_LOAD_DB', 'Kan ej ansluta till databasen.');
	define('PROC_CANT_LOAD_LANG', 'Kan ej hitta begärd språkfil.');
	define('PROC_CANT_LOAD_ACCT', 'Kontot finns inte, troligen har det raderats.');

	define('DomainDosntExist', 'Domänen finns ej på servern.');
	define('ServerIsDisable', 'E-postservern är tillfälligt stängd av administratören.');

	define('PROC_ACCOUNT_EXISTS', 'Kontot kan ej skapas eftersom det redan existerar.');
	define('PROC_CANT_GET_MESSAGES_COUNT', 'Fel i hämtning av antalet meddelanden.');
	define('PROC_CANT_MAIL_SIZE', 'Fel i hämtning av utrymmesbegränsning.');

	define('Organization', 'Organisation');
	define('WarningOutServerBlank', 'Fältet SMTP Server får ej vara tomt');

//
	define('JS_LANG_Refresh', 'Uppdatera');
	define('JS_LANG_MessagesInInbox', 'Meddelande/n i inkorgen');
	define('JS_LANG_InfoEmptyInbox', 'Inkorgen är tom');

// webmail 4.2 constants
	define('LanguagePortugueseBrazil', 'Portuguese-Brazil');
	define('LanguageHungarian', 'Ungerska');

	define('BackToList', 'Tillbaka till listan');
	define('InfoNoContactsGroups', 'Inga kontakter eller grupper.');
	define('InfoNewContactsGroups', 'Du kan skapa nya kontakter/grupper eller importera kontakter från en .CSV-fil i Outlook format.');
	define('DefTimeFormat', 'Standard tidsformat');
	define('SpellNoSuggestions', 'Inga förslag');
	define('SpellWait', 'Vänligen väntat&hellip;');

	define('InfoNoMessageSelected', 'Inga meddelanden valda.');
	define('InfoSingleDoubleClick', 'Enkelklicka för att förhandsgranska eller dubbelklicka för att se meddelandet i full storlek.');
        
// calendar
	define('TitleDay', 'Dagsvy');
	define('TitleWeek', 'Veckovy');
	define('TitleMonth', 'Månadsvy');

	define('ErrorNotSupportBrowser', 'AfterLogics kalender stödjer inte din läsare. Använd lägst FireFox 2.0/Opera 9.0/Internet Explorer 6.0/Safari 3.0.2 eller senare version.');
	define('ErrorTurnedOffActiveX', 'Stöd för ActiveX är avstängt. <br/>För att använda denna tillämpning måste ActiveX tillåtas.');

	define('Calendar', 'Kalender');
        
	define('TabDay', 'Dag');
	define('TabWeek', 'Vecka');
	define('TabMonth', 'Månad');
        
	define('ToolNewEvent', 'Ny&nbsp;avtalad&nbsp;tid');
	define('ToolBack', 'Tillbaka');
	define('ToolToday', 'Idag');
	define('AltNewEvent', 'Ny avtalad tid');
	define('AltBack', 'Tillbaka');
	define('AltToday', 'Idag');
	define('CalendarHeader', 'Kalender');
	define('CalendarsManager', 'Kalenderansvarig');
        
	define('CalendarActionNew', 'Ny kalender');
	define('EventHeaderNew', 'Ny avtalad tid');
	define('CalendarHeaderNew', 'Ny kalender');
        
	define('EventSubject', 'Ämne');
	define('EventCalendar', 'Kalender');
	define('EventFrom', 'Från');
	define('EventTill', 'till');
	define('CalendarDescription', 'Beskrivning');
	define('CalendarColor', 'Färg');
	define('CalendarName', 'Kalendernamn');
	define('CalendarDefaultName', 'Min kalender');
        
	define('ButtonSave', 'Spara');
	define('ButtonCancel', 'Ångra');
	define('ButtonDelete', 'Radera');

	define('AltPrevMonth', 'Föregående månad');
	define('AltNextMonth', 'Nästa månad');

	define('CalendarHeaderEdit', 'Redigera kalender');
	define('CalendarActionEdit', 'Redigera kalender');
	define('ConfirmDeleteCalendar', 'Är du säker på att du vill radera kalendern');
	define('InfoDeleting', 'Raderar...');
	define('WarningCalendarNameBlank', 'Kalendernamnet får ej vara tomt.');
	define('ErrorCalendarNotCreated', 'Kalender skapades ej.');
	define('WarningSubjectBlank', 'Ämnet kan ej vara blankt.');
	define('WarningIncorrectTime', 'Specificerad tid består av ogiltiga tidstecken.');
	define('WarningIncorrectFromTime', 'Från-tiden är felaktig.');
	define('WarningIncorrectTillTime', 'Till-tiden är felaktig.');
	define('WarningStartEndDate', 'Slutdatum måste vara efter eller lika med startdatum.');
	define('WarningStartEndTime', 'Sluttid måste vara senare än starttid.');
	define('WarningIncorrectDate', 'Datum är felaktig.');
	define('InfoLoading', 'Laddar...');
	define('EventCreate', 'Skapa avtalad tid');
	define('CalendarHideOther', 'Göm andra kalendrar');
	define('CalendarShowOther', 'Visa andra kalendrar');
	define('CalendarRemove', 'Ta bort kalender');
	define('EventHeaderEdit', 'Redigera avtalad tid');

	define('InfoSaving', 'Sparar...');
	define('SettingsDisplayName', 'Visningsnamn');
	define('SettingsTimeFormat', 'Tidsformat');
	define('SettingsDateFormat', 'Datumformat');
	define('SettingsShowWeekends', 'Visa helger');
	define('SettingsWorkdayStarts', 'Arbetsdag startar');
	define('SettingsWorkdayEnds', 'slutar');
	define('SettingsShowWorkday', 'Visa arbetsdagar');
	define('SettingsWeekStartsOn', 'Vecka startar på');
	define('SettingsDefaultTab', 'Standardflik');
	define('SettingsCountry', 'Land');
	define('SettingsTimeZone', 'Tidszon');
	define('SettingsAllTimeZones', 'Alla tidszoner');

	define('WarningWorkdayStartsEnds', 'Tid för \'Arbetsdag slutar\' måste vara större än tiden då arbetsdag börjar');
	define('ReportSettingsUpdated', 'Inställningarna har sparats.');

	define('SettingsTabCalendar', 'Kalender');

	define('FullMonthJanuary', 'Januari');
	define('FullMonthFebruary', 'Februari');
	define('FullMonthMarch', 'Mars');
	define('FullMonthApril', 'April');
	define('FullMonthMay', 'Maj');
	define('FullMonthJune', 'Juni');
	define('FullMonthJuly', 'Juli');
	define('FullMonthAugust', 'Augusti');
	define('FullMonthSeptember', 'September');
	define('FullMonthOctober', 'Oktober');
	define('FullMonthNovember', 'November');
	define('FullMonthDecember', 'December');
        
	define('ShortMonthJanuary', 'Jan');
	define('ShortMonthFebruary', 'Feb');
	define('ShortMonthMarch', 'Mar');
	define('ShortMonthApril', 'Apr');
	define('ShortMonthMay', 'Maj');
	define('ShortMonthJune', 'Jun');
	define('ShortMonthJuly', 'Jul');
	define('ShortMonthAugust', 'Aug');
	define('ShortMonthSeptember', 'Sep');
	define('ShortMonthOctober', 'Okt');
	define('ShortMonthNovember', 'Nov');
	define('ShortMonthDecember', 'Dec');      

	define('FullDayMonday', 'Måndag');
	define('FullDayTuesday', 'Tisdag');
	define('FullDayWednesday', 'Onsdag');
	define('FullDayThursday', 'Torsdag');
	define('FullDayFriday', 'Fredag');
	define('FullDaySaturday', 'Lördag');
	define('FullDaySunday', 'SÖndag');

	define('DayToolMonday', 'Mån');
	define('DayToolTuesday', 'Tis');
	define('DayToolWednesday', 'Ons');
	define('DayToolThursday', 'Tor');
	define('DayToolFriday', 'Fre');
	define('DayToolSaturday', 'Lör');
	define('DayToolSunday', 'Sön');

	define('CalendarTableDayMonday', 'M');
	define('CalendarTableDayTuesday', 'T');
	define('CalendarTableDayWednesday', 'O');
	define('CalendarTableDayThursday', 'T');
	define('CalendarTableDayFriday', 'F');
	define('CalendarTableDaySaturday', 'L');
	define('CalendarTableDaySunday', 'S');

	define('ErrorParseJSON', 'JSON-svar från servern kan ej tolkas.');

	define('ErrorLoadCalendar', 'Kan ej hämta kalender');
	define('ErrorLoadEvents', 'Kan ej hämta möte');
	define('ErrorUpdateEvent', 'Kan ej spara möte');
	define('ErrorDeleteEvent', 'Kan ej radera möte');
	define('ErrorUpdateCalendar', 'Kan ej spara kalender');
	define('ErrorDeleteCalendar', 'Kan ej radera kalender');
	define('ErrorGeneral', 'Ett fel inträffade på servern. Vsv försök senare.');

define('BackToCart', 'Back to administration panel');
define('StoreWebmail', 'Store webmail');