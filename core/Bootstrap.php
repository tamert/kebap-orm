<?php
/**
 * Bütün kodların başlangıç noktası
 */

class Applaication {
    public $config;
    public $_logger;
    public $importDirs;
    public $_components = array();

    /**
     * Uygulamanın başlangıcı.
     * Autoload kaydı, errorhandler kaydı gibi aşamalar burada yapılabilir
     */
    public function __construct()
    {

        spl_autoload_register('Application::autoload');
        set_exception_handler(array($this,'handleException'));
        require_once 'core/Base.php';
        require_once 'core/interfaces.php';


        $this->config = require_once 'core/Config.php';

        $this->appPreInit();
        Base::setApp($this);

        Base::log('message','Uygulama başladı');

        $gateway = new Gateway();
        Base::log('message','Gateway oluştu');
        $gateway->init();
        Base::log('message','Gateway init');




    }

    /**
     * SPL'e eklenecek autoload methodu
     * @param $classname Sınıf adı
     */
    public function autoload($classname){


        foreach($this->importDirs as $dir){
            if(file_exists($dir .'/' .$classname.'.php')){
                include_once $dir .'/' .$classname.'.php';
                return ;
            }
        }
    }

    public function initComponent($name)
    {

        $config = $this->config['components'][$name];
        $this->_components[$name] = new $config['class']();

        foreach ($config as $k=>$v)
            $this->_components[$name]->$k = $v;

        return $this->_components[$name];
    }



    public function appPreInit()
    {
        $this->importDirs = array(
            'core',
            'controller',
            'model',
            'class'
        );
    }

    /**
     * Autoloada yeni dizin eklemek
     * Kullanım: Base::app()->add2Import('core/db')
     * @param $new yeni klasör adı
     */
    public function add2Import($new)
    {
        if(is_dir($new))
            $this->importDirs[] = $new;
    }

    public function getScriptUrl()
    {

            $scriptName=basename($_SERVER['SCRIPT_FILENAME']);
            if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
                return $_SERVER['SCRIPT_NAME'];
            elseif(basename($_SERVER['PHP_SELF'])===$scriptName)
                return $_SERVER['PHP_SELF'];
            elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
                return $_SERVER['ORIG_SCRIPT_NAME'];
            elseif(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
                return substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
            elseif(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
                return str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
            else
                exit('url anlaşılamadı');
    }

    public function handleException($e)
    {

    }
    public function __get($name)
    {
        $getter = "get".$name;

        if(method_exists($this,$getter))
            return $this->$getter();


        if(isset($this->_components[$name]))
            return $this->_components[$name];

        if(isset($this->config['components'][$name]) && $this->config['components'][$name] !== null){
            $this->initComponent($name);
            return $this->_components[$name];
        }

    }

    public function __set($name,$value)
    {
        $setter = "set".$name;
        if(method_exists($this,$setter))
            return $this->$setter($value);
    }

    public function __isset($name)
    {
        $getter='get'.$name;
        if(method_exists($this,$getter))
            return $this->$getter()!==null;
    }

    public function __unset($name)
    {
        $setter='set'.$name;
        if(method_exists($this,$setter))
            $this->$setter(null);
    }

    public function __call($name,$value)
    {
        return 'tahir';
    }
 


}