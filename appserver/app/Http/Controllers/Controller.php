<?php
//
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Validator;
use Log;
use App\Helper\Token;
use App\Helper\XXTEA;
use App\Models\BaseModel;
use Illuminate\Pagination\Paginator;

class Controller extends BaseController
{
    //商家唯一编号
    private $businessId = 'TEST_e4d64e2629c7c7e34a9fd1b52exiuge';
    //商家门店或设备编号
    private $storesId = 'A001';
    //RESTURL
    private $restUrl = "https://pay.reach-life.com:8443/api/services/rest/";
    //证书 生成方式 openssl pkcs12 -in exiuge.p12 -out exiuge.crt.pem -clcerts -nokeys
    private $sslCrt = "exiuge.crt.pem";
    //私钥 生成方式 openssl pkcs12 -in exiuge.p12 -out exiuge.key.pem -nocerts -nodes
    private $sslKey = "exiuge.key.pem";
    //证书私钥用密码
    private $sslPassword = "qweasdzxc123456";

    public $validated;
    public $request;

    public function __construct() {
        $this->request = app('request');
    }

    /**
     * 验证输入信息
     * @param  array $rules
     * @return response
     */
    public function validateInput($rules)
    {
        $requests = $this->request->all();

        $validator = Validator::make($requests, $rules);
        if ($validator->fails()) {
            return self::json(BaseModel::formatError(BaseModel::BAD_REQUEST, $validator->messages()->first()));
        } else {
            $this->validated = array_intersect_key($requests, $rules);
            $this->validated = $requests ;
            return false;
        }
    }

    /**
     * 自定义验证
     */
    public function customValidate($requests, $rules)
    {
        $validator = Validator::make($requests, $rules);
        if ($validator->fails()) {
            return self::json(BaseModel::formatError(BaseModel::BAD_REQUEST, $validator->messages()->first()));
        } else {
            return false;
        }
    }

    /**
     * 返回Json数据
     * @param  array   $data
     * @param  array   $ext
     * @param  array   $paged
     * @return json
     */
    public function json($body = false)
    {
        //过滤null为空字符串(需协调客户端兼容)
        // if ($body) {
        //     $body = format_array($body);
        // }

        // 写入日志
        if (config('app.debug')) {

            $debug_id = uniqid();

            Log::debug($debug_id,[
                'LOG_ID'         => $debug_id,
                'IP_ADDRESS'     => $this->request->ip(),
                'REQUEST_URL'    => $this->request->fullUrl(),
                'AUTHORIZATION'  => $this->request->header('X-'.config('app.name').'-Authorization'),
                'REQUEST_METHOD' => $this->request->method(),
                'PARAMETERS'     => $this->validated,
                'RESPONSES'      => $body
            ]);

            $body['debug_id'] = $debug_id;
        }

        if (isset($body['error']) && $body['error']) {
            unset($body['error']);
            $response = response()->json($body);
            $response->header('X-'.config('app.name').'-ErrorCode', $body['error_code']);
            $response->header('X-'.config('app.name').'-ErrorDesc', urlencode($body['error_desc']));
        } else {
            $response = response()->json($body);
            $response->header('X-'.config('app.name').'-ErrorCode', 0);
        }

        if (config('token.refresh')) {
            if ($new_token = Token::refresh()) {
                // 生成新token
                $response->header('X-'.config('app.name').'-New-Authorization', $new_token);
            }
        }

        return $response;
    }

    //调用接口的Curl方法
    /**
     * 发送请求，并取得返回
     * @param string $tail 请求的子路径
     * @param string $param 需要放在post结果中的数据，不需要businessId和storesId。
     * @return mixed
     */
    public function curl($tail = '', $param = '') {

        try {
            $url = $this->restUrl . $tail;
            //echo $url . "\r";
            $param["businessId"] = $this->businessId;
            $param["storesId"] = $this->storesId;
            //echo json_encode($param)  . "\r";;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
//            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);

            //证书和私钥放在类文件同目录
            curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->sslCrt);
            curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->sslKey);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->sslPassword);
            curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $this->sslPassword);

            if(!$response = curl_exec($ch)) {
                $response = array(
                    "code" => curl_errno($ch),
                    "info" => curl_error($ch)
                );
            }
            curl_close($ch);
        } catch(Exception $e) {
            //var_dump($e);
            $response = array(
                "code" => $e->getCode(),
                "info" => $e->getMessage()
            );
        }
        return $response;
    }

}