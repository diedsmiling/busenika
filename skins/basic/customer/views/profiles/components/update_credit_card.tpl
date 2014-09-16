{* $Id: update_credit_card.tpl 7196 2009-04-06 07:57:02Z zeke $ *}

{assign var="capture_name" value="card_picker_`$card_id`"}
{capture name=$capture_name}
	<form name="{$id}_form" action="{""|fn_url}" method="post">
		<input type="hidden" name="profile_id" value="{$pid}" />
		<input type="hidden" name="user_id" value="{$uid}" />
		{if $card_id}
		<input type="hidden" name="card_id" value="{$card_id}" />
		<input type="hidden" name="default_cc" value="{if $card_data.default}1{/if}" />
		{/if}
		<input type="hidden" value="credit_cards" name="selected_section"/>
		<div class="object-container">
		{include file="views/orders/components/payments/cc.tpl" card_id=$card_id card_data=$card_data}
		</div>
		<div class="buttons-container">
			<span class="submit-button cm-button-main">
				<input type="submit" value="{if $card_id}{$lang.update}{else}{$lang.add}{/if}" name="dispatch[profiles.update_cards]" />
			</span>
			&nbsp;{$lang.or}&nbsp;&nbsp;<a class="cm-popup-switch cm-cancel-link">{$lang.cancel}</a>
		</div>
	</form>
{/capture}

{include file="common_templates/popupbox.tpl" id=$id link_text=$link_text text=$title content=$smarty.capture.$capture_name edit_picker=true link_meta=$link_meta}