<?php
/**
 * User: qbhy
 * Date: 2019/1/9
 * Time: 下午2:32
 */

/**
 * 加密方法
 *
 * @param string $str
 *
 * @return string
 */
if (!function_exists('aop_encrypt')) {
    function aop_encrypt($str, $screct_key)
    {
        //AES, 128 模式加密数据 CBC
        $screct_key  = base64_decode($screct_key);
        $str         = trim($str);
        $str         = aop_add_PKCS7_padding($str);
        $iv          = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), 1);
        $encrypt_str = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        return base64_encode($encrypt_str);
    }
}

/**
 * 解密方法
 *
 * @param string $str
 *
 * @return string
 */
if (!function_exists('aop_decrypt')) {
    function aop_decrypt($str, $screct_key)
    {
        //AES, 128 模式加密数据 CBC
        $str         = base64_decode($str);
        $screct_key  = base64_decode($screct_key);
        $iv          = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), 1);
        $encrypt_str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        $encrypt_str = trim($encrypt_str);

        $encrypt_str = aop_strip_PKSC7_padding($encrypt_str);
        return $encrypt_str;
    }
}

/**
 * 填充算法
 *
 * @param string $source
 *
 * @return string
 */
if (!function_exists('aop_add_PKCS7_padding')) {
    function aop_add_PKCS7_padding($source)
    {
        $source = trim($source);
        $block  = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char   = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }
}

/**
 * 移去填充算法
 *
 * @param string $source
 *
 * @return string
 */
if (!function_exists('aop_strip_PKSC7_padding')) {
    function aop_strip_PKSC7_padding($source)
    {
        $source = trim($source);
        $char   = substr($source, -1);
        $num    = ord($char);
        if ($num == 62) return $source;
        $source = substr($source, 0, -$num);
        return $source;
    }
}

/**
 * @param string $source
 *
 * @return string
 */
if (!function_exists('yuan2fen')) {
    function yuan2fen($yuan): int
    {
        return intval($yuan * 100);
    }
}

/**
 * @param string $source
 *
 * @return string
 */
if (!function_exists('fen2yuan')) {
    function fen2yuan($fen): float
    {
        return round($fen / 100, 2);
    }
}