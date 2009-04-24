<?php

/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

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

require_once 'config.php';
require_once 'utils/arc/ARC2.php';

$page = $_GET['page'];
$fmt  = $_GET['fmt'];

$parser = ARC2::getSemHTMLParser('sem_html_formats' => 'rdfa');
$parser->parse($base_url . $page);
$index = $parser->getSimpleIndex(0);

$conf = array(
	'ns' => array(
		'xhv' => 'http://www.w3.org/1999/xhtml/vocab#',
		'dc' => 'http://purl.org/dc/terms/',
		'foaf' => 'http://xmlns.com/foaf/0.1/',
		'bio' => 'http://purl.org/vocab/bio/0.1/' ,
		'sioc' => 'http://rdfs.org/sioc/ns#',
		'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
		'gob' => 'http://purl.org/ontology/last-fm/',
		'mo' => 'http://purl.org/ontology/mo/',
		'rss' => 'http://purl.org/rss/1.0/'
		)
	);

switch ($fmt)
{
	case 'xml' :
		header("Content-Type: application/rdf+xml");
		$ser = ARC2::getRDFXMLSerializer($conf);
		break;
	case 'ttl' :
		header("Content-Type: application/x-turtle");
		$ser = ARC2::getTurtleSerializer($conf);
		break;
	case 'rss' :
		header("Content-Type: application/rss+xml");
		$ser = ARC2::getRSS10Serializer($conf);
		break;
	case 'json' :
		if ($_GET['callback'])
			{ header("Content-Type: text/javascript"); }
		else 
			{ header("Content-Type: application/json"); }
		$ser = ARC2::getRDFJSONSerializer($conf);
		break;
	case 'nt' :
		header("Content-Type: text/plain");
		$ser = ARC2::getNTriplesSerializer($conf);
		break;
}

if ($_GET['callback'])
	print $_GET['callback'] . '(';
print $ser->getSerializedIndex($index);
if ($_GET['callback'])
	print ');';
	
