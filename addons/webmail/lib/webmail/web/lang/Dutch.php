<?php
	define('PROC_ERROR_ACCT_CREATE', 'Er is een fout gebeurd bij het maken van uw account');
	define('PROC_WRONG_ACCT_PWD', 'Verkeerd paswoord');
	define('PROC_CANT_LOG_NONDEF', 'Kan niet inloggen in niet-standaard account');
	define('PROC_CANT_INS_NEW_FILTER', 'Kan nieuwe filter niet opslaan');
	define('PROC_FOLDER_EXIST', 'Mapnaam bestaat al');
	define('PROC_CANT_CREATE_FLD', 'Kan map niet maken');
	define('PROC_CANT_INS_NEW_GROUP', 'Kan nieuwe groep niet opslaan');
	define('PROC_CANT_INS_NEW_CONT', 'Kan nieuw contact niet opslaan');
	define('PROC_CANT_INS_NEW_CONTS', 'Kan nieuwe contact(en) niet opslaan');
	define('PROC_CANT_ADD_NEW_CONT_TO_GRP', 'Kan contact(en) niet toevoegen aan groep');
	define('PROC_ERROR_ACCT_UPDATE', 'Er is een fout gebeurd bij het opslaan van uw account');
	define('PROC_CANT_UPDATE_CONT_SETTINGS', 'Kan de contactinstellingen niet opslaan');
	define('PROC_CANT_GET_SETTINGS', 'Kan de instellingen niet vinden');
	define('PROC_CANT_UPDATE_ACCT', 'Kan de account niet opslaan');
	define('PROC_ERROR_DEL_FLD', 'Er is een fout gebeurd bij het verwijderen van de map(pen)');
	define('PROC_CANT_UPDATE_CONT', 'Kan contact niet opslaan');
	define('PROC_CANT_GET_FLDS', 'Kan mappenweergave niet ophalen');
	define('PROC_CANT_GET_MSG_LIST', 'Kan berichtenlijst niet ophalen');
	define('PROC_MSG_HAS_DELETED', 'Dit bericht is al verwijderd van de mailserver');
	define('PROC_CANT_LOAD_CONT_SETTINGS', 'Kan contactinstellingen niet ophalen');
	define('PROC_CANT_LOAD_SIGNATURE', 'Kan account onderschrift niet laden');
	define('PROC_CANT_GET_CONT_FROM_DB', 'Kan contact niet ophalen uit de database');
	define('PROC_CANT_GET_CONTS_FROM_DB', 'Kan contact(en) niet ophalen uit de database');
	define('PROC_CANT_DEL_ACCT_BY_ID', 'Kan account niet verwijderen');
	define('PROC_CANT_DEL_FILTER_BY_ID', 'Kan filter niet verwijderen');
	define('PROC_CANT_DEL_CONT_GROUPS', 'Kan contact(en) of groep(en) niet verwijderen');
	define('PROC_WRONG_ACCT_ACCESS', 'Een poging tot niet-geauthoriseerde toegang tot andermans account werd gedetecteerd.');
	define('PROC_SESSION_ERROR', 'De vorige sessie is beëindigd wegens een timeout.');
	
	define('MailBoxIsFull', 'Mailbox is vol');
	define('WebMailException', 'WebMail fout gebeurd');
	define('InvalidUid', 'Ongeldig Bericht UID');
	define('CantCreateContactGroup', 'Kan contactgroep niet maken');
	define('CantCreateUser', 'Kan gebruiker niet maken');
	define('CantCreateAccount', 'Kan account niet maken');
	define('SessionIsEmpty', 'Sessie is leeg');
	define('FileIsTooBig', 'Het bestand is te groot');
	
	define('PROC_CANT_MARK_ALL_MSG_READ', 'Kan niet alle berichten als gelezen markeren');
	define('PROC_CANT_MARK_ALL_MSG_UNREAD', 'Kan niet alle berichten als ongelezen markeren');
	define('PROC_CANT_PURGE_MSGS', 'Kan bericht(en) niet definitief verwijderen');
	define('PROC_CANT_DEL_MSGS', 'Kan bericht(en) niet verwijderen');
	define('PROC_CANT_UNDEL_MSGS', 'Kan verwijderen niet ongedaan maken voor bericht(en)');
	define('PROC_CANT_MARK_MSGS_READ', 'Kan bericht(en) niet als gelezen markeren');
	define('PROC_CANT_MARK_MSGS_UNREAD', 'Kan bericht(en) niet als ongelezen markeren');
	define('PROC_CANT_SET_MSG_FLAGS', 'Kan berichten-vlag niet zetten');
	define('PROC_CANT_REMOVE_MSG_FLAGS', 'Kan berichten-vlag niet verwijderen');
	define('PROC_CANT_CHANGE_MSG_FLD', 'Kan map niet wijzigen');
	define('PROC_CANT_SEND_MSG', 'Kan bericht niet verzenden.');
	define('PROC_CANT_SAVE_MSG', 'Kan bericht niet opslaan.');
	define('PROC_CANT_GET_ACCT_LIST', 'Kan accountlijst niet ophalen');
	define('PROC_CANT_GET_FILTER_LIST', 'Kan filterlijst niet ophalen');
	
	define('PROC_CANT_LEAVE_BLANK', 'Gelieve alle velden gemarkeerd met * in te vullen');
	
	define('PROC_CANT_UPD_FLD', 'Kan map niet opslaan');
	define('PROC_CANT_UPD_FILTER', 'Kan filter niet opslaan');
	
	define('ACCT_CANT_ADD_DEF_ACCT', 'De account kan niet worden toegevoegd omdat hij gebruikt wordt als standaard account door een andere gebruiker.');
	define('ACCT_CANT_UPD_TO_DEF_ACCT', 'Deze account kan niet als standaard gezet worden.');
	define('ACCT_CANT_CREATE_IMAP_ACCT', 'Kan nieuwe account niet maken (IMAP4 verbindingsfout)');
	define('ACCT_CANT_DEL_LAST_DEF_ACCT', 'Kan laatste default account niet verwijderen');
	
	define('LANG_LoginInfo', 'Login Informatie');
	define('LANG_Email', 'Email');
	define('LANG_Login', 'Login');
	define('LANG_Password', 'Paswoord');
	define('LANG_IncServer', 'Inkomende Mail');
	define('LANG_PopProtocol', 'POP3');
	define('LANG_ImapProtocol', 'IMAP4');
	define('LANG_IncPort', 'Port');
	define('LANG_OutServer', 'SMTP Server');
	define('LANG_OutPort', 'Port');
	define('LANG_UseSmtpAuth', 'Gebruik SMTP authenticatie');
	define('LANG_SignMe', 'Automatisch inloggen');
	define('LANG_Enter', 'Enter');
	
	define('JS_LANG_TitleLogin', 'Login');
	define('JS_LANG_TitleMessagesListView', 'Berichtenlijst');
	define('JS_LANG_TitleMessagesList', 'Berichtenlijst');
	define('JS_LANG_TitleViewMessage', 'Bericht bekijken');
	define('JS_LANG_TitleNewMessage', 'Nieuw bericht');
	define('JS_LANG_TitleSettings', 'Instellingen');
	define('JS_LANG_TitleContacts', 'Contacten');
	
	define('JS_LANG_StandardLogin', 'Standaard&nbsp;Login');
	define('JS_LANG_AdvancedLogin', 'Geavanceerde&nbsp;Login');
	
	define('JS_LANG_InfoWebMailLoading', 'Even geduld, bezig met laden&hellip;');
	define('JS_LANG_Loading', 'Bezig met laden&hellip;');
	define('JS_LANG_InfoMessagesLoad', 'Even geduld, bezig met ophalen van berichten');
	define('JS_LANG_InfoEmptyFolder', 'De map is leeg');
	define('JS_LANG_InfoPageLoading', 'De pagina is nog steeds aan het laden...');
	define('JS_LANG_InfoSendMessage', 'Het bericht is verzonden');
	define('JS_LANG_InfoSaveMessage', 'Het bericht is opgeslagen');
