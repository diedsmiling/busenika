<?php
	define('PROC_ERROR_ACCT_CREATE', 'Il y a eu une erreur à la création du compte');
	define('PROC_WRONG_ACCT_PWD', 'Mauvais mot de passe');
	define('PROC_CANT_LOG_NONDEF', 'Impossible de se connecter avec un autre compte que celui par défaut.');
	define('PROC_CANT_INS_NEW_FILTER', 'Impossible d\'ajouter un filtre');
	define('PROC_FOLDER_EXIST', 'Le dossier existe déjà');
	define('PROC_CANT_CREATE_FLD', 'Impossible de créer le dossier');
	define('PROC_CANT_INS_NEW_GROUP', 'Impossible d\'insérer de nouveaux groupes');
	define('PROC_CANT_INS_NEW_CONT', 'Impossible d\'ajouter un nouveau contact');
	define('PROC_CANT_INS_NEW_CONTS', 'Impossible d\'ajouter un(des) nouveau(x) contact(s)');
	define('PROC_CANT_ADD_NEW_CONT_TO_GRP', 'Impossible d\'ajouter un(des) nouveau(x) contact(s) au groupe');
	define('PROC_ERROR_ACCT_UPDATE', 'Il y a eu une erreur à la mise à jour du compte');
	define('PROC_CANT_UPDATE_CONT_SETTINGS', 'Impossible de mettre à jour les paramètres');
	define('PROC_CANT_GET_SETTINGS', 'Impossible d\'obtenir les paramètres');
	define('PROC_CANT_UPDATE_ACCT', 'Impossible de mettre à jour le compte');
	define('PROC_ERROR_DEL_FLD', 'Il y a eu une erreur lors de la suppression du dossier');
	define('PROC_CANT_UPDATE_CONT', 'Impossible de mettre à jour le contact');
	define('PROC_CANT_GET_FLDS', 'Impossible d\'obtenir l\'arborescence des dossiers');
	define('PROC_CANT_GET_MSG_LIST', 'Impossible d\'obtenir la liste des dossiers');
	define('PROC_MSG_HAS_DELETED', 'The message a été supprimé du serveur de mail');
	define('PROC_CANT_LOAD_CONT_SETTINGS', 'Impossible de charger les paramètres du contact');
	define('PROC_CANT_LOAD_SIGNATURE', 'Impossible de charger la signature du compte');
	define('PROC_CANT_GET_CONT_FROM_DB', 'Impossible de charger le contact depuis la Base de Données');
	define('PROC_CANT_GET_CONTS_FROM_DB', 'Impossible de charger le(s) contact(s) depuis la Base de Données');
	define('PROC_CANT_DEL_ACCT_BY_ID', 'Impossible d\'effacer le compte par son numéro');
	define('PROC_CANT_DEL_FILTER_BY_ID', 'Impossible d\'effacer le filtre par son numéro');
	define('PROC_CANT_DEL_CONT_GROUPS', 'Impossible d\'effacer le(s) contact(s) et/ou le(s) group(s)');
	define('PROC_WRONG_ACCT_ACCESS', 'Une tentative non autorisée d\'accès à un autre compte  utilisateur a été détecté');
	define('PROC_SESSION_ERROR', 'La précédente session a été terminée ŕ cause d\'un délai dépassé.');

	define('MailBoxIsFull', 'La Boite mail est pleine');
	define('WebMailException', 'Une exception WEBMAIL est survenue');
	define('InvalidUid', 'Invalid Message UID');
	define('CantCreateContactGroup', 'Impossible de créer le groupe de contacts');
	define('CantCreateUser', 'Impossible de créer l\'utilisateur');
	define('CantCreateAccount', 'Impossible de créer le compte');
	define('SessionIsEmpty', 'La session est vide');
	define('FileIsTooBig', 'Le fichier est trop gros');

	define('PROC_CANT_MARK_ALL_MSG_READ', 'Impossible de marquer les messages comme lus');
	define('PROC_CANT_MARK_ALL_MSG_UNREAD','Impossible de marquer les messages comme non-lus');
	define('PROC_CANT_PURGE_MSGS', 'Impossible de nettoyer les message(s)');
	define('PROC_CANT_DEL_MSGS', 'Impossible d\'effacer message(s)');
	define('PROC_CANT_UNDEL_MSGS', 'Impossible de reprendre le(s) message(s) effacé(s)');
	define('PROC_CANT_MARK_MSGS_READ', 'Impossible de marquer les messages comme lus');
	define('PROC_CANT_MARK_MSGS_UNREAD','Impossible de marquer les messages comme non-lus');
	define('PROC_CANT_SET_MSG_FLAGS', 'Impossible d\'obtenir le statut des messages');
	define('PROC_CANT_REMOVE_MSG_FLAGS', 'Impossible de supprimer le statut du ou des message(s)');
	define('PROC_CANT_CHANGE_MSG_FLD', 'Impossible de changer le dossier du ou des message(s)');
	define('PROC_CANT_SEND_MSG', 'Impossible d\'envoyer le message.');
	define('PROC_CANT_SAVE_MSG', 'Impossible de sauvegarder le message.');
	define('PROC_CANT_GET_ACCT_LIST', 'Impossible d\'obtenir la liste des comptes');
	define('PROC_CANT_GET_FILTER_LIST', 'Impossible d\'obtenir la liste des filtres');

	define('PROC_CANT_LEAVE_BLANK', 'Vous ne pouvez pas laisser le champ * vide');
	
	define('PROC_CANT_UPD_FLD', 'Impossible de mettre ŕ jour le dossier');
	define('PROC_CANT_UPD_FILTER', 'Impossible de mettre ŕ jour le filtre');

	define('ACCT_CANT_ADD_DEF_ACCT', 'Impossible de rajouter ce compte car il est déjŕ utilize comme compte par défaut par un autre utilisateur.');
	define('ACCT_CANT_UPD_TO_DEF_ACCT', 'Ce statut de ce compte ne peut pas ętre changé comme compte par défaut.');
	define('ACCT_CANT_CREATE_IMAP_ACCT', 'Impossible de créer ce nouveau compte (IMAP4 erreur de connexion)');
	define('ACCT_CANT_DEL_LAST_DEF_ACCT', 'Impossible d\'effacer le dernier compte par défaut ');

	define('LANG_LoginInfo', 'Information de l\'identifiant');
	define('LANG_Email', 'Email');
	define('LANG_Login', 'Identifiant');
	define('LANG_Password', 'Mot de passe');
	define('LANG_IncServer', 'Mail entrant');
	define('LANG_PopProtocol', 'POP3');
	define('LANG_ImapProtocol', 'IMAP4');
	define('LANG_IncPort', 'Port');
	define('LANG_OutServer', 'SMTP Server');
	define('LANG_OutPort', 'Port');
	define('LANG_UseSmtpAuth', 'Utiliser l\'authentification SMTP');
	define('LANG_SignMe', 'Connectez moi automatiquement');
	define('LANG_Enter', 'Entrer');

	define('JS_LANG_TitleLogin', 'Identifiant');
	define('JS_LANG_TitleMessagesListView', 'Liste des Messages');
	define('JS_LANG_TitleMessagesList', 'Liste des Messages');
	define('JS_LANG_TitleViewMessage', 'Voir un Message');
	define('JS_LANG_TitleNewMessage', 'Nouveau Message');
	define('JS_LANG_TitleSettings', 'Paramètres');
	define('JS_LANG_TitleContacts', 'Contacts');

	define('JS_LANG_StandardLogin', 'Identification&nbsp;Standard');
	define('JS_LANG_AdvancedLogin', 'Identification&nbsp;avancée');

	define('JS_LANG_InfoWebMailLoading', 'Veuillez patienter pendant le chargement de WEBMAIL &hellip;');
	define('JS_LANG_Loading', 'Chargement &hellip;');
	define('JS_LANG_InfoMessagesLoad', 'Veuillez patienter pendant que WEBMAIL récupère la liste des messages');
	define('JS_LANG_InfoEmptyFolder', 'Le dossier est vide.');
	define('JS_LANG_InfoPageLoading', 'La page est toujours en train de charger ...');
	define('JS_LANG_InfoSendMessage', 'Le message a été envoyé');
	define('JS_LANG_InfoSaveMessage', 'Le message a été enregistré');
	define('JS_LANG_InfoHaveImported', 'Vous avez importés');
	define('JS_LANG_InfoNewContacts', 'nouveau(x) contact(s) dans votre liste de contacts.');
	define('JS_LANG_InfoToDelete', 'à effacer');
	define('JS_LANG_InfoDeleteContent', 'dossier, vous devriez effacer son contenu d\'abord.');
	define('JS_LANG_InfoDeleteNotEmptyFolders', 'Effacer des dossiers non vides est impossible. Merci de vider d\'abord le contenu des dossiers cochés avant de les supprimer.');
	define('JS_LANG_InfoRequiredFields', '* champs requis');

	define('JS_LANG_ConfirmAreYouSure', 'Etes vous sûr ?');
	define('JS_LANG_ConfirmDirectModeAreYouSure', 'Le(s) message(s) sélectionnés vont être définitivement effacés ! Etes vous sûr ?');
	define('JS_LANG_ConfirmSaveSettings', 'Les paramètres n\'ont pas été enregistrés. Appuyez sur OK pour les sauvegarder.');
	define('JS_LANG_ConfirmSaveContactsSettings', 'Les paramètres du contact n\'ont pas été enregistrés. Appuyez sur OK pour les sauvegarder.');
	define('JS_LANG_ConfirmSaveAcctProp', 'Les propriétés du compte n\'ont pas été enregistrées. Appuyez sur OK pour les sauvegarder.');
	define('JS_LANG_ConfirmSaveFilter', 'Les propriétés des filtres n\'ont pas été enregistrés. Appuyez sur OK pour les sauvegarder.');
	define('JS_LANG_ConfirmSaveSignature', 'La signature n\'a pas été enregistré. Appuyez sur OK pour la sauvegarder.');
	define('JS_LANG_ConfirmSavefolders', 'Le dossier n\'a pas été enregistré. Appuyez sur OK pour le sauvegarder.');
	define('JS_LANG_ConfirmHtmlToPlain', 'Attention : Le fait de changer le formatage de ce message d\'HTML en texte simple, vous perdrez toute mise en page. Appuyez sur OK pour continuer.');
	define('JS_LANG_ConfirmAddFolder', 'Avant d\'ajouter ce dossier, il est nécessaire de valider les changements. Appuyez sur OK pour sauvegarder.');
	define('JS_LANG_ConfirmEmptySubject', 'Le sujet est vide. Voulez-vous continuer ?');

	define('JS_LANG_WarningEmailBlank', 'Vous ne pouvez pas laisser le champ<br />Email: champ vide');
	define('JS_LANG_WarningLoginBlank', 'Vous ne pouvez pas laisser le champ<br />Identifiant: champ vide');
	define('JS_LANG_WarningToBlank', 'Vous ne pouvez pas laisser le champ: zone vide');
	define('JS_LANG_WarningServerPortBlank', 'Vous ne pouvez pas laisser le champ POP3 et<br />SMTP server/port champ vides.');
	define('JS_LANG_WarningEmptySearchLine', 'Ligne de recherche vide. Veuillez taper la partie de texte que vous souhaitez rechercher.');
	define('JS_LANG_WarningMarkListItem', 'Merci de choisir au moins un élément dans la liste.');
	define('JS_LANG_WarningFolderMove', 'Le dossier ne peut être déplacé car ici c\'est un autre niveau');
	define('JS_LANG_WarningContactNotComplete', 'Merci de saisir votre email ou votre nom');
	define('JS_LANG_WarningGroupNotComplete', 'Merci de saisir le nom du group');

	define('JS_LANG_WarningEmailFieldBlank', 'Vous ne pouvez pas laisser le champ Email vide');
	define('JS_LANG_WarningIncServerBlank', 'Vous ne pouvez pas laisser le champ Serveur POP3(IMAP4) vide');
	define('JS_LANG_WarningIncPortBlank', 'Vous ne pouvez pas laisser le champ Serveur POP3(IMAP4) vide');
	define('JS_LANG_WarningIncLoginBlank', 'Vous ne pouvez pas laisser le champ Identifiant POP3(IMAP4) vide');
	define('JS_LANG_WarningIncPortNumber', 'Vous devez spécifier une valeur positive pour le port POP3(IMAP4)');
	define('JS_LANG_DefaultIncPortNumber', 'Le numéro de port par défaut pour POP3(IMAP4) est 110(143).');
	define('JS_LANG_WarningIncPassBlank', 'Vous ne pouvez pas laisser le champ mot de passe POP3(IMAP4) vide');
	define('JS_LANG_WarningOutPortBlank', 'Vous ne pouvez pas laisser le champ port du serveur SMTP vide');
	define('JS_LANG_WarningOutPortNumber', 'Vous devez spécifier une valeur positive pour le port SMTP.');
	define('JS_LANG_WarningCorrectEmail', 'Vous devez spécifier l\'adresse email correctement.');
	define('JS_LANG_DefaultOutPortNumber', 'Le numéro du port SMTP par défaut est 25.');

	define('JS_LANG_WarningCsvExtention', 'L\'extension doit être de la forme .csv');
	define('JS_LANG_WarningImportFileType', 'Veuillez choisir le programme depuis lequel vous souhaitez copier vos contacts');
	define('JS_LANG_WarningEmptyImportFile', 'Merci de sélection un fichier en appuyant sur le bouton parcourir');
	
	define('JS_LANG_WarningContactsPerPage', 'Le nombre de contacts par page doit être un nombre positif');
	define('JS_LANG_WarningMessagesPerPage', 'Le nombre de messages par page doit être un nombre positif');
	define('JS_LANG_WarningMailsOnServerDays', 'Vous devez spécifier un nombre positif pour le champ du nombre de jours sur le serveur');
	define('JS_LANG_WarningEmptyFilter', 'Veuillez entrer une sous-chaîne ');
	define('JS_LANG_WarningEmptyFolderName', 'Veuillez saisir le nom du dossier');

	define('JS_LANG_ErrorConnectionFailed', 'Impossible de se connecter');
	define('JS_LANG_ErrorRequestFailed', 'Le transfert des données ne s\'est pas terminé');
	define('JS_LANG_ErrorAbsentXMLHttpRequest', 'L\'objet XMLHttpRequest n\'est pas présent.');
	define('JS_LANG_ErrorWithoutDesc', 'Une erreur inconnue et sans description est survenue');
	define('JS_LANG_ErrorParsing', 'Une erreur, lors de l\'analyse du fichier XML est survenue.');
	define('JS_LANG_ResponseText', 'Texte de réponse :');
	define('JS_LANG_ErrorEmptyXmlPacket', 'Les paquets de données XML sont vides');
	define('JS_LANG_ErrorImportContacts', 'Erreur pendant l\'import des contacts');
	define('JS_LANG_ErrorNoContacts', 'Aucun contact à importer');
	define('JS_LANG_ErrorCheckMail', 'La réception des messages s\'est achevée avec une erreur. Apparemment, tous les messages n\'ont pas été reçus.');

	define('JS_LANG_LoggingToServer', 'Connexion au serveur &hellip;');
	define('JS_LANG_GettingMsgsNum', 'Réception du nombre de messages');
	define('JS_LANG_RetrievingMessage', 'Réception du message');
	define('JS_LANG_DeletingMessage', 'Effacement du message');
	define('JS_LANG_DeletingMessages', 'Effacement du (des) message(s)');
	define('JS_LANG_Of', 'de');
	define('JS_LANG_Connection', 'Connexion');
	define('JS_LANG_Charset', 'Caractère');
	define('JS_LANG_AutoSelect', 'sélection automatique');

	define('JS_LANG_Contacts', 'Contacts');
	define('JS_LANG_ClassicVersion', 'Version Classique');
	define('JS_LANG_Logout', 'Déconnexion');
	define('JS_LANG_Settings', 'Paramètres');

	define('JS_LANG_LookFor', 'Chercher : ');
	define('JS_LANG_SearchIn', 'Chercher dans : ');
	define('JS_LANG_QuickSearch', 'Chercher dans De , A et dans le sujet du email seulement (plus rapide).');
	define('JS_LANG_SlowSearch', 'Chercher dans tout le message');
	define('JS_LANG_AllMailFolders', 'Tous les dossiers');
	define('JS_LANG_AllGroups', 'Tous les groupes');

	define('JS_LANG_NewMessage', 'Nouveau Message');
	define('JS_LANG_CheckMail', 'Vérifier les emails');
	define('JS_LANG_ReloadFolders', 'Recharger la liste des dossiers');
	define('JS_LANG_EmptyTrash', 'Vider la poubelle');
	define('JS_LANG_MarkAsRead', 'Marquer comme lu');
	define('JS_LANG_MarkAsUnread', 'Marquer comme non lu');
	define('JS_LANG_MarkFlag', 'Marquer avec drapeau');
	define('JS_LANG_MarkUnflag', 'Ne pas marquer avec un drapeau');
	define('JS_LANG_MarkAllRead', 'Marquer comme lu');
	define('JS_LANG_MarkAllUnread', 'Marquer comme non lu');
	define('JS_LANG_Reply', 'Répondre');
	define('JS_LANG_ReplyAll', 'Répondre à tous');
	define('JS_LANG_Delete', 'Effacer');
	define('JS_LANG_Undelete', 'Reprendre');
	define('JS_LANG_PurgeDeleted', 'Purger les fichiers effacés');
	define('JS_LANG_MoveToFolder', 'Déplacer dans le dossier');
	define('JS_LANG_Forward', 'Transférer');

	define('JS_LANG_HideFolders', 'Cacher les dossiers');
	define('JS_LANG_ShowFolders', 'Montrer les dossiers');
	define('JS_LANG_ManageFolders', 'Gérer les dossiers');
	define('JS_LANG_SyncFolder', 'Synchroniser les dossiers');
	define('JS_LANG_NewMessages', 'Nouveaux Messages');
	define('JS_LANG_Messages', 'Message(s)');

	define('JS_LANG_From', 'De');
	define('JS_LANG_To', 'A');
	define('JS_LANG_Date', 'Date');
	define('JS_LANG_Size', 'Taille');
	define('JS_LANG_Subject', 'Sujet');

	define('JS_LANG_FirstPage', 'Première Page');
	define('JS_LANG_PreviousPage', 'Page Précédente');
	define('JS_LANG_NextPage', 'Page Suivante');
	define('JS_LANG_LastPage', 'Dernière Page');

	define('JS_LANG_SwitchToPlain', 'Basculer vers du texte simple ');
	define('JS_LANG_SwitchToHTML', 'Basculer vers du texte HTML');
	define('JS_LANG_AddToAddressBokk', 'Rajouter à l\'annuaire');
	define('JS_LANG_ClickToDownload', 'Cliquer pour télécharger ');
	define('JS_LANG_View', 'Voir');
	define('JS_LANG_ShowFullHeaders', 'Montrer l\'intégralité des entêtes du Email');
	define('JS_LANG_HideFullHeaders', 'Masquer les entêtes du Email');

	define('JS_LANG_MessagesInFolder', 'Messages dans le dossier');
	define('JS_LANG_YouUsing', 'Vous utilisez');
	define('JS_LANG_OfYour', 'de votre');
	define('JS_LANG_Mb', 'MB');
	define('JS_LANG_Kb', 'KB');
	define('JS_LANG_B', 'B');

	define('JS_LANG_SendMessage', 'Envoyer');
	define('JS_LANG_SaveMessage', 'Enregistrer');
	define('JS_LANG_Print', 'Imprimer');
	define('JS_LANG_PreviousMsg', 'Message Précédent');
	define('JS_LANG_NextMsg', 'Message Suivant');
	define('JS_LANG_AddressBook', 'Annuaire');
	define('JS_LANG_ShowBCC', 'Montrer BCC');
	define('JS_LANG_HideBCC', 'Cacher BCC');
	define('JS_LANG_CC', 'CC');
	define('JS_LANG_BCC', 'BCC');
	define('JS_LANG_ReplyTo', 'Répondre&nbsp;à');
	define('JS_LANG_AttachFile', 'Attacher une pièce jointe');
	define('JS_LANG_Attach', 'Attacher');
	define('JS_LANG_Re', 'Re');
	define('JS_LANG_OriginalMessage', 'Message Original');
	define('JS_LANG_Sent', 'Envoyer');
	define('JS_LANG_Fwd', 'Transférer');
	define('JS_LANG_Low', 'Basse');
	define('JS_LANG_Normal', 'Normal');
	define('JS_LANG_High', 'Haute');
	define('JS_LANG_Importance', 'Importance');
	define('JS_LANG_Close', 'Fermer');
	
	define('JS_LANG_Common', 'Commun');
	define('JS_LANG_EmailAccounts', 'Comptes mails');
	
	define('JS_LANG_MsgsPerPage', 'Messages par page');
	define('JS_LANG_DisableRTE', 'Désactiver l\'édition de texte avancée (Rich-text)');
	define('JS_LANG_Skin', 'Thème');
	define('JS_LANG_DefCharset', 'Caractère par défaut');
	define('JS_LANG_DefCharsetInc', 'Réception de caractères par défaut ');
	define('JS_LANG_DefCharsetOut', 'Envoie de caractères par défaut');
	define('JS_LANG_DefTimeOffset', 'Temps par défaut');
	define('JS_LANG_DefLanguage', 'Langue par défaut');
	define('JS_LANG_DefDateFormat', 'Format de la date par défaut');
	define('JS_LANG_ShowViewPane', 'Liste des messages dans la fenêtre de prévisualisation');
	define('JS_LANG_Save', 'Enregistrer');
	define('JS_LANG_Cancel', 'Annuler');
	define('JS_LANG_OK', 'OK');
	
	define('JS_LANG_Remove', 'Supprimer');
	define('JS_LANG_AddNewAccount', 'Rajouter un compte');
	define('JS_LANG_Signature', 'Signature');
	define('JS_LANG_Filters', 'Filtres');
	define('JS_LANG_Properties', 'Paramètres');
	define('JS_LANG_UseForLogin', 'Utilisez les Paramètres de ce compte (identifiant et mot de passe) pour vous connecter');
	define('JS_LANG_MailFriendlyName', 'Votre nom');
	define('JS_LANG_MailEmail', 'Email');
	define('JS_LANG_MailIncHost', 'Serveur de Mail entrant');
	define('JS_LANG_Imap4', 'Imap4');
	define('JS_LANG_Pop3', 'Pop3');
	define('JS_LANG_MailIncPort', 'Port');
	define('JS_LANG_MailIncLogin', 'Identifiant');
	define('JS_LANG_MailIncPass', 'Mot de passe');
	define('JS_LANG_MailOutHost', 'Serveur SMTP ');
	define('JS_LANG_MailOutPort', 'Port');
	define('JS_LANG_MailOutLogin', 'Identifiant SMTP');
	define('JS_LANG_MailOutPass', 'Mot de passe SMTP');
	define('JS_LANG_MailOutAuth1', 'Utiliser l\'authentification SMTP');
	define('JS_LANG_MailOutAuth2', '(Vous pouvez laisser à vide les identifiants et mots de passe si ce sont les mêmes que les identifiants / Mots de passe POP3/IMAP4)');
	define('JS_LANG_UseFriendlyNm1', 'Utiliser un nom étendu à la place du email dans le champ "De:"');
	define('JS_LANG_UseFriendlyNm2', '(Votre nom &lt;sender@mail.com&gt;)');
	define('JS_LANG_GetmailAtLogin', 'Récupérer / Synchroniser les emails à la connexion');
	define('JS_LANG_MailMode0', 'Effacer les messages reçus du serveur');
	define('JS_LANG_MailMode1', 'Laisser les messages sur le serveur');
	define('JS_LANG_MailMode2', 'Laisser les messages sur le serveur ');
	define('JS_LANG_MailsOnServerDays', 'jour(s)');
	define('JS_LANG_MailMode3', 'Effacer les messages du serveur quand vous videz la poubelle');
	define('JS_LANG_InboxSyncType', 'Type de Synchronisation');
	
	define('JS_LANG_SyncTypeNo', 'ne pas synchroniser');
	define('JS_LANG_SyncTypeNewHeaders', 'Nouvel Entête');
	define('JS_LANG_SyncTypeAllHeaders', 'Tous les Entêtes');
	define('JS_LANG_SyncTypeNewMessages', 'Nouveaux Messages');
	define('JS_LANG_SyncTypeAllMessages', 'Tous les Messages');
	define('JS_LANG_SyncTypeDirectMode', 'Mode direct');
	
	define('JS_LANG_Pop3SyncTypeEntireHeaders', 'Tous les Entêtes');
	define('JS_LANG_Pop3SyncTypeEntireMessages', 'Tous les Messages');
	define('JS_LANG_Pop3SyncTypeDirectMode', 'Mode direct');
	
	define('JS_LANG_DeleteFromDb', 'Effacer les messages de la Base de Données lorsqu\'ils n\'existent plus sur le serveur de Mail');

	define('JS_LANG_EditFilter', 'Modifier un filtre');
	define('JS_LANG_NewFilter', 'Ajouter un filtre');
	define('JS_LANG_Field', 'zone');
	define('JS_LANG_Condition', 'Condition');
	define('JS_LANG_ContainSubstring', 'Contient une partie de la sous-chaine de caractères');
	define('JS_LANG_ContainExactPhrase', 'Contient exactement la sous-chaine de caractères');
	define('JS_LANG_NotContainSubstring', 'Ne contient pas de sous-chaine de caractères'); 	
	define('JS_LANG_FilterDesc_At', 'ŕ');
	define('JS_LANG_FilterDesc_Field', 'champ');
	define('JS_LANG_Action', 'Action');
	define('JS_LANG_DoNothing', 'Ne rien faire');
	define('JS_LANG_DeleteFromServer', 'Effacer immédiatement du Serveur');
	define('JS_LANG_MarkGrey', 'Marquer comme gris');
	define('JS_LANG_Add', 'Ajouter');
	define('JS_LANG_OtherFilterSettings', 'Autres paramètres du filtre');
	define('JS_LANG_ConsiderXSpam', 'Considérer comme un entête X-Spam');
	define('JS_LANG_Apply', 'Appliquer');

	define('JS_LANG_InsertLink', 'Insérer un lien');
	define('JS_LANG_RemoveLink', 'Supprimer un lien');
	define('JS_LANG_Numbering', 'Numérotation');
	define('JS_LANG_Bullets', 'Signets');
	define('JS_LANG_HorizontalLine', 'Ligne Horizontale');
	define('JS_LANG_Bold', 'Gras');
	define('JS_LANG_Italic', 'Italique');
	define('JS_LANG_Underline', 'Souligner');
	define('JS_LANG_AlignLeft', 'Aligner à gauche');
	define('JS_LANG_Center', 'Centrer');
	define('JS_LANG_AlignRight', 'Aligner à gauche');
	define('JS_LANG_Justify', 'Justifier');
	define('JS_LANG_FontColor', 'Couleur de la police de caractères');
	define('JS_LANG_Background', 'Fond');
	define('JS_LANG_SwitchToPlainMode', 'Basculer vers du texte brut');
	define('JS_LANG_SwitchToHTMLMode', 'Basculer vers du texte HTML');
	define('JS_LANG_AddSignatures', 'Rajouter une signature à tous les messages sortant');
	define('JS_LANG_DontAddToReplies', 'Ne pas ajouter de signature aux réponses et transferts');

	define('JS_LANG_Folder', 'Dossier');
	define('JS_LANG_Msgs', 'Mes\'s,');
	define('JS_LANG_Synchronize', 'Synchroniser');
	define('JS_LANG_ShowThisFolder', 'Montrer ce dossier');
	define('JS_LANG_Total', 'Total');
	define('JS_LANG_DeleteSelected', 'Effacer les sélectionnés');
	define('JS_LANG_AddNewFolder', 'Rajouter un dossier');
	define('JS_LANG_NewFolder', 'Nouveau dossier');
	define('JS_LANG_ParentFolder', 'Dossier parent');
	define('JS_LANG_NoParent', 'Pas de dossier Parent');
	define('JS_LANG_OnMailServer', 'Créer ce dossier dans Webmail ainsi que sur le serveur de Mail');
	define('JS_LANG_InWebMail', 'Créer ce dossier uniquement dans Webmail');
	define('JS_LANG_FolderName', 'Nom du dossier');

	define('JS_LANG_ContactsPerPage', 'Contacts par page');
	define('JS_LANG_WhiteList', 'Annuaire comme liste blanche');

	define('JS_LANG_CharsetDefault', 'défaut');
	define('JS_LANG_CharsetArabicAlphabetISO', 'Alphabet arabe(ISO)');
	define('JS_LANG_CharsetArabicAlphabet', 'Alphabet arabe (Windows)');
	define('JS_LANG_CharsetBalticAlphabetISO', 'Alphabet baltique (ISO)');
	define('JS_LANG_CharsetBalticAlphabet', 'Alphabet baltique (Windows)');
	define('JS_LANG_CharsetCentralEuropeanAlphabetISO', 'Alphabet de l\'Europe centrale (ISO)');
	define('JS_LANG_CharsetCentralEuropeanAlphabet', 'Alphabet de l\'Europe centrale (Windows)');
	define('JS_LANG_CharsetChineseSimplifiedEUC', 'Chinese Simplified (EUC)');
	define('JS_LANG_CharsetChineseSimplifiedGB', 'Chinese Simplified (GB2312)');
	define('JS_LANG_CharsetChineseTraditional', 'Chinois Traditionnel (Big5)');
	define('JS_LANG_CharsetCyrillicAlphabetISO', 'Alphabet cyrillique (ISO)');
	define('JS_LANG_CharsetCyrillicAlphabetKOI8R', 'Alphabet cyrillique (KOI8-R)');
	define('JS_LANG_CharsetCyrillicAlphabet', 'Alphabet cyrillique (Windows)');
	define('JS_LANG_CharsetGreekAlphabetISO', 'Alphabet Grec (ISO)');
	define('JS_LANG_CharsetGreekAlphabet', 'Alphabet Grec (Windows)');
	define('JS_LANG_CharsetHebrewAlphabetISO', 'Alphabet hébreu (ISO)');
	define('JS_LANG_CharsetHebrewAlphabet', 'Alphabet hébreu (Windows)');
	define('JS_LANG_CharsetJapanese', 'Japonais');
	define('JS_LANG_CharsetJapaneseShiftJIS', 'Japonais (Shift-JIS)');
	define('JS_LANG_CharsetKoreanEUC', 'Coréen  (EUC)');
	define('JS_LANG_CharsetKoreanISO', 'Coréen  (ISO)');
	define('JS_LANG_CharsetLatin3AlphabetISO', 'Alphabet Latin 3(ISO)');
	define('JS_LANG_CharsetTurkishAlphabet', 'Alphabet Turc');
	define('JS_LANG_CharsetUniversalAlphabetUTF7', 'Alphabet Universel (UTF-7)');
	define('JS_LANG_CharsetUniversalAlphabetUTF8', 'Alphabet Universel (UTF-8)');
	define('JS_LANG_CharsetVietnameseAlphabet',  'Alphabet vietnamien (Windows)');
	define('JS_LANG_CharsetWesternAlphabetISO', 'Alphabet Occidental (ISO)');
	define('JS_LANG_CharsetWesternAlphabet', 'Alphabet Occidental (Windows)');

	define('JS_LANG_TimeDefault', 'défaut');
	define('JS_LANG_TimeEniwetok', 'Eiwetok, Kwajalein, ligne de changement de date Temps');
	define('JS_LANG_TimeMidwayIsland', 'Midway Island, Samoa');
	define('JS_LANG_TimeHawaii', 'Hawaii');
	define('JS_LANG_TimeAlaska', 'Alaska');
	define('JS_LANG_TimePacific', 'Temps Pacifique (les USA et le Canada) ; Tijuana');
	define('JS_LANG_TimeArizona', 'Arizona');
	define('JS_LANG_TimeMountain', 'Mountain Time (US & Canada)');
	define('JS_LANG_TimeCentralAmerica', 'L\'Amérique Centrale');
	define('JS_LANG_TimeCentral', 'Temps central (les USA et le Canada)');
	define('JS_LANG_TimeMexicoCity', 'Mexico, Tegucigalpa');
	define('JS_LANG_TimeSaskatchewan', 'Saskatchewan');
	define('JS_LANG_TimeIndiana', 'L\'Indiana (Est)');
	define('JS_LANG_TimeEastern', 'Temps oriental (les USA et le Canada)');
	define('JS_LANG_TimeBogota', 'Bogota, Lima, Quito');
	define('JS_LANG_TimeSantiago', 'Santiago');
	define('JS_LANG_TimeCaracas', 'Caracas, La Paz');
	define('JS_LANG_TimeAtlanticCanada', 'Temps atlantique (Canada)');
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

	define('LanguageEnglish', 'Anglais');
	define('LanguageCatala', 'Catalan');
	define('LanguageNederlands', 'Hollandais');
	define('LanguageFrench', 'Français');
	define('LanguageGerman', 'Allemand');
	define('LanguageItaliano', 'Italien');
	define('LanguagePortuguese', 'Portuguais (BR)');
	define('LanguageEspanyol', 'Espaniol');
	define('LanguageSwedish', 'Suédois');
	define('LanguageTurkish', 'Turc');

	define('JS_LANG_DateDefault', 'défaut');
	define('JS_LANG_DateDDMMYY', 'JJ/MM/AA');
	define('JS_LANG_DateMMDDYY', 'MM/JJ/AA');
	define('JS_LANG_DateDDMonth', 'JJ Mois (01 Janv)');
	define('JS_LANG_DateAdvanced', 'Avancé');
	
	define('JS_LANG_NewContact', 'Nouveau Contact');
	define('JS_LANG_NewGroup', 'Nouveau Group');
	define('JS_LANG_AddContactsTo', 'Rajouter des contacts au');
	define('JS_LANG_ImportContacts', 'Importer des Contacts');
	
	define('JS_LANG_Name', 'Nom');
	define('JS_LANG_Email', 'Email');
	define('JS_LANG_DefaultEmail', 'Email par défaut');
	define('JS_LANG_NotSpecifiedYet', 'non spécifié pour le moment');
	define('JS_LANG_ContactName', 'Nom');
	define('JS_LANG_Birthday', 'Anniversaire');
	define('JS_LANG_Month', 'Mois');
	define('JS_LANG_January', 'Janvier');
	define('JS_LANG_February', 'Février');
	define('JS_LANG_March', 'Mars');
	define('JS_LANG_April', 'Avril');
	define('JS_LANG_May', 'Mai');
	define('JS_LANG_June', 'Juin');
	define('JS_LANG_July', 'Juillet');
	define('JS_LANG_August', 'Aout');
	define('JS_LANG_September', 'Septembre');
	define('JS_LANG_October', 'Octobre');
	define('JS_LANG_November', 'Novembre');
	define('JS_LANG_December', 'Decembre');
	define('JS_LANG_Day', 'Jour');
	define('JS_LANG_Year', 'Année');
	define('JS_LANG_UseFriendlyName1', 'Utiliser un nom étendu à la place du email dans le champ "De:"');
	define('JS_LANG_UseFriendlyName2', '(Votre nom &lt;sender@mail.com&gt;)');
	define('JS_LANG_Personal', 'Personnel');
	define('JS_LANG_PersonalEmail', 'E-mail Personnel');
	define('JS_LANG_StreetAddress', 'Addresse');
	define('JS_LANG_City', 'Ville');
	define('JS_LANG_Fax', 'Fax');
	define('JS_LANG_StateProvince', 'Etat');
	define('JS_LANG_Phone', 'Téléphone');
	define('JS_LANG_ZipCode', 'Code Postal');
	define('JS_LANG_Mobile', 'Portable');
	define('JS_LANG_CountryRegion', 'Pays/Région');
	define('JS_LANG_WebPage', 'Page Web');
	define('JS_LANG_Go', 'Go');
	define('JS_LANG_Home', 'Début');
	define('JS_LANG_Business', 'Travail');
	define('JS_LANG_BusinessEmail', 'E-mail du travail');
	define('JS_LANG_Company', 'Entreprise');
	define('JS_LANG_JobTitle', 'Poste');
	define('JS_LANG_Department', 'Départment');
	define('JS_LANG_Office', 'Bureau');
	define('JS_LANG_Pager', 'Pager');
	define('JS_LANG_Other', 'Autre');
	define('JS_LANG_OtherEmail', 'Autre E-mail');
	define('JS_LANG_Notes', 'Notes');
	define('JS_LANG_Groups', 'Groupes');
	define('JS_LANG_ShowAddFields', 'Montrer les champs supplémentaires');
	define('JS_LANG_HideAddFields', 'Masquer les champs supplémentaires');
	define('JS_LANG_EditContact', 'Modifier les informations du contact');
	define('JS_LANG_GroupName', 'Nom du groupe');
	define('JS_LANG_AddContacts', 'Rajouter des contacts');
	define('JS_LANG_CommentAddContacts', '(Si vous allez indiquer plus d\'une adresse, merci de les séparer par des virgules)');
	define('JS_LANG_CreateGroup', 'Créer un Groupe');
	define('JS_LANG_Rename', 'Renommer');
	define('JS_LANG_MailGroup', 'Groupe Mail');
	define('JS_LANG_RemoveFromGroup', 'Supprimer du Groupe');
	define('JS_LANG_UseImportTo', 'Utilisez l\'importation pour copier vos contacts de Microsoft Outlook, Microsoft Outlook Express vers vos contacts WebMail');
	define('JS_LANG_Outlook1', 'Microsoft Outlook 2000/XP/2003');
	define('JS_LANG_Outlook2', 'Microsoft Outlook Express 6');
	define('JS_LANG_SelectImportFile', 'Sélectionnez le fichier (au format .CSV) que vous souhaitez importer');
	define('JS_LANG_Import', 'Importer');
	define('JS_LANG_ContactsMessage', 'Ceci est la page de contacts !');
	define('JS_LANG_ContactsCount', 'contact(s)');
	define('JS_LANG_GroupsCount', 'groupe(s)');

