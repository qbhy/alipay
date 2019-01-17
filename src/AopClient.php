<?php
/**
 * User: qbhy
 * Date: 2019/1/9
 * Time: 下午2:18
 */

namespace Qbhy\EasyAlipay;

use GuzzleHttp\RequestOptions;
use Hanson\Foundation\AbstractAPI;
use Qbhy\EasyAlipay\Exceptions\BizContentException;
use Qbhy\EasyAlipay\Exceptions\CharsetException;
use Qbhy\EasyAlipay\Exceptions\LackConfigOptionException;
use Qbhy\EasyAlipay\Exceptions\RequestException;
use Qbhy\EasyAlipay\Exceptions\RsaPrivateKeyException;
use Qbhy\EasyAlipay\Exceptions\RsaPublicKeyException;
use Qbhy\EasyAlipay\Exceptions\SignException;
use Qbhy\EasyAlipay\Kernel\EncryptParseItem;
use Qbhy\EasyAlipay\Kernel\Contracts\Request;
use Qbhy\EasyAlipay\Kernel\SignData;

class AopClient extends AbstractAPI
{
    /** @var Alipay */
    protected $app;

    /** @var string 接口字符集 */
    protected $postCharset = 'UTF-8';

    /** @var string 文件字符集 */
    protected $fileCharset = 'UTF-8';

    /** @var string API版本 */
    protected $version = '1.0';

    /** @var string 签名类型 */
    protected $signType = 'RSA2';

    /** @var string sdk 版本 */
    protected $alipaySdkVersion = 'alipay-sdk-php-20180705';

    /** @var string 网关 */
    protected $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    /** @var string 加密密钥 */
    protected $encryptKey;

    /** @var string 加密类型 */
    public $encryptType = 'AES';

    /** @var string 格式化统一用json */
    const FORMAT = 'json';

    private $RESPONSE_SUFFIX = "_response";

    private $ERROR_RESPONSE = "error_response";

    private $SIGN_NODE_NAME = "sign";

    /**
     * ApiClient constructor.
     *
     * @param Alipay $alipay
     */
    public function __construct(Alipay $alipay)
    {
        $this->app = $alipay;
    }

    public function getApp(): Alipay
    {
        return $this->app;
    }

    /**
     * 获取 aes 密钥
     *
     * @return string
     */
    public function getEncryptKey()
    {
        if ($this->encryptKey === null) {
            $this->encryptKey = $this->getApp()->getConfig('aes_key');
        }
        return $this->encryptKey;
    }

    /**
     * @param Request $request
     * @param null    $authToken
     * @param null    $appInfoAuthToken
     *
     * @return array
     * @throws BizContentException
     * @throws CharsetException
     * @throws LackConfigOptionException
     * @throws RsaPrivateKeyException
     * @throws RsaPublicKeyException
     * @throws SignException
     */
    public function execute(Request $request, $authToken = null, $appInfoAuthToken = null)
    {
        $app = $this->getApp();

        //  如果两者编码不一致，会出现签名验签或者乱码
        if (strcasecmp($this->fileCharset, $this->postCharset)) {
            // writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
            throw new CharsetException("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
        }

        //组装系统参数
        $sysParams["app_id"]    = $app->getConfig('app_id');
        $sysParams["method"]    = $request->getApiName();
        $sysParams["format"]    = AopClient::FORMAT;
        $sysParams["charset"]   = $this->postCharset;
        $sysParams["sign_type"] = $this->signType;
        // sign
        $sysParams["timestamp"]      = date("Y-m-d H:i:s");
        $sysParams["version"]        = $request->getVersion($this->version);
        $sysParams["auth_token"]     = $authToken;
        $sysParams["app_auth_token"] = $appInfoAuthToken;

        //获取业务参数
        $apiParams = $request->getApiParams();

        if ($request->isNeedEncrypt()) {
            $sysParams["encrypt_type"] = $this->encryptType;
            if (empty($apiParams['biz_content'])) {
                throw new BizContentException(" api request Fail! The reason : encrypt request is not supperted!");
            }

            if (empty($encryptKey = $this->getEncryptKey()) || empty($this->encryptType)) {
                throw (new LackConfigOptionException("encryptType and encryptKey must not null! "))->setName('aes_key');
            }
            // 执行加密
            $enCryptContent           = aop_encrypt($apiParams['biz_content'], $encryptKey);
            $apiParams['biz_content'] = $enCryptContent;
        }

        //签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams), $this->signType);

        $requestUrl = $this->gatewayUrl . '?' . http_build_query($sysParams);

        //发起HTTP请求
        $resp = $this->getHttp()->request('POST', $requestUrl, [RequestOptions::FORM_PARAMS => $apiParams])->getBody()->__toString();

        // 将返回结果转换本地文件编码
        $r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);

        $signData = null;
        $respArr  = @json_decode($r, true);

        if (null !== $respArr) {
            $signData = $this->parserJSONSignData($request, $resp, $respArr);
        }

        // 验签
        $this->checkResponseSign($request, $signData, $respArr);

        // 解密
        if ($request->isNeedEncrypt()) {
            $resp = $this->encryptJSONSignSource($request, $resp);
            // 将返回结果转换本地文件编码
            $r       = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
            $respArr = @json_decode($r, true);
        }

        if ($respArr) {
            if (isset($respArr[$this->getResponseNodeName($request)])) {
                return $respArr[$this->getResponseNodeName($request)];
            }

            if (isset($respArr[$this->ERROR_RESPONSE])) {
                $response = $respArr[$this->ERROR_RESPONSE];
                throw new RequestException($response['msg'], $response);
            }

            throw new RequestException('request exception!', $respArr);
        }

        throw new RequestException('request exception!', $resp);
    }

