<?php

/**
 * Class Generate
 */
class Generate
{

    /**
     * @var null
     */
    public static $tableName;

    /**
     * @var
     */
    public static $modelName;

    /**
     * @var array
     */
    public $selects;

    /**
     * @var array
     */
    public $wheres;

    /**
     * @var array
     */
    public $bindings;

    /**
     * @var array
     */
    public $groupings;

    /**
     * @var array
     */
    public $orderings;

    /**
     * @var array
     */
    public $partitions;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var null
     */
    public $from;

    /**
     * @var
     */
    public $joinType;

    /**
     * @var
     */
    public $joins;

    /**
     * @var bool
     */
    public $distinct = false;

    /**
     * @var
     */
    public $aggregate;

    /**
     * start render
     * @var object
     */
    private $render;

    /**
     * Factory Attributes
     * @var array
     */
    public $_values = array();

    /**
     * Factory Control
     * @var bool
     */
    public $factoryControl = false;

    # Factory : start
    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if($this->factoryControl)
            $this->_values[$key] = $value;
    }

    /**
     * @param $values
     * @return $this
     */
    public function reflect($values){
        $this->factoryControl = true;
        $this->_values= array_merge($values, $this->_values);
        return $this->save();
    }

    /**
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        if($this->factoryControl)
            return $this->_values[$key];
        else
            return null;
    }



    /**
     * @param null $arg
     * @param bool $all
     * @return $this
     */
    public function factory($arg = null,$all = false)
    {

        $this->factoryControl = true;

        if (! is_null($arg))
        {
            if(is_array($arg))
                $this->many($arg);
            else
                $this->where('id', '=', $arg);

            if(!$all){
                $this->select(array('*'));
                $comp = DB::getOne($this->render->start($this),$this->bindings, false);
                $this->_values = array_merge($comp,$this->_values);
            }

        }
        return $this;
    }

    /**
     * @param $arg
     * @return $this
     */
    public function factories($arg)
    {
        $this->factory($arg,true);
        return $this;
    }

    # Factory : End

    /**
     * @return $this|bool
     */
    public function save()
    {
        call_user_func(array(new self::$modelName, "beforeSave"));

        $copy = $this->bindings;

        $this->bindings = (array) $this->bindings;
        $this->bindings =  array_merge(array_values($this->_values), $this->bindings);

        if(is_null($this->wheres)){
            /**
             * @todo: belki önceden ID açılır Update moduna girilebilir emin değilim
             */
            $comp = DB::query($this->render->insert($this->from, $this->_values),$this->bindings);
            // insert
        } else {

            $comp = DB::query($this->render->update($this),$this->bindings);
            // update
        }

        $this->bindings = $copy;

        if($comp!==false)
        {
            call_user_func(array(new self::$modelName, "afterSave"));
            return $this;
        }

            return false;
    }

    public function delete(){
        call_user_func(array(new self::$modelName, "beforeDelete"));

        $render = $this->render->delete($this);
        $comp = DB::query($render,$this->bindings);
        if($comp!==false)
        {
            call_user_func(array(new self::$modelName, "AfterDelete"));
            return $comp;
        }

        return false;
    }

    # Factory : End

    public function __construct()
    {
        $this->render = new Render;
        $this->from = self::$tableName;
    }

    /**
     * @param $tableName
     * @param $modelName
     */
    public static function init($tableName,$modelName)
    {
        self::$tableName = strtolower($tableName);
        self::$modelName = $modelName;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function select($columns = array('*'))
    {
        $this->selects = (array)$columns;
        return $this;
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findId($id, $columns = array('*'))
    {
        return $this->where('id', '=', $id)->first($columns);
    }

    /**
     * import all where function
     * @param null $params
     * @param array $columns
     * @return array|bool
     * @TODO: devam edilecek, eksik olanlari listele
     */
    public function many($params = null, $columns = array('*'))
    {

        /*if(is_object($params)){
           @todo: tartisma konusu / dökümantasyon MANY Format
        }*/

        if (is_null($params)) {
            return $this->select($columns)->get();

        } elseif (is_array($params)) {
            foreach ($params as $item => $element) {

                if (!is_array($element)) {
                    // The only parameter array field
                    $this->where($item, "=", $element);

                } else {
                    // The many parameters array fields
                    if (isset($element[0]) && isset($element[1]) && isset($element[2])) {

                        $column = trim($element[0]);

                        $operator = trim(strtoupper($element[1]));

                        $value = $element[2];

                        $connector = (!isset($element[3])) ? 'AND' : trim(strtoupper($element[3]));

                        switch ($operator) {
                            case 'IN':
                                $this->whereIn($column, $value, $connector);
                                break;
                            case 'NOT_IN':
                                $this->whereIn($column, $value, $connector, true);
                                break;
                            case 'BETWEEN':
                                $min = (isset($value[0])) ? $value[0] : 0;
                                $max = (isset($value[1])) ? $value[0] : 0;
                                $this->whereBetween($column, $min, $max, $connector);
                                break;
                            case 'NOT_BETWEEN':
                                $min = (isset($value[0])) ? $value[0] : 0;
                                $max = (isset($value[1])) ? $value[0] : 0;
                                $this->whereBetween($column, $min, $max, $connector, true);
                                break;
                            default:
                                switch ($connector) {
                                    case 'OR':
                                        $this->orWhere($column, $operator, $value);
                                        break;
                                    case 'AND':
                                        $this->where($column, $operator, $value);
                                        break;
                                }
                        }
                    }
                }
            }
        }
        return $this;
    }


    /**
     * @return mixed
     */
    public function first()
    {
        $results = $this->limit(1)->get();
        return $results;
    }

    /**
     * All Combination Function
     * @param $comb
     * @return $this
     */

    public function comb($comb){

        $comb = (array)$comb;

        foreach ($comb as $key=>$item) {
            switch (strtolower($key)) {
                case 'select':
                $this->select($item);
                break;
                case 'order':
                    if(!empty($item) && is_array($item))
                        $this->order($item[0],$item[1]);
                    else
                        $this->order($item);
                break;
                case 'group':
                $this->group($item);
                break;
                case 'limit':
                $this->limit($item);
                break;
                case 'offset':
                $this->offset($item);
                break;
                case 'join':
                    if(!empty($item) && is_array($item)){
                        $table = (isset($item[0]))  ? $item[0] : null;
                        $column1 = (isset($item[1]))  ? $item[1] : null;
                        $operator = (isset($item[2]))  ? $item[2] : null;
                        $type = (isset($item[3]))  ? $item[2] : 'INNER';
                        $this->join($table,$column1,$operator,$type);
                    }
                break;
            }
        }

        return $this;
    }


    /**
     * Main Where Function
     * @param $column string
     * @param null $operator string
     * @param null $value string
     * @param string $connector string // yerine gore "OR" gelecek
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $connector = 'AND')
    {

        $type = 'where';

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'connector');

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Helper Where Function
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * Helper Where Function
     * @param $column
     * @param $values
     * @param string $connector
     * @param bool $not
     * @return $this
     */
    public function whereIn($column, $values, $connector = 'AND', $not = false)
    {
        $type = ($not) ? 'where_not_in' : 'where_in';

        $this->wheres[] = compact('type', 'column', 'values', 'connector');

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Helper Where Function
     * @param $column
     * @param $min
     * @param $max
     * @param string $connector
     * @param bool $not
     * @return $this
     */
    public function whereBetween($column, $min, $max, $connector = 'AND', $not = false)
    {
        $type = ($not) ? 'where_not_between' : 'where_between';

        $this->wheres[] = compact('type', 'column', 'min', 'max', 'connector');

        $this->bindings[] = $min;
        $this->bindings[] = $max;

        return $this;
    }

    /**
     * Helper Where Function
     * @param $column
     * @param string $connector
     * @param bool $not
     * @return $this
     */
    public function whereNull($column, $connector = 'AND', $not = false)
    {
        $type = ($not) ? 'where_not_null' : 'where_null';

        $this->wheres[] = compact('type', 'column', 'connector');

        return $this;
    }

    /**
     * Helper Where Function
     * @param $column
     * @param bool $not
     * @return $this
     */
    public function orWhereNull($column, $not = false)
    {
        return $this->whereNull($column, 'OR', $not);
    }



    /**
     * Limit Render Function
     * @param $value
     * @return $this
     */
    public function limit($value)
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * Order Render Function
     * @param $column
     * @param string $direction
     * @return $this
     */
    public function order($column, $direction = 'asc')
    {
        $this->orderings[] = compact('column', 'direction');
        return $this;
    }

    /**
     * Group Render Function
     * @param $column
     * @return $this
     */
    public function group($column)
    {
        $this->groupings[] = $column;
        return $this;
    }

    /**
     * Offset Render Function
     * @param $value
     * @return $this
     */
    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * @param $table
     * @param $column1
     * @param null $operator
     * @param null $column2
     * @param string $type
     * @return $this
     */
    public function join($table, $column1, $operator = null, $column2 = null, $type = 'INNER')
    {

        if ($column1 instanceof ActiveBase)
        {
            $this->joins[] = new Join($type, $table);

            call_user_func($column1, end($this->joins));
        }
        else
        {
            $join = new Join($type, $table);

            $join->on($column1, $operator, $column2);

            $this->joins[] = $join;
        }

        return $this;
    }

    /**
     * @param $table
     * @param $column1
     * @param null $operator
     * @param null $column2
     * @return $this
     */
    public function leftJoin($table, $column1, $operator = null, $column2 = null)
    {
        return $this->join($table, $column1, $operator, $column2, 'LEFT');
    }

    /**
     * Get Generate and arguments
     * @param array $columns
     * @return array
     */
    public function get($columns = array('*'))
    {


        if (is_null($this->selects)) $this->select($columns);

        if($this->limit==1)

            $present = DB::getOne($this->render->start($this),$this->bindings);

        else

            $present = DB::getMany($this->render->start($this),$this->bindings);

        $this->bindings =array();

        $Relations = call_user_func(array(new self::$modelName, "Relations"));

        var_dump($Relations);
        var_dump($present);

        return $present;
    }

}