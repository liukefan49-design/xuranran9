<?php
include("../includes/common.php");
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}

// 检查权限：只有超级管理员（qx=0）才能访问
if ($subconf['qx'] != 0) {
    exit('<script language=\'javascript\'>alert("您没有权限访问此页面！");history.back();</script>');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $subconf['hostname']?>管理员管理</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <?php include("foot.php"); ?>
</head>
<body>
    <!-- 筛选条件 -->
    <div class="layui-card">
        <div class="layui-card-body layui-form">
            <div class="layui-form-item" style="padding-right: 5vw;padding-top: 15px;">
                <label class="layui-form-label" title="用户名">
                    用户名：
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="username" class="layui-input" placeholder="请输入用户名" />
                </div>
                <label class="layui-form-label" title="状态">
                    状态：
                </label>
                <div class="layui-input-inline">
                    <select name="state" lay-filter="state">
                        <option value="">全部</option>
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- 表格 -->
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="admin_list" lay-filter="admin_list"></table>
        </div>
    </div>

    <script type="text/html" id="admin_listTool">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="search">
                <i class="layui-icon layui-icon-search"></i><span>搜索</span>
            </button>
            <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="add">
                <i class="layui-icon layui-icon-add-1"></i><span>新增</span>
            </button>
            <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="edit">
                <i class="layui-icon layui-icon-edit"></i><span>编辑</span>
            </button>
            <button class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">
                <i class="layui-icon layui-icon-delete"></i><span>删除</span>
            </button>
        </div>
    </script>

    <!-- 状态开关 -->
    <script type="text/html" id="stateTool">
        <input type="checkbox" name="state" value="{{d.id}}" lay-skin="switch" lay-text="启用|禁用" lay-filter="state" {{ d.state == 1 ? 'checked' : '' }} />
    </script>

    <!-- 操作按钮 -->
    <script type="text/html" id="barTool">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    <script>
        layui.use(['jquery', 'table', 'form', 'layer'], function() {
            var $ = layui.$;
            var table = layui.table;
            var form = layui.form;
            var layer = layui.layer;

            // 获取查询条件
            function where() {
                return {
                    username: $('[name=username]').val(),
                    state: $('[name=state]').val()
                };
            }

            // 渲染表格
            var tableIns = table.render({
                elem: '#admin_list',
                height: 'full-170',
                url: 'ajax.php?act=getadminlist',
                page: true,
                limit: 50,
                limits: [10, 20, 30, 50, 100],
                title: '管理员列表',
                toolbar: '#admin_listTool',
                where: where(),
                cols: [[
                    {type: 'checkbox'},
                    {field: 'id', title: 'ID', width: 80, sort: true, align: 'center'},
                    {field: 'username', title: '用户名', width: 150, align: 'center'},
                    {field: 'realname', title: '真实姓名', width: 120, align: 'center'},
                    {field: 'state', title: '状态', width: 100, align: 'center', toolbar: '#stateTool'},
                    {field: 'created_at', title: '创建时间', width: 180, align: 'center'},
                    {field: 'last_login', title: '最后登录', width: 180, align: 'center'},
                    {field: 'remark', title: '备注', minWidth: 200, align: 'center'},
                    {fixed: 'right', title: '操作', width: 150, align: 'center', toolbar: '#barTool'}
                ]]
            });

            // 头工具栏事件
            table.on('toolbar(admin_list)', function(obj) {
                var checkStatus = table.checkStatus(obj.config.id);
                switch(obj.event) {
                    case 'search':
                        table.reload('admin_list', {
                            where: where(),
                            page: {curr: 1}
                        });
                        break;
                    case 'add':
                        addAdmin();
                        break;
                    case 'edit':
                        if (checkStatus.data.length === 1) {
                            editAdmin(checkStatus.data[0]);
                        } else {
                            layer.msg('请选择一条记录', {icon: 3});
                        }
                        break;
                    case 'del':
                        if (checkStatus.data.length > 0) {
                            delAdmin(checkStatus.data);
                        } else {
                            layer.msg('请选择要删除的记录', {icon: 3});
                        }
                        break;
                }
            });

            // 行工具栏事件
            table.on('tool(admin_list)', function(obj) {
                var data = obj.data;
                if (obj.event === 'edit') {
                    editAdmin(data);
                } else if (obj.event === 'del') {
                    delAdmin([data]);
                }
            });

            // 状态开关
            form.on('switch(state)', function(obj) {
                var id = this.value;
                var state = obj.elem.checked ? 1 : 0;
                
                $.ajax({
                    url: 'ajax.php?act=updateadminstate',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: id, state: state},
                    success: function(res) {
                        if (res.code == '1') {
                            layer.msg(res.msg, {icon: 1});
                        } else {
                            layer.msg(res.msg, {icon: 2});
                            obj.elem.checked = !obj.elem.checked;
                            form.render('checkbox');
                        }
                    }
                });
            });

            // 搜索条件变化
            $('.layui-input').keydown(function(e) {
                if (e.keyCode == 13) {
                    table.reload('admin_list', {
                        where: where(),
                        page: {curr: 1}
                    });
                }
            });

            form.on('select(state)', function() {
                table.reload('admin_list', {
                    where: where(),
                    page: {curr: 1}
                });
            });

            // 新增管理员
            function addAdmin() {
                layer.open({
                    type: 2,
                    title: '新增管理员',
                    area: ['500px', '450px'],
                    content: 'add_admin.php',
                    end: function() {
                        table.reload('admin_list');
                    }
                });
            }

            // 编辑管理员
            function editAdmin(data) {
                layer.open({
                    type: 2,
                    title: '编辑管理员',
                    area: ['500px', '450px'],
                    content: 'edit_admin.php?id=' + data.id,
                    end: function() {
                        table.reload('admin_list');
                    }
                });
            }

            // 删除管理员
            function delAdmin(data) {
                var ids = [];
                data.forEach(function(item) {
                    ids.push(item.id);
                });

                layer.confirm('确定删除选中的管理员吗？', {icon: 3}, function(index) {
                    $.ajax({
                        url: 'ajax.php?act=deladmin',
                        type: 'POST',
                        dataType: 'json',
                        data: {ids: ids.join(',')},
                        beforeSend: function() {
                            layer.msg('删除中...', {icon: 16, shade: 0.05, time: false});
                        },
                        success: function(res) {
                            layer.closeAll();
                            if (res.code == '1') {
                                layer.msg(res.msg, {icon: 1});
                                table.reload('admin_list');
                            } else {
                                layer.msg(res.msg, {icon: 2});
                            }
                        },
                        error: function() {
                            layer.closeAll();
                            layer.msg('删除失败', {icon: 2});
                        }
                    });
                    layer.close(index);
                });
            }
        });
    </script>
</body>
</html>

