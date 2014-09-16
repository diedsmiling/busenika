<?php
// $Id: ship.php 5807 2008-08-26 09:27:03Z zeke $
/*
    Example express shipment request.
*/

include('fedexdc.php');

// create new FedExDC object
$fed = new FedExDC('318370788', '1054992');

// Ship example
// You can either pass the FedEx tag value or the field name in the 
// $FE_RE array
// This will send a ship express shipment to FedEx.  FedEx will return
// a bunch of data including a PNG image label.  Accessed through $fed->label
$ship_Ret = $fed->ship_express(
    array(
        'weight_units' =>   'LBS'
        ,16=>   'Ma'
        ,13=>   '44 Main street'
        ,5=>    '312 stuart st'
        ,1273=> '01'
        ,1274=> '01'
        ,18=>   '6173335555'
        ,15=>   'Boston'
        ,23=>   '1'
        ,9=>    '02134'
        ,183=>  '6175556985'
        ,8=>    'MA'
        ,117=>  'US'
        ,17=>   '02116'
        ,50=>   'US'
        ,4=>    'Vermonster LLC'
        ,7=>    'Boston'
        ,12=>   'Jay Powers'
        ,1333=> '1'
        ,1401=> '2.0'
        ,116 => 1
        ,68 =>  'USD'
        ,1368 => 2
        ,1369 => 1
        ,1370 => 5
		,2399 => 1	
    )
);


echo "<PRE>";
if ($error = $fed->getError()) {
    die("ERROR: ". $error);
} else {
    // decode and save label
    $fed->label('/tmp/myLabel.png');
    echo $fed->debug_str. "\n<BR>";
    echo "\n\n";
    echo "Price $".$fed->lookup('net_charge_amount');
    echo "\n";
    echo "Tracking# ".$ship_Ret[29];
}
echo "</PRE>";
?>
