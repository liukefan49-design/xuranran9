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
	<title>下级代理管理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
</head>

<body>
	<div class="layui-card">
		<div class="layui-card-header">
			下级代理管理
			<span style="color: #999; font-size: 12px; margin-left: 20px;">
				我的级别: Level <?php echo $agentInfo['level']; ?> | 
				可添加级别: Level <?php echo ($agentInfo['level'] + 1); ?> - Level 5
			</span>
		</div>
		<div class="layui-card-body">
			<table id="sub_agent_list" lay-filter="sub_agent_list"></table>
		</div>
	</div>

	<script type="text/html" id="sub_agent_listTool">
		<div class="layui-btn-container">
			<button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">
				<i class="layui-icon layui-icon-add-1"></i>添加下级代理
			</button>
			<button class="layui-btn layui-btn-sm" lay-event="reload">
				<i class="layui-icon layui-icon-refresh"></i>刷新
			</button>
		</div>
	</script>

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
				elem: "#sub_agent_list",
				height: "full-200",
				url: "ajax.php?act=getsubagent",
				page: true,
				limit: 50,
				limits: [10, 20, 30, 50, 100],
				title: "下级代理列表",
				toolbar: "#sub_agent_listTool",
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
			table.on('toolbar(sub_agent_list)', function(obj) {
				if (obj.event === 'add') {
					layer.open({
						type: 2,
						title: '添加下级代理',
						area: ['600px', '550px'],
						content: 'new_sub_agent.php'
					});
				} else if (obj.event === 'reload') {
					table.reload('sub_agent_list');
				}
			});

			// 行工具栏事件
			table.on('tool(sub_agent_list)', function(obj) {
				var data = obj.data;
				
				if (obj.event === 'detail') {
					layer.open({
						type: 2,
						title: '代理详情 - ' + data.username,
						area: ['900px', '600px'],
						content: 'sub_agent_detail.php?id=' + data.id
					});
				} else if (obj.event === 'recharge') {
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
							url: 'ajax.php?act=rechargesubagent',
							type: 'POST',
							data: {id: data.id, amount: amount},
							dataType: 'json',
							success: function(res) {
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									table.reload('sub_agent_list');
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							}
						});
						layer.close(index);
					});
				} else if (obj.event === 'edit') {
					layer.open({
						type: 2,
						title: '编辑代理',
						area: ['600px', '450px'],
						content: 'edit_sub_agent.php?id=' + data.id
					});
				} else if (obj.event === 'disable') {
					layer.confirm('确定禁用该代理吗？', function(index) {
						$.ajax({
							url: 'ajax.php?act=updatesubagentstate',
							type: 'POST',
							data: {id: data.id, state: 0},
							dataType: 'json',
							success: function(res) {
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									table.reload('sub_agent_list');
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							}
						});
						layer.close(index);
					});
				} else if (obj.event === 'enable') {
					layer.confirm('确定启用该代理吗？', function(index) {
						$.ajax({
							url: 'ajax.php?act=updatesubagentstate',
							type: 'POST',
							data: {id: data.id, state: 1},
							dataType: 'json',
							success: function(res) {
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									table.reload('sub_agent_list');
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							}
						});
						layer.close(index);
					});
				} else if (obj.event === 'del') {
					layer.confirm('确定删除该代理吗？删除后不可恢复！', function(index) {
						$.ajax({
							url: 'ajax.php?act=delsubagent',
							type: 'POST',
							data: {id: data.id},
							dataType: 'json',
							success: function(res) {
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									table.reload('sub_agent_list');
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							}
						});
						layer.close(index);
					});
				}
			});

			// 全局reload函数
			window.reload = function(tableId) {
				table.reload(tableId);
			};
		});
	</script>
</body>
</html>

