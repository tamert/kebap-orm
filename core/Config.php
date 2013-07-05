<?php
/**
 * Global ayar dosyası
 *
 * Application nesnesinin @link Application::config özelliğinden
 * erişilebilir.
 *
 * Örn: $app->config veya Base::app()->config
 * Örn2: Base::app()->config['urlRules']
 */
/**
 * @todo: default end ?
 */
return array(
    /*
    'urunler/<s>.html'=>'urunler/<s>',
    'urunler/kategori.html'=>'urunler/category',
    'tahir'=>'falanca',
    'urunler/<id>/<urun>.html' => 'urunler/show/<id>'
     */
    'end'=>'front',


    'urlRules' => array(
        'en/*'=>'language=en&end=front',
        'panel/*'=>'language=fr',
        'login'=>'site/login',
        'log'=>'site/logger',
        '<w>.html' => 'page/default/show/<w>',
        'page/<w>'=>'page/default/<w>',
        '<m>/<w>.html'=>'<m>/default/show/<w>'

    ),
    'dbBase'=>array(
        'type' => 'mysql',
        'conf' => array(

            'name' => 'kebap',
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'pass' => 'root',
            'charset' => 'utf8'

        )
    ),
    'components'=>array(
        'logger'=>array(
            'class'=>'SimpleLog',
            'trace'=>false
        ),
        'user'=>array(
            'class'=>'UserComponent'
        ),
        'language'=>array(
            'class'=>'Language'
        ),
        'theme'=>array(
            'class'=>'Theme',
            'name'=>'twocolumn'
        )
    )
);