{* $Id: usergroup_request_subj.tpl 9743 2010-06-08 08:18:11Z lexa $ *}

{$settings.Company.company_name|unescape}: {$lang.usergroup_request_by_customer} "{if $settings.General.use_email_as_login != "Y"}{$user_data.user_login}{else}{$user_data.email}{/if}"