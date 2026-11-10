<?php

use Legacy\System\Classes\DB;

class User
{
    public $id;

    public $username;

    public $fname;

    public $lname;

    public $mail;

    public $admin;

    public $manager;

    public $level;

    public $status;

    public $buyerID;

    public $cart_count;

    public $privacy;

    public function __construct($id = 0)
    {

        // set->id
        $this->id = $id;

        // set e-commerce data
        if (DB::table('ecommerce_carts')) {
            $this->buyerID = ! $this->id && isset($_SESSION['buyerID']) ? $_SESSION['buyerID'] : $this->id;
            $this->cart_count = $this->buyerID ? DB::find('quantity FROM ecommerce_carts WHERE buyerID=? AND status=0', [$this->buyerID]) : 0;
        }

        // fetch data
        if (DB::table('users') && $row = DB::row('* FROM users WHERE id=?', [$id])) {

            // set basic data
            $this->username = $row['username'];
            $this->fname = $row['firstname'];
            $this->lname = $row['lastname'];
            $this->mail = $row['mail'];
            $this->admin = $row['admin'];
            $this->manager = $this->admin == 2;
            $this->status = $row['status'] ?? false;
            $this->level = $row['level'] ?? null;
            $this->privacy = $row['privacy'] ?? null;
        }
    }
}
