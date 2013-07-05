<?php
/**
 * Class Gateway
 *
 * URL'in işlenmesi, ilgili Controller ve Actionların çağırılması
 * burada yapılır.
 *
 * Bootstrap.php de @link Aplication da init methodu çağırılır.
 * Controller çağırma gibi işlemler Gateway->init() de gerçekleşir.
 */
class Gateway{

    private $DefaultController = "site";
    private $DefaultAction = 'index';
    private $request;

    private $controller;
    private $action;
    private $params;


    public function __construct($r = null)
    {
        if(!$r)
            $this->request = new Request();
        else
            $this->request = $r;
    }

    public function init()
    {

        $rules = Base::app()->config['urlRules'];
        $parameters = $this->Parameters($this->RuleParser($rules));

        $this->route($parameters);

    }

    public function route($params)
    {
        if(isset($params[0]))
            $params[0] = ucfirst($params[0]);

        //Set module
        if(isset($params[0]) and in_array($params[0],Base::app()->_modules)){
            Base::log('route','Bu bir modül');
            Base::app()->module = ucfirst($params[0]);
            Base::app()->module->init();
        }

        if(Base::app()->module)
        {
            if(empty($params[1]) && file_exists(Base::app()->controllerPath.'/'.Base::app()->module->DefaultController.'Controller.php'))
                $c = Base::app()->module->DefaultController;
            elseif(file_exists(Base::app()->controllerPath.'/'.$params[1].'Controller.php'))
                $c = $params[1];
            else
                throw new SystemException('Controller File Not Found');

            if(isset($params[2]) && !empty($params[2]))
                $a = $params[2];
            else
                $a = $this->DefaultAction;

            //Remove first 3 items
            array_shift($params);
            array_shift($params);
            array_shift($params);

        }
        else
        {
            // @todo bu nereye koysak?
            Base::app()->controllerPath = 'controllers';
            Base::app()->viewPath = 'view';

            if(empty($params[0]))
                $c = $this->DefaultController;
            elseif(file_exists(Base::app()->controllerPath.'/'.$params[0].'Controller.php'))
                $c = $params[0];
            else
                throw new SystemException('Controller Not Found');

            if(isset($params[1]) && !empty($params[1]))
                $a = $params[1];
            else
                $a = $this->DefaultAction;

            //Remove first 2 items
            array_shift($params);
            array_shift($params);
        }

        /**
         * remove first of two param
         */
        $this->controller = $c;
        $this->action   = $a;
        $this->params       = $params;
        $this->runController($c,$a,$params);

    }

    /**
     * @param $url
     * @return array
     */
    public function Parameters($url){
        Base::log('param',$url);
        $parameters = explode("/", $url);
        $returnObject = array();
        foreach($parameters as $parameter) {
            if(!empty($parameter))
                $returnObject [] = $parameter;
        }
        Base::log('parameters',print_r($returnObject,1));
        return $returnObject;
    }

    /**
     * @param $rules
     * @param null $url
     * @return mixed|null|string
     */
    public function RuleParser($rules,$url = null){

        if($url === null)
            $url = $this->request->url;

        Base::log('RuleParser ilk',$url);

        foreach($rules as $k=>$v)
        {
            if(strpos($k,"*") !== false)
            {
                unset($rules[$k]);
                $tmp = str_replace("*","",$k);
                if(preg_match('#^'.$tmp.'#',$url))
                {
                parse_str($v,$out);
                foreach($out as $okk => $ovv)
                    Base::app()->setParam($okk,$ovv);
                return $this->RuleParser($rules,str_replace($tmp,"",$url));
                } else {
                    continue;
                }
            }
            $replace = array();
            preg_match_all('#<([\w*])>#i',$k,$pattern);
            //var_dump($pattern);
            foreach($pattern[0] as $p)
            {

                $replace[] = $p;
                $k =  str_replace($p,'(\w*)',$k);
            }
            if(preg_match_all('#^'.$k.'$#',$url,$q)){

                foreach($replace as $key=>$value)
                {
                   $v = str_replace($replace[$key],$q[$key + 1][0],$v);
                }
                str_replace($replace,$q[1],$v);
                return str_replace($replace,$q[1],$v);
            }

        }

        Base::log('RuleParser',$url);
        return $url;
    }

    /**
     * @param string $c  Controllar name
     * @param string $a  Action name
     * @param array $vars Params
     * @throws SystemException
     */
    public function runController($c,$a = 'index',$vars = array())
    {
        $n = ucfirst($c).'Controller';
        Base::app()->controller = new $n();

        $a = 'action'.ucfirst($a);
        if(method_exists($n,$a)){
            # before
            if(Base::app()->controller->beforeAction())
                call_user_func_array(array(Base::app()->controller, $a), $vars);
            # after
            Base::app()->controller->afterAction();
            Base::log('decided action', $a);
        }else {
            Base::log('action bulunamadı', $a);
            throw new SystemException('Action Bulunamadı');
        }



    }



}