{* $Id: view_tools.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{capture name="view_tools"}
	<div class="float-right">
		{if $prev_id}
			<a class="lowercase" href="{"`$url``$prev_id`"|fn_url}">&laquo;&nbsp;{$lang.previous}</a>&nbsp;&nbsp;&nbsp;
		{/if}

		{if $next_id}
			<a class="lowercase" href="{"`$url``$next_id`"|fn_url}">{$lang.next}&nbsp;&raquo;</a>
		{/if}
	</div>
{/capture}