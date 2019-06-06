<?php if(!defined("642979")) exit();
/**
 * Created by Tarks, JHRunning.
 * User: JHRunning
 */

class PageAPIClass{


    function API_getMyPageInfo($user_srl){
        $PAGE = new PageClass();
        //require_once 'core/status.php';

        $page_info = REQUEST('page_info');
        $reg_id = REQUEST('reg_id');
//Update new member information
        $PageInfoRow = $PAGE -> PageInfo($user_srl, $user_srl ,  ExplodeInfoValue($page_info));

        if($reg_id) $PAGE -> ProfileInfoUpdate($user_srl, "reg_id", $reg_id);

        print_info($PageInfoRow, ExplodeInfoValue($page_info));
    }



    function API_getPageInfo($user_srl){
        $PAGE = new PageClass();
        //require_once 'core/status.php';

        $page_srl = REQUEST('page_srl');
        $page_info = REQUEST('page_info');

//Get Profile information
        $PageInfoRow = $PAGE -> PageInfo($user_srl, $page_srl, ExplodeInfoValue($page_info));
        //Print Profile information
        print_info($PageInfoRow, ExplodeInfoValue($page_info));
    }



    function API_PageInfoUpdate($user_srl){
        $PAGE_CLASS = new PageClass();

        $page_srl = REQUEST('page_srl');
        $title = REQUEST('title');
        $value = REQUEST('value');

        $status = setRelationStatus($user_srl, $page_srl);
        if($status < 4) ErrorMessage("permission_error");

        echo $PAGE_CLASS -> ProfileInfoUpdate($page_srl, $title, $value);

    }


    //Page - Join
    function API_PageJoin()
    {
        $PAGE = new PageClass();
        $TARKS_ACCOUNT = new TarksAccountClass();


        $admin = REQUEST('admin');
        $tarks_account_auth = POST('tarks_account_auth');
        $name_1 = REQUEST('name_1');
        $name_2 = REQUEST('name_2');
        $gender = REQUEST('gender');
        $lang = REQUEST('lang');
        $country_code = REQUEST('country_code');
        $phone_number = REQUEST('phone_number');
        $profile_pic = REQUEST('profile_pic');
        $reg_id = REQUEST('reg_id');
        $country =REQUEST('counrtry');


        if($tarks_account_auth != null){
            $tarks_account = AuthCheck($tarks_account_auth, 'tarks_account', true);
            $birthday = $TARKS_ACCOUNT -> GetTarksAccountInfo($tarks_account, "birthday");
        }else{
            $tarks_account = "null";
        }

//Check Reg Id and Tarks Account
        $row = $PAGE -> CheckSameRegID($reg_id);
        $tarksrow = $PAGE -> CheckSameTarksAccount($tarks_account);




//Check Tarks Account
        if($tarks_account != "null"){
            if($tarks_account == $tarksrow['tarks_account']){
                $PAGE -> UpdateUserActivityByApp($tarksrow['srl'], $tarks_account, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $country);
            }else{
                //if this is have tarks account
                if($row['tarks_account'] == "null"){
                    //Delete Old one Add new one
                    //Delete Old Account
                    $PAGE -> DeleteUser($row['srl']);
                }
                $PAGE -> AddUserActivityByApp($tarks_account, $admin ,$name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $lang, $country);
            }

        }else{
            //If no Tarks Account
            //Check REGID
            if($reg_id != "null"){
                //Delete User if same reg id, not null and no tarks account
                if($reg_id == $row['reg_id'] && $reg_id != "null" && $row['tarks_account'] == "null" && $row['admin'] == 0){
                    //IF more than two same reg id
                    $PAGE -> DeleteUser($row['srl']);
                }

            }
            $PAGE -> AddUserActivityByApp($tarks_account, $admin ,$name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $profile_pic, $reg_id, $lang, $country);
        }
    }






}