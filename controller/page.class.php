<?php

class PageClass
{

    function PageInfoUpdate($user_srl)
    {
        $add_info_to_system = "UPDATE `pages` SET `ip_addr` = '" . gprofile_update_app;
    }

    function PageInfo($user_srl, $page_srl, $page_info)
    {

//$user_srl = AuthCheck($user_srl, false);
//Get Member Info
        $row = $this -> GetPageInfo($page_srl);
        $row = $this -> AccessPageInfo(setRelationStatus($user_srl, $page_srl), $row, $page_srl, $page_info);
       $row['page_srl'] = $page_srl;
        $row['rel_you_status'] = setRelationStatus($user_srl, $page_srl);
        $row['rel_me_status'] = setRelationStatus($page_srl, $user_srl);

        return $row;
    }

    function GetPageInfo($user_srl)
    {
        $row = mysqli_fetch_array(DBQuery("SELECT * FROM  `pages` WHERE  `srl` LIKE '$user_srl'"));
        return $row;
    }

    function AccessPageInfo($status, $row, $you_srl, $info){
        //Select status table
        $you_srl_status = mysqli_fetch_array(DBQuery("SELECT * FROM  `status` WHERE  `user_srl` LIKE '$you_srl'"));

        $you_srl_info = mysqli_fetch_array(DBQuery("SELECT * FROM  `pages` WHERE  `user_srl` LIKE '$you_srl'"));

        //    if($status < $you_srl_info[status]) $row = null;
        for ($i=0 ; $i < count($info);$i++){
            if(($you_srl_status[$info[$i]] > $status || $you_srl_info['status'] > $status || $you_srl_status[$info[$i]] == null ||$you_srl_info['status'] == 5) && ($info[$i] != "srl")){
                $row[$info[$i]] = "null";
            }


        }

        return $row;
    }

    function setStatusAct($page_srl, $status)
    {

        return Model_Page_setStatus($page_srl, $status);
    }


//IF use this function you must import auth.php and private.php

    function BackgroundImageUpdate($ATTACH_CLASS){

    }

    function GetAllPageInfoByUpdate($user_srl, $start, $number)
    {
        $row = DBQuery("SELECT * FROM  `pages` WHERE  `status` < 4 ORDER BY  `pages`.`last_update` DESC LIMIT $start , $number");
        return $row;
    }

    function GetAllPageInfoByPopularity($user_srl, $start, $number)
    {
        $row = DBQuery("SELECT * FROM  `pages` WHERE  `status` < 4 ORDER BY  `pages`.`popularity` DESC LIMIT $start , $number");
        return $row;
    }

    function getPageAuth($user_srl, $page_srl)
    {
        $page_info = $this -> GetPageInfo($page_srl);
        if ($page_info['admin'] == $user_srl) $auth_code = FindAuthCode($page_srl, "user_srl");
        return $auth_code;
    }

    function PhoneNumberToPageNumber($phonenumbers)
    {
        $count = count($phonenumbers);
        if ($count <= 1) return false;
        for ($i = 0; $i < count($phonenumbers); $i++) {
            $orvalue = $i != 0 ? "OR" : "";
            $value = $value . $orvalue . " `phone_number` LIKE '%" . $phonenumbers[$i] . "%' ";
        }
        $row = DBQuery("SELECT * FROM  `pages` WHERE (" . $value . ") AND  `admin` = 0 ORDER BY  `pages`.`last_update` DESC LIMIT 0 ," . $count);
        return $row;
    }

    function updatePopularity($user_srl, $page_srl, $point)
    {

        if ($user_srl != $page_srl) {
            $mf = $this ->getPageInfo($page_srl);
           $this ->  ProfileInfoUpdate($page_srl, "popularity", $mf['popularity'] + $point);
        }
    }

    function ProfileInfoUpdate($user_srl, $title, $value)
    {
        $add_info_to_system = "UPDATE `pages` SET `$title` = '$value' WHERE `srl` = $user_srl";
        $system_result = DBQuery($add_info_to_system);

        return $system_result;
    }

