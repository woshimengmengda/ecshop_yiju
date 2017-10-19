<?php
/**
 * Created by PhpStorm.
 * User: yuzhongqiang
 * Date: 2017/10/10
 * Time: 0:30
 */
define('IN_ECS', TRUE);
require '../../includes/init.php';
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "../reachlife.php");
//未支付订单同步
function test_noPayOrdersSynchronization() {
    $Reachlife = new Reachlife();
    $products = array();
    $product1 = array(
        "code" => "110101",
        "goods" => "更换马桶按钮",
        "number" => "1.00",
        "amount" => "46.00",
        "category" => "1A0002",
        "price" => "50.00",
        "tax" => "6"
    );
    array_push($products, $product1);
    $param = array(
        "cardNumber" => "13917717166",
        "amount" => "46.00",
        "orderNumber" => "4103",
        "type" => "0",
        "serialNumber" => "E0021201609081144188813936",
        "orderDate" => "2016-09-08 11:44:18",
        "orderDetail" => json_encode($products),
        "price" => "50.00"
    );
    $result = $Reachlife->curl("noPayOrdersSynchronization", $param);
    return $result;
}

//未支付订单取消
function test_noPayOrdersCancalSynchronization() {
    $Reachlife = new Reachlife();
    $param = array(
        "serialNumber" => "E0021201609010924301013863",
        "orderNumber" => "4030"
    );
    $result = $Reachlife->curl("noPayOrdersCancalSynchronization", $param);
    return $result;
}
//已支付订单同步接口
function test_checkTransactionCompletion() {
    $Reachlife = new Reachlife();
    $products = array();
    $product1 = array(
        "code" => "110101",
        "goods" => "更换马桶按钮",
        "number" => "1.00",
        "amount" => "46.00",
        "category" => "1A0002",
        "price" => "50.00",
        "tax" => "6"
    );
    array_push($products, $product1);
    $param = array(
        "cardNumber" => "13917717166",
        "amount" => "46.00",
        "orderNumber" => "4103",
        "serialNumber" => "E0021201609081144188813936",
        "orderDate" => "2016-09-08 11:44:18",
        "price" => "50.00",
        "orderDetail" => json_encode($products),
        "state" => 1,
        "type" => 0
    );
    $result = $Reachlife->curl("checkTransactionCompletion", $param);
    return $result;
}

//已支付订单退款
function test_checkRefundIntegralAuthorization() {
    $Reachlife = new Reachlife();
    $products = array();
    $product1 = array(
        "code" => "110101",
        "goods" => "更换马桶按钮",
        "number" => "1.00",
        "amount" => "46.00",
        "category" => "1A0002",
        "price" => "50.00",
        "tax" => "6"
    );
    array_push($products, $product1);
    $param = array(
        "cardNumber" => "13917717166",
        "integral" => "46.00",
        "orderNumber" => "4103",
        "serialNumber" => "E0021201609081144188813936",
        "orderDate" => "2016-09-08 11:44:18",
        "orderDetail" => json_encode($products),
        "type" => 0
    );
    $result = $Reachlife->curl("checkRefundIntegralAuthorization", $param);
    return $result;
}