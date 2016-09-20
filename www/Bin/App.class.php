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

    use DMF\Events\Event;
    use DMF\Ext\Module;
    use DMF\Page\Page;
    /**
     * Class App, the core of every DMF application
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF
     */
    class App {
        /**
         * @var App
         */
        private static $instance;
        /**
         * @var string
         */
        private static $ROOTDIR;
        /**
         * @var Module[]
         */
        private $registeredModules;
        /**
         * @var RequestHandler
         */
        private $requestHandler;
        /**
         * @var InputHandler
         */
        private $inputHandler;
        /**
         * @var Page
         */
        private $page;

        /**
         * private constructor
         */
        private function __construct(){
            // set root directory of application
            App::$ROOTDIR = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
            $this->registeredModules = array();
            $this->page = new Page();
        }

        /**
         * destructor
         */
        public function __destruct(){
            $this->page->execute();
        }

        /**
         * Method to get the page
         * @return Page
         */
        public function getPage(){
            return $this->page;
        }

        /**
         * Method to get the request handler
         * @return RequestHandler
         */
        public function getRequestHandler(){
            return $this->requestHandler;
        }

        /**
         * Method to get the InputHandler
         * @return InputHandler
         */
        public function getInputHandler(){
            return $this->inputHandler;
        }

        /**
         * get an instance of App
         * @return \DMF\App
         */
        public static function getInstance(){
            if (App::$instance == null) App::$instance = new App();
            return App::$instance;
        }

        /**
         * Method to handle a specific request with given host and URI
         * @param string $host
         * @param string $uri
         */
        public function handleRequest($host, $uri){
            $this->inputHandler = new InputHandler($uri);
            $this->requestHandler = new RequestHandler($host, $uri);
            $this->requestHandler->route();
        }

        /**
         * Method to register a module
         * @param string $moduleName folder name of Module
         * @param null|string $alias
         * @throws \Exception
         */
        public function registerModule($moduleName, $alias = null) {
            // if no alias is set alias is moduleName
            $alias = strtolower(($alias == null)?$moduleName:$alias);
            // check if it is inside registeredModules array
            if (array_key_exists($alias, $this->registeredModules)) return;

            // set directory to location {root directory}/Modules/{name of module}
            $dir = App::$ROOTDIR . 'Modules' . DIRECTORY_SEPARATOR . $moduleName;
            $modulePath = $dir . DIRECTORY_SEPARATOR . "main.module";
            if(file_exists($dir)){
                // if the directory exists
                if(!file_exists($modulePath)){
                    // if the main.module doesn't exists in directory
                    throw new \Exception("Module '$moduleName' is missing 'main.module'");
                }
                /** @noinspection PhpIncludeInspection */
                require_once($modulePath);
                try {
                    // Try to create a reflection class (SEE PHP Doc: http://php.net/manual/en/class.reflectionclass.php)
                    $class = new \ReflectionClass($moduleName . '\main');
                }catch(\ReflectionException $e){
                    // if Module doesn't exist
                    throw new \Exception("Module '$moduleName' can't be initialized");
                }

                if(!$class->isSubclassOf('DMF\\Ext\\Module')){
                    // if not a subclass of abstract class 'Module'
                    throw new \Exception("Module '$moduleName' has invalid 'main.module' file");
                }
                // add new instance
                $this->registeredModules[$alias] = $class->newInstance();
                return;
            }
            throw new \Exception("Module '$moduleName' could not be Registered");
        }

        /**
         * Method to check if module is registered
         * @param string $moduleName
         * @return bool
         */
        public function moduleRegistered($moduleName){
            //string to lower, so it is the same as in registerModule()
            $moduleName = strtolower($moduleName);
            //return module is in array 'registeredModules'
            return array_key_exists($moduleName, $this->registeredModules);
        }

        /**
         * Method to get module
         * @param string $moduleName
         * @return Module|null
         */
        public function getModule($moduleName){
            //string to lower, so it is the same as in registerModule()
            $moduleName = strtolower($moduleName);
            // return null if module is not registered,
            // return the module if it is registered
            if(!$this->moduleRegistered($moduleName)) return null;
            return $this->registeredModules[$moduleName];

        }

        /**
         * Method to check if the given module is navigatable (is not a library)
         * @param string $moduleName
         * @return bool
         */
        public function moduleNavigatable($moduleName){
            if(!$this->moduleRegistered($moduleName)) return false;
            return !$this->getModule($moduleName)->isLibrary();
        }

        /**
         * Method to search the first navigatable Module (first module that is not a library)
         * @return Module|null the first registered navigatable module or null if not found
         */
        public function getFirstNavigatableModule(){
            foreach($this->registeredModules as $module){
                if (!$module->isLibrary()) return $module;
            }
            return null;
        }

        /**
         * Method to activate an event (calls all eventHandlers of Modules)
         * @param Event $event
         */
        public function activateEvent(Event $event){
            foreach($this->registeredModules as $module){
                $module->handleEvent($event);
            }
        }
    }