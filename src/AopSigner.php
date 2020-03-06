<?php
/**
 * User: qbhy
 * Date: 2019/1/16
 * Time: 下午4:33
 */

namespace Qbhy\EasyAlipay;

use Qbhy\EasyAlipay\Exceptions\NotifyException;
use Qbhy\EasyAlipay\Exceptions\RsaPrivateKeyException;
use Qbhy\EasyAlipay\Exceptions\RsaPublicKeyException;
use Qbhy\EasyAlipay\Kernel\Contracts\Signer;
use Qbhy\EasyAlipay\Kernel\Support\Arr;

/**
 * Class AopSigner
 *
 * @author qbhy <96qbhy@gmail.com>
 *
 * @package Qbhy\EasyAlipay
 */
class AopSigner implements Signer
{
    /** @var Alipay */
    protected $app;

    /** @var string rsa 私钥 */
    protected $rsaPrivateKey;

    /** @var string rsa 公钥 */
    protected $rsaPublicKey;

    public function __construct(Alipay $alipay)
    {
        $this->app = $alipay;
    }

    /**
     * 获取 rsa 私钥
     *
     * @return string|resource
     * @throws RsaPrivateKeyException
     */
    protected function getRsaPrivateKey()
    {
        if (!$this->rsaPrivateKey) {
            $key = $this->app->getRsaPrivateKey();
            if (@file_exists($key)) {
                if (!$this->rsaPrivateKey = openssl_pkey_get_private(file_get_contents($key))) {
                    throw new RsaPrivateKeyException('您使用的私钥格式错误，请检查RSA私钥配置');
                }
            } else {
                $this->rsaPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
                    wordwrap($key, 64, "\n", true) .
                    "\n-----END RSA PRIVATE KEY-----";
            }
        }
        return $this->rsaPrivateKey;
    }

    /**
     * 获取 rsa 公钥
     *
     * @return string|resource
     * @throws RsaPublicKeyException
     */
    protected function getRsaPublicKey()
    {
        if (!$this->rsaPublicKey) {
            $key = $this->app->getRsaPublicKey();
            if (@file_exists($key)) {
                if (!$this->rsaPublicKey = openssl_get_publickey(file_get_contents($key))) {
                    throw new RsaPublicKeyException('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
                }
            } else {
                $this->rsaPublicKey = "-----BEGIN PUBLIC KEY-----\n" .
                    wordwrap($key, 64, "\n", true) .
                    "\n-----END PUBLIC KEY-----";
            }
        }
        return $this->rsaPublicKey;
    }

    /**
     * 签名
     *
     * @param        $data
     * @param string $signType
     *
     * @return string
     * @throws RsaPrivateKeyException
     */
    public function sign(string $data, string $signType = "RSA2"): string
    {
        $privateKey = $this->getRsaPrivateKey();

        if ('RSA2' === $signType) {
            openssl_sign($data, $sign, $privateKey, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $privateKey);
        }

        if ($path = $this->app->getRsaPrivateKey() and file_exists($path)) {
            openssl_free_key($privateKey);
        }
        return base64_encode($sign);
    }

    /**
     * 验证签名
     *
     * @param        $data
     * @param        $sign
     * @param string $signType
     *
     * @return bool
     * @throws RsaPublicKeyException
     */
    public function verify(string $data, string $sign, string $signType = 'RSA2'): bool
    {
        $publicKey = $this->getRsaPublicKey();

        //调用openssl内置方法验签，返回bool值
        if ('RSA2' === $signType) {
            $result = (openssl_verify($data, base64_decode($sign), $publicKey, OPENSSL_ALGO_SHA256) === 1);
        } else {
            $result = (openssl_verify($data, base64_decode($sign), $publicKey) === 1);
        }

        if ($key = $this->app->getRsaPublicKey() and file_exists($key)) {
            //释放资源
            openssl_free_key($publicKey);
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return bool
     * @throws NotifyException
     * @throws RsaPublicKeyException
     */
    public function verifyNotifyParams(array $params)
    {
        if (empty($params['sign']) || empty($params['sign_type'])) {
            throw new NotifyException('notify params invalid!');
        }

        $sign = base64_decode($params['sign']);
        $signType = $params['sign_type'];

        $params = Arr::except($params, ['sign', 'sign_type']);
        ksort($params);

        return $this->verify(urldecode(http_build_query($params)), $sign, $signType);
    }
}