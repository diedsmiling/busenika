{* $Id: quick_menu.tpl 10365 2010-08-05 14:18:33Z klerik $ *}

<script type="text/javascript">
//<![CDATA[
{literal}
function fn_quick_menu_content_switch_callback()
{
	var container = $('#quick_menu_content');
	var scroll_elm = $('.menu-container', container);
	var elm = $('#sw_quick_menu_content').get(0);
	var w = jQuery.get_window_sizes();
	var offset = container.offset();
	var max_height = offset.top - w.offset_y > w.view_height / 2 ? offset.top - w.offset_y - elm.offsetHeight: w.offset_y + w.view_height - offset.top;
	scroll_elm.css('height', '');
	if (container.get(0).offsetHeight > max_height) {
		var diff = container.get(0).offsetHeight - scroll_elm.get(0).offsetHeight;
		scroll_elm.css('height', max_height - diff - 10 + 'px');
	}
	if (offset.top + container.get(0).offsetHeight > w.offset_y + w.view_height) {
		container.css('top', elm.offsetTop - container.get(0).offsetHeight + 1);
		container.addClass('quick-menu-bottom');
	} else {
		container.css('top', elm.offsetTop + elm.offsetHeight - 1);
		container.removeClass('quick-menu-bottom');
	}
	if (offset.left - elm.offsetWidth <= w.offset_x) {
		container.css('left', 0);
	} else {
		container.css('left', elm.offsetLeft + elm.offsetWidth - container.get(0).offsetWidth);
	}
}
{/literal}
{if !$edit_quick_menu && !$expand_quick_menu}
{literal}
function fn_switch_quick_menu()
{
	$('head').append('<script type="text/javascript" src="' + current_path + '/js/quick_menu.js"></sc' + 'ript>');
}
{/literal}
{/if}
//]]>
</script>

