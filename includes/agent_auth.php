<?php
/*
 * 代理认证文件
 */

if (!defined('IN_CRONLITE')) exit();

$isAgentLogin = 0;
$agentInfo = null;

if (isset($_COOKIE["agent_token"])) {
	$cookies = authcode(daddslashes($_COOKIE['agent_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $cookies);
	if ($cookies && $DB->selectRowV2("select * from agent where username=? and cookies=?", [$user, $_COOKIE['agent_token']])) {
		if ($agent = $DB->selectRowV2("select * from agent where username=? and state=1", [$user])) {
			$session = md5($agent['username'] . $agent['password'] . SYS_KEY);
			if (hash_equals($session, $sid)) {
				$isAgentLogin = 1;
				$agentInfo = $agent;
				session_regenerate_id(true);
			}
		}
	}
}

