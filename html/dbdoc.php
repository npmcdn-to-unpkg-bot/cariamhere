<?php

  include_once("./path.php");
  include_once("$env[prefix]/inc/common.php");

  MainPageHead('Tables');
  ParagraphTitle('Tables');

  $preset = $form['table'];
  $tables = array( 'carinfo', 'driver', 'person', 'location', 'notice', 'Ds', 'messaging', 'run', 'run_log');
  print('<ul class="nav nav-tabs">');
  foreach ($tables as $t) {
    if ($preset == $t) $active='active'; else $active = '';
    print<<<EOS
<li class="nav-item $active">
<a href='$env[self]?table=$t' class='nav-link'>$t</a>
</li>
EOS;
  }
  print('</ul>');

$table = $form['table'];
if ($table) {

  $qry = "show full columns from $table";
  $ret = db_query($qry);

  print<<<EOS
<table class='table'>
<thead class='thead-inverse'>
<tr>
<th>Field</th>
<th>Type</th>
<th>Null</th>
<th>Key</th>
<th>Comment</th>
</tr>
</thead>
EOS;
//<th>Default</th>
//<th>Extra</th>

  while ($row = db_fetch($ret)) {
    //dd($row);
    print<<<EOS
<tr>
<td>{$row['Field']}</td>
<td>{$row['Type']}</td>
<td>{$row['Null']}</td>
<td>{$row['Key']}</td>
<td>{$row['Comment']}</td>
</tr>
EOS;
//<td>{$row['Default']}</td>
//<td>{$row['Extra']}</td>
  }
  print<<<EOS
</table>
EOS;
}

  MainPageTail();
  exit;

?>
