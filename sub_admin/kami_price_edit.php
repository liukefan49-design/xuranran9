<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 获取所有价格数据
$prices = $DB->select("SELECT * FROM kami_price ORDER BY sort_order");
$edit_data = array();

if ($id > 0) {
    $edit_data = $DB->selectRow("SELECT * FROM kami_price WHERE id = {$id}");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $id > 0 ? '编辑' : '批量编辑'; ?>卡密价格</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .price-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .price-item-header {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .layui-input-inline {
            width: auto !important;
        }
    </style>
</head>
<body style="padding: 20px;">
    <form class="layui-form" lay-filter="priceForm" id="priceForm">
        <?php if ($id > 0): ?>
        <!-- 单个编辑模式 -->
        <div class="price-item">
            <div class="price-item-header">编辑 - <?php echo $edit_data['type_name']; ?></div>
            <input type="hidden" name="edit_id" value="<?php echo $id; ?>">
            
            <div class="layui-form-item">
                <label class="layui-form-label">类型名称</label>
                <div class="layui-input-block">
                    <input type="text" name="type_name" required lay-verify="required"
                           value="<?php echo $edit_data['type_name']; ?>" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">有效天数</label>
                <div class="layui-input-block">
                    <input type="number" name="days" required lay-verify="required|number" min="0"
                           value="<?php echo $edit_data['days']; ?>" class="layui-input">
                    <div class="layui-form-mid layui-word-aux">0表示永久有效</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">生成价格</label>
                <div class="layui-input-block">
                    <input type="number" name="price" required lay-verify="required|number" step="0.01" min="0"
                           value="<?php echo $edit_data['price']; ?>" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="number" name="sort_order" required lay-verify="required|number"
                           value="<?php echo $edit_data['sort_order']; ?>" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <textarea name="description" class="layui-textarea"><?php echo $edit_data['description'] ?? ''; ?></textarea>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- 批量编辑模式 -->
        <div id="batchContainer">
            <?php foreach ($prices as $index => $price): ?>
            <div class="price-item" data-id="<?php echo $price['id']; ?>">
                <div class="price-item-header">
                    <?php echo $price['type_name']; ?> (<?php echo $price['days'] == 0 ? '永久' : $price['days'] . '天'; ?>)
                </div>
                
                <div class="layui-form-item">
                    <label class="layui-form-label">类型名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="type_name_<?php echo $price['id']; ?>" 
                               value="<?php echo $price['type_name']; ?>" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">有效天数</label>
                    <div class="layui-input-block">
                        <input type="number" name="days_<?php echo $price['id']; ?>" min="0"
                               value="<?php echo $price['days']; ?>" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">生成价格</label>
                    <div class="layui-input-block">
                        <input type="number" name="price_<?php echo $price['id']; ?>" step="0.01" min="0"
                               value="<?php echo $price['price']; ?>" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">排序</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort_order_<?php echo $price['id']; ?>"
                               value="<?php echo $price['sort_order']; ?>" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description_<?php echo $price['id']; ?>" class="layui-textarea"><?php echo $price['description'] ?? ''; ?></textarea>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="savePrice">保存</button>
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
            form.on('submit(savePrice)', function(data) {
                var formData = data.field;
                var editId = formData.edit_id;

                if (editId) {
                    // 单个编辑
                    $.ajax({
                        url: 'ajax.php?act=savekamiprice',
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
                        }
                    });
                } else {
                    // 批量编辑
                    var batchData = [];
                    $('.price-item').each(function() {
                        var id = $(this).data('id');
                        batchData.push({
                            id: id,
                            type_name: formData['type_name_' + id],
                            days: formData['days_' + id],
                            price: formData['price_' + id],
                            sort_order: formData['sort_order_' + id],
                            description: formData['description_' + id]
                        });
                    });

                    $.ajax({
                        url: 'ajax.php?act=savekamipricebatch',
                        type: 'POST',
                        data: {data: JSON.stringify(batchData)},
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
                        }
                    });
                }

                return false;
            });
        });
    </script>
</body>
</html>
