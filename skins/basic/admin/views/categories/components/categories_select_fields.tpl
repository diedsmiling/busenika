{* $Id: categories_select_fields.tpl 10184 2010-07-23 11:11:24Z klerik $ *}

<input type="hidden" name="selected_fields[object]" value="category" />

<table cellspacing="0" cellpadding="5" border="0" width="100%">
<tr valign="top">
	<td>
		<ul>
			<li class="select-field">
				<input type="hidden" value="status" name="selected_fields[data][]" />
				<input type="checkbox" value="status" name="selected_fields[data][]" id="elm_status" checked="checked" disabled="disabled" class="checkbox cm-item-s" />
				<label for="elm_status">{$lang.status}</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="meta_description" name="selected_fields[data][]" id="elm_meta_description" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_meta_description">{$lang.meta_description}</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="product_details_layout" name="selected_fields[data][]" id="elm_product_details_layout" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_product_details_layout">{$lang.product_details_layout}</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="hidden" value="category" name="selected_fields[data][]" />
				<input type="checkbox" value="category" name="selected_fields[data][]" id="elm_category_name" checked="checked" disabled="disabled" class="checkbox cm-item-s" />
				<label for="elm_name">{$lang.name}</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="meta_keywords" name="selected_fields[data][]" id="elm_meta_keywords" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_meta_keywords">{$lang.meta_keywords}</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" value="description" name="selected_fields[data][]" id="elm_description" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_description">{$lang.category_description}</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="image_pair" name="selected_fields[images][]" id="elm_image_pair" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_image_pair">{$lang.image_pair}</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" value="page_title" id="elm_page_title" name="selected_fields[data][]" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_page_title">{$lang.title}</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="timestamp" id="elm_timestamp" name="selected_fields[data][]" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_timestamp">{$lang.created_date}</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" value="position" name="selected_fields[data][]" id="elm_position" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_position">{$lang.position}</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="usergroup_ids" name="selected_fields[data][]" id="elm_usergroup_ids" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_usergroup_ids">{$lang.usergroups}</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			{hook name="categories:fields_to_edit"}
			{/hook}
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" id="elm_localization" value="localization" name="selected_fields[data][]" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_localization">{$lang.localization}</label>
			</li>
		</ul>
	</td>
</tr>
</table>
<p>
<a name="check_all" class="cm-check-items-s cm-on underlined">{$lang.select_all}</a>&nbsp;/&nbsp;<a href="#sfields" name="check_all" class="cm-check-items-s cm-off underlined">{$lang.unselect_all}</a>
</p>

