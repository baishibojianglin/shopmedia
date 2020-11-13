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
    public $key = 'sustockshopmedia';
    public $options = 0;

    /**
     * iv是初始化向量：超过16字节或者不足16字节都会被补足16字节或者截断到16字节。由于AES是块加密，铭文被分割成固定长度的块（一般是16字节长度），所以iv也是16字节
     * @var string
     */
    public $iv = '1234567812345678';

    /**
     * 后台管理系统AES加密
     * @param string $string 需要加密的字符串
     * @return string
     * @internal param string $key 密钥
     * @internal param string $iv 初始化向量
     */
    public function adminEncrypt($string)
    {  
        $data = openssl_encrypt($string, 'AES-128-ECB', $this->key, $this->options);
        return $data;
    }

    /**
     * 后台管理系统AES解密
     * @param string $string 需要解密的字符串
     * @return string
     * @internal param string $key 密钥
     * @internal param string $iv 初始化向量
     */
    public function adminDecrypt($string)
    {
        $decrypted = openssl_decrypt($string, 'AES-128-ECB', $this->key, $this->options);
        return $decrypted;
    }


    /**
     * 客户端AES加密
     * @param string $string 需要加密的字符串
     * @return string
     * @internal param string $key 密钥
     * @internal param string $iv 初始化向量
     */
    public function encrypt($string)
    {
        $data = openssl_encrypt($string, 'AES-256-CBC', $key = config('app.aeskey'), $this->options, $this->iv);
        return $data;
    }

    /**
     * 客户端AES解密
     * @param string $string 需要解密的字符串
     * @return string
     * @internal param string $key 密钥
     * @internal param string $iv 初始化向量
     */
    public function decrypt($string)
    {
        $decrypted = openssl_decrypt($string, 'AES-256-CBC', $key = config('app.aeskey'), $this->options, $this->iv);
        return $decrypted;
    }
}