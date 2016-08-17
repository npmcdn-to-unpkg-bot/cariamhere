<?php

  include("../path.php");
  include("$env[prefix]/inc/common.php");

  MainPageHead('Home');
  ParagraphTitle('Home');

# 참고 # http://api.jqueryui.com/autocomplete/

  print<<<EOS
<script>
  $(function() {
    var availableTags = [
"가나다",
"가나라",
"가나하",
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];
    $( "#tags" ).autocomplete({
      source: availableTags
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
