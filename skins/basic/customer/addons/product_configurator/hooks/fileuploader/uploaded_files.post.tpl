{* $Id: uploaded_files.post.tpl 8241 2009-11-17 07:40:08Z alexions $ *}

{if $edit_configuration}
	<input type="hidden" name="{$name}[custom_files][uploaded][{$image.file}][product_id]" value="{$product.product_id}" />
	<input type="hidden" name="{$name}[custom_files][uploaded][{$image.file}][option_id]" value="{$po.option_id}" />
	<input type="hidden" name="{$name}[custom_files][uploaded][{$image.file}][name]" value="{$image.name}" />
	<input type="hidden" name="{$name}[custom_files][uploaded][{$image.file}][path]" value="{$image.file}" />
{/if}