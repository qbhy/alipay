<?php
/**
 * 资金API
 *
 * @link https://docs.open.alipay.com/api_28
 * User: qbhy
 * Date: 2019-01-20
 * Time: 11:01
 */

namespace Qbhy\EasyAlipay\Fund;

use Qbhy\EasyAlipay\Exceptions\EasyAlipayException;
use Qbhy\EasyAlipay\Fund\Requests\FundTransToAccountTransfer;
use Qbhy\EasyAlipay\Kernel\AbstractModule;

/**
 * Class Fund
 *
 * @author qbhy <96qbhy@gmail.com>
 * @package Qbhy\EasyAlipay\Fund
 */
class Fund extends AbstractModule
{
    /**
     * @param string $outBizNo     商户转账唯一订单号。发起转账来源方定义的转账单据ID，用于将转账回执通知给来源方。
     * @param string $payeeType    收款方账户类型。可取值：1、ALIPAY_USERID：支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。2、ALIPAY_LOGONID：支付宝登录号，支持邮箱和手机号格式。
     * @param string $payeeAccount 收款方账户。与payee_type配合使用。付款方和收款方不能是同一个账户。
     * @param int    $amount       金额,单位分，最大转账金额以实际签约的限额为准。
     * @param array  $optional     可选参数,包含 payer_show_name:付款方姓名。payee_real_name:收款方真实姓名。 remark:转账备注。
     *
     * @return array
     * @throws EasyAlipayException
     */
    public function transfer(string $outBizNo, string $payeeType, string $payeeAccount, int $amount, array $optional = [])
    {
        return $this->client()->execute(new FundTransToAccountTransfer($outBizNo, $payeeType, $payeeAccount, $amount, $optional));
    }
}