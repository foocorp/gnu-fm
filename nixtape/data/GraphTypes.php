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

require_once($install_path . '/data/Graph.php');

class GraphTopArtists extends Graph {
    
    public $artists, $artists_data;
    public $number_of_tracks;

    function __construct($user, $num = 20)
    {
        parent::__construct($user, "bar_horiz");
        $this->number_of_tracks = $num;
        
        $this->buildArtistGraphData();
    }
    
    private function buildArtistGraphData()
    {
        $tmp = Statistic::GeneratePlayStats('Scrobbles', 'artist',
                        $this->number_of_tracks, $this->user->uniqueid, 300);
        
        foreach ($tmp as $root => $node)
        {
            /* @TODO: check the URLs, make them work. */
            $artists[] = '<a href="'.$node['artisturl'].'">'.$node['artist'].'</a>';
            $artists_data[] = $node['count'];
        }
        
        /* As in DESC order, first element has to be the largest. */
        $this->setMaxX($artists_data[0]);
        
        /* To get DESC cascading, once committed, possibly alter this in SQL. */
        $artists = array_reverse($artists);
        $artists_data = array_reverse($artists_data);
        $this->artists = $this->buildJsSingleArray($artists);
        $this->data[0][0] = $artists_data;
        $this->artists_data = $this->buildJsDataArray(true);
    }
}

class GraphTopTracks extends Graph {
    
    public $number_of_tracks;
    
    function __construct($user, $num = 20)
    {
        parent::__construct($user, "bar_horiz");
        $this->number_of_tracks = $num;
        $this->buildTrackGraphData();
    }
    
    private function buildTrackGraphData()
    {
        $this->data_buffer = $this->user->getTopTracks($this->number_of_tracks);
        $tmp_artists = array();
        $tmp_listings = array();
        
        foreach($this->data_buffer as $key => $entry)
        {
            $tmp_artists[] = $entry['freq'];
            $tmp_listings[] = $entry['artist'];
        }
    }
}

class GraphTrackPerformance extends Graph {
    
}