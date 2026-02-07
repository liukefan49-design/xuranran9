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
	<title>卡密管理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
</head>

<body>
	<div class="layui-card">
		<div class="layui-card-header">
			<span>卡密管理</span>
			<span style="float: right; color: #ff5722; font-weight: bold;">当前余额: ¥<?php echo number_format($agentInfo['balance'], 2); ?></span>
		</div>
		<div class="layui-card-body">
			<table id="agent_kami" lay-filter="agent_kami"></table>
		</div>
	</div>

	<script type="text/html" id="agent_kamiTool">
		<div class="layui-btn-container">
			<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">
				<i class="layui-icon layui-icon-add-1"></i>生成卡密
			</button>
			<button class="layui-btn layui-btn-sm" lay-event="reload">
				<i class="layui-icon layui-icon-refresh"></i>刷新
			</button>
		</div>
	</script>

	<script type="text/html" id="barDemo">
		<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
	</script>

	<script src="../assets/layui/layui.js"></script>
	<script>
		var canDelKami = <?php echo isset($agentInfo['can_del_kami']) ? intval($agentInfo['can_del_kami']) : 1; ?>;
		layui.use(['table', 'form', 'layer'], function() {
			var table = layui.table;
			var form = layui.form;
			var layer = layui.layer;
			var $ = layui.jquery;

			// 渲染表格
			table.render({
				elem: "#agent_kami",
				height: "full-200",
				url: "ajax.php?act=getkami",
				page: true,
				limit: 100,
				limits: [10, 20, 30, 50, 100, 200, 300, 500, 1000],
				title: "卡密列表",
				toolbar: "#agent_kamiTool",
				defaultToolbar: ['filter', 'print', 'exports'],
				cols: [
					[{
						type: "checkbox"
					}, {
						field: "id",
						title: "序号",
						width: 100,
						sort: true,
						align: "center"
					}, {
						field: "kami",
						title: "卡密",
						width: 250,
						align: "center",
						sort: true
					}, {
						field: "times",
						title: "时长",
						width: 120,
						align: "center",
						sort: true
					}, {
						field: "sc_user",
						title: "生成用户",
						width: 120,
						align: "center",
						sort: true
					}, {
						field: "found_date",
						title: "创建时间",
						width: 170,
						align: "center",
						sort: true
					}, {
						field: "username",
						title: "激活账号",
						width: 120,
						align: "center",
						sort: true
					}, {
						field: "use_date",
						title: "使用时间",
						width: 170,
						align: "center",
						sort: true
					}, {
						field: "state",
						title: "状态",
						sort: true,
						align: "center",
						width: 100,
						templet: function(d) {
							return d.state == 0 ? '<span style="color: green;">未使用</span>' : '<span style="color: red;">已使用</span>';
						}
					}, {
						field: "comment",
						title: "备注",
						align: "center"
					}, {
						fixed: "right",
						title: "操作",
						toolbar: "#barDemo",
						width: 100,
						align: "center"
					}]
				]
			});

			// 头工具栏事件
			table.on('toolbar(agent_kami)', function(obj) {
				switch (obj.event) {
					case 'add':
						layer.open({
							type: 2,
							title: '生成卡密',
							area: ['600px', '550px'],
							content: 'new_kami.php'
						});
						break;
					case 'reload':
						table.reload('agent_kami');
						break;
				}
			});

			// 行工具栏事件
			table.on('tool(agent_kami)', function(obj) {
				var data = obj.data;
				if (obj.event === 'del') {
					if (!canDelKami) {
						layer.msg('您暂无删除卡密权限', {icon: 5});
						return;
					}
					var statusText = data.state == 1 ? '已使用' : '未使用';
					var confirmMsg = '确定删除该卡密吗？<br>状态: ' + statusText + '<br>卡密: ' + data.kami;
					if (data.state == 1) {
						confirmMsg += '<br><span style="color:red;">注意：该卡密已被使用，删除后不影响已激活的用户账号</span>';
					}

					layer.confirm(confirmMsg, {icon: 3}, function(index) {
						$.ajax({
							url: 'ajax.php?act=delkami',
							type: 'POST',
							data: {id: data.id},
							dataType: 'json',
							success: function(res) {
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									obj.del();
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							}
						});
						layer.close(index);
					});
				}
			});

			// 全局刷新函数
			window.reload = function(tableName) {
				table.reload(tableName);
			};
		});
	</script>
</body>
</html>

