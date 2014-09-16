{* $Id: index.tpl 10448 2010-08-18 10:20:38Z 2tl $ *}
{script src="js/picker.js"}
{capture name="mainbox"}

{hook name="index:index"}
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table-fixed">
<tr valign="top">
<td width="64%">

 <div class="statistics-box orders">
	{include file="common_templates/subheader_statistic.tpl" title=$lang.latest_orders}
	{assign var="order_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:true:true:true}
	<div class="statistics-body">
		{if $latest_orders}
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			{foreach from=$latest_orders item="order"}
			<tr valign="top">
				<td width="15%">
				{assign var="status_descr" value=$order.status}
				<span class="order-status order-{$order.status|lower}"><em>{$order_status_descr.$status_descr}</em></span>
				</td>
				<td width="85%">
				<a href="{"orders.details?order_id=`$order.order_id`"|fn_url}">{$lang.order}&nbsp;#{$order.order_id}</a> {$lang.by} {if $order.user_id}<a href="{"profiles.update?user_id=`$order.user_id`"|fn_url}">{/if}{$order.firstname} {$order.lastname}{if $order.user_id}</a>{/if} {$lang.for} <strong>{include file="common_templates/price.tpl" value=$order.total}</strong>
				<p class="not-approved-text">{$order.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</p>
				</td>
			</tr>
			{/foreach}
		</table>
		{else}
			<p class="no-items">{$lang.no_items}</p>
		{/if}
	</div>
</div>
<!--
<div class="statistics-box statistic">
	{include file="common_templates/subheader_statistic.tpl" title=$lang.orders_statistics}
	
	<div class="statistics-body">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th>{$lang.status}</th>
		<th class="center">{$lang.this_day}</th>
		<th class="center">{$lang.this_week}</th>
		<th class="center">{$lang.this_month}</th>
		<th class="center">{$lang.this_year}</th>
	</tr>
	{foreach from=$order_statuses item="status" key="_status"}
	<tr {cycle values="class=\"table-row\", "}>
		<td>{include file="common_templates/status.tpl" status=$_status display="view"}</td>
		<td class="center">{if $orders_stats.daily_orders.$_status.amount}<a href="{"orders.manage?status%5B%5D=`$_status`&amp;period=D"|fn_url}">{$orders_stats.daily_orders.$_status.amount}</a>{else}0{/if}</td>
		<td class="center">{if $orders_stats.weekly_orders.$_status.amount}<a href="{"orders.manage?status%5B%5D=`$_status`&amp;period=W"|fn_url}">{$orders_stats.weekly_orders.$_status.amount}</a>{else}0{/if}</td>
		<td class="center">{if $orders_stats.monthly_orders.$_status.amount}<a href="{"orders.manage?status%5B%5D=`$_status`&amp;period=M"|fn_url}">{$orders_stats.monthly_orders.$_status.amount}</a>{else}0{/if}</td>
		<td class="center">{if $orders_stats.year_orders.$_status.amount}<a href="{"orders.manage?status%5B%5D=`$_status`&amp;period=Y"|fn_url}">{$orders_stats.year_orders.$_status.amount}</a>{else}0{/if}</td>
	</tr>
	{/foreach}
	<tr {cycle values="class=\"table-row\", "}>
		<td><strong>{$lang.total_orders}</strong></td>
		<td class="center">{if $orders_stats.daily_orders.totals.amount}<a href="{"orders.manage?period=D"|fn_url}">{$orders_stats.daily_orders.totals.amount}</a>{else}0{/if}</td>
		<td class="center">{if $orders_stats.weekly_orders.totals.amount}<a href="{"orders.manage?period=W"|fn_url}">{$orders_stats.weekly_orders.totals.amount}</a>{else}0{/if}</td>
		<td class="center">{if $orders_stats.monthly_orders.totals.amount}<a href="{"orders.manage?period=M"|fn_url}">{$orders_stats.monthly_orders.totals.amount}</a>{else}0{/if}</td>
		<td class="center">{if $orders_stats.year_orders.totals.amount}<a href="{"orders.manage?period=Y"|fn_url}">{$orders_stats.year_orders.totals.amount}</a>{else}0{/if}</td>
	</tr>
	<tr class="strong">
		<td>{$lang.gross_total}</td>
		<td class="center">{include file="common_templates/price.tpl" value=$orders_stats.daily_orders.totals.total|default:"0"}</td>
		<td class="center">{include file="common_templates/price.tpl" value=$orders_stats.weekly_orders.totals.total|default:"0"}</td>
		<td class="center">{include file="common_templates/price.tpl" value=$orders_stats.monthly_orders.totals.total|default:"0"}</td>
		<td class="center">{include file="common_templates/price.tpl" value=$orders_stats.year_orders.totals.total|default:"0"}</td>
	</tr>
	<tr class="strong">
		<td>{$lang.totally_paid}</td>
		<td class="center valued-text">{include file="common_templates/price.tpl" value=$orders_stats.daily_orders.totals.total_paid|default:"0"}</td>
		<td class="center valued-text">{include file="common_templates/price.tpl" value=$orders_stats.weekly_orders.totals.total_paid|default:"0"}</td>
		<td class="center valued-text">{include file="common_templates/price.tpl" value=$orders_stats.monthly_orders.totals.total_paid|default:"0"}</td>
		<td class="center valued-text">{include file="common_templates/price.tpl" value=$orders_stats.year_orders.totals.total_paid|default:"0"}</td>
	</tr>

	</table>
	</div>
</div>
-->
{hook name="index:extra"}
{/hook}

</td>

<td class="spacer">&nbsp;</td>

<td width="34%">
<div class="statistics-box inventory">
	{include file="common_templates/subheader_statistic.tpl" title=$lang.inventory}
	
	<div class="statistics-body">
		<p class="strong">{$lang.category_inventory}:</p>
		<div class="clear">
			<ul class="float-left">
				<li>{$lang.total}:&nbsp;{if $category_stats.total}<strong>{$category_stats.total}</strong>{else}0{/if}</li>
				<li>{$lang.active}:&nbsp;{if $category_stats.status.A}<strong>{$category_stats.status.A}</strong>{else}0{/if}</li>
			</ul>
			<ul>
				<li>{$lang.hidden}:&nbsp;{if $category_stats.status.H}<strong>{$category_stats.status.H}</strong>{else}0{/if}</li>
				<li>{$lang.disabled}:&nbsp;{if $category_stats.status.D}<strong>{$category_stats.status.D}</strong>{else}0{/if}</li>
			</ul>
		</div>
		
		<p class="strong">{$lang.product_inventory}:</p>
		<div class="clear">
			<ul class="float-left">
				<li>{$lang.total}:&nbsp;{if $product_stats.total}<a href="{"products.manage"|fn_url}">{$product_stats.total}</a>{else}0{/if}</li>
				{hook name="index:inventory"}
				{/hook}
				<li>{$lang.in_stock}:&nbsp;{if $product_stats.in_stock}<a href="{"products.manage?amount_from=1&amp;amount_to=&amp;tracking[]=B&amp;tracking[]=O"|fn_url}">{$product_stats.in_stock}</a>{else}0{/if}</li>
				<li>{$lang.active}:&nbsp;{if $product_stats.status.A}<a href="{"products.manage?status=A"|fn_url}">{$product_stats.status.A}</a>{else}0{/if}</li>
				<li>{$lang.disabled}:&nbsp;{if $product_stats.status.D}<a href="{"products.manage?status=D"|fn_url}">{$product_stats.status.D}</a>{else}0{/if}</li>
			</ul>
			<ul>
				<li>{$lang.downloadable}:&nbsp;{if $product_stats.downloadable}<a href="{"products.manage?downloadable=Y"|fn_url}">{$product_stats.downloadable}</a>{else}0{/if}</li>
				<li>{$lang.text_out_of_stock}:&nbsp;{if $product_stats.out_of_stock}<a href="{"products.manage?amount_from=&amp;amount_to=0&amp;tracking[]=B&amp;tracking[]=O"|fn_url}">{$product_stats.out_of_stock}</a>{else}0{/if}</li>
				<li>{$lang.hidden}:&nbsp;{if $product_stats.status.H}<a href="{"products.manage?status=H"|fn_url}">{$product_stats.status.H}</a>{else}0{/if}</li>

				<li>{$lang.free_shipping}:&nbsp;{if $product_stats.free_shipping}<a href="{"products.manage&amp;type=extended&amp;match=any&amp;free_shipping=Y"|fn_url}">{$product_stats.free_shipping}</a>{else}0{/if}</li>
			</ul>
		</div>
	</div>
</div>

{if !"COMPANY_ID"|defined}
<div class="statistics-box users">
	{include file="common_templates/subheader_statistic.tpl" title=$lang.users}
	
	<div class="statistics-body clear">
	<ul>
		<li>
			<span><strong>{$lang.customers}:</strong></span>
			<em>{if $users_stats.total.C}<a href="{"profiles.manage?user_type=C"|fn_url}">{$users_stats.total.C}</a>{else}0{/if}</em>
		</li>

		{if $usergroups_type.C}
		<li>
			<span>{$lang.not_a_member}:</span>
			<em>{if $users_stats.not_members.C}<a href="{"profiles.manage?usergroup_id=0&amp;user_type=C"|fn_url}">{$users_stats.not_members.C}</a>{else}0{/if}</em>
		</li>
		{/if}
		{foreach from=$usergroups key="mem_id" item="mem_name"}
		{if $mem_name.type == "C"}
			<li>
				<span>{$mem_name.usergroup}:</span>
				<em>{if $users_stats.usergroup.C.$mem_id}<a href="{"profiles.manage?usergroup_id=`$mem_id`"|fn_url}">{$users_stats.usergroup.C.$mem_id}</a>{else}0{/if}</em>
			</li>
		{/if}
		{/foreach}
		<li>
			<span><strong>{$lang.staff}:</strong></span>
			<em>{if $users_stats.total.A}<a href="{"profiles.manage?user_type=A"|fn_url}">{$users_stats.total.A}</a>{else}0{/if}</em>
		</li>

		{if $usergroups_type.A}
		<li>
			<span>{$lang.root_administrators}:</span>
			<em>{if $users_stats.not_members.A}<a href="{"profiles.manage?usergroup_id=0&amp;user_type=A"|fn_url}">{$users_stats.not_members.A}</a>{else}0{/if}</em>
		</li>
		{/if}
		{foreach from=$usergroups key="mem_id" item="mem_name"}
		{if $mem_name.type == "A"}
			<li>
				<span>{$mem_name.usergroup}:</span>
				<em>{if $users_stats.usergroup.A.$mem_id}<a href="{"profiles.manage?usergroup_id=`$mem_id`"|fn_url}">{$users_stats.usergroup.A.$mem_id}</a>{else}0{/if}</em>
			</li>
		{/if}
		{/foreach}
		{hook name="index:users"}
		{/hook}
		
		<li><hr /></li>
		
		<li>
			<span><strong>{$lang.total}:</strong></span>
			<em>{if $users_stats.total_all}<a href="{"profiles.manage"|fn_url}">{$users_stats.total_all}</a>{else}0{/if}</em>
		</li>

		<li>
			<span>{$lang.disabled}:</span>
			<em>{if $users_stats.not_approved}<a href="{"profiles.manage?status=D"|fn_url}">{$users_stats.not_approved}</a>{else}0{/if}</em>
		</li>
	</ul>
	</div>
</div>
<div class="statistics-box">
	{include file="common_templates/subheader_statistic.tpl" title=$lang.shortcuts}
	
	<div class="statistics-body clear">
		<ul class="arrow-list float-left">
			<li><a href="{"products.manage"|fn_url}">{$lang.manage_products}</a></li>
			<li><a href="{"categories.manage"|fn_url}">{$lang.manage_categories}</a></li>
			<li><a href="{"shippings.manage"|fn_url}">{$lang.shipping_methods}</a></li>
			<li><a href="{"payments.manage"|fn_url}">{$lang.payment_methods}</a></li>
		</ul>

		<ul class="arrow-list float-left">
			<li><a href="{"settings.manage"|fn_url}">{$lang.general_settings}</a></li>
			<li><a href="{"database.manage"|fn_url}">{$lang.db_backup_restore}</a></li>
			<li><a href="{"pages.add&amp;parent_id=0"|fn_url}">{$lang.add_inf_page}</a></li>
			<li><a href="{"block_manager.manage"|fn_url}">{$lang.manage_blocks}</a></li>
		</ul>
	</div>
</div>
{/if}

</td>
</tr>
</table>
{/hook}

{capture name="tools"}
	{if $settings.General.feedback_type == 'manual' && !"COMPANY_ID"|defined}
		<div class="tools-container">
		{include file="common_templates/object_group.tpl" link_text="`$lang.send_feedback`&nbsp;&#155;&#155;" content=$smarty.capture.update_block id="feedback" no_table=true header_text=$lang.feedback_values but_name="dispatch[feedback.send]" href="feedback.prepare" opener_ajax_class="cm-ajax" link_class="cm-ajax-force" picker_meta="cm-clear-content" act='edit'}
		</div>
	{/if}
{/capture}
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.dashboard content=$smarty.capture.mainbox tools=$smarty.capture.tools}
