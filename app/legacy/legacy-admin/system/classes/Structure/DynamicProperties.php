<?php

namespace Structure;

trait DynamicProperties
{
    private $data = [];

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            $getMethod = 'get'.ucfirst($name);

            if (method_exists(self::class, $getMethod)) {
                return $this->data[$name] = $this->$getMethod();
            } else {
                error_log('Class '.self::class.' method '.$getMethod.' doesn\'t exits');

                return null;
            }
        }
    }

    public function resetDynamicProperties()
    {
        $this->data = [];
    }

    private function reset()
    {
        $this->data = [];
        $this->__construct();
    }
}