// You have imported 3 new contact(s) into your contacts list.
	define('JS_LANG_InfoHaveImported', 'U hebt');
	define('JS_LANG_InfoNewContacts', 'nieuwe contact(en) geïmporteerd.');
	define('JS_LANG_InfoToDelete', 'Om de map');
	define('JS_LANG_InfoDeleteContent', 'te verwijderen moet u eerst al zijn inhoud verwijderen.');
	define('JS_LANG_InfoDeleteNotEmptyFolders', 'Niet-lege mappen verwijderen is niet mogelijk. Gelieve eerst de inhoud van deze mappen te verwijderen.');
	define('JS_LANG_InfoRequiredFields', '* verplichte velden');
	
	define('JS_LANG_ConfirmAreYouSure', 'Bent u zeker?');
	define('JS_LANG_ConfirmDirectModeAreYouSure', 'De geselecteerde bericht(en) zullen PERMANENT verwijderd worden. Bent u zeker?');
	define('JS_LANG_ConfirmSaveSettings', 'De instellingen zijn niet opgeslagen. Klik OK om op te slaan.');
	define('JS_LANG_ConfirmSaveContactsSettings', 'De contactinstellingen zijn niet opgeslagen. Klik OK om op te slaan.');
	define('JS_LANG_ConfirmSaveAcctProp', 'De contact-instellingen zijn niet opgeslagen. Klik OK om op te slaan.');
	define('JS_LANG_ConfirmSaveFilter', 'De filter-instellingen zijn niet opgeslagen. Klik OK om op te slaan.');
	define('JS_LANG_ConfirmSaveSignature', 'Het onderschrift is niet opgeslagen. Klik OK om op te slaan.');
	define('JS_LANG_ConfirmSavefolders', 'De mappen zijn niet opgeslagen. Klik OK om op te slaan.');
	define('JS_LANG_ConfirmHtmlToPlain', 'Waarschuwing: Door het formaat van dit bericht te wijzigen van HTML naar plain text, zal u alle opmaak verliezen. Klik OK om verder te gaan.');
	define('JS_LANG_ConfirmAddFolder', 'Voordat u deze map kunt toevoegen moet u eerst opslaan. Klik OK om op te slaan.');
	define('JS_LANG_ConfirmEmptySubject', 'Het onderwerp-veld is leeg. Bent u zeker dat u wil verdergaan?');
	
	define('JS_LANG_WarningEmailBlank', 'U kan het <br />Email: veld niet leeg laten');
	define('JS_LANG_WarningLoginBlank', 'U kan het leave<br />Login: veld niet leeg laten');
	define('JS_LANG_WarningToBlank', 'U kan het Aan: veld niet leeg laten');
	define('JS_LANG_WarningServerPortBlank', 'U kan de POP3 en<br />SMTP server/poort velden niet leeg laten');
	define('JS_LANG_WarningEmptySearchLine', 'Leeg zoekveld. Gelieve in te vullen wat u wilt zoeken');
	define('JS_LANG_WarningMarkListItem', 'Gelieve minstens één item te markeren in de lijst');
	define('JS_LANG_WarningFolderMove', 'De map kan niet verplaatst worden omdat dit een ander niveau is');
	define('JS_LANG_WarningContactNotComplete', 'Gelieve email of naam in te vullen');
	define('JS_LANG_WarningGroupNotComplete', 'Gelieve een groepnaam in te vullen');
	
	define('JS_LANG_WarningEmailFieldBlank', 'U kan het Email veld niet leeg laten');
	define('JS_LANG_WarningIncServerBlank', 'U kan het POP3(IMAP4) Server veld niet leeg laten');
	define('JS_LANG_WarningIncPortBlank', 'U kan het POP3(IMAP4) Server Port veld niet leeg laten');
	define('JS_LANG_WarningIncLoginBlank', 'U kan het POP3(IMAP4) Login veld niet leeg laten');
	define('JS_LANG_WarningIncPortNumber', 'Gelieve een positief getal in het POP3(IMAP4) poort veld in te vullen.');
	define('JS_LANG_DefaultIncPortNumber', 'Standaard POP3(IMAP4) poort nummer is 110(143).');
	define('JS_LANG_WarningIncPassBlank', 'U kan het POP3(IMAP4) Paswoord veld niet leeg laten');
	define('JS_LANG_WarningOutPortBlank', 'U kan het SMTP Server Port veld niet leeg laten');
	define('JS_LANG_WarningOutPortNumber', 'Gelieve een positief getal in het SMTP poort veld in te vullen.');
	define('JS_LANG_WarningCorrectEmail', 'Gelieve een correct emailadres in te vullen.');
	define('JS_LANG_DefaultOutPortNumber', 'Standaard SMTP poort nummer is 25.');
	
	define('JS_LANG_WarningCsvExtention', 'Extensie moet .csv zijn');
	define('JS_LANG_WarningImportFileType', 'Gelieve de applicatie vanwaaruit u contacten wil importeren te selecteren');
	define('JS_LANG_WarningEmptyImportFile', 'Gelieve een bestand te selecteren door op de knop \'Bladeren\' te klikken');
	
	define('JS_LANG_WarningContactsPerPage', 'Aantal contacten per pagina moet positief zijn');
	define('JS_LANG_WarningMessagesPerPage', 'Aantal berichten per pagina moet positief zijn');
	define('JS_LANG_WarningMailsOnServerDays', 'U moet een positief getal invullen in \'Aantal berichten op server\' veld.');
	define('JS_LANG_WarningEmptyFilter', 'Gelieve een substring in te vullen');
	define('JS_LANG_WarningEmptyFolderName', 'Gelieve een mapnaam in te vullen');
	
	define('JS_LANG_ErrorConnectionFailed', 'Verbinding mislukt');
	define('JS_LANG_ErrorRequestFailed', 'De dataoverdracht is niet voltooid');
	define('JS_LANG_ErrorAbsentXMLHttpRequest', 'Het object XMLHttpRequest bestaat niet');
	define('JS_LANG_ErrorWithoutDesc', 'Een onbekende fout is gebeurd.');
	define('JS_LANG_ErrorParsing', 'Fout bij het interpreteren van de XML.');
	define('JS_LANG_ResponseText', 'Antwoordtekst:');
	define('JS_LANG_ErrorEmptyXmlPacket', 'Leeg XML pakket');
	define('JS_LANG_ErrorImportContacts', 'Fout bij het importeren van contacten');
	define('JS_LANG_ErrorNoContacts', 'Geen contacten gevonden om te importeren.');
	define('JS_LANG_ErrorCheckMail', 'Het ophalen van berichten is afgebroken wegens een fout. Waarschijnlijk zijn niet alle berichten ontvangen.');
	
	define('JS_LANG_LoggingToServer', 'Bezig met inloggen op de srever&hellip;');
	define('JS_LANG_GettingMsgsNum', 'Ophalen van aantal berichten');
	define('JS_LANG_RetrievingMessage', 'Bericht ophalen');
	define('JS_LANG_DeletingMessage', 'Bericht verwijderen');
	define('JS_LANG_DeletingMessages', 'Bericht(en) verwijderen');
	define('JS_LANG_Of', 'van');
	define('JS_LANG_Connection', 'Verbinding');
	define('JS_LANG_Charset', 'Karakterset');
	define('JS_LANG_AutoSelect', 'Auto-selectie');
	
	define('JS_LANG_Contacts', 'Contacten');
	define('JS_LANG_ClassicVersion', 'Klassieke versie');
	define('JS_LANG_Logout', 'Afmelden');
	define('JS_LANG_Settings', 'Instellingen');
	
	define('JS_LANG_LookFor', 'Zoeken naar');
	define('JS_LANG_SearchIn', 'Zoeken in');
	define('JS_LANG_QuickSearch', 'Zoeken in Van, Naar en Onderwerp velden alleen (sneller).');
	define('JS_LANG_SlowSearch', 'Zoeken in volledig bericht');
	define('JS_LANG_AllMailFolders', 'Alle mailmappen');
	define('JS_LANG_AllGroups', 'Alle groepen');
	
	define('JS_LANG_NewMessage', 'Nieuw bericht');
	define('JS_LANG_CheckMail', 'Mail checken');
	define('JS_LANG_ReloadFolders', 'Mappen opnieuw laden');
	define('JS_LANG_EmptyTrash', 'Prullenbak leegmaken');
	define('JS_LANG_MarkAsRead', 'Als gelezen markeren');
	define('JS_LANG_MarkAsUnread', 'Als ongelezen markeren');
	define('JS_LANG_MarkFlag', 'Vlaggen');
	define('JS_LANG_MarkUnflag', 'Uitvlaggen');
	define('JS_LANG_MarkAllRead', 'Allemaal als gelezen markeren');
	define('JS_LANG_MarkAllUnread', 'Allemaal als ongelezen markeren');
	define('JS_LANG_Reply', 'Antwoorden');
	define('JS_LANG_ReplyAll', 'Allen antwoorden');
	define('JS_LANG_Delete', 'Verwijderen');
	define('JS_LANG_Undelete', 'Verwijderen ongedaan maken');
	define('JS_LANG_PurgeDeleted', 'Definitief verwijderen');
	define('JS_LANG_MoveToFolder', 'Verplaatsen');
	define('JS_LANG_Forward', 'Doorsturen');
	
	define('JS_LANG_HideFolders', 'Mappen verbergen');
	define('JS_LANG_ShowFolders', 'Mappen weergeven');
	define('JS_LANG_ManageFolders', 'Mappen beheren');
	define('JS_LANG_SyncFolder', 'Gesynchroniseerde map');
	define('JS_LANG_NewMessages', 'Nieuwe berichten');
	define('JS_LANG_Messages', 'Bericht(en)');
	
	define('JS_LANG_From', 'Van');
	define('JS_LANG_To', 'Aan');
	define('JS_LANG_Date', 'Datum');
	define('JS_LANG_Size', 'Grootte');
	define('JS_LANG_Subject', 'Onderwerp');
	
	define('JS_LANG_FirstPage', 'Eerste pagina');
	define('JS_LANG_PreviousPage', 'Vorige pagina');
	define('JS_LANG_NextPage', 'Volgende pagina');
	define('JS_LANG_LastPage', 'Laatste pagina');
	
	define('JS_LANG_SwitchToPlain', 'Naar plain text overschakelen');
	define('JS_LANG_SwitchToHTML', 'Naar HTML overschakelen');
	define('JS_LANG_AddToAddressBokk', 'Toevoegen aan adresboek');
	define('JS_LANG_ClickToDownload', 'Klik om te downloaden');
	define('JS_LANG_View', 'Weergeven');
	define('JS_LANG_ShowFullHeaders', 'Volledige headers tonen');
	define('JS_LANG_HideFullHeaders', 'Volledige headers verbergen');
	
	define('JS_LANG_MessagesInFolder', 'Bericht(en) in map');
	define('JS_LANG_YouUsing', 'U gebruikt');
	define('JS_LANG_OfYour', 'van uw');
	define('JS_LANG_Mb', 'MB');
	define('JS_LANG_Kb', 'KB');
	define('JS_LANG_B', 'B');
	
	define('JS_LANG_SendMessage', 'Verzenden');
	define('JS_LANG_SaveMessage', 'Opslaan');
	define('JS_LANG_Print', 'Afdrukken');
	define('JS_LANG_PreviousMsg', 'Vorig bericht');
	define('JS_LANG_NextMsg', 'Volgend bericht');
	define('JS_LANG_AddressBook', 'Adresboek');
	define('JS_LANG_ShowBCC', 'BCC weergeven');
	define('JS_LANG_HideBCC', 'BCC verbergen');
	define('JS_LANG_CC', 'CC');
	define('JS_LANG_BCC', 'BCC');
	define('JS_LANG_ReplyTo', 'Antwoorden aan');
	define('JS_LANG_AttachFile', 'Bestand koppelen');
	define('JS_LANG_Attach', 'Koppelen');
	define('JS_LANG_Re', 'Re');
	define('JS_LANG_OriginalMessage', 'Origineel bericht');
	define('JS_LANG_Sent', 'Verzonden');
	define('JS_LANG_Fwd', 'Fwd');
	define('JS_LANG_Low', 'Laag');
	define('JS_LANG_Normal', 'Normal');
	define('JS_LANG_High', 'Hoog');
	define('JS_LANG_Importance', 'Prioriteit');
	define('JS_LANG_Close', 'Sluiten');
	
	define('JS_LANG_Common', 'Gemeenschappelijk');
	define('JS_LANG_EmailAccounts', 'Email Accounts');
	
	define('JS_LANG_MsgsPerPage', 'Berichten per pagina');
	define('JS_LANG_DisableRTE', 'Uitgebreide editor uitschakelen');
	define('JS_LANG_Skin', 'Skin');
	define('JS_LANG_DefCharset', 'Standaard karakterset');
	define('JS_LANG_DefCharsetInc', 'Standaard inkomende karakterset');
	define('JS_LANG_DefCharsetOut', 'Standaard uitgaande karakterset');
	define('JS_LANG_DefTimeOffset', 'Standaard tijdzone');
	define('JS_LANG_DefLanguage', 'Standaard taal');
	define('JS_LANG_DefDateFormat', 'Standaard datumformaat');
	define('JS_LANG_ShowViewPane', 'Berichtenlijst met voorbeeldweergave');
	define('JS_LANG_Save', 'Opslaan');
	define('JS_LANG_Cancel', 'Annuleren');
	define('JS_LANG_OK', 'OK');
	
	define('JS_LANG_Remove', 'Verwijderen');
	define('JS_LANG_AddNewAccount', 'Nieuwe account toevoegen');
	define('JS_LANG_Signature', 'Onderschrift');
	define('JS_LANG_Filters', 'Filters');
	define('JS_LANG_Properties', 'Eigenschappen');
	define('JS_LANG_UseForLogin', 'Gebruik deze accounteigenschappen (login en paswoord) om in te loggen');
	define('JS_LANG_MailFriendlyName', 'Uw naam');
	define('JS_LANG_MailEmail', 'Email');
	define('JS_LANG_MailIncHost', 'Inkomende mail');
	define('JS_LANG_Imap4', 'Imap4');
	define('JS_LANG_Pop3', 'Pop3');
	define('JS_LANG_MailIncPort', 'Poort');
	define('JS_LANG_MailIncLogin', 'Login');
	define('JS_LANG_MailIncPass', 'Paswoord');
	define('JS_LANG_MailOutHost', 'SMTP Server');
	define('JS_LANG_MailOutPort', 'Poort');
	define('JS_LANG_MailOutLogin', 'SMTP Login');
	define('JS_LANG_MailOutPass', 'SMTP Paswoord');
	define('JS_LANG_MailOutAuth1', 'Gebruik SMTP authenticatie');
	define('JS_LANG_MailOutAuth2', '(U mag SMTP login/paswoord velden leeg laten als ze hetzelfde zijn als de POP3/IMAP login/paswoorden)');
	define('JS_LANG_UseFriendlyNm1', 'Gebruik Friendly Name in "Van:" veld');
	define('JS_LANG_UseFriendlyNm2', '(Uw naam &lt;sender@mail.com&gt;)');
	define('JS_LANG_GetmailAtLogin', 'Mails ophalen bij login');
	define('JS_LANG_MailMode0', 'Ontvangen berichten verwijderen van server');
	define('JS_LANG_MailMode1', 'Berichten op server laten staan');
	define('JS_LANG_MailMode2', 'Berichten op server laten staan voor');
	define('JS_LANG_MailsOnServerDays', 'dag(en)');
	define('JS_LANG_MailMode3', 'Bericht verwijderen op server als het uit de prullenbak verwijderd wordt');
	define('JS_LANG_InboxSyncType', 'Type van synchronisatie');
	
	define('JS_LANG_SyncTypeNo', 'Niet synchroniseren');
	define('JS_LANG_SyncTypeNewHeaders', 'Nieuwe headers');
	define('JS_LANG_SyncTypeAllHeaders', 'Alle headers');
	define('JS_LANG_SyncTypeNewMessages', 'Nieuwe berichten');
	define('JS_LANG_SyncTypeAllMessages', 'Alle berichten');
	define('JS_LANG_SyncTypeDirectMode', 'Direct');
	
	define('JS_LANG_Pop3SyncTypeEntireHeaders', 'Alleen headers');
	define('JS_LANG_Pop3SyncTypeEntireMessages', 'Volledige berichten');
	define('JS_LANG_Pop3SyncTypeDirectMode', 'Direct');
	
	define('JS_LANG_DeleteFromDb', 'Berichten verwijderen van database als ze niet meer op de server bestaan');
	
	define('JS_LANG_EditFilter', 'Filter bewerken');
	define('JS_LANG_NewFilter', 'Nieuwe filter toevoegen');
	define('JS_LANG_Field', 'Veld');
	define('JS_LANG_Condition', 'Voorwaarde');
	define('JS_LANG_ContainSubstring', 'Bevat substring');
	define('JS_LANG_ContainExactPhrase', 'Bevat exact woord');
	define('JS_LANG_NotContainSubstring', 'Bevat substring niet');
	define('JS_LANG_FilterDesc_At', 'op');
	define('JS_LANG_FilterDesc_Field', 'veld');
	define('JS_LANG_Action', 'Actie');
	define('JS_LANG_DoNothing', 'Doe niets');
	define('JS_LANG_DeleteFromServer', 'Verwijder onmiddellijk van server');
	define('JS_LANG_MarkGrey', 'Markeer grijs');
	define('JS_LANG_Add', 'Toevoegen');
	define('JS_LANG_OtherFilterSettings', 'Andere filterinstellingen');
	define('JS_LANG_ConsiderXSpam', 'X-Spam headers bekijken');
	define('JS_LANG_Apply', 'Toepassen');
	
	define('JS_LANG_InsertLink', 'Link toevoegen');
	define('JS_LANG_RemoveLink', 'Link verwijderen');
	define('JS_LANG_Numbering', 'Nummeren');
	define('JS_LANG_Bullets', 'Items');
	define('JS_LANG_HorizontalLine', 'Horizontale lijn');
	define('JS_LANG_Bold', 'Vet');
	define('JS_LANG_Italic', 'Cursief');
	define('JS_LANG_Underline', 'Onderlijnen');
	define('JS_LANG_AlignLeft', 'Links uitlijnen');
	define('JS_LANG_Center', 'Centreren');
	define('JS_LANG_AlignRight', 'Rechts uitlijnen');
	define('JS_LANG_Justify', 'Uitvullen');
	define('JS_LANG_FontColor', 'Lettertype kleur');
	define('JS_LANG_Background', 'Achtergrond');
	define('JS_LANG_SwitchToPlainMode', 'Schakel over naar Plain Text Mode');
	define('JS_LANG_SwitchToHTMLMode', 'Schakel over naar HTML Mode');
	define('JS_LANG_AddSignatures', 'Handtekening toevoegen aan alle uitgaande berichten');
	define('JS_LANG_DontAddToReplies', 'Geen handtekening toevoegen aan antwoordmails en doorstuurmails');
	
	define('JS_LANG_Folder', 'Map');
	define('JS_LANG_Msgs', 'Berichten');
	define('JS_LANG_Synchronize', 'Synchronizeren');
	define('JS_LANG_ShowThisFolder', 'Toon deze map');
	define('JS_LANG_Total', 'Totaal');
	define('JS_LANG_DeleteSelected', 'Verwijder geselecteerde');
	define('JS_LANG_AddNewFolder', 'Nieuwe map toevoegen');
	define('JS_LANG_NewFolder', 'Nieuwe map');
	define('JS_LANG_ParentFolder', 'Hoofdmap');
	define('JS_LANG_NoParent', 'Geen hoofdmap');
	define('JS_LANG_OnMailServer', 'Maak deze map aan in de webmail en op de mailserver');
	define('JS_LANG_InWebMail', 'Maak deze map enkel aan in de webmail');
	define('JS_LANG_FolderName', 'Mapnaam');
	
	define('JS_LANG_ContactsPerPage', 'Contacten per pagina');
	define('JS_LANG_WhiteList', 'Addresboek vanuit een lege lijst');
	
	define('JS_LANG_CharsetDefault', 'Standaard');
	define('JS_LANG_CharsetArabicAlphabetISO', 'Arabisch Alfabet (ISO)');
	define('JS_LANG_CharsetArabicAlphabet', 'Arabisch Alfabet (Windows)');
	define('JS_LANG_CharsetBalticAlphabetISO', 'Baltisch Alfabet (ISO)');
	define('JS_LANG_CharsetBalticAlphabet', 'Baltisch Alfabet (Windows)');
	define('JS_LANG_CharsetCentralEuropeanAlphabetISO', 'Centraal Europees Alfabet (ISO)');
	define('JS_LANG_CharsetCentralEuropeanAlphabet', 'Centraal Europees Alfabet (Windows)');
	define('JS_LANG_CharsetChineseSimplifiedEUC', 'Chinese Simplified (EUC)');
	define('JS_LANG_CharsetChineseSimplifiedGB', 'Chinese Simplified (GB2312)');
	define('JS_LANG_CharsetChineseTraditional', 'Chinees Traditioneel');
	define('JS_LANG_CharsetCyrillicAlphabetISO', 'Cyrillisch Alfabet (ISO)');
	define('JS_LANG_CharsetCyrillicAlphabetKOI8R', 'Cyrillisch Alfabet (KOI8-R)');
	define('JS_LANG_CharsetCyrillicAlphabet', 'Cyrillisch Alfabet (Windows)');
	define('JS_LANG_CharsetGreekAlphabetISO', 'Grieks Alfabet (ISO)');
	define('JS_LANG_CharsetGreekAlphabet', 'Grieks Alfabet (Windows)');
	define('JS_LANG_CharsetHebrewAlphabetISO', 'Hebreeuws Alfabet (ISO)');
	define('JS_LANG_CharsetHebrewAlphabet', 'Hebreeuws Alfabet (Windows)');
	define('JS_LANG_CharsetJapanese', 'Japans');
	define('JS_LANG_CharsetJapaneseShiftJIS', 'Japans (Shift-JIS)');
	define('JS_LANG_CharsetKoreanEUC', 'Koreaans (EUC)');
	define('JS_LANG_CharsetKoreanISO', 'Koreaans (ISO)');
	define('JS_LANG_CharsetLatin3AlphabetISO', 'Latin 3 Alphabet (ISO)');
	define('JS_LANG_CharsetTurkishAlphabet', 'Turks Alfabet');
	define('JS_LANG_CharsetUniversalAlphabetUTF7', 'Universeel Alfabet (UTF-7)');
	define('JS_LANG_CharsetUniversalAlphabetUTF8', 'Universeel Alfabet (UTF-8)');
	define('JS_LANG_CharsetVietnameseAlphabet', 'Vietnamees Alfabet (Windows)');
	define('JS_LANG_CharsetWesternAlphabetISO', 'Westers Alfabet (ISO)');
	define('JS_LANG_CharsetWesternAlphabet', 'Westers Alfabet (Windows)');
	
	define('JS_LANG_TimeDefault', 'Standaard');
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
	define('LanguageNederlands', 'Nederlands');
	define('LanguageFrench', 'French');
	define('LanguageGerman', 'German');
	define('LanguageItaliano', 'Italian');
	define('LanguagePortuguese', 'Portuguese (BR)');
	define('LanguageEspanyol', 'Spanish');
	define('LanguageSwedish', 'Swedish');
	define('LanguageTurkish', 'Turkish');
	
	define('JS_LANG_DateDefault', 'Standaard');
	define('JS_LANG_DateDDMMYY', 'DD/MM/YY');
	define('JS_LANG_DateMMDDYY', 'MM/DD/YY');
	define('JS_LANG_DateDDMonth', 'DD Month (01 Jan)');
	define('JS_LANG_DateAdvanced', 'Geavanceerd');
	
	define('JS_LANG_NewContact', 'Nieuw contact');
	define('JS_LANG_NewGroup', 'Nieuwe groep');
	define('JS_LANG_AddContactsTo', 'Contacten toevoegen aan');
	define('JS_LANG_ImportContacts', 'Contacten importeren');
	
	define('JS_LANG_Name', 'Naam');
	define('JS_LANG_Email', 'Email');
	define('JS_LANG_DefaultEmail', 'Standaard Email');
	define('JS_LANG_NotSpecifiedYet', 'Nog niet aangepast');
	define('JS_LANG_ContactName', 'Naam');
	define('JS_LANG_Birthday', 'Verjaardag');
	define('JS_LANG_Month', 'Maand');
	define('JS_LANG_January', 'Januari');
	define('JS_LANG_February', 'Februari');
	define('JS_LANG_March', 'Maart');
	define('JS_LANG_April', 'April');
	define('JS_LANG_May', 'Mei');
	define('JS_LANG_June', 'Juni');
	define('JS_LANG_July', 'Juli');
	define('JS_LANG_August', 'Augustus');
	define('JS_LANG_September', 'September');
	define('JS_LANG_October', 'Oktober');
	define('JS_LANG_November', 'November');
	define('JS_LANG_December', 'December');
	define('JS_LANG_Day', 'Dag');
	define('JS_LANG_Year', 'Jaar');
	define('JS_LANG_UseFriendlyName1', 'Gebruik aangepaste naam');
	define('JS_LANG_UseFriendlyName2', '(bv, John Doe &lt;johndoe@mail.com&gt;)');
	define('JS_LANG_Personal', 'Persoonlijk');
	define('JS_LANG_PersonalEmail', 'Persoonlijke e-mail');
	define('JS_LANG_StreetAddress', 'Straat');
	define('JS_LANG_City', 'Stad');
	define('JS_LANG_Fax', 'Fax');
	define('JS_LANG_StateProvince', 'Provincie');
	define('JS_LANG_Phone', 'Telefoon');
	define('JS_LANG_ZipCode', 'Postcode');
	define('JS_LANG_Mobile', 'Mobiel');
	define('JS_LANG_CountryRegion', 'Land/Regio');
	define('JS_LANG_WebPage', 'Webpagina');
	define('JS_LANG_Go', 'Ga');
	define('JS_LANG_Home', 'Home');
	define('JS_LANG_Business', 'Werk');
	define('JS_LANG_BusinessEmail', 'Werk e-mail');
	define('JS_LANG_Company', 'Bedrijf');
	define('JS_LANG_JobTitle', 'Job Titel');
	define('JS_LANG_Department', 'Afdeling');
	define('JS_LANG_Office', 'Kantoor');
	define('JS_LANG_Pager', 'Pager');
	define('JS_LANG_Other', 'Ander');
	define('JS_LANG_OtherEmail', 'Ander e-mail');
	define('JS_LANG_Notes', 'Notities');
	define('JS_LANG_Groups', 'Groepen');
	define('JS_LANG_ShowAddFields', 'Toon extra velden');
	define('JS_LANG_HideAddFields', 'Verberg extra velden');
	define('JS_LANG_EditContact', 'Wijzig contact informatie');
	define('JS_LANG_GroupName', 'Groepsnaam');
	define('JS_LANG_AddContacts', 'Contacten toevoegen');
	define('JS_LANG_CommentAddContacts', '(Bij meerdere adressen, gelieve deze te scheiden met een komma)');
	define('JS_LANG_CreateGroup', 'Groep aanmaken');
	define('JS_LANG_Rename', 'Hernoemen');
	define('JS_LANG_MailGroup', 'Mailgroep');
	define('JS_LANG_RemoveFromGroup', 'Uit groep verwijderen');
	define('JS_LANG_UseImportTo', 'Gebruik importeren om je contacten van Microsoft Outlook en Microsoft Outlook Express in je webmail contactenlijst te importeren.');
	define('JS_LANG_Outlook1', 'Microsoft Outlook 2000/XP/2003');
	define('JS_LANG_Outlook2', 'Microsoft Outlook Express 6');
	define('JS_LANG_SelectImportFile', 'Selecteer het bestand (.CSV formaat) dat je wenst te importeren');
	define('JS_LANG_Import', 'Importeer');
	define('JS_LANG_ContactsMessage', 'Dit is de contactenpagina!!!');
	define('JS_LANG_ContactsCount', 'contact(en)');
	define('JS_LANG_GroupsCount', 'groep(en)');
	
