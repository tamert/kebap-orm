<?php



class SiteController extends BaseController{
    public $layout = 'layout';
    public $pageTitle;


    public function actionIndex(){

        $this->layout = 'model';

        $this->render('index', array("tamer"=>"tuna"));

    }

    public function actionLogin()
    {
        if(isset($_POST['username']))
        {
            if(Base::app()->user->auth($_POST['username'],$_POST['password']))
                Base::app()->user->login($_POST['username']);
        }
        $this->render('login');
    }

    public function actionLogout()
    {
        var_dump( (string) Base::app()->controller);
    }

	
	public function actionBugra(){
		BASE::app()->ShowLog(); //Logları görüntülemek için çağrılan function.

       //$asd = new Tahir();
        $qwe = new Fonksiyonlar();
        $zxc = new Fonksiyonlar();


        $qwe = Loader::LoadClass('Fonksiyonlar');



	}

    public function actionEnd()
    {
        var_dump(Base::app()->end);
    }

    public function actionTranslate($dil = 'tr')
    {
        echo $dil;
        var_dump(Base::app()->getParam('language'));

/*
        Base::app()->language->change(Base::app()->getParam('language'));

        echo Base::app()->getParam('language');
        echo Base::t('translate/','homepage');
*/
    }

    public function actionTheme()
    {
        $this->layout = 'layout';
        $this->render('index');
    }

    public function actioninfo()
    {

        var_dump(Base::app()->BaseUrl);

        var_dump(Base::app()->_request);
        phpinfo();
        $this->pageTitle = 'Tahiiir';

        $this->render('index');
    }
}

