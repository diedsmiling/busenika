{* $Id: statistics.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{include file="common_templates/subheader.tpl" title=$lang.summary}

<table cellpadding="2" cellspacing="1">
<tr>
	<td width="300">{$lang.polls_total_submited}:</td>
	<td>
		{if $page_data.poll.summary.total}
			{include file="common_templates/popupbox.tpl" id="poll_statistics_votes_total" href="pages.poll_reports?page_id=`$page_data.page_id`&amp;report=votes" link_text=$page_data.poll.summary.total text=$lang.polls_total_submited act="edit"}	
		{else}-{/if}
	</td>
</tr>
<tr>
	<td width="300">{$lang.polls_total_completed}:</td>
	<td>
		{if $page_data.poll.summary.completed}
			{include file="common_templates/popupbox.tpl" id="poll_statistics_votes_completed" href="pages.poll_reports?page_id=`$page_data.page_id`&amp;report=votes&completed=Y" link_text=$page_data.poll.summary.completed text=$lang.polls_total_completed act="edit"}
		{else}-{/if}
	</td>
</tr>
<tr>
	<td width="300">{$lang.polls_first_submited}:</td>
	<td>{if $page_data.poll.summary.first}{$page_data.poll.summary.first|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}{else}-{/if}</td>
</tr>
<tr>
	<td width="300">{$lang.polls_last_submited}:</td>
	<td>{if $page_data.poll.summary.last}{$page_data.poll.summary.last|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}{else}-{/if}</td>
</tr>
</table>

{include file="common_templates/subheader.tpl" title=$lang.statistics_by_questions}

{if $page_data.poll.questions}
	{foreach from=$page_data.poll.questions item=question}
		<div class="form-field">
		<label>{$question.description}:</label>

		{if $question.type == "T"}
			<table cellpadding="2" cellspacing="1">
			<tr>
				<td width="200">{$lang.polls_answers_with_comments}</td>
				<td>{include file="addons/polls/views/pages/components/graph_bar.tpl" value_width=$question.results.ratio bar_width="300px"}</td>
				<td>
					{if $question.results.count}{include file="common_templates/popupbox.tpl" id="poll_statistics_votes_q_`$question.item_id`" href="pages.poll_reports?page_id=`$page_data.page_id`&amp;report=votes&item_id=`$question.item_id`&amp;answer_id=0" link_text=$question.results.count text="`$lang.votes` - `$question.description`" act="edit"}{else}0{/if} ({$question.results.ratio|default:"0.00"}%)</td>
			</tr>
			</table>

			{if $question.has_comments}
			<div>
				{include file="common_templates/popupbox.tpl" id="poll_statistics_comments_q_`$question.item_id`" href="pages.poll_reports?page_id=`$page_data.page_id`&amp;report=answers&amp;item_id=`$question.item_id`" link_text=$lang.view_answers text="`$lang.comments` - `$question.description`" act="edit"}
			</div>
			{/if}

		{else}
			{foreach from=$question.answers item="answer"}
				<table cellpadding="2" cellspacing="1">
				<tr>
					<td width="200">{$answer.description}</td>
					<td>{include file="addons/polls/views/pages/components/graph_bar.tpl" value_width=$answer.results.ratio bar_width="300px"}</td>
					<td>
						{if $answer.results.count}{include file="common_templates/popupbox.tpl" id="poll_statistics_votes_a_`$answer.item_id`" href="pages.poll_reports?page_id=`$page_data.page_id`&amp;report=votes&amp;item_id=`$question.item_id`&amp;answer_id=`$answer.item_id`" link_text=$answer.results.count text="`$lang.polls_votes` - `$answer.description`" act="edit"}{else}0{/if} ({$answer.results.ratio|default:"0.00"}%)</td>
				</tr>
				</table>

				{if $answer.has_comments}
					<div>
						{include file="common_templates/popupbox.tpl" id="poll_statistics_comments_a_`$answer.item_id`" href="pages.poll_reports?page_id=`$page_data.page_id`&amp;report=answers&amp;item_id=`$question.item_id`&amp;answer_id=`$answer.item_id`" link_text=$lang.view_answers text="`$lang.comments` - `$answer.description`" act="edit"}
					</div>
				{/if}

			{/foreach}
		{/if}
		</div>
	{/foreach}
{/if}
