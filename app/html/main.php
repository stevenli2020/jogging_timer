<?php
$CONF = json_decode(file_get_contents("./conf"));
if($CONF->token!==sha1("rnd-".$CONF->user_hpwd.$_COOKIE['rnd'])){
	// echo sha1("rnd-".$CONF->user_hpwd.$_COOKIE['rnd'])." - ".$CONF->token;die();
    ob_start();
    header('Location: index.php');
    ob_end_flush();
    die();
}
unset($CONF->user_hpwd);unset($CONF->user_id);
unset($CONF->wiegand_d0);unset($CONF->wiegand_d1);
$COMMAND_TOPIC = "/COMMAND/".substr(sha1($CONF->host_id."raw"),0,8)."/";	
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>RFID ADAPTOR</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="./layui/css/layui.css"  media="all">
  <link rel="stylesheet" href="./local.css"  media="all">
</head>
<body class="layui-bg-gray"> 
<ul class="layui-nav layui-bg-green" style="text-align:end">
  <li class="layui-nav-item"><a href="./index.php">LOGOUT</a></li>
  <div style="position:absolute;top:20px;left:30%;font-size:20px"></div>
</ul>      
<div class="layui-tab layui-tab-card" lay-filter="tabs">
  <ul class="layui-tab-title">
	<li class="layui-this">SYSTEM STATUS</li>
    <li>ID FILTER</li>
    <li>SYSTEM SETTINGS</li>
    <li>CHANGE PASSWORD</li>
	<li>DEBUG MODE</li>
  </ul>
  <div class="layui-tab-content">
    <div class="layui-tab-item layui-show">
		<div style="padding: 5px 20px 5px 20px; background-color: #F2F2F2;">
		  <div class="layui-row layui-col-space15">
			<div id="RESTART_CARD" class="layui-col-md12" style="-webkit-transition:.5s ease-in;transition:.5s ease-in;padding:0 7.5px;overflow:hidden;">
				 <div class="layui-card" style="text-align:end;">RESTART SERVICES<i class="layui-icon">&#xe602;</i> &nbsp;&nbsp;&nbsp;<button id="RESTART_BTN" type="button" class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe9aa;</i></button></div>
			</div>
			<div id="STATUS_CARD" class="layui-col-md4">
			  <div class="layui-card">
				<div class="layui-card-header">READER CONNECTION</div>
				<div id="STCB" class="layui-card-body layui-bg-red lighten">
					<p id="STCBT" style="font-size:1.2em">STATUS: DISCONNECTED</p>
				</div>
			  </div>
			  <div class="layui-card" id="STATUS_FILTER">
				<div class="layui-card-header">SYSTEM SERVICES</div>
				<div id="STRB" class="layui-card-body layui-bg-red lighten">
					<p id="STRBT" style="font-size:1.2em">READ TAGS: NOT RUNNING</p>
				</div>
				<div id="STFB" class="layui-card-body layui-bg-red lighten">
					<p id="STFBT" style="font-size:1.2em">ID FILTER: NOT RUNNING</p>
				</div>	
				<div id="STWB" class="layui-card-body layui-bg-red lighten">
					<p id="STWBT" style="font-size:1.2em">WIEGAND: NOT RUNNING</p>
				</div>					
			  </div>	  
			</div>
			<div id="MESSAGE_CARD" class="layui-col-md3">  
				<div class="layui-card" id="MESSAGE_CARD_BKG">
					<div class="layui-card-header">SYSTEM MESSAGES</div>
					<div class="layui-card-body" id="MESSAGE_CARD_BODY" style="padding:5px">
						<div id="LOG_WINDOW" style="margin-top:4px;font-size:0.7em;line-height:1em">
						</div>
					</div>					
				</div>
			</div>			
			<div id="LIVESCAN_CARD" class="layui-col-md5">
			  <div class="layui-card">
				<div class="layui-card-header"><span id="LIVE_SCAN_TEXT">LIVE SCANS</span><div id="SCAN_BTN" status="SCANNING" style="float:right"><i id="SCAN_BTN_ICON" class="layui-icon layui-icon-pause" style="font-size:1.5em;font-weight:bolder;color:red"></i></div></div>
				<div class="layui-card-body">
					<div id="LIVE_WINDOW" style="margin-top:4px;font-size:1em;line-height:1.2em">
						<br><br><br><br><br><br><br><br><br><br>
					</div>
				</div>
			  </div>
			</div>
			<div class="layui-col-md8" id="WAH_CARD">
			  <div class="layui-card">
				<div class="layui-card-header">SYSTEM HEALTH</div>
				<div class="layui-card-body" style="overflow:auto" id="WAH_CARD_BODY">
				  <div class="layui-col-md6">
					<fieldset>
					  <legend>CPU Temperature</legend>
					  <div class="layui-progress layui-progress-big">
						<div id="STTV" class="layui-progress-bar" style="color:white;padding-right:5px" lay-percent="0%"></div>
					  </div>			  
					</fieldset>	
				  </div>
				  <div class="layui-col-md6">
					<fieldset>
					  <legend>CPU Utilization</legend>
					  <div class="layui-progress layui-progress-big">
						<div id="STCU" class="layui-progress-bar" style="color:white;padding-right:5px" lay-percent="0%"></div>
					  </div>			  
					</fieldset>
				 </div>
				  <div class="layui-col-md6">
					<fieldset>
					  <legend>RAM Utilization</legend>
						<div class="layui-progress layui-progress-big">
						  <div id="STMV" class="layui-progress-bar" style="color:white;padding-right:5px" lay-percent="0%"></div>
						</div>			  
					</fieldset>				  
				  </div>
				  <div class="layui-col-md6">					
					<fieldset>
					  <legend>Disk Usage</legend>
					  <div class="layui-progress layui-progress-big">
						<div id="STDU" class="layui-progress-bar" style="color:white;padding-right:5px" lay-percent="0%"></div>
					  </div>			  
					</fieldset>	
				  </div>
				</div>
			  </div>
			</div>	
			<div class="layui-col-md4" id="MSTATUS_CARD">
			  <div class="layui-card">
				<div class="layui-card-header">MESSAGE SERVER STATUS</div>
				<div class="layui-card-body" style="overflow:auto;top:-10px;" id="MSTATUS_CARD_BODY">
				  	<div id="MS_STATUS" style="margin-top:4px;font-size:1em;line-height:1.2em">
						<table class="layui-table" lay-skin="nob" lay-even="" lay-size="sm">
						  <tbody>
							<tr>
							  <td>Service Uptime(min)</td>
							  <td id="MUPT">N.A.</td>
							</tr>						  
							<tr>
							  <td>Clients Connected</td>
							  <td id="NCLNT">N.A.</td>
							</tr> 
							<tr>
							  <td>Bytes Recvd(1min)</td>
							  <td id="BRCVD">N.A.</td>
							</tr>
							<tr>
							  <td>Bytes Sent(1min)</td>
							  <td id="BSENT">N.A.</td>
							</tr>
							<tr>
							  <td>Bytes Total(1min)</td>
							  <td id="BTOTL">N.A.</td>
							</tr>	
							<tr>
							  <td>Bytes Total(since boot)</td>
							  <td id="BTOTL2">N.A.</td>
							</tr>							
						  </tbody>
						</table>						
					</div>
				</div>
			  </div>
			</div>
			
		  </div>
		</div>			
	</div> 
    <div class="layui-tab-item">
		<table class="layui-hide" id="id_list" lay-filter="id_list" lay-data="{id: 'id_list'}"></table> 
		<script type="text/html" id="toolbar">
		  <div class="layui-btn-container">
			<button class="layui-btn layui-btn-sm" lay-event="add">ADD NEW</button>
			<button class="layui-btn layui-btn-sm" lay-event="del">DELETE SELECTED</button>
		  </div>
		</script>		
	</div>
    <div class="layui-tab-item">
		<form class="layui-form layui-form-pane" action="">
			<fieldset class="layui-elem-field">
			  <legend class="grp_title">DEVICE INFO</legend>
			  <div class="layui-field-box">
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Name</label>
					<div class="layui-input-block">
					  <input id="DN" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
			  </div>
			</fieldset>		
			<fieldset class="layui-elem-field">
			  <legend class="grp_title">RFID READER</legend>
			  <div class="layui-field-box">
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">IP Address</label>
					<div class="layui-input-block">
					  <input id="RIP" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Port</label>
					<div class="layui-input-block">
					  <input id="RPT" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>		  
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Interval</label>
					<div class="layui-input-block">
					  <input id="RINT" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Vendor</label>
					<div class="layui-input-block">
					  <input id="MFR" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Model</label>
					<div class="layui-input-block">
					  <input id="MDL" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Device ID</label>
					<div class="layui-input-block">
					  <input id="DID" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Antenna Mask</label>
					<div class="layui-input-block">
					  <input id="AMSK" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>	
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Read Mode</label>
					<div class="layui-input-block">
					  <input type="radio" name="rmode" value="EPC" title="EPC">
					  <input type="radio" name="rmode" value="TID" title="TID">
					</div>
				  </div>				  
			  </div>
			</fieldset>	
			<fieldset class="layui-elem-field">
			  <legend class="grp_title">WIEGAND SETTINGS</legend>
			  <div class="layui-field-box">
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Interval(ms)</label>
					<div class="layui-input-block">
					  <input id="WINT" type="text" autocomplete="off" class="layui-input" placeholder=" > 300">
					</div>
				  </div>	
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Wiegand Mode</label>
					<div class="layui-input-block">
					  <input type="radio" name="wmode" value="26" title="26">
					  <input type="radio" name="wmode" value="34" title="34">
					  <input type="radio" name="wmode" value="66" title="66">
					</div>
				  </div>				  
			  </div>
			</fieldset>	
			<fieldset class="layui-elem-field">
			  <legend class="grp_title">MESSAGE SERVER</legend>
			  <div class="layui-field-box">
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">IP Address</label>
					<div class="layui-input-block">
					  <input id="SIP" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Port</label>
					<div class="layui-input-block">
					  <input id="SPT" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Web Socket</label>
					<div class="layui-input-block">
					  <input id="WSPT" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>				  
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">User</label>
					<div class="layui-input-block">
					  <input id="MUSR" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Password</label>
					<div class="layui-input-block">
					  <input id="MPWD" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>					  
			  </div>
			</fieldset>	
			<fieldset class="layui-elem-field">
			  <legend class="grp_title">BRIDGING SETTINGS</legend>
			  <div class="layui-field-box">
				<div class="layui-row"><i id="BR_ADD" class="layui-icon layui-icon-add-1" style="font-size:2em;float:right;font-weight:bolder;margin-right:10px;"></i></div>
				<div>
					<table class="layui-table" lay-skin="line" lay-filter="br_list">
					  <colgroup>
					    <col width="20%">
					    <col>
						<col width="5%">
					  </colgroup>					
					  <thead>
						<tr>
						  <th>CONNECTION NAME</th>
						  <th>ADDRESS</th>
						  <th></th>						  
						</tr> 
					  </thead>					
					  <tbody id="BRL"></tbody>
					</table>						
				</div>
			  </div>
			</fieldset>			
			  <div class="layui-form-item">
				<div class="layui-input-block" style="text-align:center;margin:0px;">
				  <button id="CFG_SUB" type="button" class="layui-btn" style="margin:20px">SUBMIT</button>
				  <button id="CFG_RST" type="button" class="layui-btn" style="margin:20px">RESET</button>
				</div>
			  </div>
		</form><br><br><br>
	</div>
	<div class="layui-tab-item">
		<form class="layui-form layui-form-pane" action="">
			<fieldset class="layui-elem-field">
			  <legend class="grp_title">OLD</legend>
			  <div class="layui-field-box">
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">User</label>
					<div class="layui-input-block">
					  <input id="O_USER" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Password</label>
					<div class="layui-input-block">
					  <input id="O_PWD" type="password" autocomplete="off" class="layui-input">
					</div>
				  </div>				  
			  </div>
			</fieldset>	
			<fieldset class="layui-elem-field">
			  <legend class="grp_title">NEW</legend>
			  <div class="layui-field-box">
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">User</label>
					<div class="layui-input-block">
					  <input id="N_USER" type="text" autocomplete="off" class="layui-input">
					</div>
				  </div>
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Password</label>
					<div class="layui-input-block">
					  <input id="N_PWD1" type="password" autocomplete="off" class="layui-input">
					</div>
				  </div>	
				  <div class="layui-form-item">
					<label class="layui-form-label label_text">Password</label>
					<div class="layui-input-block">
					  <input id="N_PWD2" type="password" autocomplete="off" class="layui-input">
					</div>
				  </div>					  
			  </div>
			</fieldset>	
			  <div class="layui-form-item">
				<div class="layui-input-block">
				  <button id="PWD_SUB" type="button" class="layui-btn">SUBMIT</button>
				  <button id="PWD_RST" type="reset" class="layui-btn">RESET</button>
				</div>
			  </div>			
		</form>	
	</div>
	<div class="layui-tab-item" style="visibility:;">
		<div class="layui-card">
		  <input id="CMD_INPUT" type="text" placeholder="Type command here, press enter to send" autocomplete="off" class="layui-input layui-bg-black" style="font-size:1.2em">
		  <div id="CMD_OUTPUT" class="layui-card-body layui-bg-cyan" style="height:calc(100vh - 150px);font-size:1em;font-family:'Courier New',Courier,monospace;overflow:scroll">
			<p style="color:grey">Command results will be shown here</p>
		  </div>
		</div>			
	</div>
  </div>
