<?php
include '../includes/common.php';
include '../includes/agent_auth.php';

if (!($isAgentLogin == 1)) {
	exit('<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<title>提示</title>
		<script src="../assets/layui/layui.js"></script>
		<link rel="stylesheet" href="../assets/layui/css/layui.css">
	</head>
	<body>
	<script>
		layui.use(["layer"], function(){
			var layer = layui.layer;
			layer.alert("您还没有登录，请先登录！", {
				title: "温馨提示",
				icon: 1,
				skin: "layui-layer-molv",
				anim: 4,
				btn: ["确定"],
					yes: function(){
						window.location.href="./login.php";
					}
			});
		});
	</script>
	</body>
	</html>');
}

$title = '代理后台首页';
include './head.php';

