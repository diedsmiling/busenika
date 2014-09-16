{* $Id: installed_upgrades.tpl 10602 2010-09-03 14:03:38Z 2tl $ *}

{capture name="mainbox"}
{assign var="return_current_url" value=$config.current_url|escape:url}
{foreach from=$packages item="package" name="fep" key="p"}
{assign var="_p" value=$p|escape:url}
	{include file="common_templates/subheader.tpl" title=$package.details.name}

	<p>
		<a class="tool-link cm-confirm" href="{"upgrade_center.revert?package=`$_p`&amp;redirect_url=`$return_current_url`"|fn_url}">{$lang.revert}</a>&nbsp;|&nbsp;<a class="tool-link cm-confirm" href="{"upgrade_center.remove?package=`$_p`"|fn_url}">{$lang.remove_backup_files}</a>
	</p>
	<p>
		{$lang.text_remove_backup_files}
	</p>

	<div class="order-info">
	{$lang.version}:&nbsp;<span>{$package.details.to_version}</span>,&nbsp;{$lang.release_date}:&nbsp;<span>{$package.details.timestamp|date_format:"`$settings.Appearance.date_format` `$settings.Appearance.time_format`"}</span>,&nbsp;{$lang.filesize}:&nbsp;<span>{$package.details.size|formatfilesize}</span>
	</div>
	
	<table width="100%">
	<tr>
		<td valign="top" width="50%">
			<p>{$lang.package_contents}</p>

			<div class="table scrollable">
				<h5>{$lang.file}</h5>
				<div class="uc-package-contents">
			{foreach from=$package.details.contents item="c"}
				<p title="{$c}">{$c|truncate:85:"&nbsp;...&nbsp;":true:true}</p>
			{/foreach}
				</div>
			</div>
		</td>
		<td valign="top" width="50%">
			<p>{$lang.text_uc_conflicts}</p>

			<div class="table scrollable">
				<h5>{$lang.file}</h5>
				<div class="uc-package-contents">
			{foreach from=$package.files key="c" item="s"}
				<p title="{$c}">
					<span class="float-left">{$c|truncate:60:"&nbsp;...&nbsp;":true:true}</span>
					{assign var="_c" value=$c|escape:url}
					<span class="float-right"><a class="tool-link" href="{"upgrade_center.diff?file=`$_c`&amp;package=`$_p`"|fn_url}">{$lang.changes}</a>&nbsp;&nbsp;&nbsp;{if $s == true}<span class="uc-ok">{$lang.resolved}</span>&nbsp;<a class="tool-link" href="{"upgrade_center.conflicts.unmark?file=`$_c`&amp;package=`$_p`"|fn_url}">{$lang.unmark}</a>{else}<a class="tool-link" href="{"upgrade_center.conflicts.mark?file=$_c&amp;package=`$_p`"|fn_url}">{$lang.mark}</a>{/if}</span>
				</p>
			{foreachelse}
				<p class="no-items">
				{$lang.text_no_conflicts}
				</p>
			{/foreach}
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p>&nbsp;</p>
			{$package.details.description|unescape}
		</td>
	</tr>

	</table>

{foreachelse}
	<p class="no-items">{$lang.no_data}</p>
{/foreach}


{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.installed_upgrades content=$smarty.capture.mainbox}
