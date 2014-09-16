{* $Id: select_popup.tpl 10582 2010-09-02 13:02:22Z 2tl $ *}

{assign var="prefix" value=$prefix|default:"select"}
<div class="select-popup-container">
	{if !$hide_for_vendor}
	<div {if $id}id="sw_{$prefix}_{$id}_wrap"{/if} class="selected-status status-{if $suffix}{$suffix}-{/if}{$status|lower}{if $id} cm-combo-on cm-combination{/if}">
		<a {if $id}class="cm-combo-on{if !$popup_disabled} cm-combination{/if}"{/if}>
	{/if}
		{if $items_status}
			{if !$items_status|is_array}
				{assign var="items_status" value=$items_status|yaml_unserialize}
			{/if}
			{$items_status.$status}
		{else}
			{if $status == "A"}
				{$lang.active}
			{elseif $status == "D"}
				{$lang.disabled}
			{elseif $status == "H"}
				{$lang.hidden}
			{elseif $status == "P"}
				{$lang.pending}
			{/if}
		{/if}
	{if !$hide_for_vendor}
		</a>
	</div>
	{/if}
	{if $id && !$hide_for_vendor}
		{assign var="_update_controller" value=$update_controller|default:"tools"}
		{if $table && $object_id_name}{capture name="_extra"}&amp;table={$table}&amp;id_name={$object_id_name}{/capture}{/if}
		<div id="{$prefix}_{$id}_wrap" class="popup-tools cm-popup-box hidden">
			<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="" class="close-icon no-margin cm-popup-switch" />
			<ul class="cm-select-list">
			{if $items_status}
				{foreach from=$items_status item="val" key="st"}
				<li><a class="status-link-{$st|lower} {if $status == $st}cm-active{else}cm-ajax{/if}"{if $status_rev} rev="{$status_rev}"{/if} href="{"`$_update_controller`.update_status?id=`$id`&amp;status=`$st``$smarty.capture._extra``$extra`"|fn_url}" onclick="return fn_check_object_status(this, '{$st|lower}');" name="update_object_status_callback">{$val}</a></li>
				{/foreach}
			{else}
				<li><a class="status-link-a {if $status == "A"}cm-active{else}cm-ajax{/if}"{if $status_rev} rev="{$status_rev}"{/if} href="{"`$_update_controller`.update_status?id=`$id`&amp;table=`$table`&amp;id_name=`$object_id_name`&amp;status=A"|fn_url}" onclick="return fn_check_object_status(this, 'a');" name="update_object_status_callback">{$lang.active}</a></li>
				<li><a class="status-link-d {if $status == "D"}cm-active{else}cm-ajax{/if}"{if $status_rev} rev="{$status_rev}"{/if} href="{"`$_update_controller`.update_status?id=`$id`&amp;table=`$table`&amp;id_name=`$object_id_name`&amp;status=D"|fn_url}" onclick="return fn_check_object_status(this, 'd');" name="update_object_status_callback">{$lang.disabled}</a></li>
				{if $hidden}
				<li><a class="status-link-h {if $status == "H"}cm-active{else}cm-ajax{/if}"{if $status_rev} rev="{$status_rev}"{/if} href="{"`$_update_controller`.update_status?id=`$id`&amp;table=`$table`&amp;id_name=`$object_id_name`&amp;status=H"|fn_url}" onclick="return fn_check_object_status(this, 'h');" name="update_object_status_callback">{$lang.hidden}</a></li>
				{/if}
			{/if}
			{if $notify}
				<li class="select-field">
					<input type="checkbox" name="__notify_user" id="{$prefix}_{$id}_notify" value="Y" class="checkbox" onclick="$('input[name=__notify_user]').attr('checked', this.checked);" />
					<label for="{$prefix}_{$id}_notify">{$notify_text|default:$lang.notify_customer}</label>
				</li>
			{/if}
			{if $notify_department}
				<li class="select-field notify-department">
					<input type="checkbox" name="__notify_department" id="{$prefix}_{$id}_notify_department" value="Y" class="checkbox" onclick="$('input[name=__notify_department]').attr('checked', this.checked);" />
					<label for="{$prefix}_{$id}_notify_department">{$lang.notify_orders_department}</label>
				</li>
			{/if}
			{if $notify_supplier && $settings.Suppliers.enable_suppliers == "Y"}
				<li class="select-field notify-department">
					<input type="checkbox" name="__notify_supplier" id="{$prefix}_{$id}_notify_supplier" value="Y" class="checkbox" onclick="$('input[name=__notify_supplier]').attr('checked', this.checked);" />
					<label for="{$prefix}_{$id}_notify_supplier">{$lang.notify_supplier}</label>
				</li>
			{/if}
			</ul>
		</div>
		{if !$smarty.capture.avail_box}
		<script type="text/javascript">
		//<![CDATA[
		{literal}
		function fn_check_object_status(obj, status) 
		{
			if ($(obj).hasClass('cm-active')) {
				$(obj).removeClass('cm-ajax');
				return false;
			}
			fn_update_object_status(obj, status);
			return true;
		}
		function fn_update_object_status_callback(data, params) 
		{
			if (data.return_status && params.preload_obj) {
				fn_update_object_status(params.preload_obj, data.return_status.toLowerCase());
			}
		}
		function fn_update_object_status(obj, status)
		{
			var upd_elm_id = $(obj).parents('.cm-popup-box:first').attr('id');
			var upd_elm = $('#' + upd_elm_id);
			upd_elm.hide();
			$(obj).attr('href', fn_query_remove($(obj).attr('href'), ['notify_user', 'notify_department']));
			if ($('input[name=__notify_user]:checked', upd_elm).length) {
				$(obj).attr('href', $(obj).attr('href') + '&notify_user=Y');
			}
			if ($('input[name=__notify_department]:checked', upd_elm).length) {
				$(obj).attr('href', $(obj).attr('href') + '&notify_department=Y');
			}
			if ($('input[name=__notify_supplier]:checked', upd_elm).length) {
				$(obj).attr('href', $(obj).attr('href') + '&notify_supplier=Y');
			}
			$('.cm-select-list li a', upd_elm).removeClass('cm-active').addClass('cm-ajax');
			$('.status-link-' + status, upd_elm).addClass('cm-active');
			$('#sw_' + upd_elm_id + ' a').text($('.status-link-' + status, upd_elm).text());
			{/literal}
			$('#sw_' + upd_elm_id).removeAttr('class').addClass('selected-status status-{if $suffix}{$suffix}-{/if}' + status + ' ' + $('#sw_' + upd_elm_id + ' a').attr('class'));
			{literal}
		}
		{/literal}
		//]]>
		</script>
		{capture name="avail_box"}Y{/capture}
		{/if}
	{/if}
</div>

