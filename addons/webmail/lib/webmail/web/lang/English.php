<?php
//WebMail Pro supports the following languages:
//Dansk, Nederlands, English, Français, Deutsch, Magyar, Português Brasil, Polski, Русский, Svenska, Türkçe
	define('PROC_ERROR_ACCT_CREATE', 'There was an error while creating account');
	define('PROC_WRONG_ACCT_PWD', 'Wrong account password');
	define('PROC_CANT_LOG_NONDEF', 'Can\'t login into non-default account');
	define('PROC_CANT_INS_NEW_FILTER', 'Can\'t insert new filter');
	define('PROC_FOLDER_EXIST', 'Folder name already exist');
	define('PROC_CANT_CREATE_FLD', 'Can\'t create folder');
	define('PROC_CANT_INS_NEW_GROUP', 'Can\'t insert new group');
	define('PROC_CANT_INS_NEW_CONT', 'Can\'t insert new contact');
	define('PROC_CANT_INS_NEW_CONTS', 'Can\'t insert new contact(s)');
	define('PROC_CANT_ADD_NEW_CONT_TO_GRP', 'Can\'t add contact(s) into group');
	define('PROC_ERROR_ACCT_UPDATE', 'There was an error while updating account');
	define('PROC_CANT_UPDATE_CONT_SETTINGS', 'Can\'t update contacts settings');
	define('PROC_CANT_GET_SETTINGS', 'Can\'t get settings');
	define('PROC_CANT_UPDATE_ACCT', 'Can\'t update account');
	define('PROC_ERROR_DEL_FLD', 'There was an error while deleting folder(s)');
	define('PROC_CANT_UPDATE_CONT', 'Can\'t update contact');
	define('PROC_CANT_GET_FLDS', 'Can\'t get folders tree');
	define('PROC_CANT_GET_MSG_LIST', 'Can\'t get message list');
	define('PROC_MSG_HAS_DELETED', 'This message has already been deleted from the mail server');
	define('PROC_CANT_LOAD_CONT_SETTINGS', 'Can\'t load contacts settings');
	define('PROC_CANT_LOAD_SIGNATURE', 'Can\'t load account signature');
	define('PROC_CANT_GET_CONT_FROM_DB', 'Can\'t get contact from DB');
	define('PROC_CANT_GET_CONTS_FROM_DB', 'Can\'t get contact(s) from DB');
	define('PROC_CANT_DEL_ACCT_BY_ID', 'Can\'t delete account');
	define('PROC_CANT_DEL_FILTER_BY_ID', 'Can\'t delete filter');
	define('PROC_CANT_DEL_CONT_GROUPS', 'Can\'t delete contact(s) and/or groups');
	define('PROC_WRONG_ACCT_ACCESS', 'An attempt of unauthorized access to account of another user detected.');
	define('PROC_SESSION_ERROR', 'The previous session was terminated due to a timeout.');

	define('MailBoxIsFull', 'Mailbox is full');
	define('WebMailException', 'WebMail exception occured');
	define('InvalidUid', 'Invalid Message UID');
	define('CantCreateContactGroup', 'Can\'t create contact group');
	define('CantCreateUser', 'Can\'t create user');
	define('CantCreateAccount', 'Can\'t create account');
	define('SessionIsEmpty', 'Session is empty');
	define('FileIsTooBig', 'The file is too big');

	define('PROC_CANT_MARK_ALL_MSG_READ', 'Can\'t mark all messages as read');
	define('PROC_CANT_MARK_ALL_MSG_UNREAD', 'Can\'t mark all messages as unread');
	define('PROC_CANT_PURGE_MSGS', 'Can\'t purge message(s)');
	define('PROC_CANT_DEL_MSGS', 'Can\'t delete message(s)');
	define('PROC_CANT_UNDEL_MSGS', 'Can\'t undelete message(s)');
	define('PROC_CANT_MARK_MSGS_READ', 'Can\'t mark message(s) as read');
	define('PROC_CANT_MARK_MSGS_UNREAD', 'Can\'t mark message(s) as unread');
	define('PROC_CANT_SET_MSG_FLAGS', 'Can\'t set message flag(s)');
	define('PROC_CANT_REMOVE_MSG_FLAGS', 'Can\'t remove message flag(s)');
	define('PROC_CANT_CHANGE_MSG_FLD', 'Can\'t change message(s) folder');
	define('PROC_CANT_SEND_MSG', 'Can\'t send message.');
	define('PROC_CANT_SAVE_MSG', 'Can\'t save message.');
	define('PROC_CANT_GET_ACCT_LIST', 'Can\'t get account list');
	define('PROC_CANT_GET_FILTER_LIST', 'Can\'t get filters list');

	define('PROC_CANT_LEAVE_BLANK', 'You can\'t leave * fields blank');

	define('PROC_CANT_UPD_FLD', 'Can\'t update folder');
	define('PROC_CANT_UPD_FILTER', 'Can\'t update filter');

	define('ACCT_CANT_ADD_DEF_ACCT', 'This account cannot be added because it\'s used as a default account by another user.');
	define('ACCT_CANT_UPD_TO_DEF_ACCT', 'This account status cannot be changed to default.');
	define('ACCT_CANT_CREATE_IMAP_ACCT', 'Can\'t create new account (IMAP4 connection error)');
	define('ACCT_CANT_DEL_LAST_DEF_ACCT', 'Can\'t delete last default account');

	define('LANG_LoginInfo', 'Login Information');
	define('LANG_Email', 'Email');
	define('LANG_Login', 'Login');
	define('LANG_Password', 'Password');
	define('LANG_IncServer', 'Incoming Mail');
	define('LANG_PopProtocol', 'POP3');
	define('LANG_ImapProtocol', 'IMAP4');
	define('LANG_IncPort', 'Port');
	define('LANG_OutServer', 'SMTP Server');
	define('LANG_OutPort', 'Port');
	define('LANG_UseSmtpAuth', 'Use SMTP authentication');
	define('LANG_SignMe', 'Sign me in automatically');
	define('LANG_Enter', 'Enter');

	define('JS_LANG_TitleLogin', 'Login');
	define('JS_LANG_TitleMessagesListView', 'Messages List');
	define('JS_LANG_TitleMessagesList', 'Messages List');
	define('JS_LANG_TitleViewMessage', 'View Message');
	define('JS_LANG_TitleNewMessage', 'New Message');
	define('JS_LANG_TitleSettings', 'Settings');
	define('JS_LANG_TitleContacts', 'Contacts');

	define('JS_LANG_StandardLogin', 'Standard&nbsp;Login');
	define('JS_LANG_AdvancedLogin', 'Advanced&nbsp;Login');

	define('JS_LANG_InfoWebMailLoading', 'Please wait while WebMail is loading&hellip;');
	define('JS_LANG_Loading', 'Loading&hellip;');
	define('JS_LANG_InfoMessagesLoad', 'Please wait while WebMail is loading messages list');
	define('JS_LANG_InfoEmptyFolder', 'The folder is empty');
	define('JS_LANG_InfoPageLoading', 'The page is still loading...');
	define('JS_LANG_InfoSendMessage', 'The message was sent');
	define('JS_LANG_InfoSaveMessage', 'The message was saved');