    function member_PrintListbyUpdate($row)
    {
        $total = mysqli_num_rows($row);
        for ($i = 0; $i < $total; $i++) {
            mysqli_data_seek($row, $i);           //포인터 이동
            $result = mysqli_fetch_array($row);        //레코드를 배열로 저장
            //    $user_info = GetPageInfo($result[value]);
            $name = SetUserName($result['lang'], $result['name_1'], $result['name_2']);
            $profile[] = array("last_update" => $result['last_update'], "user_srl" => $result['user_srl'], "name" => $name);
        }

        foreach ($profile as $key => $row) {
            $last_update[$key] = $row['last_update'];
            $user_srl[$key] = $row['user_srl'];
        }

// volume 내림차순, edition 오름차순으로 데이터를 정렬 
// 공통 키를 정렬하기 위하여 $data를 마지막 인수로 추가 
//array_multisort($last_update, SORT_DESC, $user_srl, SORT_ASC, $profile);

        foreach ($profile as $key => $value) {
            echo $value['user_srl'] . "/LINE/." . $value['name'] . "/PFILE/.";
        }

    }

// You must import member_class.php

    function CheckSameRegID($reg_id){
        $CheckSameRegID = mysqli_fetch_array(DBQuery("SELECT * FROM  `pages` WHERE  `reg_id` LIKE '$reg_id'"));
        return $CheckSameRegID;
    }




//Find lastest number.

    function CheckSameTarksAccount($tarks_account){
        $CheckSameTarksAccount = mysqli_fetch_array(DBQuery("SELECT * FROM  `pages` WHERE  `tarks_account` LIKE '$tarks_account'"));
        return $CheckSameTarksAccount;
    }

//add 1

    //Find the Same Reg ID

    function CreatePage($user_srl, $name, $lang, $country, $profile_pic){
        global  $_FILES;
        //  $user_srl = AuthCheck($user_srl, false);
        //If not page return
        $user_info = $this -> getPageInfo($user_srl);
        if($user_info == null || $name == null) return false;
        // Get PageLastNumber
        $PageNumber = $this -> PageLastNumber();
        //Get Auth Code
        $auth_code = MakeAuthCode("36" , $PageNumber, "user_srl");

        if($profile_pic == "Y") $this -> ProfileUpdate($PageNumber);
        $popularity = intval(getTimeStamp()/10000);
        //add user to db
        $sql ="INSERT INTO `pages` (`tarks_account`, `admin`, `name_2`, `lang`, `country`, `permission`, `birthday`, `join_day`, `profile_pic`, `profile_update`, `last_update`, `reg_id`, `ip_addr`, `popularity`) VALUES ('null', '$user_srl', '$name', '$lang', '$country', '3', '0' ,'".getTimeStamp()."', '$profile_pic', '".getTimeStamp()."' , '".getTimeStamp()."' , 'null', '".getIPAddr()."', '$popularity');";
        $result = DBQuery($sql);



        //Create Own Page
        //     $add_page = DBQuery("INSERT INTO `pages` (`user_srl`, `user_mode`,  `ip_addr`) VALUES ('$MemberNumber', 'Y', '$REMOTE_ADDR');");
        $this -> CreateStatus($PageNumber);

        favorite_add($PageNumber, $user_srl, '3');

        return array($PageNumber, $auth_code);

    }


    //Tarks Account

    function PageLastNumber(){
        $user_table_status =mysqli_fetch_array(DBQuery("SHOW TABLE STATUS LIKE 'pages'"));
        return $user_table_status['Auto_increment'];
    }

    function ProfileUpdate($file_name)
    {
        global $_FILES;
        if($_FILES == null){
            echo "files_null_error";
        }
        $target_path =  "files/profile/";
        $thumbnail_path =  "files/profile/thumbnail/";
      //  $tmp_img = explode(".", $_FILES['uploadedfile']['name']);
//$img_name = $file_name.".".$tmp_img[1];
        $img_name = $file_name . "." . "jpg";
        $target_path = $target_path . basename($img_name);

        $upload_result = move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path);
        Thumbnail::create($target_path,
            120, 120,
            SCALE_EXACT_FIT,
            Array(
                'savepath' => $thumbnail_path . $img_name
            ));
        $this -> ProfileInfoUpdate($file_name, "profile_update", getTimeStamp());
       $this ->  ProfileInfoUpdate($file_name, "profile_pic", "Y");
        return $upload_result;
    }

