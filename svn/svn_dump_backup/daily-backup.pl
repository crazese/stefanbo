#!/usr/bin/perl -w
#full backup svn perl

$svn_repos = "/home/svn/project";
$backups_dir = "/home/svn/backup";
$next_backup_file = "daily-incremental-backup." . `date +%Y%m%d%H%M`;

open(IN, "$backups_dir/last_backed_up");
$previous_youngest = <IN>;
chomp $previous_youngest;
close IN;

$youngest = `svnlook youngest $svn_repos`;
chomp $youngest;
if($youngest eq $previous_youngest) {
	print "No new revision to back up.\n";
	exit 0;
}
$first_rev = $previous_youngest + 1;
$last_rev = $youngest;

print "Backing up revision $first_rev to $last_rev...\n";
$svnadmin_cmd = "svnadmin dump --incremental " . 
				"--revision $first_rev:$last_rev " . 
				"$svn_repos > $backups_dir/$next_backup_file";
`$svnadmin_cmd`;

print "Compressing dump file...\n";
print `gzip -9 $backups_dir/$next_backup_file`;
open(LOG, ">$backups_dir/last_backed_up");
print LOG $last_rev;
close LOG;