<?php

namespace app\admin\model\mc;

use think\Model;


class Authme extends Model
{

    

    

    // 表名
    protected $table = 'authme';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    
    public function getLastloginAttr($value){
       return $value/1000;
    }

    public function getRegdateAttr($value){
        return $value/1000;
    }

    public function searchLastloginTimeAttr($query,$value){
        $query->whereBetweenTime('lastlogin',$value[0]*1000,$value[1]*1000);

    }








}
