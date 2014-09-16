{* $Id: image_verification.tpl 10249 2010-07-28 12:58:35Z klerik $ *}

{if ""|fn_needs_image_verification == true}
<h2 class="subheader">{$lang.image_verification_header}</h2>
<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6Lf1r8ISAAAAAGphucNK05NegIEK5saDwbP0_lgh"></script>
	<noscript>
  		<iframe src="http://www.google.com/recaptcha/api/noscript?k=6Lf1r8ISAAAAAGphucNK05NegIEK5saDwbP0_lgh" height="300" width="500" frameborder="0"></iframe><br/>
  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>
<span>{$lang.image_verification_label}</span>
{/if}
