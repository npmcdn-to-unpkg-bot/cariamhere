<?php

  include("../path.php");
  include("$env[prefix]/inc/common.php");


$term = $form['term'];
if ($term) {
  print<<<EOS
[ "ActionScript", "AppleScript", "Asp"]
EOS;
  exit;
}

  MainPageHead('Home');
  ParagraphTitle('Home');

# 참고
# http://api.jqueryui.com/autocomplete/

  print<<<EOS
<script>
  $(function() {
    $( "#tags" ).autocomplete({
      source: "$env[self]",
    });
  });
  </script>
 
<div class="ui-widget">
  <label for="tags">Tags: </label>
  <input id="tags">
</div>
 
EOS;

  MainPageTail();
  exit;

?>
