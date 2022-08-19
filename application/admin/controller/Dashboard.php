<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\mc\Authme;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\model\Attachment;
use fast\Date;
use think\Db;

/**
 * 控制台
 *
 * @icon   fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{


    /**
     * 查看
     */
    public function index()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {

        }
        $column = [];
        $starttime = Date::unixtime('day', -6);
        $endtime = Date::unixtime('day', 0, 'end');
        $joinlist = Db("user")->where('jointime', 'between time', [$starttime, $endtime])
            ->field('jointime, status, COUNT(*) AS nums, DATE_FORMAT(FROM_UNIXTIME(jointime), "%Y-%m-%d") AS join_date')
            ->group('join_date')
            ->select();
        for ($time = $starttime; $time <= $endtime;) {
            $column[] = date("Y-m-d", $time);
            $time += 86400;
        }
        $userlist = array_fill_keys($column, 0);
        foreach ($joinlist as $k => $v) {
            $userlist[$v['join_date']] = $v['nums'];
        }

        $dbTableList = Db::query("SHOW TABLE STATUS");
        $addonList = get_addon_list();
        $totalworkingaddon = 0;


        //获取今日开始时间戳和结束时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'))*1000;
        $endToday=(mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1)*1000;

        //获取三天的开始结束时间戳
        $threednuToday=mktime(0,0,0,date('m'),date('d')-2,date('Y'))*1000;
        $endthreednuToday=(mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1)*1000;

        //获取上周起始时间戳和结束时间戳
        $beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'))*1000;
        $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'))*1000;

        //获取本月起始时间戳和结束时间戳
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'))*1000;
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'))*1000;

        $this->view->assign([
            'totaluser'         => Authme::count(),
            'totaladdon'        => Authme::where('islogged','1')->count(),
            'totaladmin'        => Admin::count(),
            'todayusersignup'   => Authme::whereTime('regdate', 'between', [$beginToday, $endToday])->count(),
            'todayuserlogin'    => Authme::whereTime('lastlogin', 'between', [$beginToday, $endToday])->count(),
            'sevendau'          => Authme::whereTime('regdate|lastlogin', 'between', [$beginLastweek, $endLastweek])->count(),
            'thirtydau'         => Authme::whereTime('regdate|lastlogin', 'between', [$beginThismonth, $endThismonth])->count(),
            'threednu'          => Authme::whereTime('regdate', 'between', [$threednuToday, $endthreednuToday])->count(),
            'sevendnu'          => Authme::whereTime('regdate', 'between', [$beginLastweek, $endLastweek])->count(),
            'dbtablenums'       => count($dbTableList),
            'dbsize'            => array_sum(array_map(function ($item) {
                return $item['Data_length'] + $item['Index_length'];
            }, $dbTableList)),
            'totalworkingaddon' => $totalworkingaddon,
            'attachmentnums'    => Attachment::count(),
            'attachmentsize'    => Attachment::sum('filesize'),
            'picturenums'       => Attachment::where('mimetype', 'like', 'image/%')->count(),
            'picturesize'       => Attachment::where('mimetype', 'like', 'image/%')->sum('filesize'),
        ]);

        $this->assignconfig('column', array_keys($userlist));
        $this->assignconfig('userdata', array_values($userlist));

        return $this->view->fetch();
    }



}
