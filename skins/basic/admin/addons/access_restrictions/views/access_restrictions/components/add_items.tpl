{* $Id: add_items.tpl 6613 2008-12-19 12:46:16Z angel $ *}

<div class="object-container">
	<div class="add-new-object-group">
		<div class="tabs cm-j-tabs">
			<ul>
				<li id="tab_add_{$object}_new" class="cm-js cm-active"><a>{$lang.general}</a></li>
			</ul>
		</div>

		<div class="cm-tabs-content" id="content_tab_add_{$object}_new">
		<fieldset>
			{if $ip}
				<div class="form-field">
					<label class="cm-required">{$lang.ip_from}:</label>
					<input type="text" name="{$object}[0][range_from]" size="15" class="input-text" />
				</div>

				<div class="form-field">
					<label class="cm-required">{$lang.ip_to}:</label>
					<input type="text" name="{$object}[0][range_to]" size="15" class="input-text" />
				</div>
			{else}
				<div class="form-field">
					<label class="cm-required">{$object_name}:</label>
					<input type="text" name="{$object}[0][value]" size="15" value="" onfocus="this.value = ''" class="input-text-large main-input" />
				</div>
			{/if}

			<div class="form-field">
				<label>{$lang.reason}:</label>
				<input type="text" name="{$object}[0][reason]" class="input-text-large" />
			</div>

			{include file="common_templates/select_status.tpl" input_name="`$object`[0][status]" id=$object}
		</fieldset>
		</div>
	</div>
</div>

