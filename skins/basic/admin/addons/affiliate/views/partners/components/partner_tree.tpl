{* $Id: partner_tree.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<div class="items-container multi-level">
	{foreach from=$partners item=user name="tree_root"}
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	{if $header}
	{assign var="header" value=""}
	<tr>
		<th>
			<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="on_cat" class="hand cm-combinations" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="off_cat" class="hand cm-combinations hidden" />&nbsp;{$lang.affiliate}
		</th>
	</tr>
	{/if}
	<tr {if $level > 0}class="multiple-table-row"{/if}>
	   	{math equation="x*14" x=$level assign="shift"}
		<td width="100%">
			<span style="padding-left: {$shift}px;">
				{if $user.partners}<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_user_{$user.user_id}" class="hand cm-combination"{if !$show_all} onclick="if (!$('#user_{$user.user_id}').children().get(0)) jQuery.ajaxRequest('{"partners.update?user_id=`$user.user_id`"|fn_url}', {$ldelim}result_ids: 'user_{$user.user_id}'{$rdelim})"{/if} /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_user_{$user.user_id}" class="hand cm-combination hidden" />{/if}<a href="{"partners.update?user_id=`$user.user_id`"|fn_url}"{if !$user.partners} style="padding-left: 14px;"{/if} >{$user.firstname}{if $user.lastname}&nbsp;{/if}{$user.lastname}</a>
			</span>
		</td>
	</tr>
	</table>
	{if $user.partners}
	<div id="user_{$user.user_id}" class="hidden">
		{include file="addons/affiliate/views/partners/components/partner_tree.tpl" partners=$user.partners level=$level+1}
	</div>
	{/if}
	{foreachelse}
		<p class="no-items">{$lang.no_items}</p>
	{/foreach}
</div>