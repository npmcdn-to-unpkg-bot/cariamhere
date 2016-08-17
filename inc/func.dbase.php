<?php

// show query statement and error message if an error occurs
// db_debug(true)
function db_debug($debug) {
  global $env;
  $env['_db_debug_'] = $debug;
}
// measure mysql_query() time using microtime()
function db_clock($flag) {
  global $env;
  $env['_db_clock_'] = $flag;
}
function db_escape_string($str) {
  return mysql_real_escape_string($str);
}
function db_query($qry, $debug=0) {
  if ($debug) { dd($qry); return; }
  global $env;
  if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = mysql_query($qry);
  $err = mysql_error();
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_query_select($qry, $debug=0) {
  if ($debug) { dd($qry); return; }
  global $env;
  if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = mysql_query($qry);
  $err = mysql_error();
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_fetch($ret) {
  return mysql_fetch_assoc($ret);
}
function db_fetchone($qry, $debug=0) {
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = mysql_query($qry);
  $err = mysql_error();
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return mysql_fetch_assoc($ret);
  return $ret;
}
function db_query_modify($qry, $debug=0) { // update or delete or insert
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = mysql_query($qry);
  $err = mysql_error();
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_query_update($qry, $debug=0) {
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = mysql_query($qry);
  $err = mysql_error();
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_query_delete($qry, $debug=0) {
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = mysql_query($qry);
  $err = mysql_error();
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_query_insert($qry, $debug=0) {
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = mysql_query($qry);
  $err = mysql_error();
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}

?>
