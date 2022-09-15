<?php
$S="";
if($_GET['s']!=""){
	$S=$_GET['s'];
	file_put_contents("session_id",$S);
}else{
	unlink("session_id");
}

echo "OK ".$_GET['s'];