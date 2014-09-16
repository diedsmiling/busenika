{* $Id: extra.post.tpl 9820 2010-06-21 11:31:45Z 2tl $ *}
{if !"COMPANY_ID"|defined}
<div class="statistics-box communication">
	{include file="common_templates/subheader_statistic.tpl" title=$lang.latest_reviews}
	
	<div class="statistics-body">
	{if $latest_posts}
	<div id="stats_discussion">
	{foreach from=$latest_posts item=post}
	{assign var="o_type" value=$post.object_type}
	{assign var="object_name" value=$discussion_objects.$o_type}
	{assign var="review_name" value="discussion_title_$object_name"}
	<div class="{cycle values=" ,manage-post"} posts">
		<div class="clear">
			{if $post.type == "R" || $post.type == "B"}
				<div class="float-left">
					{include file="addons/discussion/views/discussion_manager/components/stars.tpl" stars=$post.rating}
				</div>
			{/if}
			
			<div class="float-right">
			<a class="tool-link valign" href="{$post.object_data.url|fn_url}">{$lang.edit}</a>
			{include file="buttons/button.tpl" but_role="delete_item" but_href="index.delete_post?post_id=`$post.post_id`" but_meta="cm-ajax cm-confirm" but_rev="stats_discussion"}
			</div>
			
			{$lang.$object_name}:&nbsp;<a href="{$post.object_data.url|fn_url}">{$post.object_data.description|truncate:70}</a>
			<span class="lowercase">&nbsp;{$lang.comment_by}</span>&nbsp;{$post.name}
		</div>
	
		{if $post.type == "C" || $post.type == "B"}
			<div class="scroll-x">{$post.message}</div>
		{/if}
		
		<div class="clear">
		<div class="float-left"><strong>{$lang.ip_address}:</strong>&nbsp;{$post.ip_address}</div>
		{include file="addons/discussion/views/index/components/dashboard_status.tpl"}
		</div>
	</div>
	{/foreach}
	<!--stats_discussion--></div>
	{else}
	<p class="no-items">{$lang.no_items}</p>
	{/if}
	</div>
</div>
{/if}