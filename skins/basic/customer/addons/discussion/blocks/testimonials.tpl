{* $Id: testimonials.tpl 9655 2010-05-28 09:17:48Z lexa $ *}

{** block-description:discussion_title_home_page **}

{assign var="discussion" value=0|fn_get_discussion:"E"}

{if $discussion && $discussion.type != "D"}

{assign var="posts" value=$discussion.thread_id|fn_get_discussion_posts:0:$block.properties.limit:$block.properties.random}

{if $posts}
{foreach from=$posts item=post}

{if $discussion.type == "C" || $discussion.type == "B"}
	<p class="post-message"><a href="{"discussion.view?thread_id=`$discussion.thread_id`&amp;post_id=`$post.post_id`"|fn_url}">"{$post.message|truncate:100|nl2br}"</a></p>
{/if}

<p class="post-author">&ndash; {$post.name}{hook name="discussion:block_items_list_row"}{if $block.properties.positions != "left" && $block.properties.positions != "right"}, <em>{$post.timestamp|date_format:"`$settings.Appearance.date_format`"}</em>{/if}{/hook}</p>

{if $block.properties.positions != "left" && $block.properties.positions != "right"}
<div class="clear">
	<div class="right"></div>
	{if $discussion.type == "R" || $discussion.type == "B"}
		<div class="right">{include file="addons/discussion/views/discussion/components/stars.tpl" stars=$post.rating_value|fn_get_discussion_rating}</div>
	{/if}
</div>
{/if}

{/foreach}

<div class="right">
	<a href="{"discussion.view?thread_id=`$discussion.thread_id`"|fn_url}">{$lang.more_w_ellipsis}</a>
</div>
{/if}

{/if}