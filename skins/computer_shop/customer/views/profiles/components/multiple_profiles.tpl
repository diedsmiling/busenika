{* $Id: multiple_profiles.tpl 10336 2010-08-04 07:17:04Z klerik $ *}

{if $settings.General.user_multiple_profiles == "Y" && $auth.user_id}

{if $show_title}
	{include file="common_templates/subheader.tpl" title=$lang.select_profile}
{/if}

{if !$hide_profile_select}
<div class="form-field select-profile">
	<label>{$lang.select_profile}:</label>

	{foreach from=$user_profiles item="up" name="pfe"}
		{if $up.profile_id == $profile_id}
		<strong>{$up.profile_name}</strong>
		{else}
		<a {if $use_ajax}class="cm-ajax"{/if} href="{$config.current_url|fn_query_remove:"profile":"selected_section"|fn_link_attach:"profile_id=`$up.profile_id`"|fn_url}" rev="checkout_steps,cart_items,checkout_totals">{$up.profile_name}</a>
		{/if}
		{if !$smarty.foreach.pfe.last}&nbsp;|&nbsp;{/if}

		{if $up.profile_type != "P" && !$hide_profile_delete}
			{include file="buttons/button.tpl" but_meta="cm-confirm" but_rev="checkout_steps,cart_items,checkout_totals" but_role="delete" but_text="&nbsp;" but_href="profiles.delete_profile?profile_id=`$up.profile_id`"}
		{/if}
	{/foreach}
	{if !$skip_create}
		&nbsp;&nbsp;{$lang.or}&nbsp;&nbsp;&nbsp;{if $smarty.request.profile == "new"}<strong>{$lang.create_profile}</strong>{else}<a class="lowercase{if $use_ajax} cm-ajax{/if}" href="{if $create_href}{$create_href|fn_url}{else}{$config.current_url|fn_query_remove:"profile_id":"selected_section"|fn_link_attach:"profile=new"|fn_url}{/if}" rev="checkout_steps,cart_items,checkout_totals">{$lang.create_profile}</a>{/if}
	{/if}
</div>
{/if}

{if !$hide_profile_name}
<div class="form-field">
	<label for="elm_profile_id" class="cm-required">{$lang.profile_name}:</label>
	{if $action == "add_profile" || $no_edit != "Y"}
		{assign var="profile_name" value="- `$lang.new` -"}
	{else}
		{assign var="profile_name" value=$lang.main}
	{/if}

	<input type="hidden" name="user_data[profile_id]" value="{$profile_id|default:"0"}" />
	<input type="text" class="input-text" id="elm_profile_id" name="user_data[profile_name]" size="32" value="{$user_data.profile_name|default:$profile_name}" />
</div>
{/if}

{else}
	<input type="hidden" id="profile_name" name="user_data[profile_name]" value="{$user_data.profile_name|default:$lang.main}" />
{/if}