// webmail 4.1 constants
	define('PicturesBlocked', 'Des images de ce message ont été bloquées pour votre sécurité.');
	define('ShowPictures', 'Afficher les images');
	define('ShowPicturesFromSender', 'Toujours afficher les images des messages de cet émetteur');
	define('AlwaysShowPictures', 'Toujours afficher les images contenues dans les messages');

	define('TreatAsOrganization', 'Traiter comme une organisation');

	define('WarningGroupAlreadyExist', 'Un group avec le même nom existe déjà. Veuillez choisir un autre nom.');
	define('WarningCorrectFolderName', 'Vous devriez spécifier un nom dossier correct.');
	define('WarningLoginFieldBlank', 'Vous ne pouvez pas laisser le champ Login (nom d\'utilisateur) vide.');
	define('WarningCorrectLogin', 'Vous devriez spécifier un nom d\'utilisateur (Login) correct.');
	define('WarningPassBlank', 'Vous ne pouvez pas laissez le champ Mot de Passe vide.');
	define('WarningCorrectIncServer', 'Vous devriez spécifier une adresse de serveur POP3(IMAP) correcte.');
	define('WarningCorrectSMTPServer', 'Vous devriez spécifier une adresse de serveur SMTP correcte.');
	define('WarningFromBlank', 'Vous ne pouvez pas laisser le champ DE vide.');
	define('WarningAdvancedDateFormat', 'Merci de spécifier un format de date.');

	define('AdvancedDateHelpTitle', 'Date avancée');
	define('AdvancedDateHelpIntro', 'Qaund le champ &quot;Avancé&quot; est selectionné, vous pouvez utiliser la boite de texte pour définir votre propre format de date, qui sera affiché dans MailBee WebMail Pro. Les options suivantes sont utilisées pour cela avec \':\' ou \'/\' comme caractère de délimitation:');
	define('AdvancedDateHelpConclusion', 'Par exemple, si vous spécifiez les valeurs &quot;mm/dd/yyyy&quot; dans le boite de texte &quot;Advancée&quot; , la date affichée sera mois/jour/année (i.e. 11/23/2005)');
	define('AdvancedDateHelpDayOfMonth', 'Jour du mois (1 à 31)');
	define('AdvancedDateHelpNumericMonth', 'Mois (1 à 12)');
	define('AdvancedDateHelpTextualMonth', 'Mois (Jan à Dec)');
	define('AdvancedDateHelpYear2', 'Année, 2 chiffres');
	define('AdvancedDateHelpYear4', 'Année, 4 chiffres');
	define('AdvancedDateHelpDayOfYear', 'Jour de l\'année (1 à 366)');
	define('AdvancedDateHelpQuarter', 'Trimestre');
	define('AdvancedDateHelpDayOfWeek', 'Jour de la semaine (Lun à Dim)');
	define('AdvancedDateHelpWeekOfYear', 'Week of year (1 through 53)');

	define('InfoNoMessagesFound', 'Aucun messages trouvés.');
	define('ErrorSMTPConnect', 'Impossible de se connecter au serveur SMTP. Vérifiez les paramètres de votre serveur SMTP.');
	define('ErrorSMTPAuth', 'Mauvais nom d\'utilisateur ou mot de passe. L\'authentification a échoué.');
	define('ReportMessageSent', 'Votre message a été envoyé.');
	define('ReportMessageSaved', 'Votre message a été enregistré.');
	define('ErrorPOP3Connect', 'Impossible de se connecter au serveur POP3, vérifiez les paramètres du serveur POP3.');
	define('ErrorIMAP4Connect', 'Impossible de se connecter au serveur IMAP4, vérifiez les paramètres du serveur IMAP4.');
	define('ErrorPOP3IMAP4Auth', 'Mauvais email/nom d\'utilisateur ou mot de passe. l\'authentification a échoué.');
	define('ErrorGetMailLimit', 'Désolé, votre boite mail dépasse la taille limite.');

	define('ReportSettingsUpdatedSuccessfuly', 'Les paramètres ont été mis à jour avec succès.');
	define('ReportAccountCreatedSuccessfuly', 'Le compte a été créé avec succès.');
	define('ReportAccountUpdatedSuccessfuly', 'Le compte a été mis à jour avec succès.');

	define('ConfirmDeleteAccount', 'Etes-vous sur de vouloir supprimer ce compte ?');

	define('ReportFiltersUpdatedSuccessfuly', 'Les filtres ont été mis à jour avec succès.');
	define('ReportSignatureUpdatedSuccessfuly', 'Signature has been updated successfully.');
	define('ReportFoldersUpdatedSuccessfuly', 'Folders have been updated successfully.');
	define('ReportContactsSettingsUpdatedSuccessfuly', 'Contacts\' settings have been updated successfully.');

	define('ErrorInvalidCSV', 'le fichier CSV que vous avez sélectionné a un format invalide.');
