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
	<title>用户管理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
	<style>
		.layui-card {
			margin-bottom: 15px;
		}
	</style>
</head>

<body>
	<!-- 筛选条件 -->
	<div class="layui-card">
		<div class="layui-card-body layui-form">
			<div class="layui-form-item" style="padding-right: 5vw;padding-top: 15px;">
				<label class="layui-form-label" title="用户名">
					用户名：
				</label>
				<div class="layui-input-inline">
					<input type="text" name="user" class="layui-input" placeholder="请输入用户名" />
				</div>
				<label class="layui-form-label" title="应用">
					应用：
				</label>
				<div class="layui-input-inline">
					<select name="app" lay-filter="app">
						<option value="">全部应用</option>
					</select>
				</div>
				<label class="layui-form-label" title="状态">
					状态：
				</label>
				<div class="layui-input-inline">
					<select name="expire" lay-filter="expire">
						<option value="">全部</option>
						<option value="0">正常</option>
						<option value="1">已过期</option>
					</select>
				</div>
			</div>
		</div>
	</div>

	<!-- 表格 -->
	<div class="layui-card">
		<div class="layui-card-body">
			<table id="user_list" lay-filter="user_list"></table>
		</div>
	</div>

	<script type="text/html" id="user_listTool">
		<div class="layui-btn-container">
			<button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="search">
				<i class="layui-icon layui-icon-search"></i><span>搜索</span>
			</button>
			<button class="layui-btn layui-btn-sm" lay-event="reload">
				<i class="layui-icon layui-icon-refresh"></i><span>刷新</span>
			</button>
			<button class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">
				<i class="layui-icon layui-icon-delete"></i><span>批量删除</span>
			</button>
		</div>
	</script>

	<!-- 操作栏 -->
	<script type="text/html" id="barDemo">
		<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
	</script>

	<!-- 状态开关 -->
	<script type="text/html" id="stateTool">
		<input type="checkbox" name="state" value="{{d.state}}" lay-skin="switch" lay-text="开启|关闭" lay-filter="state" {{ d.state == "1" ? 'checked' : '' }} disabled />
	</script>

	<!-- 密码状态点 -->
	<script type="text/html" id="pwddot">
		<span style="width: 20px;height: 20px;" class="layui-badge-dot {{d.pwdstate==1?'green':''}}"></span>
	</script>

	<!-- 过期状态点 -->
	<script type="text/html" id="expirdot">
		<span style="width: 20px;height: 20px;" class="layui-badge-dot {{d.expire==0?'green':''}}"></span>
	</script>

	<style>
		.green {
			background-color: #33cabb;
		}
	</style>

	<script src="../assets/layui/layui.js"></script>
	<script>
		var canDelUser = <?php echo isset($agentInfo['can_del_user']) ? intval($agentInfo['can_del_user']) : 1; ?>;
		layui.use(['jquery', 'table', 'form', 'layer'], function() {
			var $ = layui.$;
			var table = layui.table;
			var form = layui.form;
			var layer = layui.layer;

			// 加载应用列表
			function loadApps() {
				$.ajax({
					url: 'ajax.php?act=getagentapps',
					type: 'GET',
					dataType: 'json',
					success: function(res) {
						if (res.code == '1' && res.data) {
							var html = '<option value="">全部应用</option>';
							res.data.forEach(function(app) {
								html += '<option value="' + app.appcode + '">' + app.appname + '</option>';
							});
							$('select[name="app"]').html(html);
							form.render('select');
						}
					}
				});
			}

			// 获取查询条件
			function where() {
				return {
					user: $('[name=user]').val(),
					app: $('[name=app]').val(),
					expire: $('[name=expire]').val()
				};
			}

			// 渲染表格
			table.render({
				elem: '#user_list',
				height: 'full-170',
				url: 'ajax.php?act=getagentusers',
				page: true,
				limit: 100,
				limits: [10, 20, 30, 50, 100, 200, 300, 500],
				title: '用户列表',
				toolbar: '#user_listTool',
				where: where(),
				cols: [[
					{type: 'checkbox'},
					{field: 'id', title: '序号', width: 80, sort: true, align: 'center'},
					{field: 'user', title: '用户名', width: 150, align: 'center'},
					{field: 'pwd', title: '密码', width: 150, align: 'center'},
					{field: 'state', title: '账号状态', width: 100, align: 'center', toolbar: '#stateTool'},
					{field: 'pwdstate', title: '密码状态', width: 100, align: 'center', toolbar: '#pwddot'},
					{field: 'disabletime', title: '到期时间', width: 180, align: 'center'},
					{field: 'expire', title: '过期状态', width: 100, align: 'center', toolbar: '#expirdot'},
					{field: 'serverip', title: '服务器IP', width: 150, align: 'center'},
					{field: 'connection', title: '连接数', width: 100, align: 'center', templet: function(d) {
						return d.connection == -1 ? '无限制' : d.connection;
					}},
					{field: 'bandwidthup', title: '上传带宽(MB)', width: 130, align: 'center'},
					{field: 'bandwidthdown', title: '下载带宽(MB)', width: 130, align: 'center'},
					{field: 'app', title: '所属应用', width: 150, align: 'center'},
				{fixed: 'right', title: '操作', toolbar: '#barDemo', width: 100, align: 'center'}
				]]
			});

			// 头工具栏事件
			table.on('toolbar(user_list)', function(obj) {
				switch(obj.event) {
					case 'search':
						table.reload('user_list', {
							where: where(),
							page: {curr: 1}
						});
						break;
					case 'reload':
						table.reload('user_list');
						break;
					case 'del':
						if (!canDelUser) { layer.msg('您暂无删除用户权限', {icon: 5}); break; }
						var checkStatus = table.checkStatus('user_list');
						var data = checkStatus.data;
						if (data.length === 0) {
							layer.msg('请选择要删除的用户', {icon: 3});
							return;
						}

						layer.confirm('确定删除选中的 ' + data.length + ' 个用户吗？删除后无法恢复！', {icon: 3}, function(index) {
							var successCount = 0;
							var failCount = 0;
							var total = data.length;

							layer.msg('正在删除...', {icon: 16, shade: 0.3, time: false});

							// 逐个删除用户
							function deleteNext(i) {
								if (i >= total) {
									layer.closeAll('msg');
									if (failCount === 0) {
										layer.msg('全部删除成功！', {icon: 1});
									} else {
										layer.msg('删除完成！成功: ' + successCount + '，失败: ' + failCount, {icon: 2});
									}
									table.reload('user_list');
									return;
								}

								$.ajax({
									url: 'ajax.php?act=delagentuser',
									type: 'POST',
									data: {
										username: data[i].user,
										serverip: data[i].serverip
									},
									dataType: 'json',
									success: function(res) {
										if (res.code == '1') {
											successCount++;
										} else {
											failCount++;
											console.log('删除失败: ' + data[i].user + ' - ' + res.msg);
										}
										deleteNext(i + 1);
									},
									error: function() {
										failCount++;
										console.log('删除失败: ' + data[i].user);
										deleteNext(i + 1);
									}
								});
							}

							deleteNext(0);
							layer.close(index);
						});
						break;
				}
			});

			// 行工具栏事件
			table.on('tool(user_list)', function(obj) {
				var data = obj.data;
				if (obj.event === 'del') {
					if (!canDelUser) { layer.msg('您暂无删除用户权限', {icon: 5}); return; }
					layer.confirm('确定删除用户 "' + data.user + '" 吗？删除后无法恢复！', {icon: 3}, function(index) {
						layer.msg('正在删除...', {icon: 16, shade: 0.3, time: false});

						$.ajax({
							url: 'ajax.php?act=delagentuser',
							type: 'POST',
							data: {
								username: data.user,
								serverip: data.serverip
							},
							dataType: 'json',
							success: function(res) {
								layer.closeAll('msg');
								if (res.code == '1') {
									layer.msg(res.msg, {icon: 1});
									obj.del();
								} else {
									layer.msg(res.msg, {icon: 5});
								}
							},
							error: function() {
								layer.closeAll('msg');
								layer.msg('删除失败，请稍后重试', {icon: 5});
							}
						});
						layer.close(index);
					});
				}
			});

			// 监听搜索条件变化
			$('.layui-input').keydown(function(e) {
				if (e.keyCode == 13) {
					table.reload('user_list', {
						where: where(),
						page: {curr: 1}
					});
				}
			});

			form.on('select(app)', function() {
				table.reload('user_list', {
					where: where(),
					page: {curr: 1}
				});
			});

			form.on('select(expire)', function() {
				table.reload('user_list', {
					where: where(),
					page: {curr: 1}
				});
			});

			// 初始化
			loadApps();
		});
	</script>
</body>
</html>

