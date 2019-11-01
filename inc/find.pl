use warnings;


@files = <*.php>;

foreach $file (@files) {
	open FI, $file;
	while ($line = <FI>) {
		$file =~ s/[.]php//g;
		@u = split(/[ \.\?]/, $line);
		foreach $u (@u) {
			if ($u =~ /l\(([a-zA-Z0-9]+)\)/) {
				$t=$1;
				$h{$t} .= " ";
				unless ($h{$t} =~ /$file</) {
					$h{$t} .= " <a href=\"index.php?p=$file\">$file</a> ";
				}
			}
		}
	}
}

print "use lcwo; delete from lcwo_textindex;\n";

foreach (sort keys %h) {
	print "insert into lcwo_textindex ".
	" (`key`, `val`) VALUES ('$_', '$h{$_}');\n";
}
