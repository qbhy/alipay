<?php
/**
 * User: qbhy
 * Date: 2019/1/10
 * Time: 上午10:56
 */

namespace Qbhy\EasyAlipay\Exceptions;

class LackConfigOptionException extends EasyAlipayException
{
    /** @var string 缺少的配置项名字 */
    protected $name;

    /**
     * @param string $name
     *
     * @return LackConfigOptionException
     */
    public function setName(string $name): LackConfigOptionException
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}