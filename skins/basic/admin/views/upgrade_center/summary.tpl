{* $Id: summary.tpl 10381 2010-08-09 06:13:58Z klerik $ *}

{capture name="mainbox"}

{capture name="open_store_link"}
<a class="cm-ajax cm-confirm text-button" rev="store_mode" href="{"tools.store_mode?state=opened"|fn_url}">{$lang.open_store}</a>
{/capture}

{$lang.text_uc_upgrade_completed|replace:"[link]":$smarty.capture.open_store_link}
<p>&nbsp;</p>
{assign var="package" value=$smarty.request.package|escape:url}
<a href="{"upgrade_center.revert?package=`$package`"|fn_url}">{$lang.revert}</a>

<a href="{"upgrade_center.manage"|fn_url}">{$lang.upgrade_center}</a>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.summary content=$smarty.capture.mainbox}
