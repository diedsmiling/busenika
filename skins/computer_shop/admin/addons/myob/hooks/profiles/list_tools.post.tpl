{* $Id: list_tools.post.tpl 10011 2010-07-08 07:44:26Z alexions $ *}
{* Profile exporter *}
{if $search.user_type == 'C'}
	<li><a class="cm-process-items" name="dispatch[myob_export.export_profiles]" rev="userlist_form">{$lang.export_to_myob}</a></li>
{/if}