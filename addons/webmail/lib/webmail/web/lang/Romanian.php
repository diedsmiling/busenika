<?php
//WebMail Pro suportă următoarele limbi:
//Daneză, Olandeză, Engleză, Franceză, Germană, Maghiară, Portugheză Braziliană, Poloneză, Rusă, Română, Suedeză, Turcă
	define('PROC_ERROR_ACCT_CREATE', 'A existat o eroare în timp ce contul a fost creat');
	define('PROC_WRONG_ACCT_PWD', 'Parola contului e greșită');
	define('PROC_CANT_LOG_NONDEF', 'Nu vă puteți autentifica într-un cont neimplicit');
	define('PROC_CANT_INS_NEW_FILTER', 'Nu s-a putut insera un filtru nou');
	define('PROC_FOLDER_EXIST', 'Numele folerului deja există');
	define('PROC_CANT_CREATE_FLD', 'Nu s-a putut crea folder-ul');
	define('PROC_CANT_INS_NEW_GROUP', 'Nu s-a putut insera noul grup');
	define('PROC_CANT_INS_NEW_CONT', 'Nu se poate insera noul contact');
	define('PROC_CANT_INS_NEW_CONTS', 'Nu s-a putut insera noile contacte');
	define('PROC_CANT_ADD_NEW_CONT_TO_GRP', 'Nu se poate adăuga contact(e) în grup');
	define('PROC_ERROR_ACCT_UPDATE', 'A existat o eroare în timpul actualizării contului');
	define('PROC_CANT_UPDATE_CONT_SETTINGS', 'Nu se pot actualiza setările de contact');
	define('PROC_CANT_GET_SETTINGS', 'Nu se pot obține setările');
	define('PROC_CANT_UPDATE_ACCT', 'Nu se poate actualiza contul');
	define('PROC_ERROR_DEL_FLD', 'A existat o eroare în timpul ștergerii dosarului(dosarelor)');
	define('PROC_CANT_UPDATE_CONT', 'Nu se poate actualiza contactul');
	define('PROC_CANT_GET_FLDS', 'Nu se poate obține arborele folderelor');
	define('PROC_CANT_GET_MSG_LIST', 'Nu se poate obține lista de mesaje');
	define('PROC_MSG_HAS_DELETED', 'Acest mesaj a fost șters din serverul de e-mail');
	define('PROC_CANT_LOAD_CONT_SETTINGS', 'Nu s-au putut încărca setările contactelor');
	define('PROC_CANT_LOAD_SIGNATURE', 'Nu s-a putut încărca semnătura contului');
	define('PROC_CANT_GET_CONT_FROM_DB', 'Nu se poate obține contactul din Baza de Date');
	define('PROC_CANT_GET_CONTS_FROM_DB', 'Nu se pot obține contactele din Baza de Date');
	define('PROC_CANT_DEL_ACCT_BY_ID', 'Nu s-a putut șterge contul');
	define('PROC_CANT_DEL_FILTER_BY_ID', 'Nu s-a putut șterge filtrul');
	define('PROC_CANT_DEL_CONT_GROUPS', 'Nu s-a putut șterge contactul(contactele) și/sau grupurile');
	define('PROC_WRONG_ACCT_ACCESS', 'O tentativă de acces neautorizată asupra unui alt cont a fost detectată.');
	define('PROC_SESSION_ERROR', 'Sesiunea precedentă a fost terminată din cauza timpului alocat care s-a scurs.');

	define('MailBoxIsFull', 'Căsuța poștală e plină');
	define('WebMailException', 'A avut loc o excepție WebMail');
	define('InvalidUid', 'Mesaj invalid UID');
	define('CantCreateContactGroup', 'Nu se poate crea grupul de contact');
	define('CantCreateUser', 'Nu se poate crea utilizatorul');
	define('CantCreateAccount', 'Nu se poate crea contul');
	define('SessionIsEmpty', 'Sesiunea e goală');
	define('FileIsTooBig', 'Fișierul e prea mare');

	define('PROC_CANT_MARK_ALL_MSG_READ', 'Nu se pot marca toate mesajele ca citite');
	define('PROC_CANT_MARK_ALL_MSG_UNREAD', 'Nu se pot marca toate mesajele ca necitite');
	define('PROC_CANT_PURGE_MSGS', 'Nu s-a putut curăța mesajul(mesajele)');
	define('PROC_CANT_DEL_MSGS', 'Nu s-a putut șterge mesajul(mesajele)');
	define('PROC_CANT_UNDEL_MSGS', 'Nu s-a putut anula ștergere mesajului(mesajelor)');
	define('PROC_CANT_MARK_MSGS_READ', 'Nu se pot marca mesajul(mesajele) ca și citite');
	define('PROC_CANT_MARK_MSGS_UNREAD', 'Nu se pot marca mesajul(mesajele) ca și necitite');
	define('PROC_CANT_SET_MSG_FLAGS', 'Nu s-au putut seta stegulețul(stegulețele) mesajului');
	define('PROC_CANT_REMOVE_MSG_FLAGS', 'Nu s-a putut elimina stegulețul(stegulețele) mesajului');
	define('PROC_CANT_CHANGE_MSG_FLD', 'Nu s-a putut schimba dosarul mesajului(mesajelor)');
	define('PROC_CANT_SEND_MSG', 'Nu s-a putut trimite mesajul.');
	define('PROC_CANT_SAVE_MSG', 'Nu s-a putut salva mesajul.');
	define('PROC_CANT_GET_ACCT_LIST', 'Nu se poate obține lista de conturi');
	define('PROC_CANT_GET_FILTER_LIST', 'Nu se poate obține lista de filtre');

	define('PROC_CANT_LEAVE_BLANK', 'Nu puteți lăsa câmpurile goale marcate cu * ');

	define('PROC_CANT_UPD_FLD', 'Nu s-a putut acutaliza dosarul');
	define('PROC_CANT_UPD_FILTER', 'Nu s-a putut acutaliza filtrul');

	define('ACCT_CANT_ADD_DEF_ACCT', 'Acest cont nu poate fi adăugat pentru că e folosit ca cont implicit de către alt utilizator.');
	define('ACCT_CANT_UPD_TO_DEF_ACCT', 'Statusul acestui cont nu poate fi modificat către cel implicit.');
	define('ACCT_CANT_CREATE_IMAP_ACCT', 'Nu s-a putut crea noul cont (Eroare de conexiune IMAP4)');
	define('ACCT_CANT_DEL_LAST_DEF_ACCT', 'Nu s-a putut șterge ultimul cont implicit');

	define('LANG_LoginInfo', 'Informații autentificare');
	define('LANG_Email', 'E-mail');
	define('LANG_Login', 'Autentificare');
	define('LANG_Password', 'Parolă');
	define('LANG_IncServer', 'Mesaje primite');
	define('LANG_PopProtocol', 'POP3');
	define('LANG_ImapProtocol', 'IMAP4');
	define('LANG_IncPort', 'Port');
	define('LANG_OutServer', 'Server SMTP');
	define('LANG_OutPort', 'Port');
	define('LANG_UseSmtpAuth', 'Folosește autentificare SMTP');
	define('LANG_SignMe', 'Autentifică-mă automat');
	define('LANG_Enter', 'Înscrie');

	define('JS_LANG_TitleLogin', 'Autentificare');
	define('JS_LANG_TitleMessagesListView', 'Listă Mesaje');
	define('JS_LANG_TitleMessagesList', 'Lista mesajelor');
	define('JS_LANG_TitleViewMessage', 'Vezi mesaj');
	define('JS_LANG_TitleNewMessage', 'Mesaj nou');
	define('JS_LANG_TitleSettings', 'Setări');
	define('JS_LANG_TitleContacts', 'Contacte');

	define('JS_LANG_StandardLogin', 'Standard&nbsp;Autentificare');
	define('JS_LANG_AdvancedLogin', 'Avansat&nbsp;Autentificare');

	define('JS_LANG_InfoWebMailLoading', 'Vă rugăm să așteptați până ce WebMail se încarcă&hellip;');
	define('JS_LANG_Loading', 'Se încarcă&hellip;');
	define('JS_LANG_InfoMessagesLoad', 'Vă rugăm să așteptați până ce WebMail încarcă lista mesajelor');
	define('JS_LANG_InfoEmptyFolder', 'Dosarul este gol');
	define('JS_LANG_InfoPageLoading', 'Pagina încă se încarcă...');
	define('JS_LANG_InfoSendMessage', 'Mesajul a fost trimis');
	define('JS_LANG_InfoSaveMessage', 'Mesajul a fost salvat');
