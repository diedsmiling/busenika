{* $Id: my_account.tpl 10109 2010-07-19 11:58:58Z klerik $ *}
{** block-description:my_account **}

<!--dynamic:my_account-->
{if $auth.user_id}
<strong>{$user_info.firstname} {$user_info.lastname}</strong>
{/if}

{assign var="return_current_url" value=$config.current_url|escape:url}
<ul class="arrows-list">
{hook name="profiles:my_account_menu"}
	{if $auth.user_id}
		<li><a href="{"profiles.update"|fn_url}" class="underlined">{$lang.profile_details}</a></li>
		<li><a href="{"orders.downloads"|fn_url}" class="underlined">{$lang.downloads}</a></li>
	{else}
		<li><a href="{if $controller == "auth" && $mode == "login_form"}{$config.current_url|fn_url}{else}{"auth.login_form?return_url=`$return_current_url`"|fn_url}{/if}" class="underlined">{$lang.sign_in}</a> / <a href="{"profiles.add"|fn_url}" class="underlined">{$lang.register}</a></li>
	{/if}
	<li><a href="{"orders.search"|fn_url}" class="underlined">{$lang.orders}</a></li>
{/hook}

{if $auth.user_id}
		<li class="delim"></li>
		<li><a href="{"auth.logout?redirect_url=`$return_current_url`"|fn_url}" class="underlined">{$lang.sign_out}</a></li>
{/if}
</ul>

<div class="updates-wrapper">

<form action="{""|fn_url}" method="get" class="cm-ajax" name="track_order_quick">
{strip}
<p>{$lang.track_my_order}:</p>

<div class="form-field">
<label for="track_order_item{$block.block_id}" class="cm-required hidden">{$lang.track_my_order}:</label>

	<input type="text" size="20" class="input-text cm-hint" id="track_order_item{$block.block_id}" name="track_data" value="{$lang.order_id|escape:html}{if !$auth.user_id}/{$lang.email|escape:html}{/if}" />
	{include file="buttons/go.tpl" but_name="orders.track_request" alt=$lang.go}
</div>

{/strip}
</form>

</div>
<!--/dynamic-->