<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$appcode = isset($_GET['appcode']) ? addslashes($_GET['appcode']) : '';
$app = $DB->selectRow("select * from application where appcode='" . $appcode . "'");
if (!$app) {
	exit('应用不存在');
}

// 获取统计数据
$total_kami = $DB->selectRow("select COUNT(*) as count from kami where app='" . $appcode . "'");
$used_kami = $DB->selectRow("select COUNT(*) as count from kami where app='" . $appcode . "' and state=1");
$unused_kami = $DB->selectRow("select COUNT(*) as count from kami where app='" . $appcode . "' and state=0");
$today_created = $DB->selectRow("select COUNT(*) as count from kami where app='" . $appcode . "' and DATE(found_date)=CURDATE()");
$today_used = $DB->selectRow("select COUNT(*) as count from kami where app='" . $appcode . "' and state=1 and DATE(use_date)=CURDATE()");

// 获取服务器信息
$server = $DB->selectRow("select * from server_list where ip='" . $app['serverip'] . "'");

// 获取最近7天制卡统计
$week_stats = [];
for ($i = 6; $i >= 0; $i--) {
	$date = date('Y-m-d', strtotime("-$i days"));
	$count = $DB->selectRow("select COUNT(*) as count from kami where app='" . $appcode . "' and DATE(found_date)='" . $date . "'");
	$week_stats[] = [
		'date' => $date,
		'count' => $count['count']
	];
}

// 获取应用的公开状态
$is_public = isset($app['is_public']) ? $app['is_public'] : 1;

