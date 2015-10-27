<?php

/*
 * 解密函数
 * 
 */

class AseAction extends Action {

    private $_secret_key = 'vy20140217';

    public function encode($data) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->_secret_key, $iv);
        $encrypted = mcrypt_generic($td, $data);
        mcrypt_generic_deinit($td);

        return $iv . $encrypted;
    }

    public function md5_Mcrypt($data) {
        $data = md5($data . $this->_secret_key);

        return $data;
    }

}
