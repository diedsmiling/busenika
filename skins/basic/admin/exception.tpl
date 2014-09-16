{* $Id: exception.tpl 9845 2010-06-23 10:59:32Z lexa $ *}

{if !$auth.user_id}
	<span class="right"><span>&nbsp;</span></span>

	<h1 class="clear exception-header">
		<a href="{$config.admin_index|fn_url}" class="float-left"><img src="{$images_dir}/{$manifest.Signin_logo.filename}" width="{$manifest.Signin_logo.width}" height="{$manifest.Signin_logo.height}" border="0" alt="{$manifest.Signin_logo.alt}" title="{$manifest.Signin_logo.alt}" /></a>
		<span>{$lang.administration_panel}</span>
	</h1>
{/if}

<div class="exception-body login-content">

<h2>{$exception_status}</h2>

<h3>
	{if $exception_status == "403"}
		{$lang.access_denied}
	{elseif $exception_status == "404"}
		{$lang.page_not_found}
	{/if}
</h3>

<div class="exception-content">
	{if $exception_status == "403"}
		<h4>{$lang.access_denied_text}</h4>
	{elseif $exception_status == "404"}
		<h4>{$lang.page_not_found_text}</h4>
	{/if}
	
	<ul class="exception-menu">
		<li id="go_back"><a onclick="history.go(-1);">{$lang.go_back}</a></li>
		<li><a href="{$config.admin_index|fn_url}">{$lang.go_to_the_admin_homepage}</a></li>
	</ul>

	<script type="text/javascript">
	//<![CDATA[
	{literal}
	 jQuery.each(jQuery.browser, function(i, val) {
		if ((i == 'opera') && (val == true)) {
			if (history.length == 0) {
				$('#go_back').hide();
			}
		} else {
			if (history.length == 1) {
				$('#go_back').hide();
			}
		}
    });
	{/literal}
	//]]>
	</script>
</div>

</div>
