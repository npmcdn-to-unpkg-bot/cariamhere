<?php


function cb_head_style() {
  $html=<<<EOS
<link rel=stylesheet href="/utl/bootstrap-datepicker/bootstrap-datepicker.min.css">

EOS;
  return $html;
}

function cb_head_script() {
  $html=<<<EOS
<script src="/utl/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
EOS;
  return $html;
}

?>