// 获取已授权的代理列表
$authorized_agents = [];
if ($is_public == 0) {
	$sql = "SELECT a.*, agt.username, agt.name FROM app_agent_access a 
			LEFT JOIN agent agt ON a.agent_id = agt.id 
			WHERE a.appcode='" . $appcode . "' ORDER BY a.created_time DESC";
	$authorized_agents = $DB->select($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>应用详情</title>
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
		.agent-item {
			padding: 10px;
			margin-bottom: 10px;
			background: #f8f8f8;
			border-radius: 4px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
	</style>
</head>

<body>
	<!-- 基本信息 -->
	<div class="stat-card">
		<h3 style="margin-top:0;">基本信息</h3>
		<table class="info-table">
			<tr>
				<td>应用ID</td>
				<td><?php echo $app['appid']; ?></td>
			</tr>
			<tr>
				<td>应用码</td>
				<td><span style="color: #1E9FFF; font-weight: bold;"><?php echo $app['appcode']; ?></span></td>
			</tr>
			<tr>
				<td>应用名称</td>
				<td><?php echo $app['appname']; ?></td>
			</tr>
			<tr>
				<td>服务器IP</td>
				<td><?php echo $app['serverip']; ?></td>
			</tr>
			<tr>
				<td>服务器备注</td>
				<td><?php echo $server ? $server['comment'] : '-'; ?></td>
			</tr>
			<tr>
				<td>所属用户</td>
				<td><?php echo $app['username']; ?></td>
			</tr>
			<tr>
				<td>创建时间</td>
				<td><?php echo $app['found_time']; ?></td>
			</tr>
			<tr>
				<td>访问权限</td>
				<td>
					<div class="layui-form">
						<input type="radio" name="is_public" value="1" title="所有代理可见" <?php echo $is_public == 1 ? 'checked' : ''; ?> lay-filter="is_public">
						<input type="radio" name="is_public" value="0" title="仅指定代理可见" <?php echo $is_public == 0 ? 'checked' : ''; ?> lay-filter="is_public">
					</div>
				</td>
			</tr>
		</table>
	</div>

	<!-- 代理访问权限设置 -->
	<div class="stat-card" id="agent_access_card" style="display: <?php echo $is_public == 0 ? 'block' : 'none'; ?>;">
		<h3 style="margin-top:0;">
			授权代理列表
			<button class="layui-btn layui-btn-sm layui-btn-normal" style="float: right;" id="add_agent_btn">
				<i class="layui-icon layui-icon-add-1"></i> 添加代理
			</button>
		</h3>
		<div id="agent_list">
			<?php if (empty($authorized_agents)): ?>
				<div style="text-align: center; padding: 30px; color: #999;">
					暂无授权代理，请点击"添加代理"按钮添加
				</div>
			<?php else: ?>
				<?php foreach ($authorized_agents as $agent): ?>
					<div class="agent-item">
						<div>
							<strong><?php echo $agent['username']; ?></strong>
							<span style="color: #999; margin-left: 10px;"><?php echo $agent['name']; ?></span>
							<span style="color: #999; margin-left: 10px; font-size: 12px;">授权时间: <?php echo $agent['created_time']; ?></span>
						</div>
						<button class="layui-btn layui-btn-danger layui-btn-xs remove-agent" data-id="<?php echo $agent['id']; ?>">
							<i class="layui-icon layui-icon-delete"></i> 移除
						</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- 统计数据 -->
	<div class="stat-card">
		<h3 style="margin-top:0;">卡密统计</h3>
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
					<div class="stat-value" style="color: #ff5722;"><?php echo number_format(($used_kami['count'] / max($total_kami['count'], 1)) * 100, 1); ?>%</div>
					<div class="stat-label">使用率</div>
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
					<div class="stat-label">今日使用</div>
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
		var appcode = '<?php echo $appcode; ?>';
		
		layui.use(['table', 'layer', 'form'], function() {
			var table = layui.table;
			var layer = layui.layer;
			var form = layui.form;
			var $ = layui.$;

			// 渲染卡密列表
			table.render({
				elem: '#kami_list',
				url: 'ajax.php?act=getkami&app=' + appcode,
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
					{field: 'found_date', title: '生成时间', width: 160, align: 'center'},
					{field: 'use_date', title: '使用时间', width: 160, align: 'center', templet: function(d) {
						return d.use_date || '-';
					}}
				]]
			});

			// 监听访问权限切换
			form.on('radio(is_public)', function(data) {
				var is_public = data.value;
				
				layer.confirm('确定要修改访问权限吗？', {icon: 3}, function(index) {
					$.ajax({
						url: 'ajax.php?act=updateapppublic',
						type: 'POST',
						data: {
							appcode: appcode,
							is_public: is_public
						},
						dataType: 'json',
						success: function(res) {
							if (res.code == '1') {
								layer.msg(res.msg, {icon: 1});
								if (is_public == '0') {
									$('#agent_access_card').show();
								} else {
									$('#agent_access_card').hide();
								}
							} else {
								layer.msg(res.msg, {icon: 5});
								// 恢复原来的选择
								location.reload();
							}
						}
					});
					layer.close(index);
				}, function() {
					// 取消时恢复原来的选择
					location.reload();
				});
			});

			// 添加代理
			$('#add_agent_btn').click(function() {
				layer.open({
					type: 2,
					title: '添加授权代理',
					area: ['500px', '400px'],
					content: 'app_add_agent.php?appcode=' + appcode
				});
			});

			// 移除代理
			$(document).on('click', '.remove-agent', function() {
				var id = $(this).data('id');
				var $item = $(this).closest('.agent-item');
				
				layer.confirm('确定要移除该代理的访问权限吗？', {icon: 3}, function(index) {
					$.ajax({
						url: 'ajax.php?act=removeagentaccess',
						type: 'POST',
						data: {id: id},
						dataType: 'json',
						success: function(res) {
							if (res.code == '1') {
								layer.msg(res.msg, {icon: 1});
								$item.remove();
								
								// 如果没有代理了，显示提示
								if ($('#agent_list .agent-item').length == 0) {
									$('#agent_list').html('<div style="text-align: center; padding: 30px; color: #999;">暂无授权代理，请点击"添加代理"按钮添加</div>');
								}
							} else {
								layer.msg(res.msg, {icon: 5});
							}
						}
					});
					layer.close(index);
				});
			});

			// 全局刷新函数
			window.reloadAgentList = function() {
				location.reload();
			};
		});
	</script>
</body>
</html>

