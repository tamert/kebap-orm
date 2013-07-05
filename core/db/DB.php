<?php

/**
 * Class db
 */
class DB {

    /**
     * @var null
     */
    public static
        $db;
    public
        $fetchMode = PDO::FETCH_OBJ, $statement, $nquery = 0;

    /**
     * @var array
     */
    public $debugs;

    public function callquery( $query=null,$bindings=array())
    {
        try{
            $this->statement = self::$db->prepare($query);
            $this->statement->execute($bindings);
            $this->nquery++;
            return $this->statement;
        }  catch (PDOStatement $e) {
            /**
             * @todo : hatayı alamadım yaala...
             */
            throw new SystemException("Database Error: " . $e->getMessage() . "  ", "db");
        }
    }

    /**
     * @param null $query
     * @param array $selects
     * @return mixed
     * db::getSelect
     */
    public function callgetSelect($query=null,$selects=array())
    {
        return $this->callquery($query,$selects)->fetchColumn(0);
    }

    /**
     * @param null $query
     * @param array $bindings
     * @param bool $fetchMode
     * @return mixed
     */
    public function callgetOne($query=null,$bindings=array(),$fetchMode = true)
    {
        if(!$fetchMode)
            $this->fetchMode = PDO::FETCH_ASSOC;
        return $this->callquery($query,$bindings)->fetch($this->fetchMode );
    }

    /**
     * @param null $query
     * @param array $bindings
     * @param null $key
     * @param null $value
     * @param bool $fetchMode
     * @return array
     */
    public function callgetMany( $query = null, $bindings=array(), $key = null, $value = null,$fetchMode = true )
    {
        if(!$fetchMode)
            $this->fetchMode = PDO::FETCH_ASSOC;
        $rows = array();
        if( $result = $this->callquery($query,$bindings)->fetchALL($this->fetchMode) ){
            if( !$key )
                return $result;
            elseif( !$value )
                foreach( $result as $row )
                    $rows[ $row[$key] ] = $row;
            else
                foreach( $result as $row )
                    $rows[ $row[$key] ] = $row[$value];
        }
        return $rows;
    }

    /**
     * @return mixed
     */
    public function callgetLastId()
    {
        /*
         * Son id
         * PDO::lastInsertId()
         */
        return self::$db->lastInsertId();
    }

    /**
     * @param int $fetchMode
     */
    public function callsetFetchMode( $fetchMode = PDO::FETCH_ASSOC )
    {
        $this->fetch_mode = $fetchMode;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        /**
         * call config array
         */
        if(!self::$db)
            self::$db = Connection::init(base::app()->config["dbBase"]);

        return call_user_func_array(array(new DB, 'call'.$name), $arguments);
    }


    public function debug()
    {

    }

    public function setBug()
    {

    }


}
