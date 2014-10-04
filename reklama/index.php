<? 
			session_start(); 
ini_set('display_errors', 2); 

if(!empty($_SESSION['oO']))
	{
	 $oO = $_SESSION['oO'];
	
	}
else
	{
		$oO = 0;
	}
if(!empty($_POST))
	{
	$login_dev = $_POST['login'];
	$pass_dev = $_POST['pass'];
		
		if($login_dev == 'admin' && $pass_dev == '963494')
		{
			
			
		$_SESSION['oO'] = 1; // store session data
		
		}	
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Документ без названия</title>
</head>

<body>

	
<?php

date_default_timezone_set("Europe/Moscow");
echo '<script type="text/javascript" src="http://onlinesaler.ru/js/common/jquery-1.4.4.min.js"></script>'."\r\n";
echo '<script type="text/javascript">
function setparam(){



if (jQuery(".pradio1").attr("checked")) jQuery(".radio1").attr("checked","checked");
if (jQuery(".pradio2").attr("checked")) jQuery(".radio2").attr("checked","checked");
if (jQuery(".pradio22").attr("checked")) jQuery(".radio22").attr("checked","checked");
if (jQuery(".pradio3").attr("checked")) jQuery(".radio3").attr("checked","checked");
if (jQuery(".pradio4").attr("checked")) jQuery(".radio4").attr("checked","checked");
if (jQuery(".pradio5").attr("checked")) jQuery(".radio5").attr("checked","checked");

jQuery(".price").val(jQuery(".pprice").val());

};
</script>';

ini_set('display_errors', 1); 
if (file_exists("basa.xml")) $xml=simplexml_load_file("basa.xml") ;
else $xml = new SimpleXMLElement("<direct></direct>");

require_once($_SERVER['DOCUMENT_ROOT'].'/reklama/lib/nusoap.php');

if (!isset($_POST['save'])&&!isset($_POST['update'])) $_POST=updatexml($xml);

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

$result = $client->call("PingAPI", array());
err($client); 
//var_dump($result);


$params = array( 
   'Filter' => array(
      'StatusArchive' => array('No')
   )         
);

$result = $client->call("GetCampaignsListFilter", array('params' => $params));
err($client);
if($oO == 1)
{
	foreach ($result as $character) {
	echo "<a href='?idc=".$character["CampaignID"]."'>".$character["Name"]."(".$character["CampaignID"].")</a>"."<br/>\r\n";    

	   
	}

echo "<hr/><br />";
}
else
{

echo '
<form method="post">
<label>Login:</label><input type="text" name="login">
<label>Password:</label><input type="password" name="pass">
<input type="submit" value="Login">
</form>
';	
	
	
}	
	
if(isset($_GET['idc']))
{
echo '
<table width="692" border="1" cellpadding="10" cellspacing="0">
    <tr>
    <th width="281">Стратегия</th>
    <th width="60">Лимит</th>
    <th width="60" rowspan="2">
      <input type="button" name="button" id="buttonSet" value="Применить для всех" onclick="setparam()"/>    </th>
  </tr>
  <tr>
    <td>
      <input type="radio" name="radio" class="pradio1" id="radio" value="radio" /> 1-го спецразмещения<br/>      
      <input type="radio" name="radio" class="pradio2" id="radio" value="radio" /> вход в спецразмещение MAX<br/>      
      <input type="radio" name="radio" class="pradio22" id="radio" value="radio" /> вход в спецразмещение MIN<br/>      
      <input type="radio" name="radio" class="pradio3" id="radio" value="radio" /> 1-го места <br/>      
      <input type="radio" name="radio" class="pradio4" id="radio" value="radio" /> вход в гарантированные показы<br/>      
      <input type="radio" name="radio" class="pradio5" id="radio" value="radio" /> Лучшее за мои деньги    </td>
    <td>
      <input name="textfield" class="pprice" type="text" id="textfield" size="10"/>    </td>
  </tr>
</table>  

<form action="" method="post">
<input name="save" value="Сохранить" type="submit" />
<input name="update" value="Просчитать" type="submit" />
 
<table border="1" cellspacing="0" cellpadding="10">
    <tr>
    <th>Ключевик</td>
    <th width="295">Цены</th>
    <th>Все цены</th>
     <th>Ставка</th>   
    <th width="281">Стратегия</th>
    <th>Лимит</th> 
    <th>Ноывя Ставка</th> 
  </tr>
';    
    
       $params = array(
       'CampaignIDS' => array($_GET['idc']),
       'GetPhrases'=>'WithPrices',
       'Filter' => array(
          'StatusArchive' => array('No'),      
          'IsActive' => array('Yes')      
          )  
       );

    $result2 = $client->call("GetBanners", array('params' => $params));
    err($client);
    foreach ($result2 as $character) {        
        foreach ($character["Phrases"] as $character2) {
            if(!isset($character2["Prices"])) continue;
           $tmax=$character2["PremiumMax"]; 
           $tmin=$character2["PremiumMin"]; 
           $lmax=$character2["Max"]; 
           $lmin=$character2["Min"];
           if (isset($_POST['radio'][$character2["PhraseID"]]))
           $newprice=strateg($_POST['radio'][$character2["PhraseID"]],$tmax,$tmin,$lmax,$lmin,$character2["Prices"],$_POST['price'][$character2["PhraseID"]]);
           if (isset($_POST['save'])&&$newprice!=$character2["Price"]) $newprice.="(".newprice($character2["CampaignID"],$character2["BannerID"],$character2["PhraseID"],$newprice).")";  
            echo '
              <tr>
                <td>'.$character2["Phrase"].'</td>
                <td>цена 1-го спецразмещения <b>'.(isset($character2["PremiumMax"])?$character2["PremiumMax"]:"").'</b><br/>
                    вход в спецразмещение <b>'.(isset($character2["PremiumMin"])?$character2["PremiumMin"]:"").'</b><br/>
                    цена 1-го места <b>'.(isset($character2["Max"])?$character2["Max"]:"").'</b><br/>
                    вход в гарантированные показы <b>'.(isset($character2["Min"])?$character2["Min"]:"").'</b><br/></td>    
                <td>';
                $i=0;
                foreach ($character2["Prices"] as $price)
                {
                    echo "<span".($character2["Price"]==$price?" style='color:red'":"").">".$price."<b>";
                    if($price==$tmax) echo " 1 спец";
                    else
                    if($price==$tmin) echo " 3 спец";
                    else
                    if(isset($character2["Prices"][$i+1])&&$character2["Prices"][$i+1]==$tmin) echo " 2 спец"; 
                    if($price==$lmax) echo " 1 прав";
                    if($price==$lmin) echo " гарантия";
                    
                    $i++;
                    echo'</b></span><br />';
                }
                echo '</td>
                <td>'.$character2["Price"].'</td>
                <td>
                  <input type="radio" name="radio['.$character2["PhraseID"].']" class="radio1" id="radio'.$character2["PhraseID"].'" value="1" '.(isset($_POST['radio'][$character2["PhraseID"]])&&$_POST['radio'][$character2["PhraseID"]]=="1"?'checked="checked"':'').' /> 1 спецразмещение<br/>      
                  <input type="radio" name="radio['.$character2["PhraseID"].']" class="radio2" id="radio'.$character2["PhraseID"].'" value="2" '.(isset($_POST['radio'][$character2["PhraseID"]])&&$_POST['radio'][$character2["PhraseID"]]=="2"?'checked="checked"':'').' /> вход в спецразмещение MAX<br/>      
                  <input type="radio" name="radio['.$character2["PhraseID"].']" class="radio22" id="radio'.$character2["PhraseID"].'" value="22" '.(isset($_POST['radio'][$character2["PhraseID"]])&&$_POST['radio'][$character2["PhraseID"]]=="22"?'checked="checked"':'').' /> вход в спецразмещение MIN<br/>      
                  <input type="radio" name="radio['.$character2["PhraseID"].']" class="radio3" id="radio'.$character2["PhraseID"].'" value="3" '.(isset($_POST['radio'][$character2["PhraseID"]])&&$_POST['radio'][$character2["PhraseID"]]=="3"?'checked="checked"':'').' /> 1 справа <br/>      
                  <input type="radio" name="radio['.$character2["PhraseID"].']" class="radio4" id="radio'.$character2["PhraseID"].'" value="4" '.(isset($_POST['radio'][$character2["PhraseID"]])&&$_POST['radio'][$character2["PhraseID"]]=="4"?'checked="checked"':'').' /> вход в гарантию<br/>      
                  <input type="radio" name="radio['.$character2["PhraseID"].']" class="radio5" id="radio'.$character2["PhraseID"].'" value="5" '.(isset($_POST['radio'][$character2["PhraseID"]])&&$_POST['radio'][$character2["PhraseID"]]=="5"?'checked="checked"':'').' /> лучшее за мои деньги</td>
                
                <td>
                  <input name="price['.$character2["PhraseID"].']" class="price" type="text" id="price'.$character2["PhraseID"].'" size="10" value="'.(isset($_POST['price'][$character2["PhraseID"]])?$_POST['price'][$character2["PhraseID"]]:'').'"/>
                </td>                
                <td>
                  '.(isset($_POST['radio'][$character2["PhraseID"]])?$newprice:"").'
                </td>
                
              </tr>
            ';
            if (isset($_POST['radio'][$character2["PhraseID"]])) {
                $xml=addkw($xml,$character2["CampaignID"],$character2["BannerID"],$character2["PhraseID"],$_POST['price'][$character2["PhraseID"]],$_POST['radio'][$character2["PhraseID"]]);
            
            }
            //echo $character2["Phrase"]."(".$character2["Price"].")"."<br />";
            //var_dump($character2["Prices"]);
           // echo "<br />";   
        }
    }

    echo '</table><input name="seve" value="Сохранить" type="submit" />  </forma>';
     if (isset($_POST['radio'])){
    
    
      $fp = fopen("basa.xml", 'w+');
      fwrite($fp, $xml->asXML());
      fclose($fp); 
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
   //$POST=array('price'=>array(),'radio'=>array());
   //$POST['price']=array();
   //$POST['radio']=array();
   $POS=null;
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
//$result = $client->call("GetBanners", array('params' => $params));
 
 /* DEBUG 
 
 if ($client->fault) {

    echo 'Error: ';
    

} else {

    // check result
    $err_msg = $client->getError();
    if ($err_msg) {
        // print error msg
        echo 'Error: '.$err_msg;
    } else {
        // print result
        echo 'Result: ';
        print_r($result);
    }

}


 
 
 

$params = array(
   'CampaignID' => 2006391,
   'Mode' => 'Wizard',
   'PriceBase' => 'pmin',
   'ProcBase' => 'value',
   'Proc' => 0,
   'MaxPrice' => 1.5,
   'PhrasesType' => 'Search'
);

 $result = $client->call("SetAutoPrice", array('params' => $params));
print("Create new company: ");
if ($client->fault) {
    $newCampaignId = 0;
    print "SOAP Fault: ";
} else {
    $newCampaignId = $result;
    echo "$result";
}
 
// print request & response
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';

// Debug messages
echo '<h2>DEBUG</h2>';
echo '<pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';

/* END */

?>

 </body>

</html>

