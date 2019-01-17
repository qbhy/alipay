<?php
/**
 * alipay.system.oauth.token(换取授权访问令牌)
 *
 * @link https://docs.open.alipay.com/api_9/alipay.system.oauth.token
 * User: qbhy
 * Date: 2019/1/10
 * Time: 下午6:54
 */

namespace Qbhy\EasyAlipay\Tools\Requests;

use Qbhy\EasyAlipay\Kernel\AopRequest;

class SystemOauthTokenRequest extends AopRequest
{
    /** @var string 值为authorization_code时，代表用code换取；值为refresh_token时，代表用refresh_token换取 */
    protected $grantType;

    /** @var string 授权码 */
    protected $code;

    /** @var string 刷刷新令牌 */
    protected $refreshToken;

    public function __construct(string $code, string $grantType = 'authorization_code')
    {
        if ($grantType === 'authorization_code') {
            $this->code = $code;
        } else {
            $this->refreshToken = $code;
        }
        $this->grantType = $grantType;
    }

    public function getApiName(): string
    {
        return 'alipay.system.oauth.token';
    }

    public function getApiParams(): array
    {
        return [
            'grant_type'    => $this->grantType,
            'code'          => $this->code,
            'refresh_token' => $this->refreshToken,
        ];
    }

}