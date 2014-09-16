

DemoWarning = '';


// defines for sections
var SECTION_LOGIN    = 0;
var SECTION_MAIL     = 1;
var SECTION_SETTINGS = 2;
var SECTION_CONTACTS = 3;
var SECTION_CALENDAR = 4;

// defines for screens
var SCREEN_LOGIN              = 0;
var SCREEN_MESSAGES_LIST_VIEW = 1;
var SCREEN_MESSAGES_LIST      = 2;
var SCREEN_VIEW_MESSAGE       = 3;
var SCREEN_NEW_MESSAGE        = 4;
var SCREEN_USER_SETTINGS      = 5;
	var PART_COMMON_SETTINGS    = 0;
	var PART_ACCOUNT_PROPERTIES = 1;
	var PART_FILTERS            = 2;
	var PART_SIGNATURE          = 3;
	var PART_MANAGE_FOLDERS     = 4;
	var PART_CONTACTS_SETTINGS  = 5;
	var PART_CALENDAR_SETTINGS  = 6;
var SCREEN_CONTACTS           = 6;
	var PART_CONTACTS       = 0;
	var PART_NEW_CONTACT    = 1;
	var PART_VIEW_CONTACT   = 2;
	var PART_EDIT_CONTACT   = 3;
	var PART_NEW_GROUP      = 4;
	var PART_VIEW_GROUP     = 5;
	var PART_IMPORT_CONTACT = 6;
var SCREEN_CALENDAR           = 7;

var Sections = Array();
Sections[SECTION_LOGIN]    = {Scripts: [], Screens: Array()};
Sections[SECTION_MAIL]     = {Scripts: [], Screens: Array()};
Sections[SECTION_MAIL].Screens[SCREEN_MESSAGES_LIST_VIEW] = 'screen = new CMessagesListViewScreen(SkinName);';
Sections[SECTION_MAIL].Screens[SCREEN_MESSAGES_LIST] = 'screen = new CMessagesListScreen(SkinName);';
Sections[SECTION_MAIL].Screens[SCREEN_VIEW_MESSAGE] = 'screen = new CViewMessageScreen(SkinName);';
Sections[SECTION_MAIL].Screens[SCREEN_NEW_MESSAGE] = 'screen = new CNewMessageScreen(SkinName);';
//Sections[SECTION_SETTINGS] = {Scripts: ['screen.user-settings.js', 'screen.common-settings.js', 'screen.accounts-settings.js', 'screen.account-properties.js', 'screen.contacts-settings.js'], Screens: Array()}
Sections[SECTION_SETTINGS] = {Scripts: [], Screens: Array()};
Sections[SECTION_SETTINGS].Screens[SCREEN_USER_SETTINGS] = 'screen = new CUserSettingsScreen(SkinName);';
//Sections[SECTION_CONTACTS] = {Scripts: ['screen.contacts.js', 'screen.view-contact.js'], Screens: Array()}
Sections[SECTION_CONTACTS] = {Scripts: [], Screens: Array()};
Sections[SECTION_CONTACTS].Screens[SCREEN_CONTACTS] = 'screen = new CContactsScreen(SkinName);';
Sections[SECTION_CALENDAR] = {Scripts: [], Screens: Array()};
Sections[SECTION_CALENDAR].Screens[SCREEN_CALENDAR] = 'screen = new CCalendarScreen(SkinName);';

var Screens = Array();
Screens[SCREEN_LOGIN]              = {SectionId: SECTION_LOGIN,    PreRender: true,  ShowHandler: ''};
Screens[SCREEN_MESSAGES_LIST_VIEW] = {SectionId: SECTION_MAIL,     PreRender: true,  ShowHandler: ''};
Screens[SCREEN_MESSAGES_LIST]      = {SectionId: SECTION_MAIL,     PreRender: false, ShowHandler: ''};
Screens[SCREEN_VIEW_MESSAGE]       = {SectionId: SECTION_MAIL,     PreRender: false, ShowHandler: ''};
Screens[SCREEN_NEW_MESSAGE]        = {SectionId: SECTION_MAIL,     PreRender: true,  ShowHandler: ''};
Screens[SCREEN_USER_SETTINGS]      = {SectionId: SECTION_SETTINGS, PreRender: true,  ShowHandler: ''};
Screens[SCREEN_CONTACTS]           = {SectionId: SECTION_CONTACTS, PreRender: true,  ShowHandler: ''};
Screens[SCREEN_CALENDAR]           = {SectionId: SECTION_CALENDAR, PreRender: true,  ShowHandler: ''};

