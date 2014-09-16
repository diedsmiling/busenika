{* $Id: view.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{assign var="discussion" value=$object_id|fn_get_discussion:$object_type}

{if $discussion && $discussion.type != "D"}

<div id="content_discussion">
{if $wrap == true}
<p>&nbsp;</p>
{capture name="content"}
{include file="common_templates/subheader.tpl" title=$title}
{/if}

{assign var="posts" value=$discussion.thread_id|fn_get_discussion_posts:$smarty.request.page}

{if $posts}
{include file="common_templates/pagination.tpl" id="pagination_contents_comments_`$object_id`"}
{foreach from=$posts item=post}
<div class="posts{cycle values=", manage-post"}" id="post_{$post.post_id}">
{hook name="discussion:items_list_row"}
	<div class="clear">
		{if $discussion.type == "R" || $discussion.type == "B"}
		<div class="float-left">
			{include file="addons/discussion/views/discussion/components/stars.tpl" stars=$post.rating_value|fn_get_discussion_rating}
		</div>
		{/if}
		<div class="float-right">
			<em>{$post.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</em>
		</div>
	</div>
	
	{if $discussion.type == "C" || $discussion.type == "B"}<p class="post-message">"{$post.message|escape|nl2br}"</p>{/if}
	<p class="post-author">&ndash; {$post.name|escape}</p>
{/hook}
</div>
{/foreach
}
{if $object_type == "E" && $current_post_id}
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){$ldelim}
	if ($('#post_' + {$current_post_id}).length) {$ldelim}
		jQuery.scrollToElm($('#post_' + {$current_post_id}));
	{$rdelim}
{$rdelim});
//]]>
</script>
{/if}
{include file="common_templates/pagination.tpl" id="pagination_contents_comments_`$object_id`"}
{else}
<p class="no-items">{$lang.no_posts_found}</p>
{/if}

{if "CRB"|strpos:$discussion.type !== false}
{include file="common_templates/subheader.tpl" title=$lang.new_post}

<form action="{""|fn_url}" method="post" name="add_post_form">
<input type ="hidden" name="post_data[thread_id]" value="{$discussion.thread_id}" />
<input type ="hidden" name="redirect_url" value="{$config.current_url}" />
<input type="hidden" name="selected_section" value="" />

<div class="form-field">
	<label for="dsc_name" class="cm-required">{$lang.your_name}:</label>
	<input type="text" id="dsc_name" name="post_data[name]" value="{if $auth.user_id}{$user_info.firstname} {$user_info.lastname}{elseif $discussion.post_data.name}{$discussion.post_data.name}{/if}" size="50" class="input-text" />
</div>

{if $discussion.type == "R" || $discussion.type == "B"}
<div class="form-field">
	<label for="dsc_rating" class="cm-required">{$lang.your_rating}:</label>
	<select id="dsc_rating" name="post_data[rating_value]">
		<option value="5" selected="selected">{$lang.excellent}</option>
		<option value="4" {if $discussion.post_data.rating_value == "4"}selected="selected"{/if}>{$lang.very_good}</option>
		<option value="3" {if $discussion.post_data.rating_value == "3"}selected="selected"{/if}>{$lang.average}</option>
		<option value="2" {if $discussion.post_data.rating_value == "2"}selected="selected"{/if}>{$lang.fair}</option>
		<option value="1" {if $discussion.post_data.rating_value == "1"}selected="selected"{/if}>{$lang.poor}</option>
	</select>
</div>
{/if}

{hook name="discussion:add_post"}
{if $discussion.type == "C" || $discussion.type == "B"}
<div class="form-field">
	<label for="dsc_message" class="cm-required">{$lang.your_message}:</label>
	<textarea id="dsc_message" name="post_data[message]" class="input-textarea" rows="5" cols="72">{$discussion.post_data.message}</textarea>
</div>
{/if}
{/hook}

{if $settings.Image_verification.use_for_discussion == "Y"}
	{include file="common_templates/image_verification.tpl" id="discussion"}
{/if}

<div class="buttons-container">
	{include file="buttons/button.tpl" but_text=$lang.submit but_name="dispatch[discussion.add_post]"}
</div>

</form>

{/if}

{if $wrap == true}
	{/capture}
	{include file="common_templates/group.tpl" content=$smarty.capture.content}
{else}
	{capture name="mainbox_title"}{$title}{/capture}
{/if}
</div>
{/if}