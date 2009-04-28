#!/usr/bin/perl

use CGI qw(escape);
use Encode qw(_utf8_on);
use LWP::Simple;
use strict;

my $data = get('http://en.wikipedia.org/w/index.php?title=ISO_3166-1_alpha-2&action=raw');
_utf8_on($data); # mess with Perl's internals... data is in UTF8, but we need to tell Perl that!
my @lines = split /\r?\n/, $data;

my $longest_name = 0;
my $longest_link = 0;

print "DROP TABLE Countries;\n";
print "CREATE TABLE Countries (country varchar(2) PRIMARY KEY, country_name varchar(200), wikipedia_en varchar(120));\n";
foreach (@lines)
{
	# toothpicks stood on end
	if (/^	\s* \| \s*			# Initial pipe symbol
			id=\"([A-Z]{2})\"	# ID attribute
		\s* \| \s*			# Another pipe symbol
			<tt>..<\/tt>		# Teletype font
		\s* \|\| \s* 			# Double pipe symbol
			\[\[			# Double opening brackets
				([^\]\|]+)	# Link
				(\|([^\]]+))?	# Link text (optional)
			\]\]			# Double closing bracket
	/xi)
	{


		my $code = $1;
		my $link = escape($2);
		my $name = $4 || $2;

		$link =~ s/\%20/_/g;
		
		$name =~ s/\'/\'\'/g;
		
		$longest_link = length($link) if length($link) > $longest_link;
		$longest_name = length($name) if length($name) > $longest_name;
		
		print "INSERT INTO Countries VALUES ('$code', '$name', 'http://en.wikipedia.org/wiki/$link');\n";
	}
}

$longest_link += length('http://en.wikipedia.org/wiki/');

print "-- Longest name: $longest_name\n";
print "-- Longest link: $longest_link\n";
