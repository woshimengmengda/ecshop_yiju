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
$action = $_GET['action'];

if($action == 'page_sync_user'){#会员同步接口 定时任务去跑
//    $username = '12228883333';
//    $password = '123456';
//    $email = '';
//    $res = $GLOBALS['user']->add_user($username, $password, $email);
//    var_dump($res);exit;
//    $params = array(
//      'user_id' => '7',
//      'mobile' => '18855590009'
//    );
//    $res = update_user_name($params);
//    var_dump($res);exit;
//    $res = delete_user('6');
//    var_dump($res);exit;
//    $user = get_user_by_cardnumber('46694100211');
//    echo "<pre>";
//    print_r($user);exit;
    $param = array(
        'modifyDateTime' => date("Y-m-d H:i:s", time()-180),
        'page' => 1
    );
    $res = page_sync_user($param);
//    $result = json_decode($res, true);
    //同步成功返回数据
    if($result['code'] == '0'){
        foreach ((array)$result['DateDetail'] as $mk => $mv){
            switch ($mv['type']){
                //离职员工 删除
                case '3':
                    $user = get_user_by_cardnumber($mv['cardNumber']);
                    //存在离职员工则删除 根据兜礼文档说明进行此操作
                    if(!empty($user)){
                        delete_user($user['user_id']);
                    }
                    break;
                //0：员工1：家属2：退休
                case '0':
                case '1':
                case '2':
                    $user = get_user_by_cardnumber($mv['cardNumber']);
                    //存在但是手机号不相同则更新手机号
                    if(!empty($user)){
                        if($user['user_name'] != $mv['telephone']){
                            $params['user_id'] = $user['user_id'];
                            $params['mobile'] = $mv['telephone'];
                            if(update_user_name($params)){
                                echo "存在相同卡号会员，手机号不同，更新手机号成功";
                            }else{
                                echo "存在相同卡号会员，手机号不同，更新手机号失败";
                            }
                        }
                    }else{//不存在则添加
                        $username = $mv['telephone'];
                        $password = $mv['cardNumber'];
                        $email = '';
                        $res = $GLOBALS['user']->add_user($username, $password, $email);
                        //更新新增会员卡号
                        if($res){
                            $params['cardnumber'] = $mv['cardNumber'];
                            $params['user_name'] = $mv['telephone'];
                            if(update_user_cardnumber($params)){
                                echo "新增会员成功，更新会员卡号成功";
                            }else{
                                echo "新增会员成功，更新会员卡号失败";
                            }
                        }else{
                            echo "新增会员失败";
                        }

                    }
                    break;
            }
        }
    }
//    error_log("\n 兜礼会员同步接口params \n".var_export($param, 1)."\n 结果res \n".var_export($res, 1)."\n", 3, ROOT_PATH."elog.log");
}elseif($action == 'dooly_login'){
    //兜礼一号通登录接口 by xiaoq 2017-10-22
    $shopId = 'shopId';
    $shopKey = 'shopKey';
    $getParams = $_GET;
    $now_time = time();
    $targetUrl = urldecode($getParams['targetUrl']);
    if(empty($targetUrl)){
        $targetUrl = "http:\/\/".$_SERVER['HTTP_HOST']."/user.php";
    }
//    var_dump($targetUrl);exit;
//    $targetUrl = "http://dev.shop.net/user.php";
//    $getParams = array(
//        'cardNumber' => '46694100211',
//        'mobile' => '18717752290',
//    );
    $str = md5("shopId=".$shopId."&shopKey=".$shopKey."&mobile=".$getParams['mobile']."&cardNumber=".$getParams['cardNumber']."&actionTime=".$getParams['actionTime']);
    if($str == $getParams['sign']){
        //存在会员时
        if($user = get_user_by_cardnumber($getParams['cardNumber'])){
//            var_dump($user);exit;
            //会员手机号没变
            if($user['user_name'] == $getParams['mobile']){
                if($now_time-$getParams['actionTime']>180){
                    //超时 不带登录状态跳转
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }else{
                    //未超时 带登录状态跳转
                    require_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
                    $integrate = new integrate();
                    $integrate->set_session($user['user_name']);
                    $integrate->set_cookie($user['user_name']);
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }
            }else{//会员手机号变了
                if($now_time-$getParams['actionTime']>180){
                    if(!empty($getParams['mobile'])){
                        $params['user_id'] = $user['user_id'];
                        $params['mobile'] = $getParams['mobile'];
                        update_user_name($params);
                    }
                    //超时 不带登录状态跳转
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }else{
                    if(!empty($getParams['mobile'])){
                        $params['user_id'] = $user['user_id'];
                        $params['mobile'] = $getParams['mobile'];
                        update_user_name($params);
                    }
                    //未超时 带登录状态跳转
                    require_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
                    $integrate = new integrate();
                    $integrate->set_session($getParams['mobile']);
                    $integrate->set_cookie($getParams['mobile']);
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }
            }
        }else{//不存在用户
            //存在相同手机号 更新卡号
            if($user = get_user_by_mobile($getParams['mobile'])){
                if($now_time-$getParams['actionTime']>180){
                    $params['cardnumber'] = $getParams['cardNumber'];
                    $params['user_name'] = $getParams['mobile'];
                    update_user_cardnumber($params);
                    //超时 不带登录状态跳转
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }else{
                    $params['cardnumber'] = $getParams['cardNumber'];
                    $params['user_name'] = $getParams['mobile'];
                    update_user_cardnumber($params);
                    //未超时 带登录状态跳转
                    require_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
                    $integrate = new integrate();
                    $integrate->set_session($getParams['mobile']);
                    $integrate->set_cookie($getParams['mobile']);
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }
            }else{//新增用户
                if($now_time-$getParams['actionTime']>180){
                    $username = $getParams['mobile'];
                    $password = $getParams['cardNumber'];
                    $email = '';
                    $res = $GLOBALS['user']->add_user($username, $password, $email);
                    if($res){
                        $params['cardnumber'] = $getParams['cardNumber'];
                        $params['user_name'] = $getParams['mobile'];
                        update_user_cardnumber($params);
                    }
                    //超时 不带登录状态跳转
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }else{
                    $username = $getParams['mobile'];
                    $password = $getParams['cardNumber'];
                    $email = '';
                    $res = $GLOBALS['user']->add_user($username, $password, $email);
                    if($res){
                        $params['cardnumber'] = $getParams['cardNumber'];
                        $params['user_name'] = $getParams['mobile'];
                        update_user_cardnumber($params);
                    }
                    //未超时 带登录状态跳转
                    require_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
                    $integrate = new integrate();
                    $integrate->set_session($getParams['mobile']);
                    $integrate->set_cookie($getParams['mobile']);
                    ecs_header("Location: $targetUrl\n");
                    exit;
                }
            }
        }
    }else{
        ecs_header("Location: $targetUrl\n");
        exit;
    }
}
//根据会员手机号获取会员信息
function get_user_by_mobile($mobile){
    $user = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_name = '{$mobile}'");
    return $user;
}
//根据会员卡号获取会员信息
function get_user_by_cardnumber($cardnumber){
    $user = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE cardnumber = '{$cardnumber}'");
    return $user;
}
//新增会员更新会员卡号
function update_user_cardnumber($params){
    $user = $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('users') . "set cardnumber= '{$params['cardnumber']}'  WHERE user_name = '{$params['user_name']}'");
    return $user;
}
//更新会员用户名为手机号
function update_user_name($params){
    $user = $GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('users') . "set user_name= '{$params['mobile']}'  WHERE user_id = '{$params['user_id']}'");
    return $user;
}
//根据会员ID删除该会员
function delete_user($user_id){
    $res = $GLOBALS['db']->query("delete FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$user_id'");
    return $res;
}
//分页会员数据同步
function page_sync_user($param) {
    $Reachlife = new Reachlife();
//    $param = array(
//        "modifyDateTime" => "2016-08-01 00:00:00",
//        "page" => "1"
//    );
    $result = $Reachlife->curl("memberDataSynchronizationByPage", $param);
    return $result;
}

//不分页会员数据同步
function sync_user() {
    $Reachlife = new Reachlife();
    $param = array(
        "modifyDateTime" => "2016-08-01 00:00:00",
        "cardNumber" => "55587220034",
        "type" => 1,
    );
    $result = $Reachlife->curl("memberDataSynchronization", $param);
    return $result;
}