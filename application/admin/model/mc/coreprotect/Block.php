<?php

namespace app\admin\model\mc\coreprotect;

use think\Model;


class Block extends Model
{

    

    

    // 表名
    protected $table = 'co_block';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'time_text'
    ];
    

    



    public function getTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['time']) ? $data['time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\co\User', 'user', 'rowid', [], 'LEFT')->setEagerlyType(0);
    }


    public function world()
    {
        return $this->belongsTo('app\admin\model\co\World', 'wid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
