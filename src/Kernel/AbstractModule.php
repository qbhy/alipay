<?php
/**
 * User: qbhy
 * Date: 2019/1/11
 * Time: 下午3:37
 */

namespace Qbhy\EasyAlipay\Kernel;

use Qbhy\EasyAlipay\Alipay;
use Qbhy\EasyAlipay\AopClient;

/**
 * Class AbstractModule
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\Kernel
 */
abstract class AbstractModule
{
    protected $app;

    public function __construct(Alipay $alipay)
    {
        $this->app = $alipay;
    }

    protected function client(): AopClient
    {
        return $this->app->aop_client;
    }

    /**
     * @return Alipay
     */
    public function getApp(): Alipay
    {
        return $this->app;
    }


}