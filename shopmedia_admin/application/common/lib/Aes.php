<?php

namespace app\common\lib;

/**
 * AES加密与解密类库
 * Class Aes
 * @package app\common\lib
 */
class Aes {
    /**
     * 密钥
     * @var string
     */
    public $key = '123a456b789c123d';
    public $options = 0;

    /**
     * AES加密
     * @param string $string 需要加密的字符串
     * @param string $key 密钥
     * @return string
     */
    public function encrypt($string)
    {  
        $data = openssl_encrypt($string, 'AES-128-ECB', $this->key, $this->options);
        return $data;
    }

    /**
     * AES解密
     * @param string $string 需要解密的字符串
     * @param string $key 密钥
     * @return string
     */
    public function decrypt($string)
    {
        $decrypted = openssl_decrypt($string, 'AES-128-ECB', $this->key, $this->options);
        return $decrypted;
    }
}
