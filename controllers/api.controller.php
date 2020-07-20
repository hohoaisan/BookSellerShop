<?php
use Database\Database as Database;
$getProvince = function() {
  $result = Database::queryResults("select id, name from province", array());
  echo json_encode($result, JSON_UNESCAPED_UNICODE);
};

$getDistrict = function($provinceid) {
  $result = Database::queryResults("select id, name from district where pid=?", array($provinceid));
  echo json_encode($result, JSON_UNESCAPED_UNICODE);
};

$getward =  function($provinceid,$districtid) {
  $sql = "select ward.id,ward.name from ward,district,province where ward.did = district.id and district.pid = province.id
	and ward.did = ?
	and district.pid = ?
	";
  $result = Database::queryResults($sql, array($districtid,$provinceid));
  echo json_encode($result, JSON_UNESCAPED_UNICODE);
};
$getFullAddressInfo = function($wardid) {
  $sql = "select 
  province.id as provinceid,
  province.`name` as provincename,
  district.id as districtid,
  district.`name` as districtname,
  ward.id as wardid,
  ward.`name` as wardname
  from province,district,ward
  WHERE province.id=district.pid
  and district.id=ward.did
  and ward.id=?";
  $result = Database::querySingleResult($sql, array($wardid));
  return $result;
};