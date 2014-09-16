<?php
//$Id: track.php 5807 2008-08-26 09:27:03Z zeke $
/*
    Example tracking request.
*/
include('fedexdc.php');

// create new FedExDC object
// For tracking results you do not need an account# or meter#
$fed = new FedExDC();

//tracking example
$track_Ret = $fed->ref_track(
    array(
        '1537' => '790204649977', //Tracking Number
        '1534' =>'Y' // detail_scan_indicator (Show me all the tracking data)
    )
);

echo '<pre>';
if ($error = $fed->getError()) {
    echo "ERROR :". $error;
} else {
    echo $fed->debug_str. "\n<BR>";
    print_r($track_Ret);
    echo "\n\n";
    for ($i=1; $i<=$track_Ret[1584]; $i++) {
        echo "This package was deliverd on ".$track_Ret['1720-'.$i];
        echo "\nSigned for by ".$track_Ret['1706-'.$i];
    }
}


echo '</pre>';
?>
