<?php
    $DeletedFolder='/var/cache';

RemoveDir($_SERVER['DOCUMENT_ROOT'].$DeletedFolder);

  function RemoveDir($path)
{
    if(file_exists($path) && is_dir($path))
    {
        $dirHandle = opendir($path);
        while (false !== ($file = readdir($dirHandle))) 
        {
            if ($file!='.' && $file!='..')// исключаем папки с назварием '.' и '..' 
            {
                $tmpPath=$path.'/'.$file;
                chmod($tmpPath, 0777);
                
                if (is_dir($tmpPath))
                  {  // если папка
                  if (strpos($tmpPath,"pfilters")>0) {                    
                      RemoveDir($tmpPath);

                      echo  $tmpPath."<br/>";    
                      rmdir($tmpPath);
                      }
                   } 
                  else 
                  { 
                      if(file_exists($tmpPath))
                    {
                        // удаляем файл 
                          unlink($tmpPath);
                    }
                  }
            }
        }
        closedir($dirHandle);
        
        // удаляем текущую папку
        if(file_exists($path))
        {    
          
            if (strpos($patch,"pfilters")>0) {
              
            rmdir($path);
            }
        }
    }
    else
    {
        echo "Удаляемой папки не существует или это файл!";
    }
}
?>