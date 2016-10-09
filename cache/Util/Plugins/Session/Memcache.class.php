<?php
namespace Common\Util\Plugins\Session;
class SessionFactory{
    /*
     * 自动实例化memcache类（即连接memcache非关系型数据库）
     */
    private $_obj = '';
    private $config;

    //构造函数，实例化memcahce对象并保存到$_obj中
    public function __construct($config)
    {
        $this->config = $config;

        $server = $config->server;
        $port   = explode(":", $server);
        $port = $port[1];

        C('MEMCACHE_HOST',$server);
        C('MEMCACHE_PORT',$port);

        $this->_obj = new \Think\Cache\Driver\Memcache();
    }

    /*
     * 向memcache中添加缓存
     * @param $key memcahce中需要的键
     * @param $value memcahce中需要的值
     * @param $time memcahce中数据的缓存时间
     */
    public function setValue($key, $value, $time){
        $time = $this->getTime($time);
        $str = $this->config->session_keyStr;
        $res = $this->_obj->set($key.$str, $value, $time);
        return $res;
    }

    public function setValue5($key, $value, $time){
        $key = MD5($key);
        $data = $this->setValue($key, $value, $time);

        return $data;
    }

    /*
     * 从memcache中取出缓存数据
     * @param $key 需要取出数据的键名
     * @param $res 取出的结果集合
     */
    public function getValue($key){
        $str = $this->config->session_keyStr;
        $res = $this->_obj->get($key.$str);
        return $res;
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

    public function delete($key){
        $str = $this->config->session_keyStr;
        $ret = $this->_obj->rm($key.$str);
        return $ret;
    }

    public function delete5($key){
        $key = md5($key);
        $ret = $this->delete($key);
        return $ret;
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
        if(!is_int($num)){
            return $this->config->defaulttimeout;
        }
        $realtime = "";
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