<div class="quick-menu-container" {if $smarty.cookies.quick_menu_offset}style="{$smarty.cookies.quick_menu_offset}"{/if} id="quick_menu">
{if $settings.show_menu_mouseover == "Y"}
<script type="text/javascript">
//<![CDATA[
{literal}
var quick_menu_timer;
function fn_quick_menu_mouse_action(obj, over)
{
	if ($('#quick_menu').data('drag') != true) {
		if (over && !$('#quick_menu_content').is(':visible')) {
			$(obj).click();
		}
		if (!over && $('#quick_menu_content').is(':visible')) {
			if ($('#quick_menu_content').data('over_defined') != true) {
				$('#quick_menu_content').add($('#quick_menu_content').contents()).mouseover(function() {
					$('#quick_menu_content').data('over', true);
				});
				$('#quick_menu_content').mouseout(function() {
					$('#quick_menu_content').data('over', false);
					clearTimeout(quick_menu_timer);
					quick_menu_timer = setTimeout(function() {
						if ($('#quick_menu_content').data('over') != true) {
							$(obj).click();
						}
					}, 100);
				});
				$('#quick_menu_content').data('over_defined', true);
			}
			if ($('#quick_menu_content').data('over') != true) {
				setTimeout(function() {
					if ($('#quick_menu_content').data('over') != true) {
						$(obj).click();
					}
				}, 100);
			}
		}
	}
}
{/literal}
//]]>
</script>
{/if}
{if $show_quick_popup}
	<div id="quick_box" class="cm-popup-box hidden">
		<div class="cm-popup-content-header">
			<h3 class="float-left">{$lang.editing_quick_menu_section}</h3>
			<h3 class="float-left">{$lang.editing_quick_menu_link}</h3>
			<div class="select-lang float-right">
				{include file="common_templates/select_object.tpl" style="graphic" link_tpl="tools.get_quick_menu_variant"|fn_link_attach:"descr_sl=" items=$languages selected_id=$smarty.const.DESCR_SL key_name="name" suffix="quick_menu" display_icons=true}
			</div>
		</div>
		<div class="cm-popup-content-footer">
			<form class="cm-ajax" name="quick_menu_form" action="{""|fn_url}" method="post">
			<input id="qm_item_id" type="hidden" name="item[id]" value="" />
			<input id="qm_item_parent" type="hidden" name="item[parent_id]" value="0" />
			<input id="qm_descr_sl" type="hidden" name="descr_sl" value="" />
			<input type="hidden" name="result_ids" value="quick_menu" />

			<div class="form-field">
				<label class="cm-required" for="qm_item_name">{$lang.name}:</label>
				<input id="qm_item_name" name="item[name]" class="input-text-large main-input" type="text" value="" size="40"/>
			</div>
			
			<div class="form-field">
				<label class="cm-required" for="qm_item_link">{$lang.link}:</label>
				<input id="qm_item_link" name="item[url]" class="input-text-large" type="text" value="" size="40"/>
			</div>
			
			<div class="form-field">
				<label for="qm_item_position">{$lang.position}:</label>
				<input id="qm_item_position" name="item[position]" class="input-text-short" type="text" value="" size="6"/>
			</div>

			<div class="form-field">
				<a id="qm_current_link" class="underline-dashed">{$lang.use_current_link}</a>
			</div>

			<div class="buttons-container">
				{include file="buttons/save_cancel.tpl" but_name="dispatch[tools.update_quick_menu_item.edit]" cancel_action="close"}
			</div>

			</form>
		</div>
	</div>
{/if}
	<div class="cm-popup-content-header quick-menu">
		<a id="sw_quick_menu_content" class="cm-combo-{if $edit_quick_menu || $expand_quick_menu}off{else}on{/if} cm-combination"{if $settings.show_menu_mouseover == "Y"} onmouseover="fn_quick_menu_mouse_action(this, true);" onmouseout="fn_quick_menu_mouse_action(this, false);"{/if}>{$lang.quick_menu}</a>
	</div>
	
	<div id="quick_menu_content" class="quick-menu-content cm-popup-box cm-smart-position{if !$edit_quick_menu && !$expand_quick_menu} hidden{/if}">
		{if $edit_quick_menu}
		<div class="menu-container">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			{foreach from=$quick_menu key=sect_id item=sect}
				<tr item="{$sect_id}" parent_id="0" pos="{$sect.section.position}">
					<td class="nowrap section-header">
						<strong class="cm-qm-name">{$sect.section.name}</strong><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="" title="{$lang.remove_this_item}" class="hand valign cm-delete-section" />
					</td>
					<td class="right"><a class="edit cm-update-item">{$lang.edit}</a></td>
				</tr>
				{foreach from=$sect.subsection item=subsect}
				<tr item="{$subsect.menu_id}" parent_id="{$subsect.parent_id}" pos="{$subsect.position}">
					<td class="nowrap">
						<a class="cm-qm-name" href="{$subsect.url|fn_url}">{$subsect.name}</a><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="" title="{$lang.remove_this_item}" class="hand valign cm-delete-section" />
					</td>
					<td class="right"><a class="edit cm-update-item">{$lang.edit}</a></td>
				</tr>
				{/foreach}
				<tr item="{$sect_id}" parent_id="0" pos="{$sect.section.position}">
					<td colspan="2" class="cm-add-link"><a class="edit cm-add-link">{$lang.add_link}</a></td>
				</tr>
			{/foreach}
			</table>
		</div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr class="done" colspan="2">
				<td>
					<label for="show_menu_mouseover">{$lang.show_menu_on_mouse_over}:</label>
					<input id="show_menu_mouseover" type="checkbox" name="show_menu_mouseover" value="Y" {if $settings.show_menu_mouseover == "Y"}checked="checked"{/if} onclick="jQuery.ajaxRequest('{"tools.update_quick_menu_handler?enable="|fn_url:'A':'rel':'&'}' + (this.checked ? this.value : 'N'), {$ldelim}cache: false, result_ids: 'quick_menu', callback: fn_quick_menu_content_switch_callback{$rdelim});" />
				</td>
			</tr>
			<tr class="done">
				<td class="nowrap"><a class="edit cm-add-section">{$lang.add_section}</a></td>
				<td class="right">
					<a class="edit cm-ajax" rev="quick_menu" href="{"tools.show_quick_menu"|fn_url}" name="quick_menu_content_switch_callback">{$lang.done}</a></td>
			</tr>
		</table>
		{else}
		{if $quick_menu}
		<div class="menu-container">
			<ul>
			{foreach from=$quick_menu item=sect}
				<li><strong>{$sect.section.name}</strong></li>
				{foreach from=$sect.subsection item=subsect}
				<li><a href="{$subsect.url|fn_url}">{$subsect.name}</a></li>
				{/foreach}
			{/foreach}
			</ul>
		</div>
		{/if}
		<p class="right">
			<a {if !$expand_quick_menu}class="edit" onclick="fn_switch_quick_menu();"{else}class="edit cm-ajax" href="{"tools.show_quick_menu.edit"|fn_url}" rev="quick_menu" name="quick_menu_content_switch_callback"{/if}>{$lang.edit}</a>
		</p>
		{/if}
	</div>
<!--quick_menu--></div>