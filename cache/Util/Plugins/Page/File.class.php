<?php
namespace Common\Util\Plugins\Page;

class PageCache{
    private $config;

    public function __construct($config){
        $this->config = $config;
    }

    /*
        * 向文件中添加缓存
        * @param $key 文件名
        * @param $value 需要写入的内容
        * @param $time 数据的缓存时间
        */
    public function setValue($key, $value, $time){
        $key = PAGE_CACHE_DIR . $key;
        $time = $this->getTime($time);
        $arr = array();
        //var_dump($time);die;
        $arr[0] = time() + $time;
        $arr[1] = $value;
        $arr = serialize($arr);
        $res = file_put_contents($key, $arr);
        return $res;
    }

    /*
    * 从文件中取出缓存数据
    * @param $key 需要取出数据的文件名
     * 取出结果进行判断，存在且未过期，返回结果数组，否则返回false
    */
    public function getValue($key){
        $key = PAGE_CACHE_DIR . $key;
        $res = file_get_contents($key);
        $res = unserialize($res);
        $judge = $this->overTime($res);
        if($judge){
            return $res[1];
        }else{
            return $judge;
        }
    }

    /*
    * 对键名进行加密（采用MD5两次加密）
    * @param $data 传递过来需要加密的键名
    * @param $res 返回加密后的键名
    */
    public function getValue5($key){
        $key = MD5($key);
        $content = $this->getValue($key);
        return $content;
    }

    public function setValue5($key, $content, $time){
        $key = MD5($key);
        $res = $this->setValue($key, $content, $time);
        return $res;
    }

    /*
     * 判断缓存文件是否过期
     * @param $key 文件名
     * @param $time 缓存的时间
     * 当前时间减文件被修改的时间是否大于缓存时间；大于返回真；否则返回假
     */
    public function overTime($data){
        $res = $data;
        if(!empty($res) && $res[0] > time()){
            return true;
        }else{
            return false;
        }

    }

    /*
   * 根据传递过来的时间参数和单位，确定缓存的时间
   */
    public function getTime($time){
        if(is_int($time)){
            return $time;
        }

        $unit = substr($time,strlen($time)-1);
        $num = (int)substr($time,0,strlen($time)-1);

        if (!is_int($num)){
            return $this->config->defaulttimeout;
        }

        switch($unit){
            case 's':
                $realtime = $num;
                break;
            case 'n':
                $realtime = $num * 60;
                break;
            case 'h':
                $realtime = $num * 60 * 60;
                break;
            case 'd':
                $realtime = $num * 24 * 60 * 60;
                break;
            case 'w':
                $realtime = $num * 7 * 24 * 60 * 60;
                break;
            case 'm':
                $realtime = $num * 30 * 24 * 60 * 60;
                break;
            case 'y':
                $realtime = $num * 365 * 24 * 60 * 60;
                break;
            default:
                $realtime = $time;
        }

        return $realtime;
    }

}