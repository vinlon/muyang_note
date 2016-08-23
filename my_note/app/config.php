<?php

define("MODE", "DEVELOP"); //DEVELOP, TEST, PRODUCTION

$host = $_SERVER['HTTP_HOST'];

//不同模式加载不同的配置
if($host === "ab.aikaka.com.cn"){
    require_once("config_production.php");
}else if($host === "test.aikaka.com.cn"){
    require_once("config_test.php");
}else{
    require_once("config_develop.php");
}


/** FOR全局异常处理 **/
/** 项目名称 **/
define("PROJECT_NAME","WENLONG.MY_NOTE");
/** 项目负责人邮箱 **/
define("PROJECT_OWNER","liwenlong@aikaka.cc");

/** FOR服务定义 */
/** 服务所属域 **/
define("SERVICE_DOMAIN","WENLONG");
/** 服务命名空间 **/
define("SERVICE_NAMESPACE","MY_NOTE");
/**自定义错误前缀 */
define("ERROR_PREFIX","x200"); //x：项目编号

?>
