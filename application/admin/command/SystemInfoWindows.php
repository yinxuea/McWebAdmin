<?php

namespace app\admin\command;

class SystemInfoWindows
{
    /**
     * 判断指定路径下指定文件是否存在，如不存在则创建
     * @param string $fileName 文件名
     * @param string $content 文件内容
     * @return string 返回文件路径*/
    private function getFilePath($fileName, $content)
    {
        $path = dirname(__FILE__) . "\\$fileName";
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
        return $path;
    }
    /**
     * 获得cpu使用率vbs文件生成函数
     * @return string 返回vbs文件路径
     */
    private function getCupUsageVbsPath()
    {
        return $this->getFilePath(
            'cpu_usage.vbs',
            "On Error Resume Next
Set objProc  = GetObject(\"winmgmts:\\\\.\\root\\cimv2:win32_processor='cpu0'\")
Wscript.Echo (objProc.LoadPercentage)"
        );
    }

    /**
     * 获得CPU使用率
     * @return Number*/
    public function getCpuUsage()
    {
        $path = $this->getCupUsageVbsPath();
        exec("cscript -nologo $path", $usage);
        return "CPU使用率: ".$usage[0]. "%";
    }

    /**
     * 获取内存使用
     * @return array|string|void
     */
    public function getMon(){
        switch (PHP_OS)
        {
            case "WINNT":
            case "Windows":
                $out = '';
                $info = exec('wmic os get TotalVisibleMemorySize,FreePhysicalMemory',$out,$status);
                $phymem = preg_replace ( "/\s(?=\s)/","\\1",$out[1]);
                $phymem_array = explode(' ',$phymem);
                $freephymem = round($phymem_array[0]/1024/1024,2);
                $totalphymem = round($phymem_array[1]/1024/1024,2);
                return "运行内存:". ($totalphymem - $freephymem) ." GB/". $totalphymem . " GB";
            case "Linux":
                return "暂不支持linux";

        }

    }

    public function getRunTime(){
        switch (PHP_OS)
        {
            case "WINNT":
            case "Windows":
                $out = '';
                $info = exec('wmic os get lastBootUpTime,LocalDateTime',$out,$status);
                $datetime_array = explode('.',$out[1]);
                $dt_array = explode(' ',$datetime_array[1]);
                $localtime = substr($datetime_array[1],-14);
                $boottime = $datetime_array[0];
                $uptime = strtotime($localtime) - strtotime($datetime_array[0]);
                $day=floor(($uptime)/86400);
                $hour=floor(($uptime)%86400/3600);
                $minute=floor(($uptime)%86400/60);
                return "已运行: ".$day."天".$hour."小时".$minute."分钟";
            case "Linux":
                return "暂不支持linux";
        }

    }

    public function getCpu(){
        switch (PHP_OS)
        {
            case "WINNT":
            case "Windows":
                return $this->getCpuUsage();
            case "Linux":
                return "暂不支持linux";
        }
    }


}