<?php
/**
 * 卡片视图通用模板
 * 用于将表格视图转换为卡片视图
 */

// 获取页面标题
$pageTitle = isset($pageTitle) ? $pageTitle : '列表';
$cardTitle = isset($cardTitle) ? $cardTitle : '卡片';
$tableUrl = isset($tableUrl) ? $tableUrl : '';
$refreshFunction = isset($refreshFunction) ? $refreshFunction : '';

// 卡片样式
?>
<style>
    .card-container {
        padding: 15px;
        background: #f2f2f2;
    }
    
    .info-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 2px solid #667eea;
    }
    
    .card-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    
    .card-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        margin-left: 10px;
    }
    
    .card-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .card-item {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    .card-label {
        font-size: 12px;
        color: #999;
        margin-bottom: 5px;
    }
    
    .card-value {
        font-size: 16px;
        font-weight: bold;
        color: #667eea;
    }
    
    .card-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    
    /* 移动端适配 */
    @media (max-width: 768px) {
        .card-container {
            padding: 10px;
        }
        
        .info-card {
            padding: 15px;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .card-content {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .card-item {
            padding: 8px;
        }
        
        .card-label {
            font-size: 11px;
        }
        
        .card-value {
            font-size: 14px;
        }
        
        .card-actions {
            justify-content: center;
        }
        
        .card-actions .layui-btn {
            flex: 1;
            min-width: 80px;
        }
    }
</style>

<!-- 卡片视图头部 -->
<div class="layui-card">
    <div class="layui-card-header">
        <span style="font-size: 16px; font-weight: bold;"><?php echo $cardTitle; ?></span>
        <?php if($tableUrl): ?>
        <button class="layui-btn layui-btn-sm layui-btn-normal" style="float: right; margin-left: 10px;" onclick="window.location.href='<?php echo $tableUrl; ?>'">
            <i class="layui-icon layui-icon-list"></i>表格视图
        </button>
        <?php endif; ?>
        <?php if($refreshFunction): ?>
        <button class="layui-btn layui-btn-sm layui-btn-warm" style="float: right;" onclick="<?php echo $refreshFunction; ?>()">
            <i class="layui-icon layui-icon-refresh"></i>刷新
        </button>
        <?php endif; ?>
    </div>
    <div class="layui-card-body">
        <div class="card-container" id="cardContainer">
            <!-- 卡片内容将通过JS动态生成 -->
        </div>
    </div>
</div>

<script>
// 通用卡片渲染函数
window.renderCards = function(data, config) {
    var html = '';
    var colors = [
        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
    ];
    
    $.each(data, function(index, item) {
        var color = colors[index % colors.length];
        var cardHtml = '<div class="info-card">';
        
        // 卡片头部
        if (config.titleField) {
            var title = item[config.titleField];
            var badge = config.badgeField ? '<span class="card-badge">' + item[config.badgeField] + '</span>' : '';
            cardHtml += '<div class="card-header">';
            cardHtml += '<div><span class="card-title">' + title + '</span>' + badge + '</div>';
            cardHtml += '</div>';
        }
        
        // 卡片内容
        if (config.fields && config.fields.length > 0) {
            cardHtml += '<div class="card-content">';
            $.each(config.fields, function(i, field) {
                var label = field.label;
                var value = field.value ? field.value(item) : item[field.name];
                var className = field.className || '';
                cardHtml += '<div class="card-item">';
                cardHtml += '<div class="card-label">' + label + '</div>';
                cardHtml += '<div class="card-value ' + className + '">' + value + '</div>';
                cardHtml += '</div>';
            });
            cardHtml += '</div>';
        }
        
        // 卡片操作
        if (config.actions) {
            cardHtml += '<div class="card-actions">';
            $.each(config.actions, function(i, action) {
                var btnClass = action.btnClass || 'layui-btn-xs';
                var icon = action.icon ? '<i class="layui-icon ' + action.icon + '"></i> ' : '';
                var onClick = action.onClick ? action.onClick(item) : '';
                cardHtml += '<button class="layui-btn ' + btnClass + '" ' + onClick + '>' + icon + action.label + '</button>';
            });
            cardHtml += '</div>';
        }
        
        cardHtml += '</div>';
        html += cardHtml;
    });
    
    $('#cardContainer').html(html);
};
</script>