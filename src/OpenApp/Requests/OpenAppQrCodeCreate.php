<?php
/**
 * alipay.open.app.qrcode.create(小程序二维码)
 *
 * @link https://docs.alipay.com/mini/api/openapi-qrcode
 * User: qbhy
 * Date: 2019-01-20
 * Time: 12:24
 */

namespace Qbhy\EasyAlipay\OpenApp\Requests;

use Qbhy\EasyAlipay\Kernel\AopRequest;

class OpenAppQrCodeCreate extends AopRequest
{
    public $urlParam, $queryParam, $describe;

    /**
     * OpenAppQrCodeCreate constructor.
     *
     * @param string $urlParam   小程序中能访问到的页面路径
     * @param string $queryParam 小程序的启动参数，打开小程序的query，在小程序onLaunch的方法中获取
     * @param string $describe   对应的二维码描述
     */
    public function __construct(string $urlParam, string $queryParam, string $describe)
    {
        $this->urlParam   = $urlParam;
        $this->queryParam = $queryParam;
        $this->describe   = $describe;
    }

    public function getApiName(): string
    {
        return 'alipay.open.app.qrcode.create';
    }

    public function getApiParams(): array
    {
        return ['biz_content' => json_encode([
            'url_param'   => $this->urlParam,
            'query_param' => $this->queryParam,
            'describe'    => $this->describe,
        ])];
    }

}