<?php
$T=0;
if($_GET['t']!=""){$T=(int)$_GET['t'];}
file_put_contents("blocking_time",$T);
echo "OK ".$_GET['t'];