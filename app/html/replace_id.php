<?php
$NEW = base64_decode($_GET['data']);
file_put_contents("data",$NEW);
$CONF = json_decode(file_get_contents("conf"));
$CMD = "mosquitto_pub -h {$CONF->local_mosquitto} -t /update_list/ -m ''";
shell_exec($CMD);
die("OK");