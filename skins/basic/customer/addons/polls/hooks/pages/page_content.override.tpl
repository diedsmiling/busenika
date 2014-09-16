{* $Id: page_content.override.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{if $page.page_type == $smarty.const.PAGE_TYPE_POLL}
	{include file="addons/polls/views/pages/components/poll.tpl" poll=$page.poll}
{/if}