<?php

use Legacy\System\Classes\DB;

class User
{
    private $data = [];

    public $id = null;

    public $username;

    public $userFirstname;

    public $userLastname;

    public $email;

    public $groupId = null;

    public $admin = false;

    public function __construct($id)
    {
        if ($row = DB::row('* FROM users WHERE id=?', [$id])) {
            $this->id = $id;
            $this->username = $row['username'] ?: $row['email'];
            $this->userFirstname = ucfirst($row['firstname']);
            $this->userLastname = ucfirst($row['lastname']);
            $this->email = strtolower($row['email']);
            $this->groupId = $row['groupId'] ?? null;
            $this->admin = $row['admin'];
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            switch ($name) {
                // basic data
                case 'firstname':
                    return $this->data['firstname'] = $this->getFirstName();
                case 'lastname':
                    return $this->data['lastname'] = $this->getLastName();
                case 'fullName':
                    return $this->data['fullName'] = $this->getFullName();
                case 'phone':
                    return $this->data['phone'] = $this->getPhone();

                    // images
                case 'icon':
                    return $this->data['icon'] = $this->icon();
            }
        }
    }

    private function getFirstName()
    {
        global $customer;

        $firstname = $this->userFirstname;

        if (
            ! $firstname &&
            isset($customer) &&
            $customer->id &&
            $customer->lastAddress->id
        ) {
            $firstname = $customer->lastAddress->firstname;
        }

        return $firstname;
    }

    private function getLastName()
    {
        global $customer;

        $lastname = $this->userLastname;

        if (
            ! $lastname &&
            isset($customer) &&
            $customer->id &&
            $customer->lastAddress->id
        ) {
            $lastname = $customer->lastAddress->Lastname;
        }

        return $lastname;
    }

    private function getFullName()
    {
        $name = [];

        if ($this->firstname) {
            $name[] = $this->firstname;
        }
        if ($this->lastname) {
            $name[] = $this->lastname;
        }

        return implode(' ', $name);
    }

    private function icon()
    {
        $icon = '/uploads/profile-icons/'.$this->id.'.jpg';

        if (! file_exists(SERVER_ROOT.$icon)) {
            $icon = '/uploads/profile-icons/0.jpg';
        }

        return $icon;
    }

    private function getPhone()
    {
        $customerId = DB::find('id FROM ecommerce_customers WHERE userId=?', [$this->id]);
        $phone = DB::find('phone FROM ecommerce_addresses WHERE customerId=? ORDER BY time DESC', [$customerId]);

        return $phone;
    }
}
