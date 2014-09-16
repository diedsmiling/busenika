{* $Id: products_m_update.tpl 8113 2009-10-22 09:00:54Z lexa $ *}

	<table cellpadding="1" cellspacing="1" border="0">
	{foreach from=$v item="usergroup" key="usergroup_id"}
	<tr>
		<td>{$usergroup}:</td>
		<td>&nbsp;</td>
		<td><input type="text" {if $override_box}id="field_{$field}__"{/if} value="{$product.reward_points.$usergroup_id.amount|default:"0"}" class="input-text" name="{if $override_box}override_reward_points[{$usergroup_id}]{else}reward_points[{$product.product_id}][{$usergroup_id}]{/if}" class="elm-disabled" {if $override_box}disabled="disabled"{/if} /></td>
	</tr>
	{/foreach}
	</table>
