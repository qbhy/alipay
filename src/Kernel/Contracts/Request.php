<?php
/**
 * User: qbhy
 * Date: 2019/1/9
 * Time: 下午5:00
 */

namespace Qbhy\EasyAlipay\Kernel\Contracts;

interface Request
{
    public function getApiName(): string;

    public function isNeedEncrypt(): bool;

    public function getApiParams(): array;

    public function getVersion(string $default): string;

}