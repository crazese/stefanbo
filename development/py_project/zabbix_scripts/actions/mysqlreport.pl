#!/usr/bin/perl -w

# mysqlreport v3.5 Apr 16 2008
# http://hackmysql.com/mysqlreport

# mysqlreport makes an easy-to-read report of important MySQL status values.
# Copyright 2006-2008 Daniel Nichter
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# The GNU General Public License is available at:
# http://www.gnu.org/copyleft/gpl.html

use strict;
use File::Temp qw(tempfile);
use DBI;
use Getopt::Long;
eval { require Term::ReadKey; };
my $RK = ($@ ? 0 : 1);

sub have_op;

my $WIN = ($^O eq 'MSWin32' ? 1 : 0);
my %op;
my %mycnf; # ~/.my.cnf
my ($tmpfile_fh, $tmpfile);
my ($stat_name, $stat_val, $stat_label);
my $MySQL_version;
my (%stats, %vars); # SHOW STATUS, SHOW VARIABLES
my (%DMS_vals, %Com_vals, %ib_vals);
my ($dbh, $query);
my ($questions, $key_read_ratio, $key_write_ratio, $dms, $slow_query_t);
my ($key_cache_block_size, $key_buffer_used, $key_buffer_usage);
my ($qc_mem_used, $qc_hi_r, $qc_ip_r); # Query Cache
my $have_innodb_vals;
my ($ib_bp_used, $ib_bp_total, $ib_bp_read_ratio);
my ($relative_live, $relative_infiles);
my $real_uptime;
my (%stats_present, %stats_past); # For relative reports
      
GetOptions (
   \%op,
   "user=s",
   "password:s",
   "host=s",
   "port=s",
   "socket=s",
   "no-mycnf",
   "infile|in=s",
   "outfile=s",
   "flush-status",
   "email=s",
   "r|relative:i",
   "c|report-count=i",
   "detach",
   "help|?",
   "debug"
);

show_help_and_exit() if $op{'help'};

get_user_mycnf() unless $op{'no-mycnf'};

# Command line options override ~/.my.cnf
$mycnf{'host'}   = $op{'host'}   if have_op 'host';
$mycnf{'port'}   = $op{'port'}   if have_op 'port';
$mycnf{'socket'} = $op{'socket'} if have_op 'socket'; 
$mycnf{'user'}   = $op{'user'}   if have_op 'user';

$mycnf{'user'} ||= $ENV{'USER'};

if(exists $op{'password'})
{
   if($op{'password'} eq '') # Prompt for password
   {
      Term::ReadKey::ReadMode(2) if $RK;
      print "Password for database user $mycnf{'user'}: ";
      chomp($mycnf{'pass'} = <STDIN>);
      Term::ReadKey::ReadMode(0), print "\n" if $RK;
   }
   else { $mycnf{'pass'} = $op{'password'}; } # Use password given on command line
}

$op{'com'} ||= 3;
$op{'c'}   ||= 1; # Used in collect_reports() if --r given integer value

$relative_live    = 0;
$relative_infiles = 0;

if(defined $op{'r'})
{
   if($op{r}) { $relative_live    = 1; }  # if -r was given an integer value
   else       { $relative_infiles = 1; }
}

# The report is written to a tmp file first.
# Later it will be moved to $op{'outfile'} or emailed $op{'email'} if needed.
($tmpfile_fh, $tmpfile) = tempfile() or die "Cannot open temporary file for writing: $!\n";

if($op{'detach'})
{
   $SIG{'TERM'} = 'sig_handler';

   if(fork())
   {
      print "mysqlreport has forked and detached.\n";
      print "While running detached, mysqlreport writes reports to '$tmpfile'.\n";

      exit;
   }

   open(STDIN, "</dev/null");
   open(STDOUT, "> $tmpfile") or die "Cannot dup STDOUT: $!\n";
   open(STDERR, "> $tmpfile") or die "Cannot dup STDERR: $!\n";
}

select $tmpfile_fh;
$| = 1 if ($op{'detach'} || $relative_live);

print "tmp file: $tmpfile\n" if $op{debug};

# Connect to MySQL
if(!$op{'infile'} && !$relative_infiles)
{
   connect_to_MySQL();
}

$have_innodb_vals = 1; # This might be set to 0 later in get_MySQL_version()

if(defined $op{'r'})
{
   if($relative_live)
   { 
      print STDERR "mysqlreport is writing relative reports to '$tmpfile'.\n" unless $op{'detach'}; 
      get_MySQL_version();
      collect_reports();
   }

   if($relative_infiles) { read_relative_infiles(); }
}
else
{
   if(!$op{'infile'})
   {
      get_MySQL_version();
      get_vals();
      get_vars();
   }
   else
   {
      read_infile($op{'infile'});
   }

   get_Com_values();

   set_myisam_vals();
   set_ib_vals() if $have_innodb_vals;

   write_report();
}

exit_tasks_and_cleanup();

exit;

