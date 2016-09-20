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
     * Class Page
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     */
    class Page{
        /**
         * @var PageObject[]
         */
        private $pageObjects;

        /**
         * @var array
         */
        private $headers;

        /**
         * @var string
         */
        private $contenttype;

        /**
         * constructor page
         */
        public function __construct(){
            $this->clear();
        }

        /**
         * Method to add PageObject to page
         * @param PageObject $pageObject
         * @return $this
         */
        public function add(PageObject $pageObject){
            if ($pageObject::maxInstances() == 0){
                // if maxInstances = 0 aka unlimited
                array_push($this->pageObjects, $pageObject);
                // return this class so chaining is possible
                return $this;
            }
            
            //count instances
            $instances = 0;
            $class = get_class($pageObject);
    
            foreach($this->pageObjects as $v){
                if($class == get_class($v)) $instances++;
            }
    
            if($instances < $pageObject::maxInstances()) {
                array_push($this->pageObjects, $pageObject);
            }
            // return this class so chaining is possible
            return $this;
        }
        
        /**
         * Adds header to page, header is unique
         * 
         * @param string $name Name of header
         * @param string $value Value of header
         */ 
        public function addHeader($name, $value){
            $this->headers[$name] = $value; 
        }
        
        /**
         * clear added headers
         */ 
        public function clearHeaders(){
            $this->headers = [];
        }
    
        /**
         *  Clear contents of Page
         */
        public function clearPage(){
            $this->pageObjects = [];
        }
    
        /** 
         * Clears contents and headers and resets content type
         */ 
        public function clear(){
            $this->contentType('text/html');
            $this->clearHeaders();
            $this->clearPage();
        }

        /**
         * Method to set content type and get current contenttype
         * @param null|string $contenttype
         * @return string
         */
        public function contentType($contenttype = null){
            if ($contenttype != null){
                $this->contenttype = $contenttype;
                $this->addHeader('Content-Type', $contenttype);
            } 
            return $this->contenttype;
        }

        /**
         * Method to do stuff page need to do
         */
        public function execute(){
            foreach ($this->headers as $k => $v) {
                header("$k: $v");
            }
            foreach ($this->pageObjects as $v) {
                $v->display();
            }

        }
    }