<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$act = isset($_GET['act']) ? $_GET['act'] : 'add';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$level_data = array();

if ($act == 'edit' && $id > 0) {
    $level_data = $DB->selectRow("SELECT * FROM agent_level WHERE id = {$id}");
    if (!$level_data) {
        echo "<script>alert('等级不存在！');window.close();</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $act == 'add' ? '添加' : '编辑'; ?>代理等级</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .layui-form-label {
            width: 120px;
        }
        .layui-input-block {
            margin-left: 150px;
        }
        .price-input {
            width: 100px !important;
            display: inline-block;
        }
    </style>
</head>
<body style="padding: 20px;">
    <form class="layui-form" lay-filter="levelForm">
        <div class="layui-form-item">
            <label class="layui-form-label">等级名称</label>
            <div class="layui-input-block">
                <input type="text" name="level_name" required lay-verify="required" placeholder="请输入等级名称，如：一级代理"
                       value="<?php echo $level_data['level_name'] ?? ''; ?>" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">等级排序</label>
            <div class="layui-input-block">
                <input type="number" name="level_order" required lay-verify="required|number" placeholder="数字越大等级越高"
                       value="<?php echo $level_data['level_order'] ?? '1'; ?>" class="layui-input">
                <div class="layui-form-mid layui-word-aux">数字越大等级越高，相同数字按创建时间排序</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">卡密折扣</label>
            <div class="layui-input-block">
                <input type="number" name="kami_discount" required lay-verify="required|number" step="0.01" min="0.1" max="1"
                       placeholder="1.00表示原价" value="<?php echo $level_data['kami_discount'] ?? '1.00'; ?>" class="layui-input">
                <div class="layui-form-mid layui-word-aux">1.00=原价，0.90=9折，0.50=5折</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">初始余额</label>
            <div class="layui-input-block">
                <input type="number" name="initial_balance" required lay-verify="required|number" step="0.01" min="0"
                       placeholder="新注册代理的初始余额" value="<?php echo $level_data['initial_balance'] ?? '0.00'; ?>" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">每日免费卡密</label>
            <div class="layui-input-block">
                <input type="number" name="daily_free_kami" required lay-verify="required|number" min="0"
                       placeholder="0表示不免费" value="<?php echo $level_data['daily_free_kami'] ?? '0'; ?>" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">可添加下级数</label>
            <div class="layui-input-block">
                <input type="number" name="max_sub_agents" required lay-verify="required|number" min="0"
                       placeholder="0表示无限制" value="<?php echo $level_data['max_sub_agents'] ?? '0'; ?>" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">是否可生成卡密</label>
            <div class="layui-input-block">
                <input type="checkbox" name="can_generate_kami" lay-skin="switch" lay-text="是|否"
                       <?php echo ($act == 'add' || ($level_data['can_generate_kami'] ?? 1) == 1) ? 'checked' : ''; ?>>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">是否可管理用户</label>
            <div class="layui-input-block">
                <input type="checkbox" name="can_manage_user" lay-skin="switch" lay-text="是|否"
                       <?php echo ($act == 'add' || ($level_data['can_manage_user'] ?? 1) == 1) ? 'checked' : ''; ?>>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">是否可添加下级</label>
            <div class="layui-input-block">
                <input type="checkbox" name="can_add_agent" lay-skin="switch" lay-text="是|否"
                       <?php echo ($act == 'add' || ($level_data['can_add_agent'] ?? 1) == 1) ? 'checked' : ''; ?>>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">可设置折扣</label>
            <div class="layui-input-block">
                <input type="checkbox" name="can_set_discount" lay-skin="switch" lay-text="是|否"
                       <?php echo ($level_data['can_set_discount'] ?? 0) == 1 ? 'checked' : ''; ?>>
                <div class="layui-form-mid layui-word-aux">允许该等级代理设置下级代理的折扣</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">可设置免费数</label>
            <div class="layui-input-block">
                <input type="checkbox" name="can_set_free_kami" lay-skin="switch" lay-text="是|否"
                       <?php echo ($level_data['can_set_free_kami'] ?? 0) == 1 ? 'checked' : ''; ?>>
                <div class="layui-form-mid layui-word-aux">允许该等级代理设置下级代理的免费卡密数</div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="saveLevel">保存</button>
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
            form.on('submit(saveLevel)', function(data) {
                var formData = data.field;
                formData.act = '<?php echo $act; ?>';
                formData.id = <?php echo $id; ?>;

                // 处理开关值
                formData.can_generate_kami = formData.can_generate_kami === 'on' ? 1 : 0;
                formData.can_manage_user = formData.can_manage_user === 'on' ? 1 : 0;
                formData.can_add_agent = formData.can_add_agent === 'on' ? 1 : 0;
                formData.can_set_discount = formData.can_set_discount === 'on' ? 1 : 0;
                formData.can_set_free_kami = formData.can_set_free_kami === 'on' ? 1 : 0;

                // 验证折扣范围
                if (formData.kami_discount < 0.1 || formData.kami_discount > 1) {
                    layer.msg('折扣必须在0.1-1.0之间', {icon: 5});
                    return false;
                }

                $.ajax({
                    url: 'ajax_router.php?act=saveagentlevel',
                    type: 'POST',
                    data: formData,
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
