{* $Id: notification.tpl 10361 2010-08-05 12:10:47Z klerik $ *}

{if !"AJAX_REQUEST"|defined}
{foreach from=""|fn_get_notifications item="message" key="key"}
{if $message.type != "P" && $message.type != "L" && $message.type != "C"}
	{if $message.type == "O"}
		{capture name="checkout_error_content"}
		{$smarty.capture.checkout_error_content}
		<div class="error-box-container notification-content" id="notification_{$key}">
			<div class="error-box">
				<img class="cm-notification-close hand" src="{$images_dir}/icons/notification_close.gif" width="10" height="19" border="0" alt="{$lang.close}" title="{$lang.close}" />
				<p>{$message.message}</p>
			</div>
		</div>
		{/capture}
	{else}
		{capture name="notification_content"}
		{$smarty.capture.notification_content}
		<div class="notification-content{if $message.save_state == false} cm-auto-hide{/if}" id="notification_{$key}">
			<div class="notification-{$message.type|lower}">
				<img class="cm-notification-close hand" src="{$images_dir}/icons/notification_close.gif" width="10" height="19" border="0" alt="{$lang.close}" title="{$lang.close}" />
				<div class="notification-body">
					{$message.message}
				</div>
			</div>
			<h1 class="notification-header-{$message.type|lower}">{$message.title}</h1>
		</div>
		{/capture}
	{/if}
{else}
	<div class="product-notification-container{if $message.save_state == false} cm-auto-hide{/if}" id="notification_{$key}">
		<div class="w-shadow"></div>
		<div class="e-shadow"></div>
		<div class="nw-shadow"></div>
		<div class="ne-shadow"></div>
		<div class="sw-shadow"></div>
		<div class="se-shadow"></div>
		<div class="n-shadow"></div>
		<div class="popupbox-closer"><img src="{$images_dir}/icons/close_popupbox.png" class="cm-notification-close" title="{$lang.close}" alt="{$lang.close}" /></div>
		<div class="product-notification notification-correct">
			<h1>{$message.title}</h1>
			{$message.message}
		</div>
		<div class="s-shadow"></div>
	</div>
{/if}
{/foreach}

<div class="cm-notification-container">
{$smarty.capture.notification_content}
</div>
{/if}