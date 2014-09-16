{* $Id: recover_password.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{include file="letter_header.tpl"}

{$lang.text_confirm_passwd_recovery}:<br /><br />

<a href="{"auth.recover_password?ekey=`$ekey`"|fn_url:'C':'http':'&'}">{"auth.recover_password?ekey=`$ekey`"|fn_url:'C':'http':'&'}</a>

{include file="letter_footer.tpl"}