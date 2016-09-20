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
    namespace DMF\Page;

    /**
     * Class Template
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     */
    class Template extends PageObject{

        /**
         * @var string
         */
        private $path;
        /**
         * @var PageObject[]
         */
        private $pageObjects;
        /**
         * @var string
         */
        private static $TEMPLATEDIR;

        /**
         * @param string $templateName name of template
         * @throws \Exception
         */
        public function __construct($templateName) {
            if (Template::$TEMPLATEDIR == null){
                Template::$TEMPLATEDIR = dirname(dirname(dirname(__FILE__))) .
                    DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;
            } 
            $path = Template::$TEMPLATEDIR . implode(DIRECTORY_SEPARATOR, explode('/', $templateName)) . '.template';
            if (!file_exists($path)){
                throw new \Exception("invalid Template");
            }
            $this->path = $path;
            $this->pageObjects = [];
        }

        /**
         * Method to add pageObject to template
         * @param string $name template variable name
         * @param PageObject $pageObject
         * @param bool $concat if false override
         * @return $this
         */
        public function add($name, PageObject $pageObject, $concat = true){
            $name = strtolower($name);
            if (array_key_exists($name, $this->pageObjects) && $concat){
                /** @noinspection PhpParamsInspection */
                array_push($this->pageObjects[$name], $pageObject);
            }else {
                $this->pageObjects[$name] = [$pageObject];
            }
            return $this;
        }

        /**
         * @return int maximum number of instances  (if 0 no maximum);
         */
        public static function maxInstances(){
            return 0;
        }

        /**
         *  Override render function
         * @return string contents of render
         */
        public function render(){
            $result = "";
            $parsed = explode('{{', file_get_contents($this->path));
            $result .= $parsed[0];
            for($i = 1; $i < count($parsed); $i++){
                $tmp = explode('}}', $parsed[$i]);
                if(array_key_exists(strtolower($tmp[0]), $this->pageObjects)){
                    foreach($this->pageObjects[strtolower($tmp[0])] as $pageObject){
                        /** @var PageObject $pageObject */
                        $result .= $pageObject->render();
                    }
                }
                $result .= $tmp[1];
            }
            return $result;
        }
    }