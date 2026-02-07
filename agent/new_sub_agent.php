<?php
include '../includes/common.php';
include '../includes/agent_auth.php';
if ($isAgentLogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 计算可添加的级别范围 - Level 5最高，Level 1最低
$maxLevel = $agentInfo['level'] - 1;
$minLevel = 1;

if ($maxLevel < $minLevel) {
	exit('您的级别已是最低级别（Level 1），无法添加下级代理');
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>添加下级代理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css" />
	<style>
		body {
			padding: 20px;
		}
		.tips {
			background: #f8f8f8;
			padding: 15px;
			border-radius: 5px;
			margin-bottom: 20px;
			color: #666;
			font-size: 13px;
		}
		.tips i {
			color: #1E9FFF;
		}
	</style>
</head>

<body class="layui-form form">
	<div class="tips">
		<i class="layui-icon layui-icon-tips"></i>
		<strong>提示：</strong>
		您当前级别为 Level <?php echo $agentInfo['level']; ?>，
		可以添加 Level <?php echo $minLevel; ?> 到 Level <?php echo $maxLevel; ?> 的下级代理。
		下级代理可以继续发展更低级别的代理。（Level 5 最高，Level 1 最低）
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理账号
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="username" required lay-verify="required" class="layui-input" placeholder="请输入代理账号（字母、数字）">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理密码
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="password" name="password" required lay-verify="required" class="layui-input" placeholder="请输入密码">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理名称
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="name" required lay-verify="required" class="layui-input" placeholder="请输入代理名称">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			初始余额
		</label>
		<div class="layui-input-block">
			<input type="number" name="balance" class="layui-input" placeholder="请输入初始余额，默认为0" value="0" step="0.01" min="0">
			<div style="color: #999; font-size: 12px; margin-top: 5px;">
				充值将从您的账户余额中扣除
			</div>
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label">
			代理级别
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<select name="level" lay-verify="required">
				<?php
					$levelNames = [1 => '普通代理', 2 => '高级代理', 3 => '金牌代理', 4 => '钻石代理', 5 => '至尊代理'];
					for ($i = $maxLevel; $i >= $minLevel; $i--):
				?>
					<option value="<?php echo $i; ?>" <?php echo $i == $maxLevel ? 'selected' : ''; ?>>
						Level <?php echo $i; ?> - <?php echo $levelNames[$i]; ?>
					</option>
				<?php endfor; ?>
			</select>
		</div>
	</div>

	<div class="layui-form-item">
		<div class="layui-input-block">
			<button class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">立即添加</button>
			<button type="reset" class="layui-btn layui-btn-primary">重置</button>
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
				var balance = parseFloat(data.field.balance) || 0;
				var myBalance = <?php echo $agentInfo['balance']; ?>;
				
				if (balance > myBalance) {
					layer.msg('您的余额不足！当前余额：¥' + myBalance.toFixed(2), {icon: 5});
					return false;
				}

				$.ajax({
					url: "ajax.php?act=newsubagent",
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

