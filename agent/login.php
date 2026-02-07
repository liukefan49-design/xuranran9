<?php
/*
 * 代理登录页面
 */
include("../includes/common.php");

// 登出处理
if (isset($_GET['logout'])) {
	@header('Content-Type: application/json; charset=UTF-8');

	// 清除数据库中的cookies
	if (isset($_COOKIE["agent_token"])) {
		$cookies = authcode(daddslashes($_COOKIE['agent_token']), 'DECODE', SYS_KEY);
		list($user, $sid) = explode("\t", $cookies);
		if ($user) {
			$DB->update('agent', ['cookies' => ''], 'username = "' . addslashes($user) . '"');
		}
	}

	// 生成新的session id防止会话固定攻击
	session_regenerate_id(true);

	// 清除cookie
	setcookie("agent_token", "", time() - 604800, '/');
	setcookie("tab", "", time() - 604800, '/');

	// 销毁session
	session_destroy();

	$json = ["code" => "0", "msg" => "您已成功注销本次登录！"];
	exit(json_encode($json, JSON_UNESCAPED_UNICODE));
}

// 如果已登录，跳转到后台
if (isset($_COOKIE["agent_token"])) {
	$cookies = authcode(daddslashes($_COOKIE['agent_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $cookies);
	if ($cookies && $DB->selectRowV2("select * from agent where username=? and cookies=?", [$user, $_COOKIE['agent_token']])) {
		header("Location: index.php");
		exit;
	}
}

// 处理登录请求
if (isset($_GET['act'])) {
	@header('Content-Type: application/json; charset=UTF-8');
	
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		exit(json_encode(["code" => "-1", "msg" => "非法请求"], JSON_UNESCAPED_UNICODE));
	}

	// 验证必要参数
	if (!isset($_POST['user'], $_POST['pass'])) {
		exit(json_encode(["code" => "-1", "msg" => "参数不完整"], JSON_UNESCAPED_UNICODE));
	}

	try {
		$user = trim(daddslashes($_POST['user']));
		$pass = trim(daddslashes($_POST['pass']));

		// 基本输入验证
		if (empty($user) || empty($pass)) {
			throw new Exception("用户名或密码不能为空！");
		}

		// 使用参数化方式构建查询
		$row = $DB->selectRowV2("SELECT * FROM agent WHERE username=? and state=1", [$user]);
		if ($row) {
			// 验证密码 - 支持新旧两种密码格式
			$passwordValid = false;
			if ($row['password']) {
				// 先尝试使用 password_verify 验证（新格式）
				if (password_verify($pass, $row['password'])) {
					$passwordValid = true;
				}
				// 如果失败，尝试明文比对（兼容旧数据）
				elseif ($pass === $row['password']) {
					$passwordValid = true;
					// 自动升级为加密密码
					$newHash = password_hash($pass, PASSWORD_DEFAULT);
					$DB->exec("UPDATE agent SET password='" . addslashes($newHash) . "' WHERE id=" . $row['id']);
				}
			}

			if ($passwordValid) {
				// 登录成功处理
				// 生成新的session id防止会话固定攻击
				session_regenerate_id(true);
				// 生成安全的session值 - 使用数据库中的密码哈希
				$session = md5($user . $row['password'] . SYS_KEY);
				$cookies = authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);

				// 设置安全的cookie
				setcookie("agent_token", $cookies, [
					'expires' => time() + 604800,
					'path' => '/',
					'httponly' => true,
					'samesite' => 'Strict'
				]);

				setCookie("tab", "primary.php", [
					'expires' => time() + 604800,
					'path' => '/',
					'httponly' => true
				]);

				// 更新cookies和最后登录时间
				$table = 'agent';
				$values = [
					'cookies' => $cookies,
					'last_login' => date('Y-m-d H:i:s')
				];
				$where = 'username = "' . $row['username'] . '"';
				$affectedRows = $DB->update($table, $values, $where);

				WriteLog("代理登录", "登录成功", $user, $DB);
				$json = ["code" => "1", "msg" => "登录成功,欢迎您使用本系统！"];
				exit(json_encode($json, JSON_UNESCAPED_UNICODE));
			}
		}

		// 登录失败处理
		throw new Exception("用户名或密码不正确或账号已被禁用！");
	} catch (Exception $e) {
		$json = ["code" => "-1", "msg" => $e->getMessage()];
		WriteLog("代理登录", "验证失败: " . $e->getMessage(), $user ?? null, $DB);
		exit(json_encode($json, JSON_UNESCAPED_UNICODE));
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>代理登录 - <?php echo $conf['sitename'] ?? '卡密管理系统'; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="../assets/layui/css/layui.css">
	<style>
		body {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.login-container {
			width: 400px;
			background: white;
			border-radius: 10px;
			padding: 40px;
			box-shadow: 0 15px 35px rgba(0,0,0,0.2);
		}
		.login-title {
			text-align: center;
			font-size: 24px;
			font-weight: bold;
			margin-bottom: 30px;
			color: #333;
		}
		.login-subtitle {
			text-align: center;
			color: #999;
			margin-bottom: 20px;
		}
		.code-img {
			cursor: pointer;
			height: 38px;
			border-radius: 2px;
		}
	</style>
</head>
<body>
	<div class="login-container">
		<div class="login-title">代理登录</div>
		<div class="login-subtitle">卡密管理系统 - 代理中心</div>
		
		<form class="layui-form" lay-filter="loginForm">
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
			
			<div class="layui-form-item">
				<label class="layui-form-label">账号</label>
				<div class="layui-input-block">
					<input type="text" name="user" required lay-verify="required" placeholder="请输入代理账号" autocomplete="off" class="layui-input">
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">密码</label>
				<div class="layui-input-block">
					<input type="password" name="pass" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
				</div>
			</div>

			<div class="layui-form-item">
				<div class="layui-input-block">
					<button class="layui-btn layui-btn-fluid layui-btn-normal" lay-submit lay-filter="login">登录</button>
				</div>
			</div>
		</form>
	</div>

	<script src="../assets/layui/layui.js"></script>
	<script>
		layui.use(['form', 'layer', 'jquery'], function() {
			var form = layui.form;
			var layer = layui.layer;
			var $ = layui.jquery;
			
			var isSubmitting = false;

			// 监听登录提交
			form.on('submit(login)', function(data) {
				if (isSubmitting) {
					return false;
				}
				isSubmitting = true;

				$.ajax({
					url: "login.php?act",
					type: "POST",
					dataType: "json",
					data: data.field,
					timeout: 10000,
					beforeSend: function() {
						layer.msg("正在登录", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(res) {
						if (res.code == "1") {
							layer.msg(res.msg, {
								icon: 1
							});
							setTimeout(function() {
								window.location.href = "./index.php";
							}, 500);
						} else {
							layer.msg(res.msg, {
								icon: 5
							});
						}
					},
					error: function(xhr, status, error) {
						layer.msg("登录失败，请稍后重试", {
							icon: 5
						});
					},
					complete: function() {
						isSubmitting = false;
					}
				});
				return false;
			});
		});
	</script>
</body>
</html>

