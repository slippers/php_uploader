<?php

session_start();

require_once './libs/adapter.php';

Main();

//build the page
$fileList = GetAdapterFileList();

$latestIssue = array_shift($fileList);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    
<p>Latest Issue</p>
<div>
<ul><li>
<?php
echo fileHyperLink($latestIssue);
?>
</li></ul>
</div>

<p> Archive </p>
<div>
<?php
echo makeul($fileList, 'fileHyperLink');
?>
</div>
	
<?php if(Authenticated()): ?>
<div><h2>Add</h2>
	<form enctype="multipart/form-data" action="<? echo $_SERVER['PHP_SELF']; ?>" method="POST">
	    <input type="hidden" name="function" value="UploadFile" />
	    <!-- MAX_FILE_SIZE must precede the file input field -->
	    <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
	    <!-- Name of input element determines name in $_FILES array -->
	    Upload this file: <input name="adapterFile" type="file" />
        <input type="submit" value="Upload File" />
	</form>
</div>
<div><h2>Copy</h2>
	<form enctype="multipart/form-data" action="<? echo $_SERVER['PHP_SELF']; ?>" method="POST">
	    <input type="hidden" name="function" value="CloneLastAdapterIssue" />
	    <input type="text" name="newFileName">
        <input type="submit" value="Copy" />
	</form>
</div>
<div><h2>Log out</h2>
	<form enctype="multipart/form-data" action="<? echo $_SERVER['PHP_SELF']; ?>" method="POST">
	    <input type="hidden" name="function" value="LogOut" />
	    <input type="submit" value="Log Out" />
	</form>
</div>
<?php else: ?>
<div><h2>Log On</h2>
	<form enctype="multipart/form-data" action="<? echo $_SERVER['PHP_SELF']; ?>" method="POST">
	    <input type="hidden" name="function" value="Authenticate" />
	    Username:<input type="text" name="username">
	    Password:<input type="password" name="password">
	    <input type="submit" value="Log On" />
	</form>
</div>
<?php endif; ?>

</body>
</html>