// You have imported 3 new contact(s) into your contacts list.
	define('JS_LANG_InfoHaveImported', 'You have imported');
	define('JS_LANG_InfoNewContacts', 'new contact(s) into your contacts list.');
	define('JS_LANG_InfoToDelete', 'To delete');
	define('JS_LANG_InfoDeleteContent', 'folder you should delete all its contents first.');
	define('JS_LANG_InfoDeleteNotEmptyFolders', 'Deleting non-empty folders is not allowed. To delete non-checkable folders, delete their contents first.');
	define('JS_LANG_InfoRequiredFields', '* required fields');

	define('JS_LANG_ConfirmAreYouSure', 'Are you sure?');
	define('JS_LANG_ConfirmDirectModeAreYouSure', 'The selected message(s) will be PERMANENTLY deleted! Are you sure?');
	define('JS_LANG_ConfirmSaveSettings', 'The settings was not saved. Select OK to save.');
	define('JS_LANG_ConfirmSaveContactsSettings', 'The contacts settings was not saved. Select OK to save.');
	define('JS_LANG_ConfirmSaveAcctProp', 'The account properties were not saved. Select OK to save.');
	define('JS_LANG_ConfirmSaveFilter', 'The filters properties were not saved. Select OK to save.');
	define('JS_LANG_ConfirmSaveSignature', 'The signature was not saved. Select OK to save.');
	define('JS_LANG_ConfirmSavefolders', 'The folders was not saved. Select OK to save.');
	define('JS_LANG_ConfirmHtmlToPlain', 'Warning: By changing the formatting of this message from HTML to plain text, you will lose any current formatting in the message. Select OK to continue.');
	define('JS_LANG_ConfirmAddFolder', 'Before adding/removing folder it is necessary to apply changes. Select OK to save.');
	define('JS_LANG_ConfirmEmptySubject', 'The subject field is empty. Do you wish to continue?');

	define('JS_LANG_WarningEmailBlank', 'You cannot leave<br />Email: field blank');
	define('JS_LANG_WarningLoginBlank', 'You cannot leave<br />Login: field blank');
	define('JS_LANG_WarningToBlank', 'You cannot leave To: field blank');
	define('JS_LANG_WarningServerPortBlank', 'You cannot leave POP3 and<br />SMTP server/port fields blank');
	define('JS_LANG_WarningEmptySearchLine', 'Empty search line. Please enter substring you need to find');
	define('JS_LANG_WarningMarkListItem', 'Please mark at least one item in the list');
	define('JS_LANG_WarningFolderMove', 'The folder can\'t be moved because this is another level');
	define('JS_LANG_WarningContactNotComplete', 'Please enter email or name');
	define('JS_LANG_WarningGroupNotComplete', 'Please enter group name');

	define('JS_LANG_WarningEmailFieldBlank', 'You cannot leave Email field blank');
	define('JS_LANG_WarningIncServerBlank', 'You cannot leave POP3(IMAP4) Server field blank');
	define('JS_LANG_WarningIncPortBlank', 'You cannot leave POP3(IMAP4) Server Port field blank');
	define('JS_LANG_WarningIncLoginBlank', 'You cannot leave POP3(IMAP4) Login field blank');
	define('JS_LANG_WarningIncPortNumber', 'You should specify a positive number in POP3(IMAP4) port field.');
	define('JS_LANG_DefaultIncPortNumber', 'Default POP3(IMAP4) port number is 110(143).');
	define('JS_LANG_WarningIncPassBlank', 'You cannot leave POP3(IMAP4) Password field blank');
	define('JS_LANG_WarningOutPortBlank', 'You cannot leave SMTP Server Port field blank');
	define('JS_LANG_WarningOutPortNumber', 'You should specify a positive number in SMTP port field.');
	define('JS_LANG_WarningCorrectEmail', 'You should specify a correct e-mail.');
	define('JS_LANG_DefaultOutPortNumber', 'Default SMTP port number is 25.');

	define('JS_LANG_WarningCsvExtention', 'Extention should be .csv');
	define('JS_LANG_WarningImportFileType', 'Please select the application that you want to copy your contacts from');
	define('JS_LANG_WarningEmptyImportFile', 'Please select a file by clicking the browse button');

	define('JS_LANG_WarningContactsPerPage', 'Contacts per page value is positive number');
	define('JS_LANG_WarningMessagesPerPage', 'Messages per page value is positive number');
	define('JS_LANG_WarningMailsOnServerDays', 'You should specify a positive number in Messages on server days field.');
	define('JS_LANG_WarningEmptyFilter', 'Please enter substring');
	define('JS_LANG_WarningEmptyFolderName', 'Please enter folder name');

	define('JS_LANG_ErrorConnectionFailed', 'Connection is unsuccessful');
	define('JS_LANG_ErrorRequestFailed', 'The data transfer has not been completed');
	define('JS_LANG_ErrorAbsentXMLHttpRequest', 'The object XMLHttpRequest is absent');
	define('JS_LANG_ErrorWithoutDesc', 'The error without description occured');
	define('JS_LANG_ErrorParsing', 'Error while parsing XML.');
	define('JS_LANG_ResponseText', 'Response text:');
	define('JS_LANG_ErrorEmptyXmlPacket', 'Empty XML packet');
	define('JS_LANG_ErrorImportContacts', 'Error while importing contacts');
	define('JS_LANG_ErrorNoContacts', 'No contacts to import.');
	define('JS_LANG_ErrorCheckMail', 'Receiving messages terminated due to an error. Probably, not all the messages were received.');

	define('JS_LANG_LoggingToServer', 'Logging on to server&hellip;');
	define('JS_LANG_GettingMsgsNum', 'Getting number of messages');
	define('JS_LANG_RetrievingMessage', 'Retrieving message');
	define('JS_LANG_DeletingMessage', 'Deleting message');
	define('JS_LANG_DeletingMessages', 'Deleting message(s)');
	define('JS_LANG_Of', 'of');
	define('JS_LANG_Connection', 'Connection');
	define('JS_LANG_Charset', 'Charset');
	define('JS_LANG_AutoSelect', 'Auto-Select');

	define('JS_LANG_Contacts', 'Contacts');
	define('JS_LANG_ClassicVersion', 'Classic Version');
	define('JS_LANG_Logout', 'Logout');
	define('JS_LANG_Settings', 'Settings');

	define('JS_LANG_LookFor', 'Look for');
	define('JS_LANG_SearchIn', 'Search in');
	define('JS_LANG_QuickSearch', 'Search the From, To and Subject fields only (quicker).');
	define('JS_LANG_SlowSearch', 'Search entire message');
	define('JS_LANG_AllMailFolders', 'All Mail Folders');
	define('JS_LANG_AllGroups', 'All Groups');

	define('JS_LANG_NewMessage', 'New Message');
	define('JS_LANG_CheckMail', 'Check Mail');
	define('JS_LANG_ReloadFolders', 'Reload Folders Tree');
	define('JS_LANG_EmptyTrash', 'Empty Trash');
	define('JS_LANG_MarkAsRead', 'Mark As Read');
	define('JS_LANG_MarkAsUnread', 'Mark As Unread');
	define('JS_LANG_MarkFlag', 'Flag');
	define('JS_LANG_MarkUnflag', 'Unflag');
	define('JS_LANG_MarkAllRead', 'Mark All Read');
	define('JS_LANG_MarkAllUnread', 'Mark All Unread');
	define('JS_LANG_Reply', 'Reply');
	define('JS_LANG_ReplyAll', 'Reply to All');
	define('JS_LANG_Delete', 'Delete');
	define('JS_LANG_Undelete', 'Undelete');
	define('JS_LANG_PurgeDeleted', 'Purge deleted');
	define('JS_LANG_MoveToFolder', 'Move to Folder');
	define('JS_LANG_Forward', 'Forward');

	define('JS_LANG_HideFolders', 'Hide Folders');
	define('JS_LANG_ShowFolders', 'Show Folders');
	define('JS_LANG_ManageFolders', 'Manage Folders');
	define('JS_LANG_SyncFolder', 'Synchronized folder');
	define('JS_LANG_NewMessages', 'New Messages');
	define('JS_LANG_Messages', 'Message(s)');

	define('JS_LANG_From', 'From');
	define('JS_LANG_To', 'To');
	define('JS_LANG_Date', 'Date');
	define('JS_LANG_Size', 'Size');
	define('JS_LANG_Subject', 'Subject');

	define('JS_LANG_FirstPage', 'First Page');
	define('JS_LANG_PreviousPage', 'Previous Page');
	define('JS_LANG_NextPage', 'Next Page');
	define('JS_LANG_LastPage', 'Last Page');

	define('JS_LANG_SwitchToPlain', 'Switch to Plain Text View');
	define('JS_LANG_SwitchToHTML', 'Switch to HTML View');
	define('JS_LANG_AddToAddressBokk', 'Add to Address Book');
	define('JS_LANG_ClickToDownload', 'Click to download');
	define('JS_LANG_View', 'View');
	define('JS_LANG_ShowFullHeaders', 'Show Full Headers');
	define('JS_LANG_HideFullHeaders', 'Hide Full Headers');

	define('JS_LANG_MessagesInFolder', 'Message(s) in Folder');
	define('JS_LANG_YouUsing', 'You are using');
	define('JS_LANG_OfYour', 'of your');
	define('JS_LANG_Mb', 'MB');
	define('JS_LANG_Kb', 'KB');
	define('JS_LANG_B', 'B');

	define('JS_LANG_SendMessage', 'Send');
	define('JS_LANG_SaveMessage', 'Save');
	define('JS_LANG_Print', 'Print');
	define('JS_LANG_PreviousMsg', 'Previous Message');
	define('JS_LANG_NextMsg', 'Next Message');
	define('JS_LANG_AddressBook', 'Address Book');
	define('JS_LANG_ShowBCC', 'Show BCC');
	define('JS_LANG_HideBCC', 'Hide BCC');
	define('JS_LANG_CC', 'CC');
	define('JS_LANG_BCC', 'BCC');
	define('JS_LANG_ReplyTo', 'Reply To');
	define('JS_LANG_AttachFile', 'Attach File');
	define('JS_LANG_Attach', 'Attach');
	define('JS_LANG_Re', 'Re');
	define('JS_LANG_OriginalMessage', 'Original Message');
	define('JS_LANG_Sent', 'Sent');
	define('JS_LANG_Fwd', 'Fwd');
	define('JS_LANG_Low', 'Low');
	define('JS_LANG_Normal', 'Normal');
	define('JS_LANG_High', 'High');
	define('JS_LANG_Importance', 'Importance');
	define('JS_LANG_Close', 'Close');

	define('JS_LANG_Common', 'Common');
	define('JS_LANG_EmailAccounts', 'Email Accounts');

	define('JS_LANG_MsgsPerPage', 'Messages per page');
	define('JS_LANG_DisableRTE', 'Disable rich-text editor');
	define('JS_LANG_Skin', 'Skin');
	define('JS_LANG_DefCharset', 'Default charset');
	define('JS_LANG_DefCharsetInc', 'Default incoming charset');
	define('JS_LANG_DefCharsetOut', 'Default outgoing charset');
	define('JS_LANG_DefTimeOffset', 'Default time offset');
	define('JS_LANG_DefLanguage', 'Default language');
	define('JS_LANG_DefDateFormat', 'Default date format');
	define('JS_LANG_ShowViewPane', 'Messages list with preview pane');
	define('JS_LANG_Save', 'Save');
	define('JS_LANG_Cancel', 'Cancel');
	define('JS_LANG_OK', 'OK');

	define('JS_LANG_Remove', 'Remove');
	define('JS_LANG_AddNewAccount', 'Add New Account');
	define('JS_LANG_Signature', 'Signature');
	define('JS_LANG_Filters', 'Filters');
	define('JS_LANG_Properties', 'Properties');
	define('JS_LANG_UseForLogin', 'Use this account properties (login and password) for login');
	define('JS_LANG_MailFriendlyName', 'Your name');
	define('JS_LANG_MailEmail', 'Email');
	define('JS_LANG_MailIncHost', 'Incoming Mail');
	define('JS_LANG_Imap4', 'Imap4');
	define('JS_LANG_Pop3', 'Pop3');
	define('JS_LANG_MailIncPort', 'Port');
	define('JS_LANG_MailIncLogin', 'Login');
	define('JS_LANG_MailIncPass', 'Password');
	define('JS_LANG_MailOutHost', 'SMTP Server');
	define('JS_LANG_MailOutPort', 'Port');
	define('JS_LANG_MailOutLogin', 'SMTP Login');
	define('JS_LANG_MailOutPass', 'SMTP Password');
	define('JS_LANG_MailOutAuth1', 'Use SMTP authentication');
	define('JS_LANG_MailOutAuth2', '(You may leave SMTP login/password fields blank, if they\'re the same as POP3/IMAP4 login/password)');
	define('JS_LANG_UseFriendlyNm1', 'Use Friendly Name in "From:" field');
	define('JS_LANG_UseFriendlyNm2', '(Your name &lt;sender@mail.com&gt;)');
	define('JS_LANG_GetmailAtLogin', 'Get/Synchronize Mails at login');
	define('JS_LANG_MailMode0', 'Delete received messages from server');
	define('JS_LANG_MailMode1', 'Leave messages on server');
	define('JS_LANG_MailMode2', 'Keep messages on server for');
	define('JS_LANG_MailsOnServerDays', 'day(s)');
	define('JS_LANG_MailMode3', 'Delete message from server when it is removed from Trash');
	define('JS_LANG_InboxSyncType', 'Type of Inbox Synchronization');

	define('JS_LANG_SyncTypeNo', 'Don\'t Synchronize');
	define('JS_LANG_SyncTypeNewHeaders', 'New Headers');
	define('JS_LANG_SyncTypeAllHeaders', 'All Headers');
	define('JS_LANG_SyncTypeNewMessages', 'New Messages');
	define('JS_LANG_SyncTypeAllMessages', 'All Messages');
	define('JS_LANG_SyncTypeDirectMode', 'Direct Mode');

	define('JS_LANG_Pop3SyncTypeEntireHeaders', 'Headers Only');
	define('JS_LANG_Pop3SyncTypeEntireMessages', 'Entire Messages');
	define('JS_LANG_Pop3SyncTypeDirectMode', 'Direct Mode');

	define('JS_LANG_DeleteFromDb', 'Delete message from database if it no longer exists on mail server');

	define('JS_LANG_EditFilter', 'Edit&nbsp;filter');
	define('JS_LANG_NewFilter', 'Add New Filter');
	define('JS_LANG_Field', 'Field');
	define('JS_LANG_Condition', 'Condition');
	define('JS_LANG_ContainSubstring', 'Contain substring');
	define('JS_LANG_ContainExactPhrase', 'Contain exact phrase');
	define('JS_LANG_NotContainSubstring', 'Not contain substring');
	define('JS_LANG_FilterDesc_At', 'at');
	define('JS_LANG_FilterDesc_Field', 'field');
	define('JS_LANG_Action', 'Action');
	define('JS_LANG_DoNothing', 'Do nothing');
	define('JS_LANG_DeleteFromServer', 'Delete from server Immediately');
	define('JS_LANG_MarkGrey', 'Mark grey');
	define('JS_LANG_Add', 'Add');
	define('JS_LANG_OtherFilterSettings', 'Other filter settings');
	define('JS_LANG_ConsiderXSpam', 'Consider X-Spam headers');
	define('JS_LANG_Apply', 'Apply');

	define('JS_LANG_InsertLink', 'Insert Link');
	define('JS_LANG_RemoveLink', 'Remove Link');
	define('JS_LANG_Numbering', 'Numbering');
	define('JS_LANG_Bullets', 'Bullets');
	define('JS_LANG_HorizontalLine', 'Horizontal Line');
	define('JS_LANG_Bold', 'Bold');
	define('JS_LANG_Italic', 'Italic');
	define('JS_LANG_Underline', 'Underline');
	define('JS_LANG_AlignLeft', 'Align Left');
	define('JS_LANG_Center', 'Center');
	define('JS_LANG_AlignRight', 'Align Right');
	define('JS_LANG_Justify', 'Justify');
	define('JS_LANG_FontColor', 'Font Color');
	define('JS_LANG_Background', 'Background');
	define('JS_LANG_SwitchToPlainMode', 'Switch to Plain Text Mode');
	define('JS_LANG_SwitchToHTMLMode', 'Switch to HTML Mode');
	define('JS_LANG_AddSignatures', 'Add signatures to all outgoing messages');
	define('JS_LANG_DontAddToReplies', 'Don\'t add signatures to Replies and Forwards');

	define('JS_LANG_Folder', 'Folder');
	define('JS_LANG_Msgs', 'Msg\'s');
	define('JS_LANG_Synchronize', 'Synchronize');
	define('JS_LANG_ShowThisFolder', 'Show This Folder');
	define('JS_LANG_Total', 'Total');
	define('JS_LANG_DeleteSelected', 'Delete Selected');
	define('JS_LANG_AddNewFolder', 'Add New Folder');
	define('JS_LANG_NewFolder', 'New Folder');
	define('JS_LANG_ParentFolder', 'Parent Folder');
	define('JS_LANG_NoParent', 'No Parent');
	define('JS_LANG_OnMailServer', 'Create this Folder in WebMail and on Mail Server');
	define('JS_LANG_InWebMail', 'Create this Folder Only in WebMail');
	define('JS_LANG_FolderName', 'Folder Name');

	define('JS_LANG_ContactsPerPage', 'Contacts per page');
	define('JS_LANG_WhiteList', 'Address Book as White List');

	define('JS_LANG_CharsetDefault', 'Default');
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

	define('JS_LANG_TimeDefault', 'Default');
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

	define('JS_LANG_DateDefault', 'Default');
	define('JS_LANG_DateDDMMYY', 'DD/MM/YY');
	define('JS_LANG_DateMMDDYY', 'MM/DD/YY');
	define('JS_LANG_DateDDMonth', 'DD Month (01 Jan)');
	define('JS_LANG_DateAdvanced', 'Advanced');

	define('JS_LANG_NewContact', 'New Contact');
	define('JS_LANG_NewGroup', 'New Group');
	define('JS_LANG_AddContactsTo', 'Add Contacts to');
	define('JS_LANG_ImportContacts', 'Import Contacts');

	define('JS_LANG_Name', 'Name');
	define('JS_LANG_Email', 'Email');
	define('JS_LANG_DefaultEmail', 'Default Email');
	define('JS_LANG_NotSpecifiedYet', 'Not specified yet');
	define('JS_LANG_ContactName', 'Name');
	define('JS_LANG_Birthday', 'Birthday');
	define('JS_LANG_Month', 'Month');
	define('JS_LANG_January', 'January');
	define('JS_LANG_February', 'February');
	define('JS_LANG_March', 'March');
	define('JS_LANG_April', 'April');
	define('JS_LANG_May', 'May');
	define('JS_LANG_June', 'June');
	define('JS_LANG_July', 'July');
	define('JS_LANG_August', 'August');
	define('JS_LANG_September', 'September');
	define('JS_LANG_October', 'October');
	define('JS_LANG_November', 'November');
	define('JS_LANG_December', 'December');
	define('JS_LANG_Day', 'Day');
	define('JS_LANG_Year', 'Year');
	define('JS_LANG_UseFriendlyName1', 'Use Friendly Name');
	define('JS_LANG_UseFriendlyName2', '(for example, John Doe &lt;johndoe@mail.com&gt;)');
	define('JS_LANG_Personal', 'Personal');
	define('JS_LANG_PersonalEmail', 'Personal E-mail');
	define('JS_LANG_StreetAddress', 'Street Address');
	define('JS_LANG_City', 'City');
	define('JS_LANG_Fax', 'Fax');
	define('JS_LANG_StateProvince', 'State/Province');
	define('JS_LANG_Phone', 'Phone');
	define('JS_LANG_ZipCode', 'Zip Code');
	define('JS_LANG_Mobile', 'Mobile');
	define('JS_LANG_CountryRegion', 'Country/Region');
	define('JS_LANG_WebPage', 'Web Page');
	define('JS_LANG_Go', 'Go');
	define('JS_LANG_Home', 'Home');
	define('JS_LANG_Business', 'Business');
	define('JS_LANG_BusinessEmail', 'Business E-mail');
	define('JS_LANG_Company', 'Company');
	define('JS_LANG_JobTitle', 'Job Title');
	define('JS_LANG_Department', 'Department');
	define('JS_LANG_Office', 'Office');
	define('JS_LANG_Pager', 'Pager');
	define('JS_LANG_Other', 'Other');
	define('JS_LANG_OtherEmail', 'Other E-mail');
	define('JS_LANG_Notes', 'Notes');
	define('JS_LANG_Groups', 'Groups');
	define('JS_LANG_ShowAddFields', 'Show additional fields');
	define('JS_LANG_HideAddFields', 'Hide additional fields');
	define('JS_LANG_EditContact', 'Edit contact information');
	define('JS_LANG_GroupName', 'Group Name');
	define('JS_LANG_AddContacts', 'Add Contacts');
	define('JS_LANG_CommentAddContacts', '(If you\'re going to specify more than one address, please separate them with commas)');
	define('JS_LANG_CreateGroup', 'Create Group');
	define('JS_LANG_Rename', 'rename');
	define('JS_LANG_MailGroup', 'Mail Group');
	define('JS_LANG_RemoveFromGroup', 'Remove from Group');
	define('JS_LANG_UseImportTo', 'Use Import to copy your contacts from Microsoft Outlook, Microsoft Outlook Express into your MailBee WebMail contacts list.');
	define('JS_LANG_Outlook1', 'Microsoft Outlook 2000/XP/2003');
	define('JS_LANG_Outlook2', 'Microsoft Outlook Express 6');
	define('JS_LANG_SelectImportFile', 'Select the file (.CSV format) that you want to import');
	define('JS_LANG_Import', 'Import');
	define('JS_LANG_ContactsMessage', 'This is contacts page!!!');
	define('JS_LANG_ContactsCount', 'contact(s)');
	define('JS_LANG_GroupsCount', 'group(s)');

