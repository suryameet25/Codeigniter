<?php

class authpool {

    var $CI;
    var $_authpool = array();
    var $session_expire = 14400;

    function __construct() {

        $this->CI = & get_instance();
        // Load the saved session
        if ($this->CI->session->userdata('user_info') !== FALSE || $this->CI->session->userdata('user_info') != '') {
            $this->_authpool = $this->CI->session->userdata('user_info'); //save session info to variable

        } else {
            // Or init a new session
            $this->_init_properties();
        } 
        
         
    }

    private function _init_properties() { 
        $this->_authpool = false; 
    }

    //check login and redirect according to that
    function check_login($query = false) {
        //$redirect is used if redirect to any user after login.
        //This will set flashdata if not logged in
        // if no session, no expiry, or expiry is less than this time
        if (!$this->_authpool || !$this->_authpool->expire || $this->_authpool->expire < time()) { //no session exists
            $this->destroy();
                        
            if (!empty($query['redirect'])) {
                $this->CI->session->set_flashdata('redirect', $query['redirect']);
            }

            if (!empty($query['default_redirect']) && !in_array(uri_string(), $this->CI->config->item('no_login'))) {
                redirect($this->CI->config->item('login_page'), 'refresh');
            }
            return false;
        } else { // session exists
            $this->_authpool->expire = time() + $this->session_expire; //update the session
            $this->save($this->_authpool);
            return $this->_authpool;
        }
    }

    function is_admin() {
        if ($this->_authpool && $this->_authpool->type=='admin' ) {
            return true;
        } else {
            return false;
        }
    }

    
    function user() {

        if (!$this->_authpool) {
            return false;
        } else {
            return $this->_authpool;
        }
    }

    // Saves customer data in the 
    function save($data) {
        $this->_authpool = $data;
        $this->CI->session->sess_expiration = time() + 14400; // expires in 4 hours
        $this->CI->session->set_userdata('user_info', $this->_authpool);
    }

    /**
     * Destroy  the user
     *

     */
    function destroy() {
        $this->_init_properties();
        $this->CI->session->unset_userdata('user_info');
    }

}