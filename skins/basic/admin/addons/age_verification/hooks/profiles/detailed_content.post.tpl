{* $Id: detailed_content.post.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

{if $user_type == "C"}
<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.age_verification}
	<div class="form-field">
		<label for="birthday">{$lang.birthday}</label>
		{include file="common_templates/calendar.tpl" date_id="birthday" date_name="user_data[birthday]" date_val=$user_data.birthday start_year="1902" end_year="0"}
	</div>
</fieldset>
{/if}