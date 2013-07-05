<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 27.04.2013
 * Time: 20:22
 * To change this template use File | Settings | File Templates.
 */

class UserComponent extends Component
{
    public $isLoggedIn = false;
    public $id;

    public function init()
    {
        Base::log('User',get_class($this) . ' init');
        session_start();

        if(isset($_SESSION['user']))
        {
            $this->isLoggedIn = $_SESSION['user']->isLoggedIn;
            $this->id = $_SESSION['user']->id;
        }
    }

    public function auth($username,$password)
    {
        if($username == 'tahir' && $password == '123')
            return true;
        else
            return false;
    }

    public function login($username)
    {
        $this->id = $username;
        $this->isLoggedIn = true;
        $_SESSION['user'] = $this;
    }

    public function getId()
    {
        return 'Tahir';
    }
}