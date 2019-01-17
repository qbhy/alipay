<?php
/**
 * ALIPAY API: alipay.trade.query request
 * User: qbhy
 * Date: 2019/1/10
 * Time: 下午5:21
 */

namespace Qbhy\EasyAlipay\Payment\Requests;

use Qbhy\EasyAlipay\Kernel\AopRequest;

class TradeQueryRequest extends AopRequest
{
    public function getApiName(): string
    {
        return 'alipay.trade.query';
    }

    public function getApiParams(): array
    {
        return [
            'biz_content' => json_encode(['out_trade_no' => uniqid()])
        ];
    }

}
