<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id: ups.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_get_ups_rates($code, $weight_data, $location, &$auth, $shipping_settings, $package_info, $origination)
{
	static $cached_rates = array();

	if ($shipping_settings['ups_enabled'] != 'Y') {
		return false;
	}

	$cached_rate_id = fn_generate_cached_rate_id($weight_data, $origination);
		
	if (!empty($cached_rates[$cached_rate_id])) {
		if (!empty($cached_rates[$cached_rate_id][$code])) {
			return array('cost' => $cached_rates[$cached_rate_id][$code]);
		} else {
			return false;
		}
	}

	if ($shipping_settings['ups']['test_mode'] == 'Y') {
		$url = "https://wwwcie.ups.com:443/ups.app/xml/Rate";
	} else {
		$url = "https://www.ups.com:443/ups.app/xml/Rate";
	}

	// Prepare data for UPS request
	$username = $shipping_settings['ups']['username'];
	$password = $shipping_settings['ups']['password'];
	$access_key = $shipping_settings['ups']['access_key'];

	$origination_postal = $origination['zipcode'];
	$origination_country = $origination['country'];

	$height = $shipping_settings['ups']['height'];
	$width = $shipping_settings['ups']['width'];
	$length = $shipping_settings['ups']['length'];

	$pickup_type = $shipping_settings['ups']['pickup_type'];
	$package_type = $shipping_settings['ups']['package_type'];

	$destination_postal = $location['zipcode'];
	$destination_country = $location['country'];

	// define weight unit and value
	$weight = $weight_data['full_pounds'];

	if (in_array($origination_country, array('US', 'DO','PR'))) {
		$weight_unit = 'LBS';
		$measure_unit = 'IN';
	} else {
		$weight_unit = 'KGS';
		$measure_unit = 'CM';
		$weight = $weight * 0.4536;
	}

	$customer_classification = '';
	if ($origination_country == 'US' && $pickup_type == '11') {
		$customer_classification=<<<EOT
	<CustomerClassification>
		<Code>04</Code>
	</CustomerClassification>
EOT;
	}

$request=<<<EOT
<?xml version="1.0"?>
<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>$access_key</AccessLicenseNumber>
		<UserId>$username</UserId>
		<Password>$password</Password>
</AccessRequest>
<?xml version="1.0"?>
<RatingServiceSelectionRequest xml:lang='en-US'>
  <Request>
	<TransactionReference>
	  <CustomerContext>Rate Request</CustomerContext>
	  <XpciVersion>1.0</XpciVersion>
	</TransactionReference>
	<RequestAction>Rate</RequestAction>
	<RequestOption>shop</RequestOption>
  </Request>
	<PickupType>
	<Code>$pickup_type</Code>
  </PickupType>
  $customer_classification
  <Shipment>
	<Shipper>
		<Address>
			<PostalCode>$destination_postal</PostalCode>
			<CountryCode>$destination_country</CountryCode>
		</Address>
	</Shipper>	
	<ShipTo>
		<Address>
			<PostalCode>$destination_postal</PostalCode>
			<CountryCode>$destination_country</CountryCode>
			<ResidentialAddressIndicator/>
		</Address>
	</ShipTo>
	<ShipFrom>
		<Address>
			<PostalCode>$origination_postal</PostalCode>
			<CountryCode>$origination_country</CountryCode>
		</Address>
	</ShipFrom>
	<Package>
		<PackagingType>
			<Code>$package_type</Code>
		</PackagingType>
			<Dimensions>
				<UnitOfMeasurement>
				  <Code>$measure_unit</Code>
				</UnitOfMeasurement>
				<Length>$length</Length>
				<Width>$width</Width>
				<Height>$height</Height>
			</Dimensions>
		<PackageWeight>
			<UnitOfMeasurement>
				 <Code>$weight_unit</Code>
			</UnitOfMeasurement>
			<Weight>$weight</Weight>
		</PackageWeight>   
	</Package>
  </Shipment>
</RatingServiceSelectionRequest>
EOT;

	$post=explode("\n", $request);
	list($header, $result) = fn_https_request('POST', $url, $post, '', '', 'text/xml');

	$rates = fn_ups_get_rates($result);
	if (empty($cached_rates[$cached_rate_id]) && !empty($rates)) {
		$cached_rates[$cached_rate_id] = $rates;
	}

	if (!empty($rates[$code])) {
		return array('cost' => $rates[$code]);
	} else {
		if (defined('SHIPPING_DEBUG')) {
			return array('error' => fn_ups_get_error($result));
		}
	}

	return false;
}


function fn_ups_get_error($result) 
{
	// Parse XML message returned by the UPS post server.

	$doc = new XMLDocument();
	$xp = new XMLParser();
	$xp->setDocument($doc);
	$xp->parse($result);
	$doc = $xp->getDocument();
	$return = '';

	if (is_object($doc->root)) {
		$status_code = $doc->getValueByPath('RatingServiceSelectionResponse/Response/ResponseStatusCode');

		if ($status_code != '1') {
			$return = $doc->getValueByPath('RatingServiceSelectionResponse/Response/Error/ErrorDescription');
			$return .= ' (' .$doc->getValueByPath('RatingServiceSelectionResponse/Response/Error/ErrorDigest'). ').';
			return $return;
		}
	}

	return false;
}

function fn_ups_get_rates($result) 
{
	$doc = new XMLDocument();
	$xp = new XMLParser();
	$xp->setDocument($doc);
	$xp->parse($result);
	$doc = $xp->getDocument();
	$return = array();

	if (is_object($doc->root)) {
		$responseStatusCode = $doc->getValueByPath('RatingServiceSelectionResponse/Response/ResponseStatusCode');
		$root = $doc->getRoot();

		$shipment = $root->getElementsByName("RatedShipment");
		for ($i = 0; $i < count($shipment); $i++) {
			$service_code = $shipment[$i]->getValueByPath("/Service/Code");
			$total_charge = $shipment[$i]->getValueByPath("/TotalCharges/MonetaryValue");
			if (!($service_code && $total_charge)) {
				continue;
			}
			//$rated_packages = $shipment[$i]->getElementsByName("RatedPackage");
			//$days_to_delivery = $shipment[$i]->getValueByPath("/GuaranteedDaysToDelivery");
			//$delivery_time = $shipment[$i]->getValueByPath("/ScheduledDeliveryTime");
			if (!empty($total_charge)) {
				$return[$service_code] = $total_charge;
			}
		}
	}
	return $return;
}

/** /Body **/

?>