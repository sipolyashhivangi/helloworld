#!/usr/bin/perl
#nite crawler script
#Author : Ganesh Manoharan
#04 17 2013 Need to upgrade the script - Ganesh
use strict;
use warnings;

my $directory = '/sftp/ftpce/incoming/';
my $batchfilePath = '/var/www/dev/scripts/batchfiles/';

my $file;
my @files;
my %data = ();

# Read Every File into @files variable
opendir (DIR, $directory) or die $!;
system("rm /$batchfilePath/*.csv ");
while ($file = readdir(DIR)) {
  if(not -d $file) {
    push (@files, $file);
  }
}

# Sort @files list 
@files = sort {$b cmp $a} @files;

# Write to Batch Files folder
foreach $file (@files) {
  my  @outp = ($file =~ m/(\D+)(-)(\D+)/);
  #To get Prefix Name we use memory variables
  if(@outp ) {
    my $outputFile = $1.$2.$3;
    if(not exists $data{$outputFile}) {                
      #print "$file\n";
      $data{$outputFile} = 1;
      system("gpg --no-verbose --batch --decrypt --passphrase-fd 0 $file  <$batchfilePath/passphrase.txt > temp.csv ");
      system(" grep -v \"^[A-Z]\" temp.csv >> /$batchfilePath/$outputFile.csv ");
      system("rm temp.csv");
    }
  }
}

closedir(DIR);
exit 0;
