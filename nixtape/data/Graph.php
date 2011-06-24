<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Free Software Foundation, Inc

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

require_once($install_path . '/database.php');
require_once($install_path . '/data/Artist.php');
require_once($install_path . '/data/Album.php');
require_once($install_path . '/data/Server.php');
require_once($install_path . '/data/Statistic.php');

/**
 * Represents graph object, extended for specific implementations, currently
 * acts as a data only object but intention is to delegate as much functionality
 * with respect to the propagation of the graph objects themselves to this
 * class. Many methods are, therefore, included with this view in mind.
 *
 * @see GraphTypes.php for implementations 
 **/
class Graph {
    
    public $data, $data_buffer;
    public $user, $type, $renderer, $label_renderer;
    public $max_x_axis, $max_y_axis;
    public $tick_interval = 20;
    public $x_axis_label = 'X axis', $y_axis_label = 'Y Axis';
    
    /* Multidimensional array to allow for multiple series of data. */
    protected $graph_types = array(array());
    
    public static $DEFAULT_GRAPH_TYPE = 0;
    
    function __construct($user = null, $type = null) {
        $this->user = $user;
        $this->resetData();
        
        /* @todo: iterate through $type to determine renderer requirements */
        switch($type){}
    }
    
    /**
     * Resets internal data object.
     **/
    protected function resetData() {
        $this->data = array(array(array()));    
    }
    
    /**
     * Returns the graph renderer as defined at object instantiation.
     **/
    public function getGraphRenderer() {
        return $this->graph_types[$this->renderer];
    }
    
    /**
     * Returns x-axis label.
     **/
    public function getXAxis() {
        return $this->x_axis_label;
    }
    
    /**
     * Returns y-axis label.
     **/
    public function getYAxis() {
        return $this->y_axis_label;
    }
    
    /**
     * Sets the axes labels of the current object.
     * @param $x = x-axis label.
     * @param $y = y-axis label.
     **/
    public function setAxisLabels($x = NULL, $y = NULL)
    {
        $this->x_axis_label = ($x === NULL) ? $this->x_axis_label : $x;
        $this->y_axis_label = ($y === NULL) ? $this->y_axis_label : $y;
    }
    
    /**
     * Returns the JS array built from the internal data respresentation, not
     * used at present by used to hide the internal building method.
     * @return String JS array.
     * @see buildJsDataArray().
     **/
    public function getJsDataArray() {
        return $this->buildJsDataArray();
    }
    
    /**
     * Sets the internal data source to a new object.
     * @param $data = new data object for the Graph
     **/
    public function setDataSource($data = NULL)
    {
        if (($data === NULL) && (! isEmpty($data))) return;
        $this->data = $data;
    }
    
    /**
     * Sets the maximum value of x-axis, both the maximum, rounded value of the
     * x-axis ticks and also determining a round, suitable tick interval.
     */
    protected function setMaxX($raw)
    {
        $this->max_x_axis = round($raw + 100, -2);
        $this->tick_interval = ($this->max_x_axis / 10);
    }
    
    /**
     * Iterates through the multi-dimensional array $data to create a string
     * JS array representation of multiple series of data for the Graph object.
     * @param $inverse Boolean TRUE if data is being represented horizontally.
     * @return String JS multi-dimensional array.
     **/
    protected function buildJsDataArray($inverse = FALSE) {
        
        $temp = '[';
     
        foreach ($this->data as $i => $series)
        {
            $temp .= '[';  
            if ($inverse) $i = 0;
            
            foreach ($series as $j => $set)
            {
                foreach ($set as $k => $node)
                {
                    /* Determine if the node is numeric, if not, escape. */
                    $temp .= '['.((! is_numeric($node))
                            ? '\''.addslashes($node).'\''
                            : $node);
                    
                    /* @TODO: check $node for len > 1, if so tokenise string */
                    if ($inverse) $temp .= ',' . ++$i;
                    $temp .= '],'; 
                }
                $temp = rtrim($temp, ','); 
            }
            $temp .= '],';
        }
        
        $temp = rtrim($temp, ',');
        $temp .= ']';
        
        return $temp;
    }
    
    protected function buildJsSingleArray($source) {
        
        $temp = '[';
        
        foreach ($source as $i => $node)
        {
            $temp .= ((! is_numeric($node)) ? '\''.$node.'\'' : $node) . ',';
        }
        
        $temp = rtrim($temp, ',') . ']';
        return $temp;
    }
}