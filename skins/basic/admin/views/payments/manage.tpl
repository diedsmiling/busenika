{* $Id: manage.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{script src="js/tabs.js"}
{script src="js/picker.js"}

{capture name="mainbox"}

{include file="common_templates/sortable_position_scripts.tpl" sortable_table="payments" sortable_id_name="payment_id"}

<script type="text/javascript">
//<![CDATA[
var processor_descriptions = [];
{foreach from=$payment_processors item="p"}
processor_descriptions[{$p.processor_id}] = '{$p.description|escape:javascript}';
{/foreach}
{literal}
function fn_switch_processor(payment_id, processor_id)
{
	$('#tab_conf_' + payment_id).toggleBy(processor_id == 0);
	if (processor_id != 0) {
		{/literal}
		$('#tab_conf_' + payment_id + ' a').attr('href', '{"payments.processor?payment_id="|fn_url:'A':'rel':'&'}' + payment_id + '&processor_id=' + processor_id);
		{literal}
		$('#content_tab_conf_' + payment_id).remove();
		$('#elm_payment_tpl_' + payment_id).attr('disabled', 'disabled');
		if (processor_descriptions[processor_id]) {
			$('#elm_processor_description_' + payment_id).html(processor_descriptions[processor_id]).show();
		} else {
			$('#elm_processor_description_' + payment_id).hide();
		}
	} else {
		$('#elm_payment_tpl_' + payment_id).removeAttr('disabled');
		$('#elm_processor_description_' + payment_id).hide();
	}
}
{/literal}
//]]>
</script>

<div class="items-container cm-sortable" id="payments_list">
{foreach from=$payments item=payment name="pf"}

	{include file="common_templates/object_group.tpl" id=$payment.payment_id text=$payment.payment status=$payment.status href="payments.update?payment_id=`$payment.payment_id`" object_id_name="payment_id" table="payments" href_delete="payments.delete?payment_id=`$payment.payment_id`" rev_delete="payments_list"  header_text="`$lang.editing_payment`: `$payment.payment`" additional_class="cm-sortable-row cm-sortable-id-`$payment.payment_id`"}
	
{foreachelse}

	<p class="no-items">{$lang.no_data}</p>

{/foreach}
<!--payments_list--></div>

<div class="buttons-container">
	{capture name="tools"}
		{capture name="add_new_picker"}
			{include file="views/payments/update.tpl" mode="add" payment=""}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_payments" text=$lang.add_new_payments content=$smarty.capture.add_new_picker link_text=$lang.add_payment act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_payments" text=$lang.add_new_payments link_text=$lang.add_payment act="general"}
</div>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.payment_methods content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
