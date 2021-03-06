<?php
namespace Common\Util;
class CacheManager{
    //缓存的文件名或者键名的命名规则，是数据的名字+对应模型的名字（bs_bid_noticeBsite;bs_bid_notice是数据的名字也是模型中方法的名字，Bsite是该方法的模型）
    private $_obj = '';
    private $config;

    //构造函数，实例化此对象时，自动执行
    public function __construct(){
        $config = new ConfigManager();
        $conf = $config->config;
        $pagecache = $conf->pageCache;

        $mode = $pagecache->mode;
        switch ($mode){
            case "file":
                require_once APP_PATH."Common/Util/Plugins/Page/File.class.php";
                break;
            case "memcached":
            default:
                require_once APP_PATH."Common/Util/Plugins/Page/Memcache.class.php";
        }

        $this->config = $conf;
        $this->_obj = new \Common\Util\Plugins\Page\PageCache($pagecache);

    }

    //析构函数；当此对象被销毁时，自动执行
    public function __destruct(){
       $this->_obj = '';
    }

    public function encrypt($value){
        if (!is_string($value)){
            return $value;
        }

        $pagecache = $this->config->pageCache;
        if ($pagecache->security != ''){

            $process = new \Aes();
            $key = $pagecache->key;
            try {
                $value = $process->encrypt($value, $key);
            }catch(Exception $e){

            }
        }
        return $value;
    }

    public function decrypt($value){
        if (!is_string($value)){
            return $value;
        }

        $pagecache = $this->config->pageCache;
        if ($pagecache->security != ''){
            $process = new \Aes();
            $key = $pagecache->key;

            $value = $process->decrypt($value, $key);
        }

        return $value;
    }

    //缓存数据
    public function setValue5($key,$value,$time = ''){
        $value = $this->encrypt($value);
        return $this->_obj->setValue5($key, $value, $time);
    }

    //获取缓存(内部已判断是否过期，空或者过期将返回false)
    public function getValue5($key){
        if ($_GET['update'] == true){
            return false;
        }
        $value = $this->_obj->getValue5($key);
        $value = $this->decrypt($value);
        return $value;
    }

    public function setValue($key, $value, $time){
        $value = $this->encrypt($value);
        return $this->_obj->setValue($key, $value, $time);
    }

    public function getValue($key){
        if ($_GET['update'] == true){
            return false;
        }
        $value = $this->_obj->getValue($key);
        $value = $this->decrypt($value);
        return $value;
    }

}