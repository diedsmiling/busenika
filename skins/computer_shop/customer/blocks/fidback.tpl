{php}
//sorry for bad code<?

	function __rpHash($value) { 
		$hash = 5381; 
		$value = strtoupper($value); 
		for($i = 0; $i < strlen($value); $i++) { 
			$hash = (leftShift32($hash, 5) + $hash) + ord(substr($value, $i)); 
		} 
		return $hash; 
	} 
	 

	function leftShift32($number, $steps) { 
		// convert to binary (string) 
		$binary = decbin($number); 
		// left-pad with 0's if necessary 
		$binary = str_pad($binary, 32, "0", STR_PAD_LEFT); 
		// left shift manually 
		$binary = $binary.str_repeat("0", $steps); 
		// get the last 32 bits 
		$binary = substr($binary, strlen($binary) - 32); 
		// if it's a positive number return it 
		// otherwise return the 2's complement 
		return ($binary{0} == "0" ? bindec($binary) : 
			-(pow(2, 31) - bindec(substr($binary, 1)))); 
	} 

if (isset($_GET['name1']))
{
	
	$captcha = __rpHash($_GET['defaultReal']);
var_dump($captcha);
	if($captcha == $_GET['defaultRealHash'])
	{
		echo "send";
			sendMail1("witalik@mail.ru",$_GET['e-mail1'],"Вопрос с контактов","Имя: ".$_GET['name1']."<br /> Почта: ".$_GET['e-mail1']."<br /> Телефон: ".$_GET['telephon1']."<br /> Сообщение: ".$_GET['message1']."<br />");
			echo "<div class='sendtest'>Сообщение отправлено</div>";
	}
	else
	{	
		echo "<div class='sendtest'>Неверный код!</div>";
	}	
}
else
echo '
<link href="/skins/computer_shop/customer/jquery.realperson.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery.realperson.js"></script>
<script type="text/javascript">
$(function() {
	$("#defaultReal").realperson({regenerate: ""});
});
</script>
<link href="/skins/computer_shop/customer/styles.css" rel="stylesheet" type="text/css" />

 <form id="form1" name="form1" method="get" action="http://korzin.net/index.php">
            

              <label class="need" for="name1">Ваше имя: </label>  
              <input type="text" name="name1" id="name1"  size="10">  <br/>
              <label class="need" for="e-mail1">Эл.почта: </label>    
              <input type="text" name="e-mail1" id="e-mail1"  size="10">  <br/>
              <label for="telephon1">Телефон: </label>                   
              <input type="text" name="telephon1" id="telephon1" size="10"> <br/>
<label class="need" for="message1">Текст сообщения: </label>    <br/>
<textarea valid="" name="message1" id="message1"></textarea> <br/>
              <input type="hidden" value="pages.view" name="dispatch">
              <input type="hidden" value="1" name="page_id">
              <br/>
              <label for="telephon1">Введите код отображенный ниже: </label>  
              <input type="text" id="defaultReal" style="margin-top: 10px;"  name="defaultReal"  class="input_name"><br/>
              <input type="submit"  value="Отправить" name="sub1">
              
                         </form>
';   

{/php}
