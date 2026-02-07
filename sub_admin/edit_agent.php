<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$agent = $DB->selectRow("select * from agent where id=" . $id);
if (!$agent) {
	exit('代理不存在');
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>编辑代理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
	<style>
		body {
			padding: 20px;
		}
	</style>
</head>

<body class="layui-form form">
	<input type="hidden" name="id" value="<?php echo $agent['id']; ?>">

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理账号
		</label>
		<div class="layui-input-block">
			<input type="text" value="<?php echo $agent['username']; ?>" disabled class="layui-input layui-disabled">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理密码
		</label>
		<div class="layui-input-block">
			<input type="password" name="password" class="layui-input" placeholder="不修改请留空">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理名称
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="name" required lay-verify="required" class="layui-input" value="<?php echo $agent['name']; ?>">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理级别
		</label>
		<div class="layui-input-block">
			<select name="level" lay-verify="required">
				<option value="5" <?php echo $agent['level'] == 5 ? 'selected' : ''; ?>>Level 5 - 至尊代理（最高）</option>
				<option value="4" <?php echo $agent['level'] == 4 ? 'selected' : ''; ?>>Level 4 - 钻石代理</option>
				<option value="3" <?php echo $agent['level'] == 3 ? 'selected' : ''; ?>>Level 3 - 金牌代理</option>
				<option value="2" <?php echo $agent['level'] == 2 ? 'selected' : ''; ?>>Level 2 - 高级代理</option>
				<option value="1" <?php echo $agent['level'] == 1 ? 'selected' : ''; ?>>Level 1 - 普通代理（最低）</option>
			</select>
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			上级代理
		</label>
		<div class="layui-input-block">
			<select name="parent_id" lay-search="">
				<option value="0" <?php echo $agent['parent_id'] == 0 ? 'selected' : ''; ?>>总后台直属</option>
			</select>
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			权限设置
		</label>
		<div class="layui-input-block">
			<input type="checkbox" name="can_del_kami" title="允许删除卡密" <?php echo empty($agent['can_del_kami']) ? '' : 'checked'; ?> lay-skin="primary">
			<input type="checkbox" name="can_del_user" title="允许删除用户" <?php echo empty($agent['can_del_user']) ? '' : 'checked'; ?> lay-skin="primary">
		</div>
	</div>

	<div class="layui-form-item">
		<div class="layui-input-block">
			<button class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">保存修改</button>
		</div>
	</div>

	<script src="../assets/layui/layui.js"></script>
	<script>
		layui.use(['form', 'layer'], function() {
			var form = layui.form;
			var layer = layui.layer;
			var $ = layui.jquery;

			// 加载上级代理列表
			$.ajax({
				url: "ajax.php?act=getagent",
				type: "POST",
				dataType: "json",
				data: {page: 1, limit: 1000},
				success: function(res) {
					if (res.code == 0 && res.data) {
						var currentId = <?php echo $agent['id']; ?>;
						var currentParentId = <?php echo $agent['parent_id']; ?>;
						var html = '<option value="0">总后台直属</option>';
						for (var i = 0; i < res.data.length; i++) {
							var agent = res.data[i];
							// 不能选择自己作为上级
							if (agent.id != currentId) {
								var selected = agent.id == currentParentId ? 'selected' : '';
								html += '<option value="' + agent.id + '" ' + selected + '>' + agent.name + ' (ID:' + agent.id + ' / L' + agent.level + ')</option>';
							}
						}
						$('select[name="parent_id"]').html(html);
						form.render('select');
					}
				}
			});

			// 提交表单
			form.on("submit(submit)", function(data) {
				$.ajax({
					url: "ajax.php?act=editagent",
					type: "POST",
					dataType: "json",
					data: data.field,
					beforeSend: function() {
						layer.msg("正在提交", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(res) {
						if (res.code == "1") {
							parent.layer.msg(res.msg, {icon: 1});
							parent.reload('agent_list');
							var index = parent.layer.getFrameIndex(window.name);
							parent.layer.close(index);
						} else {
							layer.msg(res.msg, {icon: 5});
						}
					},
					error: function() {
						layer.msg("未知错误", {icon: 5});
					}
				});
				return false;
			});
		});
	</script>
</body>
</html>

