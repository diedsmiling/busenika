<?php

date_default_timezone_set("Europe/Moscow");

ini_set('display_errors', 1); 
 
if (file_exists($_SERVER['DOCUMENT_ROOT']."/reklama/basa.xml")) $xml=simplexml_load_file($_SERVER['DOCUMENT_ROOT']."/reklama/basa.xml") ;

require_once('lib/nusoap.php');

$_POST=updatexml($xml);

$PATH_TO_CERTS = $_SERVER['DOCUMENT_ROOT'].'/reklama/cert';  

$cert = $PATH_TO_CERTS.'/cert.crt';        # файл, содержащий сертификат пользователя
$private = $PATH_TO_CERTS.'/private.key';  # файл, содержащий приватный ключ
$cacert = $PATH_TO_CERTS.'/cacert.pem';    # файл, содержащий корневой сертификат

$wsdlurl = 'http://soap.direct.yandex.ru/wsdl/v4/';

$client = new nusoap_client($wsdlurl, 'wsdl');
$client->authtype = 'certificate';
$client->decode_utf8 = 0;
$client->soap_defencoding = 'UTF-8';
$client->certRequest['sslcertfile'] = $cert;
$client->certRequest['sslkeyfile'] = $private;
$client->certRequest['cainfofile'] = $cacert;




$params = array( 
   'Filter' => array(
      'StatusArchive' => array('No')
   )         
);

$result = $client->call("GetCampaignsListFilter", array('params' => $params));
err($client);
foreach ($result as $character) {

       $params = array(
       'CampaignIDS' => array($character["CampaignID"]),
       'GetPhrases'=>'WithPrices',
       'Filter' => array(
          'StatusArchive' => array('No'),      
          'IsActive' => array('Yes')      
          )  
       );
    if(isbasa($xml,$character["CampaignID"])) {echo $character["CampaignID"]." - не проставлен"; continue;}
    sleep(5);
    $client = new nusoap_client($wsdlurl, 'wsdl');
$client->authtype = 'certificate';
$client->decode_utf8 = 0;
$client->soap_defencoding = 'UTF-8';
$client->certRequest['sslcertfile'] = $cert;
$client->certRequest['sslkeyfile'] = $private;
$client->certRequest['cainfofile'] = $cacert;

    $result2 = $client->call("GetBanners", array('params' => $params));
    err($client);
    foreach ($result2 as $character) {        
        foreach ($character["Phrases"] as $character2) {
            if(!isset($character2["Prices"])||!isset($_POST['radio'][$character2["PhraseID"]])) continue;
           $tmax=$character2["PremiumMax"]; 
           $tmin=$character2["PremiumMin"]; 
           $lmax=$character2["Max"]; 
           $lmin=$character2["Min"];
           
           if (isset($_POST['radio'][$character2["PhraseID"]]))
                $newprice=strateg($_POST['radio'][$character2["PhraseID"]],$tmax,$tmin,$lmax,$lmin,$character2["Prices"],$_POST['price'][$character2["PhraseID"]]);
                
           if ($newprice!=$character2["Price"] && ($newprice!=""&&$newprice!=0)) {
               echo "Новая цена ".$newprice." на ".$character2["Phrase"]." страрая - ".$character2["Price"]."<br />";
               newprice($character2["CampaignID"],$character2["BannerID"],$character2["PhraseID"],$newprice); 
               sleep(1); 
           }
        }
    }



}


function err($client){
     $err_msg = $client->getError();
    if ($err_msg) {
        // print error msg
        echo 'Error: '.$err_msg;
    }    
}

function addkw($XML,$cid,$bid,$kid,$limit,$strateg){
    $flag=false;
    foreach ($XML->kw as $kw)  
        if ($kw['kid']==$kid) {
            $flag=true;
            $kw['limit']=$limit;
            $kw['strateg']=$strateg;    
        }
    if(!$flag){    
        $kw=$XML->addChild('kw');
        $kw['cid']=$cid;
        $kw['bid']=$bid;
        $kw['kid']=$kid; 
        $kw['limit']=$limit;
        $kw['strateg']=$strateg;     
    }  
    return $XML;
}

function strateg($strateg,$tmax,$tmin,$lmax,$lmin,$Prices,$limit)
{
   $limit=str_replace(",",".",$limit); 
    if($strateg=="1"){
        if($limit>$tmax) return $tmax; 
        else 
        {
        foreach ($Prices as $price) 
          if($price<=$limit) return $price;            
        return $limit; 
        }    
    }
    
    if($strateg=="22"){     
        if($limit>$tmin) return $tmin; 
        else 
        {
        foreach ($Prices as $price) 
          if($price<=$limit) return $price;            
        return $limit; 
        }            
    }   
     
    if($strateg=="2"){ 
        if($limit>$tmax) return $tmax; 
        else 
        {
            
            if ($Prices[1]!=$tmin && $Prices[1]<$limit)  return $Prices[1];
            else 
            {
                if($limit>$tmin) return $tmin; 
                else 
                {
                foreach ($Prices as $price) 
                  if($price<=$limit) return $price;            
                return $limit; 
                }     
            }
        }     
    }
    
    if($strateg=="3"){
        if($limit>$lmax) return $lmax; 
        else 
        {
        foreach ($Prices as $price) 
          if($price<=$limit) return $price;            
        return $limit; 
        }      
    }
    if($strateg=="4"){
        if($limit>$lmin) return $lmin; 
        else 
        {
        foreach ($Prices as $price) 
          if($price<=$limit) return $price;            
        return $limit; 
        }             
    }
    if($strateg=="5"){
      foreach ($Prices as $price) 
      {
         // return $price;
        if($price<=$limit) return $price;  
      }
     return $limit; 
    }
  
  return 0;  
}

function updatexml($XML)
{
   
   foreach ($XML->kw as $kw)  
    {
        if (isset($_POST['save']))$POS['save']=$_POST['save'];
        if (isset($_POST['update'])) $POS['update']=$_POST['update']; 
        $kid=$kw['kid'];
        $POS['price']["".$kid]=$kw['limit']."";
        $POS['radio']["".$kid]=$kw['strateg']."";          
    } 
    return $POS;
}

function isbasa($XML,$cid)
{
   
   foreach ($XML->kw as $kw)  
    {
        if ($kw['cid'].""==$cid) return false;          
    } 
    return true;
}


function newprice($cid,$bid,$kid,$price)
{
    global $client;
        $params = array( 
         array(
           'PhraseID' => $kid,
           'BannerID' => $bid,
           'CampaignID' => $cid,
           'Price'=>$price            
           )
       ); 
       //var_dump($params);
       $result2 = $client->call("UpdatePrices", array('params' => $params));
       return $result2; 
}

?>
