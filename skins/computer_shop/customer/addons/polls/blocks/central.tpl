{* $Id: central.tpl 9783 2010-06-10 10:24:09Z lexa $ *}
{** block-description:polls_central **}

<!--dynamic:polls_central-->
{if $items}
{foreach from=$items item="poll"}
{include file="addons/polls/views/pages/components/poll.tpl" obj_prefix="`$block.block_id`_"}
{/foreach}
{/if}
<!--/dynamic-->