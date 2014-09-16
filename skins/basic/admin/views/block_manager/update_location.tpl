{* $Id: update_location.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

<div id="content_tab_{$location}">
<form action="{""|fn_url}" method="post" class="cm-form-highlight" name="block_{$location}_update_form">
<input type="hidden" name="block[location]" value="{$location}" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li class="cm-js cm-active"><a>{$lang.general}</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content">
	<fieldset>
	{foreach from=$location_properties key="set_name" item="_option"}
		{include file="views/block_manager/components/setting_element.tpl" set_name=$set_name option=$_option block=$block set_id=$location}
	{/foreach}
	</fieldset>
	</div>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[block_manager.update_location]" cancel_action="close"}
</div>
</form>
<!--content_tab_{$location}--></div>
