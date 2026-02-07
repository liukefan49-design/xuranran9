<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 获取等级列表
$level_list = $DB->select("SELECT * FROM agent_level ORDER BY level_order ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>代理等级列表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        body { padding: 20px; background: #f2f2f2; }
        .level-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .level-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .level-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }
        .level-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .level-order {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
        .level-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-value {
            font-size: 22px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 12px;
            color: #999;
        }
        .permission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }
        .permission-tag {
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
        }
        .permission-tag.active {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        .permission-tag.inactive {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        .level-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            margin-left: 10px;
        }
        .badge-1 { background: #ff5722; }
        .badge-2 { background: #ff9800; }
        .badge-3 { background: #009688; }
        .badge-4 { background: #2196f3; }
        .badge-5 { background: #9c27b0; }
        .state-enable { color: #009688; }
        .state-disable { color: #ff5722; }
        .empty-tip {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-tip i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="layui-container">
        <div class="layui-card">
            <div class="layui-card-header">
                <span style="font-size: 18px; font-weight: bold;">代理等级列表</span>
                <button class="layui-btn layui-btn-sm layui-btn-warm" style="float: right;" onclick="initLevels()">
                    <i class="layui-icon layui-icon-refresh"></i>初始化默认等级
                </button>
                <button class="layui-btn layui-btn-sm layui-btn-normal" style="float: right; margin-right: 10px;" onclick="window.location.href='agent_level.php'">
                    <i class="layui-icon layui-icon-list"></i>表格视图
                </button>
            </div>
            <div class="layui-card-body">
                <?php if (count($level_list) > 0): ?>
                    <?php foreach ($level_list as $level): ?>
                        <div class="level-card">
                            <div class="level-header">
                                <div>
                                    <span class="level-title">
                                        <?php echo htmlspecialchars($level['level_name']); ?>
                                        <span class="level-badge badge-<?php echo $level['level_order']; ?>">
                                            等级<?php echo $level['level_order']; ?>
                                        </span>
                                    </span>
                                    <span class="layui-badge layui-bg-gray" style="margin-left: 15px;">
                                        ID: <?php echo $level['id']; ?>
                                    </span>
                                </div>
                                <div>
                                    <span style="margin-right: 15px;">
                                        状态: 
                                        <strong class="state-<?php echo $level['state'] == 1 ? 'enable' : 'disable'; ?>">
                                            <?php echo $level['state'] == 1 ? '启用' : '禁用'; ?>
                                        </strong>
                                    </span>
                                    <button class="layui-btn layui-btn-xs" onclick="editLevel(<?php echo $level['id']; ?>)">
                                        <i class="layui-icon layui-icon-edit"></i>编辑
                                    </button>
                                    <button class="layui-btn layui-btn-xs layui-btn-warm" onclick="configPermission(<?php echo $level['id']; ?>)">
                                        <i class="layui-icon layui-icon-set"></i>配置权限
                                    </button>
                                </div>
                            </div>

                            <div class="level-stats">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo ($level['kami_discount'] * 10); ?>折</div>
                                    <div class="stat-label">卡密折扣</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $level['daily_free_kami']; ?>个</div>
                                    <div class="stat-label">每日免费</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">¥<?php echo number_format($level['initial_balance'], 2); ?></div>
                                    <div class="stat-label">初始余额</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">
                                        <?php echo $level['max_sub_agents'] == 0 ? '无限制' : $level['max_sub_agents']; ?>
                                    </div>
                                    <div class="stat-label">下级数量</div>
                                </div>
                            </div>

                            <div class="permission-grid">
                                <div class="permission-tag <?php echo $level['can_generate_kami'] == 1 ? 'active' : 'inactive'; ?>">
                                    <i class="layui-icon layui-icon-template-1"></i> 生成卡密
                                </div>
                                <div class="permission-tag <?php echo $level['can_manage_user'] == 1 ? 'active' : 'inactive'; ?>">
                                    <i class="layui-icon layui-icon-user"></i> 用户管理
                                </div>
                                <div class="permission-tag <?php echo $level['can_add_agent'] == 1 ? 'active' : 'inactive'; ?>">
                                    <i class="layui-icon layui-icon-group"></i> 添加代理
                                </div>
                                <div class="permission-tag <?php echo $level['can_set_discount'] == 1 ? 'active' : 'inactive'; ?>">
                                    <i class="layui-icon layui-icon-rate"></i> 设置折扣
                                </div>
                                <div class="permission-tag <?php echo $level['can_set_free_kami'] == 1 ? 'active' : 'inactive'; ?>">
                                    <i class="layui-icon layui-icon-gift"></i> 设置免费数
                                </div>
                            </div>

                            <div style="text-align: right; margin-top: 15px;">
                                <span style="color: #999; font-size: 12px;">
                                    <i class="layui-icon layui-icon-time"></i> 创建时间: <?php echo $level['created_time']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-tip">
                        <i class="layui-icon layui-icon-list"></i>
                        <h3>暂无等级数据</h3>
                        <p>点击右上角"初始化默认等级"按钮快速创建默认等级</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['layer'], function() {
            var layer = layui.layer;

            window.initLevels = function() {
                layer.confirm('确定要初始化默认等级吗？<br><br><span style="color: #ff5722;">注意：如果数据库中已有等级数据，将提示是否覆盖。</span>', function(index) {
                    $.ajax({
                        url: 'ajax_router.php?act=initdefaultlevels',
                        type: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.code == '1') {
                                layer.msg(res.msg || '初始化成功！', {icon: 1, time: 2000}, function() {
                                    window.location.reload();
                                });
                            } else {
                                // 需要用户确认覆盖
                                if (res.need_confirm) {
                                    layer.confirm(res.msg, {
                                        btn: ['覆盖', '取消']
                                    }, function(confirmIndex) {
                                        $.ajax({
                                            url: 'ajax_router.php?act=initdefaultlevels',
                                            type: 'POST',
                                            data: {overwrite: '1'},
                                            dataType: 'json',
                                            success: function(overwriteRes) {
                                                if (overwriteRes.code == '1') {
                                                    layer.msg(overwriteRes.msg, {icon: 1}, function() {
                                                        window.location.reload();
                                                    });
                                                } else {
                                                    layer.msg(overwriteRes.msg, {icon: 2});
                                                }
                                            }
                                        });
                                        layer.close(confirmIndex);
                                    });
                                } else {
                                    layer.msg(res.msg || '初始化失败！', {icon: 2});
                                }
                            }
                        },
                        error: function() {
                            layer.msg('系统错误，请稍后重试！', {icon: 2});
                        }
                    });
                    layer.close(index);
                });
            };

            window.editLevel = function(id) {
                layer.open({
                    type: 2,
                    title: '编辑等级',
                    area: ['600px', '700px'],
                    content: 'agent_level_edit.php?act=edit&id=' + id,
                    end: function() {
                        window.location.reload();
                    }
                });
            };

            window.configPermission = function(id) {
                layer.open({
                    type: 2,
                    title: '配置权限',
                    area: ['700px', '600px'],
                    content: 'agent_level_permission.php?id=' + id
                });
            };
        });
    </script>
</body>
</html>
