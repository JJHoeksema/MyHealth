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
     * Class Json
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     */
    class Json extends PageObject{
        private $json;

        public function __construct($array){
            $this->json = $array;
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
            return json_encode($this->json);
        }
    }