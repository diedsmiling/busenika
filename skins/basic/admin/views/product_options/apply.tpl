{* $Id: apply.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="add_global_options" />

{include file="pickers/products_picker.tpl" data_id="added_products" input_name="apply_options[product_ids]" no_item_text=$lang.text_all_items_included|replace:"[items]":$lang.products type="links"}

{include file="common_templates/subheader.tpl" title=$lang.select_options}
{foreach from=$product_options item="po"}
<p>
	<label class="label-html-checkboxes">
		<input class="html-checkboxes" type="checkbox" value="{$po.option_id}" name="apply_options[options][]" />
		{$po.option_name}
	</label>
</p>
{/foreach}

<div class="buttons-container buttons-bg">
	{include file="buttons/button.tpl" but_text=$lang.apply but_name="dispatch[product_options.apply]" but_role="button_main"}

	<label class="valign" for="link">{$lang.apply_as_link}&nbsp;</label>
	<input type="hidden" name="apply_options[link]" value="N" />
	<input type="checkbox" name="apply_options[link]" id="link" value="Y" class="checkbox valign" />
</div>

</form>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.apply_to_products content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=$select_languages}
