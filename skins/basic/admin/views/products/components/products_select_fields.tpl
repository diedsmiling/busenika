{* $Id: products_select_fields.tpl 7717 2009-07-15 09:43:35Z zeke $ *}

<input type="hidden" name="selected_fields[object]" value="product" />
{math equation="ceil(n/c)" assign="rows" n=$selected_fields|count c=$columns|default:"5"}

{split data=$selected_fields|sort_by:"text" size=$rows assign="splitted_selected_fields" vertical_delimition=false size_is_horizontal=true}

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr valign="top">
	{foreach from=$splitted_selected_fields item="sfs"}
		<td>
		<ul>
			{foreach from=$sfs item="sf" name="foreach_sfs"}
				<li class="select-field">
					{if $sf}
						{if $sf.disabled == "Y"}<input type="hidden" value="Y" name="selected_fields{$sf.name}" />{/if}
						<input type="checkbox" value="Y" name="selected_fields{$sf.name}" id="elm_{$sf.name|md5}" checked="checked" {if $sf.disabled == "Y"}disabled="disabled"{/if} class="checkbox cm-item-s" />
						<label for="elm_{$sf.name|md5}">{$sf.text}&nbsp;</label>
					{/if}
				</li>
			{/foreach}
		</ul>
		</td>
	{/foreach}
</tr></table>
<p>
<a name="check_all" class="cm-check-items-s cm-on underlined">{$lang.select_all}</a>&nbsp;/&nbsp;<a href="#sfields" name="check_all" class="cm-check-items-s cm-off underlined">{$lang.unselect_all}</a>
</p>
