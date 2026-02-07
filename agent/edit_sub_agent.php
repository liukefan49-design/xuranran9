<?php
include '../includes/common.php';
include '../includes/agent_auth.php';
if ($isAgentLogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$subAgent = $DB->selectRow("select * from agent where id=" . $id . " and parent_id=" . $agentInfo['id']);
if (!$subAgent) {
	exit('代理不存在或无权限编辑');
}

// 计算可设置的级别范围 - Level 5最高，Level 1最低
$maxLevel = $agentInfo['level'] - 1;
$minLevel = 1;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>编辑下级代理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
	<style>
		body {
			padding: 20px;
		}
	</style>
</head>

<body class="layui-form form">
	<input type="hidden" name="id" value="<?php echo $subAgent['id']; ?>">

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理账号
		</label>
		<div class="layui-input-block">
			<input type="text" value="<?php echo $subAgent['username']; ?>" disabled class="layui-input layui-disabled">
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
			<input type="text" name="name" required lay-verify="required" class="layui-input" value="<?php echo $subAgent['name']; ?>">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理级别
		</label>
		<div class="layui-input-block">
			<select name="level" lay-verify="required">
				<?php
					$levelNames = [1 => '普通代理', 2 => '高级代理', 3 => '金牌代理', 4 => '钻石代理', 5 => '至尊代理'];
					for ($i = $maxLevel; $i >= $minLevel; $i--):
				?>
					<option value="<?php echo $i; ?>" <?php echo $subAgent['level'] == $i ? 'selected' : ''; ?>>
						Level <?php echo $i; ?> - <?php echo $levelNames[$i]; ?>
					</option>
				<?php endfor; ?>
			</select>
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

			// 提交表单
			form.on("submit(submit)", function(data) {
				$.ajax({
					url: "ajax.php?act=editsubagent",
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
							parent.reload('sub_agent_list');
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

