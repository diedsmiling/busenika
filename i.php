<?
exit();
set_time_limit(600); 

error_reporting (E_ALL);


#echo getmypid();

/*
 Класс реализован без обработки ошибок и исключений
*/
class as_img
{
  /* значение переменных по умолчанию*/
  private $Pth_file  = 'as_dirlist.ini';   /* файл с перечнем путей */
  private $Exc_file  = 'as_exception.ini'; /* файл с исключениями */
  private $Mask_file = "logo.png";         /* PNG файл накладываемый на оригинал */

  private $Exc_Val   = '';                 /* регулярное выражение маски исключения файлов */

  private $Dir_Arr   = array();            /* массив обрабатываемых директорий */

  private $Opasity   = 50;                 /* степень непрозрачности картинки */
  private $CRatio    = 80;                 /* Степень сжатия изображения в % */
  private $Prefix    = '';                 /* префикс к обработанным файлам */
  private $Logging   = 1;                 /* Ведём ли лог */


  /*
   позиция вывода лого - см 106 строку!

   минимальная ширина изображения, на которое накдладывать лого - 100 строка

   публичный метод вызывает конструктор класса и возвращает объект

   принимает следующий набор аргументов:

   $Pth    - название файла с путями к изображениям для обработки
   $Exc    - название файла с регулрным выражением исключений
   $Mask   - название накладываемого PNG файла

   $Ops    - степень прозрачности накладываемого изображения
   $CRatio - степень сжатия JPEG после наложения

   $log    -  ведем ли лог
   (в случае неудачи возвращает ошибку)

  */

  public function init($Pth, $Exc, $Mask, $Prefix, $Ops, $CRatio, $log)
  {
    return new self($Pth, $Exc, $Mask, $Prefix, $Ops, $CRatio, $log);
  } 




  private function __construct( $Pth = self::Pth_file, $Exc = self::Exc_file, $Mask = self::Mask_file, $Prefix = self::Prefix, $Ops = self::Opasity, $CRatio = self::CRatio, $log = self::Logging)
  {
    if(!is_null($Pth))    { $this->Pth_file  = $Pth; }
    if(!is_null($Exc))    { $this->Exc_file  = $Exc; }
    if(!is_null($Mask))   { $this->Mask_file = $Mask; }
    if(!is_null($Prefix)) { $this->Prefix    = $Prefix; }

    if(!is_null($Ops))    { $this->Opasity   = $Ops; }
    if(!is_null($CRatio)) { $this->CRatio    = $CRatio; }
    if(!is_null($log))    { $this->Logging   = $Log; }
  }



  public function Merge(){

    $this-> Get_exception_mask();
    $this-> Load_dir_list();

    $_SERVER['DOCUMENT_ROOT']."/".$this->Mask_file;
    $image2 = @imagecreatefrompng ($_SERVER['DOCUMENT_ROOT']."/".$this->Mask_file); // Это накладываемая картинка

    /* получаем линейные размеры накладываемого изображения */
    $im2x = imagesx($image2); // ширина
    $im2y = imagesy($image2); // высота


    if($this->Logging==1){
      $fp = fopen("img.log","w"); // Открываем файл лога
    }

    while(list($key,$single_dir) = each($this->Dir_Arr))
    {

      $arr = $this->Get_dir_files($single_dir);

      $path = $_SERVER['DOCUMENT_ROOT']."/".$single_dir;

      while(list($k,$v) = each($arr)){

        $image1 = ImageCreateFromJPEG($single_dir."/".$v);

        /* получаем линейные размеры основного изображения */
        $im1x = imagesx($image1); // ширина
        $im1y = imagesy($image1); // высота


        /* ширина накладываемого изображения должна быть больше ширины основного изображения */
        /* минимальная ширина изображения, на которое накладывается лого */
        if ($im1x >= $im2x AND $im1x > 150)
        {

/* ВЫБОР ПОЗИЦИИ ВЫВОДА ЛОГОТИПА - РАСКОМЕНТИРОВАТЬ НУЖНОЕ И ЗАКОМЕНТИРОВАТЬ НЕНУЖНОЕ!!! */

/* позиция логотипа - посередине */

        /* находим точку Х и У вывода изображения посередине */


        $X=intval(($im1x-$im2x)/2);
        if($X<0){$X=0;}
        $Y=intval(($im1y-$im2y)/2);
        if($Y<0){$Y=0;}




/* позиция логотипа - снизу справа */

        /* находим точку Х и У вывода изображения справа снизу - 10 пикселей отступ от краев */
        
        
/*        $X=$im1x-$im2x-10; /* 10 это отступ от края по оси X */
/*        if($X<0){$X=0;}
/*        $Y=$im1y-$im2y-10; /* 10 это отступ от края по оси Y */
/*        if($Y<0){$Y=0;}
*/




        $this->imagecopymerge_alpha($image1, $image2, $X, $Y, 0, 0, $im2x, $im2y, $this->Opasity); 

        $pathtofile = $path."/".$this->Prefix.$v;

        if(ImageJPEG($image1, $pathtofile, $this->CRatio)) /* Сохраняем готовую картинку */
        {
          $logmsg ="Файл $v был записан<br>\n"; /* сработало */
        }
        else
        {
         /* Отказ в записи файла */
          $logmsg  = $pathtofile."<font color=red><b>Error ! Файл не может быть записан.</b></font><br>\n";
        }

        /* Тут пишем в лог результат обработки файла */
        if($this->Logging==1){
          fwrite($fp, $logmsg);
        }
        ImageDestroy($image1);

        }

      }
    }

    if($this->Logging==1){
      fclose($fp); // Закрываем файл лога
    }

    ImageDestroy($image2);
    return $this->Dir_Arr;
  }




