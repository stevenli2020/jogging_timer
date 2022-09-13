<?php
$DATA = json_decode(base64_decode($_GET['data']));
// die(base64_decode($_GET['data']));
$USER = $DATA -> Old_User;
$PWD = $DATA -> Old_Pwd;
$NEW_USER = $DATA -> New_User;
$NEW_PWD = $DATA -> New_Pwd;
// die($USER."pwd".$PWD);
$HPWD = sha1($USER."pwd".$PWD);
$CONF_OLD = json_decode(file_get_contents("conf"));
if ($CONF_OLD->user_hpwd !== $HPWD){
	die($CONF_OLD->user_hpwd." -- ".$HPWD."\nIncorrect User/Password!");
}
$NEW_HPWD = sha1($NEW_USER."pwd".$NEW_PWD);
$CONF_OLD->user_id = $NEW_USER;
$CONF_OLD->user_hpwd = $NEW_HPWD;
file_put_contents("conf",json_encode($CONF_OLD));
die("OK");