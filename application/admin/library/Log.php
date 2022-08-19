<?php
/**
 * Created by hnh.
 * Email: 1123416584@qq.com
 * Blog: blog.hnh117.com
 */

namespace app\admin\library;

class Log
{
    protected $dir = null;
    protected $config = null;
    protected $error = null;

    // 过虑条件
    protected $filter = [];

    // 分隔符
    protected $separator = '---------------------------------------------------------------';

    public function __construct()
    {
        $config = config('log');
        $dir = $config['path'];
        $this->config = $config;
        $this->dir = rtrim($dir, DIRECTORY_SEPARATOR);
    }

    /**
     * 删除日志根据ID
     * @param $file_path 日志文件路径
     * @param $ids 要删除的ID
     * @return bool 成功返回true,失败返回false
     */
    public function deleteForId($file_path, $ids)
    {
        $ids = is_array($ids) ? $ids : compact('ids');
        $logArry = $this->getLogs($file_path, []);
        foreach ($ids as $id) {
            $index = $id - 1;
            if (isset($logArry[$index])) {
                unset($logArry[$index]);
            }
        }
        $logStr = '';
        foreach ($logArry as $item) {
            $logStr .= $this->separator.$item['content'];
        }
        $path = $this->complementLogPath($file_path);
        file_put_contents($path, $logStr);
        return true;
    }

    /**
     * 获取日志信息
     */
    public function getInfo($file_path)
    {
        $path = $this->complementLogPath($file_path);
        if (false === $path) {
            return false;
        }
        $info['size'] = format_bytes(filesize($path));
        $info['update_time'] = date('Y-m-d H:i:s', filemtime($path));
        return $info;
    }

    /**
     * 根据文件相对路径补全路径，自动做安全判断
     *
     * @param $file
     * @return bool|string
     */
    public function complementLogPath($file)
    {
        $path = $this->dir . $file;
        if (strpos($file, '.') === 0 || false !== strpos($file, '..') || is_dir($path)) {
            return false;
        }
        return $path;
    }

    /**
     * 获取日志
     */
    public function getLogs($file_path, $filter)
    {
        $this->filter = $filter;
        $path = $this->complementLogPath($file_path);
        if (false === $path) {
            return false;
        }
        $strlogs = file_get_contents($path);
        $arr = explode($this->separator, $strlogs);
        unset($arr[0]);
        $row = [];
        $levels = [
            'ERROR'  => '[ error ]',
            'NOTICE' => '[ notice ]',
            'INFO'   => '[ info ]',
            'DEBUG'  => '[ debug ]',
            'SQL'    => '[ sql ]',
            'LOG'    => '[ log ]',
        ];
        foreach ($arr as $k => $v) {
            $regex = '/\[\s(\d+-\d+-\d+T\d+:\d+:\d+).{6}\s\]\s(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s([A-Z]{3,8})\s(.+)/';
            preg_match($regex, $v, $matches);
            $item['id'] = $k;
            $item['time'] = $matches[1];
            $item['ip'] = $matches[2];
            $item['method'] = $matches[3];
            $item['url'] = $matches[4];
            $item['content'] = trim($v);
            $content = str_replace($matches[0], '', $v);
            foreach ($levels as $k => $v) {
                if (false !== strpos($content, $v)) {
                    $item['level'] = $k;
                    break;
                }
            }
            $this->filter($row, $item);
        }
        $order = isset($_GET['order']) ? $_GET['order'] : 'asc';
        if ($order == 'desc') {
            $row = array_reverse($row);
        }
        return $row;
    }

    /**
     * 过虑筛选选
     *
     * @param $row
     * @param $item
     * @return bool
     */
    public function filter(&$row, $item)
    {
        $filter = $this->filter;
        if (empty($filter)) {
            $row[] = $item;
            return true;
        }
        if (isset($filter['level']) && $filter['level'] != strtolower($item['level'])) {
            return false;
        }
        if (isset($filter['method']) && $filter['method'] != strtolower($item['method'])) {
            return false;
        }
        if (isset($filter['url']) && !strpos($item['url'], $filter['url'])) {
            return false;
        }
        if (isset($filter['time'])) {
            $itemTimeInt = strtotime($item['time']);
            $timeArr = explode(' - ', $filter['time']);
            array_walk($timeArr, function (&$v) {
                $v = strtotime($v);
            });
            if ($timeArr[0] > $itemTimeInt || $timeArr[1] < $itemTimeInt) {
                return false;
            }
        }
        $row[] = $item;
        return true;
    }

    /**
     * 获取日志目录
     *
     * @return array|bool
     */
    public function getDirectory()
    {
        $directory = $this->getSelectedDirectory();
        return $directory;
    }

    /**
     * 获取选中最新日志的文件目录
     *
     * @return mixed
     */
    public function getSelectedDirectory()
    {
        $directory = $this->directory($this->dir);
        $directory = array_filter($directory, function ($item) {
            return !empty($item['children']);
        });
        $directory = array_merge($directory);
        return $this->setSelected($directory);
    }

    /**
     * (简单)自动选中最新日志
     *
     * @param $directory $array 日志目录数组
     * @return mixed
     */
    public function setSelected($directory)
    {
        $last = count($directory) - 1;

        if (empty($directory[$last])) {
            return false;
        }

        if ($directory[$last]['type'] == 'folder') {
            if ($directory[$last]['children'] != []) {
                $directory[$last]['children'] = $this->setSelected($directory[$last]['children']);
            }
        } else {
            $directory[$last]['state'] = ['selected' => true];
        }
        return $directory;
    }

    /**
     * 递归获取目录
     *
     * @param $dir
     * @return array|bool
     */
    public function directory($dir)
    {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (false === is_dir($dir)) {
            return false;
        }
        $fiels = scandir($dir);
        $directory = [];
        foreach ($fiels as $k => $v) {
            if ($v == '.' || $v == '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $v;
            $id = str_replace($this->dir, '', $path);
            if (is_dir($path)) {
                $directory[] = [
                    'id'       => $id,
                    'text'     => $v,
                    'type'     => 'folder',
                    'children' => $this->directory($path),
                ];
            } else {
                $directory[] = [
                    'id'   => $id,
                    'text' => $v,
                    'type' => 'file',
                ];
            }
        }
        return $directory;
    }


    // 是否可以使用
    public function isAllow()
    {
        return $this->config['type'] == 'File' ? true : $this->setError('目前仅支持 File 日志驱动。');
    }

    public function setError($err)
    {
        $this->error = $err;
        return false;
    }

    public function getError()
    {
        return $this->error;
    }
}
