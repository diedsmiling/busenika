{* $Id: breadcrumbs.tpl 10374 2010-08-06 11:16:46Z alexions $ *}

{if $breadcrumbs && $breadcrumbs|@sizeof > 1}
	<div class="breadcrumbs">
		{strip}
			{foreach from=$breadcrumbs item="bc" name="bcn" key="key"}
				{if $key != "0"}
					<img src="{$images_dir}/icons/breadcrumbs_arrow.gif" class="bc-arrow" border="0" alt="&gt;" />
				{/if}
				{if $bc.link}
					<a href="{$bc.link|fn_url}"{if $additional_class} class="{$additional_class}"{/if}>{$bc.title|unescape|strip_tags}</a>
				{else}
					{$bc.title|unescape|strip_tags}
				{/if}
			{/foreach}
		{/strip}
	</div>
	

{/if}
