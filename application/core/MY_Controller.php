<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        log_message('debug', "MY_Controller Initialized");
    }

}