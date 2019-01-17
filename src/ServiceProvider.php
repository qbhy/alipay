<?php
/**
 * User: qbhy
 * Date: 2019/1/9
 * Time: 下午4:03
 */

namespace Qbhy\EasyAlipay;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Qbhy\EasyAlipay\Payment\Payment;
use Qbhy\EasyAlipay\Tools\Tools;
use Qbhy\EasyAlipay\User\User;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['aop_client'] = function (Alipay $alipay) {
            return new AopClient($alipay);
        };

        $pimple['aop_signer'] = function (Alipay $alipay) {
            return new AopSigner($alipay);
        };

        $pimple['user'] = function (Alipay $alipay) {
            return new User($alipay);
        };

        $pimple['tools'] = function (Alipay $alipay) {
            return new Tools($alipay);
        };

        $pimple['payment'] = function (Alipay $alipay) {
            return new Payment($alipay);
        };

    }

}