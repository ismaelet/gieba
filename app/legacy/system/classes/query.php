<?php

class Query
{
    public $res = false;

    public $num_rows = 0;

    public function __construct($stmt)
    {

        if ($stmt->num_rows) {

            $this->num_rows = $stmt->num_rows;

            $res = [];
            $row = [];
            $params = [];

            $fields = $stmt->result_metadata();

            while ($field = $fields->fetch_field()) {
                $params[] = &$row[$field->name];
            }

            $stmt->bind_result(...$params);

            $i = 0;
            while ($stmt->fetch()) {

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
