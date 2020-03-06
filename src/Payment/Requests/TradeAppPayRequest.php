<?php
/**
 * alipay.trade.app.pay(app支付接口2.0)
 *
 * @link https://docs.open.alipay.com/api_1/alipay.trade.create/
 * User: qbhy
 * Date: 2020/03/04
 * Time: 下午4:08
 */

namespace Qbhy\EasyAlipay\Payment\Requests;

use Qbhy\EasyAlipay\Kernel\AopRequest;

/**
 * Class TradeAppPayRequest
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\Payment\Requests
 */
class TradeAppPayRequest extends AopRequest
{
    /** @var array 请求参数 */
    protected $bizContent;

    /**
     * 具体参数文档 https://docs.open.alipay.com/api_1/alipay.trade.app.pay#yjbzz
     * @var array
     */
    protected $optionalParamKeys = [
        'notify_url',
        'timeout_express',
        'product_code',
        'body',
        'time_expire',
        'goods_type',
        'passback_params',
        'extend_params',
        'merchant_order_no',
        'enable_pay_channels',
        'store_id',
        'specified_channel',
        'disable_pay_channels',
        'goods_detail',
        'ext_user_info',
        'business_params',
        'agreement_sign_params',
    ];

    /**
     * TradeCreateRequest constructor.
     *
     * @param string $outTradeNo 商户订单号,64个字符以内、只能包含字母、数字、下划线；需保证在商户端不重复
     * @param int $totalAmount 订单总金额，单位为分，取值范围[1,10000000000](ps: sdk 会做 / 100 处理)
     * @param string $subject 订单标题
     * @param array $optional 可选参数,https://docs.open.alipay.com/api_1/alipay.trade.app.pay#yjbzz
     */
    public function __construct(string $outTradeNo, int $totalAmount, string $subject, array $optional = [])
    {
        $this->bizContent = [
            'out_trade_no' => $outTradeNo,
            'total_amount' => $totalAmount / 100,
            'subject' => $subject,
        ];

        $this->optionalParams = $optional;
    }

    public function getApiName(): string
    {
        return 'alipay.trade.app.pay';
    }

    public function onlyParams(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getApiParams(): array
    {
        return ['biz_content' => json_encode(array_merge($this->bizContent, $this->optionalParams))];
    }

}