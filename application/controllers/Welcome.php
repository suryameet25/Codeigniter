<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index() {
        $this->load->view('welcome_message');
    }

    function hello() {
        echo 'hello';
    }

    function update_3() {
        
    }

    function welcome1() {
        
    }

}
