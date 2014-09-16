{* $Id: visitors.tpl 9741 2010-06-08 07:56:56Z imac $ *}

<div id="visitors_list">

{if $no_sort != "Y"}
	{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{/if}

{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
	{assign var="ajax_class" value="cm-ajax"}
{/if}

<table id="visitors_log_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th width="20%">
		{if $no_sort != "Y"}<a rev="visitors_list" href="{"`$c_url`&amp;sort_by=date&amp;sort_order=`$search.sort_order`"|fn_url}" class="{$ajax_class} {if $search.sort_by == "date"}sort-link-{$search.sort_order}{/if}">{/if}{$lang.date}{if $no_sort != "Y"}</a>{/if}</th>
	<th class="center" width="15%">
		{$lang.pages}</th>
	<th width="15%">
		{if $no_sort != "Y"}<a rev="visitors_list" href="{"`$c_url`&amp;sort_by=ip&amp;sort_order=`$search.sort_order`"|fn_url}" class="{$ajax_class} {if $search.sort_by == "ip"}sort-link-{$search.sort_order}{/if} inline-block">{/if}{$lang.ip}{if $no_sort != "Y"}</a>{/if}/ {if $no_sort != "Y"}<a rev="visitors_list" href="{"`$c_url`&amp;sort_by=proxy&amp;sort_order=`$search.sort_order`"|fn_url}" class="{$ajax_class} {if $search.sort_by == "proxy"}sort-link-{$search.sort_order}{/if} inline-block">{/if}{$lang.proxy}{if $no_sort != "Y"}</a>{/if}</th>
	{if $smarty.request.client_type == "B"}
	<th width="20%">
		{if $no_sort != "Y"}<a rev="visitors_list" href="{"`$c_url`&amp;sort_by=robot&amp;sort_order=`$search.sort_order`"|fn_url}" class="{$ajax_class} {if $search.sort_by == "robot"}sort-link-{$search.sort_order}{/if}">{/if}{$lang.robot}{if $no_sort != "Y"}</a>{/if}</th>
	{else}
	<th width="10%">
		{if $no_sort != "Y"}<a rev="visitors_list" href="{"`$c_url`&amp;sort_by=os&amp;sort_order=`$search.sort_order`"|fn_url}" class="{$ajax_class} {if $search.sort_by == "os"}sort-link-{$search.sort_order}{/if}">{/if}{$lang.operating_system}{if $no_sort != "Y"}</a>{/if}</th>
	<th width="10%">
		{if $no_sort != "Y"}<a rev="visitors_list" href="{"`$c_url`&amp;sort_by=browser&amp;sort_order=`$search.sort_order`"|fn_url}" class="{$ajax_class} {if $search.sort_by == "browser"}sort-link-{$search.sort_order}{/if}">{/if}{$lang.browser}{if $no_sort != "Y"}</a>{/if}</th>
	{/if}
	<th width="20%">
		{if $no_sort != "Y"}<a rev="visitors_list" href="{"`$c_url`&amp;sort_by=country&amp;sort_order=`$search.sort_order`"|fn_url}" class="{$ajax_class} {if $search.sort_by == "country"}sort-link-{$search.sort_order}{/if}">{/if}{$lang.country}{if $no_sort != "Y"}</a>{/if}</th>
	{if $hide_extra_button != "Y"}
	<th class="center" width="5%">
		<img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combinations-visitors" /><img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combinations-visitors" /></th>
	{/if}
</tr>
{foreach from=$visitors_log item="visitor" name="visitors"}
<tbody class="hover">
<tr>
	<td class="nowrap">{$visitor.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td class="center nowrap">
		{assign var="return_current_url" value=$config.current_url|escape:url}
		{if $action != "pages"}<a href="{"statistics.visitor_pages?client_type=`$search.client_type`&amp;stat_sess_id=`$visitor.sess_id`&amp;return_url=`$return_current_url`"|fn_url}">{/if}{$visitor.requests_count|default:1}{if $action != "pages"}</a>{/if}</td>
	<td class="nowrap"><strong>{$visitor.host_ip}</strong> / {if $visitor.proxy_ip}{$visitor.proxy_ip}{else}-{/if}</td>
	{if $smarty.request.client_type == "B"}
	<td>{if $visitor.robot}{$visitor.robot}{else}{$lang.undefined}{/if}</td>
	{else}
	<td class="nowrap">
		{if $visitor.os}
			{if $visitor.os == "Windows"}
				<img src="{$images_dir}/os/os_windows.gif" width="16" height="16" border="0" alt="{$visitor.os}" title="{$visitor.os}" align="top" />
			{elseif $visitor.os == "Mac"}
				<img src="{$images_dir}/os/os_mac.gif" width="16" height="16" border="0" alt="{$visitor.os}" title="{$visitor.os}" align="top" />
			{elseif $visitor.os == "Linux"}
				<img src="{$images_dir}/os/os_linux.gif" width="16" height="16" border="0" alt="{$visitor.os}" title="{$visitor.os}" align="top" />
			{/if}
			{$visitor.os}
		{else}
			{$lang.undefined}
		{/if}
		</td>
	<td class="nowrap">
		{if $visitor.browser}
			{assign var="browser" value=$visitor.browser|lower|replace:"internet ":""}
			{if $browser == "explorer" || $browser == "firefox" || $browser == "mozilla" || $browser == "chrome" || $browser == "netscape" || $browser == "safari" || $browser == "opera"}
				<img src="{$images_dir}/browsers/browser_{$browser}.gif" width="16" height="16" border="0" alt="{$visitor.browser}" title="{$visitor.browser}" align="top" />
			{/if}
			{$visitor.browser} {$visitor.browser_version}
		{else}
			{$lang.undefined}
		{/if}</td>
	{/if}
	<td><img src="{$images_dir}/flags/{$visitor.country_code|default:"01"|lower}.png" width="16" height="16" border="0" alt="{$visitor.country}" title="{$visitor.country}" align="top" />&nbsp;{if $visitor.country}{$visitor.country}{elseif $visitor.country_code}{$visitor.country_code}{else}{$lang.undefined}{/if}</td>
	{if $hide_extra_button != "Y"}
	<td class="center nowrap">
		<img src="{$images_dir}/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_visitors_log_{$smarty.foreach.visitors.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand cm-combination-visitors" /><img src="{$images_dir}/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_visitors_log_{$smarty.foreach.visitors.iteration}" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" class="hand hidden cm-combination-visitors" /><a id="sw_visitors_log_{$smarty.foreach.visitors.iteration}" class="cm-combination-visitors">{$lang.extra}</a>
	</td>
	{/if}
</tr>
<tr id="visitors_log_{$smarty.foreach.visitors.iteration}" {if $hide_extra_button != "Y"}class="hidden"{/if}>
	<td colspan="{if $smarty.request.client_type == "B"}6{else}7{/if}">
	<div class="scroll-x">
		<div class="form-field">
			<label>{$lang.entry_page}:</label>
			{if $visitor.url}<a href="{$visitor.url}">{$visitor.url}</a>{else}{$lang.undefined}{/if}
			<p class="small-note">{$lang.page_title}:&nbsp;{if $visitor.title}{$visitor.title}{else}-{/if}</p>
		</div>

		<div class="form-field">
			<label>{$lang.current_page}:</label>
			{if $visitor.current_url}<a href="{$visitor.current_url}">{$visitor.current_url}</a>{else}-{/if}
			<p class="small-note">{$lang.page_title}:&nbsp;{if $visitor.current_title}{$visitor.current_title}{else}-{/if}</p>
		</div>

		<div class="form-field">
			<label>{$lang.referrer}:</label>
			{if $visitor.referrer}<a href="{$visitor.referrer}">{$visitor.referrer|chunk_split:118:' '}</a>{else}-{/if}
			{if $visitor.phrase}<p class="small-note">{$lang.phrase}:&nbsp;{$visitor.phrase|unescape}</p>{/if}
		</div>

		<div class="form-field">
			<label>{$lang.user_agent}:</label>
			{if $visitor.user_agent}{$visitor.user_agent}{else}-{/if}
		</div>
		
		<div class="form-field">
			<label>{$lang.language}:</label>
			{if $visitor.language}{$visitor.language}{elseif $visitor.client_language}{$visitor.client_language}{else}{$lang.undefined}{/if}
		</div>
		
		{if $smarty.request.client_type != "B"}
		<div class="form-field">
			<label>{$lang.screen}:</label>
			{$visitor.screen_x|default:0}x{$visitor.screen_y|default:0} ({$visitor.color|default:0})
		</div>
		{/if}
	</div>	
	</td>
</tr>
</tbody>
{foreachelse}
<tr class="no-items">
	<td colspan="7"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

<!--visitors_list--></div>
