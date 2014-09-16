{* $Id: carriers.tpl 8364 2009-12-08 11:43:31Z alexions $ *}

{if $carrier == "USP"}
	{assign var="url" value="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?strOrigTrackNum=`$tracking_number`"}
{elseif $carrier == "UPS"}
	{assign var="url" value="http://wwwapps.ups.com/WebTracking/processInputRequest?AgreeToTermsAndConditions=yes&amp;tracknum=`$tracking_number`"}
{elseif $carrier == "FDX"}
	{assign var="url" value="http://fedex.com/Tracking?action=track&amp;tracknumbers=`$tracking_number`"}
{elseif $carrier == "AUP"}
	<form name="tracking_form{$shipment_id}" target="_blank" action="http://ice.auspost.com.au/display.asp?ShowFirstScreenOnly=FALSE&ShowFirstRecOnly=TRUE" method="post">
		<input type="hidden"  name="txtItemNumber" maxlength="13" value="{$tracking_number}" />
	</form>
	{assign var="url" value="javascript: document.tracking_form`$shipment_id`.submit();"}
{elseif $carrier == "DHL" || $shipping.carrier == "ARB"}
	<form name="tracking_form{$shipment_id}" target="_blank" method="post" action="http://track.dhl-usa.com/TrackByNbr.asp?nav=Tracknbr">
		<input type="hidden" name="txtTrackNbrs" value="{$tracking_number}" />
	</form>
	{assign var="url" value="javascript: document.tracking_form`$shipment_id`.submit();"}
{elseif $carrier == "CHP"}
	{assign var="url" value="http://www.post.ch/swisspost-tracking?formattedParcelCodes=`$tracking_number`"}
{/if}

{capture name="carrier_url"}
{$url}
{/capture}