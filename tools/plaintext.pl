#!/usr/bin/perl -w

use strict;

# LCWO.net
# Import a plain text file with sentences

my $collid = 17;
my $lang   = "tr";
my $desc   = "Sentences";

while (my $line = <>) {
    chomp($line);
    $line =~ s/'/\\'/g;
    print "INSERT INTO `lcwo_plaintext` (`lang`, `description`, `text`, `collid`) VALUES ('$lang','$desc','$line',$collid);\n";
}
