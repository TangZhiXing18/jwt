<?php
namespace Tangzhixing1218\Jwt;

class JWT
{
    private static $key;
    private static $payload = [];

    public function __construct($iss,$exp_time,$key)
    {
        self::$payload = [
            'iss'=>$iss,//请求实体
            'iat'=>time(),//签发时间
            'exp'=>time()+$exp_time,//过期时间
        ];
        self::$key = $key;
    }

    public static function encode(string $user = 'test',string $alg = 'SHA256')
    {
        self::$payload['user'] = $user;
        $key = md5(self::$key);
        $jwt = self::urlsafeB64Encode(json_encode(['typ' => 'JWT', 'alg' => $alg])) . '.' . self::urlsafeB64Encode(json_encode(self::$payload));
        return $jwt . '.' . self::signature($jwt, $key, $alg);
    }

    public static function decode(string $jwt,string $user)
    {
        $tokens = explode('.', $jwt);
        $key    = md5(self::$key);

        if (count($tokens) != 3)
            return false;

        list($header64, $payload64, $sign) = $tokens;

        $header = json_decode(self::urlsafeB64Decode($header64), JSON_OBJECT_AS_ARRAY);
        if (empty($header['alg']))
            return false;

        if (self::signature($header64 . '.' . $payload64, $key, $header['alg']) !== $sign)
            return false;

        $payload = json_decode(self::urlsafeB64Decode($payload64), JSON_OBJECT_AS_ARRAY);

        $time = $_SERVER['REQUEST_TIME'];
        if (!isset($payload['iat']) || !isset($payload['exp']) || !isset($payload['iss']) || !$payload['user'])
            return false;
        if ($payload['iat'] > $time)
            return false;

        if ($payload['exp'] < $time)
            return false;
        if ($payload['iss'] != $_SERVER['HTTP_HOST'])
            return false;
        if ($payload['user'] != $user)
            return false;
        return $payload;
    }


    private static function signature(string $input, string $key, string $alg)
    {
        return hash_hmac($alg, $input, $key);
    }

    private static function urlsafeB64Decode(string $input)
    {
        $remainder = strlen($input) % 4;

        if ($remainder)
        {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }


    private static function urlsafeB64Encode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}