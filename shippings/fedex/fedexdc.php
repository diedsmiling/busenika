<?php

if ( !defined('AREA') ) { die('Access denied'); }
/*
$Id: fedexdc.php 8889 2010-02-19 08:56:04Z 2tl $
Copyright (c) 2004 Vermonster LLC
All rights reserved.

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


--------------------------------------------------------------------
FedEx-DirectConnect - PHP interface to FedEx Direct Connect API

This class has been developed to send transactions to FedEx's
Ship Manager Direct API.  It can be used for all transactions
the API can support.  For more detailed information please
referer to FedEx's documentation located at their website.
http://www.fedex.com/us/solutions/wis/.  Here you will be able
to download "TagTransGuide.pdf" which outlines all the FedEx
codes needed to send calls to their API.

This class requires you have PHP CURL support.

To submit a transaction to FedEx's Gateway server you must have a valid
FedEx Account Number and a FedEx Meter Number.  To gain access
and receive a Meter Number you must send a Subscribe () request to
FedEx containing your FedEx account number and contact information.

Questions, Comments

Jay Powers
jay@vermonster.com

Vermonster LLC
312 Stuart St.
Boston, MA 02116

*/
// Contact FedEx for the API address

define('REQUEST_TIMEOUT', 15, true);
define('REQUEST_TYPE', 'CURL', true);

require_once(DIR_SHIPPING_FILES . 'fedex/fedex-tags.php');

class FedExDC extends FedExTags{

        var $VERSION = '1.01';
        var $NAME = 'FedExDC';
        var $ERROR_STR = '';

        //this will be the field returned by FedEx
        //containing the binary image data
        var $image_key;

        // FedEx API URI
        var $fedex_uri;

        // set the timeout
        var $timeout;

        // Array of data from FedEx
        var $rHash = array();
        
        // Debug String
        var $debug_str = '';

        /**
        * constructor: loads account# and meter#
        *
        * @param    int $account FedEx Account number
        * @param    int $meter FedEx meter number
        * @param    array $params Associative array of parameters listed:
        *                       fedex_uri: FedEx API URI
                                fedex_host: Host for FedEx
                                referer: Referering Host
                                timeout: Connection timeout in seconds.
        *
        * @access public
        */
        function FedExDC ($account='', $meter='', $params = array()) {
            
            $this->FedExTags();

            $this->account  = $account;
            $this->meter    = $meter;
            $this->time_start = $this->getmicrotime();

            // param defaults
            $this->timeout =    REQUEST_TIMEOUT;
            $this->image_key =  188;
            foreach ($params as $key => $value) {
                $this->{$key} = $value;
            }
        }

        /**
        * Sets debug information
        *
        * @param    string $string debug data
        * @access   private
        */
        function debug($string){
            $this->debug_str .= get_class($this).": $string\n";
        }

        /**
        * returns error string if present
        *
        * @return   boolean string
        * @access   public
        */
        function getError(){
            if($this->ERROR_STR != ""){
                return $this->ERROR_STR;
            }
            return false;
        }

        /**
        * sets error string
        *
        * @param    string $str
        * @access   private
        */
        function setError($str){
            $this->ERROR_STR .= $str;
        }

        /**
        * microtime
        *
        * @return   float
        * @access   private
        */
        function getmicrotime(){
            list($usec, $sec) = explode(" ",microtime());
            return ((float)$usec + (float)$sec);
        }

        /**
        * creates FedEx buffer string
        *
        * @param    int $uti FedEx transaction UTI
        * @param    array $vals values to send to FedEx
        * @return   string
        * @access   public
        */
        function setData($meth, $vals) {
            $this->sBuf = '';
            if (empty($vals[0]))    $vals[0] = $this->FE_TT[$meth][0];
            if (empty($vals[3025])) $vals[3025] = $this->FE_TT[$meth][1];
            if (isset($this->account) and !array_key_exists(10, $vals)) $this->sBuf .= '10,"' . $this->account . '"';
            if (isset($this->meter) and !array_key_exists(498, $vals))  $this->sBuf .= '498,"' .$this->meter. '"';

            foreach ($vals as $key => $val) {

                $key = $this->fieldNameToTag($key);
                // Empty value should not be sent (except for 99).
                if (empty($val)) continue;
                
                // Get rid of the junk
                $val = trim($val);
                
                // %-escape
                $val = preg_replace('/([%"\x00])/', "chr(hexdec($1))", $val);
                
                $this->sBuf .= "$key,\"$val\"";

            }
            $time = $this->getmicrotime() - $this->time_start;
            $this->debug('setData: build FedEx string ('. $time.')');
            return $this->sBuf .= '99,""';
        }

        /**
        * parses FedEx return string into assoc array
        *
        * @return   array FedEx return values
        * @access   public
        */
        function _splitData(){

            // Match all the data elements
            if (!preg_match_all('/(0|[1-9]\d*(?:-\d*)*),"([^"]*)"/', $this->httpBody, $aData)) {
                 $this->setError("Invalid FedEx transaction data at `$this->httpBody'");
                return;
            }
			foreach ($aData[1] as $numKey => $keyVal) {

				// Zeke: removes "-1" suffix from returnred data (1409-1 => 1409) 
				$keyVal = strpos($keyVal, '-1') !== false ? intval($keyVal) : $keyVal;

                $dataVal = $aData[2][$numKey];
                
                // Duplicate Key Something is wrong
                if (isset($this->rHash[$keyVal])) {
                    $this->setError("Duplicate key $keyVal in FedEx transaction");
                    return;
                }

                // Look for empty values in data
                if (empty($dataVal) and $keyVal != '99' and $keyVal != '2399' and $keyVal != '3124' and $keyVal != '4021-2') {
                    $this->setError("Empty value for key $keyVal in FedEx transaction");
                    return;
                }

                $this->rHash[$keyVal] = $dataVal;
            }
            
            $time = $this->getmicrotime() - $this->time_start;
            $this->debug('_splitData: Parse FedEx response ('. $time.')');            
            if (isset($this->rHash[2])) {
			  // Commited due to the forum bug vbug_id=221
              //  $this->setError("FedEx Return Error ". $this->rHash[2]." : ".$this->rHash[3]);
              //  return;
            }
            return $this->rHash;
        }

