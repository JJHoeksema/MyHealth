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
    namespace DMF\Data;
    use DMF\Data\Specifier;
    use DMF\Data\Specifier\SpecifierObject;
    use DMF\Data\Specifier\WhereCheck;
    /**
     * Class MySQLDatabase
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Data
     */
    class MySQLDatabase extends DataObject{

        private $sqliInstance;
        private $host, $user, $pass;
        private $databases;

        /**
         * Construct MySQLDatabase
         * @param string $host
         * @param string $username
         * @param string $password
         * @throws \Exception
         */
        public function __construct($host = "localhost", $username = "h", $password = "h"){
            mysqli_report(MYSQLI_REPORT_STRICT);
            $config = new \Db_config();
            $this->host = $config->getHost();
            $this->user = $config->getUsername();
            $this->pass = $config->getPw();
            $this->databases = [];
            try {
                try {
                    // Try to create new SQLiInstance
                    $this->sqliInstance = new \mysqli($this->host, $this->user, $this->pass);
                }catch(\Exception $e){
                    // If there is an error with creating a new instance throw an exception
                    throw new \Exception("Check your credentials in '" . $e->getTrace()[1]["file"] . "' on Line: " . $e->getTrace()[1]["line"]);
                }
                // get all available databases
                $db = $this->query("SHOW DATABASES");
                if(!is_bool($db)){
                    // if result is not a boolean, so nothing has gone wrong
                    foreach ($db as $v) {
                        foreach($v as $string) array_push($this->databases, $string);
                    }
                }
            }catch(\Exception $e){
                // if anything has gone wrong during creating of instance
                throw new \Exception("Could not connect to MySQL database: " . $e->getMessage());
            }
        }

        /**
         * Method to map the tables, columns and their references of all found or a specific database
         * @param null|string $db name of database
         * @return array
         * @throws \Exception if mapping failed
         */
        public function map($db = null){
            $map = [];
            foreach($this->databases as $v){
                // USE {databasename} sets the given database as database to use
                if(!$this->query("USE `" . $v . "`")) throw new \Exception("Mapping failed: Check your permissions (Query 'USE')");
                if($db != $v && $db != null) continue;
                $map[$v] = [];

                // SHOW TABLES lists the tables of the selected database
                $tables = $this->query("SHOW TABLES");
                if ($tables == false && !is_array($tables)) throw new \Exception("Mapping failed: Check your permissions (Query 'SHOW TABLES'");

                foreach($tables as $table){
                    foreach($table as $tableName){
                        $map[$v][$tableName] = [];
                        // SHOW COLUMNS FROM { table name} returns all the columns of the given table
                        $columns = $this->query("SHOW COLUMNS FROM `" . $tableName . "`");
                        foreach($columns as $column){
                            $column["Reference"] = null;
                                // in information_schema data about tables is saved,
                                // therefore you can check for references in this table
                                $tmp = $this->query("SELECT *
                                                                      FROM information_schema.KEY_COLUMN_USAGE ke
                                                                      WHERE ke.referenced_table_name IS NOT NULL
                                                                       AND ke.TABLE_SCHEMA = '$v'
                                                                       AND ke.TABLE_NAME = '$tableName'
                                                                       AND ke.COLUMN_NAME = '{$column["Field"]}'");
                                // count results if not null get first row, else return null
                                $column["Reference"] = (count($tmp) != 0)?$tmp[0]: null;
                            array_push($map[$v][$tableName], $column);
                        }
                    }
                }
            }

            return $map;
        }

        private function dataType($type){
            $type = explode('(', $type)[0];
            $type = strtoupper($type);
            switch($type){
                case "INT":
                case "CHAR":
                case "BIGINT":
                case "SMALLINT":
                case "DECIMAL":
                case "DOUBLE":
                case "FLOAT":
                case "FLOAT UNSIGNED":
                    return "num";
                case "TINYINT":
                    return "bool";
                case "TEXT":
                case "LONGTEXT":
                case "MEDIUMTEXT":
                case "VARCHAR":
                    return "text";
                case "DATE":
                    return "date";
                case "DATETIME":
                case "TIMESTAMP":
                    return "dateTime";
                case "TIME":
                    return "time";
                case "SET":
                case "ENUM":
                    return "enum";
                case "LONGBLOB":
                case "BLOB":
                    return "binary";
                default:
                    throw new \Exception("Type '$type' has not (yet) been implemented");
                    break;
            }
        }

        /**
         * Generate a model
         * @param string $db name of the database
         * @param null|string $tableName name of the table
         * @return array all models generated
         * @throws \Exception if generation failed
         */
        public function generateModel($db, $tableName = null){
            $models = [];
            $map = $this->map($db);
            if(!isset($map[$db])) throw new \Exception("Database '$db' could not be found");

            foreach($map[$db] as $table => $tableval){
                if($table != $tableName && $tableName != null ) continue;
                $models[$table] = [];
                $models[$table]['Database'] = $db;
                $models[$table]['Table'] = $table;
                $models[$table]['References'] = [];
                $models[$table]['Columns'] = [];
                foreach($tableval as $column){
                    $models[$table]['Columns'][$column['Field']] = $this->dataType($column['Type']);
                    if ($column['Reference'] != null){
                        $references = [];
                        $references['Column'] = $column['Field'];
                        $references['ReferenceDatabase']    = $column['Reference']['REFERENCED_TABLE_SCHEMA'];
                        $references['ReferenceTable']       = $column['Reference']['REFERENCED_TABLE_NAME'];
                        $references['ReferenceColumn']      = $column['Reference']['REFERENCED_COLUMN_NAME'];
                        $models[$table]['References'][$column['Reference']['CONSTRAINT_NAME']] = $references;
                    }
                }

            }

            return $models;
        }

        /**
         * Method to query the database
         * @param string $string the query text
         * @return bool|array the result of the query
         */
        public function query($string){
            $result = $this->sqliInstance->query($string);
            if (is_bool($result)) return $result;
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        private function convertCompareToString($tbl, $compare, $check, $value){
            $value = $this->sqliInstance->real_escape_string($value);
            switch($check){
                case DSWC_NOTEQUAL:                 return ("`$tbl`.`$compare`" .  (($value == null)? "NOT IS NULL" : ' ~= ' ."'$value'"));
                case DSWC_EQUAL:                    return ("`$tbl`.`$compare`" .  (($value == null)? "IS NULL" : ' = '  . "'$value'"));
                case DSWC_GREATER:                  return ("`$tbl`.`$compare`" . ' > '  . (($value == null)? "NULL" : "'$value'"));
                case (DSWC_GREATER | DSWC_EQUAL):   return ("`$tbl`.`$compare`" . ' >= ' . (($value == null)? "NULL" : "'$value'"));
                case DSWC_SMALLER:                  return ("`$tbl`.`$compare`" . ' < '  . (($value == null)? "NULL" : "'$value'"));
                case (DSWC_SMALLER | DSWC_EQUAL):   return ("`$tbl`.`$compare`" . ' <= ' . (($value == null)? "NULL" : "'$value'"));
                case DSWC_LIKE:                     return ("`$tbl`.`$compare`" . ' LIKE ' . (($value == null)? "NULL" : "'$value'"));
                case DSWC_NOTLIKE:                  return ("`$tbl`.`$compare`" . ' NOT LIKE ' . (($value == null)? "NULL" : "'$value'"));
                default:                            return ("");
            }
        }

        private function hasDatabase($dbName){
            foreach($this->databases as $database){
                if($dbName == $database) return true;
            }
            return false;
        }

        private function handleWhereSpecifier(Specifier\Where $specifierObject){
            $blocks = [];
            foreach($specifierObject->getWhereBlocks() as $whereBlock){
                $block = [];
                foreach($whereBlock as $whereCheck){
                    /** @var WhereCheck $whereCheck */
                    $block[] = $this->convertCompareToString($specifierObject->getModel()->getTable(),
                                                             $whereCheck->getcolumn(),
                                                             $whereCheck->getCheck(),
                                                             $whereCheck->getValue());
                }
                $blocks[] = '(' . implode(' AND ', $block) . ')';
            }
            return implode(' OR ', $blocks);
        }

        //TODO: simplify function
        private function generateFromJoin($models){
            $references = [];
            foreach($models as $model){
                /** @var DataModel $model */
                foreach($model->getDataReferences() as $referenceName => $reference){
                    if ($reference['Table'] ==  $reference['ReferenceTable']) continue;
                    $references[$referenceName] = $reference;
                    $references[$referenceName]['Table'] = $model->getTable();
                }
            }

            $tmp = [];
            foreach($references as $key => $reference){
                foreach($models as $model) {
                    if ($reference['ReferenceTable'] == $model->getTable()) $tmp[$reference['ReferenceTable']] = $reference;
                }
            }
            $references = $tmp;

            //TODO: resolve connections

            $tables = [];
            $joinedTables = [];

            $JOIN = [];

            foreach($references as $reference){
                $tables[] = $tbl = $reference['Table'];
                $joinedTables[] = $tables[] =  $joinTbl = $reference['ReferenceTable'];
                $refCol = $reference['ReferenceColumn'];
                $col = $reference['Column'];
                $join  = "LEFT OUTER JOIN `$joinTbl` ";
                $join .= "ON (`$joinTbl`.`$refCol` = `$tbl`.`$col`)";
                $JOIN[] = $join;
            }

            $tables = array_unique($tables);
            foreach($joinedTables as $joinedtable){
                foreach($tables as $key => $table){
                    if($joinedtable == $table) unset($tables[$key]);
                }
            }



            if(count($tables) > 1) throw new \Exception("Could not join all tables, link is missing");
            $from = null;
            if(count($tables) == 1){
                reset($tables);
                $from = $tables[key($tables)];
            }else{
                reset($joinedTables);
                $from = $joinedTables[key($joinedTables)];
            }
            return "`$from` " . implode(' ', $JOIN);
        }

        /**
         * Method to automatically select multiple tables from a MySQLDatabase
         * @param DataModel|DataModel[] $models
         * @param array $columns
         * @param SpecifierObject[]|SpecifierObject|null $specifierObjects
         * @return array|bool
         * @throws \Exception
         */
        public function multiSelect($models, $columns = null, $specifierObjects = null){
            if (!(is_array($specifierObjects) || $specifierObjects == null)) $specifierObjects = [$specifierObjects];
            if (count($models) == 1) $models = $models[0];
            if (!is_array($models)) return  $this->select($models, $columns, $specifierObjects);

            /** @var DataModel[] $models */
            $db = $models[0]->getDataBase();
            $table = null;
            $parsedColumns = [];

            if($columns == null){
                foreach($models as $model){
                    if($model instanceof DataModel == false) throw new \Exception("Invalid model provided");
                    $tbl = $model->getTable();
                    foreach($model->getColumns() as $column => $type){
                        $parsedColumns[] = "`$tbl`.`$column` AS '$tbl-$column'";
                    }
                }
            }else{
                /** @var DataModel[] $tblModel */
                $tblModel = [];
                foreach($models as $model){
                    $tblModel[$model->getTable()] = $model;
                }
                foreach($columns as $tbl => $columnArray){
                    foreach($columnArray as $column){
                        if(!$tblModel[$tbl]->hasColumn($column)){
                           throw new \Exception("Column '$column' cannot be found");
                        }
                        $parsedColumns[] = "`$tbl`.`$column` AS '$tbl-$column'";
                    }
                }


            }
            $parsedColumns = implode(", ", $parsedColumns);

            $where = [];
            $sort = [];
            if ($specifierObjects != null){
                foreach($specifierObjects as $specifierObject){
                        if ($specifierObject instanceof Specifier\Where){
                            $where[] = $this->handleWhereSpecifier($specifierObject);
                            continue;
                        }
                        if($specifierObject instanceof Specifier\Sort){
                            foreach($specifierObject->getColumns() as $column){
                                $sort[] = "`" . $specifierObject->getModel()->getTable() . "`.`$column` " . ($specifierObject->isAsc())?"ASC":"DESC";
                            }
                            continue;
                        }
                }
            }

            $where  =   implode(' AND ', $where);
            $sort   =   implode(', ', $sort);
            $where  =   (!empty($where))    ?  'WHERE '     . $where    : null;
            $sort   =   (!empty($sort))     ?  'ORDER BY '  . $sort     : null;

            $query = "SELECT $parsedColumns FROM " . $this->generateFromJoin($models) . " $where $sort";

            //Execute Query:
            $this->query('USE '. $db);
            return $this->query($query);
        }

        /**
         * Method to select data from a MySQLDatabase
         * @param DataModel $model
         * @param array $columns
         * @param SpecifierObject[]|SpecifierObject $specifierObjects
         * @return array
         * @throws \Exception
         */
        public function select(DataModel $model, $columns = null, $specifierObjects = null){
            if(!(is_array($specifierObjects) || $specifierObjects == null)) $specifierObjects = [$specifierObjects];

            $db = $model->getDataBase();
            $tbl = $model->getTable();
            if(!$this->hasDatabase($db)) throw new \Exception("Invalid Database '$db'");

            $parsedColumns = [];
            if($columns == null){
                foreach($model->getColumns() as $column => $type){
                    $parsedColumns[] = "`$tbl`.`$column` AS '$tbl-$column'";
                }
            }else{
                foreach($columns as $column){
                    if(!$model->hasColumn($column)){
                       throw new \Exception("Column '$column' cannot be found");
                    }
                    $parsedColumns[] = "`$tbl`.`$column` AS '$tbl-$column'";
                }

            }
            $parsedColumns = implode(",", $parsedColumns);

            $specifierQueries = [];
            if($specifierObjects != null){
                foreach($specifierObjects as $specifierObject){
                    if ($specifierObject instanceof Specifier\Where){
                        $specifierQueries["Where"] = "WHERE " . $this->handleWhereSpecifier($specifierObject);
                        continue;
                    }
                    if($specifierObject instanceof Specifier\Sort){
                        $sort = [];
                        foreach($specifierObject->getColumns() as $column){
                            $sort[] = "`" . $specifierObject->getModel()->getTable() . "`.`$column` " . (($specifierObject->isAsc()) ? "ASC" : "DESC");
                        }
                        if(count($sort) != 0) $specifierQueries['Order By'] = 'ORDER BY '. implode(', ', $sort);
                        continue;
                    }
                }
            }

            $query = "SELECT $parsedColumns FROM `$tbl`";
            if(isset($specifierQueries["Where"])) $query .= " " . $specifierQueries["Where"];
            if(isset($specifierQueries["Order By"])) $query .= " " . $specifierQueries["Order By"];

            //Execute Query
            $this->query("USE " . $db);
            return $this->query($query);
        }

        /**
         * Method to insert data into a MySQLDatabase
         * @param DataModel $model
         * @param array $data
         * @return bool successful
         * @throws \Exception
         */
        public function insert(DataModel $model, $data){
            $db = $model->getDataBase();
            $tbl = $model->getTable();
            if(!$this->hasDatabase($db)) throw new \Exception("Invalid Database '$db'");

            foreach($data as $column => $type){
                if(!$model->hasColumn($column)) throw new \Exception("Column '$column' is not part of table");
            }

            $columns = [];
            $values = [];
            $modelColumns = $model->getColumns();
            foreach($data as $column => $value){
                $val = $this->validateType($modelColumns[$column], $value);
                $values[] = "'$val'";
                $columns[] = "`$column`";
            }

            $query = "INSERT INTO `$tbl` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";

            //execute Query
            $this->query("USE " . $db);
            $result = $this->query($query);
            if(!$result){ return [];}

            $cols = $model->getColumns();
            reset($cols);
            $first = key($cols);
            $result =  $this->select($model, null, [$this->generateWhereSpecifier($model, $data), new Specifier\Sort($model, $first, false)]);
            if(count($result) == 0) return [];
            return $result[0];
        }

        private function generateWhereSpecifier($model, $data){
            $specifiers = [];
            foreach($data as $column => $value){
                if ($value == NULL) continue;
                $specifiers = new WhereCheck($column, '==', $value);
            }

            if ($specifiers == NULL) return null;
            return new Specifier\Where($model, $specifiers);
        }

        /**
         * Method to update the data in a MySQLDatabase
         * @param DataModel $model
         * @param array $data
         * @param SpecifierObject[]|SpecifierObject $specifierObjects
         * @return bool successful
         * @throws \Exception
         */
        public function update(DataModel $model, $data, $specifierObjects){
            if(!is_array($specifierObjects)) $specifierObjects = [$specifierObjects];

            $db = $model->getDataBase();
            $tbl = $model->getTable();
            if(!$this->hasDatabase($db)) throw new \Exception("Invalid Database '$db'");

            foreach($data as $column => $type){
                if(!$model->hasColumn($column)) throw new \Exception("Column '$column' is not part of table");
            }

            $sets = [];
            $modelColumns = $model->getColumns();
            foreach($data as $column => $value){
                $val = $this->validateType($modelColumns[$column], $value);
                $sets[] = "`$column` = " . (($val == null)?"NULL": "'$val'");
            }
            $where = null;
            foreach($specifierObjects as $specifierObject){
                    if ($specifierObject instanceof Specifier\Where){
                        $where = $this->handleWhereSpecifier($specifierObject);
                        continue;
                    }
            }
            if($where == null) throw new \Exception("Invalid WhereSpecifier");

            //execute Query
            $this->query("USE " . $db);
            $query = "UPDATE `$tbl` SET " . implode(', ', $sets) . " WHERE " . $where;
            return $this->query($query);
        }

        /**
         * Method to delete data out of a MySQLDatabase
         * @param DataModel $model
         * @param $specifierObjects
         * @return bool successful
         * @throws \Exception
         */
        public function delete(DataModel $model, $specifierObjects){
            if(!is_array($specifierObjects)) $specifierObjects = [$specifierObjects];

            $db = $model->getDataBase();
            $tbl = $model->getTable();
            if(!$this->hasDatabase($db)) throw new \Exception("Invalid Database '$db'");

            $where = null;
            foreach($specifierObjects as $specifierObject){
                    if ($specifierObject instanceof Specifier\Where){
                        $where = "WHERE " . $this->handleWhereSpecifier($specifierObject);
                        continue;
                    }
            }
            if($where == null) throw new \Exception("Invalid WhereSpecifier");

            //execute Query
            $this->query("USE " . $db);
            $query = "DELETE FROM `$tbl` " . $where;
            return $this->query($query);
        }
    }