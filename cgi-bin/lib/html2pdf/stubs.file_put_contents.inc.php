<?php

function file_put_contents($filename, $data) {
  $file = fopen($filename, 'w');
  fwrite($file, $data);
  fclose($file);
  @chmod($filename, DEFAULT_FILE_PERMISSIONS);
}

?>