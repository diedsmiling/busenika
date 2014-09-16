{* $Id: product_info.post.tpl 9196 2010-03-30 13:03:21Z lexa $ *}

{if $oi.returns_info}
	{if !$return_statuses}{assign var="return_statuses" value=$smarty.const.STATUSES_RETURN|fn_get_statuses:true}{/if}

	<p>
		<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_ret_{$key}" class="hand cm-combination" />
		<img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_ret_{$key}" class="hand hidden cm-combination" />
		<a id="sw_ret_{$key}" class="cm-combination">{$lang.returns_info}</a>
	</p>

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tbody id="ret_{$key}" class="hidden">	
	<tr>
		<th>&nbsp;{$lang.status}</th>
		<th>{$lang.amount}</th>
	</tr>
	{foreach from=$oi.returns_info item="amount" key="status" name="f_rinfo"}
	<tr>
		<td>{$return_statuses.$status|default:""}</td>
		<td>{$amount}</td>
	</tr>
	{/foreach}	
	</tbody>	
	</table>
	</div>
{/if}
