<?php

// load modules
load_module('logout');

if (isset($_POST['submit'])) {

    logout();
}
