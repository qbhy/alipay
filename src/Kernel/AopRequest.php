<?php
/**
 * User: qbhy
 * Date: 2019/1/10
 * Time: 下午5:24
 */

namespace Qbhy\EasyAlipay\Kernel;

use Qbhy\EasyAlipay\Exceptions\UndefinedPropertyException;
use Qbhy\EasyAlipay\Kernel\Contracts\Request;

/**
 * Class AopRequest
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\Kernel
 */
abstract class AopRequest implements Request
{
    /**
     * @var string API版本
     */
    protected $version = '1.0';

    /**
     * @var bool 是否需要加密
     */
    protected $needEncrypt = false;

    /**
     * @var array 可选参数
     */
    protected $optionalParams = [];

    /**
     * @var array 可选参数键
     */
    protected $optionalParamKeys = [];

    public function isNeedEncrypt(): bool
    {
        return $this->needEncrypt;
    }

    public function getVersion(string $default): string
    {
        return $this->version ?: $default;
    }

    public function __get($name)
    {
        return $this->optionalParams[$name] ?? null;
    }

    public function __set($name, $value)
    {
        if (in_array($this->optionalParamKeys, $name)) {
            $this->optionalParams[$name] = $value;
        }

        throw new UndefinedPropertyException("Undefined property: {$name}");
    }

    /**
     * 是否只要参数
     * @return bool
     */
    public function onlyParams(): bool
    {
        return false;
    }
}