// defines data types
var TYPE_SETTINGS_LIST      = 0;
var TYPE_UPDATE             = 1;
var TYPE_ACCOUNTS_LIST      = 2;
var TYPE_FOLDERS_LIST       = 3;
var TYPE_MESSAGES_LIST      = 4;
var TYPE_MESSAGES_OPERATION = 5;
var TYPE_MESSAGE            = 6;
var TYPE_USER_SETTINGS      = 7;
var TYPE_ACCOUNT_PROPERTIES = 8;
var TYPE_FILTERS            = 9;
var TYPE_FILTER_PROPERTIES  = 10;
var TYPE_X_SPAM             = 11;
var TYPE_CONTACTS_SETTINGS  = 12;
var TYPE_SIGNATURE          = 13;
var TYPE_FOLDERS            = 14;
var TYPE_CONTACTS           = 15;
var TYPE_CONTACT            = 16;
var TYPE_GROUPS             = 17;
var TYPE_GROUP              = 18;
var TYPE_SPELLCHECK			= 19;
var TYPE_CALENDAR_SETTINGS  = 20;

//defines for folder types
var FOLDER_TYPE_DEFAULT      = 0;
var FOLDER_TYPE_INBOX        = 1;
var FOLDER_TYPE_SENT         = 2;
var FOLDER_TYPE_DRAFTS       = 3;
var FOLDER_TYPE_TRASH        = 4;
var FOLDER_TYPE_DEFAULT_SYNC = 5;
var FOLDER_TYPE_INBOX_SYNC   = 6;
var FOLDER_TYPE_SENT_SYNC    = 7;
var FOLDER_TYPE_DRAFTS_SYNC  = 8;
var FOLDER_TYPE_TRASH_SYNC   = 9;

//defines for sync type
var SYNC_TYPE_NO          = 0;
var SYNC_TYPE_NEW_HEADERS = 1;
var SYNC_TYPE_ALL_HEADERS = 2;
var SYNC_TYPE_NEW_MSGS    = 3;
var SYNC_TYPE_ALL_MSGS    = 4;
var SYNC_TYPE_DIRECT_MODE = 5;

var FolderImages = Array();
FolderImages[FOLDER_TYPE_DEFAULT]      = 'folder.gif';
FolderImages[FOLDER_TYPE_INBOX]        = 'folder_inbox.gif';
FolderImages[FOLDER_TYPE_SENT]         = 'folder_send.gif';
FolderImages[FOLDER_TYPE_DRAFTS]       = 'folder_drafts.gif';
FolderImages[FOLDER_TYPE_TRASH]        = 'folder_trash.gif';
FolderImages[FOLDER_TYPE_DEFAULT_SYNC] = 'folder_sync.gif';
FolderImages[FOLDER_TYPE_INBOX_SYNC]   = 'folder_inbox_sync.gif';
FolderImages[FOLDER_TYPE_SENT_SYNC]    = 'folder_send_sync.gif';
FolderImages[FOLDER_TYPE_DRAFTS_SYNC]  = 'folder_drafts_sync.gif';
FolderImages[FOLDER_TYPE_TRASH_SYNC]   = 'folder_trash_sync.gif';

//defines for mark
var MARK_AS_READ    = 0;
var MARK_AS_UNREAD  = 1;
var FLAG            = 2;
var UNFLAG          = 3;
var MARK_ALL_READ   = 4;
var MARK_ALL_UNREAD = 5;

//defines images and types for mark
var Mark = Array();
Mark[MARK_AS_READ]    = {Image: 'mark_as_read.gif',    LangField: 'MarkAsRead',    ImgClass: 'wm_menu_mark_read_img'};
Mark[MARK_AS_UNREAD]  = {Image: 'mark_as_unread.gif',  LangField: 'MarkAsUnread',  ImgClass: 'wm_menu_mark_unread_img'};
Mark[FLAG]            = {Image: 'flag.gif',            LangField: 'MarkFlag',      ImgClass: 'wm_menu_flag_img'};
Mark[UNFLAG]          = {Image: 'unflag.gif',          LangField: 'MarkUnflag',    ImgClass: 'wm_menu_unflag_img'};
Mark[MARK_ALL_READ]   = {Image: 'mark_all_read.gif',   LangField: 'MarkAllRead',   ImgClass: 'wm_menu_mark_all_read_img'};
Mark[MARK_ALL_UNREAD] = {Image: 'mark_all_unread.gif', LangField: 'MarkAllUnread', ImgClass: 'wm_menu_mark_all_unread_img'};

