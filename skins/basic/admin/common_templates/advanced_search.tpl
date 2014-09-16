{* $Id: advanced_search.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{assign var="a_id" value=$dispatch|fn_crc32|string_format:"s_%s"}

<div class="advanced-search-button">
	<a id="sw_{$a_id}" class="cm-combination cm-save-state">
		{$lang.advanced_search_options}&nbsp;
		<img src="{$images_dir}/icons/section_collapsed.gif" width="7" height="9" border="0" alt="" id="on_{$a_id}" class="cm-combination cm-save-state {if $smarty.cookies.$a_id}hidden{/if}" />
		<img src="{$images_dir}/icons/section_expanded.gif" width="7" height="9" border="0" alt="" id="off_{$a_id}" class="cm-combination cm-save-state {if !$smarty.cookies.$a_id}hidden{/if}" />
	</a>
</div>

{assign var="views" value=$view_type|fn_get_views}

<div id="{$a_id}" class="search-advanced {if !$smarty.cookies.$a_id}hidden{/if}">
	{$content}

	<div class="buttons-container save-search">
		<div class="float-left">
			{include file="buttons/search.tpl" but_name="dispatch[$dispatch]" but_role="submit"}
		</div>
		{if $smarty.request.dispatch|strpos:".picker" === false}
		<div class="right">
			{$lang.save_this_search_as}:
			<input type="text" id="view_name" name="new_view" value="{if $search.view_id && $views[$search.view_id]}{$views[$search.view_id].name|escape}{else}{$lang.name}{/if}" class="input-save-name {if !$search.view_id && !$views[$search.view_id]}cm-hint{/if}" />
			<span class="submit-button">
				{include file="buttons/button.tpl" but_text=$lang.save but_onclick="fn_check_views('view_name', 'views')" but_role="button"}
			</span>
		</div>
		{/if}
	</div>

	{capture name="title_extra"}
	{assign var="items" value="10"}
	{split data=$views size=$items assign="splitted_views"}

	<div class="float-left">
		<div class="views">
			<a id="sw_views" class="cm-combo-on cm-combination">{if $search.view_id && $views[$search.view_id]}{$views[$search.view_id].name|escape}{else}{$lang.all}{/if}</a>
			<div id="views" class="list nowrap hidden cm-popup-box">
				<div class="list-content">
				{if $views|count <= 10}
					<ul>
				{/if}
					{foreach from=$splitted_views item=s_views name="splitted_views"}
						{if $views|count > 10}
							<div class="float-left">
								<ul>
						{/if}
							{if $smarty.foreach.splitted_views.first}
								<li onmouseover="this.className = 'item-hover'" onmouseout="this.className = ''">
									<a href="{"`$dispatch`.reset_view"|fn_url}">{$lang.all}</a>
								</li>
							{else}
								<li>&nbsp;</li>
							{/if}
							{foreach from=$s_views item=view}
							{if $view}
							<li onmouseover="this.className = 'item-hover'" onmouseout="this.className = ''">
								{assign var="return_current_url" value=$config.current_url|fn_query_remove:"view_id":"new_view"}
								<a class="cm-view-name" rev="{$view.view_id}" href="{"`$return_current_url`&amp;view_id=`$view.view_id`"|fn_url}">{$view.name|escape}</a>
								{assign var="redirect_current_url" value=$config.current_url|escape:url}
								<a href="{"`$dispatch`.delete_view?view_id=`$view.view_id`&amp;redirect_url=`$redirect_current_url`"|fn_url}" class="cm-confirm"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="{$lang.delete}" class="hand valign" /></a>
							</li>
							{elseif !$view && $views|count > 10}
							<li>&nbsp;</li>
							{/if}
							{/foreach}
						{if $views|count > 10}
								</ul>
							</div>
						{/if}
					{/foreach}
				{if $views|count <= 10}
						<li class="last right">
							{include file="buttons/button.tpl" but_text=$lang.new_saved_search but_role="text" but_onclick="fn_advanced_search_open('`$a_id`');" but_meta="text-button"}
						</li>
					</ul>
				{/if}
				{if $views|@count > 10}
				<p class="last right">
					{include file="buttons/button.tpl" but_text=$lang.new_saved_search but_role="text" but_onclick="fn_advanced_search_open('`$a_id`');" but_meta="text-button"}
				</p>
				{/if}
				</div>
			</div>
		</div>
	</div>
	{/capture}
</div>

<script type="text/javascript">
//<![CDATA[
lang.object_exists = '{$lang.object_exists}';
{literal}
function fn_advanced_search_open(id)
{
	var elm = $('#' + id);
	elm.show(); 
	jQuery.scrollToElm(elm);
	$('input[name=new_view]', elm).focus();
}
function fn_check_views(input_id, views_id)
{
	var match = true;
	var sbm_button = $(':submit:first', $('#' + input_id).parents('form:first'));
	$('.cm-view-name', $('#' + views_id)).each(function() {
		if ($(this).text().toLowerCase() == $('#' + input_id).val().toLowerCase()) {
			match = confirm(lang.object_exists);
			if (match) {
				$('<input type="hidden" name="update_view_id" value="' + $(this).attr('rev') + '" />').appendTo($('#' + input_id).parent());
			}
			return false;
		}
	});
	if (match) {
		sbm_button.attr('name', sbm_button.attr('name').substr(0, sbm_button.attr('name').length - 1) + '.save_view]');
		sbm_button.click();
	}
}
{/literal}
//]]>
</script>