//requrie favorite_class.php

    function CreateStatus($member_srl){
        $ExistStatus = mysqli_fetch_array(DBQuery("SELECT * FROM  `status` WHERE  `user_srl` LIKE '$member_srl'"));
        if($ExistStatus == null)   DBQuery("INSERT INTO `status` (`user_srl`, `phone_number`) VALUES ('$member_srl', '3');");
    }

    function addUnregisteredUser()
    {
        $result = $this->AddUser("unregistered", 0, null, null, 0, 0, 0, 0, null, null, null, null);

        return $result;
    }


    function DeleteUser($user_srl) {
        $deletesql ="DELETE FROM `pages` WHERE `user_srl` = '$user_srl'";
        $deleteresult = DBQuery($deletesql);

        return $deleteresult;

    }

    function CleanPageInformation($page_srl)
    {
        return Model_Page_CleanInformation($page_srl);
    }

    function AddUserActivityByApp($tarks_account, $admin ,$name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $lang, $country){
        $AddUserAct = $this -> AddUser($tarks_account, $admin ,$name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $lang, $country);
        echo $AddUserAct[0]."//".$AddUserAct[1];
    }

    function AddUser($category, $admin, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $lang, $country) {
        //Add user to System

// Get PageLastNumber
        $PageNumber = $this -> PageLastNumber();

        //Get Auth Code
        $auth_code = MakeAuthCode("36" , $PageNumber, "user_srl");

//Profile picture
        if($profile_pic == "Y") $this -> ProfileUpdate($PageNumber);
        $popularity = intval(getTimeStamp()/10000);
        //add user to db
        $sql = Model_Page_addPage($category, '0', $admin, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, 3, $profile_pic, $reg_id, $lang, $country, $popularity);
        $result = DBQuery($sql);



        //Create Own Page
        //     $add_page = DBQuery("INSERT INTO `pages` (`user_srl`, `user_mode`,  `ip_addr`) VALUES ('$MemberNumber', 'Y', '$REMOTE_ADDR');");
        $this -> CreateStatus($PageNumber);



        return array("page_srl" => $PageNumber,  "auth" => $auth_code);
    }

//    function UpdateUserActivityByApp($user_srl, $tarks_account, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $country){
//        $UpdateUserAct = $this -> UpdateUser($user_srl, $tarks_account, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $country);
//        echo $UpdateUserAct[0]."//".$UpdateUserAct[1];
//    }

    function UpdateUser($user_srl, $tarks_account, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $country) {



        if($profile_pic == "Y")  $this -> ProfileUpdate($user_srl);
        //add user to db
        $sql ="UPDATE `pages` SET `name_1` = '$name_1', `name_2` = '$name_2', `gender` = '$gender', `country_code` = '$country_code', `phone_number` = '$phone_number', `profile_pic` = '$profile_pic', `profile_update` = '".getTimeStamp()."', `reg_id` = '$reg_id', `country` = '$country' WHERE `user_srl` = '$user_srl'";
        $result = DBQuery($sql);

        $auth_code = FindAuthCode($user_srl, "user_srl");


        //     unlink﻿("../files/profile/".$user_srl.".jpg");


        return array($user_srl, $auth_code);
    }

    function updateLastUpdate($user_srl, $value)
    {
        return $this->update($user_srl, 'last_update', $value);
    }

    function update($user_srl, $attribute, $value)
    {
        return Model_Page_UpdateByAttribute($user_srl, $attribute, $value);
    }



    function isAdmin($permission){
        return $permission < 3 && $permission > 0;
    }

    function amIAdmin(){
        return $this -> isAdmin(getIdentity()['permission']);
    }

    function setRegIDAct($page_srl, $Regid)
    {
        return Model_Page_setRegID($page_srl, $Regid);
    }

    /**
     * getActiveUsers
     * return active users array
     *
     * @param integer $time : seconds which determine list how long user has active profile.
     * @return mysqli_result $return : Result (User List)_.
     */
    function getActiveUsers($time)
    {
        $PAGE = new Page();
        if (!$this->isAdmin(getIdentity()['permission'])) return "permission_error";
        return $PAGE->getActiveUsers($time);
    }


    function getAllowedStatus($access_user_srl) {
        $pageRow = $this -> GetPageInfo($access_user_srl);
        return getAllowedStatus(getIdentity()['user_srl'], getIdentity()['status'], getIdentity()['permission'], $pageRow['status'], $access_user_srl, $pageRow['status'] );
    }

}
      
?>
