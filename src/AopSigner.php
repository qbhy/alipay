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
use Qbhy\EasyAlipay\Kernel\AbstractModule;
use Qbhy\EasyAlipay\Kernel\Contracts\Signer;
use Qbhy\EasyAlipay\Kernel\Support\Arr;

class AopSigner extends AbstractModule implements Signer
{
    /** @var string rsa 私钥 */
    protected $rsaPrivateKey;

    /** @var string rsa 公钥 */
    protected $rsaPublicKey;

    /**
     * 获取 rsa 私钥
     *
     * @return string|resource
     * @throws RsaPrivateKeyException
     */
    protected function getRsaPrivateKey()
    {
        if (!$this->rsaPrivateKey) {
            $key = $this->getApp()->getConfig('rsa_private_key');
            if (@file_exists($key)) {
                if (!$this->rsaPrivateKey = openssl_get_privatekey(file_get_contents($key))) {
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
            $key = $this->getApp()->getConfig('rsa_public_key');
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

        if ($path = $this->getApp()->getConfig('rsa_private_key') and file_exists($path)) {
            openssl_free_key($privateKey);
        }
        $sign = base64_encode($sign);
        return $sign;
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

        if ($key = $this->getApp()->getConfig('rsa_public_key') and file_exists($key)) {
            //释放资源
            openssl_free_key($publicKey);
        }

        return $result;
    }

    public function verifyNotifyParams(array $params)
    {
        if (empty($params['sign']) || empty($params['sign_type'])) {
            throw new NotifyException('notify params invalid!');
        }

        $sign     = base64_decode($params['sign']);
        $signType = $params['sign_type'];

        $params = Arr::except($params, ['sign', 'sign_type']);
        ksort($params);

        return $this->verify(urldecode(http_build_query($params)), $sign, $signType);
    }
}