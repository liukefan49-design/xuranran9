<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$agent = $DB->selectRow("select * from agent where id=" . $id);
if (!$agent) {
	exit('代理不存在');
}

// 获取统计数据
$total_kami = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id);
$used_kami = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and state=1");
$unused_kami = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and state=0");
$today_created = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and DATE(found_date)=CURDATE()");
$today_used = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and state=1 and DATE(use_date)=CURDATE()");

// 获取今日使用的卡密类型统计
$today_used_hour = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and state=1 and DATE(use_date)=CURDATE() and times like '%hour%'");
$today_used_day = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and state=1 and DATE(use_date)=CURDATE() and times like '%day%'");
$today_used_month = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and state=1 and DATE(use_date)=CURDATE() and times like '%month%'");
$today_used_year = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and state=1 and DATE(use_date)=CURDATE() and times like '%year%'");

// 获取最近7天制卡统计
$week_stats = [];
for ($i = 6; $i >= 0; $i--) {
	$date = date('Y-m-d', strtotime("-$i days"));
	$count = $DB->selectRow("select COUNT(*) as count from kami where agent_id=" . $id . " and DATE(found_date)='" . $date . "'");
	$week_stats[] = [
		'date' => $date,
		'count' => $count['count']
	];
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>代理详情</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
	<style>
		body {
			padding: 15px;
			background: #f2f2f2;
		}
		.stat-card {
			background: #fff;
			padding: 20px;
			margin-bottom: 15px;
			border-radius: 4px;
			box-shadow: 0 1px 2px rgba(0,0,0,0.1);
		}
		.stat-item {
			text-align: center;
			padding: 15px;
		}
		.stat-value {
			font-size: 28px;
			font-weight: bold;
			color: #1E9FFF;
		}
		.stat-label {
			color: #666;
			margin-top: 5px;
		}
		.info-table {
			width: 100%;
		}
		.info-table td {
			padding: 10px;
			border-bottom: 1px solid #f0f0f0;
		}
		.info-table td:first-child {
			width: 120px;
			color: #666;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<!-- 基本信息 -->
	<div class="stat-card">
		<h3 style="margin-top:0;">基本信息</h3>
		<table class="info-table">
			<tr>
				<td>代理ID</td>
				<td><?php echo $agent['id']; ?></td>
			</tr>
			<tr>
				<td>代理账号</td>
				<td><?php echo $agent['username']; ?></td>
			</tr>
			<tr>
				<td>代理名称</td>
				<td><?php echo $agent['name']; ?></td>
			</tr>
			<tr>
				<td>账户余额</td>
				<td><span style="color: #ff5722; font-size: 18px; font-weight: bold;">¥<?php echo number_format($agent['balance'], 2); ?></span></td>
			</tr>
			<tr>
				<td>代理级别</td>
				<td>Level <?php echo $agent['level']; ?></td>
			</tr>
			<tr>
				<td>上级代理</td>
				<td><?php echo $agent['parent_id'] == 0 ? '总后台' : 'ID: ' . $agent['parent_id']; ?></td>
			</tr>
			<tr>
				<td>账号状态</td>
				<td>
					<?php if ($agent['state'] == 1): ?>
						<span style="color: green; font-weight: bold;">启用</span>
					<?php else: ?>
						<span style="color: red; font-weight: bold;">禁用</span>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>创建时间</td>
				<td><?php echo $agent['created_time']; ?></td>
			</tr>
			<tr>
				<td>最后登录</td>
				<td><?php echo $agent['last_login'] ?: '从未登录'; ?></td>
			</tr>
		</table>
	</div>

	<!-- 统计数据 -->
	<div class="stat-card">
		<h3 style="margin-top:0;">统计数据</h3>
		<div class="layui-row layui-col-space15">
			<div class="layui-col-md3">
				<div class="stat-item">
					<div class="stat-value" style="color: #1E9FFF;"><?php echo $total_kami['count']; ?></div>
					<div class="stat-label">总卡密数</div>
				</div>
			</div>
			<div class="layui-col-md3">
				<div class="stat-item">
					<div class="stat-value" style="color: #5FB878;"><?php echo $used_kami['count']; ?></div>
					<div class="stat-label">已使用</div>
				</div>
			</div>
			<div class="layui-col-md3">
				<div class="stat-item">
					<div class="stat-value" style="color: #FFB800;"><?php echo $unused_kami['count']; ?></div>
					<div class="stat-label">未使用</div>
				</div>
			</div>
			<div class="layui-col-md3">
				<div class="stat-item">
					<div class="stat-value" style="color: #ff5722;"><?php echo number_format($agent['balance'], 2); ?></div>
					<div class="stat-label">当前余额</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 今日数据 -->
	<div class="stat-card">
		<h3 style="margin-top:0;">今日数据</h3>
		<div class="layui-row layui-col-space15">
			<div class="layui-col-md6">
				<div class="stat-item">
					<div class="stat-value" style="color: #1E9FFF;"><?php echo $today_created['count']; ?></div>
					<div class="stat-label">今日制卡</div>
				</div>
			</div>
			<div class="layui-col-md6">
				<div class="stat-item">
					<div class="stat-value" style="color: #5FB878;"><?php echo $today_used['count']; ?></div>
					<div class="stat-label">今日使用（全部）</div>
				</div>
			</div>
		</div>

		<!-- 今日使用详细统计 -->
		<div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
			<h4 style="margin: 0 0 15px 0; color: #666;">今日使用详情（按卡密类型）</h4>
			<div class="layui-row layui-col-space15">
				<div class="layui-col-md3">
					<div class="stat-item" style="background: #f8f8f8; border-radius: 4px;">
						<div class="stat-value" style="color: #FF5722; font-size: 24px;"><?php echo $today_used_hour['count']; ?></div>
						<div class="stat-label" style="font-size: 13px;">小时卡</div>
					</div>
				</div>
				<div class="layui-col-md3">
					<div class="stat-item" style="background: #f8f8f8; border-radius: 4px;">
						<div class="stat-value" style="color: #FFB800; font-size: 24px;"><?php echo $today_used_day['count']; ?></div>
						<div class="stat-label" style="font-size: 13px;">天卡</div>
					</div>
				</div>
				<div class="layui-col-md3">
					<div class="stat-item" style="background: #f8f8f8; border-radius: 4px;">
						<div class="stat-value" style="color: #009688; font-size: 24px;"><?php echo $today_used_month['count']; ?></div>
						<div class="stat-label" style="font-size: 13px;">月卡</div>
					</div>
				</div>
				<div class="layui-col-md3">
					<div class="stat-item" style="background: #f8f8f8; border-radius: 4px;">
						<div class="stat-value" style="color: #01AAED; font-size: 24px;"><?php echo $today_used_year['count']; ?></div>
						<div class="stat-label" style="font-size: 13px;">年卡</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 最近7天制卡趋势 -->
	<div class="stat-card">
		<h3 style="margin-top:0;">最近7天制卡趋势</h3>
		<table class="layui-table">
			<thead>
				<tr>
					<th>日期</th>
					<th>制卡数量</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($week_stats as $stat): ?>
				<tr>
					<td><?php echo $stat['date']; ?></td>
					<td><span style="color: #1E9FFF; font-weight: bold;"><?php echo $stat['count']; ?></span></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<!-- 卡密列表 -->
	<div class="stat-card">
		<h3 style="margin-top:0;">卡密列表（最近20条）</h3>
		<table id="kami_list" lay-filter="kami_list"></table>
	</div>

	<script src="../assets/layui/layui.js"></script>
	<script>
		layui.use(['table', 'layer'], function() {
			var table = layui.table;
			var layer = layui.layer;

			// 渲染卡密列表
			table.render({
				elem: '#kami_list',
				url: 'ajax.php?act=getagentkami&agent_id=<?php echo $id; ?>',
				page: true,
				limit: 20,
				limits: [20, 50, 100],
				cols: [[
					{field: 'id', title: 'ID', width: 80, align: 'center'},
					{field: 'kami', title: '卡密', width: 200, align: 'center'},
					{field: 'times', title: '时长', width: 100, align: 'center'},
					{field: 'state', title: '状态', width: 100, align: 'center', templet: function(d) {
						return d.state == 1 ? '<span style="color: green;">已使用</span>' : '<span style="color: blue;">未使用</span>';
					}},
					{field: 'comment', title: '备注', width: 150, align: 'center'},
					{field: 'addtime', title: '生成时间', width: 160, align: 'center'},
					{field: 'use_date', title: '使用时间', width: 160, align: 'center', templet: function(d) {
						return d.use_date || '-';
					}}
				]]
			});
		});
	</script>
</body>
</html>