// Aveți importate 3 contacte noi în lista dvs. de contacte.
	define('JS_LANG_InfoHaveImported', 'Ați importat');
	define('JS_LANG_InfoNewContacts', 'contact(e) nou(noi) în lista de contacte.');
	define('JS_LANG_InfoToDelete', 'Pentru ștergere');
	define('JS_LANG_InfoDeleteContent', 'mai întâi va trebui să ștergeți tot conținutul dosarului.');
	define('JS_LANG_InfoDeleteNotEmptyFolders', 'Ștergerea dosarelor care nu sunt goale nu este permisă. Pentru a șterge dosare neverificate, șterge-ți mai întâi conținutul lor.');
	define('JS_LANG_InfoRequiredFields', '* câmpuri obligatorii');

	define('JS_LANG_ConfirmAreYouSure', 'Sunteți sigur(ă)?');
	define('JS_LANG_ConfirmDirectModeAreYouSure', 'Mesajul(mesajele) selecate vor și șterse PERMANENT! sunteți sigur(ă)?');
	define('JS_LANG_ConfirmSaveSettings', 'Setările nu au fost salvate. Selectați OK pentru a le salva.');
	define('JS_LANG_ConfirmSaveContactsSettings', 'Setările contactelor nu au fost salvate. Selectați OK pentru a le salva.');
	define('JS_LANG_ConfirmSaveAcctProp', 'Proprietățiile contului nu au fost salvate. Selectați OK pentru a le salva.');
	define('JS_LANG_ConfirmSaveFilter', 'Proprietățiile filtrelor nu au fost salvate. Selectați OK pentru a le salva.');
	define('JS_LANG_ConfirmSaveSignature', 'Semnătura nu a fost salvată. Selctați OK pentru a salva-o.');
	define('JS_LANG_ConfirmSavefolders', 'Dosarele nu au fost salvate. Selectați OK pentru a le salva.');
	define('JS_LANG_ConfirmHtmlToPlain', 'Înștiințare: Schimbând tipul formatului acestui mesaj de la HTML în text simplu, veți pierde conținutul mesajului. Selectați OK pentru a continua.');
	define('JS_LANG_ConfirmAddFolder', 'Înainte de adăugare/ștergere dosarelor e necesar aplicarea modificărilor. Selectați OK pentru a le salva.');
	define('JS_LANG_ConfirmEmptySubject', 'Câmpul subiect este gol. Doriți să continuați?');

	define('JS_LANG_WarningEmailBlank', 'Nu puteți lăsa câmpul <br />E-mail: gol');
	define('JS_LANG_WarningLoginBlank', 'Nu puteți lăsa câmpul <br />Autentificare: gol');
	define('JS_LANG_WarningToBlank', 'Nu puteți lăsa câmpul Destinatar: gol');
	define('JS_LANG_WarningServerPortBlank', 'Nu puteți lăsa câmpurile POP3 și<br />Server SMTP / port goale');
	define('JS_LANG_WarningEmptySearchLine', 'Linie de căutare goală. Vă rugăm introduceți datele care doriți să le găsiți');
	define('JS_LANG_WarningMarkListItem', 'Vă rugăm marcați cel puțin un element în listă');
	define('JS_LANG_WarningFolderMove', 'Dosarul nu poate fi mutat pentru că se află într-un alt nivel');
	define('JS_LANG_WarningContactNotComplete', 'Vă rugăm introduce-ți e-mail sau nume');
	define('JS_LANG_WarningGroupNotComplete', 'Vă rugăm introduce-ți numele grupului');

	define('JS_LANG_WarningEmailFieldBlank', 'Nu puteți lăsa câmpul E-mail gol');
	define('JS_LANG_WarningIncServerBlank', 'Nu puteți lăsa câmpul Server POP3(IMAP4) gol');
	define('JS_LANG_WarningIncPortBlank', 'Nu puteți lăsa câmpul Port-ul Serverului POP3(IMAP4) gol');
	define('JS_LANG_WarningIncLoginBlank', 'Nu puteți lăsa câmpul Autentificare POP3(IMAP4) gol');
	define('JS_LANG_WarningIncPortNumber', 'Ar trebui să specificați un număr pozitiv în câmpul port POP3(IMAP4) gol.');
	define('JS_LANG_DefaultIncPortNumber', 'Numărul implicit al portului POP3(IMAP4) e 110(143).');
	define('JS_LANG_WarningIncPassBlank', 'Nu puteți lăsa câmpul Parolă POP3(IMAP4) gol');
	define('JS_LANG_WarningOutPortBlank', 'Nu puteți lăsa câmpul Port-ul Serverului SMTP gol');
	define('JS_LANG_WarningOutPortNumber', 'Trebuie să specificați o valoare pozibită în câmpul Port SMTP.');
	define('JS_LANG_WarningCorrectEmail', 'Trebuie să specificați o adresă de e-mail corectă.');
	define('JS_LANG_DefaultOutPortNumber', 'Numărul implicit al Portului SMTP e 25.');

	define('JS_LANG_WarningCsvExtention', 'Extensia ar trebui să fie .csv');
	define('JS_LANG_WarningImportFileType', 'Vă rugăm selectați aplicația din care doriți să copiați contactele');
	define('JS_LANG_WarningEmptyImportFile', 'Vă rugăm selectați un fișier făcând click pe butonul de răsfoire');

	define('JS_LANG_WarningContactsPerPage', 'Valoare contactelor per pagină e un număr pozitiv');
	define('JS_LANG_WarningMessagesPerPage', 'Valoare mesajelor per pagină e un număr pozitiv');
	define('JS_LANG_WarningMailsOnServerDays', 'Trebuie să specificați un număr pozitiv în câmpul Numărul zilelor cât timp vor fi stocate Mesajele pe server.');
	define('JS_LANG_WarningEmptyFilter', 'Vă rugăm introduce-ți subgrupul de date');
	define('JS_LANG_WarningEmptyFolderName', 'Vă rugăm introduce-ți numele folderului');

	define('JS_LANG_ErrorConnectionFailed', 'Conexiunea nu a avut succes');
	define('JS_LANG_ErrorRequestFailed', 'Transferul de date nu a fost finalizat');
	define('JS_LANG_ErrorAbsentXMLHttpRequest', 'Obiectul XMLHttpRequest este absent');
	define('JS_LANG_ErrorWithoutDesc', 'O eroare fără descriere a avut loc');
	define('JS_LANG_ErrorParsing', 'Eroare în timpul analizei XML.');
	define('JS_LANG_ResponseText', 'Text răspuns:');
	define('JS_LANG_ErrorEmptyXmlPacket', 'Pachet XML gol');
	define('JS_LANG_ErrorImportContacts', 'A apărut o eroare în timpul importării contactelor');
	define('JS_LANG_ErrorNoContacts', 'Nici un contact de adăugat.');
	define('JS_LANG_ErrorCheckMail', 'Primirea mesajelor a fost încheiată din cauza unei erori. Probabil, nu toate mesajele au fost primite.');

	define('JS_LANG_LoggingToServer', 'Autentificare pe server&hellip;');
	define('JS_LANG_GettingMsgsNum', 'Se obține numărul de mesaje');
	define('JS_LANG_RetrievingMessage', 'Recuperând mesajele');
	define('JS_LANG_DeletingMessage', 'Se șterge mesajul');
	define('JS_LANG_DeletingMessages', 'Se șterge mesajele');
	define('JS_LANG_Of', 'din');
	define('JS_LANG_Connection', 'Conexiune');
	define('JS_LANG_Charset', 'Setul de caractere');
	define('JS_LANG_AutoSelect', 'Auto-Selectare');

	define('JS_LANG_Contacts', 'Contacte');
	define('JS_LANG_ClassicVersion', 'Versiunea Clasică');
	define('JS_LANG_Logout', 'Ieșire');
	define('JS_LANG_Settings', 'Setări');

	define('JS_LANG_LookFor', 'Căutați după');
	define('JS_LANG_SearchIn', 'Căutați în');
	define('JS_LANG_QuickSearch', 'Căutați doar după câmpurile: Expeditor, Destinatar și Subiect (o căutare mai rapidă).');
	define('JS_LANG_SlowSearch', 'Caută întregul mesaj');
	define('JS_LANG_AllMailFolders', 'Toate dosarele E-mail-ului');
	define('JS_LANG_AllGroups', 'Toate grupurile');

	define('JS_LANG_NewMessage', 'Mesaj nou');
	define('JS_LANG_CheckMail', 'Verificați corespondența');
	define('JS_LANG_ReloadFolders', 'Reîncărcați arborele de dosare');
	define('JS_LANG_EmptyTrash', 'Goliți coșul de gunoi');
	define('JS_LANG_MarkAsRead', 'Marcați conversația ca citită');
	define('JS_LANG_MarkAsUnread', 'Marcați conversația ca necitită');
	define('JS_LANG_MarkFlag', 'Marcați coversația cu stea');
	define('JS_LANG_MarkUnflag', 'Eliminați steaua');
	define('JS_LANG_MarkAllRead', 'Marcați toate conversațiile ca citite');
	define('JS_LANG_MarkAllUnread', 'Marcați toate conversațiile ca necitite');
	define('JS_LANG_Reply', 'Răspundeți');
	define('JS_LANG_ReplyAll', 'Răspundeți la toți');
	define('JS_LANG_Delete', 'Ștergeți');
	define('JS_LANG_Undelete', 'Anulare ștergere');
	define('JS_LANG_PurgeDeleted', 'Curățați mesajele șterse');
	define('JS_LANG_MoveToFolder', 'Mutațile în dosar');
	define('JS_LANG_Forward', 'Redirecționați');

	define('JS_LANG_HideFolders', 'Ascundeți dosar');
	define('JS_LANG_ShowFolders', 'Afișați dosar');
	define('JS_LANG_ManageFolders', 'Gestionare dosare');
	define('JS_LANG_SyncFolder', 'Dosar sincronizat');
	define('JS_LANG_NewMessages', 'Mesaje noi');
	define('JS_LANG_Messages', 'Mesaj(e)');

	define('JS_LANG_From', 'De la');
	define('JS_LANG_To', 'Către');
	define('JS_LANG_Date', 'Data');
	define('JS_LANG_Size', 'Mărime');
	define('JS_LANG_Subject', 'Subiect');

	define('JS_LANG_FirstPage', 'Prima pagină');
	define('JS_LANG_PreviousPage', 'Pagina precedentă');
	define('JS_LANG_NextPage', 'Următoare pagină');
	define('JS_LANG_LastPage', 'Ultima pagină');

	define('JS_LANG_SwitchToPlain', 'Comutare la vizualizare simplă a textului');
	define('JS_LANG_SwitchToHTML', 'Comutare la vizualizare HTML');
	define('JS_LANG_AddToAddressBokk', 'Adăugați în Agendă');
	define('JS_LANG_ClickToDownload', 'Click pentru descăcare');
	define('JS_LANG_View', 'View');
	define('JS_LANG_ShowFullHeaders', 'Afișați antetele în totalitate');
	define('JS_LANG_HideFullHeaders', 'Ascundeți antetele în totalitate');

	define('JS_LANG_MessagesInFolder', 'Mesaj(e) în dosar');
	define('JS_LANG_YouUsing', 'Folosiți');
	define('JS_LANG_OfYour', 'din');
	define('JS_LANG_Mb', 'MB');
	define('JS_LANG_Kb', 'KB');
	define('JS_LANG_B', 'B');

	define('JS_LANG_SendMessage', 'Trimiteți');
	define('JS_LANG_SaveMessage', 'Salvați');
	define('JS_LANG_Print', 'Imprimare');
	define('JS_LANG_PreviousMsg', 'Measjul precedent');
	define('JS_LANG_NextMsg', 'Următorul mesaj');
	define('JS_LANG_AddressBook', 'Agentă');
	define('JS_LANG_ShowBCC', 'Afișează BCC');
	define('JS_LANG_HideBCC', 'Ascunde BCC');
	define('JS_LANG_CC', 'CC');
	define('JS_LANG_BCC', 'BCC');
	define('JS_LANG_ReplyTo', 'Răspundeți la');
	define('JS_LANG_AttachFile', 'Fișier atașat');
	define('JS_LANG_Attach', 'Atașare');
	define('JS_LANG_Re', 'Re');
	define('JS_LANG_OriginalMessage', 'Mesaj original');
	define('JS_LANG_Sent', 'Trimis');
	define('JS_LANG_Fwd', 'Fwd');
	define('JS_LANG_Low', 'Scăzut');
	define('JS_LANG_Normal', 'Normal');
	define('JS_LANG_High', 'Ridicat');
	define('JS_LANG_Importance', 'Importanță');
	define('JS_LANG_Close', 'Închideți');

	define('JS_LANG_Common', 'General');
	define('JS_LANG_EmailAccounts', 'Conturi e-mail-uri');

	define('JS_LANG_MsgsPerPage', 'Mesaje per pagină');
	define('JS_LANG_DisableRTE', 'Dezactivați editorul de text');
	define('JS_LANG_Skin', 'Skin');
	define('JS_LANG_DefCharset', 'Setul de caractere implicit');
	define('JS_LANG_DefCharsetInc', 'Setul de caractere implicit pentru intrare');
	define('JS_LANG_DefCharsetOut', 'Setul de caractere implicit pentru ieșire');
	define('JS_LANG_DefTimeOffset', 'Fusul orar implicit');
	define('JS_LANG_DefLanguage', 'Limba implicită');
	define('JS_LANG_DefDateFormat', 'Formatul dată implicit');
	define('JS_LANG_ShowViewPane', 'Lista mesajelor cu panoul de previzualizare');
	define('JS_LANG_Save', 'Salvați');
	define('JS_LANG_Cancel', 'Anulați');
	define('JS_LANG_OK', 'OK');

	define('JS_LANG_Remove', 'Ștergeți');
	define('JS_LANG_AddNewAccount', 'Adăugați un cont nou');
	define('JS_LANG_Signature', 'Semnătură');
	define('JS_LANG_Filters', 'Filtre');
	define('JS_LANG_Properties', 'Proprietăți');
	define('JS_LANG_UseForLogin', 'Folosește proprietățile acestui cont (utilizator și parolă) pentru autentificare');
	define('JS_LANG_MailFriendlyName', 'Numele dvs.');
	define('JS_LANG_MailEmail', 'E-mail');
	define('JS_LANG_MailIncHost', 'Mail-uri primite');
	define('JS_LANG_Imap4', 'Imap4');
	define('JS_LANG_Pop3', 'Pop3');
	define('JS_LANG_MailIncPort', 'Port');
	define('JS_LANG_MailIncLogin', 'Utilizator');
	define('JS_LANG_MailIncPass', 'Parolă');
	define('JS_LANG_MailOutHost', 'Server SMTP');
	define('JS_LANG_MailOutPort', 'Port');
	define('JS_LANG_MailOutLogin', 'Autentificare SMTP');
	define('JS_LANG_MailOutPass', 'Parolă SMTP');
	define('JS_LANG_MailOutAuth1', 'Folosește autentificare SMTP');
	define('JS_LANG_MailOutAuth2', '(Puteți lăsa câmpurile utilizator/parolă SMTP goale, dacă sunt identice cu utilizator/parolă POP3/IMAP4)');
	define('JS_LANG_UseFriendlyNm1', 'Folosiți un Nume Favorabil în câmpul "De la:"');
	define('JS_LANG_UseFriendlyNm2', '(Numele dvs. &lt;expreditor@mail.com&gt;)');
	define('JS_LANG_GetmailAtLogin', 'Obțineți/Sincronizați e-mail-urile la autentificare');
	define('JS_LANG_MailMode0', 'Ștergeți mesajele primite de pe server');
	define('JS_LANG_MailMode1', 'Lăsați mesajele pe server');
	define('JS_LANG_MailMode2', 'Păstrați mesajele pe sever timp de');
	define('JS_LANG_MailsOnServerDays', 'zi(le)');
	define('JS_LANG_MailMode3', 'Ștergeți mesajele de pe server când au fost șterse din Coșul de Gunoi');
	define('JS_LANG_InboxSyncType', 'Tipul sincronizării mesajelor primite');

	define('JS_LANG_SyncTypeNo', 'Nu sincroniza');
	define('JS_LANG_SyncTypeNewHeaders', 'Antete noi');
	define('JS_LANG_SyncTypeAllHeaders', 'Toate antetele');
	define('JS_LANG_SyncTypeNewMessages', 'Mesaje noi');
	define('JS_LANG_SyncTypeAllMessages', 'Toate mesajele');
	define('JS_LANG_SyncTypeDirectMode', 'Modul direct');

	define('JS_LANG_Pop3SyncTypeEntireHeaders', 'Doar antetele');
	define('JS_LANG_Pop3SyncTypeEntireMessages', 'Mesajele în întregime');
	define('JS_LANG_Pop3SyncTypeDirectMode', 'Modul direct');

	define('JS_LANG_DeleteFromDb', 'Șterge mesajele din baza de date dacă nu mai există pe serverul de e-mail');

	define('JS_LANG_EditFilter', 'Editare&nbsp;filtru');
	define('JS_LANG_NewFilter', 'Adăugați un filtru nou');
	define('JS_LANG_Field', 'Câmp');
	define('JS_LANG_Condition', 'Condiție');
	define('JS_LANG_ContainSubstring', 'Conține subgrupuri de date');
	define('JS_LANG_ContainExactPhrase', 'Conține frază exactă');
	define('JS_LANG_NotContainSubstring', 'Nu conține subgrupuri de date');
	define('JS_LANG_FilterDesc_At', 'la');
	define('JS_LANG_FilterDesc_Field', 'câmp');
	define('JS_LANG_Action', 'Acționează');
	define('JS_LANG_DoNothing', 'Nu fă nimic');
	define('JS_LANG_DeleteFromServer', 'Șterge-le de pe server imediat');
	define('JS_LANG_MarkGrey', 'Marchează ca șters');
	define('JS_LANG_Add', 'Adaugă');
	define('JS_LANG_OtherFilterSettings', 'Alte setări ale filtrului');
	define('JS_LANG_ConsiderXSpam', 'Considerați antetele X-Spam');
	define('JS_LANG_Apply', 'Aplică');

	define('JS_LANG_InsertLink', 'Inserați o legătură');
	define('JS_LANG_RemoveLink', 'Înlăturați legătura');
	define('JS_LANG_Numbering', 'Numerotare');
	define('JS_LANG_Bullets', 'Buline');
	define('JS_LANG_HorizontalLine', 'Linie orizontală');
	define('JS_LANG_Bold', 'Îngroșat');
	define('JS_LANG_Italic', 'Italic');
	define('JS_LANG_Underline', 'Subliniat');
	define('JS_LANG_AlignLeft', 'Aliniere stânga');
	define('JS_LANG_Center', 'Centru');
	define('JS_LANG_AlignRight', 'Aliniere dreapta');
	define('JS_LANG_Justify', 'Aliniere justify');
	define('JS_LANG_FontColor', 'Culoarea fontului');
	define('JS_LANG_Background', 'Fundal');
	define('JS_LANG_SwitchToPlainMode', 'Comută la modul text simplu');
	define('JS_LANG_SwitchToHTMLMode', 'Comută la Modul HTML');
	define('JS_LANG_AddSignatures', 'Adaugă semnăturile tuturor mesajelor expediate');
	define('JS_LANG_DontAddToReplies', 'Nu adăuga semnături pentru mesajele răspuns și cele direcționate');

	define('JS_LANG_Folder', 'Dosar');
	define('JS_LANG_Msgs', 'Msge');
	define('JS_LANG_Synchronize', 'Sincronizează');
	define('JS_LANG_ShowThisFolder', 'Arată acest dosar');
	define('JS_LANG_Total', 'Total');
	define('JS_LANG_DeleteSelected', 'Șterge cele selectate');
	define('JS_LANG_AddNewFolder', 'Adaugă un dosar nou');
	define('JS_LANG_NewFolder', 'Dosar nou');
	define('JS_LANG_ParentFolder', 'Dosar de bază');
	define('JS_LANG_NoParent', 'Fără dosar de bază');
	define('JS_LANG_OnMailServer', 'Creați acest dosar în WebMail și pe Serverul de E-Mail');
	define('JS_LANG_InWebMail', 'Creați acest dosar doar în WebMail');
	define('JS_LANG_FolderName', 'Numele dosarului');

	define('JS_LANG_ContactsPerPage', 'Contacte per pagină');
	define('JS_LANG_WhiteList', 'Agenda ca Listă albă');

	define('JS_LANG_CharsetDefault', 'Implicit');
	define('JS_LANG_CharsetArabicAlphabetISO', 'Alfabetul Arabic (ISO)');
	define('JS_LANG_CharsetArabicAlphabet', 'Alfabetul Arabic (Windows)');
	define('JS_LANG_CharsetBalticAlphabetISO', 'Alfabetul Baltic (ISO)');
	define('JS_LANG_CharsetBalticAlphabet', 'Alfabetul Baltic (Windows)');
	define('JS_LANG_CharsetCentralEuropeanAlphabetISO', 'Alfabetul Central European (ISO)');
	define('JS_LANG_CharsetCentralEuropeanAlphabet', 'Alfabetul Central European (Windows)');
	define('JS_LANG_CharsetChineseSimplifiedEUC', 'Chineză Simplificată (EUC)');
	define('JS_LANG_CharsetChineseSimplifiedGB', 'Chineză simplificată (GB2312)');
	define('JS_LANG_CharsetChineseTraditional', 'Chineză Tradițională (Big5)');
	define('JS_LANG_CharsetCyrillicAlphabetISO', 'Alfabetul Chirilic (ISO)');
	define('JS_LANG_CharsetCyrillicAlphabetKOI8R', 'Alfabetul Chirilic (KOI8-R)');
	define('JS_LANG_CharsetCyrillicAlphabet', 'Alfabetul Chirilic (Windows)');
	define('JS_LANG_CharsetGreekAlphabetISO', 'Alfabetul Grecesc (ISO)');
	define('JS_LANG_CharsetGreekAlphabet', 'Alfabetul Grecesc (Windows)');
	define('JS_LANG_CharsetHebrewAlphabetISO', 'Alfabetul Ebraic (ISO)');
	define('JS_LANG_CharsetHebrewAlphabet', 'Alfabetul Ebraic (Windows)');
	define('JS_LANG_CharsetJapanese', 'Japoneză');
	define('JS_LANG_CharsetJapaneseShiftJIS', 'Japoneză (Shift-JIS)');
	define('JS_LANG_CharsetKoreanEUC', 'Coreană (EUC)');
	define('JS_LANG_CharsetKoreanISO', 'Coreană (ISO)');
	define('JS_LANG_CharsetLatin3AlphabetISO', 'Alfabetul Latin 3 (ISO)');
	define('JS_LANG_CharsetTurkishAlphabet', 'Alfabetul Turc');
	define('JS_LANG_CharsetUniversalAlphabetUTF7', 'Alfabetul Universal (UTF-7)');
	define('JS_LANG_CharsetUniversalAlphabetUTF8', 'Alfabetul Universal (UTF-8)');
	define('JS_LANG_CharsetVietnameseAlphabet', 'Alfabetul Vietnamez (Windows)');
	define('JS_LANG_CharsetWesternAlphabetISO', 'Alfabetul Vestic (ISO)');
	define('JS_LANG_CharsetWesternAlphabet', 'Alfabetul Vestic (Windows)');

	define('JS_LANG_TimeDefault', 'Implicit');
	define('JS_LANG_TimeEniwetok', 'Eniwetok, Kwajalein, Fus orar');
	define('JS_LANG_TimeMidwayIsland', 'Insulele Midway, Samoa');
	define('JS_LANG_TimeHawaii', 'Hawaii');
	define('JS_LANG_TimeAlaska', 'Alaska');
	define('JS_LANG_TimePacific', 'Pacific Time (US & Canada); Tijuana');
	define('JS_LANG_TimeArizona', 'Arizona');
	define('JS_LANG_TimeMountain', 'Mountain Time (US & Canada)');
	define('JS_LANG_TimeCentralAmerica', 'America Centrală');
	define('JS_LANG_TimeCentral', 'Ora centrală (US & Canada)');
	define('JS_LANG_TimeMexicoCity', 'Orașul Mexic, Tegucigalpa');
	define('JS_LANG_TimeSaskatchewan', 'Saskatchewan');
	define('JS_LANG_TimeIndiana', 'Indiana (Est)');
	define('JS_LANG_TimeEastern', 'Fus orar vestic (US & Canada)');
	define('JS_LANG_TimeBogota', 'Bogota, Lima, Quito');
	define('JS_LANG_TimeSantiago', 'Santiago');
	define('JS_LANG_TimeCaracas', 'Caracas, La Paz');
	define('JS_LANG_TimeAtlanticCanada', 'Fus orar Atlantic (Canada)');
	define('JS_LANG_TimeNewfoundland', 'Newfoundland');
	define('JS_LANG_TimeGreenland', 'Groelanda');
	define('JS_LANG_TimeBuenosAires', 'Buenos Aires, Georgetown');
	define('JS_LANG_TimeBrasilia', 'Brasilia');
	define('JS_LANG_TimeMidAtlantic', 'Mid-Atlantic');
	define('JS_LANG_TimeCapeVerde', 'Is. Cape Verde');
	define('JS_LANG_TimeAzores', 'Azores');
	define('JS_LANG_TimeMonrovia', 'Casablanca, Monrovia');
	define('JS_LANG_TimeGMT', 'Dublin, Edinburgh, Lisabona, Londra');
	define('JS_LANG_TimeBerlin', 'Amsterdam, Berlin, Bern, Rome, Stockholm, Viena');
	define('JS_LANG_TimePrague', 'Belgrad, Bratislava, Budapesta, Ljubljana, Praga');
	define('JS_LANG_TimeParis', 'Bruxelles, Copenhaga, Madrid, Paris');
	define('JS_LANG_TimeSarajevo', 'Sarajevo, Skopje, Sofija, Vilnius, Varșovia, Zagreb');
	define('JS_LANG_TimeWestCentralAfrica', 'Africa de Vest Centrală');
	define('JS_LANG_TimeAthens', 'Atena, Istanbul, Minsk');
	define('JS_LANG_TimeEasternEurope', 'București');
	define('JS_LANG_TimeCairo', 'Cairo');
	define('JS_LANG_TimeHarare', 'Harare, Pretoria');
	define('JS_LANG_TimeHelsinki', 'Helsinki, Riga, Tallinn');
	define('JS_LANG_TimeIsrael', 'Israel, Fusul orar standard al Ierusalimului');
	define('JS_LANG_TimeBaghdad', 'Bagdad');
	define('JS_LANG_TimeArab', 'Arab, Kuwait, Riyadh');
	define('JS_LANG_TimeMoscow', 'Moscova, St. Petersburg, Volgograd');
	define('JS_LANG_TimeEastAfrica', 'Africa de Est, Nairobi');
	define('JS_LANG_TimeTehran', 'Teheran');
	define('JS_LANG_TimeAbuDhabi', 'Abu Dhabi, Muscat');
	define('JS_LANG_TimeCaucasus', 'Baku, Tbilisi, Erevan');
	define('JS_LANG_TimeKabul', 'Kabul');
	define('JS_LANG_TimeEkaterinburg', 'Ekaterinburg');
	define('JS_LANG_TimeIslamabad', 'Islamabad, Karachi, Sverdlovsk, Taşkent');
	define('JS_LANG_TimeBombay', 'Calcuta, Chennai, Mumbai, New Delhi, Fusul orar standard al Indiei');
	define('JS_LANG_TimeNepal', 'Kathmandu, Nepal');
	define('JS_LANG_TimeAlmaty', 'Alma-Ata, Novosibirsk, Nordul Asiei Centrale');
	define('JS_LANG_TimeDhaka', 'Astana, Dhaka');
	define('JS_LANG_TimeSriLanka', 'Sri Jayawardenepura, Sri Lanka');
	define('JS_LANG_TimeRangoon', 'Rangoon');
	define('JS_LANG_TimeBangkok', 'Bangkok, Hanoi, Jakarta');
	define('JS_LANG_TimeKrasnoyarsk', 'Krasnoyarsk');
	define('JS_LANG_TimeBeijing', 'Beijing, Chongqing, Hong Kong SAR, Urumqi');
	define('JS_LANG_TimeIrkutsk', 'Irkutsk, Ulaan Bataar');
	define('JS_LANG_TimeSingapore', 'Kuala Lumpur, Singapore');
	define('JS_LANG_TimePerth', 'Perth, Vestul Australiei');
	define('JS_LANG_TimeTaipei', 'Taipei');
	define('JS_LANG_TimeTokyo', 'Osaka, Sapporo, Tokyo');
	define('JS_LANG_TimeSeoul', 'Seoul, Fusul orar standard al Coreei');
	define('JS_LANG_TimeYakutsk', 'Yakutsk');
	define('JS_LANG_TimeAdelaide', 'Adelaide, Australia Centrală');
	define('JS_LANG_TimeDarwin', 'Darwin');
	define('JS_LANG_TimeBrisbane', 'Brisbane, Estul Australiei');
	define('JS_LANG_TimeSydney', 'Canberra, Melbourne, Sydney, Hobart');
	define('JS_LANG_TimeGuam', 'Guam, Port Moresby');
	define('JS_LANG_TimeHobart', 'Hobart, Tasmania');
	define('JS_LANG_TimeVladivostock', 'Vladivostok');
	define('JS_LANG_TimeMagadan', 'Magadan, Is. Solomon, Noua Caledonie');
	define('JS_LANG_TimeWellington', 'Auckland, Wellington');
	define('JS_LANG_TimeFiji', 'Insulele Fiji, Kamchatka, Is. Marshall');
	define('JS_LANG_TimeTonga', 'Nuku\'alofa, Tonga,');

	define('LanguageEnglish', 'Engleză');
	define('LanguageCatala', 'Catalană');
	define('LanguageNederlands', 'Olandeză');
	define('LanguageFrench', 'Franceză');
	define('LanguageGerman', 'Germană');
	define('LanguageItaliano', 'Italiană');
	define('LanguagePortuguese', 'Portugheză (BR)');
	define('LanguageEspanyol', 'Spaniolă');
	define('LanguageSwedish', 'Suedeză');
	define('LanguageTurkish', 'Turcă');

	define('JS_LANG_DateDefault', 'Implicit');
	define('JS_LANG_DateDDMMYY', 'DD/MM/YY');
	define('JS_LANG_DateMMDDYY', 'MM/DD/YY');
	define('JS_LANG_DateDDMonth', 'DD Luni (01 ian)');
	define('JS_LANG_DateAdvanced', 'Avansat');

	define('JS_LANG_NewContact', 'Contact nou');
	define('JS_LANG_NewGroup', 'Grup nou');
	define('JS_LANG_AddContactsTo', 'Adaugă contactele în');
	define('JS_LANG_ImportContacts', 'Importare Contacte');

	define('JS_LANG_Name', 'Nume');
	define('JS_LANG_Email', 'E-mail');
	define('JS_LANG_DefaultEmail', 'E-mail-ul implicit');
	define('JS_LANG_NotSpecifiedYet', 'Nu s-a specificat încă');
	define('JS_LANG_ContactName', 'Nume');
	define('JS_LANG_Birthday', 'Zi de naștere');
	define('JS_LANG_Month', 'Lună');
	define('JS_LANG_January', 'Ianuarie');
	define('JS_LANG_February', 'Februarie');
	define('JS_LANG_March', 'Martie');
	define('JS_LANG_April', 'Aprilie');
	define('JS_LANG_May', 'Mai');
	define('JS_LANG_June', 'Iunie');
	define('JS_LANG_July', 'Iulie');
	define('JS_LANG_August', 'August');
	define('JS_LANG_September', 'Septembrie');
	define('JS_LANG_October', 'Octombrie');
	define('JS_LANG_November', 'Noiembrie');
	define('JS_LANG_December', 'Decembrie');
	define('JS_LANG_Day', 'Zi');
	define('JS_LANG_Year', 'An');
	define('JS_LANG_UseFriendlyName1', 'Folosiți un Nume Favorabil');
	define('JS_LANG_UseFriendlyName2', '(de exemplu, Ioan Doe &lt;ioandoe@mail.com&gt;)');
	define('JS_LANG_Personal', 'Personal');
	define('JS_LANG_PersonalEmail', 'E-mail Personal');
	define('JS_LANG_StreetAddress', 'Strada');
	define('JS_LANG_City', 'Oraș');
	define('JS_LANG_Fax', 'Fax');
	define('JS_LANG_StateProvince', 'Județ/Provincie');
	define('JS_LANG_Phone', 'Telefon');
	define('JS_LANG_ZipCode', 'Cod poștal');
	define('JS_LANG_Mobile', 'Mobil');
	define('JS_LANG_CountryRegion', 'Țară/Regiune');
	define('JS_LANG_WebPage', 'Pagină Web');
	define('JS_LANG_Go', 'Trimiteți');
	define('JS_LANG_Home', 'Acasă');
	define('JS_LANG_Business', 'Afaceri');
	define('JS_LANG_BusinessEmail', 'E-mail-ul afacerii');
	define('JS_LANG_Company', 'Companie');
	define('JS_LANG_JobTitle', 'Titlul locului de muncă');
	define('JS_LANG_Department', 'Departament');
	define('JS_LANG_Office', 'Serviciu');
	define('JS_LANG_Pager', 'Peger');
	define('JS_LANG_Other', 'Altul');
	define('JS_LANG_OtherEmail', 'Alt E-mail');
	define('JS_LANG_Notes', 'Note');
	define('JS_LANG_Groups', 'Grupuri');
	define('JS_LANG_ShowAddFields', 'Afișați câmpurile adiționale');
	define('JS_LANG_HideAddFields', 'Ascundeți câmpurile adiționale');
	define('JS_LANG_EditContact', 'Editare inforamții de contact');
	define('JS_LANG_GroupName', 'Numele grupului');
	define('JS_LANG_AddContacts', 'Adăugați contacte');
	define('JS_LANG_CommentAddContacts', '(Dacă veți specifica mai mult decât o adresă, vă rugăm separați-le cu virgulă)');
	define('JS_LANG_CreateGroup', 'Creați Grup');
	define('JS_LANG_Rename', 'redenumiți');
	define('JS_LANG_MailGroup', 'Mail Groupului');
	define('JS_LANG_RemoveFromGroup', 'Înlăturați din cadrul Grupului');
	define('JS_LANG_UseImportTo', 'Folosiți Importarea pentru a vă copia lista de contacte din Microsoft Outlook, Microsoft Outlook Express în MailBee-ul dvs. , în lista de contacte a WebMail-ului.');
	define('JS_LANG_Outlook1', 'Microsoft Outlook 2000/XP/2003');
	define('JS_LANG_Outlook2', 'Microsoft Outlook Express 6');
	define('JS_LANG_SelectImportFile', 'Selectați fișierul (format .CSV) care doriți să-l importați');
	define('JS_LANG_Import', 'Importare');
	define('JS_LANG_ContactsMessage', 'Aceasta este pagina de contacte!!!');
	define('JS_LANG_ContactsCount', 'contact(e)');
	define('JS_LANG_GroupsCount', 'group(uri)');

