<?php
$CONF = json_decode(base64_decode($_GET['data']));
// print_r($CONF);die();
$CONF_OLD = json_decode(file_get_contents("conf"));
foreach ($CONF as $key => $value) {
	$CONF_OLD -> $key = $value;
}
// echo json_encode($CONF_OLD);die();
file_put_contents("conf",json_encode($CONF_OLD));
$CMD = "mosquitto_pub -h {$CONF_OLD->message_host} -p {$CONF_OLD->message_port} -t /COMMAND/".time()." -m 'docker restart raw wiegand filter message'";
shell_exec($CMD);
sleep(1);
die("OK");