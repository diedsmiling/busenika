{* $Id: logos.tpl 10132 2010-07-20 11:08:12Z angel $ *}

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="logotypes_form" enctype="multipart/form-data">

{foreach from=$manifest_definition key="a" item="m" name="fel"}
{assign var="sa" value="skin_name_`$m.skin`"}
<p>{$lang[$m.text]}</p>
<div class="clear">
	<div class="float-left">
		{include file="common_templates/fileuploader.tpl" var_name="logotypes[`$a`]"}
	</div>
	<div class="float-left attach-images-alt logo-image">
		<img class="solid-border" src="{$config.current_path}/skins/{$settings.$sa}/{$m.path}/images/{$manifests[$m.skin][$m.name].filename}" width="{$manifests[$m.skin][$m.name].width}" height="{$manifests[$m.skin][$m.name].height}" />
		<label for="alt_text_{$a}">{$lang.alt_text}:</label>
		<input type="text" class="input-text cm-image-field" id="alt_text_{$a}" name="logo_alt[{$a}]" value="{$manifests[$m.skin][$m.name].alt}" />
	</div>
</div>
{if !$smarty.foreach.fel.last}
<hr />
{/if}
{/foreach}

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[site_layout.update_logos]" hide_second_button=true}
</div>

</form>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.logos content=$smarty.capture.mainbox}
