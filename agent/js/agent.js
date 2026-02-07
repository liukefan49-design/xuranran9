/**
 * 代理端专用JS
 */
layui.use(['element', 'layer', 'jquery'], function() {
	var element = layui.element;
	var layer = layui.layer;
	var $ = layui.jquery;

	// 退出登录
	$("#quit").click(function () {
		layer.confirm("确定退出当前登录账号吗？", {
			btn: ["确定", "取消"],
			icon: 3
		}, function () {
			layer.msg("正在退出账号中", {
				icon: 16,
				time: 1000
			}, function () {
				// 清除tab cookie
				setCookie("tab", "", -1);
				
				// 调用退出接口
				$.get("./login.php?logout", function (e) {
					layer.msg("注销登录成功", {
						icon: 1
					});
				});
				
				// 跳转到登录页
				setTimeout(function() {
					window.location.href = "login.php";
				}, 1000);
			});
		});
	});

	// Cookie操作函数
	function setCookie(name, value, days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "") + expires + "; path=/";
	}
});

