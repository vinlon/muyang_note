<?php
/**
 * Custom Error
 */

/**
* 自定义错误提示
*/
class CustomError{
    const AUTH_ERR_CODE = 100;
    const PARAM_ERR_CODE = 200;
    const REDIS_ERR_CODE = 300;
    const CURL_ERR_CODE = 400;
    const MYSQL_ERR_CODE = 900;

    const INVALID_MSG_TYPE = [ 1, 'message type is invalid'];
    const INVALID_HANDLER_URL = [ 2, 'invalid handler url'];
}

?>