  /* 
  вынимает значение исключения из текстового файла
  */
  private function Get_exception_mask()
  {
    /* Получаем значение регулярного выражения */
    if ($f = fopen( $this->Exc_file, 'r')) do
    {
      $this->Exc_Val=trim(fgets($f)); // регулярное выражение маски исключения файлов
    }while (!feof($f));
      fclose($f); 
  }




  private function Load_dir_list()
  {
   /* Открываем файл путей и для всех путей вызываем функцию получения файлов */
    if ($f = fopen($this->Pth_file, 'r')) do
    {
      $single_dir = trim(fgets($f));
      $this->Dir_Arr[] = $single_dir;
    }while (!feof($f));
    fclose($f);
  }



  /* 
  создает массив файлов подпадающих под условия обработки
  */

  private function Get_dir_files($single_dir)
  {

    $a = scandir($_SERVER['DOCUMENT_ROOT']."/".$single_dir);
    $b = $this->Matched($a, $this->Exc_Val);

    return $b;
  }




  /* 
  формирует и возвращает массив фалов данной директории надлежащий обработке 
  */

  private function Matched($arr, $exc)
  {
    while(list($key,$val) = each($arr))
    {
      if(preg_match($exc, $val))
      {
      }else{
        $ar_ret[]=$val;
      }
    }
   return $ar_ret;
  }





  /* 
   Корректно обрабатывает PNG файлы учитывая альфа канал
  */

  function Imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 

    if(!isset($pct)){ 
        return false; 
    } 

    $pct /= 100; 
    // Получаем ширину и высоту картинки
    $w = imagesx( $src_im ); 
    $h = imagesy( $src_im ); 
    // Turn alpha blending off 
    imagealphablending( $src_im, false ); 
    // Ищем наитболее прозрачный пиксел картинки (С наименьшей альфой) 
    $minalpha = 127; 
    for( $x = 0; $x < $w; $x++ ) 
    for( $y = 0; $y < $h; $y++ ){ 
        $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF; 
        if( $alpha < $minalpha ){ 
            $minalpha = $alpha; 
        } 
    } 
    //пробегаем каждый пиксел картинки изменяем альфу для каждого
    for( $x = 0; $x < $w; $x++ ){ 
        for( $y = 0; $y < $h; $y++ ){ 
            //Получаем текущее значение альфа (represents the TANSPARENCY!) 
            $colorxy = imagecolorat( $src_im, $x, $y ); 
            $alpha = ( $colorxy >> 24 ) & 0xFF; 
            // вычисляем новую альфа
            if( $minalpha !== 127 ){ 
                $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha ); 
            } else { 
                $alpha += 127 * $pct; 
            } 
            //получаем значение цвета новой альфа
            $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha ); 
            //устанавливаем пиксел с вновым цветом и прозрачностью
            if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){ 
                return false; 
            } 
        } 
    } 
    // Это копирование картинки
    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h); 
  } 

}


/* 	-	-	-	-	-	-	-	-	-	-	-	*/
//phpinfo();


 $a = as_img::init();

 print_r($a->Merge());

?>
<hr>