// webmail 4.1 constante
	define('PicturesBlocked', 'Imaginile din acest mesaj au fost blochate pentru siguranța dvs.');
	define('ShowPictures', 'Afișați imaginile');
	define('ShowPicturesFromSender', 'Întotdeauna afișați imaginile de la acest expeditor');
	define('AlwaysShowPictures', 'Întotdeauna afișați imaginiile în mesaje');

	define('TreatAsOrganization', 'Tratați-l ca o organizație');

	define('WarningGroupAlreadyExist', 'Un asemenea nume de grup există. Vă rugăm specificați alt nume.');
	define('WarningCorrectFolderName', 'Trebuie să specificați un nume de dosar corect.');
	define('WarningLoginFieldBlank', 'Nu puteți lăsa câmpul utilizator gol.');
	define('WarningCorrectLogin', 'Trebuie să specificați un utilizator corect.');
	define('WarningPassBlank', 'Nu puteți lăsa câmpul Parolă gol.');
	define('WarningCorrectIncServer', 'Trebuie să specificați o adresă corectă a serverului POP3(IMAP).');
	define('WarningCorrectSMTPServer', 'Trebuie să specificați o adresă corectă a serverului SMTP.');
	define('WarningFromBlank', 'Nu puteți să lăsați câmpul De la: gol.');
	define('WarningAdvancedDateFormat', 'Vă rugăm specificați un format dată-timp.');

	define('AdvancedDateHelpTitle', 'Dată Avansată');
	define('AdvancedDateHelpIntro', 'Când câmpul &quot;Avansat&quot; e selectat, puteți folosi căsuța de text pentru a vă seta propriul format de dată, care doriți să fie afișat în MailBee WebMail Pro. Următoarele opțiuni sunt folosite pentru acest scop împreună cu delimitatoarele: \':\' sau \'/\'');
	define('AdvancedDateHelpConclusion', 'De exemplu, dacă ați specificat valoarea &quot;ll/zz/aaaa&quot; în căsuța de text a câmpului &quot;Avansat&quot;, data va fi afișată ca lună/zi/an (ex: 11/23/2005)');
	define('AdvancedDateHelpDayOfMonth', 'Zi a lunii (de la 1 până la 31)');
	define('AdvancedDateHelpNumericMonth', 'Lună (de la 1 până la 12)');
	define('AdvancedDateHelpTextualMonth', 'Lună (de la Ianuarie până la Decembrie)');
	define('AdvancedDateHelpYear2', 'An, 2 cifre');
	define('AdvancedDateHelpYear4', 'An, 4 cifre');
	define('AdvancedDateHelpDayOfYear', 'Zi a anului (de la 1 până la 366)');
	define('AdvancedDateHelpQuarter', 'Trimestru');
	define('AdvancedDateHelpDayOfWeek', 'Zi a săptămânii (de la Luni până la Duminică)');
	define('AdvancedDateHelpWeekOfYear', 'Săptămâna a anului (de la 1 până la 53)');

	define('InfoNoMessagesFound', 'Nici un mesaj nu a fost găsit.');
	define('ErrorSMTPConnect', 'Nu s-a putut conecta la serverul SMTP. Verificați setările serverului SMTP.');
	define('ErrorSMTPAuth', 'Utilizator și/sau parolă greșită. Autentificarea a eșuat.');
	define('ReportMessageSent', 'Mesajul dvs. a fost trimis.');
	define('ReportMessageSaved', 'Mesajul dvs. a fost salvat.');
	define('ErrorPOP3Connect', 'Nu s-a putut conecta la serverul POP3, verificați setările serverului POP3.');
	define('ErrorIMAP4Connect', 'Nu s-a putut conecta la serverul IMAP4, verificați setările serverului IMAP4.');
	define('ErrorPOP3IMAP4Auth', 'E-mail/utilizator și/sau greșite. Autentificarea a eșuat.');
	define('ErrorGetMailLimit', 'Ne pare rău, limita de spațiu a căsuței poșta a fost depășită.');

	define('ReportSettingsUpdatedSuccessfuly', 'Setările au fost actualizate cu succes.');
	define('ReportAccountCreatedSuccessfuly', 'Contul a fost creat cu succes.');
	define('ReportAccountUpdatedSuccessfuly', 'Contul a fost actualizat cu succes.');
	define('ConfirmDeleteAccount', 'Sunteți sigur(ă) că doriți să ștergeți contul?');
	define('ReportFiltersUpdatedSuccessfuly', 'Filtrele au fost acutalizate cu succes.');
	define('ReportSignatureUpdatedSuccessfuly', 'Semnătura a fost acutalizată cu succes.');
	define('ReportFoldersUpdatedSuccessfuly', 'Dosarele au fost actualizate cu succes.');
	define('ReportContactsSettingsUpdatedSuccessfuly', 'Setările contactelor au fost actualizate cu succes.');

	define('ErrorInvalidCSV', 'Fișierul CSV selectat are un format invalid.');