// webmail 4.1 constants
	define('PicturesBlocked', 'Afbeeldingen in dit bericht werden voor uw veiligheid geblokkeerd.');
	define('ShowPictures', 'Toon afbeeldingen');
	define('ShowPicturesFromSender', 'Toon altijd afbeeldingen in berichten van deze afzender');
	define('AlwaysShowPictures', 'Toon altijd afbeeldingen in berichten');
	
	define('TreatAsOrganization', 'Behandel als een bedrijf');
	
	define('WarningGroupAlreadyExist', 'Groep met deze naam bestaat al. Gelieve de naam aan te passen.');
	define('WarningCorrectFolderName', 'Kies een correcte mapnaam.');
	define('WarningLoginFieldBlank', 'Login veld kan niet leeg zijn.');
	define('WarningCorrectLogin', 'Kies een correcte login.');
	define('WarningPassBlank', 'Paswoord veld kan niet leeg zijn.');
	define('WarningCorrectIncServer', 'Kies een correct POP3(IMAP) server adres.');
	define('WarningCorrectSMTPServer', 'Kies een correct SMTP server adres.');
	define('WarningFromBlank', 'Vanm veld kan niet leeg zijn.');
	define('WarningAdvancedDateFormat', 'Kies een datum-tijd formaat.');
	
	define('AdvancedDateHelpTitle', 'Geavanceerde datum');
	define('AdvancedDateHelpIntro', 'Wanneer het veld &quot;geavanceerd&quot; geselecteerd is, kan je de textbox gebruiken om je eigen datumformaat te kiezen, dat zal getoond worden in de webmail. De volgende opties worden gebruikt \':\' of \'/\' delimiter char:');
	define('AdvancedDateHelpConclusion', 'Bv, als je kiest voor &quot;mm/dd/yyyy&quot; in de textbox &quot;geavanceerd&quot;, dan wordt de datum weergegeven als maand/dag/jaar (bv. 14/07/2007)');
	define('AdvancedDateHelpDayOfMonth', 'Dag (van 1 tot 31)');
	define('AdvancedDateHelpNumericMonth', 'Maand (1 tot 12)');
	define('AdvancedDateHelpTextualMonth', 'Maand (Jan tot Dec)');
	define('AdvancedDateHelpYear2', 'Jaar, 2 karakters');
	define('AdvancedDateHelpYear4', 'Jaar, 4 karakters');
	define('AdvancedDateHelpDayOfYear', 'Dag (1 tot 366)');
	define('AdvancedDateHelpQuarter', 'Seizoen');
	define('AdvancedDateHelpDayOfWeek', 'Weekdag (Maandag tot Zondag)');
	define('AdvancedDateHelpWeekOfYear', 'Weeknr (1 tot 53)');
	
	define('InfoNoMessagesFound', 'Geen berichten gevonden.');
	define('ErrorSMTPConnect', 'Kan niet verbinden met de SMTP server. Controleer de SMTP server instellingen.');
	define('ErrorSMTPAuth', 'Verkeerde gebruikersnaam en/of paswoord. Aanmelden mislukt.');
	define('ReportMessageSent', 'Uw bericht is verzonden.');
	define('ReportMessageSaved', 'Uw bericht werd opgeslagen.');
	define('ErrorPOP3Connect', 'Kan niet verbinden met de POP3 server, Controleer de POP3 server instellingen.');
	define('ErrorIMAP4Connect', 'Kan niet verbinden met de IMAP4 server, Controleer de IMAP4 server instellingen.');
	define('ErrorPOP3IMAP4Auth', 'Verkeerde email/login en/of paswoord. Aanmelden mislukt.');
	define('ErrorGetMailLimit', 'Sorry, de limiet van uw mailbox werd bereikt.');
	
	define('ReportSettingsUpdatedSuccessfuly', 'Instellingen zijn met succes bewaard.');
	define('ReportAccountCreatedSuccessfuly', 'Account is met succes aangemaakt.');
	define('ReportAccountUpdatedSuccessfuly', 'Account is met succes aangepast.');
	define('ConfirmDeleteAccount', 'Ben je zeker dat je deze account wil verwijderen?');
	define('ReportFiltersUpdatedSuccessfuly', 'Filters werden met succes aangepast.');
	define('ReportSignatureUpdatedSuccessfuly', 'Handtekening is met succes aangepast.');
	define('ReportFoldersUpdatedSuccessfuly', 'Mappen zijn met succes aangepast.');
	define('ReportContactsSettingsUpdatedSuccessfuly', 'Contacten\' zijn met succes aangepast.');
	
	define('ErrorInvalidCSV', 'CSV bestand dat je selecteerde heeft een verkeerde indeling.');
