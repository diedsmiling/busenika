{* $Id: period_selector.tpl 8364 2009-12-08 11:43:31Z alexions $ *}

<div class="nowrap">
<script type="text/javascript">
//<![CDATA[
function fn_change_calendar_dates(value, prefix)
{ldelim}
	var date_obj = new Date();
	var cal_date = new ccal();

	cal_date.month_first = {if $settings.Appearance.calendar_date_format}true{else}false{/if};

	var current_date = cal_date.get_date(date_obj);
	var previous_date = cal_date.get_date(date_obj);

	{literal}
	if (value == 'A') {
		$('#' + prefix + 'f_date').val('');
		$('#' + prefix + 't_date').val('');
		return true;
	} else if (value == 'D') {
		current_date.day = date_obj.getUTCDate();
	} else if (value == 'W') {
		current_date.day = date_obj.getUTCDate() - date_obj.getDay() + 1;
	} else if (value == 'M') {
		current_date.day = 1;
	} else if (value == 'Y') {
		current_date.year = date_obj.getFullYear();
		current_date.month = 0;
		current_date.day = 1;
	} else if (value == 'LD') {
		current_date.day = date_obj.getUTCDate() - 1;
		previous_date.day = date_obj.getUTCDate() - 1;
	} else if (value == 'HH') {
		current_date.day = date_obj.getUTCDate() - 1;
		previous_date.day = date_obj.getUTCDate();
	} else if (value == 'LW') {
		current_date.day = date_obj.getUTCDate() - (date_obj.getDay() + 6);
		previous_date.day = date_obj.getUTCDate() - date_obj.getDay();
	} else if (value == 'LM') {
		current_date.month = date_obj.getMonth() - 1;
		current_date.day = 1;
		var m_date = current_date.month < 0 ? current_date.month + 12 : current_date.month;
		var y_date = current_date.month < 0 ? current_date.year - 1 : current_date.year;
		previous_date.day = cal_date.get_days(m_date, y_date);
		previous_date.month = m_date;
		previous_date.year = y_date;
	} else if (value == 'LY') {
		current_date.year = date_obj.getFullYear() - 1;
		current_date.month = 0;
		current_date.day = 1;
		previous_date.year = current_date.year;
		previous_date.month = 11;
		previous_date.day = cal_date.get_days(previous_date.month, previous_date.year);
	} else if (value == 'HM') {
		current_date.day -= 30;
	} else if  (value == 'HW') {
		current_date.day -= 7;
	}

	if (current_date.day <= 0) {
		current_date.month -= 1;
		if (current_date.month < 0) {
			current_date.year -= 1;
			current_date.month += 12;
		}
		current_date.day += cal_date.get_days(current_date.month, current_date.year);
	}

	if (current_date.month < 0) {
		current_date.year -= 1;
		current_date.month += 12;
	}

	$('#' + prefix + 'f_date').val(cal_date.generate_date(current_date));
	$('#' + prefix + 't_date').val(cal_date.generate_date(previous_date));

	{/literal}
{rdelim}
//]]>
</script>

{if $display == "form"}
<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field">
	<label>{$lang.period}:</label>
	<div class="break">
{/if}
	<select name="{$prefix}period" id="{$prefix}period_selects" onchange="fn_change_calendar_dates(this.value, '{$prefix}')">
		<option value="A" {if $period == "A" || !$period}selected="selected"{/if}>{$lang.all}</option>
		<optgroup label="=============">
			<option value="D" {if $period == "D"}selected="selected"{/if}>{$lang.this_day}</option>
			<option value="W" {if $period == "W"}selected="selected"{/if}>{$lang.this_week}</option>
			<option value="M" {if $period == "M"}selected="selected"{/if}>{$lang.this_month}</option>
			<option value="Y" {if $period == "Y"}selected="selected"{/if}>{$lang.this_year}</option>
		</optgroup>
		<optgroup label="=============">
			<option value="LD" {if $period == "LD"}selected="selected"{/if}>{$lang.yesterday}</option>
			<option value="LW" {if $period == "LW"}selected="selected"{/if}>{$lang.previous_week}</option>
			<option value="LM" {if $period == "LM"}selected="selected"{/if}>{$lang.previous_month}</option>
			<option value="LY" {if $period == "LY"}selected="selected"{/if}>{$lang.previous_year}</option>
		</optgroup>
		<optgroup label="=============">
			<option value="HH" {if $period == "HH"}selected="selected"{/if}>{$lang.last_24hours}</option>
			<option value="HW" {if $period == "HW"}selected="selected"{/if}>{$lang.last_n_days|replace:"[N]":7}</option>
			<option value="HM" {if $period == "HM"}selected="selected"{/if}>{$lang.last_n_days|replace:"[N]":30}</option>
			{*<option value="HC" {if $period == "HC"}selected="selected"{/if}>{$lang.last_n_days|replace:"[N]":$var}</option>  implemented programatically only *}
		</optgroup>
		<optgroup label="=============">
			<option value="C" {if $period == "C"}selected="selected"{/if}>{$lang.custom}</option>
		</optgroup>
	</select>

{if $display == "form"}
	</div>
	</td>
	<td class="search-field">
{/if}

	{if $display != "form"}&nbsp;&nbsp;{/if}
	<label{if $display != "form"} class="label-html"{/if}>{$lang.select_dates}:</label>

{if $display == "form"}
	<div class="break nowrap">
{/if}

	{assign var="time_from" value="`$prefix`time_from"}
	{assign var="time_to" value="`$prefix`time_to"}
	
	{include file="common_templates/calendar.tpl" date_id="`$prefix`f_date" date_name="`$prefix`time_from" date_val=$search.$time_from  start_year=$settings.Company.company_start_year extra="onchange=\"$('#`$prefix`period_selects').val('C');\""}
	&nbsp;&nbsp;-&nbsp;&nbsp;
	{include file="common_templates/calendar.tpl" date_id="`$prefix`t_date" date_name="`$prefix`time_to" date_val=$search.$time_to  start_year=$settings.Company.company_start_year extra="onchange=\"$('#`$prefix`period_selects').val('C');\""}

{if $display == "form"}
	</div>
	</td>
	<td class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.search but_name=$but_name but_role="submit"}
	</td>
</tr>
</table>
{/if}

</div>
