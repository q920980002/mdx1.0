<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/21
 * Time: 下午3:36
 */

namespace common\service;

class CheckService
{

    /**
     * 验证身份证号是否正确
     * @param $id_card
     * @return bool
     */
    public function validation_filter_id_card($id_card)
    {
        if (strlen($id_card) == 18) {
            return $this->_idcard_checksum18($id_card);
        } elseif ((strlen($id_card) == 15)) {
            $id_card = $this->_idcard_15to18($id_card);
            return $this->_idcard_checksum18($id_card);

        } else {
            return false;
        }
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    /**
     * @param $idcard_base
     * @return bool
     */
    private function _idcard_verify_number($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;


    }

    /**
     * @param $idcard
     * @return bool|string
     */
    private function _idcard_15to18($idcard)
    {
        if(strlen($idcard)!=15) {
            return false;

        }else{
            if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){

                $idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);


            }else{
                $idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);

            }
        }
        $idcard=$idcard.$this->_idcard_verify_number($idcard);
        return $idcard;


    }

    /**
     * @param $idcard
     * @return bool
     */
    private function  _idcard_checksum18($idcard){
        if(strlen($idcard)!=18){
            return false;
        }
        $idcard_base=substr($idcard,0,17);
        if($this->_idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))) {
            return false;

        }else{
            return true;
        }
    }

}