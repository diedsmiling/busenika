{* $Id: dynamic.tpl 5805 2008-08-24 20:31:37Z zeke $ *}

<div id="container_{$elm_id}">
{if $smarty.request.condition}
	{include file="views/promotions/components/condition.tpl"}

{elseif $smarty.request.group}
	{include file="views/promotions/components/group.tpl"}

{elseif $smarty.request.bonus}
	{include file="views/promotions/components/bonus.tpl"}
{/if}
<!--container_{$elm_id}--></div>
