{* $Id: manage.tpl 9820 2010-06-21 11:31:45Z 2tl $ *}

{script src="js/picker.js"}
{script src="js/tabs.js"}

{literal}
<script type="text/javascript">
//<![CDATA[
function fn_check_product_filter_type(value, tab_id, id)
{
	$('#' + tab_id).toggleBy(!(value.indexOf('R') == 0) && !(value.indexOf('D') == 0));
	$('[id^=inputs_ranges' + id + ']').toggleBy((value.indexOf('D') == 0));
	$('[id^=dates_ranges' + id + ']').toggleBy(!(value.indexOf('D') == 0));
}
//]]>
</script>
{/literal}

{capture name="mainbox"}

{include file="views/product_filters/components/product_filters_search_form.tpl" dispatch="product_filters.manage"}

{include file="common_templates/pagination.tpl"}

{assign var="r_url" value=$config.current_url|escape:url}

<div class="items-container" id="manage_filters_list">
{foreach from=$filters item="filter"}

	{include file="common_templates/object_group.tpl" id=$filter.filter_id text=$filter.filter details=$filter.filter_description|unescape href="product_filters.update?filter_id=`$filter.filter_id`&amp;return_url=$r_url" href_delete="product_filters.delete?filter_id=`$filter.filter_id`" rev_delete="pagination_contents" header_text="`$lang.editing_filter`:&nbsp;`$filter.filter`" table="product_filters" object_id_name="filter_id" status=$filter.status}
	
{foreachelse}

	<p class="no-items">{$lang.no_data}</p>

{/foreach}
<!--manage_filters_list--></div>

{include file="common_templates/pagination.tpl"}

{if !"COMPANY_ID"|defined}
<div class="buttons-container">
	{include file="common_templates/popupbox.tpl" id="add_product_filter" text=$lang.new_filter link_text=$lang.add_filter act="general"}
</div>
{/if}

{capture name="tools"}
	{capture name="add_new_picker"}
		{include file="views/product_filters/update.tpl" mode="add" filter=""}
	{/capture}
	{if !"COMPANY_ID"|defined}
	{include file="common_templates/popupbox.tpl" id="add_product_filter" text=$lang.new_filter content=$smarty.capture.add_new_picker link_text=$lang.add_filter act="general"}
	{/if}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.product_filters content=$smarty.capture.mainbox tools=$smarty.capture.tools title_extra=$smarty.capture.title_extra select_languages=true}