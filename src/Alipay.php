<?php
/**
 * User: qbhy
 * Date: 2019/1/9
 * Time: 下午2:27
 */

namespace Qbhy\EasyAlipay;

use Hanson\Foundation\Foundation;
use Qbhy\EasyAlipay\Fund\Fund;
use Qbhy\EasyAlipay\Payment\Payment;
use Qbhy\EasyAlipay\Tools\Tools;
use Qbhy\EasyAlipay\User\User;

/**
 * Class Alipay
 *
 * @property AopClient $aop_client
 * @property AopSigner $aop_signer
 * @property User      $user
 * @property Tools     $tools
 * @property Payment   $payment
 * @property Fund      $fund
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay
 */
class Alipay extends Foundation
{
    protected $providers = [
        ServiceProvider::class,
    ];


}