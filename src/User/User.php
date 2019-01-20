<?php
/**
 * @link https://docs.open.alipay.com/api_2
 * User: qbhy
 * Date: 2019/1/11
 * Time: 下午3:24
 */

namespace Qbhy\EasyAlipay\User;

use Qbhy\EasyAlipay\Kernel\AbstractModule;
use Qbhy\EasyAlipay\User\Requests\UserInfoShareRequest;

/**
 * Class User
 *
 * @author  qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay\User
 */
class User extends AbstractModule
{
    /**
     * @param string $accessToken
     *
     * @return array
     */
    public function userInfoShare(string $accessToken)
    {
        return $this->client()->execute(new UserInfoShareRequest(), $accessToken);
    }
}