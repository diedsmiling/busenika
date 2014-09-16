{* $Id: section.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<div class="clear">
	<div class="section-border">
		{$section_content}
		{if $section_state}
			<p align="right">
				<a href="{"`$index_script`?`$smarty.server.QUERY_STRING`&amp;close_section=`$key`"|fn_url}" class="underlined">{$lang.close}</a>
			</p>
		{/if}
	</div>
</div>