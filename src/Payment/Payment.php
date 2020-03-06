<?php
/**
 * @link https://docs.open.alipay.com/api_1
 * User: qbhy
 * Date: 2019/1/10
 * Time: 下午5:19
 */

namespace Qbhy\EasyAlipay\Payment;

use Qbhy\EasyAlipay\Exceptions\EasyAlipayException;
use Qbhy\EasyAlipay\Kernel\AbstractModule;
use Qbhy\EasyAlipay\Payment\Requests\TradeAppPayRequest;
use Qbhy\EasyAlipay\Payment\Requests\TradeCreateRequest;
use Qbhy\EasyAlipay\Payment\Requests\TradeQueryRequest;

/**
 * Class Payment
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\Payment
 */
class Payment extends AbstractModule
{

    /**
     * @param string $outTradeNo 商户订单号,64个字符以内、只能包含字母、数字、下划线；需保证在商户端不重复
     * @param int $totalAmount 订单总金额，单位为分，取值范围[1,10000000000](ps: sdk 会做 / 100 处理)
     * @param string $subject 订单标题
     * @param null $buyerId 买家的支付宝唯一用户号（2088开头的16位纯数字）
     * @param array $optional 可选参数
     *
     * @return array
     * @throws EasyAlipayException
     */
    public function tradeCreate(string $outTradeNo, int $totalAmount, string $subject, $buyerId = null, array $optional = [])
    {
        return $this->client()->execute(new TradeCreateRequest($outTradeNo, $totalAmount, $subject, $buyerId, $optional));
    }

    /**
     * @param array $optional
     *
     * @return array
     * @throws
     */
    public function tradeQuery(array $optional)
    {
        return $this->client()->execute(new TradeQueryRequest($optional));
    }

    /**
     * alipay.trade.app.pay(app支付接口2.0)
     * @param $outTradeNo
     * @param $totalAmount
     * @param $subject
     * @param array $options
     * @return string
     * @throws
     */
    public function tradeAppPay($outTradeNo, $totalAmount, $subject, array $options = [])
    {
        return $this->client()->sdkExecute(new TradeAppPayRequest($outTradeNo, $totalAmount, $subject, $options));
    }
}