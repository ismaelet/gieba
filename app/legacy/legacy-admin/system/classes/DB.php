<?php

namespace Legacy\System\Classes;

use Query;

class DB
{
    // generic query
    public static function execute($query, $values = false)
    {

        global $con;

        if ($values) {
            $values = (array) $values;
        }

        // prepare the query
        $stmt = $con->prepare($query);

        // execute the query
        if ($stmt) {
            $types = [];

            if (! empty($values)) {
                foreach ($values as &$value) {
                    switch (true) {
                        case $value && strval($value)[0] == 0:
                            $types[] = 's';
                            break;

                        case is_int($value):
                            $types[] = 'i';
                            break;

                        case is_numeric($value):
                            $types[] = 'd';
                            break;

                        default:
                            $types[] = 's';
                            $value = htmlspecialchars($value);
                    }
                }

                $stmt->bind_param(implode('', $types), ...$values);
            }

            $stmt->execute();
            $stmt->store_result();

            return new Query($stmt);
        } else {
            return false;
        }
    }

    // select several rows
    public static function select($query, $values = false)
    {

        $query = 'SELECT '.$query;
        $res = self::execute($query, $values);

        if ($res) {
            return $res->get_result();
        } else {
            return false;
        }
    }

    // select a single row
    public static function row($query, $values = false)
    {

        $query = $query.' LIMIT 1';

        $res = self::select($query, $values);

        return $res->fetch_assoc();
    }

    // select a specific value
    public static function find($query, $values = false)
    {

        $what = substr($query, 0, strpos($query, ' FROM'));

        $row = self::row($query, $values);

        if ($row) {
            return $row[$what];
        } else {
            return false;
        }
    }

    // get a count of entries
    public static function count($query, $values = false)
    {

        $query = 'COUNT(*) AS num FROM '.$query;

        return self::row($query, $values)['num'];
    }

    // get a sum of values
    public static function sum($query, $values = false)
    {

        $what = substr($query, 0, strpos($query, ' FROM'));
        $query = substr($query, strpos($query, 'FROM ') + 5);

        $query = 'SUM('.$what.') AS tot FROM '.$query;

        return self::row($query, $values)['tot'];
    }

    // add an element
    public static function add($query, $values = [])
    {

        global $con;

        $query = 'INSERT INTO '.$query;

        if (! strpos($query, 'VALUES') && strpos($query, '(')) {
            $placeholders = [];

            if ($values) {
                for ($i = 1; $i <= count($values); $i++) {
                    $placeholders[] = '?';
                }
            }

            $query .= ' VALUES ('.implode(',', $placeholders).')';
        }

        if (self::execute($query, $values)) {
            return $con->insert_id;
        } else {
            return false;
        }
    }

    // update an element
    public static function update($query, $values = false)
    {

        $query = 'UPDATE '.$query;

        return self::execute($query, $values);
    }

    // delete an element
    public static function delete($query, $values = false)
    {

        $query = 'DELETE FROM '.$query;

        return self::execute($query, $values);
    }

    // check if an element exists
    public static function exists($query, $values = false)
    {

        $query = '1 FROM '.$query.' LIMIT 1';

        $res = self::select($query, $values);

        return $res->num_rows;
    }

    // check if a table exists
    public static function table($tbl)
    {

        global $con;

        $query = "SHOW TABLES LIKE '".$tbl."'";

        return $con->query($query)->num_rows;
    }
}
