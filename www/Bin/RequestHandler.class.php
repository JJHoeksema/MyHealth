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

    use DMF\Events\StatusChangeEvent;
    /**
     * Class RequestHandler, handles requests
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF
     */
    class RequestHandler{
        /**
         * @var string
         */
        private $host;
        /**
         * @var string
         */
        private $uri;
        /**
         * @var App
         */
        private $app;
        /**
         * @var Ext\Module|null
         */
        private $module;

        private $controller;
        private $action;

        /**
         * Constructor
         * @param string $host domain or IP
         * @param string $uri
         */
        public function __construct($host, $uri){
            $this->host = $host;
            $this->uri = $uri;
            $this->app = App::getInstance();
            $this->module = $this->app->getFirstNavigatableModule();
        }

        /**
         * Method to route the request
         */
        public function route(){
            $tmp = explode("/", $this->uri);
            // uri for -> /module/controller/action
            
            // Route Module
            if( isset($tmp[1]) && !empty($tmp[1]) ){
                // if module name is in uri
                if(!$this->app->moduleNavigatable($tmp[1])) {
                    //if not navigatable, activate StatusChangeEvent with status code 404 (NOT FOUND)
                    $this->app->activateEvent(new Events\StatusChangeEvent(404, 
                                    "Could not find navigatable path: Module Not Found"));
                    return;   
                }
                $this->module = $this->app->getModule($tmp[1]);
            }else{
                // if module name is not in uri use first registered and navigatable module
                if( $this->module == null ) {
                    // if module is null, activate StatusChangeEvent with status code 500 (Internal server Error)
                    $this->app->activateEvent(new Events\StatusChangeEvent(500, 
                                    "There are no registered modules that are navigatable"));
                    return;
                }
            }
            
            $route = $this->module->getMainRoute();
            
            // route controller
            if( isset($tmp[2]) && !empty($tmp[2]) ){
                // if controller name is in uri
                $this->controller = $this->getController($tmp[2]);
                if($this->controller == null){
                    // if not navigatable, activate StatusChangeEvent with status code 404 (NOT FOUND)
                    $this->app->activateEvent(new Events\StatusChangeEvent(404,
                                "Could not find navigatable path: Controller Not Found"));
                    return;
                }
            }else{
                // if controller name is not in uri, then get default controller from main route in module
                $this->controller = $this->getController($route["controller"]);
                if($this->controller == null){
                    //if controller doesn't exist, activate StatusChangeEvent with status code 500 (Internal Server Error)
                    $this->app->activateEvent(new Events\StatusChangeEvent(500,
                                "Invalid controller in main route of module '{$this->module->getName()}'"));
                    return;
                }
            }
            
            // route action
            if( isset($tmp[3]) && !empty($tmp[3]) ){
                // if action name is in uri
                $this->action = $this->getAction($tmp[3]);
                if($this->action == null){
                    // if action doesn't exist, activate StatusChangeEvent with status code 404(NOT FOUND)
                    $this->app->activateEvent(new Events\StatusChangeEvent(404,
                                "Could not find navigatable path: Action Not Found'"));
                    return;
                }
                if(!$this->action->isPublic()){
                    $this->app->activateEvent(new Events\StatusChangeEvent(404,
                                "Could not find navigatable path: Action Not Navigatable'"));
                    return;
                }
                
            }else{
                // if action name is not in uri, then get default action from main route in module
                $this->action = $this->getAction($route["action"]);
                if($this->action == null){
                    // if action doesn't exist, activate StatusChangeEvent with status code 500(Internal Server Error)
                    $this->app->activateEvent(new Events\StatusChangeEvent(500,
                                "Invalid action in main route of module '{$this->module->getName()}'"));
                    return;
                }
                
            }
            
            // call action
            try{
                $this->action->invoke($this->controller->newInstance());
            }catch(\Exception $e){
                App::getInstance()->activateEvent(new StatusChangeEvent(500, '<b>'. $e->getMessage() .'</b>: ' . $e->getTraceAsString()));
            }
        
            return;
        }
        
        private function getController($controllerName){
            $class = null;
            try {
                // Try to create a reflection class (SEE PHP Doc: http://php.net/manual/en/class.reflectionclass.php)
                $class = new \ReflectionClass($this->module->getName() . '\\controller\\' . $controllerName . 'Controller');
                if(!$class->isSubclassOf('DMF\\Ext\\Controller')) return null;
            }catch(\ReflectionException $e){
                // if controller doesn't exist
                return null;
            }
            return $class;
        }

        private function getAction($action){
            $method = null;
            try{
                /** @noinspection PhpUndefinedMethodInspection */
                $method = new \ReflectionMethod($this->controller->getName(), $action);
            }catch(\ReflectionException $e){
                // if method doesn't exist
                return null;
            }
            return $method;
        }

        /**
         * Method to check if is an secure request
         * @return bool
         */
        public function isSecure(){
             return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        }

        /**
         * redirects to  current location
         */
        public function refresh(){
            $path = "http" . (($this->isSecure()) ? "s" : "") .  "://" . $this->host.$this->uri;
            $this->app->getPage()->addHeader("location", $path);
        }

        /**
         * redirects to given location
         * @param string $path location
         * @param bool $local
         */
        public function redirect($path = '', $local = true){
            if($local) $path = $this->host .'/' . $path;
            $this->app->getPage()->clear();
            $path = $this->getProtocol() .  "://" . $path;
            $this->app->getPage()->addHeader("location", $path);
        }

        /**
         * Method to get the hostname
         * @return string
         */
        public function getHost(){
            return $this->host;
        }

        /**
         * Method to get the uri
         * @return string
         */
        public function getURI(){
            return $this->uri;
        }

        /**
         * Method to return the protocol
         * @return string "http" or "https"
         */
        public function getProtocol(){
            return "http" . (($this->isSecure()) ? "s" : "");
        }
    }