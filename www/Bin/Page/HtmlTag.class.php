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
     * Class HtmlTag
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     */
    class HtmlTag extends PageObject{

        private $tag;
        /**
         * @var array
         */
        private $attributes;

        /**
         * @var \DMF\Page\PageObject[]
         */
        private $pageObjects;

        /**
         * HtmlTag Constructor
         * @param string $tagName (a, p, h1, h2, ...)
         */
        public function __construct($tagName){
            $this->tag = $tagName;
            $this->attributes = [];
            $this->pageObjects = [];

        }

        /**
         * Method to add a PageObject
         * @param PageObject $pageObject
         * @return self HtmlTag
         * @throws \Exception
         */
        public function add(PageObject $pageObject){
            if ($pageObject::maxInstances() == 0){
                array_push($this->pageObjects, $pageObject);
                return $this;
            }
            throw new \Exception("Can only add PageObjects with limitless instances");
        }

        /**
         * Method to add an html attribute
         * @param $name
         * @param $value
         */
        public function addAttribute($name, $value){
            $this->attributes[$name] = $value;
        }

        /**
         * @return int maximum number of instances  (if 0 no maximum);
         */
        public static function maxInstances(){
            return 0;
        }
        /**
         * method to display the start of the HtmlTag
         */
        protected function displayStart(){
            $result =  "<{$this->tag}";
            foreach($this->attributes as $k => $v){
                $result .= " $k =\"$v\"";
            }
            return $result . ">";
        }

        /**
         * Method to display the end of the Htmltag
         */
        protected function displayEnd(){
            return "</{$this->tag}>";
        }

        /**
         *  Override render function
         * @return string contents of render
         */
        public function render(){
            $result = $this->displayStart();
            foreach($this->pageObjects as $object){
                $result .= $object->render();
            }
            $result .= $this->displayEnd();
            return $result;
        }

    }