<?php


class Page
{
    public $srl, $category, $status, $write_status, $admin, $name_1, $name_2, $gender, $birthday, $country_code, $phone_number, $permission, $join_day, $profile_pic, $profile_update, $last_update, $tags, $reg_id, $key, $lang, $country, $ip_addr, $like_me, $favorite, $popularity;

    public function __construct(Array $properties = array())
    {
        foreach ($properties as $key => $value) {
            $this->{$key} = $value;
        }
    }


    function getLastUpdatedUsers($time)
    {
        $criterion = getTimeStamp() - $time;
        //  echo getSqlInsertQuery('square_channel', (array) $this);
        return DBQuery(getSqlAdvSelectQuery('pages', array('last_update' => array(">", $criterion)), "last_update", "DESC", true));
    }


}