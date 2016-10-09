<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 13:57
 */

//namespace Common\Util\Plugins\Security;


class Aes
{
    public function encrypt($data, $key){
        $key = md5($key);
        $key1 = substr($key, 8, 16);
        $privateKey = $key;
        $iv = $key1;

        $data = base64_encode($data);

        // 后端添加个数
        $length = 32;
        $count = strlen($data);
        $add = $length - ($count % $length);

        $data = $data . str_repeat("\x00", $add);

        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        $v = base64_encode($encrypted);

        return $v;
    }

    public function decrypt($data, $key){
        $key = md5($key);
        $key1 = substr($key, 8, 16);
        $privateKey = $key;
        $iv = $key1;

        $encryptedData = base64_decode($data);

        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        $decrypted = base64_decode($decrypted);

        return $decrypted;
    }
}