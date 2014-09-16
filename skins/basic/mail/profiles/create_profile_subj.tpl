{* $Id: create_profile_subj.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{assign var='u_type' value=$user_data.user_type|fn_get_user_type_description|lower|escape}
{$settings.Company.company_name|unescape}: {$lang.new_profile_notification|replace:'[user_type]':$u_type}