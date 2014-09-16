{* $Id: top.override.tpl 9820 2010-06-21 11:31:45Z 2tl $ *}

		{if !"COMPANY_ID"|defined}
		<div>
			<a href="{"statistics.visitors?report=online&amp;section=general"|fn_url}" class="underlined">{$lang.users_online}:&nbsp;<strong>{$users_online|default:0}</strong></a>
		</div>
		{/if}