//defines for reply
var REPLY     = 0;
var REPLY_ALL = 1;
var FORWARD   = 2;

//defines images for reply
var Reply = Array();
Reply[REPLY]     = {Image: 'reply.gif',    LangField: 'Reply'};
Reply[REPLY_ALL] = {Image: 'replyall.gif', LangField: 'ReplyAll'};

//defines for delete
var DELETE   = 6;
var UNDELETE = 7;
var PURGE    = 8;

var Delete = Array();
Delete[DELETE]   = {Image: 'delete.gif', LangField: 'Delete'};
Delete[UNDELETE] = {Image: 'delete.gif', LangField: 'Undelete'};
Delete[PURGE]    = {Image: 'purge.gif',  LangField: 'PurgeDeleted'};

//other group operations
var MOVE_TO_FOLDER = 9;

var OperationTypes = Array();
OperationTypes[DELETE]          = 'delete';
OperationTypes[UNDELETE]        = 'undelete';
OperationTypes[PURGE]           = 'purge';
OperationTypes[MARK_AS_READ]    = 'mark_read';
OperationTypes[MARK_AS_UNREAD]  = 'mark_unread';
OperationTypes[FLAG]            = 'flag';
OperationTypes[UNFLAG]          = 'unflag';
OperationTypes[MARK_ALL_READ]   = 'mark_all_read';
OperationTypes[MARK_ALL_UNREAD] = 'mark_all_unread';
OperationTypes[MOVE_TO_FOLDER]  = 'move_to_folder';

var SORT_FIELD_NOTHING = -1;
var SORT_FIELD_DATE    = 0;
var SORT_FIELD_FROM    = 2;
var SORT_FIELD_TO      = 4;
var SORT_FIELD_SIZE    = 6;
var SORT_FIELD_SUBJECT = 8;
var SORT_FIELD_ATTACH  = 10;
var SORT_FIELD_FLAG    = 12;
var SORT_ORDER_DESC = 0;
var SORT_ORDER_ASC  = 1;

//defines for inbox headers
var IH_CHECK       = 0;
var IH_ATTACHMENTS = 1;
var IH_FLAGGED     = 2;
var IH_FROM        = 3;
var IH_TO          = 4;
var IH_DATE        = 5;
var IH_SIZE        = 6;
var IH_SUBJECT     = 7;
var IH_CC          = 8;
var IH_BCC         = 9;
var IH_REPLY_TO    = 10;

