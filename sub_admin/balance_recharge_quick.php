<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$agent_id = isset($_GET['agent_id']) ? intval($_GET['agent_id']) : 0;
$agent = array();
if ($agent_id > 0) {
    $agent = $DB->selectRow("SELECT * FROM agent WHERE id = {$agent_id}");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>快速充值</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
</head>
<body style="padding: 20px;">
    <form class="layui-form" lay-filter="rechargeForm">
        <div class="layui-form-item">
            <label class="layui-form-label">充值方式</label>
            <div class="layui-input-block">
                <input type="radio" name="recharge_type" value="id" title="代理ID" checked lay-filter="rechargeType">
                <input type="radio" name="recharge_type" value="unique_id" title="专属ID" lay-filter="rechargeType">
            </div>
        </div>

        <div class="layui-form-item" id="agentIdItem">
            <label class="layui-form-label">代理ID</label>
            <div class="layui-input-block">
                <input type="number" name="agent_id" placeholder="请输入代理ID"
                       value="<?php echo $agent_id; ?>" class="layui-input" <?php echo $agent_id > 0 ? 'readonly' : ''; ?>>
                <?php if ($agent_id > 0): ?>
                <div class="layui-form-mid layui-word-aux">代理账号：<?php echo $agent['username']; ?> | 当前余额：¥<?php echo $agent['balance']; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="layui-form-item layui-hide" id="uniqueIdItem">
            <label class="layui-form-label">专属ID</label>
            <div class="layui-input-block">
                <input type="text" name="unique_id" placeholder="请输入代理专属ID" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">充值金额</label>
            <div class="layui-input-block">
                <input type="number" name="amount" required lay-verify="required|number" step="0.01" min="0.01"
                       placeholder="请输入充值金额" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">快捷金额</label>
            <div class="layui-input-block">
                <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" onclick="setAmount(10)">¥10</button>
                <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" onclick="setAmount(50)">¥50</button>
                <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" onclick="setAmount(100)">¥100</button>
                <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" onclick="setAmount(500)">¥500</button>
                <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" onclick="setAmount(1000)">¥1000</button>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea name="remark" placeholder="请输入备注信息（选填）" class="layui-textarea"></textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="recharge">立即充值</button>
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

            // 充值方式切换
            form.on('radio(rechargeType)', function(data) {
                if (data.value === 'id') {
                    $('#agentIdItem').removeClass('layui-hide');
                    $('#uniqueIdItem').addClass('layui-hide');
                } else {
                    $('#agentIdItem').addClass('layui-hide');
                    $('#uniqueIdItem').removeClass('layui-hide');
                }
            });

            // 快捷金额
            window.setAmount = function(amount) {
                $('input[name="amount"]').val(amount);
            };

            // 监听提交
            form.on('submit(recharge)', function(data) {
                var formData = data.field;

                if (formData.recharge_type === 'id') {
                    if (!formData.agent_id) {
                        layer.msg('请输入代理ID', {icon: 5});
                        return false;
                    }
                } else {
                    if (!formData.unique_id) {
                        layer.msg('请输入专属ID', {icon: 5});
                        return false;
                    }
                }

                if (formData.amount <= 0) {
                    layer.msg('充值金额必须大于0', {icon: 5});
                    return false;
                }

                $.ajax({
                    url: 'ajax_agent.php?act=quickrecharge',
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
                    error: function() {
                        layer.msg('请求失败，请重试', {icon: 5});
                    }
                });

                return false;
            });
        });
    </script>
</body>
</html>
