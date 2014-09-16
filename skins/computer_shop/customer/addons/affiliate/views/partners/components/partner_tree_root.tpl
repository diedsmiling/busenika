{* $Id: partner_tree_root.tpl 6892 2009-02-09 14:50:51Z angel $ *}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th>
		<img src="{$images_dir}/icons/plus_minus.gif" id="on_partners" width="13" height="12" border="0" alt="" class="hand cm-combinations" />
		<img src="{$images_dir}/icons/minus_plus.gif" id="off_partners" width="13" height="12" border="0" alt="" class="hand cm-combinations hidden" />
	</th>
	<th width="100%">{$lang.affiliate_tree}</th>
</tr>
<tr>
	<td colspan="2">
	{if $partners}
		<div id="id_tree">
		{foreach from=$partners item=user name="tree_root"}
			{include file="addons/affiliate/views/partners/components/partner_tree_limb.tpl" user=$user level=0 space="" last=$smarty.foreach.tree_root.last}
		{/foreach}
		</div>
	{else}
		<p class="no-items">{$lang.no_users_found}</p>
	{/if}
	</td>
</tr>
<tr class="table-footer">
	<td colspan="2">&nbsp;</td>
</tr>
</table>