// webmail 4.1 constants
	define('PicturesBlocked', 'Pictures in this message have been blocked for your safety.');
	define('ShowPictures', 'Show pictures');
	define('ShowPicturesFromSender', 'Always show pictures in messages from this sender');
	define('AlwaysShowPictures', 'Always show pictures in messages');

	define('TreatAsOrganization', 'Treat as an organization');

	define('WarningGroupAlreadyExist', 'Group with such name already exists. Please specify another name.');
	define('WarningCorrectFolderName', 'You should specify a correct folder name.');
	define('WarningLoginFieldBlank', 'You cannot leave Login field blank.');
	define('WarningCorrectLogin', 'You should specify a correct login.');
	define('WarningPassBlank', 'You cannot leave Password field blank.');
	define('WarningCorrectIncServer', 'You should specify a correct POP3(IMAP) server address.');
	define('WarningCorrectSMTPServer', 'You should specify a correct SMTP server address.');
	define('WarningFromBlank', 'You cannot leave From: field blank.');
	define('WarningAdvancedDateFormat', 'Please specify a date-time format.');

	define('AdvancedDateHelpTitle', 'Advanced Date');
	define('AdvancedDateHelpIntro', 'When the &quot;Advanced&quot; field is selected, you can use the text box to set your own date format, which would be displayed in MailBee WebMail Pro. The following options are used for this purpose along with \':\' or \'/\' delimiter char:');
	define('AdvancedDateHelpConclusion', 'For instance, if you\'ve specified &quot;mm/dd/yyyy&quot; value in the text box of &quot;Advanced&quot; field, the date is displayed as month/day/year (i.e. 11/23/2005)');
	define('AdvancedDateHelpDayOfMonth', 'Day of month (1 through 31)');
	define('AdvancedDateHelpNumericMonth', 'Month (1 through 12)');
	define('AdvancedDateHelpTextualMonth', 'Month (Jan through Dec)');
	define('AdvancedDateHelpYear2', 'Year, 2 digits');
	define('AdvancedDateHelpYear4', 'Year, 4 digits');
	define('AdvancedDateHelpDayOfYear', 'Day of year (1 through 366)');
	define('AdvancedDateHelpQuarter', 'Quarter');
	define('AdvancedDateHelpDayOfWeek', 'Day of week (Mon through Sun)');
	define('AdvancedDateHelpWeekOfYear', 'Week of year (1 through 53)');

	define('InfoNoMessagesFound', 'No messages found.');
	define('ErrorSMTPConnect', 'Can\'t connect to SMTP server. Check SMTP server settings.');
	define('ErrorSMTPAuth', 'Wrong username and/or password. Authentication failed.');
	define('ReportMessageSent', 'Your message has been sent.');
	define('ReportMessageSaved', 'Your message has been saved.');
	define('ErrorPOP3Connect', 'Can\'t connect to POP3 server, check POP3 server settings.');
	define('ErrorIMAP4Connect', 'Can\'t connect to IMAP4 server, check IMAP4 server settings.');
	define('ErrorPOP3IMAP4Auth', 'Wrong email/login and/or password. Authentication failed.');
	define('ErrorGetMailLimit', 'Sorry, your mailbox size limit is exceeded.');

	define('ReportSettingsUpdatedSuccessfuly', 'Settings have been updated successfully.');
	define('ReportAccountCreatedSuccessfuly', 'Account has been created successfully.');
	define('ReportAccountUpdatedSuccessfuly', 'Account has been updated successfully.');
	define('ConfirmDeleteAccount', 'Are you sure you want to delete account?');
	define('ReportFiltersUpdatedSuccessfuly', 'Filters have been updated successfully.');
	define('ReportSignatureUpdatedSuccessfuly', 'Signature has been updated successfully.');
	define('ReportFoldersUpdatedSuccessfuly', 'Folders have been updated successfully.');
	define('ReportContactsSettingsUpdatedSuccessfuly', 'Contacts\' settings have been updated successfully.');

	define('ErrorInvalidCSV', 'CSV file you selected has invalid format.');
