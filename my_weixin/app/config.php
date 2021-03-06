<?php

define('MODE', 'DEVELOP'); //DEVELOP, TEST, PRODUCTION

//不同模式加载不同的配置
if(MODE === 'PRODUCTION'){
    require_once('config_production.php');
}else if(MODE === 'TEST'){
    require_once('config_test.php');
}else{
    require_once('config_develop.php');
}

/** 微信相关配置 **/
/** APPID **/
define('APPID', 'wx73d9011240b539eb');
/** APPSECRET **/
define('APPSECRET', 'b8d7496ac3a1730e016670d7c6954180');
/** TOKEN **/
define('TOKEN', 'limuyang');
/** AES_KEY **/
define('AES_KEY', 'lwaYEyl6xUHzKrJu9CbNydok1GgPp0j9jEDQIFYk66K');


/** FOR全局异常处理 **/
/** 项目名称 **/
define('PROJECT_NAME','WENLONG.MY_WEIXIN');
/** 项目负责人邮箱 **/
define('PROJECT_OWNER','liwenlong@aikaka.cc');

/** FOR服务定义 */
/** 服务所属域 **/
define('SERVICE_DOMAIN','WENLONG');
/** 服务命名空间 **/
define('SERVICE_NAMESPACE','MY_WEIXIN');
/**自定义错误前缀 */
define('ERROR_PREFIX','x200'); //x：项目编号

?>
