<?php
include '../includes/common.php';

header('Content-Type: application/json; charset=UTF-8');

$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

switch ($act) {
    case 'agentregister':
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $register_type = intval($_POST['register_type'] ?? 2);
        $level_id = intval($_POST['level_id'] ?? 1);
        $remark = trim($_POST['remark'] ?? '');
        $kami_code = trim($_POST['kami_code'] ?? '');

        // 验证必填字段
        if (empty($username) || empty($password) || empty($name) || empty($level_id)) {
            exit(json_encode(["code" => "-1", "msg" => "请填写完整信息"], JSON_UNESCAPED_UNICODE));
        }

        // 检查账号是否已存在
        $exists = $DB->selectRow("SELECT id FROM agent WHERE username = '{$username}'");
        if ($exists) {
            exit(json_encode(["code" => "-1", "msg" => "账号已存在"], JSON_UNESCAPED_UNICODE));
        }

        // 检查账号是否已注册
        $registered = $DB->selectRow("SELECT id FROM agent_register WHERE username = '{$username}' AND audit_status != 2");
        if ($registered) {
            exit(json_encode(["code" => "-1", "msg" => "该账号已有注册记录"], JSON_UNESCAPED_UNICODE));
        }

        // 检查等级是否存在
        $level = $DB->selectRow("SELECT * FROM agent_level WHERE id = {$level_id} AND state = 1");
        if (!$level) {
            exit(json_encode(["code" => "-1", "msg" => "选择的等级不存在或已禁用"], JSON_UNESCAPED_UNICODE));
        }

        // 卡密注册验证
        if ($register_type == 1 && !empty($kami_code)) {
            $kami = $DB->selectRow("SELECT * FROM kami WHERE kami = '{$kami_code}' AND state = 0");
            if (!$kami) {
                exit(json_encode(["code" => "-1", "msg" => "卡密不存在或已使用"], JSON_UNESCAPED_UNICODE));
            }
            
            // 如果卡密指定了等级，使用卡密的等级
            if ($kami['agent_level_id'] > 0) {
                $level_id = $kami['agent_level_id'];
                $level = $DB->selectRow("SELECT * FROM agent_level WHERE id = {$level_id} AND state = 1");
                if (!$level) {
                    exit(json_encode(["code" => "-1", "msg" => "卡密指定的等级不存在"], JSON_UNESCAPED_UNICODE));
                }
            }

            // 标记卡密为已使用
            $DB->update('kami', ['state' => 1, 'use_date' => date('Y-m-d H:i:s'), 'username' => $username], "id={$kami['id']}");
        }

        // 插入注册申请
        $data = [
            'username' => $username,
            'password' => $password,
            'name' => $name,
            'contact' => $contact,
            'register_type' => $register_type,
            'kami_code' => $kami_code,
            'level_id' => $level_id,
            'remark' => $remark,
            'register_time' => date('Y-m-d H:i:s'),
            'register_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'audit_status' => 0
        ];

        $result = $DB->insert('agent_register', $data);
        if ($result) {
            $msg = $register_type == 1 ? "卡密注册成功，等待审核" : "注册成功，等待管理员审核";
            exit(json_encode(["code" => "1", "msg" => $msg], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(["code" => "-1", "msg" => "注册失败，请重试"], JSON_UNESCAPED_UNICODE));
        }
        break;

    case 'getagentnotice':
        $agent_id = $_SESSION['agent_id'] ?? 0;
        if (!$agent_id) {
            exit(json_encode(["code" => "-1", "msg" => "未登录"], JSON_UNESCAPED_UNICODE));
        }

        // 获取代理等级
        $agent = $DB->selectRow("SELECT level_id FROM agent WHERE id = {$agent_id}");
        $level_id = $agent['level_id'] ?? 0;

        // 获取公告（该等级可见的 + 所有等级可见的）
        $sql = "SELECT * FROM agent_notice 
                WHERE state = 1 
                AND (target_level = 0 OR target_level = {$level_id})
                AND (expire_time IS NULL OR expire_time > NOW())
                ORDER BY is_sticky DESC, priority DESC, publish_time DESC 
                LIMIT 10";
        $result = $DB->select($sql);

        // 增加阅读次数
        foreach ($result as $notice) {
            $DB->update('agent_notice', ['read_count' => 'read_count + 1'], "id={$notice['id']}");
        }

        exit(json_encode(["code" => "1", "msg" => $result], JSON_UNESCAPED_UNICODE));
        break;

    case 'getlevelinfo':
        $agent_id = $_SESSION['agent_id'] ?? 0;
        if (!$agent_id) {
            exit(json_encode(["code" => "-1", "msg" => "未登录"], JSON_UNESCAPED_UNICODE));
        }

        $agent = $DB->selectRow("SELECT a.*, l.* FROM agent a LEFT JOIN agent_level l ON a.level_id = l.id WHERE a.id = {$agent_id}");
        if (!$agent) {
            exit(json_encode(["code" => "-1", "msg" => "代理不存在"], JSON_UNESCAPED_UNICODE));
        }

        // 获取今日已生成的免费卡密数量
        $today = date('Y-m-d');
        $daily_record = $DB->selectRow("SELECT * FROM agent_daily_kami WHERE agent_id = {$agent_id} AND record_date = '{$today}'");
        $today_free = $daily_record['free_kami_count'] ?? 0;

        exit(json_encode([
            "code" => "1", 
            "msg" => [
                "agent" => [
                    "id" => $agent['id'],
                    "username" => $agent['username'],
                    "name" => $agent['name'],
                    "unique_id" => $agent['unique_id'],
                    "balance" => $agent['balance'],
                    "level_id" => $agent['level_id'],
                    "level_name" => $agent['level_name']
                ],
                "level" => [
                    "kami_discount" => $agent['kami_discount'],
                    "daily_free_kami" => $agent['daily_free_kami'],
                    "max_sub_agents" => $agent['max_sub_agents'],
                    "can_generate_kami" => $agent['can_generate_kami'],
                    "can_manage_user" => $agent['can_manage_user'],
                    "can_add_agent" => $agent['can_add_agent'],
                    "can_set_discount" => $agent['can_set_discount'],
                    "can_set_free_kami" => $agent['can_set_free_kami']
                ],
                "today_free_used" => $today_free
            ]
        ], JSON_UNESCAPED_UNICODE));
        break;

    case 'getkamiprice':
        $agent_id = $_SESSION['agent_id'] ?? 0;
        if (!$agent_id) {
            exit(json_encode(["code" => "-1", "msg" => "未登录"], JSON_UNESCAPED_UNICODE));
        }

        // 获取代理等级折扣
        $agent = $DB->selectRow("SELECT level_id FROM agent WHERE id = {$agent_id}");
        $level = $DB->selectRow("SELECT kami_discount FROM agent_level WHERE id = {$agent['level_id']}");
        $discount = $level['kami_discount'] ?? 1.00;

        // 获取启用的卡密价格
        $prices = $DB->select("SELECT * FROM kami_price WHERE state = 1 ORDER BY sort_order");

        // 计算实际价格
        foreach ($prices as &$price) {
            $price['actual_price'] = $price['price'] * $discount;
        }

        exit(json_encode(["code" => "1", "msg" => $prices, "discount" => $discount], JSON_UNESCAPED_UNICODE));
        break;

    case 'generatesubagentkami':
        $agent_id = $_SESSION['agent_id'] ?? 0;
        if (!$agent_id) {
            exit(json_encode(["code" => "-1", "msg" => "未登录"], JSON_UNESCAPED_UNICODE));
        }

        $level_id = intval($_POST['level_id'] ?? 0);
        $num = intval($_POST['num'] ?? 1);
        $remark = trim($_POST['remark'] ?? '下级代理卡密');

        if ($num <= 0 || $num > 100) {
            exit(json_encode(["code" => "-1", "msg" => "生成数量必须在1-100之间"], JSON_UNESCAPED_UNICODE));
        }

        // 检查等级是否存在
        $level = $DB->selectRow("SELECT * FROM agent_level WHERE id = {$level_id} AND state = 1");
        if (!$level) {
            exit(json_encode(["code" => "-1", "msg" => "等级不存在"], JSON_UNESCAPED_UNICODE));
        }

        // 检查是否可以设置该等级
        $agent = $DB->selectRow("SELECT * FROM agent WHERE id = {$agent_id}");
        if ($level_id >= $agent['level_id']) {
            exit(json_encode(["code" => "-1", "msg" => "不能生成同级或更高级别的卡密"], JSON_UNESCAPED_UNICODE));
        }

        // 计算价格（基础价格 * 代理折扣 * 等级折扣）
        $base_kami = $DB->selectRow("SELECT * FROM kami_price WHERE kami_type = 'forever' AND state = 1");
        if (!$base_kami) {
            exit(json_encode(["code" => "-1", "msg" => "未设置永久卡价格"], JSON_UNESCAPED_UNICODE));
        }

        $agent_level = $DB->selectRow("SELECT kami_discount FROM agent_level WHERE id = {$agent['level_id']}");
        $agent_discount = $agent_level['kami_discount'] ?? 1.00;
        $level_discount = $level['kami_discount'] ?? 1.00;

        $total_price = $base_kami['price'] * $agent_discount * $level_discount * $num;
        $current_balance = floatval($agent['balance']);

        if ($current_balance < $total_price) {
            exit(json_encode(["code" => "-1", "msg" => "余额不足，需要 ¥" . number_format($total_price, 2)], JSON_UNESCAPED_UNICODE));
        }

        // 扣除余额并生成卡密
        $kami_list = [];
        $success = 0;

        foreach (range(1, $num) as $i) {
            $kami_code = random(16);
            $data = [
                'kami' => $kami_code,
                'times' => '+100 year',
                'host' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                'sc_user' => $agent['username'],
                'state' => 0,
                'app' => 'agent_register',
                'comment' => $remark,
                'agent_id' => $agent_id,
                'agent_level_id' => $level_id
            ];

            $result = $DB->insert('kami', $data);
            if ($result) {
                $success++;
                $kami_list[] = $kami_code;
            }
        }

        if ($success > 0) {
            // 更新余额
            $new_balance = $current_balance - ($base_kami['price'] * $agent_discount * $level_discount * $success);
            $DB->update('agent', ['balance' => $new_balance], "id={$agent_id}");

            exit(json_encode([
                "code" => "1", 
                "msg" => "成功生成 {$success} 张卡密，消耗 ¥" . number_format($current_balance - $new_balance, 2),
                "kami_list" => $kami_list,
                "new_balance" => $new_balance
            ], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(["code" => "-1", "msg" => "生成失败"], JSON_UNESCAPED_UNICODE));
        }
        break;

    case 'addsubagent':
        $agent_id = $_SESSION['agent_id'] ?? 0;
        if (!$agent_id) {
            exit(json_encode(["code" => "-1", "msg" => "未登录"], JSON_UNESCAPED_UNICODE));
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $level_id = intval($_POST['level_id'] ?? 0);
        $contact = trim($_POST['contact'] ?? '');

        // 验证权限
        $agent = $DB->selectRow("SELECT a.*, l.* FROM agent a LEFT JOIN agent_level l ON a.level_id = l.id WHERE a.id = {$agent_id}");
        if (!$agent['can_add_agent'] || $agent['max_sub_agents'] == 0) {
            exit(json_encode(["code" => "-1", "msg" => "您没有添加下级代理的权限"], JSON_UNESCAPED_UNICODE));
        }

        // 检查下级数量限制
        $sub_count = $DB->selectRow("SELECT COUNT(*) as count FROM agent WHERE parent_id = {$agent_id}");
        if ($sub_count['count'] >= $agent['max_sub_agents']) {
            exit(json_encode(["code" => "-1", "msg" => "下级代理数量已达上限"], JSON_UNESCAPED_UNICODE));
        }

        // 检查账号是否存在
        $exists = $DB->selectRow("SELECT id FROM agent WHERE username = '{$username}'");
        if ($exists) {
            exit(json_encode(["code" => "-1", "msg" => "账号已存在"], JSON_UNESCAPED_UNICODE));
        }

        // 检查等级
        $level = $DB->selectRow("SELECT * FROM agent_level WHERE id = {$level_id} AND state = 1");
        if (!$level || $level_id >= $agent['level_id']) {
            exit(json_encode(["code" => "-1", "msg" => "不能添加同级或更高级别的代理"], JSON_UNESCAPED_UNICODE));
        }

        // 检查是否有设置折扣权限
        if (!$agent['can_set_discount']) {
            $level_discount = $level['kami_discount'];
        } else {
            $level_discount = floatval($_POST['kami_discount'] ?? $level['kami_discount']);
        }

        // 检查是否有设置免费卡密权限
        if (!$agent['can_set_free_kami']) {
            $level_free = $level['daily_free_kami'];
        } else {
            $level_free = intval($_POST['daily_free_kami'] ?? $level['daily_free_kami']);
        }

        // 生成专属ID
        $unique_id = 'A' . str_pad($agent_id, 4, '0', STR_PAD_LEFT) . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // 创建下级代理
        $data = [
            'username' => $username,
            'password' => $password,
            'name' => $name,
            'contact' => $contact,
            'balance' => $level['initial_balance'],
            'parent_id' => $agent_id,
            'level_id' => $level_id,
            'level' => $level['level_order'],
            'unique_id' => $unique_id,
            'register_type' => 2,
            'state' => 1
        ];

        // 更新等级配置
        $DB->update('agent_level', [
            'kami_discount' => $level_discount,
            'daily_free_kami' => $level_free
        ], "id={$level_id}");

        $result = $DB->insert('agent', $data);
        if ($result) {
            exit(json_encode(["code" => "1", "msg" => "下级代理添加成功"], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(["code" => "-1", "msg" => "添加失败"], JSON_UNESCAPED_UNICODE));
        }
        break;

    default:
        exit(json_encode(["code" => "-1", "msg" => "未知操作"], JSON_UNESCAPED_UNICODE));
}
