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
     * Class Select
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     */
    class Select extends PageObject{
        /**
         * @var string|null
         */
        private $selected;
        /**
         * @var array
         */
        private $options;
        /**
         * @var bool
         */
        private $sanitize;
        /**
         * @var array
         */
        private $attributes;

        /**
         * Constructor select
         * @param null|array $options options
         * @param null|mixed $selected selected value
         */
        public function __construct($options = null, $selected = null){
            $this->selected = $selected;
            $this->attributes = [];
            if(is_array($options)) $this->options = $options;
            $this->sanitize = true;
        }

        /**
         * Method to add an Option
         * @param string $value
         * @param string $text
         */
        public function addOption($value, $text){
            $this->options[$value] = $text;
        }

        /**
         * Method to add an attribute
         * @param string $name
         * @param string $value
         */
        public function addAttribute($name, $value){
            $this->attributes[$name] = $value;
        }

        /**
         * Method to set the selected value
         * @param string $value
         */
        public function setSelected($value){
            $this->selected = $value;
        }

        /**
         * Method to set sanitation
         * @param $bool
         */
        public function setSanitize($bool){
            $this->sanitize = $bool;
        }

        /**
         *  Override render function
         * @return string contents of render
         */
        public function render(){
            $result = '<select';
            foreach($this->attributes as $name => $value){
                $result .= " $name = \"$value\"";
            }
            $result .= '>';
            foreach($this->options as $value => $text){
                $result .= "<option value=\"$value\"";
                if($this->selected == $value) $result .= ' selected';
                if($this->sanitize) $text = htmlspecialchars($text);
                $result .= ">$text</option>";
            }
            return $result . "</select>";
        }

        /**
         * @return int maximum number of instances  (if 0 no maximum);
         */
        public static function maxInstances(){
            return 0;
        }
        
    }