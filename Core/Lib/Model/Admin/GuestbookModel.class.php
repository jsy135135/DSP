<?php

class GuestbookModel extends Model {
    public function getInfoByPhone($Phone) {
        $Guestbook = M("guestbook");
        if (isset($Phone) && !empty($Phone)) {
            $data = $Guestbook->where("phone = '".$Phone."'")->select();
        }else{
            $data =  'Phonenuber is error';
        }
        return $data;
    }

}
