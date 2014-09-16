{* $Id: bulk_allow_discussion.tpl 6773 2009-01-15 15:24:45Z zeke $ *}

{assign var="discussion" value=$object_id|fn_get_discussion:$object_type}
<select name="{$prefix}[{$object_id}][discussion_type]">
	<option {if $discussion.type == "B"}selected="selected"{/if} value="B">{$lang.communication} {$lang.and} {$lang.rating}</option>
	<option {if $discussion.type == "C"}selected="selected"{/if} value="C">{$lang.communication}</option>
	<option {if $discussion.type == "R"}selected="selected"{/if} value="R">{$lang.rating}</option>
	<option {if $discussion.type == "D" || !$discussion}selected="selected"{/if} value="D">{$lang.disabled}</option>
</select>
