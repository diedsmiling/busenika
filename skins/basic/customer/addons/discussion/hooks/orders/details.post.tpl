{* $Id: details.post.tpl 7192 2009-04-03 14:04:11Z zeke $ *}

{include file="addons/discussion/views/discussion/view.tpl" object_id=$order_info.order_id object_type="O" title=$lang.discussion_title_order}

{if $smarty.request.selected_section == "discussion"}
{literal}
<script type="text/javascript">
//<![CDATA[
	$(document).ready(function() {
		jQuery.scrollToElm($('#content_discussion'));
	});
//]]>
</script>
{/literal}
{/if}