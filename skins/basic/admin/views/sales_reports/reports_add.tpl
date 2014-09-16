{* $Id: reports_add.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

<form action="{""|fn_url}" method="post" name="create_new" class="cm-form-highlight">

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="details_new" class="cm-js cm-active"><a>{$lang.general}</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content" id="content_details_new">
		<div class="form-field">
			<label for="description" class="cm-required">{$lang.name}</label>
			<input type="text" name="add_report[0][description]" id="description" value="" size="40" class="input-text" />
		</div>
		
		<div class="form-field">
			<label for="position">{$lang.position_short}</label>
			<input type="text" name="add_report[0][position]" id="position" value="" size="3" class="input-text-short" />
		</div>
		
		<div class="form-field">
			<label for="status">{$lang.status}</label>
			<div class="select-field">
				<input type="radio" name="add_report[0][status]" id="status_a" checked="checked" value="A" class="radio" />
				<label for="status_a">{$lang.active}</label>
		
				<input type="radio" name="add_report[0][status]" id="status_d" value="D" class="radio" />
				<label for="status_d">{$lang.disabled}</label>
			</div>
		</div>
	</div>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[sales_reports.reports_list.add]" cancel_action="close"}
</div>
</form>

		