    /**
     * @param array  $params
     * @param string $signType
     *
     * @return string
     * @throws RsaPrivateKeyException
     */
    public function generateSign($params, $signType = 'RSA2')
    {
        return $this->getApp()->aop_signer->sign($this->getSignContent($params), $signType);
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function getSignContent($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i                = 0;
        foreach ($params as $k => $v) {
            if (false === empty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->charset($v, $this->postCharset);

                if ($i == 0) {
                    $stringToBeSigned .= "{$k}={$v}";
                } else {
                    $stringToBeSigned .= "&" . "{$k}={$v}";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     *
     * @param $data
     * @param $targetCharset
     *
     * @return string
     */
    public function charset($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }

        return $data;
    }

    /**
     * 此方法对value做urlencode
     *
     * @param $params
     *
     * @return string
     */
    public function getSignContentUrlencode($params)
    {
        ksort($params);

        $stringToBeSigned = "";
        $i                = 0;
        foreach ($params as $k => $v) {
            if (false === empty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->charset($v, $this->postCharset);

                if ($i == 0) {
                    $stringToBeSigned .= "{$k}=" . urlencode($v);
                } else {
                    $stringToBeSigned .= "&" . "{$k}=" . urlencode($v);
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 解析得到sub_code字段
     *
     * @param Request $request
     * @param         $respArr
     *
     * @return string|null
     */
    public function parserResponseSubCode(Request $request, $respArr)
    {
        $rootNodeName = $this->getResponseNodeName($request);

        if (isset($respArr[$rootNodeName])) {
            return $respArr[$rootNodeName]['sub_code'] ?? null;
        }
        return $respArr[$this->ERROR_RESPONSE]['sub_code'] ?? null;
    }

    /**
     * 获取响应节点名称
     *
     * @param Request $request
     *
     * @return string
     */
    public function getResponseNodeName(Request $request): string
    {
        $apiName = $request->getApiName();
        return str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
    }

    /**
     * @param Request $request
     * @param         $responseContent
     * @param array   $responseJSON
     *
     * @return string
     */
    function parserJSONSignSource(Request $request, $responseContent, $responseJSON)
    {
        $rootNodeName = $this->getResponseNodeName($request);
        $rootIndex    = strpos($responseContent, $rootNodeName);
        $errorIndex   = strpos($responseContent, $this->ERROR_RESPONSE);

        if (isset($responseJSON[$rootNodeName])) {
            return $this->parserJSONSource($responseContent, $rootNodeName, $rootIndex);
        } else {
            return $this->parserJSONSource($responseContent, $this->ERROR_RESPONSE, $errorIndex);
        }
    }

    /**
     * @param $responseContent
     * @param $nodeName
     * @param $nodeIndex
     *
     * @return bool|null|string
     */
    public function parserJSONSource($responseContent, $nodeName, $nodeIndex)
    {
        $signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
        $signIndex          = strrpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
        // 签名前-逗号
        $signDataEndIndex = $signIndex - 1;
        $indexLen         = $signDataEndIndex - $signDataStartIndex;
        if ($indexLen < 0) {
            return null;
        }
        return substr($responseContent, $signDataStartIndex, $indexLen);
    }

    /**
     * @param       $request
     * @param       $responseContent
     * @param array $responseJSON
     *
     * @return SignData|null
     */
    function parserJSONSignData(Request $request, $responseContent, $responseJSON)
    {
        $signData = null;
        if (isset($responseJSON['sign'])) {
            $signData                 = new SignData();
            $signData->sign           = $responseJSON['sign'] ?? null;
            $signData->signSourceData = $this->parserJSONSignSource($request, $responseContent, $responseJSON);
        }
        return $signData;
    }


    /**
     * @param Request       $request
     * @param SignData|null $signData
     * @param               $respArr
     *
     * @throws RsaPublicKeyException
     * @throws SignException
     */
    public function checkResponseSign(Request $request, $signData, $respArr)
    {
        if (!is_null($signData)) {
            if (empty($signData->sign) || empty($signData->signSourceData)) {
                throw (new SignException(" check sign Fail! The reason : signData is Empty"));
            }

            // 获取结果sub_code
            $responseSubCode = $this->parserResponseSubCode($request, $respArr);

            if (!empty($responseSubCode) || (empty($responseSubCode) && !empty($signData->sign))) {
                $checkResult = $this->getApp()->aop_signer->verify($signData->signSourceData, $signData->sign, $this->signType);
                if (!$checkResult) {
                    if (strpos($signData->signSourceData, "\\/") > 0) {
                        $signData->signSourceData = str_replace("\\/", "/", $signData->signSourceData);
                        $checkResult              = $this->getApp()->aop_signer->verify($signData->signSourceData, $signData->sign, $this->signType);
                        if (!$checkResult) {
                            throw new SignException("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
                        }
                    } else {
                        throw new SignException("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
                    }
                }
            }
        }
    }

    /**
     * @param $request
     * @param $responseContent
     *
     * @return string
     */
    private function encryptJSONSignSource(Request $request, $responseContent)
    {
        $parseItem = $this->parserEncryptJSONSignSource($request, $responseContent);

        $bodyIndexContent = substr($responseContent, 0, $parseItem->startIndex);
        $bodyEndContent   = substr($responseContent, $parseItem->endIndex, strlen($responseContent) + 1 - $parseItem->endIndex);

        $bizContent = aop_decrypt($parseItem->encryptContent, $this->encryptKey);
        return $bodyIndexContent . $bizContent . $bodyEndContent;
    }

    /**
     * @param Request $request
     * @param         $responseContent
     *
     * @return null|EncryptParseItem
     */
    private function parserEncryptJSONSignSource(Request $request, $responseContent)
    {
        $rootNodeName = $this->getResponseNodeName($request);

        $rootIndex  = strpos($responseContent, $rootNodeName);
        $errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);

        if ($rootIndex > 0) {
            return $this->parserEncryptJSONItem($responseContent, $rootNodeName, $rootIndex);
        } else if ($errorIndex > 0) {
            return $this->parserEncryptJSONItem($responseContent, $this->ERROR_RESPONSE, $errorIndex);
        } else {
            return null;
        }
    }

    /**
     * @param $responseContent
     * @param $nodeName
     * @param $nodeIndex
     *
     * @return EncryptParseItem
     */
    private function parserEncryptJSONItem($responseContent, $nodeName, $nodeIndex): EncryptParseItem
    {
        $signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
        $signIndex          = strpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
        // 签名前-逗号
        $signDataEndIndex = $signIndex - 1;

        if ($signDataEndIndex < 0) {
            $signDataEndIndex = strlen($responseContent) - 1;
        }

        $indexLen   = $signDataEndIndex - $signDataStartIndex;
        $encContent = substr($responseContent, $signDataStartIndex + 1, $indexLen - 2);

        $encryptParseItem                 = new EncryptParseItem();
        $encryptParseItem->encryptContent = $encContent;
        $encryptParseItem->startIndex     = $signDataStartIndex;
        $encryptParseItem->endIndex       = $signDataEndIndex;

        return $encryptParseItem;

    }

}