#
# Subroutines
#
sub show_help_and_exit
{
   print <<"HELP";
mysqlreport v3.5 Apr 16 2008
mysqlreport makes an easy-to-read report of important MySQL status values.

Command line options (abbreviations work):
   --user USER       Connect to MySQL as USER
   --password PASS   Use PASS or prompt for MySQL user's password
   --host ADDRESS    Connect to MySQL at ADDRESS
   --port PORT       Connect to MySQL at PORT
   --socket SOCKET   Connect to MySQL at SOCKET
   --no-mycnf        Don't read ~/.my.cnf
   --infile FILE     Read status values from FILE instead of MySQL
   --outfile FILE    Write report to FILE
   --email ADDRESS   Email report to ADDRESS (doesn't work on Windows)
   --flush-status    Issue FLUSH STATUS; after getting current values
   --relative X      Generate relative reports. If X is an integer,
                     reports are live from the MySQL server X seconds apart.
                     If X is a list of infiles (file1 file2 etc.),
                     reports are generated from the infiles in the order
                     that they are given.
   --report-count N  Collect N number of live relative reports (default 1)
   --detach          Fork and detach from terminal (run in background)
   --help            Prints this
   --debug           Print debugging information

Visit http://hackmysql.com/mysqlreport for more information.
HELP

   exit;
}

sub get_user_mycnf
{
   print "get_user_mycnf\n" if $op{debug};

   return if $WIN;
   open MYCNF, "$ENV{HOME}/.my.cnf" or return;
   while(<MYCNF>)
   {
      if(/^(.+?)\s*=\s*"?(.+?)"?\s*$/)
      {
         $mycnf{$1} = $2;
         print "get_user_mycnf: read '$1 = $2'\n" if $op{debug};
      }
   }
   $mycnf{'pass'} ||= $mycnf{'password'} if exists $mycnf{'password'};
   close MYCNF;
}

sub connect_to_MySQL
{
   print "connect_to_MySQL\n" if $op{debug};

   my $dsn;

   if($mycnf{'socket'} && -S $mycnf{'socket'})
   {
      $dsn = "DBI:mysql:mysql_socket=$mycnf{socket}";
   }
   elsif($mycnf{'host'})
   {
      $dsn = "DBI:mysql:host=$mycnf{host}" . ($mycnf{port} ? ";port=$mycnf{port}" : "");
   }
   else
   {
      $dsn = "DBI:mysql:host=localhost";
   }

   print "connect_to_MySQL: DBI DSN: $dsn\n" if $op{debug};

   $dbh = DBI->connect($dsn, $mycnf{'user'}, $mycnf{'pass'}) or die;
}

sub collect_reports
{
   print "collect_reports\n" if $op{debug};

   my $i;

   get_vals();
   get_vars();

   get_Com_values();

   %stats_past = %stats;

   set_myisam_vals();
   set_ib_vals() if $have_innodb_vals;

   print "#\n# Beginning report, 0 0:0:0\n#\n";

   write_report();

   for($i = 0; $i < $op{'c'}; $i++)
   {
      $dbh->disconnect();

      sleep($op{'r'});

      connect_to_MySQL();

      print "\n#\n# Interval report " , $i + 1 , ", +", sec_to_dhms(($i + 1) * $op{'r'}), "\n#\n";

      get_vals();

      write_relative_report();
   }
}

sub read_relative_infiles
{
   print "read_relative_infiles\n" if $op{debug};

   my $slurp;    # Used to check infiles for multiple sets of status values
   my $n_stats;  # Number of multiple sets of status values in an infile
   my $infile;
   my $report_n; # Report number

   $report_n = 1;

   foreach $infile (@ARGV)
   {
      # Read all of infile into $slurp
      open INFILE, "< $infile" or warn and next;
      $slurp = do { local $/;  <INFILE> };
      close INFILE;

      $n_stats = 0;

      # Count number of status value sets
      $n_stats++ while $slurp =~ /Aborted_clients/g;

      print "read_relative_infiles: found $n_stats sets of status values in file '$infile'\n"
         if $op{debug};

      if($n_stats == 1)
      {
         read_infile($infile);
         relative_infile_report($report_n++);
      }

      if($n_stats > 1)
      {
         my @tmpfile_fh;
         my @tmpfile_name;
         my $i;
         my $stat_n;  # Status value set number

         # Create a tmp file for each set of status values
         for($i = 0; $i < $n_stats; $i++)
         {
            my ($fh, $name) = tempfile()
               or die "read_relative_infiles: cannot open temporary file for writing: $!\n";

            push(@tmpfile_fh, $fh);
            push(@tmpfile_name, $name);

            print "read_relative_infiles: created tmp file '$name' for set $i\n" if $op{debug};
         }

         $i = 0;
         $stat_n = 0;

         select $tmpfile_fh[$i];

         # Read infile again and copy each set of status values to seperate tmp files
         open INFILE, "< $infile" or warn and next;
         while(<INFILE>)
         {
            next if /^\+/;
            next if /^$/;

            # The infile must begin with the system variable values.
            # Therefore, the first occurance of Aborted_clients indicates the beginning
            # of the first set of status values if no sets have occured yet ($stat_n == 0).
            # In this case, the following status values are printed to the current fh,
            # along with the system variable values read thus far, until Aborted_clients
            # occurs again. Then begins the second and subsequent sets of status values.

            if(/Aborted_clients/)
            {
               print and next if $stat_n++ == 0;
               select $tmpfile_fh[++$i];
            }

            print;
         }
         close INFILE;

         # Re-select the main tmp file into which the reports are being written.
         select $tmpfile_fh;

         for($i = 0; $i < $n_stats; $i++)
         {
            close $tmpfile_fh[$i];

            print "read_relative_infiles: reading set $i tmp file '$tmpfile_name[$i]'\n"
               if $op{debug};

            read_infile($tmpfile_name[$i]);
            relative_infile_report($report_n++);

            if($WIN) { `del $tmpfile_name[$i]`;   }
            else     { `rm -f $tmpfile_name[$i]`; }

            print "read_relative_infiles: deleted set $i tmp file '$tmpfile_name[$i]'\n"
               if $op{debug};
         }

      } # if($n_stats > 1)
   } # foreach $infile (@files)
}

sub relative_infile_report
{
   print "relative_infile_report\n" if $op{debug};

   my $report_n = shift;

   if($report_n == 1)
   {
      get_Com_values();

      %stats_past = %stats;

      set_myisam_vals();
      set_ib_vals() if $have_innodb_vals;

      print "#\n# Beginning report, 0 0:0:0\n#\n";

      write_report();
   }
   else
   {
      print "\n#\n# Interval report ", $report_n - 1, ", +",
         sec_to_dhms($stats{Uptime} - $stats_past{Uptime}),
         "\n#\n";

      write_relative_report();
   }
}

sub get_vals
{
   print "get_vals\n" if $op{debug};

   my @row;

   # Get status values
   if($MySQL_version >= 50002)
   {
      $query = $dbh->prepare("SHOW GLOBAL STATUS;");
   }
   else
   {
      $query = $dbh->prepare("SHOW STATUS;");
   }
   $query->execute();
   while(@row = $query->fetchrow_array()) { $stats{$row[0]} = $row[1]; }

   $real_uptime = $stats{'Uptime'};
}

sub get_vars
{
   print "get_vars\n" if $op{debug};

   my @row;

   # Get server system variables
   $query = $dbh->prepare("SHOW VARIABLES;");
   $query->execute();
   while(@row = $query->fetchrow_array()) { $vars{$row[0]} = $row[1]; }

   # table_cache was renamed to table_open_cache in MySQL 5.1.3
   if($MySQL_version >= 50103)
   {
      $vars{'table_cache'} = $vars{'table_open_cache'};
   }
}

sub read_infile
{
   print "read_infile\n" if $op{debug};

   my $infile = shift;

   # Default required system variable values if not set in INFILE.
   # As of mysqlreport v3.5 the direct output from SHOW VARIABLES;
   # can be put into INFILE instead. See http://hackmysql.com/mysqlreportdoc
   # for details.
   $vars{'version'} = "0.0.0"         if !exists $vars{'version'};
   $vars{'table_cache'} = 64          if !exists $vars{'table_cache'};
   $vars{'max_connections'} = 100     if !exists $vars{'max_connections'};
   $vars{'key_buffer_size'} = 8388600 if !exists $vars{'key_buffer_size'}; # 8M
   $vars{'thread_cache_size'} = 0     if !exists $vars{'thread_cache_size'}; 
   $vars{'tmp_table_size'} = 0        if !exists $vars{'tmp_table_size'};
   $vars{'long_query_time'} = '?'     if !exists $vars{'long_query_time'};
   $vars{'log_slow_queries'} = '?'    if !exists $vars{'log_slow_queries'};

   # One should also add:
   #    key_cache_block_size
   #    query_cache_size
   # to INFILE if needed.

   open INFILE, "< $infile" or die "Cannot open INFILE '$infile': $!\n";

   while(<INFILE>)
   {
      last if !defined $_;

      next if /^\+/;  # skip divider lines 
      next if /^$/;   # skip blank lines

      next until /(Aborted_clients|back_log|=)/;

      if($1 eq 'Aborted_clients')  # status values
      {
         print "read_infile: start stats\n" if $op{debug};

         while($_)
         {
            chomp;
            if(/([A-Za-z_]+)[\s\t|]+(\d+)/)
            {
               $stats{$1} = $2;
               print "read_infile: save $1 = $2\n" if $op{debug};
            }
            else { print "read_infile: ignore '$_'\n" if $op{debug}; }

            last if $1 eq 'Uptime';  # exit while() if end of status values
            $_ = <INFILE>; # otherwise, read next line of status values
         }
      }
      elsif($1 eq  'back_log')  # system variable values
      {
         print "read_infile: start vars\n" if $op{debug};

         while($_)
         {
            chomp;
            if(/([A-Za-z_]+)[\s\t|]+([\w\.\-]+)/)  # This will exclude some vars
            {                                      # like pid_file which we don't need
               $vars{$1} = $2;
               print "read_infile: save $1 = $2\n" if $op{debug};
            }
            else { print "read_infile: ignore '$_'\n" if $op{debug}; }

            last if $1 eq 'wait_timeout';  # exit while() if end of vars
            $_ = <INFILE>; # otherwise, read next line of vars
         }
      }
      elsif($1 eq '=')  # old style, manually added system variable values
      {
         print "read_infile: start old vars\n" if $op{debug};

         while($_ && $_ =~ /=/)
         {
            chomp;
            if(/^\s*(\w+)\s*=\s*([0-9.]+)(M*)\s*$/)  # e.g.: key_buffer_size = 128M
            {
               $vars{$1} = ($3 ? $2 * 1024 * 1024 : $2);
               print "read_infile: read '$_' as $1 = $vars{$1}\n" if $op{debug};
            }
            else { print "read_infile: ignore '$_'\n" if $op{debug}; }

            $_ = <INFILE>; # otherwise, read next line of old vars
         }

         redo;
      }
      else
      {
         print "read_infile: unrecognized line: '$_'\n" if $op{debug};
      }
   }

   close INFILE;

   $real_uptime = $stats{'Uptime'};

   $vars{'table_cache'} = $vars{'table_open_cache'} if exists $vars{'table_open_cache'};

   get_MySQL_version();
}

sub get_MySQL_version
{
   print "get_MySQL_version\n" if $op{debug};

   return if $MySQL_version;

   my ($major, $minor, $patch);

   if($op{'infile'} || $relative_infiles)
   {
      ($major, $minor, $patch) = ($vars{'version'} =~ /(\d{1,2})\.(\d{1,2})\.(\d{1,2})/);
   }
   else
   {
      my @row;

      $query = $dbh->prepare("SHOW VARIABLES LIKE 'version';");
      $query->execute();
      @row = $query->fetchrow_array();
      ($major, $minor, $patch) = ($row[1] =~ /(\d{1,2})\.(\d{1,2})\.(\d{1,2})/);
   }

   $MySQL_version = sprintf("%d%02d%02d", $major, $minor, $patch);

   # Innodb_ status values were added in 5.0.2
   if($MySQL_version < 50002)
   {
      $have_innodb_vals = 0;
      print "get_MySQL_version: no InnoDB reports because MySQL version is older than 5.0.2\n" if $op{debug};
   }
}

sub set_myisam_vals
{
   print "set_myisam_vals\n" if $op{debug};

   $questions = $stats{'Questions'};

   $key_read_ratio = sprintf "%.2f",
                     ($stats{'Key_read_requests'} ?
                      100 - ($stats{'Key_reads'} / $stats{'Key_read_requests'}) * 100 :
                      0);

   $key_write_ratio = sprintf "%.2f",
                      ($stats{'Key_write_requests'} ?
                       100 - ($stats{'Key_writes'} / $stats{'Key_write_requests'}) * 100 :
                       0);

   $key_cache_block_size = (defined $vars{'key_cache_block_size'} ?
                            $vars{'key_cache_block_size'} :
                            1024);

   $key_buffer_used = $stats{'Key_blocks_used'} * $key_cache_block_size;

   if(defined $stats{'Key_blocks_unused'}) # MySQL 4.1.2+
   {
      $key_buffer_usage =  $vars{'key_buffer_size'} -
                           ($stats{'Key_blocks_unused'} * $key_cache_block_size);
   }
   else { $key_buffer_usage = -1; }

   # Data Manipulation Statements: http://dev.mysql.com/doc/refman/5.0/en/data-manipulation.html
   %DMS_vals =
   (
      SELECT  => $stats{'Com_select'},
      INSERT  => $stats{'Com_insert'}  + $stats{'Com_insert_select'},
      REPLACE => $stats{'Com_replace'} + $stats{'Com_replace_select'},
      UPDATE  => $stats{'Com_update'}  +
                 (exists $stats{'Com_update_multi'} ? $stats{'Com_update_multi'} : 0),
      DELETE  => $stats{'Com_delete'}  +
                 (exists $stats{'Com_delete_multi'} ? $stats{'Com_delete_multi'} : 0)
   );

   $dms = $DMS_vals{SELECT} + $DMS_vals{INSERT} + $DMS_vals{REPLACE} + $DMS_vals{UPDATE} + $DMS_vals{DELETE};

   $slow_query_t = format_u_time($vars{long_query_time});

}

sub set_ib_vals
{
   print "set_ib_vals\n" if $op{debug};

   $ib_bp_used  = ($stats{'Innodb_buffer_pool_pages_total'} -
                   $stats{'Innodb_buffer_pool_pages_free'}) *
                   $stats{'Innodb_page_size'};

   $ib_bp_total = $stats{'Innodb_buffer_pool_pages_total'} * $stats{'Innodb_page_size'};

   $ib_bp_read_ratio = sprintf "%.2f",
                       ($stats{'Innodb_buffer_pool_read_requests'} ?
                        100 - ($stats{'Innodb_buffer_pool_reads'} /
                           $stats{'Innodb_buffer_pool_read_requests'}) * 100 :
                        0);
}

sub write_relative_report
{
   print "write_relative_report\n" if $op{debug};

   %stats_present = %stats;

   for(keys %stats)
   {
      if($stats_past{$_} =~ /\d+/)
      {
         if($stats_present{$_} >= $stats_past{$_}) # Avoid negative values
         {
            $stats{$_} = $stats_present{$_} - $stats_past{$_};
         }
      }
   }

   # These values are either "at present" or "high water marks".
   # Therefore, it is more logical to not relativize these values.
   # Doing otherwise causes strange and misleading values.
   $stats{'Key_blocks_used'}      = $stats_present{'Key_blocks_used'};
   $stats{'Open_tables'}          = $stats_present{'Open_tables'};
   $stats{'Max_used_connections'} = $stats_present{'Max_used_connections'};
   $stats{'Threads_running'}      = $stats_present{'Threads_running'};
   $stats{'Threads_connected'}    = $stats_present{'Threads_connected'};
   $stats{'Threads_cached'}       = $stats_present{'Threads_cached'};
   $stats{'Qcache_free_blocks'}   = $stats_present{'Qcache_free_blocks'};
   $stats{'Qcache_total_blocks'}  = $stats_present{'Qcache_total_blocks'};
   $stats{'Qcache_free_memory'}   = $stats_present{'Qcache_free_memory'};
   if($have_innodb_vals)
   {
      $stats{'Innodb_page_size'}                 = $stats_present{'Innodb_page_size'};
      $stats{'Innodb_buffer_pool_pages_data'}    = $stats_present{'Innodb_buffer_pool_pages_data'};
      $stats{'Innodb_buffer_pool_pages_dirty'}   = $stats_present{'Innodb_buffer_pool_pages_dirty'};
      $stats{'Innodb_buffer_pool_pages_free'}    = $stats_present{'Innodb_buffer_pool_pages_free'};
      $stats{'Innodb_buffer_pool_pages_latched'} = $stats_present{'Innodb_buffer_pool_pages_latched'};
      $stats{'Innodb_buffer_pool_pages_misc'}    = $stats_present{'Innodb_buffer_pool_pages_misc'};
      $stats{'Innodb_buffer_pool_pages_total'}   = $stats_present{'Innodb_buffer_pool_pages_total'};
      $stats{'Innodb_data_pending_fsyncs'}       = $stats_present{'Innodb_data_pending_fsyncs'};
      $stats{'Innodb_data_pending_reads'}        = $stats_present{'Innodb_data_pending_reads'};
      $stats{'Innodb_data_pending_writes'}       = $stats_present{'Innodb_data_pending_writes'};

      # Innodb_row_lock_ values were added in MySQL 5.0.3
      if($MySQL_version >= 50003)
      {
         $stats{'Innodb_row_lock_current_waits'} = $stats_present{'Innodb_row_lock_current_waits'};
         $stats{'Innodb_row_lock_time_avg'}      = $stats_present{'Innodb_row_lock_time_avg'};
         $stats{'Innodb_row_lock_time_max'}      = $stats_present{'Innodb_row_lock_time_max'};
      }
   }

   get_Com_values();

   %stats_past = %stats_present;

   set_myisam_vals();
   set_ib_vals() if $have_innodb_vals;

   write_report();
}

sub write_report
{
   print "write_report\n" if $op{debug};

   $~ = 'MYSQL_TIME', write;
   $~ = 'KEY_BUFF_MAX', write;
   if($key_buffer_usage != -1) { $~ = 'KEY_BUFF_USAGE', write }
   $~ = 'KEY_RATIOS', write;
   write_DTQ();
   $~ = 'SLOW_DMS', write;
   write_DMS();
   write_Com();
   $~ = 'SAS', write; 
   write_qcache(); 
   $~ = 'REPORT_END', write;
   $~ = 'TAB', write;

   write_InnoDB() if $have_innodb_vals;
}

sub sec_to_dhms # Seconds to days hours:minutes:seconds
{
   my $s = shift;
   my ($d, $h, $m) = (0, 0, 0);

   return '0 0:0:0' if $s <= 0;

   if($s >= 86400)
   {
      $d = int $s / 86400;
      $s -= $d * 86400;
   }

   if($s >= 3600)
   {
     $h = int $s / 3600;
     $s -= $h * 3600;
   }
   
   $m = int $s / 60;
   $s -= $m * 60;
   
   return "$d $h:$m:$s";
}

sub make_short
{
   my ($number, $kb, $d) = @_;
   my $n = 0;
   my $short;

   $d ||= 2;

   if($kb) { while ($number > 1023) { $number /= 1024; $n++; }; }
   else { while ($number > 999) { $number /= 1000; $n++; }; }

   $short = sprintf "%.${d}f%s", $number, ('','k','M','G','T')[$n];
   if($short =~ /^(.+)\.(00)$/) { return $1; } # 12.00 -> 12 but not 12.00k -> 12k

   return $short;
}

# What began as a simple but great idea has become the new standard:
# long_query_time in microseconds. For MySQL 5.1.21+ and 6.0.4+ this
# is now standard. For 4.1 and 5.0 patches, the architects of this
# idea provide: http://www.mysqlperformanceblog.com/mysql-patches/
# Relevant notes in MySQL manual:
# http://dev.mysql.com/doc/refman/5.1/en/slow-query-log.html
# http://dev.mysql.com/doc/refman/6.0/en/slow-query-log.html
#
# The format_u_time sub simply beautifies long_query_time.

sub format_u_time  # format microsecond (µ) time value
{
   # 0.000000 - 0.000999 = 0 - 999 µ
   # 0.001000 - 0.999999 = 1 ms - 999.999 ms
   # 1.000000 - n.nnnnnn = 1 s - n.nnnnn s

   my $t = shift;
   my $f;  # formatted µ time
   my $u = chr(($WIN ? 230 : 181));

   $t = 0 if $t < 0;

   if($t > 0 && $t <= 0.000999)
   {
      $f = ($t * 1000000) . " $u";
   }
   elsif($t >= 0.001000 && $t <= 0.999999)
   {
      $f = ($t * 1000) . ' ms';
   }
   elsif($t >= 1)
   {
      $f = ($t * 1) . ' s';  # * 1 to remove insignificant zeros
   }
   else
   {
      $f = 0;  # $t should = 0 at this point
   }

   return $f;
}

sub perc # Percentage
{
   my($is, $of) = @_;
   return sprintf "%.2f", ($is * 100) / ($of ||= 1);
}

sub t # Time average per second
{
   my $val = shift;
   return 0 if !$val;
   return(make_short($val / $stats{'Uptime'}, 0, 1));
}

sub email_report # Email given report to $op{'email'}
{
   print "email_report\n" if $op{debug};

   return if $WIN;

   my $report = shift;

   open SENDMAIL, "|/usr/sbin/sendmail -t";
   print SENDMAIL "From: mysqlreport\n";
   print SENDMAIL "To: $op{email}\n";
   print SENDMAIL "Subject: MySQL status report on " . ($mycnf{'host'} || 'localhost') . "\n\n";
   print SENDMAIL `cat $report`;
   close SENDMAIL;
}

sub cat_report # Print given report to screen
{
   print "cat_report\n" if $op{debug};

   my $report = shift;
   my @report;

   open REPORT, "< $report";
   @report = <REPORT>;
   close REPORT;
   print @report;
}

sub get_Com_values
{
   print "get_Com_values\n" if $op{debug};

   %Com_vals = ();

   # Make copy of just the Com_ values
   for(keys %stats)
   {
      if(grep /^Com_/, $_ and $stats{$_} > 0)
      {
         /^Com_(.*)/;
         $Com_vals{$1} = $stats{$_};
      }
   }

   # Remove DMS values
   delete $Com_vals{'select'};
   delete $Com_vals{'insert'};
   delete $Com_vals{'insert_select'};
   delete $Com_vals{'replace'};
   delete $Com_vals{'replace_select'};
   delete $Com_vals{'update'};
   delete $Com_vals{'update_multi'} if exists $Com_vals{'update_multi'};
   delete $Com_vals{'delete'};
   delete $Com_vals{'delete_multi'} if exists $Com_vals{'delete_multi'};
}

sub write_DTQ # Write DTQ report in descending order by values
{
   print "write_DTQ\n" if $op{debug};

   $~ = 'DTQ';

   my %DTQ;
   my $first = 1;

   # Total Com values
   $stat_val = 0;
   for(values %Com_vals) { $stat_val += $_; }
   $DTQ{'Com_'} = $stat_val;

   $DTQ{'DMS'}      = $dms;
   $DTQ{'QC Hits'}  = $stats{'Qcache_hits'} if $stats{'Qcache_hits'} != 0;
   $DTQ{'COM_QUIT'} = int (($stats{'Connections'} - 2) - ($stats{'Aborted_clients'} / 2));

   $stat_val = 0;
   for(values %DTQ) { $stat_val += $_; }
   if($questions != $stat_val)
   {
      $DTQ{($questions > $stat_val ? '+Unknown' : '-Unknown')} = abs $questions - $stat_val;
   }

   for(sort { $DTQ{$b} <=> $DTQ{$a} } keys(%DTQ))
   {
      if($first) { $stat_label = '%Total:'; $first = 0; }
      else       { $stat_label = ''; }

      $stat_name = $_;
      $stat_val  = $DTQ{$_};
      write;
   }
}

sub write_DMS # Write DMS report in descending order by values
{
   print "write_DMS\n" if $op{debug};

   $~ = 'DMS';

   for(sort { $DMS_vals{$b} <=> $DMS_vals{$a} } keys(%DMS_vals))
   {
      $stat_name = $_;
      $stat_val  = $DMS_vals{$_};
      write;
   }
}

sub write_Com # Write COM report in descending order by values
{
   print "write_Com\n" if $op{debug};

   my $i = $op{'com'};

   $~ = 'COM_1';

   # Total Com values and write first line of COM report
   $stat_label = '%Total:' unless $op{'dtq'};
   $stat_val   = 0;
   for(values %Com_vals) { $stat_val += $_; }
   write;

   $~ = 'COM_2';

   # Sort remaining Com values, print only the top $op{'com'} number of values
   for(sort { $Com_vals{$b} <=> $Com_vals{$a} } keys(%Com_vals))
   {
      $stat_name = $_;
      $stat_val  = $Com_vals{$_};
      write;

      last if !(--$i);
   }
}

sub write_qcache
{
   print "write_qcache\n" if $op{debug};

   # Query cache was added in 4.0.1, but have_query_cache was added in 4.0.2,
   # ergo this method is slightly more reliable
   return if not exists $vars{'query_cache_size'};
   return if $vars{'query_cache_size'} == 0;

   $qc_mem_used = $vars{'query_cache_size'} - $stats{'Qcache_free_memory'};
   $qc_hi_r = sprintf "%.2f", $stats{'Qcache_hits'} / ($stats{'Qcache_inserts'} ||= 1);
   $qc_ip_r = sprintf "%.2f", $stats{'Qcache_inserts'} / ($stats{'Qcache_lowmem_prunes'} ||= 1);

   $~ = 'QCACHE';
   write;
}

sub write_InnoDB
{
   print "write_InnoDB\n" if $op{debug};

   return if not defined $stats{'Innodb_page_size'};

   $~ = 'IB';
   write;

   # Innodb_row_lock_ values were added in MySQL 5.0.3
   if($MySQL_version >= 50003)
   {
      $~ = 'IB_LOCK';
      write;
   }

   # Data, Pages, Rows
   $~ = 'IB_DPR';
   write;
}

sub have_op
{
   my $key = shift;
   return 1 if (exists $op{$key} && $op{$key} ne '');
   return 0;
}

sub sig_handler
{
   print "\nReceived signal at " , scalar localtime , "\n";
   exit_tasks_and_cleanup();
   exit;
}

sub exit_tasks_and_cleanup
{
   print "exit_tasks_and_cleanup\n" if $op{debug};

   close $tmpfile_fh;
   select STDOUT unless $op{'detach'};

   email_report($tmpfile) if $op{'email'};

   cat_report($tmpfile) unless $op{'detach'};

   if($op{'outfile'})
   {
      if($WIN) { `move $tmpfile $op{outfile}`; }
      else     { `mv $tmpfile $op{outfile}`;   }
   }
   else
   {
      if($WIN) { `del $tmpfile`;   }
      else     { `rm -f $tmpfile`; }
   }

   if(!$op{'infile'} && !$relative_infiles)
   {
      if($op{'flush-status'})
      {
         $query = $dbh->prepare("FLUSH STATUS;");
         $query->execute();
      }

      $query->finish();
      $dbh->disconnect();
   }
}

#
# Formats
#

format MYSQL_TIME =
MySQL @<<<<<<<<<<<<<<<<  uptime @<<<<<<<<<<<   @>>>>>>>>>>>>>>>>>>>>>>>>
$vars{'version'}, sec_to_dhms($real_uptime), (($op{infile} || $relative_infiles) ? '' : scalar localtime)
.

format KEY_BUFF_MAX =

__ Key _________________________________________________________________
Buffer used   @>>>>>> of @>>>>>>  %Used: @>>>>>
make_short($key_buffer_used, 1), make_short($vars{'key_buffer_size'}, 1), perc($key_buffer_used, $vars{'key_buffer_size'})
.

format KEY_BUFF_USAGE =
  Current     @>>>>>>            %Usage: @>>>>>
make_short($key_buffer_usage, 1), perc($key_buffer_usage, $vars{'key_buffer_size'})
.

format KEY_RATIOS =
Write hit     @>>>>>%
$key_write_ratio
Read hit      @>>>>>%
$key_read_ratio

__ Questions ___________________________________________________________
Total       @>>>>>>>>  @>>>>>/s
make_short($questions), t($questions)
.

format DTQ =
  @<<<<<<<  @>>>>>>>>  @>>>>>/s  @>>>>>> @>>>>>
$stat_name, make_short($stat_val), t($stat_val), $stat_label, perc($stat_val, $questions)
.

format SLOW_DMS =
Slow @<<<<<<< @>>>>>>  @>>>>>/s          @>>>>>  %DMS: @>>>>>  Log: @>> 
$slow_query_t, make_short($stats{'Slow_queries'}), t($stats{'Slow_queries'}), perc($stats{'Slow_queries'}, $questions), perc($stats{'Slow_queries'}, $dms), $vars{'log_slow_queries'}
DMS         @>>>>>>>>  @>>>>>/s          @>>>>>
make_short($dms), t($dms), perc($dms, $questions)
.

format DMS =
  @<<<<<<<  @>>>>>>>>  @>>>>>/s          @>>>>>        @>>>>>
$stat_name, make_short($stat_val), t($stat_val), perc($stat_val, $questions), perc($stat_val, $dms)
.

format COM_1 =
Com_        @>>>>>>>>  @>>>>>/s          @>>>>>
make_short($stat_val), t($stat_val), perc($stat_val, $questions)
.

format COM_2 =
  @<<<<<<<<<< @>>>>>>  @>>>>>/s          @>>>>>
$stat_name, make_short($stat_val), t($stat_val), perc($stat_val, $questions)
.

format SAS =

__ SELECT and Sort _____________________________________________________
Scan          @>>>>>>   @>>>>/s %SELECT: @>>>>>
make_short($stats{'Select_scan'}), t($stats{'Select_scan'}), perc($stats{'Select_scan'}, $stats{'Com_select'})
Range         @>>>>>>   @>>>>/s          @>>>>>
make_short($stats{'Select_range'}), t($stats{'Select_range'}), perc($stats{'Select_range'}, $stats{'Com_select'})
Full join     @>>>>>>   @>>>>/s          @>>>>>
make_short($stats{'Select_full_join'}), t($stats{'Select_full_join'}), perc($stats{'Select_full_join'}, $stats{'Com_select'})
Range check   @>>>>>>   @>>>>/s          @>>>>>
make_short($stats{'Select_range_check'}), t($stats{'Select_range_check'}), perc($stats{'Select_range_check'}, $stats{'Com_select'})
Full rng join @>>>>>>   @>>>>/s          @>>>>>
make_short($stats{'Select_full_range_join'}), t($stats{'Select_full_range_join'}), perc($stats{'Select_full_range_join'}, $stats{'Com_select'})
Sort scan     @>>>>>>   @>>>>/s
make_short($stats{'Sort_scan'}), t($stats{'Sort_scan'})
Sort range    @>>>>>>   @>>>>/s
make_short($stats{'Sort_range'}), t($stats{'Sort_range'})
Sort mrg pass @>>>>>>   @>>>>/s
make_short($stats{'Sort_merge_passes'}), t($stats{'Sort_merge_passes'})
.

format QCACHE =

__ Query Cache _________________________________________________________
Memory usage  @>>>>>> of @>>>>>>  %Used: @>>>>>
make_short($qc_mem_used, 1), make_short($vars{'query_cache_size'}, 1), perc($qc_mem_used, $vars{'query_cache_size'})
Block Fragmnt @>>>>>%
perc($stats{'Qcache_free_blocks'}, $stats{'Qcache_total_blocks'})
Hits          @>>>>>>   @>>>>/s
make_short($stats{'Qcache_hits'}), t($stats{'Qcache_hits'})
Inserts       @>>>>>>   @>>>>/s
make_short($stats{'Qcache_inserts'}), t($stats{'Qcache_inserts'})
Insrt:Prune @>>>>>>:1   @>>>>/s
make_short($qc_ip_r), t($stats{'Qcache_inserts'} - $stats{'Qcache_lowmem_prunes'})
Hit:Insert  @>>>>>>:1
$qc_hi_r, t($qc_hi_r)
.

# Not really the end...
format REPORT_END =

__ Table Locks _________________________________________________________
Waited      @>>>>>>>>  @>>>>>/s  %Total: @>>>>>
make_short($stats{'Table_locks_waited'}), t($stats{'Table_locks_waited'}), perc($stats{'Table_locks_waited'}, $stats{'Table_locks_waited'} + $stats{'Table_locks_immediate'});
Immediate   @>>>>>>>>  @>>>>>/s
make_short($stats{'Table_locks_immediate'}), t($stats{'Table_locks_immediate'})

__ Tables ______________________________________________________________
Open        @>>>>>>>> of @>>>    %Cache: @>>>>>
$stats{'Open_tables'}, $vars{'table_cache'}, perc($stats{'Open_tables'}, $vars{'table_cache'})
Opened      @>>>>>>>>  @>>>>>/s
make_short($stats{'Opened_tables'}), t($stats{'Opened_tables'})

__ Connections _________________________________________________________
Max used    @>>>>>>>> of @>>>      %Max: @>>>>>
$stats{'Max_used_connections'}, $vars{'max_connections'}, perc($stats{'Max_used_connections'}, $vars{'max_connections'})
Total       @>>>>>>>>  @>>>>>/s
make_short($stats{'Connections'}), t($stats{'Connections'})

__ Created Temp ________________________________________________________
Disk table  @>>>>>>>>  @>>>>>/s
make_short($stats{'Created_tmp_disk_tables'}), t($stats{'Created_tmp_disk_tables'})
Table       @>>>>>>>>  @>>>>>/s    Size: @>>>>>
make_short($stats{'Created_tmp_tables'}), t($stats{'Created_tmp_tables'}), make_short($vars{'tmp_table_size'}, 1, 1)
File        @>>>>>>>>  @>>>>>/s
make_short($stats{'Created_tmp_files'}), t($stats{'Created_tmp_files'})
.

format TAB =

__ Threads _____________________________________________________________
Running     @>>>>>>>> of @>>>
$stats{'Threads_running'}, $stats{'Threads_connected'}
Cached      @>>>>>>>> of @>>>      %Hit: @>>>>>
$stats{'Threads_cached'}, $vars{'thread_cache_size'}, make_short(100 - perc($stats{'Threads_created'}, $stats{'Connections'}))
Created     @>>>>>>>>  @>>>>>/s
make_short($stats{'Threads_created'}), t($stats{'Threads_created'})
Slow        @>>>>>>>>  @>>>>>/s
$stats{'Slow_launch_threads'}, t($stats{'Slow_launch_threads'})

__ Aborted _____________________________________________________________
Clients     @>>>>>>>>  @>>>>>/s
make_short($stats{'Aborted_clients'}), t($stats{'Aborted_clients'})
Connects    @>>>>>>>>  @>>>>>/s
make_short($stats{'Aborted_connects'}), t($stats{'Aborted_connects'})

__ Bytes _______________________________________________________________
Sent        @>>>>>>>>  @>>>>>/s
make_short($stats{'Bytes_sent'}), t($stats{'Bytes_sent'})
Received    @>>>>>>>>  @>>>>>/s
make_short($stats{'Bytes_received'}), t($stats{'Bytes_received'})
.

format IB =

__ InnoDB Buffer Pool __________________________________________________
Usage         @>>>>>> of @>>>>>>  %Used: @>>>>>
make_short($ib_bp_used, 1), make_short($ib_bp_total, 1), perc($ib_bp_used, $ib_bp_total)
Read hit      @>>>>>%
$ib_bp_read_ratio;
Pages
  Free      @>>>>>>>>            %Total: @>>>>>
make_short($stats{'Innodb_buffer_pool_pages_free'}), perc($stats{'Innodb_buffer_pool_pages_free'}, $stats{'Innodb_buffer_pool_pages_total'})
  Data      @>>>>>>>>                    @>>>>> %Drty: @>>>>>
make_short($stats{'Innodb_buffer_pool_pages_data'}), perc($stats{'Innodb_buffer_pool_pages_data'}, $stats{'Innodb_buffer_pool_pages_total'}), perc($stats{'Innodb_buffer_pool_pages_dirty'}, $stats{'Innodb_buffer_pool_pages_data'})
  Misc      @>>>>>>>>                    @>>>>>
  $stats{'Innodb_buffer_pool_pages_misc'}, perc($stats{'Innodb_buffer_pool_pages_misc'}, $stats{'Innodb_buffer_pool_pages_total'})
  Latched   @>>>>>>>>                    @>>>>>
$stats{'Innodb_buffer_pool_pages_latched'}, perc($stats{'Innodb_buffer_pool_pages_latched'}, $stats{'Innodb_buffer_pool_pages_total'})
Reads       @>>>>>>>>  @>>>>>/s  
make_short($stats{'Innodb_buffer_pool_read_requests'}), t($stats{'Innodb_buffer_pool_read_requests'})
  From file @>>>>>>>>  @>>>>>/s          @>>>>>
make_short($stats{'Innodb_buffer_pool_reads'}), t($stats{'Innodb_buffer_pool_reads'}), perc($stats{'Innodb_buffer_pool_reads'}, $stats{'Innodb_buffer_pool_read_requests'})
  Ahead Rnd @>>>>>>>>  @>>>>>/s
$stats{'Innodb_buffer_pool_read_ahead_rnd'}, t($stats{'Innodb_buffer_pool_read_ahead_rnd'})
  Ahead Sql @>>>>>>>>  @>>>>>/s
$stats{'Innodb_buffer_pool_read_ahead_seq'}, t($stats{'Innodb_buffer_pool_read_ahead_seq'})
Writes      @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_buffer_pool_write_requests'}), t($stats{'Innodb_buffer_pool_write_requests'})
Flushes     @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_buffer_pool_pages_flushed'}), t($stats{'Innodb_buffer_pool_pages_flushed'})
Wait Free   @>>>>>>>>  @>>>>>/s
$stats{'Innodb_buffer_pool_wait_free'}, t($stats{'Innodb_buffer_pool_wait_free'})
.

format IB_LOCK =

__ InnoDB Lock _________________________________________________________
Waits       @>>>>>>>>  @>>>>>/s
$stats{'Innodb_row_lock_waits'}, t($stats{'Innodb_row_lock_waits'})
Current     @>>>>>>>>
$stats{'Innodb_row_lock_current_waits'}
Time acquiring
  Total     @>>>>>>>> ms
$stats{'Innodb_row_lock_time'}
  Average   @>>>>>>>> ms
$stats{'Innodb_row_lock_time_avg'}
  Max       @>>>>>>>> ms
$stats{'Innodb_row_lock_time_max'}
.

format IB_DPR =

__ InnoDB Data, Pages, Rows ____________________________________________
Data
  Reads     @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_data_reads'}), t($stats{'Innodb_data_reads'})
  Writes    @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_data_writes'}), t($stats{'Innodb_data_writes'})
  fsync     @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_data_fsyncs'}), t($stats{'Innodb_data_fsyncs'})
  Pending
    Reads   @>>>>>>>>
$stats{'Innodb_data_pending_reads'}, t($stats{'Innodb_data_pending_reads'})
    Writes  @>>>>>>>>
$stats{'Innodb_data_pending_writes'}, t($stats{'Innodb_data_pending_writes'})
    fsync   @>>>>>>>>
$stats{'Innodb_data_pending_fsyncs'}, t($stats{'Innodb_data_pending_fsyncs'})

Pages
  Created   @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_pages_created'}), t($stats{'Innodb_pages_created'})
  Read      @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_pages_read'}), t($stats{'Innodb_pages_read'})
  Written   @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_pages_written'}), t($stats{'Innodb_pages_written'})

Rows
  Deleted   @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_rows_deleted'}), t($stats{'Innodb_rows_deleted'})
  Inserted  @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_rows_inserted'}), t($stats{'Innodb_rows_inserted'})
  Read      @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_rows_read'}), t($stats{'Innodb_rows_read'})
  Updated   @>>>>>>>>  @>>>>>/s
make_short($stats{'Innodb_rows_updated'}), t($stats{'Innodb_rows_updated'})
.
