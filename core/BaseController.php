<?php
class BaseController {
    /**
     * @var string
     */
    public $layout = "";

    /**
     * @var array
     */
    private static $objects= array();

    public function __construct(){
        Base::log('message','controller construct ');
    }

    public function getControllerName()
    {
       return strtolower(str_replace('Controller','',get_class($this)));
    }

    public function __toString()
    {
        return $this->getControllerName();
    }

    public function beforeAction()
    {
        Base::log('controller','BeforeAction');
        return true;
    }

    public function afterAction()
    {
        Base::log('controller','afterAction');
        return true;
    }

    /**
     * @param string $_file  View File Name
     * @param array $_param  Params which can be used in render file
     * @return string Output
     * @throws SystemException
     */
    public function renderPartial($_file,$_param=array())
    {
        // IF there is a view file in theme folder than render it
        if(file_exists(Base::app()->theme->viewPath . $_file . '.php'))
            $_file = Base::app()->theme->viewPath . $_file . '.php';
        elseif(file_exists(Base::app()->viewPath . $_file.'.php'))
            $_file = Base::app()->viewPath . $_file.'.php';
        else
            throw new SystemException('View File Not Found:'.$_file);


        if(is_array($_param))
            extract($_param);


            ob_start();
            ob_implicit_flush(false);
            require($_file);
            return ob_get_clean();


    }

    /**
     * @param $_file string view File Name
     * @param array $_param array Params which can be used in render file
     * @param bool $_return if true it returns output otherwise echoes output
     * @return string output
     * @throws SystemException
     */
    public function render($_file,$_param = array(),$_return = false)
    {

        $output = $this->renderPartial($_file,$_param);
        if(!empty($this->layout))
        {
            $output = $this->renderPartial($this->layout,array('content'=>$output));
        }

        if($_return)
            return $output;
        else
            echo $output;
    }

    /**
     * @param null $url
     * @param int $interval
     * @todo: crateUrl'a bağala
     */
    public function redirect($url = null,$interval = 0)
    {
        if(!$url)
            $url = Base::app()->baseUrl;

        if($interval>0)
        {
            echo '<meta http-equiv="refresh" content="'.$interval.';url='.$url.'">';
        }else{
            header('Location:'.$url);
        }
        Base::app()->finish();
    }


    /**
     * @param null $url
     * @param array $params
     * @return null|string
     * @todo: return ayarlancak, end ve konum vs..
     */
    public function createUrl($url = null,$params = array())
    {

        if(!$url or $url=='')
            return null;

        $url = preg_replace('/^\//', '', $url);

        if(is_array($params) && empty($params))
        {
            $params = implode("/", $params);;
        }

        $params = preg_replace('/^\//', '', $params);
        $params = '/'.$params;

        var_dump(Base::app()->request);

        if(strpos($url,'/') === false)
            return Base::app()->baseUrl.'/'.$this->controllerName.'/'.$url.$params;
        # only action
        else
            return Base::app()->baseUrl.'/'.$url.$params;
        # controller/action or controller/action/params
    }

}

