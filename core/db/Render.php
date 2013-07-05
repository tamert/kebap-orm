<?php

/**
 * Class Render
 */
class Render
{


    /**
     * @var array
     */
    protected $areas = array(
        'aggregate','selects', 'from', 'joins', 'wheres',
        'groupings', 'orderings', 'limit', 'offset',
    );

    /**
     * @var string
     */
    protected $wrapper = '%s';


    /**
     * @param $generate
     * @return string
     */
    public function start($generate)
    {
        return $this->concatenate($this->components($generate));
    }

    /**
     * @param $generate
     * @return array
     */
    final protected function components($generate)
    {


        foreach ($this->areas as $component)
        {

            if ( ! is_null($generate->$component))
            {
                $sql[$component] = call_user_func(array($this, $component), $generate);
            }
        }

        return (array) $sql;
    }

    /**
     * @param $components
     * @return string
     */
    final protected function concatenate($components)
    {
        return implode(' ', array_filter($components, function($value)
        {
            return (string) $value !== '';
        }));
    }

    /**
     * @param $generate
     * @return string
     */
    protected function selects($generate)
    {
        //if ( ! is_null($generate->aggregate)) return;

        $select = ($generate->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';

        return $select.$this->columnize($generate->selects);
    }

    /**
     * @param $generate
     * @return string
     */
    protected function aggregate($generate)
    {
        $column = $this->columnize($generate->aggregate['columns']);

        if ($generate->distinct and $column !== '*')
        {
            $column = 'DISTINCT '.$column;
        }

        return 'SELECT '.$generate->aggregate['aggregator'].'('.$column.') AS '.$this->wrap('aggregate');
    }

    /**
     * @param $generate
     * @return string
     */
    protected function from($generate)
    {
        return 'FROM '.$this->wrap_table($generate->from);
    }

    /**
     * @param $generate
     * @return string
     */
    protected function joins($generate)
    {

        foreach ($generate->joins as $join)
        {
            $table = $this->wrap_table($join->table);

            $clauses = array();

            foreach ($join->clauses as $clause)
            {
                extract($clause);

                $column1 = $this->wrap($column1);

                $column2 = $this->wrap($column2);

                $clauses[] = "{$connector} {$column1} {$operator} {$column2}";
            }

            $search = array('AND ', 'OR ');

            $clauses[0] = str_replace($search, '', $clauses[0]);

            $clauses = implode(' ', $clauses);

            $sql[] = "{$join->type} JOIN {$table} ON {$clauses}";
        }

        return implode(' ', $sql);
    }

    /**
     * @param $generate
     * @return bool|string
     */
    final protected function wheres($generate)
    {
        if (is_null($generate->wheres)) return '';

        foreach ($generate->wheres as $where)
        {
            $sql[] = $where['connector'].' '.$this->{$where['type']}($where);
        }

        if  (isset($sql))
        {
            return 'WHERE '.preg_replace('/AND |OR /', '', implode(' ', $sql), 1);
        }
        return false; // look again
    }

    /**
     * @param $where
     * @return string
     */
    protected function where($where)
    {
        $parameter = $this->parameter($where['value']);

        return $this->wrap($where['column']).' '.$where['operator'].' '.$parameter;
    }

    /**
     * @param $where
     * @return string
     */
    protected function where_in($where)
    {
        $parameters = $this->parameterize($where['values']);

        return $this->wrap($where['column']).' IN ('.$parameters.')';
    }

    /**
     * @param $where
     * @return string
     */
    protected function where_not_in($where)
    {
        $parameters = $this->parameterize($where['values']);

        return $this->wrap($where['column']).' NOT IN ('.$parameters.')';
    }

    /**
     * @param $where
     * @return string
     */
    protected function where_between($where)
    {
        $min = $this->parameter($where['min']);
        $max = $this->parameter($where['max']);

        return $this->wrap($where['column']).' BETWEEN '.$min.' AND '.$max;
    }

    /**
     * Compile a WHERE NOT BETWEEN clause
     * @param  array $where
     * @return string
     */
    protected function where_not_between($where)
    {
        $min = $this->parameter($where['min']);
        $max = $this->parameter($where['max']);

        return $this->wrap($where['column']).' NOT BETWEEN '.$min.' AND '.$max;
    }

    /**
     * @param $where
     * @return string
     */
    protected function where_null($where)
    {
        return $this->wrap($where['column']).' IS NULL';
    }

    /**
     * @param $where
     * @return string
     */
    protected function where_not_null($where)
    {
        return $this->wrap($where['column']).' IS NOT NULL';
    }

    /**
     * @param $where
     * @return mixed
     */
    final protected function where_raw($where)
    {
        return $where['sql'];
    }

    /**
     * @param $generate
     * @return string
     */
    protected function groupings($generate)
    {
        return 'GROUP BY '.$this->columnize($generate->groupings);
    }

    /**
     * @param $generate
     * @return string
     */
    protected function havings($generate)
    {
        if (is_null($generate->havings)) return '';

        foreach ($generate->havings as $having)
        {
            $sql[] = 'AND '.$this->wrap($having['column']).' '.$having['operator'].' '.$this->parameter($having['value']);
        }

        return 'HAVING '.preg_replace('/AND /', '', implode(' ', $sql), 1);
    }

    /**
     * @param $generate
     * @return string
     */
    protected function orderings($generate)
    {
        foreach ($generate->orderings as $ordering)
        {
            $sql[] = $this->wrap($ordering['column']).' '.strtoupper($ordering['direction']);
        }

        return 'ORDER BY '.implode(', ', $sql);
    }

    /**
     * @param $generate
     * @return string
     */
    protected function limit($generate)
    {
        return 'LIMIT '.$generate->limit;
    }

    /**
     * @param $generate
     * @return string
     */
    protected function offset($generate)
    {
        return 'OFFSET '.$generate->offset;
    }

    // Dockers tim -> Bize Docker diyenler kendileri dockers'ten giyiniyor
    /**
     * @param $columns
     * @return string
     */
    final public function columnize($columns)
    {
        return implode(', ', array_map(array($this, 'wrap'), $columns));
    }

    /**
     * @param $value
     * @return string
     */
    public function wrap($value)
    {

        if ($value instanceof Render)
        {
            return $value->get();
        }


        if (strpos(strtolower($value), ' as ') !== false)
        {
            $segments = explode(' ', $value);

            return sprintf(
                '%s AS %s',
                $this->wrap($segments[0]),
                $this->wrap($segments[2])
            );
        }

        $segments = explode('.', $value);

        foreach ($segments as $key => $value)
        {
            if ($key == 0 and count($segments) > 1)
            {
                $wrapped[] = $this->wrap_table($value);
            }
            else
            {
                $wrapped[] = $this->wrap_value($value);
            }
        }

        return implode('.', $wrapped);
    }

    /**
     * @param $value
     * @return string
     */
    protected function wrap_value($value)
    {
        return ($value !== '*') ? sprintf($this->wrapper, $value) : $value;
    }

    /**
     * @param $table
     * @return string
     */
    public function wrap_table($table)
    {

        if ($table instanceof Render)
        {
            return $this->wrap($table);
        }

        $prefix = '';

        return $this->wrap($prefix.$table);
    }

    /**
     * @param $values
     * @return string
     */
    final public function parameterize($values)
    {
        return implode(', ', array_map(array($this, 'parameter'), $values));
    }

    /**
     * @param $value
     * @return string
     */
    final public function parameter($value)
    {
        return ($value instanceof Render) ? $value->get() : '?';
    }

    # Factory : Start

    /**
     * @param $table
     * @param $values
     * @return string
     */
    public function insert($table, $values)
    {
        $table = $this->wrap_table($table);

        if ( ! is_array(reset($values)))
            $values = array($values);

        $columns = $this->columnize(array_keys(reset($values)));

        $parameters = $this->parameterize(reset($values));

        $parameters = implode(', ', array_fill(0, count($values), "($parameters)"));

        return "INSERT INTO {$table} ({$columns}) VALUES {$parameters}";
    }

    /**
     * @param $render
     * @return string
     */
    public function update($render)
    {

        $table = $this->wrap_table($render->from);

        foreach ($render->_values as $column => $value):
            $columns[] = $this->wrap($column).' = '.$this->parameter($value);
        endforeach;

        $columns = implode(', ', $columns);
        return trim("UPDATE {$table} SET {$columns} ".$this->wheres($render));
    }

    /**
     * @param $render
     * @return string
     */
    public function delete($render)
    {

        $table = $this->wrap_table($render->from);
        return trim("DELETE FROM {$table} ".$this->wheres($render));
    }

}