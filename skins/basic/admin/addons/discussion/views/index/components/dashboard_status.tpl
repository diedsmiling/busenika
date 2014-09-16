{* $Id: dashboard_status.tpl 7321 2009-04-20 13:20:33Z angel $ *}

<div class="float-right nowrap right" id="post_{$post.post_id}">
	{include file="common_templates/select_popup.tpl" id=$post.post_id status=$post.status hidden="" object_id_name="post_id" table="discussion_posts" items_status="A: `$lang.approved`, D: `$lang.disapproved`"}
	<strong>{$post.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</strong>&nbsp;-&nbsp;
<!--post_{$post.post_id}--></div>
