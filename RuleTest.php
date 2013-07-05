<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pc
 * Date: 24.04.2013
 * Time: 20:19
 * To change this template use File | Settings | File Templates.
 */

$rules = array(
    'urunler/<s>.html'=>'urunler/<s>',
    'urunler/kategori.html'=>'urunler/category',
    'tahir'=>'falanca',
    'urunler/<id>/<urun>.html' => 'urunler/show/<id>'

);

$go = '';

$url = $_GET['url'];
foreach($rules as $k=>$v)
{
    preg_match_all('#<(.*?)>#i',$k,$pattern);
    foreach($pattern[0] as $p)
    {
        $k =  str_replace($p,'(.*?)',$k);
    }

    if(preg_match('#^'.$k.'$#',$url,$q))
        $go = $v;

}

echo '<h1>'.$go . '</h1>';

