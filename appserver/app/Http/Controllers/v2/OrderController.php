<?php
//
namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\v2\Order;
use App\Models\v2\Payment;
use App\Models\v2\Features;
class OrderController extends Controller
{
    /**
     * POST /ecapi.order.list
     */
    public function index()
    {
        $rules = [
            'page'            => 'required|integer|min:1',
            'per_page'        => 'required|integer|min:1',
            'status'          => 'integer|min:0',
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $data = Order::getList($this->validated);
        return $this->json($data);
    }

    /**
     * POST /ecapi.order.get
     */
    public function view()
    {
        $rules = [
            'order' => 'required|integer|min:1',
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $data = Order::getInfo($this->validated);
        return $this->json($data);
    }

    /**
     * POST /ecapi.order.confirm
     */
    public function confirm()
    {
        $rules = [
            'order' => 'required|integer|min:1',
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $data = Order::confirm($this->validated);
        return $this->json($data);
    }

    /**
     * POST /ecapi.order.cancel
     */
    public function cancel()
    {
        $rules = [
            'order'  => 'required|integer|min:1',
            'reason' => 'required|integer|min:1',
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $data = Order::cancel($this->validated);
        return $this->json($data);
    }

    /**
     * POST /ecapi.order.price
     */
    public function price()
    {
        $rules = [
            "shop"             => "integer|min:1",          // 店铺ID
            "consignee"        => "integer|min:1",          // 收货人ID
            "shipping"         => "integer|min:1",          // 快递ID
            "coupon"           => "string|min:1",           // 优惠券ID
            "cashgift"         => "string|min:1",           // 红包ID
            "score"            => "integer",                // 积分
            "order_product"    => "required|string",        // 商品id数组
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $data = Order::price($this->validated);
        return $this->json($data);
    }

    /**
     * POST /ecapi.order.review
     */
    public function review()
    {
        $rules = [
            'order' => 'required|integer|min:1',
            'review' => 'required|json',
            'is_anonymous' => 'required|integer|in:0,1'
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $items = json_decode($this->validated['review'], true);

        $items_rules = [
            '*.goods'   => 'required|integer|min:1',
            '*.grade'   => 'required|integer|in:1,2,3',
            '*.content' => 'string'
        ];

        if ($error = $this->customValidate($items, $items_rules)) {
            return $error;
        }

        $data = Order::review($this->validated, $items);
        return $this->json($data);
    }

    /**
     * POST /ecapi.payment.types.list
     */
    public function paymentList()
    {
        $rules = [
            'shop' => 'integer|min:1',
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $data = Payment::getList($this->validated);
        return $this->json($data);
    }

    /**
     * POST /ecapi.payment.pay
     */
    public function pay()
    {
        $rules = [
            'order' => 'required|integer|min:1',
            'code' => 'required|string|in:alipay.app,wxpay.app,unionpay.app,cod.app,wxpay.web,teegon.wap,alipay.wap,wxpay.wxa,balance',            
            'openid' => 'required_if:code,wxpay.web|string',
            'channel' => 'string',
            'referer' => 'string',
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        extract($this->validated);
        $payarr = ['alipay.app' => 'pay.alipay', 'wxpay.app' => 'pay.weixin', 'unionpay.app' => 'pay.unionpay', 'cod.app' => 'pay.cod','wxpay.web' => 'pay.wxweb','teegon.wap' => 'pay.teegon','alipay.wap' => 'pay.alipaywap','wxpay.wxa'=>'pay.wxa','balance'=>'balance'];

        // if($res = Features::check($payarr[$code]))
        // {
        //     return $this->json($res);
        // }

        $data = Payment::pay($this->validated);
        return $this->json($data);
    }

    /**
     * POST /order/notify/:code
     */
    public function notify($code)
    {
        Payment::notify($code);
    }

    /**
     * POST /ecapi.order.reason.list
     */
    public function reasonList()
    {
        return $this->json(Order::getReasonList());
    }

    /**
     * POST /ecapi.order.subtotal
     */
    public function subtotal()
    {
        return $this->json(Order::subtotal());
    }
    /**
     * POST /ecapi.payment.getcode
     */
    public function getCode()
    {
        $rules = [
            'order' => 'required|string',
            'code' => 'required|string|in:doolypay.wap'
        ];
        if($error = $this->validateInput($rules)){
            return $error;
        }
//        $params = $this->validated;
//        var_dump($params);exit;
        $data = Order::getInfoById($this->validated);
//        var_dump($data);exit;
        foreach($data[0]['goods'] as $r => $d){
            $o_detail[$r]['code'] = $d['goods_sn'];
            $o_detail[$r]['amount'] = $d['goods_price'];
            $o_detail[$r]['category'] = '0000';
            $o_detail[$r]['price'] = $d['goods_price'];
            $o_detail[$r]['tax'] = '0';
            $o_detail[$r]['goods'] = $d['goods_name'];
            $o_detail[$r]['number'] = $d['goods_number'];
        }
        $d_order = array(
            "amount" => $data['order_info']['order_amount'],
            "price" => $data['order_info']['order_amount'],
            "orderDetail"=>$o_detail,
            "orderDate" => date("Y-m-d H:i:s", time()),
            "orderNumber" => $data['order_info']['order_sn'],
            "serialNumber" => $data['order_info']['order_id'],
        );
        $d_order['cardNumber'] = $data['cardnumber']?$data['cardnumber']:'';

        //调用兜礼接口
        $res = $this->curl("makePayVerificationCode", $d_order);
//        $res['code'] = '0';
        return $this->json($res);
    }
    /**
     * POST /ecapi.payment.doolyPay
     */
    public function doolyPay()
    {
        $rules = [
            'order' => 'required|string',
            'doolyCode' => 'required|string',
            'payPassword' => 'string',
        ];
        if($error = $this->validateInput($rules)){
            return $error;
        }
        $params = $this->request->all();
        $data = Order::getInfoById($this->validated);

        foreach($data[0]['goods'] as $r => $d){
            $o_detail[$r]['code'] = $d['goods_sn'];
            $o_detail[$r]['amount'] = $d['goods_price'];
            $o_detail[$r]['category'] = '0000';
            $o_detail[$r]['price'] = $d['goods_price'];
            $o_detail[$r]['tax'] = '0';
            $o_detail[$r]['goods'] = $d['goods_name'];
            $o_detail[$r]['number'] = $d['goods_number'];
        }

        $d_order = array(
            "amount" => $data['order_info']['order_amount'],
            "verificationCode" => $params['doolyCode'] ? $params['doolyCode'] : '',
            "orderNumber" => $data['order_info']['order_sn'],
            "serialNumber" => $data['order_info']['order_id'],
            "orderDate" => date("Y-m-d H:i:s", time()),
            "payPassword" => $params['doolyPassWord']?$params['doolyPassWord']:'0',
            "productDetail" => $o_detail,
        );

        $d_order['cardNumber'] = $data['cardnumber']?$data['cardnumber']:'';

        //调用兜礼接口

        $res = $this->curl("checkIntegralConsumption", $d_order);
        //支付成功 更新订单状态 by xiaoq 2017-10-25
//        $res['code'] = '0';
        if($res['code'] == '0'){
            $code = array(
                'pay_name' => 'doolypay',
                'order_sn' => $data['order_info']['order_sn'],
            );
            Payment::notify($code);
        }
        return $this->json($res);
    }


}
