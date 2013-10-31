#!/usr/bin/perl -w
#full backup svn perl

$svn_repos = "/home/svn/project";
$backups_dir = "/home/svn/backup";
$next_backup_file = "weekly-full-backup." . `date +%Y%m%d%H%M`;

$youngest = `svnlook youngest $svn_repos`;

chomp $youngest;

print "Backing up to revision $youngest\n";
$svnadmin_cmd = "svnadmin dump --revision 0:$youngest "."$svn_repos > $backups_dir/$next_backup_file";
`$svnadmin_cmd`;

print "Compressing dump file...\n";
print `gzip -9 $backups_dir/$next_backup_file`;
open(LOG, ">$backups_dir/last_backed_up");
print LOG $youngest;
close LOG;