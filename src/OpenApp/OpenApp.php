<?php
/**
 * User: qbhy
 * Date: 2019-01-20
 * Time: 12:23
 */

namespace Qbhy\EasyAlipay\OpenApp;

use Qbhy\EasyAlipay\Exceptions\EasyAlipayException;
use Qbhy\EasyAlipay\Kernel\AbstractModule;
use Qbhy\EasyAlipay\OpenApp\Requests\OpenAppQrCodeCreate;

/**
 * Class OpenApp
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\OpenApp
 */
class OpenApp extends AbstractModule
{
    /**
     * @param string $urlParam   小程序中能访问到的页面路径
     * @param string $queryParam 小程序的启动参数，打开小程序的query，在小程序onLaunch的方法中获取
     * @param string $describe   对应的二维码描述
     *
     * @return array
     * @throws EasyAlipayException
     */
    public function createQrCode(string $urlParam, string $queryParam, string $describe)
    {
        return $this->client()->execute(new OpenAppQrCodeCreate($urlParam, $queryParam, $describe));
    }
}