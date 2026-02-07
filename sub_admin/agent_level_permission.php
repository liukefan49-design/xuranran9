<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$level = $DB->selectRow("SELECT * FROM agent_level WHERE id = {$id}");
if (!$level) {
    echo "<script>alert('等级不存在！');window.close();</script>";
    exit;
}

// 获取该等级的代理数量
$agent_count = $DB->selectRow("SELECT COUNT(*) as count FROM agent WHERE level_id = {$id}");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>等级权限配置 - <?php echo $level['level_name']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .permission-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .permission-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .permission-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .permission-item:last-child {
            border-bottom: none;
        }
        .permission-label {
            flex: 1;
            font-size: 14px;
            color: #666;
        }
        .permission-desc {
            font-size: 12px;
            color: #999;
            margin-left: 10px;
        }
        .level-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 15px;
            color: white;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
        }
    </style>
</head>
<body style="padding: 20px;">
    <div class="level-stats">
        <div class="stat-box">
            <div class="stat-label">代理数量</div>
            <div class="stat-value"><?php echo $agent_count['count']; ?></div>
        </div>
        <div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="stat-label">卡密折扣</div>
            <div class="stat-value"><?php echo ($level['kami_discount'] * 10); ?>折</div>
        </div>
        <div class="stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="stat-label">每日免费</div>
            <div class="stat-value"><?php echo $level['daily_free_kami']; ?>个</div>
        </div>
        <div class="stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="stat-label">初始余额</div>
            <div class="stat-value">¥<?php echo $level['initial_balance']; ?></div>
        </div>
    </div>

    <form class="layui-form" lay-filter="permissionForm">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <div class="permission-section">
            <div class="permission-title">基础权限</div>
            
            <div class="permission-item">
                <div>
                    <div class="permission-label">生成卡密权限</div>
                    <div class="permission-desc">是否允许该等级代理生成卡密</div>
                </div>
                <input type="checkbox" name="can_generate_kami" lay-skin="switch" lay-text="允许|禁止"
                       <?php echo $level['can_generate_kami'] == 1 ? 'checked' : ''; ?>>
            </div>
            
            <div class="permission-item">
                <div>
                    <div class="permission-label">用户管理权限</div>
                    <div class="permission-desc">是否允许管理用户账号</div>
                </div>
                <input type="checkbox" name="can_manage_user" lay-skin="switch" lay-text="允许|禁止"
                       <?php echo $level['can_manage_user'] == 1 ? 'checked' : ''; ?>>
            </div>
            
            <div class="permission-item">
                <div>
                    <div class="permission-label">添加下级代理权限</div>
                    <div class="permission-desc">是否允许添加下级代理</div>
                </div>
                <input type="checkbox" name="can_add_agent" lay-skin="switch" lay-text="允许|禁止"
                       <?php echo $level['can_add_agent'] == 1 ? 'checked' : ''; ?>>
            </div>
        </div>

        <div class="permission-section">
            <div class="permission-title">高级权限</div>
            
            <div class="permission-item">
                <div>
                    <div class="permission-label">设置下级折扣权限</div>
                    <div class="permission-desc">允许该等级代理设置下级代理的卡密折扣</div>
                </div>
                <input type="checkbox" name="can_set_discount" lay-skin="switch" lay-text="允许|禁止"
                       <?php echo $level['can_set_discount'] == 1 ? 'checked' : ''; ?>>
            </div>
            
            <div class="permission-item">
                <div>
                    <div class="permission-label">设置免费卡密数权限</div>
                    <div class="permission-desc">允许该等级代理设置下级代理的每日免费卡密数</div>
                </div>
                <input type="checkbox" name="can_set_free_kami" lay-skin="switch" lay-text="允许|禁止"
                       <?php echo $level['can_set_free_kami'] == 1 ? 'checked' : ''; ?>>
            </div>
        </div>

        <div class="permission-section">
            <div class="permission-title">数值配置</div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">卡密折扣</label>
                <div class="layui-input-block">
                    <input type="number" name="kami_discount" step="0.01" min="0.1" max="1"
                           value="<?php echo $level['kami_discount']; ?>" class="layui-input">
                    <div class="layui-form-mid layui-word-aux">1.00=原价，0.90=9折，0.50=5折</div>
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">每日免费卡密</label>
                <div class="layui-input-block">
                    <input type="number" name="daily_free_kami" min="0"
                           value="<?php echo $level['daily_free_kami']; ?>" class="layui-input">
                    <div class="layui-form-mid layui-word-aux">0表示不免费</div>
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">初始余额</label>
                <div class="layui-input-block">
                    <input type="number" name="initial_balance" step="0.01" min="0"
                           value="<?php echo $level['initial_balance']; ?>" class="layui-input">
                    <div class="layui-form-mid layui-word-aux">新注册代理的初始余额</div>
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">最大下级数</label>
                <div class="layui-input-block">
                    <input type="number" name="max_sub_agents" min="0"
                           value="<?php echo $level['max_sub_agents']; ?>" class="layui-input">
                    <div class="layui-form-mid layui-word-aux">0表示无限制</div>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="savePermission">保存配置</button>
                <button type="button" class="layui-btn layui-btn-primary" onclick="window.close()">取消</button>
            </div>
        </div>
    </form>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['form', 'layer'], function() {
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.jquery;

            // 监听提交
            form.on('submit(savePermission)', function(data) {
                var formData = data.field;
                
                // 明确设置ID值
                formData.id = <?php echo $id; ?>;

                // 验证折扣范围
                if (formData.kami_discount < 0.1 || formData.kami_discount > 1) {
                    layer.msg('折扣必须在0.1-1.0之间', {icon: 5});
                    return false;
                }

                $.ajax({
                    url: 'ajax_agent.php?act=saveagentlevel',
                    type: 'POST',
                    data: {
                        id: formData.id,
                        level_name: '<?php echo $level['level_name']; ?>',
                        level_order: <?php echo $level['level_order']; ?>,
                        kami_discount: formData.kami_discount,
                        initial_balance: formData.initial_balance,
                        daily_free_kami: formData.daily_free_kami,
                        max_sub_agents: formData.max_sub_agents,
                        can_generate_kami: formData.can_generate_kami === 'on' ? 1 : 0,
                        can_manage_user: formData.can_manage_user === 'on' ? 1 : 0,
                        can_add_agent: formData.can_add_agent === 'on' ? 1 : 0,
                        can_set_discount: formData.can_set_discount === 'on' ? 1 : 0,
                        can_set_free_kami: formData.can_set_free_kami === 'on' ? 1 : 0
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.code == '1') {
                            layer.msg(res.msg, {icon: 1}, function() {
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(index);
                            });
                        } else {
                            layer.msg(res.msg, {icon: 5});
                        }
                    },
                    error: function(xhr, status, error) {
                        layer.msg('请求失败：' + (xhr.responseJSON ? xhr.responseJSON.msg : error), {icon: 5});
                    }
                });

                return false;
            });
        });
    </script>
</body>
</html>
