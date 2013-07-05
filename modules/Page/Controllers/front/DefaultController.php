<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 28.04.2013
 * Time: 01:30
 * To change this template use File | Settings | File Templates.
 */

class DefaultController extends BaseController{

    public function actionIndex()
    {
        echo 'Page Module';
    }

    public function actionShow($id)
    {
        echo $id;
        echo 'Sayfa Göster';
    }


}