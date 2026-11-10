<?php

class Query
{
    public $stmt = false;

    public $res = false;

    public $num_rows = 0;

    public function __construct($stmt)
    {

        $this->stmt = $stmt;

        if ($this->stmt->num_rows) {

            $this->num_rows = $this->stmt->num_rows;

            $res = [];
            $row = [];
            $params = [];

            $fields = $this->stmt->result_metadata();

            while ($field = $fields->fetch_field()) {
                $params[] = &$row[$field->name];
            }

            $this->stmt->bind_result(...$params);

            $i = 0;
            while ($this->stmt->fetch()) {

                $j = 0;
                foreach ($row as $value) {
                    $res[$i][array_keys($row)[$j]] = $value;
                    $j++;
                }

                $i++;
            }

            $this->res = $res;

        }
    }

    // get result
    public function get_result()
    {
        return new Query_Result($this);
    }
}

class Query_Result
{
    public $res = false;

    public $num_rows = 0;

    public function __construct($stmt)
    {

        $this->res = $stmt->res;
        $this->num_rows = $stmt->num_rows;
    }

    // fetch rows
    public function fetch_assoc()
    {

        if ($this->res) {

            $row = array_shift($this->res);

            return $row;

        } else {
            return false;
        }
    }
}
