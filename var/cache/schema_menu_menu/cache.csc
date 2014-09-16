a:2:{s:4:"data";s:9553:"<orders>
	<item title="view_orders" dispatch="orders.manage" alt="order_management"/>
	<item title="sales_reports" dispatch="sales_reports.reports" />
	<item title="order_statuses" dispatch="statuses.manage" extra="type=O" />
	<item title="shipments" dispatch="shipments.manage" active_option="settings.General.use_shipments" />
	<side>
		<item group="sales_reports.reports" title="manage_reports" href="%INDEX_SCRIPT?dispatch=sales_reports.reports_list" />
	</side>
</orders>

<catalog>
	<item title="categories" dispatch="categories.manage" />
	<item title="products" dispatch="products.manage" />
	<item title="product_features" dispatch="product_features.manage" />
	<item title="product_filters" dispatch="product_filters.manage" />
	<item title="global_options" dispatch="product_options.manage" />
	<item title="promotions" dispatch="promotions.manage" />

	<side>
		<item group="products.manage" title="global_update" href="%INDEX_SCRIPT?dispatch=products.global_update" />
		<item group="products.manage" title="bulk_product_addition" href="%INDEX_SCRIPT?dispatch=products.m_add" />
		
		<item group="categories.manage" title="bulk_category_addition" href="%INDEX_SCRIPT?dispatch=categories.m_add" />

		<item group="categories.update" title="add_subcategory" href="%INDEX_SCRIPT?dispatch=categories.add&amp;parent_id=%CATEGORY_ID" />
		<item group="categories.update" title="add_product" href="%INDEX_SCRIPT?dispatch=products.add&amp;category_id=%CATEGORY_ID" />
		<item group="categories.update" title="view_products" href="%INDEX_SCRIPT?dispatch=products.manage&amp;cid=%CATEGORY_ID" />
		<item group="categories.update" title="delete_this_category" href="%INDEX_SCRIPT?dispatch=categories.delete&amp;category_id=%CATEGORY_ID" meta="cm-confirm" />

		<item group="products.update" title="add_product" href="%INDEX_SCRIPT?dispatch=products.add" />
		<item group="products.update" title="clone_this_product" href="%INDEX_SCRIPT?dispatch=products.clone&amp;product_id=%PRODUCT_ID" />
		<item group="products.update" title="delete_this_product" href="%INDEX_SCRIPT?dispatch=products.delete&amp;product_id=%PRODUCT_ID" meta="cm-confirm" />
		
		<item group="product_options.global" title="apply_to_products" href="%INDEX_SCRIPT?dispatch=product_options.global.apply" />
			<item group="promotions.update" title="add_cart_promotion" href="%INDEX_SCRIPT?dispatch=promotions.add&amp;zone=cart" />
			<item group="promotions.update" title="add_catalog_promotion" href="%INDEX_SCRIPT?dispatch=promotions.add&amp;zone=catalog" />
	</side>
</catalog>

<users>
	<item title="users" links_group="users" dispatch="profiles.manage" />
	<item title="administrators" links_group="users" dispatch="profiles.manage" extra="user_type=A" />
	<item title="customers" links_group="users" dispatch="profiles.manage" extra="user_type=C" />
	<item title="profile_fields" dispatch="profile_fields.manage" />
	<item title="users_carts" dispatch="cart.cart_list" />
	<item title="usergroups" dispatch="usergroups.manage" />
	<side>
		<item group="usergroups.manage" title="user_group_requests" href="%INDEX_SCRIPT?dispatch=usergroups.requests" />
	</side>
</users>

<shippings_taxes>
	<item title="shipping_methods" dispatch="shippings.manage" />
	<item title="taxes" dispatch="taxes.manage" />
	<item title="states" dispatch="states.manage" />
	<item title="countries" dispatch="countries.manage" />
	<item title="locations" dispatch="destinations.manage" />
	<item title="localizations" dispatch="localizations.manage" />
	<side>
		<item group="shippings.manage" title="realtime_shippings" href="%INDEX_SCRIPT?dispatch=settings.manage&amp;section_id=Shippings" />
		<item group="shippings.update" title="add_shipping_method" href="%INDEX_SCRIPT?dispatch=shippings.add" />
		<item group="shippings.update" title="shipping_methods" href="%INDEX_SCRIPT?dispatch=shippings.manage" />
		<item group="shippings.update" title="realtime_shippings" href="%INDEX_SCRIPT?dispatch=settings.manage&amp;section_id=Shippings" />
		
		<item group="profiles.update" title="add_user" href="%INDEX_SCRIPT?dispatch=profiles.add" />
		
		<item group="taxes.update" title="add_tax" href="%INDEX_SCRIPT?dispatch=taxes.add" />
		
		<item group="destinations.update" title="add_location" href="%INDEX_SCRIPT?dispatch=destinations.add" />
			<item group="localizations.update" title="add_localization" href="%INDEX_SCRIPT?dispatch=localizations.add" />
		</side>
</shippings_taxes>

<administration>
	<item title="settings" dispatch="settings.manage" />
	<item title="addons" dispatch="addons.manage" />
	<item title="payment_methods" dispatch="payments.manage" />
	<item title="database" dispatch="database.manage" />
	<item title="credit_cards" dispatch="static_data.manage" extra="section=C" />
	<item title="titles" dispatch="static_data.manage" extra="section=T" />
	<item title="currencies" dispatch="currencies.manage" />
	<item title="import_data" dispatch="exim.import" />
	<item title="export_data" dispatch="exim.export" />
	<item title="revisions" dispatch="revisions.manage" active_option="settings.General.active_revisions_objects" />
	<item title="workflow" dispatch="revisions_workflow.manage" active_option="settings.General.active_revisions_objects" />
	<item title="logs" dispatch="logs.manage" />
	<item title="upgrade_center" dispatch="upgrade_center.manage" />
	<side>
			<item group="upgrade_center.manage" title="settings" href="%INDEX_SCRIPT?dispatch=settings.manage&amp;section_id=Upgrade_center" />
		<item group="upgrade_center.check" title="settings" href="%INDEX_SCRIPT?dispatch=settings.manage&amp;section_id=Upgrade_center" />
			<item group="database.manage" title="logs" href="%INDEX_SCRIPT?dispatch=logs.manage" />
		<item group="database.manage" title="phpinfo" href="%INDEX_SCRIPT?dispatch=tools.phpinfo" target="_blank" />

		<item group="logs.manage" title="db_backup_restore" href="%INDEX_SCRIPT?dispatch=database.manage" />
		<item group="logs.manage" title="phpinfo" href="%INDEX_SCRIPT?dispatch=tools.phpinfo" target="_blank" />
		<item group="logs.manage" title="clean_logs" href="%INDEX_SCRIPT?dispatch=logs.clean" meta="cm-confirm" />
		<item group="logs.manage" title="settings" href="%INDEX_SCRIPT?dispatch=settings.manage&amp;section_id=Logging" />

		<item group="exim.import" title="export" href="%INDEX_SCRIPT?dispatch=exim.export&amp;section=products" />
		<item group="exim.export" title="import" href="%INDEX_SCRIPT?dispatch=exim.import&amp;section=products" />
	</side>
</administration>

<design>
	<item title="logos" dispatch="site_layout.logos" />
	<item title="design_mode" dispatch="site_layout.design_mode" />
	<item title="blocks" dispatch="block_manager.manage" />
	<item title="appearance_settings" dispatch="settings.manage" extra="section_id=Appearance" />
	<item title="quick_links" dispatch="static_data.manage" extra="section=N" />
	<item title="top_menu" dispatch="static_data.manage" extra="section=A" />
	<item title="sitemap" dispatch="sitemap.manage" />
	<item title="template_editor" dispatch="template_editor.manage" />
	<item title="skin_selector" dispatch="skin_selector.manage" />

	<side>
		<item group="sitemap" title="sitemap_settings" href="%INDEX_SCRIPT?dispatch=settings.manage&amp;section_id=Sitemap" />

		<item group="sitemap.manage" title="sitemap_settings" href="%INDEX_SCRIPT?dispatch=settings.manage&amp;section_id=Sitemap" />
	</side>
</design>

<content>
	<item title="pages" links_group="pages" dispatch="pages.manage" extra="get_tree=multi_level"/>
	<item title="languages" dispatch="languages.manage" />

	<side>
		<item group="pages.update" title="delete_this_page" href="%INDEX_SCRIPT?dispatch=pages.delete&amp;page_id=%PAGE_ID" meta="cm-confirm" />
		<item group="pages.update" title="clone_this_page" href="%INDEX_SCRIPT?dispatch=pages.clone&amp;page_id=%PAGE_ID" />
		<item group="pages.update" title="add_page" href="%INDEX_SCRIPT?dispatch=pages.add&amp;page_type=T&amp;parent_id=%PAGE_ID" />
		<item group="pages.update" title="add_link" href="%INDEX_SCRIPT?dispatch=pages.add&amp;page_type=L&amp;parent_id=%PAGE_ID" />
			<item group="languages.manage" title="translate_privileges" href="%INDEX_SCRIPT?dispatch=usergroups.privileges" />
		</side>
</content>
<content>
	<item title="site_news" dispatch="news.manage" />
	<item title="newsletters" dispatch="newsletters.manage" />
	<item title="mailing_lists" dispatch="mailing_lists.manage" />
	<item title="subscribers" dispatch="subscribers.manage" />

	<side>
		<item group="news.update" title="add_news" href="%INDEX_SCRIPT?dispatch=news.add" />
		
		<item group="newsletters.update" title="add_newsletter" href="%INDEX_SCRIPT?dispatch=newsletters.add&amp;type=N" />
		<item group="newsletters.update" title="add_template" href="%INDEX_SCRIPT?dispatch=newsletters.add&amp;type=T" />
		<item group="newsletters.update" title="add_autoresponder" href="%INDEX_SCRIPT?dispatch=newsletters.add&amp;type=A" />
	</side>
</content>
<content>
	<side>
		<item group="pages.update" title="add_poll" href="%INDEX_SCRIPT?dispatch=pages.add&amp;page_type=P&amp;parent_id=%PAGE_ID" />
	</side>
</content><content>
	<item title="banners" dispatch="banners.manage" />
</content>
<content>
	<item title="comments_and_reviews" dispatch="discussion_manager.manage" />
</content>
<content>
	<item title="discussion_title_home_page" dispatch="discussion.update&amp;discussion_type=E" />
</content>
";s:6:"expiry";i:0;}