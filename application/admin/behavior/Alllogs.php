<?php

namespace app\admin\behavior;

class Alllogs
{
    public function run(&$params)
    {
        \app\admin\model\general\Alllogs::record();
    }
}