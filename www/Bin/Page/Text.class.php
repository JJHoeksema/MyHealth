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
     * Class Text
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     */
    class Text extends PageObject{

        /**
         * @var mixed
         */
        private $text;
        /**
         * @var bool
         */
        private $escapeHTML;

        /**
         * constructor Text
         * @param mixed $text text to display
         * @param bool $escapeHTML escape html characters
         */
        public function __construct($text, $escapeHTML = true){
            $this->text = $text;
            $this->escapeHTML = $escapeHTML;
        }

        /**
         *  Override render function
         * @return string contents of render
         */
        public function render(){
            if ($this->escapeHTML) $this->text = htmlspecialchars($this->text);
            return $this->text;
        }

        /**
         * @return int maximum number of instances  (if 0 no maximum);
         */
        public static function maxInstances(){
            return 0;
        }
}