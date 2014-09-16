{* $Id: update_question.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

<div id="content_group{$question_data.item_id}">

<form action="{""|fn_url}" method="post" name="question_form{$question_data.item_id}">
<input type="hidden" name="item_id" value="{$question_data.item_id}" />
<input type="hidden" name="page_id" value="{$question_data.page_id|default:$smarty.request.page_id}" />
<input type="hidden" name="selected_section" value="poll_questions" />

<div class="object-container">

<div class="tabs cm-j-tabs">
	<ul>
		<li id="details_{$question_data.item_id}" class="cm-js cm-active"><a>{$lang.general}</a></li>
		{if !$question_data || $question_data.type != "T"}
		<li id="conf_{$question_data.item_id}" class="cm-js"><a>{$lang.answers}</a></li>
		{/if}
	</ul>
</div>

<div class="cm-tabs-content">
	<div id="content_details_{$question_data.item_id}">

		<div class="form-field">
			<label for="descr_{$question_data.item_id}" class="cm-required">{$lang.question_text}:</label>
			<input type="text" name="question_data[description]" id="descr_{$question_data.item_id}" value="{$question_data.description}" class="input-text-large main-input" />
		</div>

		<div class="form-field">
			<label for="pos_{$question_data.item_id}">{$lang.position}:</label>
			<input type="text" name="question_data[position]" id="pos_{$question_data.item_id}" value="{$question_data.position}" class="input-text-short" />
		</div>

		<div class="form-field">
			<label for="type_{$question_data.item_id}" class="cm-required">{$lang.type}:</label>
			<select name="question_data[type]" id="type_{$question_data.item_id}" {if !$question_data}onchange="$('#conf_').toggleBy(this.value == 'T');"{/if}>
					{if !$question_data || $question_data.type == "Q" || $question_data.type == "M"}
					<option value="Q" {if $question_data.type == "Q"}selected="selected"{/if}>{$lang.select_single_type}</option>
					<option value="M" {if $question_data.type == "M"}selected="selected"{/if}>{$lang.select_one_or_more_type}</option>
					{/if}
					{if !$question_data || $question_data.type == "T"}
					<option value="T" {if $question_data.type == "T"}selected="selected"{/if}>{$lang.text_answer_type}</option>
					{/if}
			</select>			
		</div>

		<div class="form-field">
			<label for="req_{$question_data.item_id}">{$lang.required}:</label>
			<input type="hidden" name="question_data[required]" value="N" />
			<input type="checkbox" name="question_data[required]" id="req_{$question_data.item_id}" value="Y" {if $question_data.required == "Y"}checked="checked"{/if} class="checkbox" />
		</div>

	</div>

	{if !$question_data || $question_data.type != "T"}
	<div class="hidden" id="content_conf_{$question_data.item_id}">

		<table cellpadding="0" cellspacing="0" border="0" class="table">
		<tr>
			<th>{$lang.position_short}</th>
			<th width="80%">{$lang.answer_text}</th>
			<th>{$lang.text_box}</th>
			<th>&nbsp;</th>
		</tr>
		{foreach from=$question_data.answers item="answer"}
		<tr {cycle values="class=\"table-row\","} id="box_answer_{$answer.item_id}">
			<td>
				<input type="text" name="question_data[answers][{$answer.item_id}][position]" size="3" value="{$answer.position}" class="input-text" /></td>
			<td>
				<input type="text" name="question_data[answers][{$answer.item_id}][description]" size="75" value="{$answer.description}" class="input-text-100" /></td>
			<td class="center">
				<input type="hidden" name="question_data[answers][{$answer.item_id}][type]" value="A" />
				<input type="checkbox" name="question_data[answers][{$answer.item_id}][type]" value="O" class="checkbox"{if $answer.type == "O"} checked="checked"{/if} /></td>
			<td>
				{include file="buttons/multiple_buttons.tpl" item_id="answer_`$answer.item_id`" only_delete="Y"}</td>
		</tr>
		{/foreach}
		<tr id="box_new_answer_{$question_data.item_id}">
			<td>
				<input type="text" name="question_data[new_answers][0][position]" size="3" value="" class="input-text" /></td>
			<td>
				<input type="text" name="question_data[new_answers][0][description]" size="75" value="" class="input-text-100" /></td>
			<td class="center">
				<input type="hidden" name="question_data[new_answers][0][type]" value="A" />
				<input type="checkbox" name="question_data[new_answers][0][type]" value="O" class="checkbox"{if $answer.type == "O"} checked="checked"{/if} /></td>
			<td>
				{include file="buttons/multiple_buttons.tpl" item_id="new_answer_`$question_data.item_id`"}</td>
		</tr>
		</table>

	</div>
	{/if}

</div>

</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[pages.update_question]" cancel_action="close"}
</div>

</form>
<!--content_group{$question_data.item_id}--></div>