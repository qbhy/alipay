<?php
/**
 * alipay.user.info.share(支付宝会员授权信息查询接口)
 *
 * @link https://docs.open.alipay.com/api_2/alipay.user.info.share
 * User: qbhy
 * Date: 2019/1/11
 * Time: 下午3:25
 */

namespace Qbhy\EasyAlipay\User\Requests;

use Qbhy\EasyAlipay\Kernel\AopRequest;

class UserInfoShareRequest extends AopRequest
{
    public function getApiName(): string
    {
        return 'alipay.user.info.share';
    }

    public function getApiParams(): array
    {
        return [];
    }

}