// Grupul "guies" a fost adăugat cu succes.
	define('ReportGroupSuccessfulyAdded1', 'Grupul');
	define('ReportGroupSuccessfulyAdded2', 'a fost adăugat cu succes.');
	define('ReportGroupUpdatedSuccessfuly', 'Grupul a fost acutalizat cu succes.');
	define('ReportContactSuccessfulyAdded', 'Contactul a fost adăugat cu succes.');
	define('ReportContactUpdatedSuccessfuly', 'Contactul a fost actualizat cu succes.');
// Contact(ele) au fost adăugate grupului "friends".
	define('ReportContactAddedToGroup', 'Contact(ele) au fost adăugate grupului');
	define('AlertNoContactsGroupsSelected', 'Nici un contact sau grup selectat.');

	define('InfoListNotContainAddress', 'Dacă lista nu conține adresa care o căutați, păstrați doar primele caractre.');

	define('DirectAccess', 'D');
	define('DirectAccessTitle', 'Modul Direct. WebMail accesează mesajele direct de pe serverul de e-mail.');

	define('FolderInbox', 'Mesaje primite');
	define('FolderSentItems', 'Mesaje trimise');
	define('FolderDrafts', 'Mesaje nefinalizate');
	define('FolderTrash', 'Coș de gunoi');

	define('LanguageDanish', 'Daneză');
	define('LanguagePolish', 'Poloneză');

	define('FileLargerAttachment', 'Acest fișier depășește limita admisă unui Atașament.');
	define('FilePartiallyUploaded', 'Doar o parte din fișier a fost încărcate din cauza unei erori necunoscute.');
	define('NoFileUploaded', 'Nici un fișier nu a fost încărcat.');
	define('MissingTempFolder', 'Dosarul temporar lipsește.');
	define('MissingTempFile', 'Fișierul temporar lipsește.');
	define('UnknownUploadError', 'O eroare necunoscută a apărut în timpul încărcării.');
	define('FileLargerThan', 'Eroare încărcare fișier. Cel mai probabil, fișierul e mai mare de ');
	define('PROC_CANT_LOAD_DB', 'Nu se poate face conexiunea cu baza de date.');
	define('PROC_CANT_LOAD_LANG', 'Nu se poate găsit fișierul limbă necesar.');
	define('PROC_CANT_LOAD_ACCT', 'Contul nu există, poate a fost șters.');

	define('DomainDosntExist', 'Un asemenea domeniu nu există pe serverul de e-mail.');
	define('ServerIsDisable', 'Utilizare serverului de e-mail este interzisă de către administrator.');

	define('PROC_ACCOUNT_EXISTS', 'Acest cont nu poate fi creat pentru că deja există.');
	define('PROC_CANT_GET_MESSAGES_COUNT', 'Nu s-a putut obține numărul de mesaje a dosarului.');
	define('PROC_CANT_MAIL_SIZE', 'Nu s-a putut obține dimensiunea de stocare a căsuței poștale.');

	define('Organization', 'Organizație');
	define('WarningOutServerBlank', 'Nu puteți lăsa câmpul Server SMTP gol');

