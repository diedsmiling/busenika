{* $Id: design_mode_panel.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

<div id="design_mode_panel" class="popup {if "CUSTOMIZATION_MODE"|defined}customization{else}translate{/if}-mode" style="{if $smarty.cookies.design_mode_panel_offset}{$smarty.cookies.design_mode_panel_offset}{/if}">
	<div class="cm-popup-content-header">
		<h1>{if "CUSTOMIZATION_MODE"|defined}{$lang.customization_mode}{else}{$lang.translate_mode}{/if}</h1>
	</div>
	<div class="cm-popup-content-footer">
		<form action="{""|fn_url}" method="post" name="design_mode_panel_form">
			<input type="hidden" name="design_mode" value="{if "CUSTOMIZATION_MODE"|defined}translation_mode{else}customization_mode{/if}" />
			<input type="hidden" name="current_url" value="{$config.current_url}" />
			<input type="submit" name="dispatch[design_mode.update_design_mode]" value="" class="hidden" />
			{if "CUSTOMIZATION_MODE"|defined}
				{assign var="mode_val" value=$lang.switch_to_translation_mode}
			{else}
				{assign var="mode_val" value=$lang.switch_to_customization_mode}
			{/if}
			<p class="right"><a class="cm-submit" name="dispatch[design_mode.update_design_mode]" rev="design_mode_panel_form">{$mode_val}</a></p>
		</form>
	</div>
</div>