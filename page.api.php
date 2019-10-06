<?php
/**
 *
 * User: JHRunning
 * Date: 2/10/15
 * Time: 2:32 PM
 */



//Page
if($ACTION == "page_info") $PageAPI -> API_getPageInfo($user_srl);
if($ACTION == "my_page_info") $PageAPI -> API_getMyPageInfo($user_srl);
if($ACTION == "page_info_update") $PageAPI -> API_PageInfoUpdate($user_srl);
//Page - ADD
if($ACTION == "page_join") $PageAPI -> API_PageJoin();
if($ACTION == "page_add_unregistered") $PageAPI -> API_addUnregisteredUser($user_srl);


