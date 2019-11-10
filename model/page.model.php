<?php


function Model_Page_addPage($category, $status, $admin, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $permission, $profile_pic, $reg_id, $lang, $country, $popularity){
    return DBQuery("INSERT INTO `pages` ( `category`, `status`,  `admin`, `name_1`, `name_2`, `gender`, `birthday`, `country_code`, `phone_number` ,`permission`, `join_day`, `profile_pic`, `profile_update`, `last_update`,  `reg_id`, `lang`, `country` , `popularity`) VALUES ( '$category', '$status', '$admin', '$name_1', '$name_2', '$gender', '$birthday', '$country_code', '$phone_number', '$permission', '".getTimeStamp()."', '$profile_pic', '".getTimeStamp()."', '".getTimeStamp()."', '$reg_id', '$lang', '$country', '$popularity');");


}


function Model_Page_CleanInformation($page_srl)
{
    return DBQuery("UPDATE `pages` SET `name_1` = '' , `name_2` = '' ,`phone_number` = '0' WHERE `srl` = '$page_srl' AND `status` > -1");
}


function Model_Page_setStatus($page_srl, $status)
{
    return DBQuery("UPDATE `pages` SET `status` = '$status'   WHERE `srl` = '$page_srl' AND `status` > -1");
}


function Model_Page_setRegID($page_srl, $Regid)
{
    return DBQuery("UPDATE `pages` SET `reg_id` = '$Regid'   WHERE `srl` = '$page_srl' AND `status` > -1");
}
