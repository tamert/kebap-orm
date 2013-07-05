<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pc
 * Date: 23.04.2013
 * Time: 18:23
 * To change this template use File | Settings | File Templates.
 */

class Deneme extends ActiveBase
{

    // tanımlı değilse modelin adına bakar istersen silde dene :)
    public function tableName()
    {
        return 'deneme';
    }

    /**
    // çalışan fonksiyonlar
    public function  beforeSave(){
    echo "beforeSave ---<br>";
    }

    public function  afterSave(){
    echo "afterSave ---<br>";
    }

    public function  beforeDelete(){
    echo "beforeDelete ---<br>";
    }

    public function  afterDelete(){
    echo "afterDelete ---<br>";
    }
     */


    public function belongsTo()
    {
        return array(
            'parent' => array('deneme', 'parent_id')
        );
    }

    public function hasMany()
    {
        return array(
            'children' => array('deneme', 'parent_id')
        );
    }

    public function hasOne()
    {
        return array(
            'elemanlar' => array('elemanlar', 'deneme_id'),
        );
    }



}