// The group "guies" was successfully added.
	define('ReportGroupSuccessfulyAdded1', 'Le groupe');
	define('ReportGroupSuccessfulyAdded2', 'a été ajouté avec succès.');
	define('ReportGroupUpdatedSuccessfuly', 'Le groupe a été mis à jour avec succès.');
	define('ReportContactSuccessfulyAdded', 'Le contact a été ajouté avec succès.');
	define('ReportContactUpdatedSuccessfuly', 'Le contact a été mis à jour avec succès.');
// Contact(s) was added to group "friends".
	define('ReportContactAddedToGroup', 'Contact(s) ont été ajoutés au groupe');
	define('AlertNoContactsGroupsSelected', 'Aucun contacts ou groupes sélectionnés.');

	define('InfoListNotContainAddress', 'Si la liste ne contient pas l\'adresse que vous cherchez, essayez avec sa première lettre.');

	define('DirectAccess', 'D');
	define('DirectAccessTitle', 'Mode Direct. WebMail accède aux messages directement sur le serveur de mail.');

	define('FolderInbox', 'Boite de réception');
	define('FolderSentItems', 'Eléments envoyés');
	define('FolderDrafts', 'Brouillon');
	define('FolderTrash', 'Poubelle');

	define('LanguageDanish', 'Danois');
	define('LanguagePolish', 'Polonais');

	define('FileLargerAttachment', 'Le fichier attaché dépasse la taille maximum autorisée.');
	define('FilePartiallyUploaded', 'Seulement une partie du fichier a été télécharger à cause d\'une erreur.');
	define('NoFileUploaded', 'Aucun fichier n\'a été télécharger.');
	define('MissingTempFolder', 'Le répertoire temporaire est manquant.');
	define('MissingTempFile', 'Le fichier temporaire est manquant.');
	define('UnknownUploadError', 'Une erreur inattendue est survenue lors du téléchargement du fichier.');
	define('FileLargerThan', 'Erreur de téléchargement. Vraisemblablement, le fichier est plus grand que');
	define('PROC_CANT_LOAD_DB', 'Impossible de se connecter à la base de données.');
	define('PROC_CANT_LOAD_LANG', 'Impossible de trouver le fichier de langue nécessaire.');
	define('PROC_CANT_LOAD_ACCT', 'Le compte n\'existe pas, peut-être a t\'il été effacé.');

	define('DomainDosntExist', 'Ce nom de domaine n\'existe pas sur le serveur de mails.');
	define('ServerIsDisable', 'L\'utilisation du serveur de mail par un administrateur est interdite.');

	define('PROC_ACCOUNT_EXISTS', 'Le compte ne peut-être créé car il existe déjà.');
	define('PROC_CANT_GET_MESSAGES_COUNT', 'Impossible d\'obtenir le nombre de messages du dossier.');
	define('PROC_CANT_MAIL_SIZE', 'Impossible d\'obtenir la taille du message.');

	define('Organization', 'Organisation');
	define('WarningOutServerBlank', 'Vous ne pouvez pas laisser le champ Serveur SMTP vide');
	
