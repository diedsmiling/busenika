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
// $Id: usps.php 10297 2010-08-02 12:01:12Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_get_usps_rates($code, $weight_data, $location, &$auth, $shipping_settings, $package_info, $origination)
{
	static $cached_rates = array();

	if ($shipping_settings['usps_enabled'] != 'Y') {
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
	
	if ($shipping_settings['usps']['test_mode'] == 'Y') {
		$url = 'http://testing.shippingapis.com/ShippingAPI.dll';
	} else {
		$url = 'http://production.shippingapis.com/ShippingAPI.dll';
	}


	$username = $shipping_settings['usps']['username'];

	$machinable = $shipping_settings['usps']['machinable'];
	$container_priority = $shipping_settings['usps']['container_priority'];
	$container_express = $shipping_settings['usps']['container_express'];
	$mailtype = $shipping_settings['usps']['mailtype'];

	$package_size = $shipping_settings['usps']['package_size'];
	$first_class_mail_type = $shipping_settings['usps']['first_class_mail_type'];

	$pounds = $weight_data['pounds'];
	$ounces = $weight_data['ounces'];
	
	// The zip code should be in 5 digit format so we cut all digits after "-"
	$origination_postal = preg_replace('/-\d*/i', '', trim($origination['zipcode']));
	$destination_postal = preg_replace('/-\d*/i', '', trim($location['zipcode']));

	$origination_country = $origination['country'];
	$destination_country = $location['country'];

	$size_parameters = '';
	if ($package_size == 'Large') {
		$_width = $shipping_settings['usps']['priority_width'];
		$_length = $shipping_settings['usps']['priority_length'];
		$_height = $shipping_settings['usps']['priority_height'];
		$size_parameters = <<<EOT
			<Width>$_width</Width>
			<Length>$_length</Length>
			<Height>$_height</Height>
EOT;
		if($container_priority == 'NonRectangular') {
			$_priority_girth = $shipping_settings['usps']['priority_girth'];
			$size_parameters .= "<Girth>$_priority_girth</Girth>";
		}

	}


	$us_dependent_territories = array(
		'AS', // American Samoa
		'VI', // U.S. Virgin Islands
		'PR', // Puerto Rico
		'GU', // Guam
		'MP', // Northern Mariana Islands
		'FM', //Micronesia
		'MH', //Marshall Islands,
		'PW' //Palau
	);
	if (in_array($destination_country, $us_dependent_territories)) {
		$destination_country = 'US';
	}
	
	if ($origination_country == $destination_country) {
		// Domestic rate calculation
		$query=<<<EOT
		<RateV3Request USERID="$username">
		  <Package ID="0">
			<Service>EXPRESS</Service>
			<ZipOrigination>$origination_postal</ZipOrigination>
			<ZipDestination>$destination_postal</ZipDestination>
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<Container>$container_express</Container>
			<Size>$package_size</Size>
		  </Package>
		  <Package ID="1">
			<Service>FIRST CLASS</Service>
			<FirstClassMailType>$first_class_mail_type</FirstClassMailType>
			<ZipOrigination>$origination_postal</ZipOrigination>
			<ZipDestination>$destination_postal</ZipDestination>
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<Size>$package_size</Size>
			<Machinable>$machinable</Machinable>
		  </Package>
		  <Package ID="2">
			<Service>PRIORITY</Service>
			<ZipOrigination>$origination_postal</ZipOrigination>
			<ZipDestination>$destination_postal</ZipDestination>
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<Container>$container_priority</Container>
			<Size>$package_size</Size>
			$size_parameters
		  </Package>
		  <Package ID="3">
			<Service>PARCEL</Service>
			<ZipOrigination>$origination_postal</ZipOrigination>
			<ZipDestination>$destination_postal</ZipDestination>
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<Size>$package_size</Size>
			<Machinable>$machinable</Machinable>
		  </Package>
		  <Package ID="4">
			<Service>BPM</Service>
			<ZipOrigination>$origination_postal</ZipOrigination>
			<ZipDestination>$destination_postal</ZipDestination>
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<Size>$package_size</Size>
		  </Package>
		  <Package ID="5">
			<Service>LIBRARY</Service>
			<ZipOrigination>$origination_postal</ZipOrigination>
			<ZipDestination>$destination_postal</ZipDestination>
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<Size>$package_size</Size>
		  </Package>
		  <Package ID="6">
			<Service>MEDIA</Service>
			<ZipOrigination>$origination_postal</ZipOrigination>
			<ZipDestination>$destination_postal</ZipDestination>
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<Size>$package_size</Size>
		  </Package>
		</RateV3Request>
EOT;

		$get = array (
			'API' => 'RateV3',
			'XML' => $query,
		);
		$is_domestic = true;

	} else {

		// International rate calculation
		$destination_country = fn_get_usps_country($destination_country);
		if (empty($destination_country)) {
			return false;
		}

		$query=<<<EOT
		<IntlRateRequest USERID="$username">
		  <Package ID="0">
			<Pounds>$pounds</Pounds>
			<Ounces>$ounces</Ounces>
			<MailType>$mailtype</MailType>
			<Country>$destination_country</Country>
		  </Package>
		</IntlRateRequest>
EOT;

		$get = array (
			'API' => 'IntlRate',
			'XML' => $query,
		);
		$is_domestic = false;
	}
	list($header, $result) = fn_http_request('GET', $url, $get);

	$rates = fn_usps_get_rates($result, $is_domestic);
	if (empty($cached_rates[$cached_rate_id])) {
		$cached_rates[$cached_rate_id] = $rates;
	}

	if (!empty($rates[$code])) {
		return array('cost' => $rates[$code]);
	} else {
		if (defined('SHIPPING_DEBUG')) {
			return array('error' => fn_usps_get_error($result));
		}
	}

	return false;
}


function fn_usps_get_error($result) 
{
	$doc = new XMLDocument();
	$xp = new XMLParser();
	$xp->setDocument($doc);
	$xp->parse($result);
	$doc = $xp->getDocument();

	$return = array();

	if (is_object($doc->root)) {
		$root = $doc->getRoot();
		if ($root->getName() == 'Error') {
			$return[] = $root->getValueByPath('/Description');
		} elseif ($root->getElementByName('Error')) {
			$return[] = $root->getValueByPath('/Error/Description');
		} else {
			$packages = $root->getElementsByName('Package');
			for ($i = 0; $i < count($packages); $i++) {
				if ($packages[$i]->getElementByName('Error')) {
					$return[] = $packages[$i]->getValueByPath('/Error/Description');
				}
			}
		}

		return implode(' / ', $return);
	}

	return false;
}

function fn_usps_get_rates($result, $is_domestic) 
{
	// Parse XML message returned by the UPS post server.

	$doc = new XMLDocument();
	$xp = new XMLParser();
	$xp->setDocument($doc);
	$xp->parse($result);
	$doc = $xp->getDocument();
	$return = array();

	if (is_object($doc->root)) {
		$root = $doc->getRoot();
		if ($is_domestic == true) {
			$shipment = $root->getElementsByName("Package");
		} else {
			$shipment = $root->getElementByName("Package");
			if (!empty($shipment)) {
				$shipment = $shipment->getElementsByName("Service");
			} else {
				return false;
			}
		}

		for ($i = 0; $i < count($shipment); $i++) {

			$service_name = '';
			if ($is_domestic == true) {
				if ($shipment[$i]->getElementByName("Postage")) {
					$service_name = $shipment[$i]->getValueByPath("/Postage/MailService");
					$rate = $shipment[$i]->getValueByPath("/Postage/Rate");
					if (floatval($rate)) {
						$is_machinable = $shipment[$i]->getValueByPath("/Machinable");
						if ($service_name == 'Parcel Post') {
							$service_name .= ($is_machinable == 'TRUE' ? ' M' : ' N');
						} elseif (strpos($service_name, 'Express Mail') !== false) {
							$service_name = 'Express Mail';
						} elseif (strpos($service_name, 'Priority Mail') !== false) {
							$service_name = 'Priority Mail';
						} elseif (strpos($service_name, 'First-Class Mail') !== false) {
							$service_name = 'First-Class Mail';
						}
					}
				}
			} else {
				if ($shipment[$i]->getElementByName("Postage")) {
					$service_name = $shipment[$i]->getValueByPath("/SvcDescription");
					$rate = $shipment[$i]->getValueByPath("/Postage");
				}
			}

			if (empty($service_name)) {
				continue;
			}

			$return[$service_name] = $rate;
		}

		return $return;
	}

	return false;
}

function fn_get_usps_country($code)
{
	static $countries = array ( 
		'AD' => 'Andorra',
		'AE' => 'United Arab Emirates',
		'AF' => 'Afghanistan',
		'AG' => 'Antigua',
		'AI' => 'Anguilla',
		'AL' => 'Albania',
		'AM' => 'Armenia',
		'AN' => 'Netherlands Antilles',
		'AO' => 'Angola',
		'AR' => 'Argentina',
		'AS' => 'American Samoa',
		'AT' => 'Austria',
		'AU' => 'Australia',
		'AW' => 'Aruba',
		'AZ' => 'Azerbaijan',
		'BA' => 'Bosnia-Herzegovina',
		'BB' => 'Barbados',
		'BD' => 'Bangladesh',
		'BE' => 'Belgium',
		'BF' => 'Burkina Faso',
		'BG' => 'Bulgaria',
		'BH' => 'Bahrain',
		'BI' => 'Burundi',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BN' => 'Brunei Darussalam',
		'BO' => 'Bolivia',
		'BR' => 'Brazil',
		'BS' => 'Bahamas',
		'BT' => 'Bhutan',
		'BW' => 'Botswana',
		'BY' => 'Belarus',
		'BZ' => 'Belize',
		'CA' => 'Canada',
		'CC' => 'Cocos Island',
		'CF' => 'Central African Republic',
		'CG' => 'Congo, Democratic Republic of the',
		'CH' => 'Switzerland',
		'CI' => 'Cote d\'Ivoire',
		'CK' => 'Cook Islands',
		'CL' => 'Chile',
		'CM' => 'Cameroon',
		'CN' => 'China',
		'CO' => 'Colombia',
		'CR' => 'Costa Rica',
		'CU' => 'Cuba',
		'CV' => 'Cape Verde',
		'CX' => 'Christmas Island',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DE' => 'Germany',
		'DJ' => 'Djibouti',
		'DK' => 'Denmark',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'DZ' => 'Algeria',
		'EC' => 'Ecuador',
		'EE' => 'Estonia',
		'EG' => 'Egypt',
		'ER' => 'Eritrea',
		'ES' => 'Spain',
		'ET' => 'Ethiopia',
		'FI' => 'Finland',
		'FJ' => 'Fiji',
		'FK' => 'Falkland Islands',
		'FM' => 'Micronesia, Federated States of',
		'FO' => 'Faroe Islands',
		'FR' => 'France',
		'GA' => 'Gabon',
		'GB' => 'Great Britain and Northern Ireland',
		'GD' => 'Grenada',
		'GE' => 'Georgia, Republic of',
		'GF' => 'French Guiana',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GL' => 'Greenland',
		'GM' => 'Gambia',
		'GN' => 'Guinea',
		'GP' => 'Guadeloupe',
		'GQ' => 'Equatorial Guinea',
		'GR' => 'Greece',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GU' => 'Guam',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HK' => 'Hong Kong',
		'HN' => 'Honduras',
		'HR' => 'Croatia',
		'HT' => 'Haiti',
		'HU' => 'Hungary',
		'ID' => 'Indonesia',
		'IE' => 'Ireland',
		'IL' => 'Israel',
		'IN' => 'India',
		'IQ' => 'Iraq',
		'IR' => 'Iran',
		'IM' => 'Isle of Man',
		'IS' => 'Iceland',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JO' => 'Jordan',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'KE' => 'Kenya',
		'KG' => 'Kyrgyzstan',
		'KH' => 'Cambodia',
		'KI' => 'Kiribati',
		'KM' => 'Comoros',
		'KN' => 'Saint Christopher and Nevis',
		'KP' => 'Korea, Democratic People\'s Republic of',
		'KR' => 'Korea, Republic of',
		'KW' => 'Kuwait',
		'KY' => 'Cayman Islands',
		'KZ' => 'Kazakhstan',
		'LA' => 'Laos',
		'LB' => 'Lebanon',
		'LC' => 'Saint Lucia',
		'LI' => 'Liechtenstein',
		'LK' => 'Sri Lanka',
		'LR' => 'Liberia',
		'LS' => 'Lesotho',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'LV' => 'Latvia',
		'LY' => 'Libya',
		'MA' => 'Morocco',
		'MC' => 'Monaco',
		'MD' => 'Moldova',
		'MG' => 'Madagascar',
		'MH' => 'Marshall Islands',
		'MK' => 'Macedonia',
		'ML' => 'Mali',
		'MM' => 'Burma',
		'MN' => 'Mongolia',
		'MO' => 'Macao',
		'MP' => 'Northern Mariana Islands, Commonwealth',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MS' => 'Montserrat',
		'MT' => 'Malta',
		'MU' => 'Mauritius',
		'MV' => 'Maldives',
		'MW' => 'Malawi',
		'MX' => 'Mexico',
		'MY' => 'Malaysia',
		'MZ' => 'Mozambique',
		'NA' => 'Namibia',
		'NC' => 'New Caledonia',
		'NE' => 'Niger',
		'NF' => 'Norfolk Island',
		'NG' => 'Nigeria',
		'NI' => 'Nicaragua',
		'NL' => 'Netherlands',
		'NO' => 'Norway',
		'NP' => 'Nepal',
		'NR' => 'Nauru',
		'NU' => 'Niue',
		'NZ' => 'New Zealand',
		'OM' => 'Oman',
		'PA' => 'Panama',
		'PE' => 'Peru',
		'PF' => 'French Polynesia',
		'PG' => 'Papua New Guinea',
		'PH' => 'Philippines',
		'PK' => 'Pakistan',
		'PL' => 'Poland',
		'PM' => 'Saint Pierre and Miquelon',
		'PN' => 'Pitcairn Island',
		'PR' => 'Puerto Rico',
		'PT' => 'Portugal',
		'PW' => 'Palau',
		'PY' => 'Paraguay',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russia',
		'RW' => 'Rwanda',
		'SA' => 'Saudi Arabia',
		'SB' => 'Solomon Islands',
		'SC' => 'Seychelles',
		'SD' => 'Sudan',
		'SE' => 'Sweden',
		'SG' => 'Singapore',
		'SH' => 'Saint Helena',
		'SI' => 'Slovenia',
		'SK' => 'Slovak Republic',
		'SL' => 'Sierra Leone',
		'SM' => 'San Marino',
		'SN' => 'Senegal',
		'SO' => 'Somalia',
		'SR' => 'Suriname',
		'ST' => 'Sao Tome and Principe',
		'SV' => 'El Salvador',
		'SY' => 'Syrian Arab Republic',
		'SZ' => 'Swaziland',
		'TC' => 'Turks and Caicos Islands',
		'TD' => 'Chad',
		'TG' => 'Togo',
		'TH' => 'Thailand',
		'TJ' => 'Tajikistan',
		'TK' => 'Tokelau (Union) Group',
		'TM' => 'Turkmenistan',
		'TN' => 'Tunisia',
		'TO' => 'Tonga',
		'TP' => 'East Timor',
		'TR' => 'Turkey',
		'TT' => 'Trinidad and Tobago',
		'TV' => 'Tuvalu',
		'TW' => 'Taiwan',
		'TZ' => 'Tanzania',
		'UA' => 'Ukraine',
		'UG' => 'Uganda',
		'UK' => 'United Kingdom',
		'US' => 'United States',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VA' => 'Vatican City',
		'VC' => 'Saint Vincent and the Grenadines',
		'VE' => 'Venezuela',
		'VG' => 'British Virgin Islands',
		'VI' => 'Virgin Islands U.S.',
		'VN' => 'Vietnam',
		'VU' => 'Vanuatu',
		'WF' => 'Wallis and Futuna Islands',
		'WS' => 'Western Samoa',
		'YE' => 'Yemen',
		'YT' => 'Mayotte',
		'YU' => 'Yugoslavia',
		'ZA' => 'South Africa',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe'
	);

	return $countries[$code];
}
/** /Body **/

/** Functions **/
/** /Functions **/

?>