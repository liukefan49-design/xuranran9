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
	<title>代理主页</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
	<style>
		.card {
			border-radius: 8px;
			padding: 20px;
			color: white;
			position: relative;
			overflow: hidden;
			box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
		}
		.card-icon {
			float: left;
			width: 60px;
			height: 60px;
			line-height: 60px;
			text-align: center;
			font-size: 30px;
		}
		.card-box {
			margin-left: 80px;
		}
		.card-box-title {
			font-size: 14px;
			opacity: 0.9;
			margin-bottom: 5px;
		}
		.card-box-num {
			font-size: 28px;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<div class="layui-row layui-col-space12 layer-anim layui-anim-scaleSpring" style="margin-bottom: 10px;">
		<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
			<div class="card" style="background-color: #33cabb;">
				<div class="card-icon">
					<i class="layui-icon layui-icon-rmb"></i>
				</div>
				<div class="card-box">
					<div class="card-box-title">账户余额</div>
					<div id="balance" class="card-box-num">0.00</div>
				</div>
			</div>
		</div>
		<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
			<div class="card" style="background-color: #ce68fd;">
				<div class="card-icon">
					<i class="layui-icon layui-icon-template-1"></i>
				</div>
				<div class="card-box">
					<div class="card-box-title">卡密总数</div>
					<div id="kaminum" class="card-box-num">0</div>
				</div>
			</div>
		</div>
		<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
			<div class="card" style="background-color: #ffa45c;">
				<div class="card-icon">
					<i class="layui-icon layui-icon-ok-circle"></i>
				</div>
				<div class="card-box">
					<div class="card-box-title">已使用卡密</div>
					<div id="usedkami" class="card-box-num">0</div>
				</div>
			</div>
		</div>
		<div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
			<div class="card" style="background-color: #5fb878;">
				<div class="card-icon">
					<i class="layui-icon layui-icon-date"></i>
				</div>
				<div class="card-box">
					<div class="card-box-title">今日使用</div>
					<div id="todaykami" class="card-box-num">0</div>
				</div>
			</div>
		</div>
	</div>

	<div class="layui-row layui-col-space12">
		<div class="layui-col-md6">
			<div class="layui-card">
				<div class="layui-card-header">代理信息</div>
				<div class="layui-card-body">
					<table class="layui-table">
						<tbody>
							<tr>
								<td width="150">代理账号</td>
								<td><?php echo $agentInfo['username']; ?></td>
							</tr>
							<tr>
								<td>代理名称</td>
								<td><?php echo $agentInfo['name']; ?></td>
							</tr>
							<tr>
								<td>代理级别</td>
								<td>Level <?php echo $agentInfo['level']; ?></td>
							</tr>
							<tr>
								<td>账户余额</td>
								<td style="color: #ff5722; font-weight: bold; font-size: 18px;">¥<?php echo number_format($agentInfo['balance'], 2); ?></td>
							</tr>
							<tr>
								<td>创建时间</td>
								<td><?php echo $agentInfo['created_time']; ?></td>
							</tr>
							<tr>
								<td>最后登录</td>
								<td><?php echo $agentInfo['last_login'] ?? '首次登录'; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="layui-col-md6">
			<div class="layui-card">
				<div class="layui-card-header">下级代理统计</div>
				<div class="layui-card-body">
					<table class="layui-table">
						<tbody>
							<tr>
								<td width="150">下级代理数</td>
								<td id="subagent_count" style="color: #1E9FFF; font-weight: bold; font-size: 18px;">0</td>
							</tr>
							<tr>
								<td>启用代理</td>
								<td id="subagent_active" style="color: #5FB878; font-weight: bold;">0</td>
							</tr>
							<tr>
								<td>禁用代理</td>
								<td id="subagent_disabled" style="color: #FF5722; font-weight: bold;">0</td>
							</tr>
							<tr>
								<td>下级总余额</td>
								<td id="subagent_balance" style="color: #FFB800; font-weight: bold; font-size: 16px;">¥0.00</td>
							</tr>
							<tr>
								<td>下级总卡密</td>
								<td id="subagent_kami" style="color: #33cabb; font-weight: bold;">0</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center;">
									<a href="sub_agent_list.php" class="layui-btn layui-btn-sm layui-btn-normal">
										<i class="layui-icon layui-icon-group"></i> 管理下级代理
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<script src="../assets/layui/layui.js"></script>
	<script>
		layui.use(['jquery'], function() {
			var $ = layui.jquery;

			// 加载统计数据
			function loadStats() {
				$.ajax({
					url: 'ajax.php?act=getstats',
					type: 'GET',
					dataType: 'json',
					success: function(res) {
						if (res.code == '1') {
							$('#balance').text('¥' + parseFloat(res.balance).toFixed(2));
							$('#kaminum').text(res.kaminum || 0);
							$('#usedkami').text(res.usedkami || 0);
							$('#todaykami').text(res.todaykami || 0);
						}
					}
				});
			}

			// 加载下级代理统计
			function loadSubAgentStats() {
				$.ajax({
					url: 'ajax.php?act=getsubagentstats',
					type: 'GET',
					dataType: 'json',
					success: function(res) {
						if (res.code == '1') {
							$('#subagent_count').text(res.total || 0);
							$('#subagent_active').text(res.active || 0);
							$('#subagent_disabled').text(res.disabled || 0);
							$('#subagent_balance').text('¥' + parseFloat(res.balance || 0).toFixed(2));
							$('#subagent_kami').text(res.kami || 0);
						}
					}
				});
			}

			loadStats();
			loadSubAgentStats();
		});
	</script>
</body>
</html>

