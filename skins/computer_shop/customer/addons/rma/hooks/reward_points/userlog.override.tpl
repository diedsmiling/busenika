{* $Id: userlog.override.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $ul.action == $smarty.const.CHANGE_DUE_RMA}
	{include file="addons/rma/views/reward_points/components/rma_userlog.tpl"}
{/if}
