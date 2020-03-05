<?php
/**
 * User: qbhy
 * Date: 2019/1/9
 * Time: 下午2:27
 */

namespace Qbhy\EasyAlipay;

use Hanson\Foundation\Foundation;
use Qbhy\EasyAlipay\Fund\Fund;
use Qbhy\EasyAlipay\OpenApp\OpenApp;
use Qbhy\EasyAlipay\Payment\Payment;
use Qbhy\EasyAlipay\Tools\Tools;
use Qbhy\EasyAlipay\User\User;

/**
 * Class Alipay
 *
 * @property AopClient $aop_client
 * @property AopSigner $aop_signer
 * @property User $user
 * @property Tools $tools
 * @property Payment $payment
 * @property Fund $fund
 * @property OpenApp $open_app
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

    public function getAesKey()
    {
        return $this->getConfig('aes_key');
    }

    public function getAppId()
    {
        return $this->getConfig('app_id');
    }

    public function getRsaPrivateKey()
    {
        return $this->getConfig('rsa_private_key');
    }

    public function getRsaPublicKey()
    {
        return $this->getConfig('rsa_public_key');
    }

}