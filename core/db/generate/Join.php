<?php
/**
 * Class Join
 */
class Join {

    /**
     * @var
     */
    public $type;

    /**
     * @var
     */
    public $table;

    /**
     * @var array
     */
    public $clauses = array();

    /**
     * @param $type
     * @param $table
     */
    public function __construct($type, $table)
    {
        $this->type = $type;
        $this->table = $table;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     * @param string $connector
     * @return $this
     */
    public function on($column1, $operator, $column2, $connector = 'AND')
    {
        $this->clauses[] = compact('column1', 'operator', 'column2', 'connector');

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     * @return $this
     */
    public function or_on($column1, $operator, $column2)
    {
        return $this->on($column1, $operator, $column2, 'OR');
    }

}