/*
sortIconPlace values:
	0 - left of content
	1 - instead of content
	2 - right of content
align values: 'left', 'center', 'right'
*/
var InboxHeaders = Array();
InboxHeaders[IH_CHECK] =
{
	DisplayField: 'Check',
	LangField: '',
	Picture: '',
	SortField: SORT_FIELD_NOTHING,
	SortIconPlace: 1,
	Align: 'center', 
	Width: 24,
	MinWidth: 24,
	IsResize: false
};
InboxHeaders[IH_ATTACHMENTS] =
{
	DisplayField: 'HasAttachments',
	LangField: '',
	Picture: 'menu/attachment.gif',
	SortField: SORT_FIELD_ATTACH,
	SortIconPlace: 1,
	Align: 'center', 
	Width: 20,
	MinWidth: 20,
	IsResize: false
};
InboxHeaders[IH_FLAGGED] =
{
	DisplayField: 'Flagged',
	LangField: '',
	Picture: 'menu/flag.gif',
	SortField: SORT_FIELD_FLAG,
	SortIconPlace: 1,
	Align: 'center', 
	Width: 20,
	MinWidth: 20,
	IsResize: false
};
InboxHeaders[IH_FROM] =
{
	DisplayField: 'FromAddr',
	LangField: 'From',
	Picture: '',
	SortField: SORT_FIELD_FROM,
	SortIconPlace: 2,
	Align: 'left', 
	Width: 150,
	MinWidth: 100,
	IsResize: true
};
InboxHeaders[IH_TO] =
{
	DisplayField: 'ToAddr',
	LangField: 'To',
	Picture: '',
	SortField: SORT_FIELD_TO,
	SortIconPlace: 2,
	Align: 'left', 
	Width: 150,
	MinWidth: 100,
	IsResize: true
};
InboxHeaders[IH_DATE] =
{
	DisplayField: 'Date',
	LangField: 'Date',
	Picture: '',
	SortField: SORT_FIELD_DATE,
	SortIconPlace: 2,
	Align: 'center', 
	Width: 140,
	MinWidth: 100,
	IsResize: true
};
InboxHeaders[IH_SIZE] =
{
	DisplayField: 'Size',
	LangField: 'Size',
	Picture: '',
	SortField: SORT_FIELD_SIZE,
	SortIconPlace: 2,
	Align: 'center', 
	Width: 50,
	MinWidth: 40,
	IsResize: true
};
InboxHeaders[IH_SUBJECT] =
{
	DisplayField: 'Subject',
	LangField: 'Subject',
	Picture: '',
	SortField: SORT_FIELD_SUBJECT,
	SortIconPlace: 2,
	Align: 'left', 
	Width: 150,
	MinWidth: 100,
	IsResize: true
};
InboxHeaders[IH_CC] =
{
	DisplayField: 'CCAddr',
	LangField: 'CC',
	Picture: '',
	SortField: SORT_FIELD_NOTHING,
	SortIconPlace: 2,
	Align: 'center', 
	Width: 100,
	MinWidth: 100,
	IsResize: false
};
InboxHeaders[IH_BCC] =
{
	DisplayField: 'BCCAddr',
	LangField: 'BCC',
	Picture: '',
	SortField: SORT_FIELD_NOTHING,
	SortIconPlace: 2,
	Align: 'center', 
	Width: 100,
	MinWidth: 100,
	IsResize: false
};
InboxHeaders[IH_REPLY_TO] =
{
	DisplayField: 'ReplyToAddr',
	LangField: 'ReplyTo',
	Picture: '',
	SortField: SORT_FIELD_NOTHING,
	SortIconPlace: 2,
	Align: 'center', 
	Width: 100,
	MinWidth: 100,
	IsResize: false
};

//defines for parts of message type
var PART_MESSAGE_HEADERS               = 0;
var PART_MESSAGE_HTML                  = 1;
var PART_MESSAGE_MODIFIED_PLAIN_TEXT   = 2;
var PART_MESSAGE_REPLY_HTML            = 3;
var PART_MESSAGE_REPLY_PLAIN           = 4;
var PART_MESSAGE_FORWARD_HTML          = 5;
var PART_MESSAGE_FORWARD_PLAIN         = 6;
var PART_MESSAGE_FULL_HEADERS          = 7;
var PART_MESSAGE_ATTACHMENTS           = 8;
var PART_MESSAGE_UNMODIFIED_PLAIN_TEXT = 9;
 
// defines for toolbar items
var TOOLBAR_BACK_TO_LIST    = 0;
var TOOLBAR_NEW_MESSAGE     = 1;
var TOOLBAR_CHECK_MAIL      = 2;
var TOOLBAR_RELOAD_FOLDERS  = 3;
var TOOLBAR_DELETE          = 4;
var TOOLBAR_EMPTY_TRASH     = 5;
var TOOLBAR_FORWARD         = 6;
var TOOLBAR_NEW_CONTACT     = 7;
var TOOLBAR_NEW_GROUP       = 8;
var TOOLBAR_IMPORT_CONTACTS = 9;
var TOOLBAR_SEND_MESSAGE    = 10;
var TOOLBAR_SAVE_MESSAGE    = 11;
var TOOLBAR_PRINT_MESSAGE   = 12;
var TOOLBAR_PREV_MESSAGE    = 13;
var TOOLBAR_NEXT_MESSAGE    = 14;

var REDRAW_NOTHING = 0;
var REDRAW_FOLDER  = 1;
var REDRAW_HEADER  = 2;
var REDRAW_PAGE    = 3;

var COOKIE_STORAGE_DAYS  = 20;
var FOLDERS_TREES_INDENT = 8;
var AUTOSELECT_CHARSET = -1;

var POP3_PROTOCOL  = 0;
var IMAP4_PROTOCOL = 1;
var POP3_PORT  = 110;
var IMAP4_PORT = 143;
var SMTP_PORT  = 25;

var VIEW_MODE_WITH_PANE     = 1;
var VIEW_MODE_SHOW_PICTURES = 2;

