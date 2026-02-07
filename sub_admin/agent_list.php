<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>代理管理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
</head>

<body>
	<div class="layui-card">
		<div class="layui-card-header">
			<span>代理管理</span>
			<div style="float: right;">
				<button class="layui-btn layui-btn-sm layui-btn-primary" style="margin-right: 10px;" onclick="showCardView()">
					<i class="layui-icon layui-icon-template"></i>卡片视图
				</button>
				<button class="layui-btn layui-btn-sm layui-btn-normal" onclick="addAgent()">
					<i class="layui-icon layui-icon-add-1"></i>添加代理
				</button>
				<button class="layui-btn layui-btn-sm" style="margin-left: 10px;" onclick="reload()">
					<i class="layui-icon layui-icon-refresh"></i>刷新
				</button>
			</div>
		</div>
		<div class="layui-card-body">
			<table id="agent_list" lay-filter="agent_list"></table>
		</div>
	</div>

			// 添加代理
			window.addAgent = function() {
				layer.open({
					type: 2,
					title: '添加代理',
					area: ['90%', '80%'],
					maxmin: true,
					content: 'new_agent.php'
				});
			};

			// 卡片视图
			window.showCardView = function() {
				window.location.href = 'agent_list_view.php';
			};

	<script type="text/html" id="barDemo">
		<a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
		<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="recharge">充值</a>
		<a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
		{{# if(d.state == 1) { }}
		<a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="disable">禁用</a>
		{{# } else { }}
		<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="enable">启用</a>
		{{# } }}
		<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
	</script>

	<script src="../assets/layui/layui.js"></script>
	<script>
		layui.use(['table', 'form', 'layer'], function() {
			var table = layui.table;
			var form = layui.form;
			var layer = layui.layer;
			var $ = layui.jquery;

			// 渲染表格
			table.render({
				elem: "#agent_list",
				height: "full-200",
				url: "ajax.php?act=getagent",
				page: true,
				limit: 100,
				limits: [10, 20, 30, 50, 100, 200],
				title: "代理列表",
				toolbar: "#agent_listTool",
				defaultToolbar: ['filter', 'print'],
				cols: [
					[{
						type: "checkbox"
					}, {
						field: "id",
						title: "ID",
						width: 80,
						sort: true,
						align: "center"
					}, {
						field: "username",
						title: "代理账号",
						width: 150,
						align: "center"
					}, {
						field: "name",
						title: "代理名称",
						width: 150,
						align: "center"
					}, {
						field: "balance",
						title: "余额",
						width: 120,
						align: "center",
						templet: function(d) {
							return '<span style="color: #ff5722; font-weight: bold;">¥' + parseFloat(d.balance).toFixed(2) + '</span>';
						}
					}, {
						field: "total_kami",
						title: "总卡密",
						width: 100,
						align: "center",
						templet: function(d) {
							return d.total_kami || 0;
						}
					}, {
						field: "used_kami",
						title: "已使用",
						width: 100,
						align: "center",
						templet: function(d) {
							return '<span style="color: green;">' + (d.used_kami || 0) + '</span>';
						}
					}, {
						field: "today_kami",
						title: "今日制卡",
						width: 100,
						align: "center",
						templet: function(d) {
							return '<span style="color: #1E9FFF;">' + (d.today_kami || 0) + '</span>';
						}
					}, {
						field: "today_used",
						title: "今日使用",
						width: 100,
						align: "center",
						templet: function(d) {
							return '<span style="color: #FFB800;">' + (d.today_used || 0) + '</span>';
						}
					}, {
						field: "level",
						title: "级别",
						width: 80,
						align: "center",
						templet: function(d) {
							return 'L' + d.level;
						}
					}, {
						field: "state",
						title: "状态",
						width: 90,
						align: "center",
						templet: function(d) {
							return d.state == 1 ? '<span style="color: green;">启用</span>' : '<span style="color: red;">禁用</span>';
						}
					}, {
						field: "created_time",
						title: "创建时间",
						width: 160,
						align: "center"
					}, {
						field: "last_login",
						title: "最后登录",
						width: 160,
						align: "center"
					}, {
						fixed: "right",
						title: "操作",
						toolbar: "#barDemo",
						width: 280,
						align: "center"
					}]
				]
			});

			// 头工具栏事件
			table.on('toolbar(agent_list)', function(obj) {
				switch (obj.event) {
					case 'add':
						layer.open({
							type: 2,
							title: '添加代理',
							area: ['500px', '450px'],
							content: 'new_agent.php'
						});
						break;
					case 'reload':
						table.reload('agent_list');
						break;
				}
			});

			// 行工具栏事件
			table.on('tool(agent_list)', function(obj) {
				var data = obj.data;

				if (obj.event === 'detail') {
					// 查看详情
					layer.open({
						type: 2,
						title: '代理详情 - ' + data.username,
						area: ['900px', '600px'],
						content: 'agent_detail.php?id=' + data.id
					});
				} else if (obj.event === 'recharge') {
					// 充值
					layer.prompt({
						title: '充值余额 - ' + data.username,
						formType: 0,
						value: ''
					}, function(value, index) {
						var amount = parseFloat(value);
						if (isNaN(amount) || amount <= 0) {
							layer.msg('请输入正确的金额', {icon: 5});
							return;
						}
						
						$.ajax({
							url: 'ajax.php?act=rechargeagent',
							type: 'POST',
							data: {id: data.id, amount: amount},
							dataType: 'json',
							success: function(res) {
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									table.reload('agent_list');
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							}
						});
						layer.close(index);
					});
				} else if (obj.event === 'edit') {
					// 编辑
					layer.open({
						type: 2,
						title: '编辑代理',
						area: ['500px', '400px'],
						content: 'edit_agent.php?id=' + data.id
					});
				} else if (obj.event === 'disable') {
					// 禁用
					layer.confirm('确定禁用该代理吗？', function(index) {
						$.ajax({
							url: 'ajax.php?act=updateagentstate',
							type: 'POST',
							data: {id: data.id, state: 0},
							dataType: 'json',
							success: function(res) {
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									table.reload('agent_list');
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							}
						});
						layer.close(index);
					});
				} else if (obj.event === 'enable') {
					// 启用
					$.ajax({
						url: 'ajax.php?act=updateagentstate',
						type: 'POST',
						data: {id: data.id, state: 1},
						dataType: 'json',
						success: function(res) {
							if (res.code == '1') {
								layer.msg(res.msg, {icon: 1});
								table.reload('agent_list');
							} else {
								layer.msg(res.msg, {icon: 5});
							}
						}
					});
				} else if (obj.event === 'del') {
					// 删除
					layer.confirm('确定删除该代理吗？删除后无法恢复！', function(index) {
						$.ajax({
							url: 'ajax.php?act=delagent',
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

