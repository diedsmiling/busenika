{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

{if $installed_upgrades.has_conflicts == true}
<div class="notes">
<h5>{$lang.note}:</h5>
{$lang.text_uc_has_conflicts}: <a class="tool-link" href="{"upgrade_center.installed_upgrades"|fn_url}">{$lang.view}</a>
</div>
{/if}

{* ???????? ######## ???????? *}
{if $require_license_number == true}

<p>{$lang.text_uc_license_number_required}</p>
<form action="{""|fn_url}" method="post" name="uc_license_form">
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

<div class="form-field">
	<label for="elm_license_number">{$lang.license_number}:</label>
	<input type="text" name="settings_data[license_number]" id="elm_license_number" size="20" value="{$uc_settings.license_number}" class="input-text-large" />
</div>

{* ???????? ######## ???????? *}
<div><br />Warning: Access to current file list unsuccessful. Do not continue with updates or upgrade!<br /></div>
{* ???????? ######## ???????? *}

<div class="buttons-container buttons-bg">
	{include file="buttons/button.tpl" but_name="dispatch[upgrade_center.update_settings]" but_text=$lang.save but_role="button_main"}
</div>

</form>

{else}

{foreach from=$packages item="package" name="fep"}
	{include file="common_templates/subheader.tpl" title=$package.name}

	<div class="order-info">
	{$lang.version}:&nbsp;<span>{$package.to_version}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$lang.release_date}:&nbsp;<span>{$package.timestamp|date_format:"`$settings.Appearance.date_format` `$settings.Appearance.time_format`"}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$lang.filesize}:&nbsp;<span>{$package.size|formatfilesize}</span>
	</div>

	<p>{$lang.package_contents}</p>
	<div class="table scrollable">
		<h5>{$lang.file}</h5>
		<div class="uc-package-contents">
	{foreach from=$package.contents item="c"}
		<p title="{$c}">
			{$c|truncate:60:"&nbsp;...&nbsp;":true:true}
		</p>
	{/foreach}
		</div>
	</div>

	<p>&nbsp;</p>
	{$package.description|unescape}

	<div class="buttons-container">
		{if $package.from_version == $smarty.const.PRODUCT_VERSION}
			{if $package.is_avail == 'Y'}	
				<form action="{""|fn_url}" method="get" name="uc_form_{$package.package_id}" >
				<input type="hidden" name="package_id" value="{$package.package_id}" />
				<input type="hidden" name="md5" value="{$package.md5}" />
		
				{include file="buttons/button.tpl" but_text=$lang.install but_name="dispatch[upgrade_center.get_upgrade]" but_role="button_main"}
		
				</form>
			{elseif $package.purchase_time_limit == 'Y'}
				<strong>{$lang.upgrade_is_not_avail|replace:"[url]":$uc_settings.updates_server}</strong>	
			{else}
				<strong>{$lang.update_period_expired|replace:"[url]":$uc_settings.updates_server}</strong>
			{/if}
		{else}
		<p>{$lang.text_uc_upgrade_needed|replace:"[to_version]":$package.from_version|replace:"[your_version]":$smarty.const.PRODUCT_VERSION}</p>
		{/if}
	</div>

{foreachelse}
	{if $has_conflicts == true}
	<p>&nbsp;</p>
	{/if}
	<p class="no-items">{$lang.text_no_upgrades_available}</p>
{/foreach}


{capture name="extra_tools"}
	{include file="buttons/button.tpl" but_href="upgrade_center.refresh" but_text=$lang.refresh_packages_list but_role="tool"}
	{if $installed_upgrades.has_upgrades}
		&nbsp;|&nbsp;{include file="buttons/button.tpl" but_href="upgrade_center.installed_upgrades" but_text=$lang.installed_upgrades but_role="tool"}
	{/if}
{/capture}


{/if}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.upgrade_center content=$smarty.capture.mainbox extra_tools=$smarty.capture.extra_tools}
