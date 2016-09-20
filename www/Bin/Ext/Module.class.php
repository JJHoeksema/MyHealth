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
    namespace DMF\Ext;

    use \DMF\Events\Event;
    /**
     * Class Module
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Ext
     */
    abstract class Module{
        
        /**
         * Method to get name of Module
         * @returns string the name of the module
         */
        public abstract function getName();
        /**
         * Method to check if Module is a library
         * @return boolean  is this module a library
         */
        public abstract function isLibrary();
        /**
         * Method to get the default route
         * @return array the main route
         */
        public abstract function getMainRoute();

        /**
         * method to handle an event
         * @param Event $event
         * @return bool has handled event
         */
        public function handleEvent(Event $event){ return false; }
    }