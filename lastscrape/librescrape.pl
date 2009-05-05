#!/usr/bin/perl

# librescrape.pl - proof of concept scraper for libre.fm.
#
# Note that we're not really scraping the pages at all, but querying
# them like a database! Cool, huh?

use LWP::Simple;
use RDF::Redland;
use RDF::RDFa::Parser::Redland '0.02';

my $uri = shift @ARGV
	or die "Usage: librescrape.pl 'http://alpha.libre.fm/user/somebody'\n";

if ($uri =~ /user-profile\.php/)
{
	$uri =~ s/user-profile.php/user-recent-tracks.php/;
	$uri .= '&count=1000';
}
else
{
	$uri .= '/recent-tracks?count=1000';
}

my $parser = RDF::RDFa::Parser::Redland->new(get($uri), $uri);
$parser->consume;
my $model = $parser->redland;

my $query = <<SPARQL;
PREFIX gob:  <http://purl.org/ontology/last-fm/>
PREFIX dc:   <http://purl.org/dc/terms/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT *
WHERE
{
	<$uri>  foaf:primaryTopic ?person .
	?gobble a gob:ScrobbleEvent ;
	        gob:track_played ?track ;
	        dc:date ?timestamp ;
	        gob:user ?person .
	?track  dc:title ?trackname ;
	        foaf:maker ?artist .
	?artist foaf:name ?artistname .
}
ORDER BY DESC(?timestamp)
SPARQL

my $query_obj = RDF::Redland::Query->new($query, undef, undef, 'sparql');
my $results   = $query_obj->execute($model);

while((!$results->finished) && (my %row = $results->bindings))
{
	$results->next_result;

	print sprintf("%s\t%s\t%s\n",
		$row{'artistname'}->literal_value,
		$row{'trackname' }->literal_value,
		$row{'timestamp' }->literal_value);
}