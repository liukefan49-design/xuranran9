<?php
require_once("../includes/Task.php");
require_once("../includes/Scheduler.php");
include("../includes/common.php");
if ($islogin == 1) {
} else exit(json_encode(["code" => "-1", "msg" => "未登录"], JSON_UNESCAPED_UNICODE));

$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;
@header('Content-Type: application/json; charset=UTF-8');

switch ($act) {
    // 代理等级相关
    case 'getagentlevel':
        try {
            $sql = "SELECT l.*, COUNT(a.id) as agent_count FROM agent_level l 
                    LEFT JOIN agent a ON l.id = a.level_id 
                    GROUP BY l.id ORDER BY l.level_order DESC";
            $result = $DB->select($sql);
            
            // 检查数据是否正确获取
            if ($result === false) {
                exit(json_encode(["code" => "0", "msg" => "数据库查询失败"], JSON_UNESCAPED_UNICODE));
            }
            
            // 确保返回的是数组
            if (!is_array($result)) {
                $result = [];
            }
            
            // 处理每个等级的数据,确保所有字段都是正确的类型
            foreach ($result as &$level) {
                $level['id'] = intval($level['id']);
                $level['level_order'] = intval($level['level_order']);
                $level['kami_discount'] = floatval($level['kami_discount']);
                $level['initial_balance'] = floatval($level['initial_balance']);
                $level['daily_free_kami'] = intval($level['daily_free_kami']);
                $level['max_sub_agents'] = intval($level['max_sub_agents']);
                $level['can_generate_kami'] = intval($level['can_generate_kami']);
                $level['can_manage_user'] = intval($level['can_manage_user']);
                $level['can_add_agent'] = intval($level['can_add_agent']);
                $level['can_set_discount'] = intval($level['can_set_discount']);
                $level['can_set_free_kami'] = intval($level['can_set_free_kami']);
                $level['agent_count'] = intval($level['agent_count']);
            }
            
            // 返回符合Layui表格标准格式的数据
            exit(json_encode([
                "code" => "0",
                "msg" => "",
                "count" => count($result),
                "data" => $result
            ], JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode([
                "code" => "1",
                "msg" => "系统错误：" . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
        }
        break;

    case 'initdefaultlevels':
        try {
            // 检查是否已有等级数据
            $existing_levels = $DB->select("SELECT COUNT(*) as count FROM agent_level");
            if (isset($existing_levels[0]) && $existing_levels[0]['count'] > 0) {
                // 有数据,需要确认是否覆盖
                $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] == '1';
                if (!$overwrite) {
                    exit(json_encode([
                        "code" => "0",
                        "msg" => "数据库中已存在等级数据,请确认是否覆盖",
                        "need_confirm" => true
                    ], JSON_UNESCAPED_UNICODE));
                } else {
                    // 清空现有数据
                    $DB->query("TRUNCATE TABLE agent_level");
                }
            }
            
            // 默认等级数据
            $default_levels = [
                [
                    'level_name' => '钻石代理',
                    'level_order' => 1,
                    'can_manage_user' => 1,
                    'can_add_agent' => 1,
                    'max_sub_agents' => 50,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 10,
                    'kami_discount' => 0.70,
                    'initial_balance' => 500.00,
                    'can_set_discount' => 1,
                    'can_set_free_kami' => 1,
                    'state' => 1
                ],
                [
                    'level_name' => '黄金代理',
                    'level_order' => 2,
                    'can_manage_user' => 1,
                    'can_add_agent' => 1,
                    'max_sub_agents' => 20,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 5,
                    'kami_discount' => 0.85,
                    'initial_balance' => 200.00,
                    'can_set_discount' => 1,
                    'can_set_free_kami' => 1,
                    'state' => 1
                ],
                [
                    'level_name' => '白银代理',
                    'level_order' => 3,
                    'can_manage_user' => 1,
                    'can_add_agent' => 1,
                    'max_sub_agents' => 10,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 3,
                    'kami_discount' => 0.90,
                    'initial_balance' => 100.00,
                    'can_set_discount' => 0,
                    'can_set_free_kami' => 0,
                    'state' => 1
                ],
                [
                    'level_name' => '青铜代理',
                    'level_order' => 4,
                    'can_manage_user' => 0,
                    'can_add_agent' => 0,
                    'max_sub_agents' => 5,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 1,
                    'kami_discount' => 0.95,
                    'initial_balance' => 50.00,
                    'can_set_discount' => 0,
                    'can_set_free_kami' => 0,
                    'state' => 1
                ],
                [
                    'level_name' => '普通代理',
                    'level_order' => 5,
                    'can_manage_user' => 0,
                    'can_add_agent' => 0,
                    'max_sub_agents' => 0,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 0,
                    'kami_discount' => 1.00,
                    'initial_balance' => 0.00,
                    'can_set_discount' => 0,
                    'can_set_free_kami' => 0,
                    'state' => 1
                ]
            ];
            
            // 插入默认数据
            $success_count = 0;
            foreach ($default_levels as $level) {
                $result = $DB->insert('agent_level', $level);
                if ($result) {
                    $success_count++;
                }
            }
            
            if ($success_count > 0) {
                exit(json_encode([
                    "code" => "1",
                    "msg" => "成功初始化 {$success_count} 个默认等级"
                ], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode([
                    "code" => "0",
                    "msg" => "初始化失败,请检查数据库连接"
                ], JSON_UNESCAPED_UNICODE));
            }
        } catch (Exception $e) {
            exit(json_encode([
                "code" => "0",
                "msg" => "系统错误: " . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
        }
        break;
                    'level_name' => '白银代理',
                    'level_order' => 3,
                    'can_manage_user' => 1,
                    'can_add_agent' => 1,
                    'max_sub_agents' => 10,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 3,
                    'kami_discount' => 0.90,
                    'initial_balance' => 100.00,
                    'can_set_discount' => 0,
                    'can_set_free_kami' => 0,
                    'state' => 1
                ],
                [
                    'level_name' => '青铜代理',
                    'level_order' => 4,
                    'can_manage_user' => 0,
                    'can_add_agent' => 0,
                    'max_sub_agents' => 5,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 1,
                    'kami_discount' => 0.95,
                    'initial_balance' => 50.00,
                    'can_set_discount' => 0,
                    'can_set_free_kami' => 0,
                    'state' => 1
                ],
                [
                    'level_name' => '普通代理',
                    'level_order' => 5,
                    'can_manage_user' => 0,
                    'can_add_agent' => 0,
                    'max_sub_agents' => 0,
                    'can_generate_kami' => 1,
                    'daily_free_kami' => 0,
                    'kami_discount' => 1.00,
                    'initial_balance' => 0.00,
                    'can_set_discount' => 0,
                    'can_set_free_kami' => 0,
                    'state' => 1
                ]
            ];
            
            // 插入默认数据
            $success_count = 0;
            foreach ($default_levels as $level) {
                $result = $DB->insert('agent_level', $level);
                if ($result) {
                    $success_count++;
                }
            }
            
            if ($success_count > 0) {
                exit(json_encode([
                    "code" => "1",
                    "msg" => "成功初始化 " . $success_count . " 个代理等级",
                    "count" => $success_count
                ], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode([
                    "code" => "0",
                    "msg" => "初始化失败，请检查数据库连接"
                ], JSON_UNESCAPED_UNICODE));
            }
        } catch (Exception $e) {
            exit(json_encode([
                "code" => "-1",
                "msg" => "系统错误：" . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
        }
        break;

    case 'saveagentlevel':
        try {
            $id = intval($_POST['id'] ?? 0);
            
            // 验证必要参数
            $level_name = trim($_POST['level_name'] ?? '');
            if (empty($level_name)) {
                exit(json_encode(["code" => "0", "msg" => "等级名称不能为空"], JSON_UNESCAPED_UNICODE));
            }
            
            $data = [
                'level_name' => $level_name,
                'level_order' => intval($_POST['level_order'] ?? 1),
                'kami_discount' => floatval($_POST['kami_discount'] ?? 1.00),
                'initial_balance' => floatval($_POST['initial_balance'] ?? 0),
                'daily_free_kami' => intval($_POST['daily_free_kami'] ?? 0),
                'max_sub_agents' => intval($_POST['max_sub_agents'] ?? 0),
                'can_generate_kami' => intval($_POST['can_generate_kami'] ?? 1),
                'can_manage_user' => intval($_POST['can_manage_user'] ?? 1),
                'can_add_agent' => intval($_POST['can_add_agent'] ?? 1),
                'can_set_discount' => intval($_POST['can_set_discount'] ?? 0),
                'can_set_free_kami' => intval($_POST['can_set_free_kami'] ?? 0)
            ];

            // 验证折扣范围
            if ($data['kami_discount'] < 0.1 || $data['kami_discount'] > 1) {
                exit(json_encode(["code" => "0", "msg" => "折扣必须在0.1-1.0之间"], JSON_UNESCAPED_UNICODE));
            }

            if ($id > 0) {
                // 更新现有等级
                $result = $DB->update('agent_level', $data, "id={$id}");
                $msg = $result ? "更新成功" : "更新失败";
            } else {
                // 添加新等级
                $result = $DB->insert('agent_level', $data);
                $msg = $result ? "添加成功" : "添加失败";
            }
            exit(json_encode(["code" => $result ? "1" : "0", "msg" => $msg], JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(["code" => "-1", "msg" => "系统错误：" . $e->getMessage()], JSON_UNESCAPED_UNICODE));
        }
        break;

    case 'updatelevelstate':
        $id = intval($_POST['id'] ?? 0);
        $state = intval($_POST['state'] ?? 0);
        $result = $DB->update('agent_level', ['state' => $state], "id={$id}");
        $msg = $result ? "状态更新成功" : "状态更新失败";
        exit(json_encode(["code" => $result ? "1" : "0", "msg" => $msg], JSON_UNESCAPED_UNICODE));
        break;

    case 'delagentlevel':
        $id = intval($_POST['id'] ?? 0);
        // 检查是否有代理使用该等级
        $agent_count = $DB->selectRow("SELECT COUNT(*) as count FROM agent WHERE level_id = {$id}");
        if ($agent_count['count'] > 0) {
            exit(json_encode(["code" => "0", "msg" => "该等级下还有代理，无法删除"], JSON_UNESCAPED_UNICODE));
        }
        $result = $DB->delete("agent_level", "id={$id}");
        $msg = $result ? "删除成功" : "删除失败";
        exit(json_encode(["code" => $result ? "1" : "0", "msg" => $msg], JSON_UNESCAPED_UNICODE));
        break;

    // 卡密价格相关
    case 'getkamiprice':
        try {
            $result = $DB->select("SELECT * FROM kami_price ORDER BY sort_order ASC");
            exit(json_encode(["code" => "1", "data" => $result, "msg" => "获取成功"], JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(["code" => "0", "data" => [], "msg" => "获取失败: " . $e->getMessage()], JSON_UNESCAPED_UNICODE));
        }
        break;

    case 'savekamiprice':
        $id = intval($_POST['edit_id'] ?? 0);
        $data = [
            'type_name' => $_POST['type_name'] ?? '',
            'days' => intval($_POST['days'] ?? 0),
            'price' => floatval($_POST['price'] ?? 0),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'description' => $_POST['description'] ?? ''
        ];
        
        if ($id > 0) {
            $result = $DB->update('kami_price', $data, "id={$id}");
        } else {
            $result = false;
        }
        exit(json_encode(["code" => $result ? "1" : "0", "msg" => $result ? "保存成功" : "保存失败"], JSON_UNESCAPED_UNICODE));
        break;

    case 'savekamipricebatch':
        $batch_data = json_decode($_POST['data'] ?? '[]', true);
        $success = 0;
        foreach ($batch_data as $item) {
            $data = [
                'type_name' => $item['type_name'] ?? '',
                'days' => intval($item['days'] ?? 0),
                'price' => floatval($item['price'] ?? 0),
                'sort_order' => intval($item['sort_order'] ?? 0),
                'description' => $item['description'] ?? ''
            ];
            $result = $DB->update('kami_price', $data, "id=" . intval($item['id']));
            if ($result) $success++;
        }
        exit(json_encode(["code" => "1", "msg" => "成功更新 {$success} 条数据"], JSON_UNESCAPED_UNICODE));
        break;

    case 'updatepricestate':
        $id = intval($_POST['id'] ?? 0);
        $state = intval($_POST['state'] ?? 0);
        $result = $DB->update('kami_price', ['state' => $state], "id={$id}");
        exit(json_encode(["code" => $result ? "1" : "0", "msg" => $result ? "状态更新成功" : "状态更新失败"], JSON_UNESCAPED_UNICODE));
        break;

    // 系统公告相关
    case 'getagentnotice':
        $sql = "SELECT * FROM agent_notice ORDER BY is_sticky DESC, priority DESC, publish_time DESC";
        $count = $DB->selectRow("SELECT COUNT(*) as num FROM agent_notice");
        $data = $DB->selectPage($sql, $DB->pageNo = $_REQUEST['page'], $DB->pageRows = $_REQUEST['limit']);
        exit(json_encode(["code" => "0", "count" => $count['num'], "data" => $data], JSON_UNESCAPED_UNICODE));
        break;

    case 'saveagentnotice':
        $act_type = $_POST['act'] ?? 'add';
        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'notice_type' => intval($_POST['notice_type'] ?? 1),
            'priority' => intval($_POST['priority'] ?? 0),
            'target_level' => intval($_POST['target_level'] ?? 0),
            'is_sticky' => intval($_POST['is_sticky'] ?? 0),
            'expire_time' => $_POST['expire_time'] ?? null,
            'created_by' => $subconf['username'],
            'state' => intval($_POST['state'] ?? 1)
        ];

        if ($act_type == 'add') {
            $result = $DB->insert('agent_notice', $data);
        } else {
            $id = intval($_POST['id'] ?? 0);
            $result = $DB->update('agent_notice', $data, "id={$id}");
        }
        exit(json_encode(["code" => $result ? "1" : "0", "msg" => $result ? "保存成功" : "保存失败"], JSON_UNESCAPED_UNICODE));
        break;

    case 'updatenoticestate':
        $id = intval($_POST['id'] ?? 0);
        $state = intval($_POST['state'] ?? 0);
        $result = $DB->update('agent_notice', ['state' => $state], "id={$id}");
        exit(json_encode(["code" => $result ? "1" : "0", "msg" => $result ? "状态更新成功" : "状态更新失败"], JSON_UNESCAPED_UNICODE));
        break;

    case 'delagentnotice':
        $id = intval($_POST['id'] ?? 0);
        $result = $DB->delete("agent_notice", "id={$id}");
        exit(json_encode(["code" => $result ? "1" : "0", "msg" => $result ? "删除成功" : "删除失败"], JSON_UNESCAPED_UNICODE));
        break;

    // 余额充值相关
    case 'getrechargelist':
        $sql = "SELECT r.*, a.username as agent_name FROM balance_recharge r 
                LEFT JOIN agent a ON r.agent_id = a.id 
                ORDER BY r.recharge_time DESC";
        $count = $DB->selectRow("SELECT COUNT(*) as num FROM balance_recharge");
        $data = $DB->selectPage($sql, $DB->pageNo = $_REQUEST['page'], $DB->pageRows = $_REQUEST['limit']);
        exit(json_encode(["code" => "0", "count" => $count['num'], "data" => $data], JSON_UNESCAPED_UNICODE));
        break;

    case 'getrechargestats':
        $today = date('Y-m-d');
        $month = date('Y-m');
        
        $today_total = $DB->selectRow("SELECT SUM(recharge_amount) as total FROM balance_recharge WHERE DATE(recharge_time) = '{$today}'");
        $month_total = $DB->selectRow("SELECT SUM(recharge_amount) as total FROM balance_recharge WHERE DATE_FORMAT(recharge_time, '%Y-%m') = '{$month}'");
        $count = $DB->selectRow("SELECT COUNT(*) as count FROM balance_recharge");
        
        exit(json_encode([
            "code" => "1", 
            "data" => [
                "today_total" => $today_total['total'] ?? 0,
                "month_total" => $month_total['total'] ?? 0,
                "count" => $count['count'] ?? 0
            ]
        ], JSON_UNESCAPED_UNICODE));
        break;

    case 'quickrecharge':
        $agent_id = intval($_POST['agent_id'] ?? 0);
        $unique_id = trim($_POST['unique_id'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $remark = trim($_POST['remark'] ?? '');

        if ($amount <= 0) {
            exit(json_encode(["code" => "-1", "msg" => "充值金额必须大于0"], JSON_UNESCAPED_UNICODE));
        }

        // 通过ID或专属ID查找代理
        $agent = null;
        if ($agent_id > 0) {
            $agent = $DB->selectRow("SELECT * FROM agent WHERE id = {$agent_id}");
        } elseif (!empty($unique_id)) {
            $agent = $DB->selectRow("SELECT * FROM agent WHERE unique_id = '{$unique_id}'");
        }

        if (!$agent) {
            exit(json_encode(["code" => "-1", "msg" => "代理不存在"], JSON_UNESCAPED_UNICODE));
        }

        $old_balance = floatval($agent['balance']);
        $new_balance = $old_balance + $amount;

        // 更新余额
        $result = $DB->update('agent', ['balance' => $new_balance], "id={$agent['id']}");

        if ($result) {
            // 记录充值日志
            $DB->insert('balance_recharge', [
                'agent_id' => $agent['id'],
                'agent_name' => $agent['username'],
                'recharge_amount' => $amount,
                'balance_before' => $old_balance,
                'balance_after' => $new_balance,
                'recharge_type' => 1,
                'remark' => $remark,
                'created_by' => $subconf['username']
            ]);
            exit(json_encode(["code" => "1", "msg" => "充值成功，新余额：¥{$new_balance}"], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(["code" => "-1", "msg" => "充值失败"], JSON_UNESCAPED_UNICODE));
        }
        break;

    // 代理注册审核
    case 'getagentregister':
        $sql = "SELECT r.*, l.level_name FROM agent_register r 
                LEFT JOIN agent_level l ON r.level_id = l.id 
                WHERE r.audit_status = 0 
                ORDER BY r.register_time DESC";
        $result = $DB->select($sql);
        exit(json_encode(["code" => "1", "msg" => $result], JSON_UNESCAPED_UNICODE));
        break;

    case 'auditagentregister':
        $id = intval($_POST['id'] ?? 0);
        $status = intval($_POST['status'] ?? 0); // 1=通过，2=拒绝
        $reason = trim($_POST['reason'] ?? '');

        $register = $DB->selectRow("SELECT * FROM agent_register WHERE id = {$id}");
        if (!$register) {
            exit(json_encode(["code" => "-1", "msg" => "注册记录不存在"], JSON_UNESCAPED_UNICODE));
        }

        if ($status == 1) {
            // 审核通过，创建代理账号
            $level_data = $DB->selectRow("SELECT * FROM agent_level WHERE id = {$register['level_id']}");
            
            // 生成专属ID
            $unique_id = 'A' . str_pad($register['id'], 6, '0', STR_PAD_LEFT);
            
            $agent_data = [
                'username' => $register['username'],
                'password' => $register['password'],
                'name' => $register['name'],
                'contact' => $register['contact'],
                'balance' => $level_data['initial_balance'],
                'parent_id' => $register['parent_id'],
                'level_id' => $register['level_id'],
                'level' => $level_data['level_order'],
                'unique_id' => $unique_id,
                'register_type' => $register['register_type'],
                'state' => 1
            ];

            $result = $DB->insert('agent', $agent_data);
            if ($result) {
                $DB->update('agent_register', [
                    'audit_status' => 1,
                    'audit_time' => date('Y-m-d H:i:s'),
                    'audit_by' => $subconf['username']
                ], "id={$id}");
                exit(json_encode(["code" => "1", "msg" => "审核通过，代理账号已创建"], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(["code" => "-1", "msg" => "创建代理账号失败"], JSON_UNESCAPED_UNICODE));
            }
        } else {
            // 审核拒绝
            $result = $DB->update('agent_register', [
                'audit_status' => 2,
                'audit_time' => date('Y-m-d H:i:s'),
                'audit_by' => $subconf['username'],
                'audit_reason' => $reason
            ], "id={$id}");
            exit(json_encode(["code" => "1", "msg" => "已拒绝该注册申请"], JSON_UNESCAPED_UNICODE));
        }
        break;

    default:
        exit(json_encode(["code" => "-1", "msg" => "未知操作"], JSON_UNESCAPED_UNICODE));
}
