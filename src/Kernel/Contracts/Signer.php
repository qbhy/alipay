<?php
/**
 * User: qbhy
 * Date: 2019/1/16
 * Time: 下午4:37
 */

namespace Qbhy\EasyAlipay\Kernel\Contracts;

/**
 * Interface Signer
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\Kernel\Contracts
 */
interface Signer
{
    public function sign(string $data, string $signType = 'RSA2'): string;

    public function verify(string $data, string $sign, string $signType = 'RSA2'): bool;
}