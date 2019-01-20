<?php
/**
 * @link https://docs.open.alipay.com/api_9
 * User: qbhy
 * Date: 2019/1/10
 * Time: 下午6:42
 */

namespace Qbhy\EasyAlipay\Tools;

use Qbhy\EasyAlipay\Kernel\AbstractModule;
use Qbhy\EasyAlipay\Tools\Requests\SystemOauthTokenRequest;

/**
 * Class Tools
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\Tools
 */
class Tools extends AbstractModule
{
    /**
     * @param string $code
     * @param string $grantType='authorization_code'
     *
     * @return array
     */
    public function systemOauthToken(string $code, $grantType = 'authorization_code')
    {
        return $this->client()->execute(new SystemOauthTokenRequest($code, $grantType));
    }
}