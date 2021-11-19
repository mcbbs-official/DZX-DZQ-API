<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class Utils {
    private static $prikey = '';

    private static $pubkey = '';

    private static $header = array(
        'alg'=>'HS256', //生成signature的算法
        'typ'=>'JWT'    //类型
    );

    /**
     * @var mixed
     */

    public static function outPut($code, $msg = '', $data = [], $requestId = null, $requestTime = null) {
        global $_G;
        require_once libfile('function/misc');
        $arr = [
            "Code" => intval($code),
            "Msg" => $msg,
            "Data" => $data,
            "RequestId" => empty($requestId) ? Utils::create_uuid() : $requestId,
            'RequestTime' => empty($requestTime) ? date('Y-m-d H:i:s') : $requestTime
        ];
        header('content-type:application/json; charset=utf-8');
        // 去掉设置cookie
        header_remove('set-cookie');
        header('access-control-expose-headers: Authorization, authenticated');
        header('access-control-allow-credentials: true');
        header('access-control-allow-methods: GET, POST, PATCH, PUT, OPTIONS, DELETE');
        header('access-control-allow-headers: Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
        header('access-control-allow-origin: '.$_G['cache']['plugin']['zhaisoul_dzq_api']['dzq_url']);
        header('x-content-type-options: nosniff');
        header('x-frame-options: SAMEORIGIN');
        header('x-xss-protection: 1; mode=block');

        if($code != 0) {
            http_response_code(503);
        }

        exit(json_encode($arr));
    }

    /** 创建UUID  */
    public static function create_uuid($prefix=""){
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr ( $chars, 0, 8 ) . '-'
            . substr ( $chars, 8, 4 ) . '-'
            . substr ( $chars, 12, 4 ) . '-'
            . substr ( $chars, 16, 4 ) . '-'
            . substr ( $chars, 20, 12 );
        return $prefix.$uuid ;
    }

    /** 验证Token是否合法  */
    public static function verifyToken(string $Token) {
        $tokens = explode('.', $Token);
        if (count($tokens) != 3) {
            return false;
        }
        list($base64header, $base64payload, $sign) = $tokens;

        $base64decodeheader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64decodeheader['alg'])) {
            return false;
        }

        if (!self::verify($base64header.'.'.$base64payload, self::base64UrlDecode($sign))) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);

        if(TIMESTAMP > $payload['exp']) {
            return false;
        }

        return $payload;
    }

    private static function base64UrlDecode(string $input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    private static function base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private static function verify($input, $sign) {
        global $_G;
        self::$prikey = $_G['cache']['plugin']['zhaisoul_dzq_api']['pri_key'];
        self::$pubkey = $_G['cache']['plugin']['zhaisoul_dzq_api']['pub_key'];

        //解决Windows下CRLF导致key无法验证的问题
        $key = str_replace("\r\n", "\n", self::$pubkey);
        $pass = openssl_verify($input, $sign, $key, OPENSSL_ALGO_SHA256);

        //调试用，获取openssl_verify验证失败的错误原因
        $err = array();
        while($msg = openssl_error_string())
            $err[] = $msg;

        return $pass;
    }

    private static function signature($input, $key, $alg = 'HS256') {
        $key = str_replace("\r\n", "\n", $key);
        $token = '';
        openssl_sign($input, $token, $key, OPENSSL_ALGO_SHA256);
        return self::base64UrlEncode($token);
    }

    public static function getToken($payload) {
        global $_G;
        self::$prikey = $_G['cache']['plugin']['zhaisoul_dzq_api']['pri_key'];
        self::$pubkey = $_G['cache']['plugin']['zhaisoul_dzq_api']['pub_key'];

        $base64header = self::base64UrlEncode(json_encode(self::$header));
        $base64payload = self::base64UrlEncode(json_encode($payload));
        return $base64header . '.' . $base64payload . '.' . self::signature($base64header . '.' . $base64payload, self::$prikey, self::$header['alg']);
    }
}