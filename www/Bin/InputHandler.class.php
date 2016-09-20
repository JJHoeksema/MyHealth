<?php
/*
 *  DMF: Data Modeler Framework
 *  Copyright (C) 2015  Marwijnn de Kuijper
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
    namespace DMF;

    /**
     * Class InputHandler, handles different types of input
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF
     */
    class InputHandler{
        
        private $uri;
        private $params;
        private $gets;
        private $posts;
        private $cookies;
        
        public function __construct($uri){
            $this->uri = $uri;
            $this->cookies = $this->posts = $this->gets = $this->params = [];

            $tmp = explode("/", $uri);
            unset($tmp[0]);                     // remove domain name;
            if(isset($tmp[1])) unset($tmp[1]);  // remove module if it is set
            if(isset($tmp[2])) unset($tmp[2]);  // remove controller if it is set
            if(isset($tmp[3])) unset($tmp[3]);  // remove action if it is set
            
            foreach($tmp as $value){
                if ($value == "") continue;
                if(is_numeric($value)){
                    $this->params[] = (is_float($value + 0)? floatval($value) : intval($value));
                }else  $this->params[] = $value;
            }

            $lastIndex = count($this->params) - 1;
            if($lastIndex >= 0){
                if(!is_int($this->params[$lastIndex])){
                    if ($this->params[$lastIndex][0] == "?"){
                        unset($this->params[$lastIndex]);
                    }
                }
            }

            foreach($_GET as $key => $value){
                if($value == "") continue;
                if(is_numeric($value)){
                    $this->gets[$key] = (is_float($value + 0)? floatval($value) : intval($value));
                }else $this->gets[$key] = $value;
            }

            foreach($_POST as $key => $value){
                if($value == "") continue;
                if(is_numeric($value)){
                    $this->posts[$key] = (is_float($value + 0)? floatval($value) : intval($value));
                }else $this->posts[$key] = $value;
            }

            foreach($_COOKIE as $key => $value){
                if($value == "") continue;
                if(is_numeric($value)){
                    $this->cookies[$key] = (is_float($value + 0)? floatval($value) : intval($value));
                }else $this->cookies[$key] = $value;
            }
        }

        /**
         * This Method will return the parameters given in the url
         * @param int $index
         * @return mixed null if index does not exist, mixed variable if index is set
         */
        public function arg($index, $default = null, $sanatize = true){
            if(!isset($this->params[$index])) return $default;
            return $this->params[$index];
        }

        /**
         * This Method will return all parameters
         * @return array
         */
        public function allArgs(){
            return $this->params;
        }

        /**
         * This Method will return a get value
         * @param string $index
         * @return mixed null if index does not exist, mixed variable if index is set
         */
        public function get($index, $default = null, $sanatize = true){
            if(!isset($this->gets[$index])) return $default;
            if($sanatize == false) return $_GET[$index];
            return $this->gets[$index];
        }


        /**
         * This Method will return all get values
         * @return array
         */
        public function allGets(){
            return $this->gets;
        }

        /**
         * This Method will return a post value
         * @param string $index
         * @return mixed null if index does not exist, mixed variable if index is set
         */
        public function post($index, $default = null, $sanatize = true){
            if(!isset($this->posts[$index])) return $default;
            if($sanatize == false) return $_POST[$index];
            return $this->posts[$index];
        }

        /**
         * This Method will return all post value
         * @return array
         */
        public function allPosts(){
            return $this->posts;
        }

        /**
         * This Method will return a cookie value
         * @param string $index
         * @return mixed null if index does
         */
        public function cookie($index){
            if(!isset($this->cookies[$index])) return null;
            return $this->cookies[$index];
        }

        /**
         * This Method will return all cookie values
         * @return array
         */
        public function allCookies(){
            return $this->posts;
        }

        /**
         * This Method will return a fileObject
         * @todo implement fileObject
         * @param string $index
         * @return null
         */
        public function file($index){
            return null;
        }
        

        
    }