// The group "guies" was successfully added.
	define('ReportGroupSuccessfulyAdded1', 'The group');
	define('ReportGroupSuccessfulyAdded2', 'was successfully added.');
	define('ReportGroupUpdatedSuccessfuly', 'Group has been updated successfully.');
	define('ReportContactSuccessfulyAdded', 'Contact was successfully added.');
	define('ReportContactUpdatedSuccessfuly', 'Contact has been updated successfully.');
// Contact(s) was added to group "friends".
	define('ReportContactAddedToGroup', 'Contact(s) was added to group');
	define('AlertNoContactsGroupsSelected', 'No contacts or groups selected.');

	define('InfoListNotContainAddress', 'If the list doesn\'t contain the address you\'re looking for, keep typing its first chars.');

	define('DirectAccess', 'D');
	define('DirectAccessTitle', 'Direct Mode. WebMail accesses messages directly on mail server.');

	define('FolderInbox', 'Inbox');
	define('FolderSentItems', 'Sent Items');
	define('FolderDrafts', 'Drafts');
	define('FolderTrash', 'Trash');

	define('LanguageDanish', 'Danish');
	define('LanguagePolish', 'Polish');

	define('FileLargerAttachment', 'The file size exceeds Attachment Size limit.');
	define('FilePartiallyUploaded', 'Only a part of the file was uploaded due to an unknown error.');
	define('NoFileUploaded', 'No file was uploaded.');
	define('MissingTempFolder', 'The temporary folder is missing.');
	define('MissingTempFile', 'The temporary file is missing.');
	define('UnknownUploadError', 'An unknown file upload error occurred.');
	define('FileLargerThan', 'File upload error. Most probably, the file is larger than ');
	define('PROC_CANT_LOAD_DB', 'Can\'t connect to database.');
	define('PROC_CANT_LOAD_LANG', 'Can\'t find required language file.');
	define('PROC_CANT_LOAD_ACCT', 'The account doesn\'t exist, perhaps, it has just been deleted.');

	define('DomainDosntExist', 'Such domain doesn\'t exist on mail server.');
	define('ServerIsDisable', 'Using mail server is prohibited by administrator.');

	define('PROC_ACCOUNT_EXISTS', 'The account cannot be created because it already exists.');
	define('PROC_CANT_GET_MESSAGES_COUNT', 'Can\'t get folder message count.');
	define('PROC_CANT_MAIL_SIZE', 'Can\'t get mail storage size.');

	define('Organization', 'Organization');
	define('WarningOutServerBlank', 'You cannot leave SMTP Server field blank');