//
	define('JS_LANG_Refresh', 'Actualizați');
	define('JS_LANG_MessagesInInbox', 'Mesaj(e) primite');
	define('JS_LANG_InfoEmptyInbox', 'Căsuță poștală goală');

// webmail 4.2 constante
	define('LanguagePortugueseBrazil', 'Portugheză-Braziliană');
	define('LanguageHungarian', 'Maghiară');

	define('BackToList', 'Înapoi la listă');
	define('InfoNoContactsGroups', 'Nici un contact sau grup.');
	define('InfoNewContactsGroups', 'Puteți crea fie un contact/grup nou sau importați dintr-un fișier .CSV în formatul MS Outlook.');
	define('DefTimeFormat', 'Formatul implicit al fusului orar');
	define('SpellNoSuggestions', 'Nici o sugestie');
	define('SpellWait', 'Vă rugăm așteptați&hellip;');

	define('InfoNoMessageSelected', 'Nici un mesaj selectat.');
	define('InfoSingleDoubleClick', 'Îl puteți vizualiza aici dând un simplu-click sau dublu-click pentru a-l vizualiza întreg.');

// calendar
	define('TitleDay', 'Vizualizare pe zile');
	define('TitleWeek', 'Vizualizare pe săptămâni');
	define('TitleMonth', 'Vizualizare pe ani');

	define('ErrorNotSupportBrowser', 'Calendarul AfterLogic nu poate oferi suport navigatorului dvs. Vă rugăm folosiți FireFox 2.0 sau mai nou, Opera 9.0 sau mai nou, Internet Explorer 6.0 sau mai nou, Safari 3.0.2 sau mai nou.');
	define('ErrorTurnedOffActiveX', 'Suportul ActiveX este oprit. <br/>Ar trebui să-l activați pentru a folosi această aplicație.');

	define('Calendar', 'Calendar');

	define('TabDay', 'Zi');
	define('TabWeek', 'Săptămână');
	define('TabMonth', 'Lună');

	define('ToolNewEvent', 'Eveniment&nbsp;Nou');
	define('ToolBack', 'Înapoi');
	define('ToolToday', 'Astăzi');
	define('AltNewEvent', 'Eveniment nou');
	define('AltBack', 'Înapoi');
	define('AltToday', 'Astăzi');
	define('CalendarHeader', 'Calendar');
	define('CalendarsManager', 'Gestionare Calendare');

	define('CalendarActionNew', 'Calendar nou');
	define('EventHeaderNew', 'Eveniment nou');
	define('CalendarHeaderNew', 'Calendar nou');

	define('EventSubject', 'Subiect');
	define('EventCalendar', 'Calendar');
	define('EventFrom', 'Din');
	define('EventTill', 'până');
	define('CalendarDescription', 'Descriere');
	define('CalendarColor', 'Culoare');
	define('CalendarName', 'Numele calendarului');
	define('CalendarDefaultName', 'Calendarul meu');

	define('ButtonSave', 'Salvați');
	define('ButtonCancel', 'Anulați');
	define('ButtonDelete', 'Ștergeți');

	define('AltPrevMonth', 'Luna precedentă');
	define('AltNextMonth', 'Luna următoare');

	define('CalendarHeaderEdit', 'Editare Calendar');
	define('CalendarActionEdit', 'Editare Calendar');
	define('ConfirmDeleteCalendar', 'Suteți sigur(ă) că doriți să ștergeți calendarul');
	define('InfoDeleting', 'Se șterge...');
	define('WarningCalendarNameBlank', 'Nu puteți lăsa numele calendarului nespecificat.');
	define('ErrorCalendarNotCreated', 'Calendarul nu a fost creat.');
	define('WarningSubjectBlank', 'Nu puteți lăsa subiectul nespecificat.');
	define('WarningIncorrectTime', 'Timpul specificat conține caractere ilegale');
	define('WarningIncorrectFromTime', 'Timpul de început este incorect.');
	define('WarningIncorrectTillTime', 'Timpul până când este incorect.');
	define('WarningStartEndDate', 'Data finalizării trebuie să fie mai mare sau egală cu data de începere.');
	define('WarningStartEndTime', 'Data finalizării trebuie să fie mai mare decât data de început.');
	define('WarningIncorrectDate', 'Data trebuie să fie corectă.');
	define('InfoLoading', 'Se încarcă...');
	define('EventCreate', 'Creați eveniment');
	define('CalendarHideOther', 'Ascundeți alte calendare');
	define('CalendarShowOther', 'Afișați alte calendare');
	define('CalendarRemove', 'Ștergeți Calendar');
	define('EventHeaderEdit', 'Editare Eveniment');

	define('InfoSaving', 'Se salvează...');
	define('SettingsDisplayName', 'Numele de afișare');
	define('SettingsTimeFormat', 'Format timp');
	define('SettingsDateFormat', 'Format dată');
	define('SettingsShowWeekends', 'Afișează sfârșiturile de săptămână');
	define('SettingsWorkdayStarts', 'Ziua de lucru începe');
	define('SettingsWorkdayEnds', 'se termină');
	define('SettingsShowWorkday', 'Afișați ziua de lucru');
	define('SettingsWeekStartsOn', 'Săptămâna începe în');
	define('SettingsDefaultTab', 'Tab-ul implicit');
	define('SettingsCountry', 'Țară');
	define('SettingsTimeZone', 'Fus orar');
	define('SettingsAllTimeZones', 'Toate fusurile orare');

	define('WarningWorkdayStartsEnds', 'Timpul \'Sfârșitului zilei de lucru\' trebuie să fie mai mare decât timpul \'Începutul zilei de lucru\'');
	define('ReportSettingsUpdated', 'Setările au fost actualizate cu succes.');

	define('SettingsTabCalendar', 'Calendar');

	define('FullMonthJanuary', 'Ianuarie');
	define('FullMonthFebruary', 'Februarie');
	define('FullMonthMarch', 'Martie');
	define('FullMonthApril', 'Aprilie');
	define('FullMonthMay', 'Mai');
	define('FullMonthJune', 'Iunie');
	define('FullMonthJuly', 'Iulie');
	define('FullMonthAugust', 'August');
	define('FullMonthSeptember', 'Septembrie');
	define('FullMonthOctober', 'Octombrie');
	define('FullMonthNovember', 'Noiembrie');
	define('FullMonthDecember', 'Decembrie');

	define('ShortMonthJanuary', 'Ian');
	define('ShortMonthFebruary', 'Feb');
	define('ShortMonthMarch', 'Mar');
	define('ShortMonthApril', 'Apr');
	define('ShortMonthMay', 'Mai');
	define('ShortMonthJune', 'Iun');
	define('ShortMonthJuly', 'Iul');
	define('ShortMonthAugust', 'Aug');
	define('ShortMonthSeptember', 'Sep');
	define('ShortMonthOctober', 'Oct');
	define('ShortMonthNovember', 'Noi');
	define('ShortMonthDecember', 'Dec');

	define('FullDayMonday', 'Luni');
	define('FullDayTuesday', 'Marți');
	define('FullDayWednesday', 'Miercuri');
	define('FullDayThursday', 'Joi');
	define('FullDayFriday', 'Vineri');
	define('FullDaySaturday', 'Sâmbătă');
	define('FullDaySunday', 'Duminică');

	define('DayToolMonday', 'Lun');
	define('DayToolTuesday', 'Mar');
	define('DayToolWednesday', 'Mie');
	define('DayToolThursday', 'Joi');
	define('DayToolFriday', 'Vin');
	define('DayToolSaturday', 'Sâm');
	define('DayToolSunday', 'Dum');

	define('CalendarTableDayMonday', 'L');
	define('CalendarTableDayTuesday', 'M');
	define('CalendarTableDayWednesday', 'M');
	define('CalendarTableDayThursday', 'J');
	define('CalendarTableDayFriday', 'V');
	define('CalendarTableDaySaturday', 'S');
	define('CalendarTableDaySunday', 'D');

	define('ErrorParseJSON', 'Răspunsul JSON returnat de către server nu poate fi analizat.');

	define('ErrorLoadCalendar', 'Nu se pot încărca calendarele');
	define('ErrorLoadEvents', 'Nu se pot încărca evenimentele');
	define('ErrorUpdateEvent', 'Nu se poate salva evenimentul');
	define('ErrorDeleteEvent', 'Nu se poate șterge evenimentul');
	define('ErrorUpdateCalendar', 'Nu se poate salva calendarul');
	define('ErrorDeleteCalendar', 'Nu se poate șterge caledarul');
	define('ErrorGeneral', 'A apărut o eroare pe server. Vă rugăm încercați mai târziu.');

define('BackToCart', 'Înapoi la panoul de administrare');
define('StoreWebmail', 'Webmail-ul magazinului');