<?php
/*
 * @Author: yihua
 * @Date: 2022-06-25 21:02:04
 * @LastEditTime: 2025-01-05 14:06:36
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\includes\member.php
 * 一花一叶 一行代码
 * Copyright (c) 2022 by yihua 487735913@qq.com, All Rights Reserved. 
 */

if (!defined('IN_CRONLITE')) exit();
if (isset($_COOKIE["sub_admin_token"])) {
	$cookies = authcode(daddslashes($_COOKIE['sub_admin_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $cookies);

	// 首先尝试从 sub_admin 表验证
	if ($cookies && $DB->selectRowV2("select * from sub_admin where username=? and cookies=?", [$user, $_COOKIE['sub_admin_token']])) {
		if ($users = $DB->selectRowV2("select * from sub_admin where username=?", [$user])) {
			$session = md5($users['username'] . $users['password'] . $password_hash);
			if (hash_equals($session, $sid)) {  // 使用安全的字符串比较
                $islogin = 1;
                // 建议添加登录时间验证
                session_regenerate_id(true);  // 更新session_id防止会话固定攻击
            }
		}
	}
	// 如果 sub_admin 表验证失败，尝试从 admin 表验证
	else if ($cookies) {
		$adminUser = $DB->selectRowV2("select * from admin where username=?", [$user]);
		if ($adminUser && $adminUser['state'] == 1) {
			// 管理员登录成功
			// 标记这是管理员登录
			$_SESSION['is_admin_user'] = true;
			$_SESSION['admin_username'] = $user;
			$islogin = 1;
			session_regenerate_id(true);
		}
	}
}
