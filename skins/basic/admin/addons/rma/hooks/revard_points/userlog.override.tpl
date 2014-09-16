{* $Id: userlog.override.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $ul.action == $smarty.const.CHANGE_DUE_RMA}
	{assign var="statuses" value=$smarty.const.STATUSES_RETURN|fn_get_statuses:true}
	{assign var="reason" value=$ul.reason|@unserialize}
	{$lang.rma_return}&nbsp;<a href="{"rma.details?return_id=`$reason.return_id`"|fn_url}" class="underlined">&nbsp;<strong>#{$reason.return_id}</strong></a>:&nbsp;{$statuses[$reason.from]}&nbsp;&#8212;&#8250;&nbsp;{$statuses[$reason.to]}
{/if}