// The group "guies" was successfully added.
	define('ReportGroupSuccessfulyAdded1', 'De groep');
	define('ReportGroupSuccessfulyAdded2', 'met succes toegevoegd.');
	define('ReportGroupUpdatedSuccessfuly', 'Groep is met succes aangepast.');
	define('ReportContactSuccessfulyAdded', 'Contact is met succes aangepast.');
	define('ReportContactUpdatedSuccessfuly', 'Contact is met succes aangepast.');
// Contact(s) was added to group "friends".
	define('ReportContactAddedToGroup', 'Contact(s) werd toegevoegd aan de groep');
	define('AlertNoContactsGroupsSelected', 'Geen contacten of groepen geselecteerd.');
	
	define('InfoListNotContainAddress', 'Als deze lijst niet het gezochte adres bevat, typ dan de eerste karakters.');
	
	define('DirectAccess', 'D');
	define('DirectAccessTitle', 'Directe modus. Webmail benadert de berichten rechtstreeks op de mailserver.');
	
	define('FolderInbox', 'Inbox');
	define('FolderSentItems', 'Verzonden berichten');
	define('FolderDrafts', 'Concepten');
	define('FolderTrash', 'Prullenbak');
	
	define('LanguageDanish', 'Danish');
	define('LanguagePolish', 'Polish');
	
	define('FileLargerAttachment', 'De bestandsgrootte is hoger dan toegelaten.');
	define('FilePartiallyUploaded', 'Enkel een gedeelte van het bestand werd geuploaded, te wijten aan een onbekende fout.');
	define('NoFileUploaded', 'Geen bestand geuploaded.');
	define('MissingTempFolder', 'De tijdelijke map is onbestaande.');
	define('MissingTempFile', 'Het tijdelijk bestand is onbestaande.');
	define('UnknownUploadError', 'Een onbekende fout is opgetreden.');
	define('FileLargerThan', 'Fout bij het uploaden. Waarschijnlijk is het bestand groter dan ');
	define('PROC_CANT_LOAD_DB', 'Kan niet verbinden met de database.');
	define('PROC_CANT_LOAD_LANG', 'Kan het gevraagde taalbestand niet vinden.');
	define('PROC_CANT_LOAD_ACCT', 'Deze account bestaat niet, misschien werd ze juist verwijderd.');
	
	define('DomainDosntExist', 'Dit domain bestaat niet op de mailserver.');
	define('ServerIsDisable', 'Deze mailserver gebruiken werd verboden door de beheerder.');
	
	define('PROC_ACCOUNT_EXISTS', 'Deze account kan niet worden aangemaakt omdat het al bestaat.');
	define('PROC_CANT_GET_MESSAGES_COUNT', 'Kan het aantal berichten niet ophalen.');
	define('PROC_CANT_MAIL_SIZE', 'Kan bestandsgrootte niet ophalen.');
	
	define('Organization', 'Bedrijf');
	define('WarningOutServerBlank', 'Het veld SMTP Server kan niet leeg zijn');
	
