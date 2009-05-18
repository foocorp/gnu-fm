<?php

include '../nixtape/config.php';
include '../nixtape/database.php';
include '../nixtape/utils/linkeddata.php';

function htmlnumericentities($str)
{ 
	return preg_replace('/[^!-%\x27-;=?-~ ]/e', '"&#".ord("$0").chr(59)', $str); 
}

$username = $argv[1];
if (! $username)
{
	die("Must provide a username.\n");
}

$user = new User($username);
$scrobbles = $user->getScrobbles(10000);

print "<rdf:RDF
	xmlns=\"http://purl.org/ontology/mo/\"
	xmlns:bio=\"http://purl.org/vocab/bio/0.1/\"
	xmlns:dc=\"http://purl.org/dc/terms/\"
	xmlns:foaf=\"http://xmlns.com/foaf/0.1/\"
	xmlns:gob=\"http://purl.org/ontology/last-fm/\"
	xmlns:mo=\"http://purl.org/ontology/mo/\"
	xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\"
	xmlns:sioc=\"http://rdfs.org/sioc/ns#\"
	xmlns:rss=\"http://purl.org/rss/1.0/\"
	xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"
	xmlns:geo=\"http://www.w3.org/2003/01/geo/wgs84_pos#\"
	xml:lang=\"en\">\n";

while ($s = array_shift($scrobbles))
{
	printf("
	<gob:ScrobbleEvent rdf:about=\"%s\">
		<gob:user rdf:resource=\"%s\" />
		<gob:track_played>
			<Track rdf:about=\"%s\" dc:title=\"%s\">
				<foaf:maker>
					<MusicArtist rdf:about=\"%s\" foaf:name=\"%s\" />
				</foaf:maker>
			</Track>
		</gob:track_played>
		<dc:date rdf:datatype=\"http://www.w3.org/2001/XMLSchema#dateTime\">%s</dc:date>
	</gob:ScrobbleEvent>
	<Record about=\"%s\">
		<track rdf:resource=\"%s\" />
	</Record>\n",
	htmlnumericentities($s['id']),
	htmlnumericentities($user->id),
	htmlnumericentities($s['id_track']),
	htmlnumericentities($s['track']),
	htmlnumericentities($s['id_artist']),
	htmlnumericentities($s['artist']),
	htmlnumericentities($s['timeiso']),
	htmlnumericentities($s['id_album']),
	htmlnumericentities($s['id_track'])
	);
}

print "</rdf:RDF>\n";