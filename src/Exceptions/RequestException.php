<?php
/**
 * User: qbhy
 * Date: 2019/1/11
 * Time: 下午6:10
 */

namespace Qbhy\EasyAlipay\Exceptions;

use Throwable;

class RequestException extends EasyAlipayException
{
    protected $response;

    public function __construct(string $message, $response, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}