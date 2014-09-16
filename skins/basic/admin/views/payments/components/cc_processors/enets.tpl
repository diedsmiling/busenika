{* $Id: enets.tpl 8080 2009-10-09 12:18:07Z zeke $ *}

{assign var="r_url" value="<strong>`$config.http_location`/payments/enets.php</strong>"}
<p>{$lang.text_enets_notice|replace:"[r_url]":$r_url}</p>
<hr />

<div class="form-field">
	<label for="merchantid">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchantid]" id="merchantid" value="{$processor_params.merchantid}" class="input-text" size="60" />
</div>
