<?php
$TOKEN_CLIENT=$_COOKIE["TOKEN"];
$TOKEN_SERVER=file_get_contents("./token");
if ($TOKEN_CLIENT!=$TOKEN_SERVER){
	unlink("./token");
	ob_start();
	header('Location: index.php');
	ob_end_flush();
	die();			
}
$T=file_get_contents("blocking_time");

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>MAIN</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="./layui/css/layui.css"  media="all">
  <link rel="stylesheet" href="./local.css"  media="all">
  <style>
	.r_gap{margin-right:10px}
	.r_gap2{margin-right:5px}
	.time_badge{width:50px}
  </style>
</head>
<body class="layui-bg-gray" style="background-image:url('./img/bkg.jpg')">       
<div id="MAIN_WINDOW" class="layui-fluid main_window shadow1" style="max-width:1000px">
  <div class="layui-row">
	  <div class="layui-row">
		  <div class="layui-col-md3">
			<div class="layui-panel">
			  <div style="padding: 20px 10px 10px 20px;">
				<div style="padding-bottom:5px;">Connection status:</div>
				<div id="STATUS" style="width:120px;display:inline-block;height:38px;line-height:38px;padding:0 18px;background-color:#FF5722;color:#fff;white-space:nowrap;text-align:center;font-size:14px;border:none;border-radius:2px;margin-right:20px">NOT CONNECTED</div>
			  </div>
			</div> 			
		  </div>	
		  <div class="layui-col-md3">
			<div class="layui-panel">
			  <div style="padding: 20px 10px 10px 20px;">
				<div style="padding-bottom:5px;">Blocking time(seconds):</div>
				<div class="layui-form-item layui-input-inline">
					<select id="BLOCK_TIME_SELECT" name="BLOCK_TIME" lay-filter="BLOCK_TIME" style="width:150px;display:inline-block;height:38px;line-height:38px;padding:0 10px;color:#000;white-space:nowrap;text-align:left;font-size:14px;border:1px solid #C9C9C9;border-radius:2px;margin-right:20px">
					  <option value="5">5s</option>
					  <option value="10">10s</option>
					  <option value="30">30s</option>
					  <option value="60">60s</option>
					</select> 
				</div>
			  </div>
			</div> 			
		  </div>	
		  <div class="layui-col-md6">
			<div class="layui-panel">
			  <div style="padding: 20px 10px 10px 20px;">
				<div style="padding-bottom:5px;">Session Status:</div>
				<div id="SESSION_STATUS" style="width:90px;display:inline-block;height:38px;line-height:38px;padding:0 18px;background-color:#393D49;color:#fff;white-space:nowrap;text-align:center;font-size:14px;border:none;border-radius:2px;margin-right:20px">NO SESSION</div>
				<button id="SESSION_BTN" type="button" class="layui-btn layui-btn-radius layui-btn-primary" style="width:90px">Start</button>
				<button id="EXPORT_BTN" type="button" class="layui-btn layui-btn-radius layui-btn-primary" style="width:100px; display:none;">Download</button>
			  </div>
			</div> 			
		  </div>		  
	  </div>
	  <div class="layui-row">
		  <div class="layui-col-md12">
			<div class="layui-panel">
			  <div style="padding: 20px 10px 10px 20px;">
				<div style="padding-bottom:5px;">Live events:</div>
				<div style="padding:5px;overflow:scroll;" id="EVENTS">
<!-- 					<div class="layui-row" id="1234"><span class="layui-badge-dot layui-bg-blue r_gap"></span><span class="layui-badge-rim r_gap"><i class="layui-icon r_gap">&#xe650;</i>1234</span><span class="layui-badge layui-bg-black r_gap time_badge"><i class="layui-icon r_gap2">&#xe60e;</i>102s</span><span class="layui-badge layui-bg-black r_gap time_badge"><i class="layui-icon r_gap2">&#xe60e;</i>2102s</span></div>
					<div class="layui-row" id="2222"><span class="layui-badge-dot layui-bg-blue r_gap"></span><span class="layui-badge-rim r_gap"><i class="layui-icon r_gap">&#xe650;</i>1234</span><span class="layui-badge layui-bg-black r_gap time_badge"><i class="layui-icon r_gap2">&#xe60e;</i>12s</span><span class="layui-badge layui-bg-black r_gap time_badge"><i class="layui-icon r_gap2">&#xe60e;</i>22s</span></div> -->
				</div>
			  </div>
			</div> 			
		  </div>	  
	  </div>
  </div>
