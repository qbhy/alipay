<?php
/**
 * ALIPAY API: alipay.trade.query request
 * User: qbhy
 * Date: 2019/1/10
 * Time: 下午5:21
 */

namespace Qbhy\EasyAlipay\Payment\Requests;

use Qbhy\EasyAlipay\Kernel\AopRequest;

/**
 * Class TradeQueryRequest
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @property $out_trade_no 商户订单号
 * @property $out_trade    支付宝交易号
 * @property $org_pid      银行间联模式下有用
 *
 * @package Qbhy\EasyAlipay\Payment\Requests
 */
class TradeQueryRequest extends AopRequest
{
    protected $optionalParamKeys = ['out_trade_no', 'trade_noe', 'org_pid',];

    public function __construct(array $optional)
    {
        $this->optionalParams = $optional;
    }

    public function getApiName(): string
    {
        return 'alipay.trade.query';
    }

    public function getApiParams(): array
    {
        return [
            'biz_content' => json_encode($this->optionalParams)
        ];
    }

}
