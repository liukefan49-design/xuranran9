<?php
/**
 * 统一AJAX请求路由文件
 * 自动将代理相关的请求路由到正确的处理文件
 */

include("../includes/common.php");
if ($islogin == 1) {
} else exit(json_encode(["code" => "-1", "msg" => "未登录"], JSON_UNESCAPED_UNICODE));

// 获取act参数
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

// 代理相关的act列表（应该由ajax_agent.php处理）
$agent_acts = [
    'getagentlevel',
    'saveagentlevel',
    'updatelevelstate',
    'delagentlevel',
    'initdefaultlevels',
    'getkamiprice',
    'savekamiprice',
    'savekamipricebatch',
    'deletekamiprice',
    'getagentnotice',
    'saveagentnotice',
    'updatenoticestate',
    'deletenotice',
    'getagentregister',
    'auditagentregister',
    'getrechargelist',
    'getrechargestats',
    'quickrecharge'
];

// 判断是否为代理相关的请求
if (in_array($act, $agent_acts)) {
    // 路由到ajax_agent.php处理
    include_once 'ajax_agent.php';
} else {
    // 路由到ajax.php处理
    include_once 'ajax.php';
}
