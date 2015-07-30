<?php

/* * ********************************************************************
 *  OpenSRS - Simple Wrapper For OpenSRS API
 * *
 *
 *
 *  CREATED BY Tucows Co       ->        http://www.opensrs.com
 *  CONTACT                    ->        reseller.support@tucows.com
 *  Version                    ->        1.0 
 *  Release Date               ->        Oct 2/2012
 *
 *
 * Copyright (C) 2012 by Tucows Co/OpenSRS.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * ******************************************************************** */

if(!class_exists('openSRS_base'))
    require_once 'openSRS_base.php';
if(!class_exists('openSRS_crypt'))
    require_once 'openSRS_crypt.php';
if(!class_exists('openSRS_ops'))
    require_once 'openSRS_ops.php';




/**
 * Simple wrapper to set up base settings
 */
if (!class_exists('OpenSRS')) {

    class OpenSRS {

        private $errors = array();
        private $infos = array();
        private $language = 'english';
        private $lang = array();
        private $success = false;
        protected $_opsHandler;
        protected $connect;

        /**
         * Set up default settings.
         * @param type $username
         * @param type $password
         * @param type $apikey
         * @param type $live 
         */
        public function __construct($username, $password, $apikey, $live = 0) {
            // modified by Maks Aloksa max@modulesgarden.com
            $this->connect = new stdClass();
            $this->connect->connect->osrs_username = $username;
            $this->connect->connect->osrs_password = $password;
            $this->connect->connect->osrs_key = $apikey;
            $this->connect->connect->osrs_environment = $live ? 'LIVE' : 'TEST';
            $this->connect->connect->osrs_protocol = 'XCP';
            $this->connect->connect->osrs_host = $live ? 'rr-n1-tor.opensrs.net' : 'horizon.opensrs.net';
            $this->connect->connect->osrs_port = 55443;
            $this->connect->connect->osrs_sslport = 55443;
            $this->connect->connect->osrs_baseclassversion = '1.0';
            $this->connect->connect->osrs_version = '1.0';
            

            // OpenSRS reseller username
            
            $this->_opsHandler = new openSRS_ops();
        }

        /**
         * Prepare data before send and parse response from the server
         * @param type $array
         * @return type 
         * 
         * 
         * edited by max@modulesgarden.com
         */
        public function send($array) {
            $this->success = false;
            if (!isset($array['protocol'])) {
                $array['protocol'] = $this->connect->connect->osrs_protocol;
            }

            try {
                $xml = $this->_opsHandler->encode($array);
                
                set_error_handler("osrsError", E_USER_WARNING);
                
                $base = new OpenSRS_base($this->connect);
                $res = $base->send_cmd($xml);
                
                restore_error_handler();
                
                $arr = $this->_opsHandler->decode($res);
            } catch (Exception $e) {
                $this->addError($e->getMessage());
                return false;
            }
            if ($arr['is_success'] == 1) {
                $this->addInfo($arr['response_text']);
                $this->success = 1;
            } else {
                if (isset($arr['error_details']) && count($arr['error_details'])) {
                    $this->addError($arr['response_text'] . '. ' . $arr['error_details'][0]['error_detail']);
                } else {
                    $this->addError($arr['response_text']);
                }
            }

            return $arr;
        }

        public function isSuccess() {
            return $this->success;
        }

        //INFOS
        private function addInfo($info) {
            $this->infos[] = $info;
        }

        public function getInfo() {
            if ($this->hasInfo())
                return $this->infos[0];
            return false;
        }

        public function hasInfo() {
            return (bool) $this->infos;
        }

        //ERRORS
        private function addError($error) {
            $this->errors[] = $error;
        }

        public function getError() {
            if ($this->hasError())
                return $this->errors[0];
            return false;
        }

        public function hasError() {
            return (bool) $this->errors;
        }

    }

}
