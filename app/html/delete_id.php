<?php
$DATA = base64_decode($_GET['data']);
$LIST = explode(",",substr($DATA,1,strlen($DATA)-2));
$NEW = file_get_contents("data");
foreach ($LIST as $ITEM) {
    $NEW = str_replace($ITEM,"",$NEW);
}
$NEW = str_replace(",,","",$NEW);
$NEW = str_replace("[,","[",$NEW);
$NEW = str_replace(",]","]",$NEW);
$NEW = str_replace("}{","},{",$NEW);
file_put_contents("data",$NEW);
echo json_encode($NEW); 
$CONF = json_decode(file_get_contents("conf"));
$CMD = "mosquitto_pub -h {$CONF->local_mosquitto} -t /update_list/ -m ''";
shell_exec($CMD);
die();