//
	define('JS_LANG_Refresh', 'Refresh');
	define('JS_LANG_MessagesInInbox', 'Message(s) in Inbox');
	define('JS_LANG_InfoEmptyInbox', 'Inbox is empty');

// webmail 4.2 constants
	define('LanguagePortugueseBrazil', 'Portuguese-Brazil');
	define('LanguageHungarian', 'Hungarian');

	define('BackToList', 'Back to List');
	define('InfoNoContactsGroups', 'No contacts or groups.');
	define('InfoNewContactsGroups', 'You can either create new contacts/groups or import contacts from a .CSV file in MS Outlook format.');
	define('DefTimeFormat', 'Default time format');
	define('SpellNoSuggestions', 'No suggestions');
	define('SpellWait', 'Please wait&hellip;');

	define('InfoNoMessageSelected', 'No message selected.');
	define('InfoSingleDoubleClick', 'You can either single-click any message in the list to preview it here or double-click to view it full size.');

// calendar
	define('TitleDay', 'Day View');
	define('TitleWeek', 'Week View');
	define('TitleMonth', 'Month View');

	define('ErrorNotSupportBrowser', 'AfterLogic Calendar doesn\'t support your browser. Please use FireFox 2.0 or higher, Opera 9.0 or      higher, Internet Explorer 6.0 or higher, Safari 3.0.2 or higher.');
	define('ErrorTurnedOffActiveX', 'ActiveX support is turned off . <br/>You should turn it on in order to use this application.');

	define('Calendar', 'Calendar');

	define('TabDay', 'Day');
	define('TabWeek', 'Week');
	define('TabMonth', 'Month');

	define('ToolNewEvent', 'New&nbsp;Event');
	define('ToolBack', 'Back');
	define('ToolToday', 'Today');
	define('AltNewEvent', 'New Event');
	define('AltBack', 'Back');
	define('AltToday', 'Today');
	define('CalendarHeader', 'Calendar');
	define('CalendarsManager', 'Calendars Manager');

	define('CalendarActionNew', 'New calendar');
	define('EventHeaderNew', 'New Event');
	define('CalendarHeaderNew', 'New Calendar');

	define('EventSubject', 'Subject');
	define('EventCalendar', 'Calendar');
	define('EventFrom', 'From');
	define('EventTill', 'till');
	define('CalendarDescription', 'Description');
	define('CalendarColor', 'Color');
	define('CalendarName', 'Calendar Name');
	define('CalendarDefaultName', 'My Calendar');

	define('ButtonSave', 'Save');
	define('ButtonCancel', 'Cancel');
	define('ButtonDelete', 'Delete');

	define('AltPrevMonth', 'Prev Month');
	define('AltNextMonth', 'Next Month');

	define('CalendarHeaderEdit', 'Edit Calendar');
	define('CalendarActionEdit', 'Edit Calendar');
	define('ConfirmDeleteCalendar', 'Are you sure you want to delete calendar');
	define('InfoDeleting', 'Deleting...');
	define('WarningCalendarNameBlank', 'You cannot leave the calendar name blank.');
	define('ErrorCalendarNotCreated', 'Calendar not created.');
	define('WarningSubjectBlank', 'You cannot leave the subject blank.');
	define('WarningIncorrectTime', 'The specified time contains illegal characters.');
	define('WarningIncorrectFromTime', 'The from time is incorrect.');
	define('WarningIncorrectTillTime', 'The till time is incorrect.');
	define('WarningStartEndDate', 'The end date must be greater or equal to the start date.');
	define('WarningStartEndTime', 'The end time must be greater than the start time.');
	define('WarningIncorrectDate', 'The date must be correct.');
	define('InfoLoading', 'Loading...');
	define('EventCreate', 'Create event');
	define('CalendarHideOther', 'Hide other calendars');
	define('CalendarShowOther', 'Show other calendars');
	define('CalendarRemove', 'Remove Calendar');
	define('EventHeaderEdit', 'Edit Event');

	define('InfoSaving', 'Saving...');
	define('SettingsDisplayName', 'Display Name');
	define('SettingsTimeFormat', 'Time Format');
	define('SettingsDateFormat', 'Date Format');
	define('SettingsShowWeekends', 'Show weekends');
	define('SettingsWorkdayStarts', 'Workday starts');
	define('SettingsWorkdayEnds', 'ends');
	define('SettingsShowWorkday', 'Show workday');
	define('SettingsWeekStartsOn', 'Week starts on');
	define('SettingsDefaultTab', 'Default Tab');
	define('SettingsCountry', 'Country');
	define('SettingsTimeZone', 'Time Zone');
	define('SettingsAllTimeZones', 'All time zones');

	define('WarningWorkdayStartsEnds', 'The \'Workday ends\' time must be greater than the \'Workday starts\' time');
	define('ReportSettingsUpdated', 'Settings have been updated successfully.');

	define('SettingsTabCalendar', 'Calendar');

	define('FullMonthJanuary', 'January');
	define('FullMonthFebruary', 'February');
	define('FullMonthMarch', 'March');
	define('FullMonthApril', 'April');
	define('FullMonthMay', 'May');
	define('FullMonthJune', 'June');
	define('FullMonthJuly', 'July');
	define('FullMonthAugust', 'August');
	define('FullMonthSeptember', 'September');
	define('FullMonthOctober', 'October');
	define('FullMonthNovember', 'November');
	define('FullMonthDecember', 'December');

	define('ShortMonthJanuary', 'Jan');
	define('ShortMonthFebruary', 'Feb');
	define('ShortMonthMarch', 'Mar');
	define('ShortMonthApril', 'Apr');
	define('ShortMonthMay', 'May');
	define('ShortMonthJune', 'Jun');
	define('ShortMonthJuly', 'Jul');
	define('ShortMonthAugust', 'Aug');
	define('ShortMonthSeptember', 'Sep');
	define('ShortMonthOctober', 'Oct');
	define('ShortMonthNovember', 'Nov');
	define('ShortMonthDecember', 'Dec');

	define('FullDayMonday', 'Monday');
	define('FullDayTuesday', 'Tuesday');
	define('FullDayWednesday', 'Wednesday');
	define('FullDayThursday', 'Thursday');
	define('FullDayFriday', 'Friday');
	define('FullDaySaturday', 'Saturday');
	define('FullDaySunday', 'Sunday');

	define('DayToolMonday', 'Mon');
	define('DayToolTuesday', 'Tue');
	define('DayToolWednesday', 'Wed');
	define('DayToolThursday', 'Thu');
	define('DayToolFriday', 'Fri');
	define('DayToolSaturday', 'Sat');
	define('DayToolSunday', 'Sun');

	define('CalendarTableDayMonday', 'M');
	define('CalendarTableDayTuesday', 'T');
	define('CalendarTableDayWednesday', 'W');
	define('CalendarTableDayThursday', 'T');
	define('CalendarTableDayFriday', 'F');
	define('CalendarTableDaySaturday', 'S');
	define('CalendarTableDaySunday', 'S');

	define('ErrorParseJSON', 'The JSON response returned by the server cannot be parsed.');

	define('ErrorLoadCalendar', 'Unable to load calendars');
	define('ErrorLoadEvents', 'Unable to load events');
	define('ErrorUpdateEvent', 'Unable to save event');
	define('ErrorDeleteEvent', 'Unable to delete event');
	define('ErrorUpdateCalendar', 'Unable to save calendar');
	define('ErrorDeleteCalendar', 'Unable to delete calendar');
	define('ErrorGeneral', 'An error occured on the server. Try again later.');

define('BackToCart', 'Back to administration panel');
define('StoreWebmail', 'Store webmail');