//
	define('JS_LANG_Refresh', 'Vernieuwen');
	define('JS_LANG_MessagesInInbox', 'Bericht(en) in Postvak IN');
	define('JS_LANG_InfoEmptyInbox', 'Postvak IN is leeg');

// webmail 4.2 constants
	define('LanguagePortugueseBrazil', 'Portuguese-Brazil');
	define('LanguageHungarian', 'Hongaars');

	define('BackToList', 'Terug naar lijst');
	define('InfoNoContactsGroups', 'Geen contacten of groepen.');
	define('InfoNewContactsGroups', 'U kan nieuwe contacten of groepen aanmaken, of u kan contacten importeren uit een .CSV bestand of in MS Outlook formaat.');
	define('DefTimeFormat', 'Standaard tijdsformaat');
	define('SpellNoSuggestions', 'Geen suggesties');
	define('SpellWait', 'Even geduld&hellip;');

	define('InfoNoMessageSelected', 'Geen berichten geselecteerd.');
	define('InfoSingleDoubleClick', 'U kan klikken op een bericht in de lijst om het voorbeeld te bekijken, of dubbelklikken om de volledige versie te bekijken.');

// calendar
	define('TitleDay', 'Dagweergave');
	define('TitleWeek', 'Weekweergave');
	define('TitleMonth', 'Maandweergave');

	define('ErrorNotSupportBrowser', 'AfterLogic Calendar ondersteunt uw browser niet. Gelieve over te schakelen naar FireFox 2.0 of nieuwer, Opera 9.0 of nieuwer, Internet Explorer 6.0 of nieuwer, Safari 3.0.2 of nieuwer.');
	define('ErrorTurnedOffActiveX', 'ActiveX is uitgeschakeld. <br/>Gelieve dit in te schakelen om deze toepassing te gebruiken.');

	define('Calendar', 'Kalender');

	define('TabDay', 'Dag');
	define('TabWeek', 'Week');
	define('TabMonth', 'Maand');

	define('ToolNewEvent', 'Nieuwe&nbsp;gebeurtenis');
	define('ToolBack', 'Terug');
	define('ToolToday', 'Vandaag');
	define('AltNewEvent', 'Nieuwe gebeurtenis');
	define('AltBack', 'Terug');
	define('AltToday', 'Vandaag');
	define('CalendarHeader', 'Agenda');
	define('CalendarsManager', 'Agendabeheer');

	define('CalendarActionNew', 'Nieuwe agenda');
	define('EventHeaderNew', 'Nieuwe gebeurtenis');
	define('CalendarHeaderNew', 'Nieuwe agenda');

	define('EventSubject', 'Onderwerp');
	define('EventCalendar', 'Agenda');
	define('EventFrom', 'Van');
	define('EventTill', 'tot');
	define('CalendarDescription', 'Beschrijving');
	define('CalendarColor', 'Kleur');
	define('CalendarName', 'Agendanaam');
	define('CalendarDefaultName', 'Mijn agenda');
	
	define('ButtonSave', 'Opslaan');
	define('ButtonCancel', 'Annuleren');
	define('ButtonDelete', 'Verwijderen');

	define('AltPrevMonth', 'Vorige maand');
	define('AltNextMonth', 'Volgende maand');

	define('CalendarHeaderEdit', 'Agenda aanpassen');
	define('CalendarActionEdit', 'Agenda aanpassen');
	define('ConfirmDeleteCalendar', 'Bent u zeker dat u deze agenda wil verwijderen');
	define('InfoDeleting', 'Bezig met verwijderen...');
	define('WarningCalendarNameBlank', 'U kan de agendanaam niet leeg laten.');
	define('ErrorCalendarNotCreated', 'Agenda niet gemaakt.');
	define('WarningSubjectBlank', 'U kan het onderwerp niet leeg laten.');
	define('WarningIncorrectTime', 'Het uur bevat sommige ongeldige karakters.');
	define('WarningIncorrectFromTime', 'De starttijd is ongeldig.');
	define('WarningIncorrectTillTime', 'De eindtijd is ongeldig.');
	define('WarningStartEndDate', 'De einddatum moet groter dan of gelijk zijn aan de startdatum.');
	define('WarningStartEndTime', 'De eindtijd moet groter dan of gelijk zijn aan de starttijd.');
	define('WarningIncorrectDate', 'De datum moet correct zijn.');
	define('InfoLoading', 'Bezig met laden...');
	define('EventCreate', 'Maak gebeurtenis');
	define('CalendarHideOther', 'Verberg andere agenda\'s');
	define('CalendarShowOther', 'Toon andere agenda\'s');
	define('CalendarRemove', 'Verwijder agenda');
	define('EventHeaderEdit', 'Gebeurtenis aanpassen');

	define('InfoSaving', 'Bezig met opslaan...');
	define('SettingsDisplayName', 'Weergavenaam');
	define('SettingsTimeFormat', 'Tijdsformaat');
	define('SettingsDateFormat', 'Datumformaat');
	define('SettingsShowWeekends', 'Weekends tonen');
	define('SettingsWorkdayStarts', 'Werkdag start op');
	define('SettingsWorkdayEnds', 'eindigt op');
	define('SettingsShowWorkday', 'Werkdagen tonen');
	define('SettingsWeekStartsOn', 'Week start op');
	define('SettingsDefaultTab', 'Standaard-tab');
	define('SettingsCountry', 'Land');
	define('SettingsTimeZone', 'Tijdzone');
	define('SettingsAllTimeZones', 'Alle tijdzones');

	define('WarningWorkdayStartsEnds', 'De \'Werkdag eindigt op\' tijd moet groter zijn dan de \'Werkdag start op\' tijd');
	define('ReportSettingsUpdated', 'Instellingen succesvol opgeslagen');

	define('SettingsTabCalendar', 'Agenda');

	define('FullMonthJanuary', 'Januari');
	define('FullMonthFebruary', 'Februari');
	define('FullMonthMarch', 'Maart');
	define('FullMonthApril', 'April');
	define('FullMonthMay', 'Mei');
	define('FullMonthJune', 'Juni');
	define('FullMonthJuly', 'Juli');
	define('FullMonthAugust', 'Augustus');
	define('FullMonthSeptember', 'September');
	define('FullMonthOctober', 'Oktober');
	define('FullMonthNovember', 'November');
	define('FullMonthDecember', 'December');

	define('ShortMonthJanuary', 'Jan');
	define('ShortMonthFebruary', 'Feb');
	define('ShortMonthMarch', 'Maa');
	define('ShortMonthApril', 'Apr');
	define('ShortMonthMay', 'Mei');
	define('ShortMonthJune', 'Jun');
	define('ShortMonthJuly', 'Jul');
	define('ShortMonthAugust', 'Aug');
	define('ShortMonthSeptember', 'Sep');
	define('ShortMonthOctober', 'Okt');
	define('ShortMonthNovember', 'Nov');
	define('ShortMonthDecember', 'Dec');

	define('FullDayMonday', 'Maandag');
	define('FullDayTuesday', 'Dinsdag');
	define('FullDayWednesday', 'Woensday');
	define('FullDayThursday', 'Donderday');
	define('FullDayFriday', 'Vrijday');
	define('FullDaySaturday', 'Zaterdag');
	define('FullDaySunday', 'Zondag');

	define('DayToolMonday', 'Maa');
	define('DayToolTuesday', 'Din');
	define('DayToolWednesday', 'Woe');
	define('DayToolThursday', 'Don');
	define('DayToolFriday', 'Vri');
	define('DayToolSaturday', 'Zat');
	define('DayToolSunday', 'Zon');

	define('CalendarTableDayMonday', 'M');
	define('CalendarTableDayTuesday', 'D');
	define('CalendarTableDayWednesday', 'W');
	define('CalendarTableDayThursday', 'D');
	define('CalendarTableDayFriday', 'V');
	define('CalendarTableDaySaturday', 'Z');
	define('CalendarTableDaySunday', 'Z');

	define('ErrorParseJSON', 'Het JSON antwoord van de server kan niet gelezen worden.');

	define('ErrorLoadCalendar', 'Kan agenda niet laden');
	define('ErrorLoadEvents', 'Kan gebeurtenissen niet laden');
	define('ErrorUpdateEvent', 'Kan gebeurtenis niet opslaan');
	define('ErrorDeleteEvent', 'Kan gebeurtenis niet verwijderen');
	define('ErrorUpdateCalendar', 'Kan agenda niet opslaan');
	define('ErrorDeleteCalendar', 'Kan agenda niet verwijderen');
	define('ErrorGeneral', 'Er is een fout gebeurd op de server. Probeer het later opnieuw.');

define('BackToCart', 'Back to administration panel');
define('StoreWebmail', 'Store webmail');