        /**
        * decode binary label data
        *
        * @param    string $label_file file to save label on disk
        * @return   mixed
        * @access   public
        */
        function label($label_file=false) {
            $this->httpLabel =  $this->rHash[$this->image_key];
            if ($this->httpLabel = preg_replace('/%([0-9][0-9])/e', "chr(hexdec($1))", $this->httpLabel)) {
                    $this->debug('separate binary image data');
                    $this->debug('decoded binary label data');
            }
            if ($label_file) {
                $this->debug('label: trying to write out label to '. $label_file);
                $FH = fopen ($label_file, "w+b");
                 if (!fwrite($FH, $this->httpLabel)) {
                    $this->setError("Can't write to file $label_file");
                    return false;
                 }
                 fclose($FH);
                 @chmod($label_file, DEFAULT_FILE_PERMISSIONS);
            } else {
                return $this->httpLabel;
            }

        }
        
        /**
        * lookup a value from FedEx response
        *
        * @param    string $code item you are looking for.  Can be either a field name or tag
        * @return   string
        * @access   public
        */
        function lookup($code) {
            $code = $this->fieldNameToTag($code);
            return @$this->rHash[$code];
        }

        /**
        * prepares and sends request to FedEx API
        *
        * @param    string $buf pre-formatted FedEx buffer
        * @return   mixed
        * @access   public
        */
        function transaction($buf=false) {
                if ($buf) $this->sBuf = $buf;

                // Future design to allow different types of requests
                if (REQUEST_TYPE == 'CURL') {
                        $meth = '_sendCurl';
                }
                if ($this->$meth()) {
                        $this->_splitData();
                        return $this->rHash;
                } else {
                        return false;
                }
        }

        /**
        * sends a request to FedEx using cUrl
        *
        * @return   string
        * @access   private
        */
        function _sendCurl() {

				list($header, $this->httpBody) = fn_https_request('POST', $this->fedex_uri, $this->sBuf, '', '');

				if (empty($header)){
                        $this->setError($this->httpBody);
                        return false;
                }

                if(strlen($this->httpBody) == 0){
                        $this->debug("body contains no data");
                        $this->setError("body contains no data");
                        return false;
                }
                $time = $this->getmicrotime() - $this->time_start;
                $this->debug('Got response from FedEx ('. $time.')');

                return $this->httpBody;
        }

        
        /* Below are methods for each of FedEx's services
           I thought this would be easier as all the
           functions are the same except for the setData
           and image key value.  Perfect task for PHP5 __call method!
        */

        /**
        * close ground shipments
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function close_ground ($aData) {
                $this->setData('close_ground', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process close_ground');
                    return false;
                }
        }
        
        /**
        * cancel an express shipment
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function cancel_express ($aData) {
                $this->setData('cancel_express', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process cancel_express');
                    return false;
                }
        }

        /**
        * send an express shipment
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function ship_express ($aData) {
                $this->setData('ship_express', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process ship_express');
                    return false;
                }
        }
        
        /**
        * global rate available services
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function global_rate_express ($aData) {
                $this->setData('global_rate_express', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process global_rate');
                    return false;
                }
        }
        
        /**
        * FedEx service availability
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function service_avail ($aData) {
                $this->setData('service_avail', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process service_avail');
                    return false;
                }
        }

        /**
        * rate all available services
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function rate_services ($aData) {
                $this->setData('rate_services', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process rate_services');
                    return false;
                }
        }
        
        /**
        * Locate FedEx services
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function fedex_locater ($aData) {
                $this->setData('fedex_locater', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process fedex_locater');
                    return false;
                }
        }

        /**
        * send a ground shipment
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function ship_ground ($aData) {
                $this->setData('ship_ground', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process ship_ground');
                    return false;
                }
        }

        /**
        * cancel ground shipments
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function cancel_ground ($aData) {
                $this->setData('cancel_ground', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process cancel_ground');
                    return false;
                }
        }

        /**
        * Subscribe to FedEx API
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function subscribe ($aData) {
                $this->setData('subscribe', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process subscribe');
                    return false;
                }
        }

        /**
        * global rate available services
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function global_rate_ground ($aData) {
                $this->setData('global_rate_ground', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process global_rate');
                    return false;
                }
        }

        /**
        * Signature Proof of Delivery
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function sig_proof_delivery ($aData) {
                $this->image_key = 1471;
                $this->setData('sig_proof_delivery', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process sig_proof_delivery');
                    return false;
                }
        }
        
        /**
        * Track a shipment by tracking number
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function track ($aData) {
                $this->setData('track', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process track');
                    return false;
                }
        }
        
        /**
        * Track By Number, Destination, Ship Date, and Reference
        *
        * @param    $aData array values to send to FedEx
        * @return   string
        * @access   private
        */
        function ref_track ($aData) {
                $this->setData('ref_track', $aData);
                if ($aRet = $this->transaction()) {
                    return $aRet;
                } else {
                    $this->setError('unable to process ref_track');
                    return false;
                }
        }
}
?>
