<?php
$NEW = base64_decode($_GET['data']);
// die($NEW);
$DATA = file_get_contents("data");
$DATA = str_replace("]",",".$NEW."]",$DATA);
// die($DATA);
file_put_contents("data",$DATA);
$CONF = json_decode(file_get_contents("conf"));
$CMD = "mosquitto_pub -h {$CONF->local_mosquitto} -t /update_list/ -m ''";
shell_exec($CMD);
die("OK");