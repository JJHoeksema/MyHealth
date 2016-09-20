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
     * Class Head
     * @author Marwijnn de Kuijper <ikbenmarwijnn@gmail.com>
     * @package DMF\Page
     * @todo DO NOT USE incomplete
     */
    final class Head extends PageObject{

        private $meta;
        private $script;
        private $link;

        private $title;
        private $charset;
        private $description;
        private $author;
        private $keywords;

        /**
         * constructor of head tag
         */
        public function __construct(){
            $this->reset();
        }

        /**
         * @return int maximum number of instances  (if 0 no maximum);
         */
        public static function maxInstances(){
            return 1;
        }

        private function addMeta($name = NULL, $content = NULL, $httpEquiv = NULL, $charset = NULL){
            $array = [];
            if ($name != NULL) $array['name'] = $name;
            if ($content != NULL) $array['content'] = $content;
            if ($httpEquiv != NULL) $array['http-equiv'] = $httpEquiv;
            if ($charset != NULL) $array['charset'] = $charset;
            if (count($array) != 0) array_push($this->meta, $array);
        }

        private function addLink($array){
            //TODO: add link (aka stylesheet)
        }

        /**
         * method to add a keyword to html page
         * @param $keywords
         */
        public function addKeyword($keywords){
            if (!is_array($keywords)) $keywords = [$keywords];;
            $this->keywords = array_merge($this->keywords, $keywords);
        }

        /**
         * method to add script to a html page
         * @param $url
         */
        public function addScript($url){
            //TODO: add script
            $args = func_get_args();
            //var_dump($args);
        }

        /**
         * method to set FavIcon
         * @param $url
         */
        public function setFavIcon($url){
            //TODO: <link rel="icon" href="/favicon.ico" type="image/x-icon">
        }

        /**
         * Method to set searchfield
         * @param $url
         */
        public function setSearch($url){
            //TODO: <link rel="search"> http://dev.bowdenweb.com/html/e/link/link-rel-search-element.html
        }

        /**
         * Method to add stylesheet
         * @param $url
         */
        public function addStylesheet($url){
            //TODO: <link rel="stylesheet" type="text/css" href="mystyle.css">
        }

        /**
         * Method to set and get title
         * @param string $title
         * @return string the current title
         */
        public function title($title = NULL){
            if ($title != null) $this->title = $title;
            return $this->title;
        }

        /**
         * Method to reset Head tag to default
         */
        public function reset(){
            $this->title("No title");
            $this->charset = "UTF-8";
            $this->description = "- No description -";
            $this->author = "unknown";
            $this->keywords = [];
            $this->meta = [];
            $this->script = [];
            $this->link = [];
        }

        public function render(){
            //todo: inmplement render function
            return "";
        }

        /**
         * override Method to display to screen
         */
        public function display(){
            echo '<head>';

            //add to meta
            $this->addMeta(NULL,NULL,NULL, $this->charset);
            $this->addMeta('keywords', implode(", ", $this->keywords));
            $this->addMeta('description', $this->description);
            $this->addMeta('author', $this->author);

            //add <meta>
            foreach($this->meta as $array){
                echo '<meta ';
                foreach ($array as $k => $v) echo $k . '= "' . $v .'"';
                echo '/>';
            }

            //add <title>
            echo '<title>' . $this->title . '</title>';

            //add <script>
            foreach ($this->script as $v){
                echo '<script src=' . '"' . $v . '"></script>';
            }

            //add <link>
            foreach($this->link as $v){

            }

            echo '</head>';
        }
    }