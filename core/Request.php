<?php

class Request {

    private $vars = array();


    public function __construct(){

    Base::log('Request','Started');
    $this->end      = Base::app()->end;
    $this->base     = str_replace('\\', '/', dirname(getenv('SCRIPT_NAME')) );
    $this->method   = getenv('REQUEST_METHOD') ? : 'GET';
    $this->referrer = getenv('HTTP_REFERER') ?: '';
    $this->ip       = getenv('REMOTE_ADDR') ?: '';
    $this->ajax     = getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
    $this->scheme   = getenv('SERVER_PROTOCOL') ?: 'HTTP/1.1';
    $this->user_agent = getenv('HTTP_USER_AGENT') ?: '';
    $this->body     = file_get_contents('php://input');
    $this->type     = getenv('CONTENT_TYPE') ?: '';
    $this->length   = getenv('CONTENT_LENGTH') ?: 0;
    $this->secure   = getenv('HTTPS') && getenv('HTTPS') != 'off';
    $this->accept   = getenv('HTTP_ACCEPT') ?: '';

    $this->url      = isset($_GET['url'])? $_GET['url'] : '';
    $this->get      = $this->parseGet();

    Base::log('Request','Parsed');
    }

    protected function parseGet()
    {
        $array = $_GET;
        unset($array['url']);
        return  $array;
    }

    public function __get($name){
        if(isset($this->vars[$name]))
        return $this->vars[$name];
    }

    public function __set($name,$value){
        $this->vars[$name] = $value;
        return $this->vars[$name];
    }
}