</div>
</body>
</html>
<script src="./layui/layui.js" charset="utf-8"></script>
<script src="./local.js" charset="utf-8"></script>
<script>
let socket=null;
let SESSION_ID="";
let SESSION_ID_LAST="";
let DATAPOOL={};
let Tb=<?php echo trim($T);?>;
layui.use(['form','layedit','laydate','jquery'], function(){	
	var form=layui.form,layer=layui.layer;
	var $=layui.jquery;
	function INIT(){
		var dt = new Date();
		console.log(dt.toISOString());		
		$("#BLOCK_TIME_SELECT").val(Tb.toString()).change();
		$.get("session.php", function(data, status){console.log("Data: " + data + "\nStatus: " + status);});
		$("#SESSION_STATUS").css("background-color","#393D49");
		$("#SESSION_STATUS").text("NO SESSION");
		$("#SESSION_BTN").text("Start");
		SESSION_ID="";		
		WS_Connect();		
		var MAIN_WINDOW_TOP = $("#MAIN_WINDOW").offset().top;
		var EVENTS_WINDOW_TOP = $("#EVENTS").offset().top
		var MAIN_WINDOW_HEIGHT = $("#MAIN_WINDOW").outerHeight()
		var EVENTS_WINDOW_HEIGHT = MAIN_WINDOW_HEIGHT*0.95 - (EVENTS_WINDOW_TOP - MAIN_WINDOW_TOP);
		$("#EVENTS").height(EVENTS_WINDOW_HEIGHT);
	}
	function WS_Connect(){
		socket = new WebSocket("ws://"+location.host+":7681");
		socket.onopen = function(e) {
		  console.log("[open] Connection established");
		  $("#STATUS").text("CONNECTED");
		  $("#STATUS").css("background-color","#5FB878");
		  $("#CONN").text("Disonnect");
		  socket.send('{"CloseRfPower":{}}');
		};	
		socket.onmessage = function(event) {
			DATA_OBJ=JSON.parse(event.data);
			if("GenRead_AckTypeOk" in DATA_OBJ){
				if(SESSION_ID==""){return;}
				EPC = DATA_OBJ["GenRead_AckTypeOk"]["EPC"];
				TIME = DATA_OBJ["GenRead_AckTypeOk"]["time"];
				// console.log(TIME+": "+EPC); 
				var T1=Math.round(new Date()/1000);
				var IDX=EPC.substr(EPC.length - 4);
				
				if(!(EPC in DATAPOOL)){
					DATAPOOL[EPC]=T1;
					console.log(IDX);
					var HTML='<div class="layui-row" id="'+IDX.toString()+'"><span class="layui-badge-dot layui-bg-blue r_gap"></span><span class="layui-badge-rim r_gap" style="width:75px"><i class="layui-icon r_gap">&#xe650;</i>'+IDX.toString()+'</span></div>'
					$("#EVENTS").prepend($(HTML));
				}else{
					var Td=T1-DATAPOOL[EPC];
					if(Td>Tb){
						DATAPOOL[EPC]=T1;
						// console.log(DATAPOOL);
						var LINE=$("#"+IDX).html()+'<span class="layui-badge layui-bg-black r_gap time_badge"><i class="layui-icon r_gap2">&#xe60e;</i>'+Td.toString()+'s</span>';
						var HTML='<div class="layui-row" id="'+IDX+'">'+LINE+'</div>'
						$("#"+IDX).remove();
						$("#EVENTS").prepend($(HTML));
					}
				}
			}else{
				console.log(DATA_OBJ);
			}
			 
		};		
		socket.onclose = function(event) {
		  if (event.wasClean) {
			console.log('[close] Connection closed cleanly, code=${event.code} reason=${event.reason}');
		  } else {
			console.log('[close] Connection died');
		  }
		  $("#STATUS").text("NOT CONNECTED");
		  $("#STATUS").css("background-color","#FF5722");
		  $("#CONN").text("Connect");	  
		};			
	}
    $('#BLOCK_TIME_SELECT').change(function(){
		var VAL = $(this).find("option:selected").attr('value');
		$.get("set_block_time.php?t="+VAL.toString(), function(data, status){
			console.log("Data: " + data + "\nStatus: " + status);
		});		
    });
	$("#SESSION_BTN").click(function(){
		if($("#SESSION_BTN").text()=="Start"){
			console.log("Scan start");
			$("#EVENTS").empty();
			$("#EXPORT_BTN").css("display","none");
			DATAPOOL={};
			SESSION_ID=(Math.round(new Date()/1000)-1600000000).toString();
			socket.send('{"GenRead":{"Antennas":"00000001","Q":1,"OpType":0,"LenTid":0,"PointerUserEvb":0,"LenUser":0}}');
			$("#SESSION_STATUS").css("background-color","#1E9FFF");
			$("#SESSION_STATUS").text("ID = "+SESSION_ID);
			$("#SESSION_BTN").text("Stop");
			$.get("session.php?s="+SESSION_ID.toString(), function(data, status){
				console.log("Data: " + data + "\nStatus: " + status);
			});		
		}else{
			$("#SESSION_STATUS").css("background-color","#393D49");
			$("#SESSION_STATUS").text("NO SESSION");
			$("#SESSION_BTN").text("Start");
			$("#EXPORT_BTN").css("display","inline-block");
			SESSION_ID_LAST=SESSION_ID;
			SESSION_ID="";
			console.log("Scan stop");
			socket.send('{"CloseRfPower":{}}');	
			$.get("session.php", function(data, status){
				console.log("Data: " + data + "\nStatus: " + status);
			});				
		}	
	});	
	$("#EXPORT_BTN").click(function(){
		var STR=$("#EVENTS").text().replace(/\s/g,'').replace(/\ue650/g,'\r\nID=').replace(/\ue60e/g,',').trim();
		downloadBlob(STR, 'report_'+SESSION_ID_LAST.toString()+'.csv', 'text/csv;charset=utf-8;');
		function downloadBlob(content, filename, contentType) {
		  var blob = new Blob([content], { type: contentType });
		  var url = URL.createObjectURL(blob);
		  var pom = document.createElement('a');
		  pom.href = url;
		  pom.setAttribute('download', filename);
		  pom.click();
		}		
	});
	INIT();

	
});
</script>