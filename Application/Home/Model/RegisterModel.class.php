<?php
namespace Home\Model;
use Think\Model;

/**
 * Class PublicModel
 * @package Home\Model
 * User: @Andy-lizhongjian
 */
class RegisterModel extends Model {

    public function decryption($password){

        //解密
        $privateKey = "04eb029e72b446e7";

        $iv 	= "04eb029e72b446e7";

        //解密
        $encryptedData = base64_decode($password);

        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);

        $data = md5(trim($decrypted));

        return $data;
    }

    
    public function decryptionOriginal($password){

        //解密
        $privateKey = "04eb029e72b446e7";

        $iv 	= "04eb029e72b446e7";

        //解密
        $encryptedData = base64_decode($password);

        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);

        $data = trim($decrypted);

        return $data;
    }

}