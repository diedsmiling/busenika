{* $Id: view.tpl 9452 2010-05-14 12:05:08Z 2tl $ *}

<div class="clear product-main-info">
	<div class="float-left image-border center">
		<span id="compnay_image_{$company_data.company_id}">
			<img border="0" width="120" alt="{$company_manifest.Customer_logo.alt}" src="{if $company_manifest.Customer_logo.vendor}{$config.images_path}{else}{$images_dir}/{/if}{$company_manifest.Customer_logo.filename}" />
		</span>
	</div>

	<div id="block_company_{$company_data.company_id}" class="product-info">
		<h1 class="mainbox-title">{$company_data.company}</h1>
		<hr class="dashed" />
		<p class="product-descr">{$company_data.company_description}</p>

		{if $company_data.phone}
		<div id="company_phone" class="form-field product-list-field">
			<label>{$lang.phone}:</label>
			{$company_data.phone}
		</div>
		{/if}

		{if $company_data.url}
		<div id="company_website" class="form-field product-list-field">
			<label>{$lang.website}:</label>
			{$company_data.url}
		</div>
		{/if}
	</div>
</div>