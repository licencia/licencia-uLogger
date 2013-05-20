<?php














/***************************************************
 * Not used
 **************************************************/

function filesize_recursive($path){
  if(!file_exists($path)) return 0;
  if(is_file($path)) return filesize($path);
  $ret = 0;
  foreach(glob($path."/*") as $fn)
  $ret += filesize_recursive($fn);
  return $ret;
}

function formatSize($bytes)
{
  $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
  for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
}

function getSize() {
  /* get disk space free (in bytes) */
  $df = 1000000; //disk_free_space(APACHE_DIR);
  /* and get disk space total (in bytes)  */
  $dt = disk_total_space(APACHE_DIR);
  /* now we calculate the disk space used (in bytes) */
  $du = $dt - $df;
  /* percentage of disk used - this will be used to also set the width % of the progress bar */
  $dp = sprintf('%.2f',($du / $dt) * 100);
  /* size of trace directory */
  $fs = filesize_recursive(TRACE_DIR);
  /* max trace file space */
  $ts = $df + $fs;
  /* procentage of trace storage used */
  $tp = sprintf('%.2f',($fs / $ts) * 100);
  return array('df' => formatSize($df), 'dt' => formatSize($dt), 'du' => formatSize($du), 'dp' => $dp, 'ts' => formatSize($ts), 'fs' => formatSize($fs), 'tp' => $tp);
}