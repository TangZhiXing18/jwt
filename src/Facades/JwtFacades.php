<?php
namespace Tangzhixing1218\Jwt;

class JwtFacades extends \Illuminate\Support\Facades\Facade
{
    /**
     * 获取组件的注册名称。
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return JWT::class;
    }
}