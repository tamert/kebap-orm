<?php
/**
 * Bütün kodların başlangıç noktası
 */

class Application {
    public $config;
   // public $_logger; sanırım bu gereksiz... :) hatam varsa açarsınız.
    public $importDirs;
    public $baseUrl;
    public $_components = array();
    public $_modules = array('Page');
    public $_module = null;
    public $_controllerPath = 'controllers';
    public $_vievPath = 'view';
    public $_modulePath = 'modules';
    public $_end;
    public $_request;
    private $_controller;
    private $_params = array();



    /**
     * Uygulamanın başlangıcı.
     * Autoload kaydı, errorhandler kaydı gibi aşamalar burada yapılabilir
     * @param null $config
     */
    public function __construct($config = null)
    {

			if(phpversion()<5.3){
				echo "Kebap Framework En Az PHP 5.3 Versiyonu İle Çalışmaktadır. PHP Sürümünü Güncelleyerek Tekrar Çalıştırmayı Deneyiniz.";
				exit;
			}

        spl_autoload_register('Application::autoload');
        set_error_handler(array($this,'handlerError'));
        set_exception_handler(array($this,'handleException'));
        require_once 'core/Base.php';
        require_once 'core/interfaces.php';


        if($config == null)
            $this->config = require_once 'core/Config.php';
        else
            $this->config = $config;


        $this->appPreInit();

        Base::setApp($this);

        $this->appPostInit();


        Base::log('message','Uygulama başladı');

        $this->_request = new Request();
        //var_dump($this->_request);
        //var_dump($_GET);
        $gateway = new Gateway($this->_request);
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

    /**
     * @param $name
     * @return mixed
     */
    public function initComponent($name)
    {

        $config = $this->config['components'][$name];
        $this->_components[$name] = new $config['class']();

        foreach ($config as $k=>$v)
            $this->_components[$name]->$k = $v;
        $this->_components[$name]->init();

        return $this->_components[$name];
    }

    public function appPreInit()
    {
        $this->importDirs = array(
            'core',
            'model',
            'class'
        );


    }

    public function appPostInit()
    {
        if(isset(Base::app()->config['end']))
            $this->end = Base::app()->config['end'];

        // Init core components
        $this->language;
        $this->theme;


        // @deprecated
        //$this->add2Import($this->getControllerpath());
    }

    /**
     * Autoloada yeni dizin eklemek
     * Kullanım: Base::app()->add2Import('core/db')
     * @param $new string yeni klasör adı
     */
    public function add2Import($new)
    {
        if(is_dir($new))
            $this->importDirs[] = $new;
    }


    public function getUrl()
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'] . $this->baseUrl;
        return$url;
    }

    public function handleException($e)
    {


    }

    function handlerError($errno, $errstr, $errfile, $errline)
    {
        $file = highlight_file($errfile,true);
        $file = explode('<br />',$file);

        $code = '';
        for($i = $errline - 10; $i<= $errline + 10; $i++)
        {
            $code .= isset($file[$i]) ? ($i+1) . $file[$i] : '';
        }

        echo '<div style="border:2px solid red">
            <div style="background: #ddd; color:red; width:98%; padding:5px; font:14px verdana;">
            <p><strong>Error:</strong> '.$errstr.' </p>
            <p><strong>Fil:</strong> '.$errfile.'</p>
            <p><strong>Line:</strong> '.$errline.'</p>
            </div>
        ';
        echo '<pre>'.$code.'</pre>';
        echo '</div>';

    }

    public function getEnd()
    {
        if($this->_end != null)
            return $this->_end;

        $this->end = 'front';
        return $this->_end;

    }

    public function getEndPath()
    {

        if($this->_end != null)
            return $this->_end . '/';

        else
            return '';
    }

    public function setEnd($value)
    {
        $this->_end = $value;
    }

    public function getControllerpath()
    {
        return $this->_controllerPath;
    }

    public function setControllerPath($v)
    {
        $this->_controllerPath = $v  .'/'. $this->getEndPath();
        $this->add2Import($this->_controllerPath);
    }

    public function getViewPath()
    {
        return $this->_vievPath;
    }

    public function setViewPath($v)
    {
        $this->_vievPath = $v  .'/'. $this->getEndPath();
        $this->add2Import($this->_vievPath);
    }

    public function getModule()
    {
        if($this->_module !== null)
            return $this->_module;

        return null;

    }

    public function setModule($value)
    {
        $path   = $this->_modulePath . '/' . $value;

        $this->add2Import($path);


        $moduleName = $value.'Module';
        $this->_module = new $moduleName();

        $this->setControllerPath($path. '/'.$this->_module->controllerPath);
        $this->setViewPath($path . '/'.$this->_module->viewPath);



    }

    public function setController($v)
    {
        $this->_controller = $v;
    }
    public function getController()
    {
        return $this->_controller;
    }

    public function getBaseUrl()
    {
        return $this->_request->base;
    }

    public function setParam($name,$value)
    {
        $this->_params[$name] = $value;
    }

    public function getParam($name)
    {
        if(isset($this->_params[$name]))
            return $this->_params[$name];
        return null;
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

    public function __destruct()
    {
        //var_dump($this);
        //var_dump($this->importDirs);
    }

    public function finish()
    {
        Base::log('System','Application Finished');
        exit();
    }
 


}