{* $Id: update.tpl 10579 2010-09-02 11:32:23Z zeke $ *}
{script src="js/jquery.simpletip-1.3.1.js"}

{assign var="_addon" value=$smarty.request.addon}

<div id="content_group{$_addon}">
<form action="{""|fn_url}" method="post" name="update_addon_{$_addon}_form" class="cm-form-highlight">
<input type="hidden" name="addon" value="{$smarty.request.addon}" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			{foreach from=$fields key="section" item="field_item"}
			<li class="cm-js" id="{$_addon}_{$section}"><a>{$lang.$section}</a></li>
			{/foreach}
		</ul>
	</div>
	<div class="cm-tabs-content" id="tabs_content_{$_addon}">
	{foreach from=$fields key="section" item="field_item"}
	<div id="content_{$_addon}_{$section}">
		<fieldset>
		{foreach from=$field_item key="name" item="data" name="fe_addons"}
			{if $data.type == "O"}
				<div>{$data.info|unescape}</div>
			{elseif $data.type == "E"}
				<div>{include file="addons/`$smarty.request.addon`/settings/`$data.template`"}</div>
			{elseif $data.type == "H"}
				{include file="common_templates/subheader.tpl" title=$data.description}
			{else}
			<div class="form-field">
				<label for="addon_option_{$_addon}_{$name}">{$data.description}{if $data.tooltip}{include file="common_templates/tooltip.tpl" tooltip=$data.tooltip}{/if}:</label>
				{if $data.type == "S"}
					<select id="addon_option_{$_addon}_{$name}" name="addon_data[options][{$name}]">
						{foreach from=$data.variants key="v_name" item="v_data"}
						<option value="{$v_name}" {if $addon_options.$name == $v_name}selected="selected"{/if}>{$v_data}</option>
						{/foreach}
					</select>
				{elseif $data.type == "M"}
					<select id="addon_option_{$_addon}_{$name}" name="addon_data[options][{$name}][]" multiple="multiple">
						{foreach from=$data.variants key="v_name" item="v_data"}
						<option value="{$v_name}" {if $addon_options.$name && $addon_options.$name.$v_name}selected="selected"{/if}>{$v_data}</option>
						{/foreach}
					</select>
				{elseif $data.type == "R"}
					<div class="select-field">
					{foreach from=$data.variants item=v key=k}
					<input type="radio" name="addon_data[options][{$name}]" value="{$k}" {if $addon_options.$name == $k}checked="checked"{/if} class="radio" id="variant_{$_addon}_{$name}_{$k}" /><label for="variant_{$_addon}_{$name}_{$k}">{$v}</label>
					{/foreach}
					</div>

				{elseif $data.type == "N"}
					<div class="select-field">
					{foreach from=$data.variants item=v key=k}
					<input type="checkbox" name="addon_data[options][{$name}][]" value="{$k}" {if $addon_options.$name.$k}checked="checked"{/if} class="checkbox" id="variant_{$_addon}_{$name}_{$k}" /><label for="variant_{$_addon}_{$name}_{$k}">{$v}</label>
					{/foreach}
					</div>

				{elseif $data.type == "X"}
					<select id="addon_option_{$_addon}_{$name}" name="addon_data[options][{$name}]">
						<option value="">- {$lang.select_country} -</option>
						{assign var="countries" value=""|fn_get_simple_countries}
						{foreach from=$countries item=country key=ccode}
							<option value="{$ccode}" {if $ccode == $addon_options.$name}selected="selected"{/if}>{$country|escape}</option>
						{/foreach}
					</select>

				{elseif $data.type == "W"}
					<script type="text/javascript">
						//<![CDATA[
						var default_state = {$ldelim}'billing':'{$addon_options.$name|escape:javascript}'{$rdelim};
						//]]>
					</script>
					<input type="text" id="addon_option_{$_addon}_{$name}_d" name="addon_data[options][{$name}]" value="{$addon_options.$name}" size="32" maxlength="64" value="" disabled="disabled" class="hidden" />
					<select id="addon_option_{$_addon}_{$name}" name="addon_data[options][{$name}]">
						<option value="">- {$lang.select_state} -</option>
					</select>

				{elseif $data.type == "F"}
					<input id="input_addon_option_{$_addon}_{$name}" type="text" name="addon_data[options][{$name}]" value="{$addon_options.$name}" size="30" class="valign input-text" />&nbsp;<input id="addon_option_{$_addon}_{$name}" type="button" value="{$lang.browse}" class="valign input-text" onclick="fileuploader.init('box_server_upload', 'input_' + this.id, event);" />

				{elseif $data.type == "C"}
					<input type="hidden" name="addon_data[options][{$name}]" value="N" />
					<input type="checkbox" name="addon_data[options][{$name}]" id="addon_option_{$_addon}_{$name}" value="Y" {if $addon_options.$name == "Y"} checked="checked"{/if} class="checkbox" />

				{elseif $data.type == "I"}
					<input type="text" name="addon_data[options][{$name}]" id="addon_option_{$_addon}_{$name}" value="{$addon_options.$name}" class="input-text" />

				{elseif $data.type == "P"}
					<input type="password" name="addon_data[options][{$name}]" id="addon_option_{$_addon}_{$name}" value="{$addon_options.$name}" class="input-text" />

				{elseif $data.type == "T"}
					<textarea class="input-textarea-long" name="addon_data[options][{$name}]" cols="55" rows="8" id="addon_option_{$_addon}_{$name}">{$addon_options.$name}</textarea>
				{elseif $data.type == "B"}
					{include file="views/addons/components/selectable_box.tpl" addon=$_addon name=$name id="addon_option_`$_addon`_`$name`" fields=$data.variants selected_fields=$addon_options.$name}
				{/if}
			</div>
			{/if}
		{/foreach}
		</fieldset>
		{hook name="addons:options"}
		{/hook}
	</div>
	{/foreach}
	</div>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[addons.update]" cancel_action="close"}
</div>

</form>

<!--content_group{$_addon}--></div>