</div>
<div style="height:0;overflow:hidden;">
<form class="layui-form" action="" id="id_add">
  <div class="layui-form-item layui-form-text">
    <div class="layui-input-block" style="margin-left:10px;margin-right:10px;">
      <textarea id="csv-text" placeholder="Input list of IDs in CSV format.&#10;LOAD: load IDs from a CSV file&#10;APPEND: add to the existing list. &#10;REPLACE: replace the existing list." class="layui-textarea"></textarea>
    </div>
  </div>
  <div class="layui-form-item">
    <div class="layui-input-block" style="margin-left:10px;margin-right:10px;">
	  <button id="add_load" class="layui-btn" type="button" style="padding-left:10px;padding-right:10px">LOAD</button>
      <button id="add_append" class="layui-btn" type="button" style="padding-left:10px;padding-right:10px">APPEND</button>
	  <button id="add_replace" class="layui-btn" type="button" style="padding-left:10px;padding-right:10px">REPLACE</button>
      <button id="add_clear" type="reset" class="layui-btn layui-btn-primary" style="padding-left:10px;padding-right:10px">CLEAR</button>
	  <input id="file-input" type="file" accept=".csv,.txt" name="name" style="display: none;" />
    </div>
  </div>
</form>
</div>
<div style="height:0;overflow:hidden;">
<form class="layui-form" action="" id="br_details" style="padding-right:5px;margin-top:0.5em">
  <div class="layui-form-item" id="BRID_INPUT">
	<label class="layui-form-label label_text">Name</label>
	<div class="layui-input-block">
	  <input id="BRID" type="text" autocomplete="off" class="layui-input">
	</div>
  </div>
  <div class="layui-form-item">
	<label class="layui-form-label label_text">IP Address</label>
	<div class="layui-input-block">
	  <input id="BRIP" type="text" autocomplete="off" class="layui-input">
	</div>
  </div>
  <div class="layui-form-item">
	<label class="layui-form-label label_text">Port Number</label>
	<div class="layui-input-block">
	  <input id="BRPT" type="text" autocomplete="off" class="layui-input">
	</div>
  </div>  
  <div class="layui-form-item">
	<label class="layui-form-label label_text">User Name</label>
	<div class="layui-input-block">
	  <input id="BRUSR" type="text" autocomplete="off" class="layui-input">
	</div>
  </div>
  <div class="layui-form-item">
	<label class="layui-form-label label_text">Password</label>
	<div class="layui-input-block">
	  <input id="BRPWD" type="text" autocomplete="off" class="layui-input">
	</div>
  </div>   
  <div class="layui-form-item">
	<label class="layui-form-label label_text">Uplink</label>
	<div class="layui-input-block" id="UL_L"></div>
	<label class="layui-form-label label_text"></label>
	<div class="layui-input-block" style='height:1em'><div class='layui-row' style='background-color:lightgray'><i id='UL_T_ADD' class='layui-icon layui-icon-add-1' style='font-size:1em;font-weight:bolder;top:0px;position:absolute;right:0.5em;'></i></div></div>
  </div>   
  <div class="layui-form-item">
	<div class="layui-input-block" style="margin:30px 10px 10px 30px;text-align:center;">
	  <button id="BR_SUB" type="button" class="layui-btn">UPDATE</button>
	  <button id="BR_DEL" type="button" class="layui-btn">DELETE</button>
	  <button id="BR_CNL" type="button" class="layui-btn">CANCEL</button>
	</div>
  </div>	  
