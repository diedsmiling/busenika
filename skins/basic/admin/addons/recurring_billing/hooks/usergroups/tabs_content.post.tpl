{* $Id: tabs_content.post.tpl 9190 2010-03-30 11:08:39Z alexions $ *}

<div id="content_recurring_plan_{$id}">
	{include file="addons/recurring_billing/pickers/recurring_plans_picker.tpl" data_id="add_recurring_plans" input_name="usergroup_data[`$id`][recurring_plans_ids]" item_ids=$usergroup.recurring_plans_ids}
</div>