<?php

namespace Timespay\Zfb\demo;

class Md5RSA
{

    /**
     * 利用约定数据和私钥生成数字签名
     * @param $data 待签数据
     * @return String 返回签名
     */
    public function sign($data = '')
    {
        if (empty($data)) {
            return False;
        }
        $private_key = "MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgE==";
        $private_key = chunk_split($private_key, 64, "\n");
        $private_key = "-----BEGIN PRIVATE KEY-----\n$private_key-----END PRIVATE KEY-----";

        if (empty($private_key)) {
            echo "Private Key error!";
            return False;
        }
        // 生成密钥资源id
        $private_key_resource_id = openssl_get_privatekey($private_key);
        if (empty($private_key_resource_id)) {
            echo "private key resource identifier False!";
            return False;
        }

        $verify = openssl_sign($data, $signature, $private_key_resource_id, OPENSSL_ALGO_MD5);
        openssl_free_key($private_key_resource_id);
        // Base64编码
        return base64_encode($signature);
    }

    /**
     * 利用公钥和数字签名以及约定数据验证合法性
     * @param $data 待验证数据
     * @param $signature 数字签名
     * @return -1 验证错误；0 验证失败；1 验证成功
     */
    public function isValid($data = '', $signature = '')
    {
        if (empty($data) || empty($signature)) {
            return False;
        }

        $public_key = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCB";
        $public_key = chunk_split($public_key, 64, "\n");
        $public_key = "-----BEGIN PUBLIC KEY-----\n$public_key-----END PUBLIC KEY-----";

        if (empty($public_key)) {
            echo "Public Key error!";
            return False;
        }
        // 生成密钥资源id
        $public_key_resource_id = openssl_get_publickey($public_key);
        if (empty($public_key_resource_id)) {
            echo "public key resource identifier False!";
            return False;
        }

        $signature = base64_decode($signature);
        $ret = openssl_verify($data, $signature, $public_key_resource_id, OPENSSL_ALGO_MD5);
        openssl_free_key($public_key_resource_id);
        return $ret;
    }

}
