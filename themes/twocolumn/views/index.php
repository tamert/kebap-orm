<h1>ActiveBase</h1>

<h4>Retrieving Your Data</h4>

<h1><?php $this->pageTitle; ?></h1>
<?php

echo '<h2>findAll</h2>';
echo '
<p>These magic functions can be used as a shortcut to search your tables by a certain field. Just add the name of the field (in MANY format) to the end of these functions, and supply the criteria for that field as the first parameter.
</p>
<pre>
foreach(Deneme::findAll() as $item){
    echo "Title : $item->title".PHP_EOL;
}
</pre>
which returns
<pre>';
foreach(Deneme::findAll() as $item){

    echo "Title : $item->title".PHP_EOL;
}
echo "</pre>";

echo '<h2>find</h2>
<p>The find magic functions also accept some optional parameters:</p>
<pre>Deneme::find(array("id"=>1)); or Deneme::find(1); or else Deneme::findId(1) (fast methods are finally) </pre>
';
$test = Deneme::find(array("id"=>1));


echo "
which returns
<pre>";
echo "Başlık :$test->title <br/>";
echo "</pre>";


echo '<h2>factory</h2>';

echo '<p>
Factory is insert, update and delete magic functions also accept some optional parameters
</p>
<strong>Insert</strong>
<pre>
    $new = Deneme::factory();
    $new->title = "New Page";
    $new->save();
</pre>
';
$new = Deneme::factory();
$new->title = "New Page";
$new->save();


$test = Deneme::factory(array("title"=>"New Page"));
echo "results :<pre>";
echo "Insert of the title : $test->title <br/>";
echo "</pre>";

$model =  Deneme::factory(array("title"=>"New Page"));


echo '
<strong>Update</strong>
<pre>
    $get = Deneme::factory(array("title"=>"New Page"))
    $get->title = "John Joe";
    $get->save();
</pre>
<strong>Delete</strong>
<pre>
    Deneme::factory(array("title"=>"John Joe"))->delete();
</pre>
<pre>
'.$model->id;
echo '</pre>';
$model->delete();

echo '<h2>new ModelName</h2>';
$test = new Deneme;
$test->title = 'Alija Izzetbegovic';
if($test->save()){
    echo 'success..<br/>';
}
echo '
<pre>
    $new = new Deneme;
    $new->title = "Alija Izzetbegovic";
    $new->save();
</pre>
';

echo '<h2>factories</h2>';
echo '<p>Birden çok kayıtın Update ve Delete işlemlerini gerçekleştirmek için kullanılır. </p>';
echo '<pre>
Deneme::factories(array("title"=>"Alija Izzetbegovic"))->delete();
</pre>';
if(Deneme::factories(array(
    array('title','=','Alija Izzetbegovic')
))->delete()){
    echo 'Deneme::factories(array)->delete() yöntemi ile toplu kıyım yapıldı bunu save() kullanırsan
            toplu update olur eğer factories boş değer girilirse hiçç olur. not: toplu sünneti belediye yapıyor.<br/>';
}


echo '<h4>Query Factory</h4>';
echo '<h2>Manuel Query Build</h2>';
echo '<pre>
Deneme::where("id","!=","1")->get();
    // SELECT * FROM deneme WHERE id!=1

Deneme::where("id","!=","1")
    ->order("title")
    ->get();
    // SELECT * FROM deneme WHERE id!=1 ORDER BY title

Deneme::where("id","!=","1")
    ->limit(1)
    ->get();
    // SELECT * FROM deneme WHERE id!=1 LIMIT 1

Deneme::where("id","!=","1")
    ->select(array("id"))
    ->get();
    // SELECT id FROM deneme WHERE id!=1

Deneme::where("id","=","21")
    ->orWhere("title", "LIKE", "%ta%")
    ->get();
    // SELECT id FROM deneme WHERE id!=21 or title LIKE %ta%
</pre>';

echo '<h2>MANY Format</h2>';
echo '
<strong>Simple Type</strong>
<pre>
1
// where id=1
</pre>
<strong>One Type</strong>
<pre>
array("id"=>1)
// where id=1
</pre>
<strong>Many One</strong>
<pre>
array(
    "id"=>5,
    array("parent_id","in",
        array(1,2,3,4,5,6,7,8,9,10)
    ),
    array("id","=","1","or")
    )
    // where id = 5 and parent_id in (1,2,3,4,5,6,7,8,9,10) or id = 1
<pre>
<p>Let`s Try</p>
<pre>
find(MANY Format,Combination)
findAll(MANY Format,Combination)
factory(MANY Format)
factories(MANY Format)
</pre>
';
?>