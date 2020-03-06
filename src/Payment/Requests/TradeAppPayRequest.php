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
 * @property  $seller_id             卖家支付宝用户ID。
 * @property  $body                  对交易或商品的描述
 * @property  $goods_detail          订单包含的商品列表信息，json格式
 * @property  $operator_id           商户操作员编号
 * @property  $store_id              商户门店编号
 * @property  $terminal_id           商户机具终端编号
 * @property  $extend_params         业务扩展参数
 * @property  $timeout_express       该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。
 * @property  $settle_info           描述结算信息，json格式
 * @property  $logistics_detail      物流信息
 * @property  $business_params       商户传入业务信息，具体值要和支付宝约定，应用于安全，营销等参数直传场景，格式为json格式
 * @property  $receiver_address_info 收货人及地址信息
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\Payment\Requests
 */
class TradeAppPayRequest extends AopRequest
{
    /** @var array 请求参数 */
    protected $bizContent;

    protected $optionalParamKeys = [
        'body',
        'goods_detail',
        'operator_id',
        'store_id',
        'terminal_id',
        'extend_params',
        'timeout_express',
        'settle_info',
        'logistics_detail',
        'business_params',
        'receiver_address_info',
    ];

    /**
     * TradeCreateRequest constructor.
     *
     * @param string $outTradeNo 商户订单号,64个字符以内、只能包含字母、数字、下划线；需保证在商户端不重复
     * @param int $totalAmount 订单总金额，单位为分，取值范围[1,10000000000](ps: sdk 会做 / 100 处理)
     * @param string $subject 订单标题
     * @param array $optional 可选参数
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