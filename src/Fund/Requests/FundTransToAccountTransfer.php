<?php
/**
 * alipay.fund.trans.toaccount.transfer(单笔转账到支付宝账户接口)
 *
 * @link https://docs.open.alipay.com/api_28/alipay.fund.trans.toaccount.transfer
 * User: qbhy
 * Date: 2019-01-20
 * Time: 11:04
 */

namespace Qbhy\EasyAlipay\Fund\Requests;

use Qbhy\EasyAlipay\Kernel\AopRequest;

/**
 * Class FundTransToAccountTransfer
 *
 * @property string $payer_show_name 付款方姓名
 * @property string $payee_real_name 收款方真实姓名
 * @property string $remark          转账备注
 *
 * @author qbhy <96qbhy@gmail.com>
 * @package Qbhy\EasyAlipay\Fund\Requests
 */
class FundTransToAccountTransfer extends AopRequest
{
    public $outBizNo, $payeeType, $payeeAccount, $amount;

    protected $optionalParamKeys = [
        'payer_show_name', 'payee_real_name', 'remark',
    ];

    /**
     * FundTransToAccountTransfer constructor.
     *
     * @param string $outBizNo     商户转账唯一订单号。发起转账来源方定义的转账单据ID，用于将转账回执通知给来源方。
     * @param string $payeeType    收款方账户类型。可取值：1、ALIPAY_USERID：支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。2、ALIPAY_LOGONID：支付宝登录号，支持邮箱和手机号格式。
     * @param string $payeeAccount 收款方账户。与payee_type配合使用。付款方和收款方不能是同一个账户。
     * @param int    $amount       金额,单位分，最大转账金额以实际签约的限额为准。
     * @param array  $optional     可选参数,包含 payer_show_name:付款方姓名。payee_real_name:收款方真实姓名。 remark:转账备注。
     */
    public function __construct(string $outBizNo, string $payeeType, string $payeeAccount, int $amount, array $optional = [])
    {
        $this->outBizNo       = $outBizNo;
        $this->payeeType      = in_array($payeeType, ['ALIPAY_USERID', 'ALIPAY_LOGONID']) ? $payeeType : 'ALIPAY_USERID';
        $this->payeeAccount   = $payeeAccount;
        $this->amount         = fen2yuan($amount);
        $this->optionalParams = $optional;
    }

    public function getApiName(): string
    {
        return 'alipay.fund.trans.toaccount.transfer';
    }

    public function getApiParams(): array
    {
        return ['biz_content' => json_encode(array_merge([
            'out_biz_no'    => $this->outBizNo,
            'payee_type'    => $this->payeeType,
            'payee_account' => $this->payeeAccount,
            'amount'        => $this->amount,
        ], $this->optionalParams))];
    }

}