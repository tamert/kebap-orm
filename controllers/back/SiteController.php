<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 27.04.2013
 * Time: 18:34
 * To change this template use File | Settings | File Templates.
 */

class SiteController extends BaseController{

    public function actionIndex()
    {
        echo 'Backend işindesiniz';
        $this->createUrl('');
    }
}