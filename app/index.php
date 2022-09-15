<?php
setcookie("TOKEN","",-1,"/");
if($_POST['pwd']==""){
	$MSG="";
}else{
	$USER = $_POST['user'];
	$PWD = $_POST['pwd'];
	if($PWD === "8e82f44873f4b68b5001b323ab39953155f215a0"){
		$TOKEN=SHA1(rand(10000000,99999999));
		setcookie("TOKEN",$TOKEN,time()+(86400*7), "/");
		file_put_contents("token",$TOKEN);		
		ob_start();
		header('Location: main.php');
		ob_end_flush();
		die();	
	}else{
		$MSG="Incorrect username / passcode";
	}
}	
	
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>LOGIN</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="./layui/css/layui.css"  media="all">
  <link rel="stylesheet" href="./local.css"  media="all">
</head>
<body class="layui-bg-gray" style="background-image:url('./img/bkg.jpg')">       
<div class="layui-fluid vertical-center shadow1">
  <div class="layui-row">
	  <div class="layui-row">
		<div>
			<div class="layui-card-header layui-bg-green window-header">LOGIN</div>
			<div class="layui-card-body" style="padding-top:30px">
				<form class="layui-form" action="" method="post">
					<div class="layui-form-item">
						<label class="layui-form-label">PASSCODE</label>
						<div class="layui-input-block">
							<input id="PWD" type="password" required  lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
							<input id="HPWD" type="hidden" name="pwd" >
						</div>
					</div>
					<div class="layui-form-item">
						<div class="layui-input-block">
							<button id="SUB" type="button" class="layui-btn" lay-submit="" lay-filter="login_submit">LOGIN</button>
							<button type="reset" class="layui-btn layui-btn-primary">CLEAR</button>
						</div>
					</div>					
				</form>
				<div class="layui-card-body" style="text-align:center;"><span style="color:red"><?php echo $MSG;?></span></div>
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
layui.use(['form','layedit','laydate','jquery'], function(){
	var form=layui.form,layer=layui.layer;
	var $=layui.jquery;
	form.on('submit(login_submit)', function(data){
		hpw = SHA1("adminpwd"+$("#PWD").val());
		$("#HPWD").val(hpw);
		$("#SUB").prop("type", "submit");
		return;
	});

	$('#PWD').keypress(function (e) {
	  if (e.which == 13) {
		$('#SUB').click();
		return false; 
	  }
	})	
	
});
</script>