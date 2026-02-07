<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$appcode = isset($_GET['appcode']) ? addslashes($_GET['appcode']) : '';
$app = $DB->selectRow("select * from application where appcode='" . $appcode . "'");
if (!$app) {
    exit('应用不存在');
}

// 获取所有代理(排除已授权的)
$sql = "SELECT a.* FROM agent a 
        WHERE a.state=1 
        AND a.id NOT IN (
            SELECT agent_id FROM app_agent_access WHERE appcode='" . $appcode . "'
        )
        ORDER BY a.id DESC";
$agents = $DB->select($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加授权代理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        body {
            padding: 15px;
        }
        .agent-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .agent-checkbox {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .agent-checkbox:hover {
            background: #f8f8f8;
        }
    </style>
</head>
<body>
    <form class="layui-form" lay-filter="addAgentForm">
        <input type="hidden" name="appcode" value="<?php echo $appcode; ?>">
        
        <div class="layui-form-item">
            <label class="layui-form-label">应用名称</label>
            <div class="layui-input-block">
                <input type="text" value="<?php echo $app['appname']; ?> (<?php echo $app['appcode']; ?>)" class="layui-input" disabled>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">选择代理</label>
            <div class="layui-input-block">
                <?php if (empty($agents)): ?>
                    <div style="padding: 20px; text-align: center; color: #999;">
                        暂无可添加的代理
                    </div>
                <?php else: ?>
                    <div class="agent-list">
                        <?php foreach ($agents as $agent): ?>
                            <div class="agent-checkbox">
                                <input type="checkbox" name="agent_ids[]" value="<?php echo $agent['id']; ?>" 
                                       title="<?php echo $agent['username']; ?> - <?php echo $agent['name']; ?> (余额: ¥<?php echo $agent['balance']; ?>)" 
                                       lay-skin="primary">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($agents)): ?>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="submitForm">确定添加</button>
                <button type="button" class="layui-btn layui-btn-primary" onclick="closeWindow()">取消</button>
            </div>
        </div>
        <?php endif; ?>
    </form>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['form', 'layer'], function() {
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.$;

            // 监听提交
            form.on('submit(submitForm)', function(data) {
                var agent_ids = [];
                $('input[name="agent_ids[]"]:checked').each(function() {
                    agent_ids.push($(this).val());
                });

                if (agent_ids.length == 0) {
                    layer.msg('请至少选择一个代理', {icon: 5});
                    return false;
                }

                $.ajax({
                    url: 'ajax.php?act=addagentaccess',
                    type: 'POST',
                    data: {
                        appcode: data.field.appcode,
                        agent_ids: agent_ids
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        layer.msg('正在添加...', {icon: 16, shade: 0.3, time: false});
                    },
                    success: function(res) {
                        layer.closeAll('msg');
                        if (res.code == '1') {
                            layer.msg(res.msg, {icon: 1}, function() {
                                parent.reloadAgentList();
                                closeWindow();
                            });
                        } else {
                            layer.msg(res.msg, {icon: 5});
                        }
                    },
                    error: function(xhr, status, error) {
                        layer.closeAll('msg');
                        layer.msg('操作失败：' + (xhr.responseJSON ? xhr.responseJSON.msg : error), {icon: 5});
                    }
                });

                return false;
            });
        });

        function closeWindow() {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        }
    </script>
</body>
</html>