var $Title = Array();
$Title[SCREEN_LOGIN]              = Lang.TitleLogin;
$Title[SCREEN_MESSAGES_LIST_VIEW] = Lang.TitleMessagesListView;
$Title[SCREEN_MESSAGES_LIST]      = Lang.TitleMessagesList;
$Title[SCREEN_VIEW_MESSAGE]       = Lang.TitleViewMessage;
$Title[SCREEN_NEW_MESSAGE]        = Lang.TitleNewMessage;
$Title[SCREEN_USER_SETTINGS]      = Lang.TitleSettings;
$Title[SCREEN_CONTACTS]           = Lang.TitleContacts;
$Title[SCREEN_CALENDAR]           = Lang.Calendar;
Lang.Title = $Title;

var $Mark = Array();
$Mark[MARK_AS_READ]    = Lang.MarkAsRead;
$Mark[MARK_AS_UNREAD]  = Lang.MarkAsUnread;
$Mark[FLAG]            = Lang.MarkFlag;
$Mark[UNFLAG]          = Lang.MarkUnflag;
$Mark[MARK_ALL_READ]   = Lang.MarkAllRead;
$Mark[MARK_ALL_UNREAD] = Lang.MarkAllUnread;

var $Reply = Array();
$Reply[REPLY]     = Lang.Reply;
$Reply[REPLY_ALL] = Lang.ReplyAll;

var $Delete = Array();
$Delete[DELETE]   = Lang.Delete;
$Delete[UNDELETE] = Lang.Undelete;
$Delete[PURGE]    = Lang.PurgeDeleted;

var $SyncTypes = Array();
$SyncTypes[SYNC_TYPE_NO]          = Lang.SyncTypeNo;
$SyncTypes[SYNC_TYPE_NEW_HEADERS] = Lang.SyncTypeNewHeaders;
$SyncTypes[SYNC_TYPE_ALL_HEADERS] = Lang.SyncTypeAllHeaders;
$SyncTypes[SYNC_TYPE_NEW_MSGS]    = Lang.SyncTypeNewMessages;
$SyncTypes[SYNC_TYPE_ALL_MSGS]    = Lang.SyncTypeAllMessages;
$SyncTypes[SYNC_TYPE_DIRECT_MODE] = Lang.SyncTypeDirectMode;
Lang.SyncTypes = $SyncTypes;

var $Pop3InboxSyncTypes = Array();
$Pop3InboxSyncTypes[SYNC_TYPE_NEW_HEADERS] = Lang.Pop3SyncTypeEntireHeaders;
$Pop3InboxSyncTypes[SYNC_TYPE_NEW_MSGS]    = Lang.Pop3SyncTypeEntireMessages;
$Pop3InboxSyncTypes[SYNC_TYPE_DIRECT_MODE] = Lang.Pop3SyncTypeDirectMode;
Lang.Pop3InboxSyncTypes = $Pop3InboxSyncTypes;

//defines for contacts headers
var CH_CHECK = 20;
var CH_GROUP = 21;
var CH_NAME  = 22;
var CH_EMAIL = 23;

var SORT_FIELD_GROUP    = 0;
var SORT_FIELD_NAME     = 1;
var SORT_FIELD_EMAIL    = 2;
var SORT_FIELD_USE_FREQ = 3;

var ContactsHeaders = Array();
ContactsHeaders[CH_CHECK] =
{
	DisplayField: 'Check',
	LangField: '',
	Picture: '',
	SortField: SORT_FIELD_NOTHING,
	SortIconPlace: 1,
	Align: 'center', 
	Width: 24,
	MinWidth: 24,
	IsResize: false
};
ContactsHeaders[CH_GROUP] =
{
	DisplayField: 'IsGroup',
	LangField: '',
	Picture: 'contacts/group.gif',
	SortField: SORT_FIELD_GROUP,
	SortIconPlace: 1,
	Align: 'center', 
	Width: 25,
	MinWidth: 25,
	IsResize: false
};
ContactsHeaders[CH_NAME] =
{
	DisplayField: 'Name',
	LangField: 'Name',
	Picture: '',
	SortField: SORT_FIELD_NAME,
	SortIconPlace: 2,
	Align: 'left', 
	Width: 150,
	MinWidth: 100,
	IsResize: true
};
ContactsHeaders[CH_EMAIL] =
{
	DisplayField: 'Email',
	LangField: 'Email',
	Picture: '',
	SortField: SORT_FIELD_EMAIL,
	SortIconPlace: 2,
	Align: 'left', 
	Width: 150,
	MinWidth: 100,
	IsResize: true
};