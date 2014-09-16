<?php
	$objectName = isset($_POST['HistoryStorageObjectName']) ? $_POST['HistoryStorageObjectName'] : '';
	$historyKey = isset($_POST['HistoryKey']) ? $_POST['HistoryKey'] : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<body>
<script>
<?php
if (strlen($historyKey) > 0)
{
	echo 'parent.'.$objectName.'.ProcessHistory(\''.$historyKey.'\');';
}
?>
</script>
</body>
</html> 