{* $Id: average_rating.tpl 5626 2008-07-21 07:47:04Z brook $ *}

{assign var="average_rating" value=$object_id|fn_get_average_rating:$object_type}

{if $average_rating}
{include file="addons/discussion/views/discussion/components/stars.tpl" stars=$average_rating|fn_get_discussion_rating}
{/if}
