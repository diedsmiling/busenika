{* $Id: logs_search_form.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="section"}
<form action="{""|fn_url}" name="logs_form" method="get">
<input type="hidden" name="object" value="{$smarty.request.object}">

{include file="common_templates/period_selector.tpl" period=$search.period extra="" display="form" but_name="dispatch[logs.manage]"}

{capture name="advanced_search"}

<div class="search-field">
	<label>{$lang.user}:</label>
	<input type="text" name="q_user" size="30" value="{$search.q_user}" class="input-text" />
</div>

<div class="search-field">
	<label>{$lang.type}/{$lang.action}:</label>
	<select id="q_type" name="q_type" onchange="fn_logs_build_options();">
		<option value=""{if !$search.q_type} selected="selected"{/if}>{$lang.all}</option>
		{foreach from=$log_types item="o"}
			<option value="{$o.type}"{if $search.q_type == $o.type} selected="selected"{/if}>{$o.description}</option>
		{/foreach}
	</select>
	&nbsp;&nbsp;
	<select id="q_action" class="hidden" name="q_action">
	</select>
</div>

{/capture}

{include file="common_templates/advanced_search.tpl" content=$smarty.capture.advanced_search dispatch="logs.manage" view_type="logs"}

<script type="text/javascript">
//<![CDATA[
var types = new Array();
{foreach from=$log_types item="o"}
types['{$o.type}'] = new Array();
{foreach from=$o.actions item="v"}
types['{$o.type}']['{$v.action}'] = '{$v.description}';
{/foreach}
{/foreach}

lang.all = '{$lang.all|escape:"javascript"}';

{literal}
function fn_logs_build_options(current_action)
{
	var elm_t = $('#q_type');
	var elm_a = $('#q_action');

	elm_a.html('<option value="">' + lang.all + '</option>');

	for (var action in types[elm_t.val()]) {
		elm_a.append('<option value="' + action + '"' + (current_action && current_action == action ? ' selected="selected"' : '') + '>' + types[elm_t.val()][action] + '</option>');
	}

	$('#q_action').toggleBy(($('option', elm_a).length == 1));
}
{/literal}

fn_logs_build_options('{$search.q_action}');

//]]>
</script>

</form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}