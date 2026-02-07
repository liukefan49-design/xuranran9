<?php
include '../includes/common.php';
include '../includes/agent_auth.php';
if ($isAgentLogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>生成卡密</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
	<style>
		body {
			padding: 20px;
		}
		.balance-info {
			background: #f8f8f8;
			padding: 15px;
			border-radius: 5px;
			margin-bottom: 20px;
			text-align: center;
		}
		.balance-info .balance {
			font-size: 24px;
			color: #ff5722;
			font-weight: bold;
		}
	</style>
</head>

<body class="layui-form form">
	<div class="balance-info">
		<div>当前余额</div>
		<div class="balance">¥<?php echo number_format($agentInfo['balance'], 2); ?></div>
		<div style="color: #999; font-size: 12px; margin-top: 5px;">每张卡密消耗 ¥1.00</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			所属应用
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<select name="app" lay-verify="required" lay-filter="app">
				<option value="">请选择应用</option>
			</select>
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			前缀
		</label>
		<div class="layui-input-block">
			<input type="text" name="qianzhui" class="layui-input" placeholder="为空则自动生成前缀">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			卡密长度
		</label>
		<div class="layui-input-block">
			<input type="number" maxlength="128" name="kamilen" class="layui-input" placeholder="卡密长度，默认为16位" value="16">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			生成数量
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="number" name="num" required lay-verify="required|number" class="layui-input" placeholder="请输入生成数量(1-10000)">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			时长
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<select name="duration" lay-verify="required" lay-filter="duration">
				<option value="">请选择时长</option>
				<option value="1day">1天</option>
				<option value="3day">3天</option>
				<option value="7day">7天</option>
				<option value="15day">15天</option>
				<option value="30day">30天</option>
				<option value="90day">90天</option>
				<option value="180day">180天</option>
				<option value="365day">365天</option>
				<option value="-1">自定义</option>
			</select>
		</div>
	</div>

	<div class="layui-form-item" id="custom_duration" style="display: none;">
		<label class="layui-form-label">
			自定义时长
		</label>
		<div class="layui-input-inline" style="width: 200px;">
			<input type="number" name="kamidur" class="layui-input" placeholder="请输入时长">
		</div>
		<div class="layui-input-inline" style="width: 150px;">
			<select name="kamidurdangwei">
				<option value="hour">小时</option>
				<option value="day" selected>天</option>
				<option value="month">月</option>
				<option value="year">年</option>
			</select>
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			备注
		</label>
		<div class="layui-input-block">
			<input type="text" name="comment" class="layui-input" placeholder="请输入备注">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			最大连接数
		</label>
		<div class="layui-input-block">
			<input type="number" name="connection" class="layui-input" placeholder="-1为不限制">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			上传带宽
		</label>
		<div class="layui-input-block">
			<input type="number" name="bandwidthup" class="layui-input" placeholder="-1为不限制，单位KB/s">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			下载带宽
		</label>
		<div class="layui-input-block">
			<input type="number" name="bandwidthdown" class="layui-input" placeholder="-1为不限制，单位KB/s">
		</div>
	</div>

	<div class="layui-form-item">
		<div class="layui-input-block">
			<button class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">立即生成</button>
			<button type="reset" class="layui-btn layui-btn-primary">重置</button>
		</div>
	</div>

	<script src="../assets/layui/layui.js"></script>
	<script>
		layui.use(['form', 'layer'], function() {
			var form = layui.form;
			var layer = layui.layer;
			var $ = layui.jquery;

			// 加载应用列表
			function loadApps() {
				$.ajax({
					url: "ajax.php?act=getapp",
					type: "POST",
					dataType: "json",
					success: function(data) {
						if (data.code == "1" && data.msg) {
							var html = '<option value="">请选择应用</option>';
							for (var key in data.msg) {
								var app = data.msg[key];
								html += '<option value="' + app.appcode + '">' + app.appname + '</option>';
							}
							$('select[name="app"]').html(html);
							form.render('select');
						} else {
							layer.msg('获取应用列表失败', {icon: 5});
						}
					},
					error: function() {
						layer.msg('获取应用列表失败', {icon: 5});
					}
				});
			}
			loadApps();

			// 监听时长选择
			form.on('select(duration)', function(data) {
				if (data.value == '-1') {
					$('#custom_duration').show();
				} else {
					$('#custom_duration').hide();
				}
			});

			// 复制文本内容
			function copy(txval) {
				let txa = document.createElement('textarea');
				txa.value = txval;
				document.body.appendChild(txa);
				txa.select();
				let res = document.execCommand('copy');
				document.body.removeChild(txa);
			}

			// 提交表单
			form.on("submit(submit)", function(data) {
				if (data.field.duration == -1) {
					if (data.field.kamidur == "") {
						layer.msg("自定义时长不能为空！", {icon: 5});
						return false;
					}
				}

				// 计算所需余额
				var num = parseInt(data.field.num);
				var totalCost = num * 1.0;
				var currentBalance = <?php echo $agentInfo['balance']; ?>;

				if (currentBalance < totalCost) {
					layer.msg('余额不足！需要 ¥' + totalCost.toFixed(2) + '，当前余额 ¥' + currentBalance.toFixed(2), {icon: 5});
					return false;
				}

				$.ajax({
					url: "ajax.php?act=newkami",
					type: "POST",
					dataType: "json",
					data: data.field,
					beforeSend: function() {
						layer.msg("正在生成", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(data) {
						if (data.code == "1" || data.code == "2") {
							window.parent.frames.reload("agent_kami");
							parent.layer.closeAll();
							parent.layer.msg("生成成功", {icon: 1});

							if (data.code == "2" && data.kami) {
								var kami = "您生成的卡密为：\n\n";
								var num = 0;
								for (var key in data.kami) {
									kami += data.kami[key]["kami"] + "\n";
									num++;
								}

								if (num > 500) {
									parent.layer.msg("生成成功，但是卡密数量过多，请使用导出功能导出卡密", {
										icon: 1,
										time: 2000
									});
									return;
								}

								parent.layer.open({
									type: 1,
									title: '卡密窗口',
									area: ['400px', '500px'],
									content: '<div style="padding: 20px;">' +
										'<div style="margin-bottom:10px;">共生成 ' + num + ' 张卡密：</div>' +
										'<textarea readonly style="width:100%;height:350px;resize:none;">' + kami + '</textarea>' +
										'<button class="layui-btn layui-btn-normal" onclick="layui.jquery(this).prev().select();document.execCommand(\'copy\');parent.layer.msg(\'复制成功\',{icon:1,time:1000});" style="margin-top:10px;">复制卡密</button>' +
										'</div>'
								});

								copy(kami);
								parent.layer.msg("卡密已经复制成功！", {time: 1500});
							}
						} else {
							layer.msg(data.msg, {icon: 5});
						}
					},
					error: function(data) {
						layer.msg("未知错误", {icon: 5});
					}
				});
				return false;
			});
		});
	</script>
</body>
</html>