//
	define('JS_LANG_Refresh', 'Actualiser');
	define('JS_LANG_MessagesInInbox', 'Message(s) dans la boite de réception');
	define('JS_LANG_InfoEmptyInbox', 'La boite de réception est vide');

// webmail 4.2 constants
	define('LanguagePortugueseBrazil', 'Portuguese-Brazil');
	define('LanguageHungarian', 'Hongrois');

	define('BackToList', 'Retour à la liste');
	define('InfoNoContactsGroups', 'Pas de contacts ou de groupes.');
	define('InfoNewContactsGroups', 'Vous pouvez soit créer des nouveaux contacts/groupes ou importer des contacts depuis un fichier .CSV au format MS Outlook.');
	define('DefTimeFormat', 'Format heures par défaut');
	define('SpellNoSuggestions', 'Pas de suggestions');
	define('SpellWait', 'Merci de patienter&hellip;');

	define('InfoNoMessageSelected', 'Aucun message sélectionné.');
	define('InfoSingleDoubleClick', 'Vous pouvez soit cliquer une seule fois sur un message de la liste pour en voir l\'aperçu ici ou double cliquer pour le voir en affichage plein écran.');

// calendar
	define('TitleDay', 'Affichage par jour');
	define('TitleWeek', 'Affichage par semaine');
	define('TitleMonth', 'Affichage par mois');

	define('ErrorNotSupportBrowser', 'Le Calendrier AfterLogic ne supporte pas votre navigateur Internet. Merci d\'utiliser FireFox 2.0 ou supérieur, Opera 9.0 ou supérieur, Internet Explorer 6.0 ou supérieur, Safari 3.0.2 ou supérieur.');
	define('ErrorTurnedOffActiveX', 'Le Support ActiveX est désactivé. <br/>Vous devez l\'activer pour pouvoir utiliser cette application.');

	define('Calendar', 'Calendrier');

	define('TabDay', 'Jour');
	define('TabWeek', 'Semaine');
	define('TabMonth', 'Mois');

	define('ToolNewEvent', 'Nouvel&nbsp;évènement');
	define('ToolBack', 'Retour');
	define('ToolToday', 'Aujourd\'hui');
	define('AltNewEvent', 'Nouvel évènement');
	define('AltBack', 'Retour');
	define('AltToday', 'Aujourd\'hui');
	define('CalendarHeader', 'Calendrier');
	define('CalendarsManager', 'Gestionnaire de Calendriers');

	define('CalendarActionNew', 'Nouveau calendrier');
	define('EventHeaderNew', 'Nouvel évènement');
	define('CalendarHeaderNew', 'Nouveau calendrier');

	define('EventSubject', 'Sujet');
	define('EventCalendar', 'Calendrier');
	define('EventFrom', 'De');
	define('EventTill', 'jusqu\'à');
	define('CalendarDescription', 'Description');
	define('CalendarColor', 'Couleur');
	define('CalendarName', 'Nom du Calendrier');
	define('CalendarDefaultName', 'Mon Calendrier');

	define('ButtonSave', 'Enregistrer');
	define('ButtonCancel', 'Annuler');
	define('ButtonDelete', 'Effacer');

	define('AltPrevMonth', 'Mois précédent');
	define('AltNextMonth', 'Mois suivant');

	define('CalendarHeaderEdit', 'Modifier Calendrier');
	define('CalendarActionEdit', 'Modifier Calendrier');
	define('ConfirmDeleteCalendar', 'Etes-vous sûr de vouloir supprimer ce calendrier ?');
	define('InfoDeleting', 'Effacement en cours...');
	define('WarningCalendarNameBlank', 'Vous ne pouvez-vous pas laisser le nom du calendrier vide.');
	define('ErrorCalendarNotCreated', 'Calendrier non créé.');
	define('WarningSubjectBlank', 'Vous ne pouvez-vous pas laisser le sujet vide.');
	define('WarningIncorrectTime', 'L\'heure spécifiée contient des caractères illégaux.');
	define('WarningIncorrectFromTime', 'La valeur du champ \'De\' est incorrecte.');
	define('WarningIncorrectTillTime', 'La valeur du champ \'jusqu\'à\' incorrecte.');
	define('WarningStartEndDate', 'La valeur \'Heure de fin\' doit être supérieure ou égale à \'Heure de départ\'');
	define('WarningStartEndTime', 'La valeur \'Heure de fin\' doit être supérieure à \'Heure de départ\'');
	define('WarningIncorrectDate', 'Le Format de la date doit être correct.');
	define('InfoLoading', 'Chargement...');
	define('EventCreate', 'Créer un évènement');
	define('CalendarHideOther', 'Masquer les autres calendriers');
	define('CalendarShowOther', 'Afficher les autres calendriers');
	define('CalendarRemove', 'Effacer le Calendrier');
	define('EventHeaderEdit', 'Modifier un évènement');

	define('InfoSaving', 'Enregistrement...');
	define('SettingsDisplayName', 'Nom affiché');
	define('SettingsTimeFormat', 'Format de l\'Heure');
	define('SettingsDateFormat', 'Format de la Date');
	define('SettingsShowWeekends', 'Afficher les Week-Ends');
	define('SettingsWorkdayStarts', 'Début par jour ouvré');
	define('SettingsWorkdayEnds', 'Fin');
	define('SettingsShowWorkday', 'Afficher les journées de travail');
	define('SettingsWeekStartsOn', 'Les Week-Ends commencent le ');
	define('SettingsDefaultTab', 'Onglet par défaut');
	define('SettingsCountry', 'Pays');
	define('SettingsTimeZone', 'Fuseau horaire');
	define('SettingsAllTimeZones', 'Tous les fuseaux horaires');

	define('WarningWorkdayStartsEnds', 'L\'heure de fin des \'jours ouvrés\' doit être supérieure à l\'heure de départ des \'jours ouvrés\'');
	define('ReportSettingsUpdated', 'Vos paramêtres ont été enregistrés correctement.');

	define('SettingsTabCalendar', 'Calendrier');

	define('FullMonthJanuary', 'Janvier');
	define('FullMonthFebruary', 'Fevrier');
	define('FullMonthMarch', 'Mars');
	define('FullMonthApril', 'Avril');
	define('FullMonthMay', 'Mai');
	define('FullMonthJune', 'Juin');
	define('FullMonthJuly', 'Juillet');
	define('FullMonthAugust', 'Aout');
	define('FullMonthSeptember', 'Septembre');
	define('FullMonthOctober', 'Octobre');
	define('FullMonthNovember', 'Novembre');
	define('FullMonthDecember', 'Decembre');

	define('ShortMonthJanuary', 'Jan');
	define('ShortMonthFebruary', 'Fev');
	define('ShortMonthMarch', 'Mar');
	define('ShortMonthApril', 'Avr');
	define('ShortMonthMay', 'Mai');
	define('ShortMonthJune', 'Jun');
	define('ShortMonthJuly', 'Jul');
	define('ShortMonthAugust', 'Aou');
	define('ShortMonthSeptember', 'Sep');
	define('ShortMonthOctober', 'Oct');
	define('ShortMonthNovember', 'Nov');
	define('ShortMonthDecember', 'Dec');

	define('FullDayMonday', 'Lundi');
	define('FullDayTuesday', 'Mardi');
	define('FullDayWednesday', 'Mercredi');
	define('FullDayThursday', 'Jeudi');
	define('FullDayFriday', 'Vendredi');
	define('FullDaySaturday', 'Samedi');
	define('FullDaySunday', 'Dimanche');

	define('DayToolMonday', 'Lun');
	define('DayToolTuesday', 'Mar');
	define('DayToolWednesday', 'Mer');
	define('DayToolThursday', 'Jeu');
	define('DayToolFriday', 'Ven');
	define('DayToolSaturday', 'Sam');
	define('DayToolSunday', 'Dim');

	define('CalendarTableDayMonday', 'L');
	define('CalendarTableDayTuesday', 'M');
	define('CalendarTableDayWednesday', 'M');
	define('CalendarTableDayThursday', 'J');
	define('CalendarTableDayFriday', 'V');
	define('CalendarTableDaySaturday', 'S');
	define('CalendarTableDaySunday', 'D');

	define('ErrorParseJSON', 'La réponse \'JSON\' fournie par le serveur ne peut être analysée.');

	define('ErrorLoadCalendar', 'Impossible de charger les calendriers');
	define('ErrorLoadEvents', 'Impossible de charger les évčnements');
	define('ErrorUpdateEvent', 'Impossible d\'enregistrer l\'évčnements');
	define('ErrorDeleteEvent', 'Impossible d\'effacer l\'évčnements');
	define('ErrorUpdateCalendar', 'Impossible d\'enregistrer le calendrier');
	define('ErrorDeleteCalendar', 'Impossible d\'effacer le calendrier');
	define('ErrorGeneral', 'Une erreur est survenue sur le serveur. Merci d\'essayer ultérieurement.');

define('BackToCart', 'Back to administration panel');
define('StoreWebmail', 'Store webmail');