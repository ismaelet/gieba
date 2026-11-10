<?php

namespace Structure;

trait ConstructFromDatabase
{
    public function __construct(?int $id = null)
    {
        if ($dbData = \Legacy\System\Classes\DB::row('* FROM '.self::TABLE.' WHERE '.self::INDEX.' = ?', [$id])) {
            foreach ($dbData as $field => $value) {
                $this->{$field} = $value;
            }
        }
    }
}
