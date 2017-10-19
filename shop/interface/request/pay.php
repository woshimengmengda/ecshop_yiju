<?php
/**
 * Created by PhpStorm.
 * User: yuzhongqiang
 * Date: 2017/10/10
 * Time: 10:28
 */
define('IN_ECS', TRUE);
require '../../includes/init.php';
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "../reachlife.php");
//优惠券使用
function test_setCouponAuthorization() {
    $Reachlife = new Reachlife();
    $param = array(
        "telephone" => "13917717166",
        "date" => "2016-09-08 16:33:18",
        "code" => "aHzKesyH"
    );
    $result = $Reachlife->curl("setCouponAuthorization", $param);
    return $result;
}