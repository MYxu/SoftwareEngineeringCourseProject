<?php
return array(
	//'配置项'=>'配置值'

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',      // 数据库类型
    'DB_HOST'               =>  '127.0.0.1',  // 服务器地址
    'DB_NAME'               =>  'guo_ya',     // 数据库名
    'DB_USER'               =>  'root',       // 用户名
    'DB_PWD'                =>  '',           // 密码
    'DB_PORT'               =>  '3306',       // 端口
    'DB_DEBUG'  			=>  TRUE,         // 数据库调试模式 开启后可以记录SQL日志
    'DB_CHARSET'            =>  'utf8',       // 数据库编码默认采用utf8

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  =>  true,         // URL区分大小写
    'URL_MODEL'               =>  2,          //URL模式 REWRITE模式


    /* 模板引擎设置 */
    'TMPL_TEMPLATE_SUFFIX'  =>  '.html',           // 伪静态后缀

    /* 自动加载公共函数 配置两个公共函数 */
    'LOAD_EXT_FILE'  => 'functions'

);