</form>
</div>

</body>
</html>
<script src="./layui/layui.js" charset="utf-8"></script>
<script src="./local.js" charset="utf-8"></script>
<script src="./paho/paho-mqtt-min.js" charset="utf-8"></script>
<script>
var DATA = <?php echo trim(file_get_contents("data"));?>; 
var CONF = <?php echo json_encode($CONF);?>;
var COMMAND_TOPIC = '<?php echo $COMMAND_TOPIC;?>';
var BRIDGES = [];
var TAGS = ["<br>","<br>","<br>","<br>","<br>","<br>","<br>","<br>","<br>","<br>"];
var LOGS = [];
var IDX = 0;
var DialogIdx = "";
var BR_ID = "";
var TOTALBYTES = 0;
var TOTALBYTES1M = 0;
var RND1 = String(Math.random()).substr(2, 8);
var RND2 = String(Math.random()).substr(2, 8);
var C = getCookie("CMD_BUF");
var R = getCookie("rnd");
if (C==""){C="[]"}
var CMD_BUF = JSON.parse(C);
CMD_BUF = Consolidate(CMD_BUF);
console.log(CONF);
client = "";
layui.use(['form','layedit','laydate','jquery','element','table'], function(){
	var form=layui.form,layer=layui.layer;
	var element = layui.element;
	var table = layui.table;
	var $=layui.jquery;
	var T;
	table.render({
	elem: '#id_list'
	,data: DATA
	,toolbar: '#toolbar'
	,limits: [10,25,50,100,500]
	,cols: [[{type:'checkbox'},{field:'id', title: 'LIST OF RFID EPC / TID', sort: true}]]
	,page: true
	});
	$('#LIVESCAN_CARD').height($("#STATUS_CARD").height());
	$('#MESSAGE_CARD').height($("#STATUS_CARD").height());
	$('#MESSAGE_CARD_BKG').height($("#STATUS_CARD").height());
	$('#MSTATUS_CARD').height($("#WAH_CARD").height());
	$('#MSTATUS_CARD_BODY').height($("#WAH_CARD_BODY").height());
	ReconnectMessage();
	element.on('tab(tabs)', function(data){
	  if(data.index == 0){
		console.log("Monitoring Mode");
		  if(client.isConnected()){
			  client.subscribe("/TAGS_FOUND/#");
			  client.subscribe("/TAGS_MATCHED/#");
			  client.subscribe("/RESPONSE/"+RND1+"/#");
			  client.subscribe("$SYS/broker/clients/connected");		  
			  client.subscribe("$SYS/broker/load/bytes/+/1min");		  			  
			  client.subscribe("$SYS/broker/bytes/#");		  			  
			  client.subscribe("$SYS/broker/uptime");		  			  
			  client.unsubscribe("/RESPONSE/"+RND2+"/#");
			  T = setTimeout(getStatus, 3000); 
		  }	else {
			  console.log("Messaging service is not connected");	
			  layer.msg('Messaging service is not connected, please refresh page to try restarting the messaging service');
		  }		
	  } else if (data.index < 4) {
		  if(client.isConnected()){
			  client.unsubscribe("/TAGS_FOUND/#");
			  client.unsubscribe("/TAGS_MATCHED/#");
			  client.unsubscribe("/RESPONSE/"+RND1+"/#");
			  client.unsubscribe("/RESPONSE/"+RND2+"/#");	
			  client.unsubscribe("$SYS/broker/clients/connected");		  
			  client.unsubscribe("$SYS/broker/load/bytes/+/1min");		  			  
			  client.unsubscribe("$SYS/broker/bytes/#");		  			  
			  client.unsubscribe("$SYS/broker/uptime");		  			  
		  }		  
		  clearTimeout(T);	
	  } else if (data.index == 4) {
		  console.log("Debug Mode");
		  $("#CMD_INPUT").focus();
		   if(client.isConnected()){
			  client.unsubscribe("/TAGS_FOUND/#");
			  client.unsubscribe("/TAGS_MATCHED/#");
			  client.unsubscribe("/RESPONSE/"+RND1+"/#");
			  client.unsubscribe("$SYS/broker/clients/connected");		  
			  client.unsubscribe("$SYS/broker/load/bytes/+/1min");		  			  
			  client.unsubscribe("$SYS/broker/bytes/#");		  			  
			  client.unsubscribe("$SYS/broker/uptime");		  			  
			  client.subscribe("/RESPONSE/"+RND2+"/#");   
		   }
		   clearTimeout(T);	
	  }
	});	
	
	table.on('toolbar(id_list)', function(obj){
		var checkStatus = table.checkStatus(obj.config.id);
		switch(obj.event){
		  case 'add':
			layer.open({
			  type: 1
			  ,title: "ADD IDS"
			  ,content: $('#id_add')
			});			
		  break;
		  case 'del':
			var data = checkStatus.data;
			//console.log(data.length);  
			L = data.length;
			if (L == 0){
				layer.msg('No items are selected');				
			} else {
				if (L==1){
					MSG = 'Confirm to delete the selected item?';
				} else {
					MSG = 'Confirm to delete the selected '+L+' items?';
				}
				layer.open({
				  content: MSG
				  ,title: "ATTENTION"
				  ,btn: ['Confirm', 'Cancel']
				  ,yes: function(index, layero){
					message = new Paho.MQTT.Message(JSON.stringify(data));
					message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+RND2+"/DELETE_ID";
					client.send(message);				
					layer.close(index);
					T1 = setTimeout(ReloadPage, 3000);
				  }
				  ,btn2: function(index, layero){
					layer.close(index);
				  }
				  ,cancel: function(index){ 
					layer.close(index);
				  }
				});
			}
		  break;
		};
	});	
	$('#add_load').on('click', function() {
		$('#file-input').trigger('click');
	});
	$('#add_append').on('click', function() {
		if($("#csv-text").val()==""){
			layer.msg('Please enter at least one ID');
		} else {
			input = $("#csv-text").val();
			layer.open({
			  content: "Append the list to database?"
			  ,title: "ATTENTION"
			  ,btn: ['Confirm', 'Cancel']
			  ,yes: function(index, layero){
				$('#add_clear').trigger('click');
				new_list = input.split(",");
				DATA_Str = JSON.stringify(DATA).toUpperCase();
				//console.log(DATA_Str);
				var NL = "";
				new_list.forEach(function(item) {
					id=item.trim().toUpperCase();
					if (!(DATA_Str.includes(id))){
						//console.log("+"+id.toUpperCase());
						NL = NL+',{"id":"'+id.toUpperCase()+'"}';
					}
				});
				var N = (NL.match(/,/g) || []).length;
				NL = NL.substr(1);
				message = new Paho.MQTT.Message(NL);
				message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+RND2+"/ADD_ID";
				client.send(message);				
				layer.closeAll();
				layer.msg('Appended '+N+' ID to list');
				T1 = setTimeout(ReloadPage, 3000);
			  }
			  ,btn2: function(index, layero){
				layer.close(index);
			  }
			  ,cancel: function(index){ 
				layer.close(index);
			  }
			});				
			//console.log(input);
		}
	});	
	$('#add_replace').on('click', function() {
		if($("#csv-text").val()==""){
			layer.msg('Please enter at least one ID');
		} else {
			input = $("#csv-text").val();
			layer.open({
			  content: "Overwrite the existing list?"
			  ,title: "ATTENTION"
			  ,btn: ['Confirm', 'Cancel']
			  ,yes: function(index, layero){
				$('#add_clear').trigger('click');
				new_list = input.split(",");
				var NL = "[";
				new_list.forEach(function(item) {
					id=item.trim();
					NL = NL+',{"id":"'+id.toUpperCase()+'"}';
				});
				NL = NL.replace("[,{","[{")+"]";	
				message = new Paho.MQTT.Message(NL);
				message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+RND2+"/UPDATE_ID";
				client.send(message);				
				layer.closeAll();
				layer.msg('List updated');
				T1 = setTimeout(ReloadPage, 3000);
			  }
			  ,btn2: function(index, layero){
				layer.close(index);
			  }
			  ,cancel: function(index){ 
				layer.close(index);
			  }
			});				
			//console.log(input);
		}		
	});
	$('#file-input').on('change',function(e){
		var file = e.target.files[0];
		fr = new FileReader();
		fr.onload = receivedText;
		fr.readAsDataURL(file);
	});
	function receivedText() {
		var n = fr.result.lastIndexOf(",");
		console.log(fr.result.substr(n+1));
		list = atob(fr.result.substr(n+1)).replace(/,/g, ",\n");;
		console.log(list);
		$("#csv-text").val(list);
	} 	
	$('#BR_CNL').on('click', function(){layer.close(DialogIdx);});
	$('#BR_DEL').on('click', function(){
		layer.open({
		  content: "Are you sure to delete this bridge connection? <br>Operation will be irreversible."
		  ,title: "ATTENTION"
		  ,btn: ['Confirm', 'Cancel']
		  ,yes: function(index, layero){
			message = new Paho.MQTT.Message(BR_ID);
			message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+String(Math.random()).substr(2, 8)+"/DELETE_BRIDGE";
			client.send(message);				
			layer.closeAll();
			layer.msg('Configurations updated, the new settings will take effect in about 10 seconds.');
			BR_ID="";
			T1 = setTimeout(ReloadPage, 3000);			
		  }
		  ,btn2: function(index, layero){
			layer.close(index);
		  }
		  ,cancel: function(index){ 
			layer.close(index);
		  }
		});	
	});
	$('#BR_ADD').on('click', function(){
		console.log(BRIDGES);
		$("#BRID_INPUT").show();
		$("#BR_DEL").hide();
		$("#BRID_INPUT").val("");
		$("#BRIP").val("");
		$("#BRPT").val("");
		$("#BRUSR").val("");
		$("#BRPWD").val("");
		$('#UL_L').empty();
		$('#UL_L').append("<input id='ULT_0' type='text' autocomplete='off' class='layui-input' placeholder='topic for uplinking'>");
		layui.form.render();
		DialogIdx=layer.open({
		  type: 1
		  ,area: "auto"
		  ,title: "Add New Connection"
		  ,content: $('#br_details')
		});			
	})
	$('#BR_SUB').on('click', function(){
		NEW_BRIDGE={};
		if (($("#BRID").val()=="")&&(BR_ID=="")){
			layer.msg('Bridge Name must not be empty.');
		} else {
			NEW_BRIDGE["name"]=BR_ID==""?$("#BRID").val():BR_ID;
			NEW_BRIDGE["remote_host"]=$("#BRIP").val();
			NEW_BRIDGE["remote_port"]=$("#BRPT").val();
			NEW_BRIDGE["user"]=$("#BRUSR").val();
			NEW_BRIDGE["pwd"]=$("#BRPWD").val();
			NEW_BRIDGE["topics"]={};
			NEW_BRIDGE["topics"]["out"]=[];
			i = 0;
			$('#UL_L').children('input').each(function () {
				if (this.value.trim()!=""){
					NEW_BRIDGE["topics"]["out"].push({"topic":this.value,"prefix":"/UPLINK/"+CONF['host_id']});
				}
			});			
			message = new Paho.MQTT.Message(JSON.stringify(NEW_BRIDGE));
			message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+String(Math.random()).substr(2,8)+"/UPDATE_BRIDGE";
			client.send(message);	
			// console.log(NEW_BRIDGE);
			T1 = setTimeout(ReloadPage, 3000); 
			layer.msg('Configurations updated, the new settings will take effect in about 10 seconds.');
			layer.close(DialogIdx);
			BR_ID="";
		}
	});	
	$('#CFG_RST').on('click', function(){
		initConfig();
	});
	$('#SCAN_BTN').on('click', function(){		
		if($(this).attr('status')=="SCANNING"){
			message = new Paho.MQTT.Message("STOP");
			message.destinationName = COMMAND_TOPIC+String(Math.random()).substr(2,8)+"/SET_MODE";
			client.send(message);		
		} else if ($(this).attr('status')=="STOPPED"){
			message = new Paho.MQTT.Message("SCAN");
			message.destinationName = COMMAND_TOPIC+String(Math.random()).substr(2,8)+"/SET_MODE";
			client.send(message);				
		}
	});	
	$('#CFG_SUB').on('click', function(){
		NEW_CONF = {};
		NEW_CONF['host_id']=$("#DN").val();
		NEW_CONF['ip']=$("#RIP").val();
		NEW_CONF['port']=parseInt($("#RPT").val());
		NEW_CONF['report_interval']=parseFloat($("#RINT").val());
		NEW_CONF['manufacturer']=$("#MFR").val();
		NEW_CONF['model']=$("#MDL").val();
		NEW_CONF['id']=$("#DID").val();
		NEW_CONF['antenna']=$("#AMSK").val();
		NEW_CONF['message_host']=$("#SIP").val();
		NEW_CONF['message_port']=parseInt($("#SPT").val());
		NEW_CONF['message_wsport']=parseInt($("#WSPT").val());
		NEW_CONF['wiegand_interval']=parseInt($("#WINT").val());
		NEW_CONF['message_user']=$("#MUSR").val()=="" ? null : $("#MUSR").val();
		NEW_CONF['message_pwd']=$("#MPWD").val()=="" ? null : $("#MPWD").val();
		if (NEW_CONF['wiegand_interval']<300) {NEW_CONF['wiegand_interval']=300;$("#WINT").val(300)}
		NEW_CONF['read_mode']=$("[name=rmode]:checked").val();
		NEW_CONF['wiegand_mode']=parseInt($("[name=wmode]:checked").val());
		console.table(NEW_CONF);
		NC = JSON.stringify(NEW_CONF);
		//console.log(btoa(NC));
		layer.open({
		  content: "Update the existing configurations?"
		  ,title: "ATTENTION"
		  ,btn: ['Confirm', 'Cancel']
		  ,yes: function(index, layero){
			message = new Paho.MQTT.Message(JSON.stringify(NEW_CONF));
			message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+String(Math.random()).substr(2, 8)+"/UPDATE_CONFIG";
			client.send(message);				
			layer.closeAll();
			layer.msg('Configurations updated, services restarting');
			CONF = NEW_CONF;
			T1 = setTimeout(ReloadPage, 3000);
			// console.table(CONF);
		  }
		  ,btn2: function(index, layero){
			layer.close(index);
		  }
		  ,cancel: function(index){ 
			layer.close(index);
		  }
		});				
	});	
	$('#PWD_SUB').on('click', function(){
		if (($("#N_USER").val().trim()=="")||($("#O_USER").val().trim()=="")||($("#O_PWD").val().trim()=="")||($("#N_PWD1").val().trim()=="")||($("#N_PWD2").val().trim()=="")) {
			layer.msg("All inputs must be filled");
		} else if ($("#N_PWD1").val()!=$("#N_PWD2").val()){
			layer.msg("New passwords are not the same");
		} else {
			var d = '{"Old_User":"'+$("#O_USER").val().trim()+'","New_User":"'+$("#N_USER").val().trim()+'","Old_Pwd":"'+$("#O_PWD").val().trim()+'","New_Pwd":"'+$("#N_PWD1").val().trim()+'"}';
			layer.open({
			  content: "Update user/password pair?"
			  ,title: "ATTENTION"
			  ,btn: ['Confirm', 'Cancel']
			  ,yes: function(index, layero){
				$.get("update_password.php", {data:btoa(d)}, function(data, status){
					if(status=="success"){
						if (data.trim()=="OK"){
							alert('Username/password updated, please re-login');
							window.location.replace("index.php");							
						} else {
							layer.msg(data);
						}
					} else {
						layer.msg("Status: "+status);
					}
				});					
				layer.closeAll();
			  }
			  ,btn2: function(index, layero){
				layer.close(index);
			  }
			  ,cancel: function(index){ 
				layer.close(index);
			  }
			});				
		}
	});		
	function ReconnectMessage(){
		client = new Paho.MQTT.Client(CONF['message_wshost'], CONF['message_wsport'], "ws_client"+String(Math.random()).substr(2, 8));
		initConfig();
		initStatus();
	}	
	function initBridges(){
		BRIDGES = CONF['bridges'];
		console.log(BRIDGES);
		$("#BRL").empty();
		for(var i=0;i<BRIDGES.length;i++){
			$("#BRL").append("<tr><td>"+BRIDGES[i]["name"]+"</td><td>"+BRIDGES[i]["remote_host"]+" : "+BRIDGES[i]["remote_port"]+"</td><td><i class='layui-icon layui-icon-set' style='font-size:1.5em;float:right;font-weight:bolder;margin-right:10px;'></i></td></tr>");
			_T = BRIDGES[i]['topics'];
		}		
	}	
	function initScanMode(){
		console.log(CONF['reader_config']['mode']+" MODE");
		if(CONF['reader_config']['mode']=="STOP"){
			$('#SCAN_BTN').attr('status','STOPPED');
			$('#LIVE_SCAN_TEXT').text('LIVE SCANS (STOPPED)');
			$('#SCAN_BTN_ICON').removeClass('layui-icon-pause');
			$('#SCAN_BTN_ICON').css('color','#009688');
			$('#SCAN_BTN_ICON').addClass('layui-icon-play');				
		} else if(CONF['reader_config']['mode']=="SCAN"){
			$('#SCAN_BTN').attr('status','SCANNING');
			$('#LIVE_SCAN_TEXT').text('LIVE SCANS (RUNNING)');
			$('#SCAN_BTN_ICON').css('color','red');
			$('#SCAN_BTN_ICON').removeClass('layui-icon-play');
			$('#SCAN_BTN_ICON').addClass('layui-icon-pause');		
		}
	}
	function initConfig(){
		$("#DN").val(CONF['host_id']);
		$("#RIP").val(CONF['ip']);
		$("#RPT").val(CONF['port']);
		$("#RINT").val(CONF['report_interval']);
		$("#MFR").val(CONF['manufacturer']);
		$("#MDL").val(CONF['model']);
		$("#DID").val(CONF['id']);
		$("#AMSK").val(CONF['antenna']);
		$("#SIP").val(CONF['message_host']);
		$("#SPT").val(CONF['message_port']);
		$("#WSPT").val(CONF['message_wsport']);
		$("#MUSR").val(CONF['message_user']);
		$("#MPWD").val(CONF['message_pwd']);
		$("#WINT").val(CONF['wiegand_interval']);
		$("input:radio[name=rmode][value="+CONF['read_mode']+"]").next().find("i").click();
		$("input:radio[name=wmode][value="+CONF['wiegand_mode']+"]").next().find("i").click();		
	}
	function getStatus(){
		message = new Paho.MQTT.Message("");
		message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+RND1+"/GET_STATUS";
		client.send(message);	
		// console.log(message);
		T = setTimeout(getStatus, 3000);
	}	
	function initStatus(){
		client.onConnectionLost = onConnectionLost;
		client.onMessageArrived = onMessageArrived;
		client.connect({userName:CONF['message_user']==null?"":CONF['message_user'],password:CONF['message_pwd']==null?"":CONF['message_wspwd'],onSuccess:onConnect});
		
		$('#RESTART_BTN').on('click', function(){restartServices();});
		$('#UL_T_ADD').on('click', function(){
			N = $("#UL_L").children().length; 
			K = N-1;
			if ($('#ULT_'+K.toString()).val()!==""){
				$('#UL_L').append("<input id='ULT_"+N+"' type='text' autocomplete='off' class='layui-input' placeholder='topic for uplinking'>");
				form_id = $("#br_details").parent().parent().attr('id');
				new_top = (parseInt(($("#"+form_id).css('top')).replace("px",""))-38).toString()+"px";
				$("#"+form_id).css('top',new_top);
			} else {
				$('#ULT_'+(N-1).toString()).focus();
			}
		});			
		$('#BRL').delegate('tr','click', function(){
			i = $(this).closest('tr').index();
			BR_ID=BRIDGES[i]['name'];
			$("#BRID_INPUT").hide();
			$("#BR_DEL").show();
			$('#BRIP').val(BRIDGES[i]["remote_host"]);
			$('#BRPT').val(BRIDGES[i]["remote_port"]);
			$('#BRUSR').val(BRIDGES[i]["user"]);
			$('#BRPWD').val(BRIDGES[i]["pwd"]);
			$('#UL_L').empty();
			UPLINK = BRIDGES[i]['topics']['out'];
			for(var j=0;j<UPLINK.length;j++){
				$('#UL_L').append("<input id='ULT_"+j+"' type='text' autocomplete='off' class='layui-input' value='"+UPLINK[j]['topic']+"'>");
			}	
			DialogIdx=layer.open({
			  type: 1
			  ,area: "auto"
			  ,title: "Edit Connection ["+BRIDGES[i]["name"]+"]"
			  ,content: $('#br_details')
			});	
		});
		
		function onConnect() {
		  console.log("onConnect - "+CONF['message_wshost']+":"+CONF['message_wsport']);
		  client.subscribe("/SYSTEM_MESSAGE/#");
		  client.subscribe("/CONFIG/");
		  client.subscribe("/TAGS_FOUND/#");
		  client.subscribe("/TAGS_MATCHED/#");
		  client.subscribe("/RESPONSE/"+RND1+"/#");		  	  
		  client.subscribe("$SYS/broker/clients/connected");		  
		  client.subscribe("$SYS/broker/load/bytes/+/1min");		  
		  client.subscribe("$SYS/broker/bytes/#");		  
		  client.subscribe("$SYS/broker/uptime");	
		  m = new Paho.MQTT.Message(R);
		  m.destinationName = "/"+CONF['host_id']+"/COMMAND/init/UPDATE_RND";
		  client.send(m);		  
		  setTimeout(getStatus, 1);
		}
		function onConnectionLost(responseObject) {
		  if (responseObject.errorCode !== 0) {
			console.log("onConnectionLost:"+responseObject.errorMessage);
			clearTimeout(T);
			console.log("Attempt to reconnect ...");
			T = setTimeout(ReconnectMessage, 3000); 
		  }
		}
		function onMessageArrived(message) {
		  // console.log(message);
		  if (message.destinationName=="/TAGS_FOUND"){
			  TAGS.push(new Date().toLocaleTimeString()+" :&emsp;&emsp;"+message.payloadString.trim()+"<br>");
		  } else if(message.destinationName.includes("SYSTEM_MESSAGE")){
			  // layer.msg('System Message:<br>'+message.payloadString);
			  LOGS.push("<b>"+new Date().toLocaleTimeString()+" :</b>&emsp;"+message.payloadString.trim()+"<br>");		  
		  } else if(message.destinationName=="/RESPONSE/"+RND1+"/"){
			  ST = JSON.parse(message.payloadString.trim().toLowerCase());
			  updateStatus(ST);
		  } else if(message.destinationName=="/RESPONSE/"+RND2+"/"){
			  $("#CMD_OUTPUT").html(message.payloadString.substring(2).replace(/\n/g,"<br>"));
		  } else if(message.destinationName.includes("clients/connected")){
			  $("#NCLNT").text(message.payloadString);
		  } else if(message.destinationName.includes("broker/uptime")){
			  $("#MUPT").text(parseInt(parseInt(message.payloadString)/60));
		  } else if(message.destinationName.includes("broker/bytes/received")){
			  TOTALBYTES=parseInt(message.payloadString);
			  // $("#BTOTL").text(N+parseInt($("#BSENT").text()));
		  } else if(message.destinationName.includes("broker/bytes/sent")){
			  TOTALBYTES=TOTALBYTES+parseInt(message.payloadString);
			  $("#BTOTL2").text(numberWithCommas(TOTALBYTES));
		  } else if(message.destinationName.includes("load/bytes/received")){
			  TOTALBYTES1M=parseInt(message.payloadString);
			  $("#BRCVD").text(numberWithCommas(TOTALBYTES1M));
			  // $("#BTOTL").text(N+parseInt($("#BSENT").text()));
		  } else if(message.destinationName.includes("load/bytes/sent")){
			  TOTALBYTES1M=TOTALBYTES1M+parseInt(message.payloadString);
			  $("#BSENT").text(numberWithCommas(parseInt(message.payloadString)));
			  $("#BTOTL").text(TOTALBYTES1M);
		  } else if(message.destinationName.includes("/CONFIG/")){
			  console.log(message.destinationName);
			  CONF = JSON.parse(message.payloadString);
			  initScanMode();
			  initBridges();
		  } else {
			  TAGS.push(new Date().toLocaleTimeString()+" :&emsp;&emsp;<span style='color:red'>"+message.payloadString.trim()+"</span><br>");
		  }
		  TAGS = TAGS.slice(TAGS.length - 10);
		  if (LOGS.length>18){
			LOGS = LOGS.slice(LOGS.length - 18);  
		  }
		  // console.log(TAGS);
		  $("#LIVE_WINDOW").html(TAGS.join(" "));
		  $("#LOG_WINDOW").html(LOGS.join(" "));
		}
		function restartServices(){
			// console.log("restartServices");
		    message = new Paho.MQTT.Message("RESTART");
			message.destinationName = "/"+CONF['host_id']+"/COMMAND/1234/RESTART";
			client.send(message);	
			
		}		
		function updateStatus(S){
			// console.log(S);
			if (S['reader']){
				$("#STCB").removeClass("layui-bg-red");
				$("#STCB").addClass("layui-bg-green");	
				$("#STCBT").text("STATUS: CONNECTED");
			} else {
				$("#STCB").removeClass("layui-bg-green");
				$("#STCB").addClass("layui-bg-red");	
				$("#STCBT").text("STATUS: DISCONNECTED");			
			}
			if (S['raw']){
				$("#STRB").removeClass("layui-bg-red");
				$("#STRB").addClass("layui-bg-green");
				$("#STRBT").text("READ TAGS: RUNNING");
			}else {
				$("#STRB").removeClass("layui-bg-green");
				$("#STRB").addClass("layui-bg-red");	
				$("#STRBT").text("READ TAGS: NOT RUNNING");
			}			
			if (S['filter']){
				$("#STFB").removeClass("layui-bg-red");
				$("#STFB").addClass("layui-bg-green");
				$("#STFBT").text("ID FILTER: RUNNING");
			}else {
				$("#STFB").removeClass("layui-bg-green");
				$("#STFB").addClass("layui-bg-red");	
				$("#STFBT").text("ID FILTER: NOT RUNNING");
			}
			if (S['wiegand']){
				$("#STWB").removeClass("layui-bg-red");
				$("#STWB").addClass("layui-bg-green");
				$("#STWBT").text("WIEGAND: RUNNING");
			}else {
				$("#STWB").removeClass("layui-bg-green");
				$("#STWB").addClass("layui-bg-red");	
				$("#STWBT").text("WIEGAND: NOT RUNNING");
			}			
			if (S['filter'] && S['raw'] && S['wiegand']){
				$("#RESTART_CARD").css("max-height","0px");
			} else {
				$("#RESTART_CARD").css("max-height","38px");
			}
			$("#STTV").text(S['cpu_temp'].toUpperCase());
			$("#STMV").text(S['mem_used'].toUpperCase());
			$("#STCU").text(S['cpu_util'].toUpperCase());
			$("#STDU").text(S['hdd'].toUpperCase());
			CPUT = (parseFloat(S['cpu_temp'].replace("C",""))-20)/60;
			MEMU = parseFloat(S['mem_used'].replace("%",""))/100;
			CPUU = parseFloat(S['cpu_util'].replace("%",""))/100;
			DSKU = parseFloat(S['hdd'].replace("%",""))/100;
			$("#STTV").css("width",S['cpu_temp'].replace("'c","%"));
			$("#STMV").css("width",S['mem_used']);
			$("#STCU").css("width",S['cpu_util']);			
			$("#STDU").css("width",S['hdd']);
			$("#STTV").css("background-color",getColor(CPUT));
			$("#STMV").css("background-color",getColor(MEMU));
			$("#STCU").css("background-color",getColor(CPUU));			
			$("#STDU").css("background-color",getColor(DSKU));			
		}
		function getColor(value){
			var hue=((1-value)*120).toString(10);
			var light=(value*10+35).toString();
			return ["hsl(",hue,",100%,",light,"%)"].join("");
		}
		$("#CMD_INPUT").keyup(function(e){
			if(e.key=="Enter"){
				// console.log(CMD_BUF);
				// console.log($("#CMD_INPUT").val());
				var CMD = $("#CMD_INPUT").val();
				CMD_BUF.push(CMD);
				// console.log(CMD_BUF);
				IDX = 0;
				setCookie("CMD_BUF",JSON.stringify(CMD_BUF),3650);
				// console.log(CMD_BUF);
				$("#CMD_INPUT").val("");
				$("#CMD_OUTPUT").text("");
				message = new Paho.MQTT.Message(CMD);
				message.destinationName = "/"+CONF['host_id']+"/COMMAND/"+RND2+"/RUN";
				client.send(message);
			} else if(e.key=="ArrowUp"){
				IDX=IDX+1;
				if(IDX>CMD_BUF.length){IDX=CMD_BUF.length;}
				$("#CMD_INPUT").val(CMD_BUF[CMD_BUF.length-IDX]);
			} else if(e.key=="ArrowDown"){
				IDX=IDX-1;
				if(IDX<0){IDX=0;}
				$("#CMD_INPUT").val(CMD_BUF[CMD_BUF.length-IDX]);
			}
			
		});		
	}
});
function ReloadPage(){
	console.log("Reload page");
	location.reload();
}
function Consolidate(A){
	var R=[];
	R.push(A[0]);
	if(A.length>1){
		for(i=1;i<A.length;i++){
			if(A[i]!==A[i-1]){
				R.push(A[i]);
			}
		}		
		return R;
	} else {
		return A;
	}
}
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>