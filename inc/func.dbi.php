<?php

function db_connect() {
  global $conf;
  global $mysqli;

  $mysqli = new mysqli();
  if (!$mysqli) die('mysqli_init failed');

  if (!$mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 1')) {
    die('Setting MYSQLI_INIT_COMMAND failed');
  }

  $mysqli->options(MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'");

  if (!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
  }

  if (!$mysqli->real_connect($conf['dbhost'], $conf['dbuser'], $conf['dbpasswd'], $conf['dbname'], $conf['dbport'], $conf['dbsocket'])) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
  }
}


// show query statement and error message if an error occurs
// db_debug(true)
function db_debug($debug) {
  global $env;
  $env['_db_debug_'] = $debug;
}
function db_clock($flag) {
  global $env;
  $env['_db_clock_'] = $flag;
}
function db_escape_string($str) {
  global $mysqli;
  return $mysqli->real_escape_string($str);
}
function db_query($qry, $debug=0) {
  global $mysqli;
  global $env;
  if ($debug) { dd($qry); return; }
  if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = $mysqli->query($qry);
  $err = $mysqli->error;
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_error() {
  global $mysqli;
  return $mysqli->error;
}
function db_affected_rows() {
  global $mysqli;
  return $mysqli->affected_rows;
}
function db_query_select($qry, $debug=0) {
  global $mysqli;
  if ($debug) { dd($qry); return; }
  global $env;
  if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = $mysqli->query($qry);
  $err = $mysqli->error;
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_fetch($ret) {
  return $ret->fetch_assoc();
}
function db_fetchone($qry, $debug=0) {
  global $mysqli;
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = $mysqli->query($qry);
  $err = $mysqli->error;
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret->fetch_assoc();
}
function db_query_modify($qry, $debug=0) { // update or delete or insert
  global $mysqli;
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = $mysqli->query($qry);
  $err = $mysqli->error;
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_query_update($qry, $debug=0) {
  global $mysqli;
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = $mysqli->query($qry);
  $err = $mysqli->error;
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_query_delete($qry, $debug=0) {
  global $mysqli;
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = $mysqli->query($qry);
  $err = $mysqli->error;
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}
function db_query_insert($qry, $debug=0) {
  global $mysqli;
  if ($debug) { dd($qry); return; }
  global $env; if (@$env['_db_debug_']) dd($qry);
  if (@$env['_db_clock_']) $t1 = microtime();
  $ret = $mysqli->query($qry);
  $err = $mysqli->error;
  if (@$env['_db_clock_']) $t2 = microtime();
  if (@$env['_db_clock_']) dd(sprintf("query elapsed time = %f micro time", $t2-$t1));
  if ($err) { if (is_developer()) die($qry.$err); else die($err); }
  return $ret;
}

?>
