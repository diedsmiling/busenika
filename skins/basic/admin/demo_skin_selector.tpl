{* $Id: demo_skin_selector.tpl 7497 2009-05-19 10:41:21Z zeke $ *}

{assign var="area" value=$smarty.const.AREA}
{assign var="area_name" value=$smarty.const.AREA_NAME}
{assign var="l" value="text_`$area_name`_skin"}
{assign var="c_url" value=$config.current_url|fn_query_remove:"demo_skin"}
{if $c_url|strpos:"?" === false}
	{assign var="c_url" value="`$c_url`?"}
{/if}

<div class="demo-site-panel" style="padding: 3px;">
<table cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
	<td class="strong">DEMO SITE PANEL</td>
	<td class="right">{$lang.$l}:</td>
	<td>
		<select name="demo_skin[{$area}]" onchange="jQuery.redirect('{$c_url|fn_link_attach:"demo_skin[`$area`]="}' + this.value);">
		{foreach from=$demo_skin.available_skins item=s key=k}
			<option value="{$k}" {if $demo_skin.selected.$area == $k}selected="selected"{/if}>{$s.description}</option>
		{/foreach}
		</select>
	</td>
	<td width="100%" class="right">Area:</td>
	<td>
		<select name="area" onchange="jQuery.redirect(this.value);">
			<option value="{$config.customer_index}" {if $area == "C"}selected="selected"{/if}>Storefront</option>
			<option value="{$config.admin_index}" {if $area == "A"}selected="selected"{/if}>Administration panel</option>
		</select>
	</td>
</tr>
</table>
</div>