{* $Id: manage.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{script src="js/picker.js"}
{script src="js/tabs.js"}
{script src="js/profiles_scripts.js"}
<script type="text/javascript">
	//<![CDATA[
	{assign var="states" value=$smarty.const.CART_LANGUAGE|fn_get_all_states:false:true}
	var states = new Array();
	{if $states}
	{foreach from=$states item=country_states key=country_code}
	states['{$country_code}'] = new Array();
	{foreach from=$country_states item=state name="fs"}
	states['{$country_code}']['{$state.code|escape:quotes}'] = '{$state.state|escape:javascript}';
	{/foreach}
	{/foreach}
	{/if}
	//]]>
</script>
{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{capture name="mainbox"}

<div class="items-container" id="addons_list">
{foreach from=$addons_list item="a" key="key"}
	{if $a.status == "N"}
		{assign var="details" value=""}
		{assign var="status" value=""}
		{assign var="act" value="fake"}
		{assign var="non_editable" value=true}
		{capture name="links"}
		<a class="lowercase" href="{"addons.install?addon=`$key`"|fn_url}">{$lang.install}</a>
		{/capture}
	{else}
		{assign var="details" value=""}
		{assign var="status" value=$a.status}
		{assign var="link_text" value=""}
		{if $a.has_options}
			{assign var="act" value="edit"}
			{assign var="non_editable" value=false}
		{else}
			{assign var="act" value="fake"}
			{assign var="non_editable" value=true}
		{/if}
		{capture name="links"}
		<a class="cm-confirm lowercase" href="{"addons.uninstall?addon=`$a.addon`"|fn_url}">{$lang.uninstall}</a>
		{/capture}
	{/if}
	{include file="common_templates/object_group.tpl" id=$a.addon text=$a.name details=$a.description status_rev="header" update_controller="addons" href="addons.update?addon=`$a.addon`" href_delete="" rev_delete="addons_list" header_text="`$a.name`:&nbsp;<span class=\"lowercase\">`$lang.options`</span>" links=$smarty.capture.links non_editable=$non_editable}

{foreachelse}

	<p class="no-items">{$lang.no_items}</p>

{/foreach}
<!--addons_list--></div>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.addons content=$smarty.capture.mainbox}