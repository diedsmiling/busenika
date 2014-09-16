{* $Id: check.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}


{if $check_results.non_writable || $check_results.changed}
<table width="{if $check_results.non_writable && $check_results.changed}100%{else}50%{/if}">
<tr>
		{if $check_results.non_writable}
		<td valign="top" width="50%">
                <p>{$lang.text_uc_non_writable_files}</p>

                <div class="table scrollable">
                        <h5>{$lang.file}</h5>
                        <div class="uc-package-contents">
                {foreach from=$check_results.non_writable item="c"}
                        <p title="{$c}">
                                <span class="float-left">{$c|truncate:60:"&nbsp;...&nbsp;":true:true}</span>
                        </p>
                {foreachelse}
                        <p class="no-items">
                        {$lang.no_data}
                        </p>
                {/foreach}
                        </div>
                </div>

        </td>
		{/if}

		{if $check_results.changed}
		<td valign="top" width="50%">
                <p>{$lang.text_uc_changed_files}</p>

                <div class="table scrollable">
                        <h5>{$lang.file}</h5>
                        <div class="uc-package-contents">
                {foreach from=$check_results.changed item="c"}
                        <p title="{$c}">
                                <span class="float-left">{$c|truncate:60:"&nbsp;...&nbsp;":true:true}</span>
                        </p>
                {foreachelse}
                        <p class="no-items">
                        {$lang.no_data}
                        </p>
                {/foreach}
                        </div>
                </div>
        </td>
		{/if}
</tr>
</table>
{/if}

{if $check_results.no_ftp == true}
<p>{$lang.text_uc_ftp_needed}</p>
<form action="{""|fn_url}" method="post" name="uc_ftp_access">
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

<div class="form-field">
	<label for="ftp_host">{$lang.host}:</label>
	<input type="text" name="settings_data[ftp_hostname]" id="ftp_host" size="10" value="{$uc_settings.ftp_hostname}" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="ftp_user">{$lang.username}:</label>
	<input type="text" name="settings_data[ftp_username]" id="ftp_user" size="10" value="{$uc_settings.ftp_username}" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="ftp_password">{$lang.password}:</label>
	<input type="password" name="settings_data[ftp_password]" id="ftp_password" size="10" value="{$uc_settings.ftp_password}" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="ftp_directory">{$lang.directory}:</label>
	<input type="text" name="settings_data[ftp_directory]" id="ftp_directory" size="10" value="{$uc_settings.ftp_directory}" class="input-text-medium" />
</div>

<div class="buttons-container buttons-bg">
	{include file="buttons/button.tpl" but_name="dispatch[upgrade_center.update_settings]" but_text=$lang.save but_role="button_main"}
</div>

</form>

{else}

{if !$check_results.changed && !$check_results.non_writable}
{$lang.text_uc_check_ok}
{/if}

<form action="{""|fn_url}" method="get" name="uc_check_form">

<div class="buttons-container buttons-bg">
{if !$check_results.non_writable}
	{include file="buttons/button.tpl" but_text=$lang.continue but_name="dispatch[upgrade_center.run_backup]" but_role="button_main"}
{else}
	{include file="buttons/button.tpl" but_text=$lang.check_again but_name="dispatch[upgrade_center.check]" but_role="button_main"}
{/if}
</div>
</form>
{/if}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.check content=$smarty.capture.mainbox}
