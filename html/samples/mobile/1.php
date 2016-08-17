<?php

  print<<<EOS
<script>
if (navigator.geolocation) {
  alert('Geolocation is supported!');
}
else {
  alert('Geolocation is not supported for this Browser/OS version yet.');
}
</script>
EOS;

?>
