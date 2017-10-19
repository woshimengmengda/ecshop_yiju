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
    $result = json_decode($res, true);
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
                                echo "存在相同卡号会员，手机号不同，更新手机号成功";exit;
                            }else{
                                echo "存在相同卡号会员，手机号不同，更新手机号失败";exit;
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
                                echo "新增会员成功，更新会员卡号成功";exit;
                            }else{
                                echo "新增会员成功，更新会员卡号失败";exit;
                            }
                        }else{
                            echo "新增会员失败";exit;
                        }

                    }
                    break;
            }
        }
    }else{
        echo "<pre>";
        print_r($res);exit;
    }
}elseif($action == "sync_user"){
    $res = sync_user();
    echo "<pre>";
    print_r($res);
}
//根据会员卡号获取会员信息
function get_user_by_cardnumber($cardnumber){
    $user = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE cardnumber = '$cardnumber'");
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