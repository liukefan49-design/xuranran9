<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="../assets/layui/css/layui.css?v=20241111001" />
		<link rel="stylesheet" type="text/css" href="../sub_admin/css/admin.css" />
		<link rel="stylesheet" type="text/css" href="../sub_admin/css/theme.css" />
		<title>代理后台管理</title>
        <style>
            * {
                transition: none;
            }
            
            .layui-nav-item {
                transition: background-color 0.3s ease;
            }
            
            .layui-nav-item:hover {
                background-color: rgba(255,255,255,0.1);
            }
            
            .layui-nav-item a i,
            .layui-nav-item a span,
            .layui-nav-item a em {
                display: inline-block;
                transition: transform 0.2s ease-out;
                will-change: transform;
            }
            
            .layui-nav-item a:hover i,
            .layui-nav-item a:hover span,
            .layui-nav-item a:hover em {
                transform: translateX(5px);
            }
            
            .layui-nav-child dd a:hover i,
            .layui-nav-child dd a:hover span {
                transform: none;
            }
            
            .custom-header .layui-nav-item a:hover i,
            .custom-header .layui-nav-item a:hover span {
                transform: none;
            }
            
            .custom-logo {
                padding: 20px 0;
                text-align: center;
                transition: all 0.4s;
            }
            
            #logos {
                font-size: 24px;
                color: #fff;
                margin: 0;
                text-shadow: 0 0 10px rgba(51, 202, 187, 0.5);
                animation: glow 2s ease-in-out infinite alternate;
            }
            
            .custom-logo #logowz {
                display: block;
                font-size: 14px;
                color: #33cabb !important;
                margin-top: 5px;
                font-weight: 500;
                letter-spacing: 1px;
                text-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
            }
            
            .layui-badge {
                transition: transform 0.3s;
            }
            
            .layui-badge:hover {
                transform: scale(1.1);
            }
            
            @keyframes glow {
                from {
                    text-shadow: 0 0 5px #33cabb, 0 0 10px #33cabb;
                }
                to {
                    text-shadow: 0 0 10px #33cabb, 0 0 20px #33cabb;
                }
            }
            
            .mobile-mask {
                transition: opacity 0.3s;
            }
            
            .layui-tab-title li {
                transition: all 0.3s;
            }
            
            .layui-tab-title li:hover {
                background-color: rgba(51, 202, 187, 0.1);
            }
            
            @media screen and (max-width: 768px) {
                .custom-logo {
                    padding: 10px 0;
                }
                
                #logos {
                    font-size: 20px;
                }
            }
        </style>
	</head>
	<body class="layui-layout-body">
		<div class="layui-layout layui-layout-admin">
			<!-- 头部 -->
			<div class="layui-header custom-header">
				<ul class="layui-nav layui-layout-left">
					<li class="layui-nav-item slide-sidebar" lay-unselect>
						<a href="javascript:;" class="icon-font"><i class="ai ai-menufold"></i></a>
					</li>
				</ul>
				<ul class="layui-nav layui-layout-right">
					<li class="layui-nav-item">
						<a href="javascript:;">
							<i class="layui-icon layui-icon-notice"></i>
							<span>消息</span><span class="layui-badge">0</span>
						</a>
					</li>
					<li class="layui-nav-item">
						<a href="javascript:;" style="color:#33cabb;">
							<i class="layui-icon layui-icon-username"></i>
							<span id="username"><?php echo $agentInfo['username']; ?></span>
						</a>
						<dl class="layui-nav-child">
							<dd>
								<a href="javascript:;" id="update_password">
									<i class="layui-icon layui-icon-password"></i>
									<span>修改密码</span>
								</a>
							</dd>
							<dd>
								<a href="javascript:;" id="quit">
									<i class="layui-icon layui-icon-logout"></i>
									<span>退出登录</span>
								</a>
							</dd>
						</dl>
					</li>
				</ul>
			</div>
			<!-- 左侧 -->
			<div class="layui-side custom-admin">
				<div class="layui-side-scroll">
					<div class="custom-logo">
						<h1 id="logos">代理中心</h1>
						<span id="logowz">卡密管理系统</span>
					</div>
					<ul id="Nav" class="layui-nav layui-nav-tree" lay-filter="tabnav">
						<li class="layui-nav-item layui-nav-itemed">
							<a href="javascript:;">
								<i class="layui-icon layui-icon-console"></i>
								<em>控制台</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="primary.php"><span>系统主页</span></a></dd>
								<dd><a href="kami.php"><span>卡密生成</span></a></dd>
								<dd><a href="new_kami.php"><span>快速生成</span></a></dd>
							</dl>
						</li>
						<li class="layui-nav-item">
							<a href="javascript:;">
								<i class="layui-icon layui-icon-user"></i>
								<em>用户管理</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="user_list.php"><span>用户列表</span></a></dd>
							</dl>
						</li>
						<li class="layui-nav-item">
							<a href="javascript:;">
								<i class="layui-icon layui-icon-group"></i>
								<em>下级代理</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="sub_agent_list.php"><span>代理管理</span></a></dd>
								<dd><a href="new_sub_agent.php"><span>添加代理</span></a></dd>
							</dl>
						</li>
						<li class="layui-nav-item">
							<a href="javascript:;">
								<i class="layui-icon layui-icon-form"></i>
								<em>卡密管理</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="kami.php"><span>卡密生成</span></a></dd>
								<dd><a href="new_kami.php"><span>快速生成</span></a></dd>
							</dl>
						</li>
					</ul>
				</div>
			</div>
			<!-- 主体 -->
			<div class="layui-body">
				<div class="layui-tab app-container" lay-allowClose="true" lay-filter="tabs">
					<ul id="appTabs" class="layui-tab-title custom-tab"></ul>
					<div id="appTabPage" class="layui-tab-content"></div>
				</div>
			</div>
			<div class="mobile-mask"></div>
		</div>
		<script src="../assets/layui/layui.js"></script>
		<script src="../assets/js/index.js"></script>
	<script src="./js/agent.js"></script>
	</body>
</html>

