<?
exit();
set_time_limit(600); 

error_reporting (E_ALL);


#echo getmypid();

/*
 ����� ���������� ��� ��������� ������ � ����������
*/
class as_img
{
  /* �������� ���������� �� ���������*/
  private $Pth_file  = 'as_dirlist.ini';   /* ���� � �������� ����� */
  private $Exc_file  = 'as_exception.ini'; /* ���� � ������������ */
  private $Mask_file = "logo.png";         /* PNG ���� ������������� �� �������� */

  private $Exc_Val   = '';                 /* ���������� ��������� ����� ���������� ������ */

  private $Dir_Arr   = array();            /* ������ �������������� ���������� */

  private $Opasity   = 50;                 /* ������� �������������� �������� */
  private $CRatio    = 80;                 /* ������� ������ ����������� � % */
  private $Prefix    = '';                 /* ������� � ������������ ������ */
  private $Logging   = 1;                 /* ���� �� ��� */


  /*
   ������� ������ ���� - �� 106 ������!

   ����������� ������ �����������, �� ������� ������������ ���� - 100 ������

   ��������� ����� �������� ����������� ������ � ���������� ������

   ��������� ��������� ����� ����������:

   $Pth    - �������� ����� � ������ � ������������ ��� ���������
   $Exc    - �������� ����� � ��������� ���������� ����������
   $Mask   - �������� �������������� PNG �����

   $Ops    - ������� ������������ �������������� �����������
   $CRatio - ������� ������ JPEG ����� ���������

   $log    -  ����� �� ���
   (� ������ ������� ���������� ������)

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
    $image2 = @imagecreatefrompng ($_SERVER['DOCUMENT_ROOT']."/".$this->Mask_file); // ��� ������������� ��������

    /* �������� �������� ������� �������������� ����������� */
    $im2x = imagesx($image2); // ������
    $im2y = imagesy($image2); // ������


    if($this->Logging==1){
      $fp = fopen("img.log","w"); // ��������� ���� ����
    }

    while(list($key,$single_dir) = each($this->Dir_Arr))
    {

      $arr = $this->Get_dir_files($single_dir);

      $path = $_SERVER['DOCUMENT_ROOT']."/".$single_dir;

      while(list($k,$v) = each($arr)){

        $image1 = ImageCreateFromJPEG($single_dir."/".$v);

        /* �������� �������� ������� ��������� ����������� */
        $im1x = imagesx($image1); // ������
        $im1y = imagesy($image1); // ������


        /* ������ �������������� ����������� ������ ���� ������ ������ ��������� ����������� */
        /* ����������� ������ �����������, �� ������� ������������� ���� */
        if ($im1x >= $im2x AND $im1x > 150)
        {

/* ����� ������� ������ �������� - ���������������� ������ � ��������������� ��������!!! */

/* ������� �������� - ���������� */

        /* ������� ����� � � � ������ ����������� ���������� */


        $X=intval(($im1x-$im2x)/2);
        if($X<0){$X=0;}
        $Y=intval(($im1y-$im2y)/2);
        if($Y<0){$Y=0;}




/* ������� �������� - ����� ������ */

        /* ������� ����� � � � ������ ����������� ������ ����� - 10 �������� ������ �� ����� */
        
        
/*        $X=$im1x-$im2x-10; /* 10 ��� ������ �� ���� �� ��� X */
/*        if($X<0){$X=0;}
/*        $Y=$im1y-$im2y-10; /* 10 ��� ������ �� ���� �� ��� Y */
/*        if($Y<0){$Y=0;}
*/




        $this->imagecopymerge_alpha($image1, $image2, $X, $Y, 0, 0, $im2x, $im2y, $this->Opasity); 

        $pathtofile = $path."/".$this->Prefix.$v;

        if(ImageJPEG($image1, $pathtofile, $this->CRatio)) /* ��������� ������� �������� */
        {
          $logmsg ="���� $v ��� �������<br>\n"; /* ��������� */
        }
        else
        {
         /* ����� � ������ ����� */
          $logmsg  = $pathtofile."<font color=red><b>Error ! ���� �� ����� ���� �������.</b></font><br>\n";
        }

        /* ��� ����� � ��� ��������� ��������� ����� */
        if($this->Logging==1){
          fwrite($fp, $logmsg);
        }
        ImageDestroy($image1);

        }

      }
    }

    if($this->Logging==1){
      fclose($fp); // ��������� ���� ����
    }

    ImageDestroy($image2);
    return $this->Dir_Arr;
  }




  /* 
  �������� �������� ���������� �� ���������� �����
  */
  private function Get_exception_mask()
  {
    /* �������� �������� ����������� ��������� */
    if ($f = fopen( $this->Exc_file, 'r')) do
    {
      $this->Exc_Val=trim(fgets($f)); // ���������� ��������� ����� ���������� ������
    }while (!feof($f));
      fclose($f); 
  }




  private function Load_dir_list()
  {
   /* ��������� ���� ����� � ��� ���� ����� �������� ������� ��������� ������ */
    if ($f = fopen($this->Pth_file, 'r')) do
    {
      $single_dir = trim(fgets($f));
      $this->Dir_Arr[] = $single_dir;
    }while (!feof($f));
    fclose($f);
  }



  /* 
  ������� ������ ������ ����������� ��� ������� ���������
  */

  private function Get_dir_files($single_dir)
  {

    $a = scandir($_SERVER['DOCUMENT_ROOT']."/".$single_dir);
    $b = $this->Matched($a, $this->Exc_Val);

    return $b;
  }




  /* 
  ��������� � ���������� ������ ����� ������ ���������� ���������� ��������� 
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
   ��������� ������������ PNG ����� �������� ����� �����
  */

  function Imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 

    if(!isset($pct)){ 
        return false; 
    } 

    $pct /= 100; 
    // �������� ������ � ������ ��������
    $w = imagesx( $src_im ); 
    $h = imagesy( $src_im ); 
    // Turn alpha blending off 
    imagealphablending( $src_im, false ); 
    // ���� ��������� ���������� ������ �������� (� ���������� ������) 
    $minalpha = 127; 
    for( $x = 0; $x < $w; $x++ ) 
    for( $y = 0; $y < $h; $y++ ){ 
        $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF; 
        if( $alpha < $minalpha ){ 
            $minalpha = $alpha; 
        } 
    } 
    //��������� ������ ������ �������� �������� ����� ��� �������
    for( $x = 0; $x < $w; $x++ ){ 
        for( $y = 0; $y < $h; $y++ ){ 
            //�������� ������� �������� ����� (represents the TANSPARENCY!) 
            $colorxy = imagecolorat( $src_im, $x, $y ); 
            $alpha = ( $colorxy >> 24 ) & 0xFF; 
            // ��������� ����� �����
            if( $minalpha !== 127 ){ 
                $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha ); 
            } else { 
                $alpha += 127 * $pct; 
            } 
            //�������� �������� ����� ����� �����
            $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha ); 
            //������������� ������ � ������ ������ � �������������
            if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){ 
                return false; 
            } 
        } 
    } 
    // ��� ����������� ��������
    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h); 
  } 

}


/* 	-	-	-	-	-	-	-	-	-	-	-	*/
//phpinfo();


 $a = as_img::init();

 print_r($a->Merge());

?>
<hr>