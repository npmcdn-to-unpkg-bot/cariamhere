<?php

class location {
  var $debug = false;

  function location() {

  }

  function option_location_group($preset='') {
    $a = array('=선택=:null');
    $b = $this->location_groups();
    $list = array_merge($a, $b);

    $opt = option_general($list, $preset);
    return $opt;
  }

  // APIs
  // pflag = true 경유지 포함
  function location_groups($pflag=true) {
    if ($pflag)
    return array('공항','숙소','행사장','기타','경유지');
    else
    return array('공항','숙소','행사장','기타');
  }

  function select_option_location($preset='') {
    $opt = '';
    $list = $this->list_location();

    $flag = false;
    foreach ($list as $item) {
      $id = $item['location_id'];

      $title = $item['title'];
      $group = $item['group'];
      $tit = "$title($group)";

      if ($preset == $id) {
        $sel = ' selected';
        $flag = true;
      } else $sel = '';
      $opt .= "<option value='$id'$sel>$tit</option>";
    }
    if (!$flag) {
      $opt .= "<option value='$preset' selected>$preset</option>";
    }
    return $opt;
  }

  function id2name($id) {
    $qry = "SELECT * FROM location WHERE id='$id'";
    $row = db_fetchone($qry);
    return $row['loc_title'];
  }
  function location_name($id) {
    return $this->id2name($id);
  }

  function get_location($id) {
    $qry = "SELECT * FROM location WHERE id='$id'";
    $row = db_fetchone($qry);
    return $row;
  }

  // APIs
  function list_location($group='', $treeflag=false) {

    $w = array('1');
    $w[] = "loc_group!='경유지'";
    if ($group) $w[] = "loc_group='$group'";
    $sql_where = " WHERE ".join(" AND ", $w);

    $qry = "SELECT * FROM location $sql_where";
    $ret = db_query($qry);

    if ($treeflag) {

      $info = array();
      while ($row = db_fetch($ret)) {
        $group = $row['loc_group'];
        $info[$group][] = array(
          'location_id'=>$row['id'],
          'title'=>$row['loc_title'],
          'group'=>$row['loc_group'],
          'lat'=>$row['lat'],
          'lng'=>$row['lng'],
        );
      }
      $groups = $this->location_groups(false);
      $info['groups'] = $groups;

    } else {

      $info = array();
      while ($row = db_fetch($ret)) {
        $info[] = array(
          'location_id'=>$row['id'],
          'group'=>$row['loc_group'],
          'title'=>$row['loc_title'],
          'lat'=>$row['lat'],
          'lng'=>$row['lng'],
        );
      }
    }
    return $info;
  }

  // 경유지 정보
  function list_passby_locations() {
    $w = array('1');
    $w[] = "loc_group='경유지'";
    $sql_where = " WHERE ".join(" AND ", $w);

    $qry = "SELECT * FROM location $sql_where";
    $ret = db_query($qry);

    $info = array();
    while ($row = db_fetch($ret)) {
      $info[] = array(
        'location_id'=>$row['id'],
        'title'=>$row['loc_title'],
        'lat'=>$row['lat'],
        'lng'=>$row['lng'],
      );
    }
    return $info;
  }

  // 장소선택에서 기타장소입력시, db에등록한후 ID리턴
  function location_add($loc_id, $title_etc) {
    if ($loc_id > 0) return $loc_id;

    $title = trim($title_etc);
    if (!$title) return 0;

    $qry = "SELECT max(id) max from location";
    $row = db_fetchone($qry);
    $id = $row['max']+1;

    $lat = 37.56647878771299; $lng = 126.97829604148865; // 서울시청
    $qry = "insert into location "
     ." set id='$id', loc_title='$title', loc_group='기타', lat='$lat', lng='$lng', udate=now(), idate=now()"; 
    db_query($qry);

    return $id;
  }



};

?>
