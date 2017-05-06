<?php
$uid=18;
// mysql db
$container=array();
$container['db']=connectPDO();
$msg=change_verify_status($uid);
echo $msg;
function change_verify_status($uid)
{
  global $container;
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  if(isset($uid))
  {
    $verified_status=1;
    try{
                // update the old password with new password
                $sql="update `user` set `verified_status`=:verified_status where `uid`=:uid";
                // Get DB Object
                $db = $container['db'];
                $p_stmt= $db->prepare($sql);
                $p_stmt->bindParam(":verified_status",$verified_status);
                $p_stmt->bindParam(":uid",$uid);
                $p_stmt->execute();
                if($p_stmt->rowCount()>0)
                {
                  $result = array("status"=>1,"msg"=>"verify status changed succesfully!");
                  return json_encode($result);
                }
                else {
                  $result['msg']='Sorry! Error in resetting status';
                  return json_encode($result);
                }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return json_encode($result);
    }
  }
  else {
    $result['msg']='Please provide all neccessary fields!!';
    return json_encode($result);
  }
}


function connectPDO($db_no=0)
{
    $dbname = array("gaudugdh_ios");
    $dbhost = array("gaudugdham.com");
    $dbuser = array("gaudugdh_user");
    $dbpass = array("apiios@gaudugdham");

    try {
      $dbh=new PDO('mysql:host='.$dbhost[$db_no].';dbname='.$dbname[$db_no], $dbuser[$db_no], $dbpass[$db_no]);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbh;
    } catch (Exception $e) {
      echo $e->getMessage();
    }

}
?>
