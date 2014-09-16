{* $Id: manage.tpl 9655 2010-05-28 09:17:48Z lexa $ *}

{capture name="mainbox"}

{include file="addons/discussion/views/discussion_manager/components/discussion_search_form.tpl"}

{capture name="tabsbox"}

<div id="content_{$discussion_object_types.$object_type}">

<form action="{""|fn_url}" method="POST" name="update_posts_form_{$object_type|lower}">

{include file="common_templates/pagination.tpl" save_current_url=true div_id="pagination_contents_`$object_type`"}

{if $posts}
<div class="posts-container">
{foreach from=$posts item=post}
<div class="{cycle values="manage-row, "} posts">
{hook name="discussion_manager:items_list_row"}
	<div class="clear">
		<div class="valign float-left">
			<input type="text" name="posts[{$post.post_id}][name]" value="{$post.name}" size="40" class="input-text valign strong" /><span class="valign">&nbsp;|&nbsp;{$lang.ip_address}:&nbsp;{$post.ip_address}&nbsp;|&nbsp;<a href="{$post.object_data.url|fn_url}">{$post.object_data.description|truncate:70}</a></span>
		</div>
		{if $post.type == "R" || $post.type == "B"}
		<div class="float-right">

			<strong class="valign">{$lang.rating}:</strong>
			<select class="valign" name="posts[{$post.post_id}][rating_value]">
				<option value="5" {if $post.rating_value == "5"}selected="selected"{/if}>{$lang.excellent}</option>
				<option value="4" {if $post.rating_value == "4"}selected="selected"{/if}>{$lang.very_good}</option>
				<option value="3" {if $post.rating_value == "3"}selected="selected"{/if}>{$lang.average}</option>
				<option value="2" {if $post.rating_value == "2"}selected="selected"{/if}>{$lang.fair}</option>
				<option value="1" {if $post.rating_value == "1"}selected="selected"{/if}>{$lang.poor}</option>
			</select>

		</div>
		{/if}
	</div>

	{hook name="discussion:update_post"}
	{if $post.type == "C" || $post.type == "B"}
		<textarea name="posts[{$post.post_id}][message]" class="input-textarea-long" cols="80" rows="5">{$post.message}</textarea>
	{/if}
	{/hook}

<p>
	<span class="strong italic">{$post.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span>
	&nbsp;-&nbsp;
	{strip}	
	[&nbsp;&nbsp<span class="select-field"><input type="checkbox" name="delete_posts[{$post.post_id}]" id="delete_checkbox_{$post.post_id}"  class="checkbox cm-item-{$object_type|lower}" value="Y" /><label for="delete_checkbox_{$post.post_id}">{$lang.delete}</label></span>|&nbsp;&nbsp;
	<span class="select-field">
		<input type="hidden" name="posts[{$post.post_id}][status]" value="{$post.status}" />
		<input type="checkbox" class="checkbox" name="posts[{$post.post_id}][status]" id="dis_approve_post_{$post.post_id}" value="{if $post.status == "A"}D{else}A{/if}" />
		<label for="dis_approve_post_{$post.post_id}">{if $post.status == "A"}{$lang.disapprove}{else}{$lang.approve}{/if}</label>
	</span>]&nbsp;-&nbsp;{if $post.status == "A"}<span class="approved-text">{$lang.approved}{else}<span class="not-approved-text">{$lang.not_approved}{/if}</span>
	{/strip}
</p>
{/hook}
</div>
{/foreach}
</div>
{else}
<p class="no-items">{$lang.no_data}</p>
{/if}

{include file="common_templates/pagination.tpl" div_id="pagination_contents_`$object_type`"}

{if $object_notice}
<p align="right">
{eval var=$object_notice|unescape}
</p>
{/if}

{if $posts}
<div class="buttons-container buttons-bg">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[discussion.delete_posts]" class="cm-process-items-{$object_type|lower} cm-confirm" rev="update_posts_form_{$object_type|lower}">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[discussion.update_posts]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
</div>
{/if}

</form>

<!--content_{$discussion_object_types.$object_type}--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$discussion_object_types.$object_type}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.discussion content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra}
