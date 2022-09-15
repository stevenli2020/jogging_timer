<?php
$F=$_GET['i'];
$CSV=$_GET['d'];
if($F==""){
	echo "NAK";
	die();
}
file_put_contents("./data/".$F.".csv",$CSV);
echo "OK ".$F;