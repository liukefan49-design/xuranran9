<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$act = isset($_GET['act']) ? $_GET['act'] : 'add';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$notice_data = array();

if ($act == 'edit' && $id > 0) {
    $notice_data = $DB->selectRow("SELECT * FROM agent_notice WHERE id = {$id}");
    if (!$notice_data) {
        echo "<script>alert('公告不存在！');window.close();</script>";
        exit;
    }
}

// 获取所有等级列表
$levels = $DB->select("SELECT id, level_name FROM agent_level WHERE state = 1 ORDER BY level_order");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $act == 'add' ? '发布' : '编辑'; ?>公告</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
</head>
<body style="padding: 20px;">
    <form class="layui-form" lay-filter="noticeForm">
        <div class="layui-form-item">
            <label class="layui-form-label">公告标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" required lay-verify="required" placeholder="请输入公告标题"
                       value="<?php echo $notice_data['title'] ?? ''; ?>" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">公告类型</label>
            <div class="layui-input-block">
                <select name="notice_type" lay-verify="required">
                    <option value="1" <?php echo ($notice_data['notice_type'] ?? 1) == 1 ? 'selected' : ''; ?>>系统公告</option>
                    <option value="2" <?php echo ($notice_data['notice_type'] ?? 1) == 2 ? 'selected' : ''; ?>>活动通知</option>
                    <option value="3" <?php echo ($notice_data['notice_type'] ?? 1) == 3 ? 'selected' : ''; ?>>重要提醒</option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">优先级</label>
            <div class="layui-input-block">
                <input type="number" name="priority" required lay-verify="required|number"
                       placeholder="数字越大优先级越高" value="<?php echo $notice_data['priority'] ?? '0'; ?>" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">目标等级</label>
            <div class="layui-input-block">
                <select name="target_level" lay-verify="required">
                    <option value="0" <?php echo ($notice_data['target_level'] ?? 0) == 0 ? 'selected' : ''; ?>>所有等级</option>
                    <?php foreach ($levels as $level): ?>
                    <option value="<?php echo $level['id']; ?>" <?php echo ($notice_data['target_level'] ?? 0) == $level['id'] ? 'selected' : ''; ?>>
                        <?php echo $level['level_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">公告内容</label>
            <div class="layui-input-block">
                <textarea name="content" required lay-verify="required" placeholder="请输入公告内容（支持HTML）"
                          class="layui-textarea" style="min-height: 200px;"><?php echo $notice_data['content'] ?? ''; ?></textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">过期时间</label>
            <div class="layui-input-block">
                <input type="text" name="expire_time" id="expire_time" placeholder="留空表示不过期"
                       value="<?php echo $notice_data['expire_time'] ?? ''; ?>" class="layui-input">
                <div class="layui-form-mid layui-word-aux">留空表示公告永不过期</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">置顶公告</label>
            <div class="layui-input-block">
                <input type="checkbox" name="is_sticky" lay-skin="switch" lay-text="是|否"
                       <?php echo ($notice_data['is_sticky'] ?? 0) == 1 ? 'checked' : ''; ?>>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">立即发布</label>
            <div class="layui-input-block">
                <input type="checkbox" name="state" lay-skin="switch" lay-text="是|否"
                       <?php echo ($act == 'add' || ($notice_data['state'] ?? 1) == 1) ? 'checked' : ''; ?>>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="saveNotice">保存</button>
                <button type="button" class="layui-btn layui-btn-primary" onclick="window.close()">取消</button>
            </div>
        </div>
    </form>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['form', 'laydate', 'layer'], function() {
            var form = layui.form;
            var laydate = layui.laydate;
            var layer = layui.layer;
            var $ = layui.jquery;

            // 日期选择器
            laydate.render({
                elem: '#expire_time',
                type: 'datetime',
                format: 'yyyy-MM-dd HH:mm:ss'
            });

            // 监听提交
            form.on('submit(saveNotice)', function(data) {
                var formData = data.field;
                formData.act = '<?php echo $act; ?>';
                formData.id = <?php echo $id; ?>;
                formData.is_sticky = formData.is_sticky === 'on' ? 1 : 0;
                formData.state = formData.state === 'on' ? 1 : 0;

                if (!formData.expire_time) {
                    formData.expire_time = null;
                }

                $.ajax({
                    url: 'ajax_agent.php?act=saveagentnotice',
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
