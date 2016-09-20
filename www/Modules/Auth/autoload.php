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
namespace Auth;

use DMF\App;
use DMF\Events\StatusChangeEvent;
/**
 * autoload method
 * @param string $className The name of the class to load
 */
function autoload($className) {
    $tmp = explode("\\", $className);
    $filePath = dirname(__FILE__). DIRECTORY_SEPARATOR;
    if($tmp[0] == __NAMESPACE__) {
        unset($tmp[0]);
        $filePath .= implode(DIRECTORY_SEPARATOR, $tmp). ".class.php";
    }else return;

    try {
        if(!file_exists($filePath)) {
            App::getInstance()->activateEvent(new StatusChangeEvent(500, "Could not load File"));
        } else {
            /** @noinspection PhpIncludeInspection */
            require($filePath);
        }
    } catch (\Exception $e) {
        echo $e->getMessage() . "<br />";
    }


}

spl_autoload_register(__NAMESPACE__ .'\autoload');
?>