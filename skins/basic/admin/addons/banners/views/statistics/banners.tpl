{* $Id: banners.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

	{include file="addons/statistics/views/statistics/components/search_form.tpl" key=$action dispatch="statistics.banners" hide_advanced=true}

	{capture name="table_chart"}

	{include file="common_templates/pagination.tpl" div_id="banners_pagination_contents"}

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th>{$lang.banner}</th>
		<th>{$lang.clicks}</th>
		<th>{$lang.views}</th>
		<th>{$lang.conversion}</th>
		<th>&nbsp;</th>
	</tr>
	{foreach from=$banners item="banner"}
	<tr {cycle values="class=\"table-row\"," name="banner_top"}>
		<td>{$banner.banner}</td>
		<td>{$banners_statistics[$banner.banner_id].C.number|default:"0"}</td>
		<td>{$banners_statistics[$banner.banner_id].V.number|default:"0"}</td>
		<td>{$banners_statistics[$banner.banner_id].conversion|default:0}%</td>
		<td>{include file="common_templates/popupbox.tpl" id="banner_stats_`$banner.banner_id`" text=$lang.statistics link_text=$lang.details act="edit" href="statistics.banner_stats?banner_id=`$banner.banner_id`&amp;time_from=`$search.time_from`&amp;time_to=`$search.time_to`"}</td>
	</tr>
	{foreachelse}
	<tr class="no-items">
		<td colspan="5"><p>{$lang.no_data}</p></td>
	</tr>
	{/foreach}
	</table>

	{include file="common_templates/pagination.tpl" div_id="banners_pagination_contents"}

	{/capture}
	{include file="addons/statistics/views/statistics/components/select_charts.tpl" chart_table=$smarty.capture.table_chart chart_type=$chart_type applicable_charts=""}

{/capture}
{include file="common_templates/mainbox.tpl" title="`$lang.statistics`: `$lang.banners`" content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra select_languages=true}