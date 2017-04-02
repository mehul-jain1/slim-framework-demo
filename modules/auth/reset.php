<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;
function forget_password(Request $request, Response $response)
{
  global $container;
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['mobile_no']) && isset($request_params['new_password']))
  {
    // logging
    $container['logger']->addInfo("resetting password");
    // logged
    $mobile_no=trim($request_params['mobile_no']);
    $new_password=crypt(trim($request_params['new_password']));
    $sql="select `uid` from `user` where `mobile_no`=:mobile_no";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":mobile_no",$mobile_no);
        $p_stmt->execute();
          if($p_stmt->rowCount()>0)
          {
          $user_data=$p_stmt->fetch(PDO::FETCH_ASSOC);
          if(isset($user_data['uid']))
          {
                // update the old password with new password
                $sql="update `user` set `password`=:password where `uid`=:uid";
                // Get DB Object
                $db = $container['db'];
                $p_stmt= $db->prepare($sql);
                $p_stmt->bindParam(":password",$new_password);
                $p_stmt->bindParam(":uid",$user_data["uid"]);
                $p_stmt->execute();
                if($p_stmt->rowCount()>0)
                {
                  $result = array("status"=>1,"msg"=>"password changed succesfully!");
                  return $response->withStatus(200)->write(json_encode($result));
                }
                else {
                  $result['msg']='Sorry! Error in resetting password';
                  return $response->withStatus(302)->write(json_encode($result));
                }
                return $response;
          }
          else {
            $result['msg']='unable to reset password';
            return $response->withStatus(302)->write(json_encode($result));
          }
        }
        else {
          $result['msg']='This mobile no. is not registered!';
          return $response->withStatus(302)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(403)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Please provide all neccessary fields!!';
    return $response->withStatus(302)->write(json_encode($result));
  }
}
function reset_mobile($request,$response)
{
  global $container;
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['old_mobile_no']) && isset($request_params['new_mobile_no']))
  {
    // logging
    $container['logger']->addInfo("resetting mobile");
    // logged
    $old_mobile_no=trim($request_params['old_mobile_no']);
    $new_mobile_no=trim($request_params['new_mobile_no']);
    $sql="select `uid`,`mobile_no` from `user` where `mobile_no`=:old_mobile_no";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":old_mobile_no",$old_mobile_no);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
                $user_data=$p_stmt->fetch(PDO::FETCH_ASSOC);
                if(isset($user_data["uid"]))
                {
                    $sql="update `user` set `mobile_no`=:new_mobile_no where `uid`=:uid";
                    // Get DB Object
                    $db = $container['db'];
                    $p_stmt= $db->prepare($sql);
                    $p_stmt->bindParam(":new_mobile_no",$new_mobile_no);
                    $p_stmt->bindParam(":uid",$user_data["uid"]);
                    $p_stmt->execute();
                    if($p_stmt->rowCount()>0)
                    {
                      $result = array("status"=>1,"msg"=>"mobile no  changed succesfully!");
                      return $response->withStatus(200)->write(json_encode($result));
                    }
                    else {
                      $result['msg']='Sorry! Error in resetting mobile no.';
                      return $response->withStatus(302)->write(json_encode($result));
                    }
                }
                else {
                  $result['msg']='Sorry! Error in resetting mobile no.';
                  return $response->withStatus(302)->write(json_encode($result));
                }
                return $response;
        }
        else {
          $result['msg']='This mobile no. is not registered!';
          return $response->withStatus(302)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(403)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Please provide all neccessary fields!!';
    return $response->withStatus(302)->write(json_encode($result));
  }
  return $response->withStatus(200)->write(json_encode('hi'));
}
function update_profile($request,$response)
{
  global $container;
  $container['logger']->addInfo("updating user data");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  $user_data=array();
  if(isset($request_params['mobile_no']))
  {
  $mobile_no=trim($request_params['mobile_no']);
  $sql="select `uid` from `user` where `mobile_no`=:mobile_no";
  try{
      // Get DB Object
      $db = $container['db'];
      $p_stmt= $db->prepare($sql);
      $p_stmt->bindParam(":mobile_no",$mobile_no);
      $p_stmt->execute();
      if($p_stmt->rowCount()>0)
      {
              $user_data=$p_stmt->fetch(PDO::FETCH_ASSOC);
              if(isset($user_data["uid"]))
              {
                $uid=(int)$user_data["uid"];
              }
      }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  unset($user_data);
  if(isset($uid))
  {
    // process all the field to update if exists
    $user_columns=array();
    if(isset($request_params['name']) && $request_params['name'] !='')
    $user_columns[]="`name`=".":name";
    if(isset($request_params['address']) && $request_params['address'] !='')
    $user_columns[]="`address`=".":address";
    if(isset($request_params['city'])&& $request_params['city'] !='')
    $user_columns[]="`city`=".":city";
    if(isset($request_params['pincode']) && $request_params['pincode'] !='')
    $user_columns[]="`pincode`=".":pincode";
    if(count($user_columns)>0)
    {
    $user_data_sql="update  `user` set ".join(',',$user_columns)."  where `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($user_data_sql);
        $p_stmt->bindParam(":uid",$uid);
        if(isset($request_params['name']) && $request_params['name'] !='')
        $p_stmt->bindParam(":name",$request_params['name']);
        if(isset($request_params['address']) && $request_params['address'] !='')
        $p_stmt->bindParam(":address",$request_params['address']);
        if(isset($request_params['city'])&& $request_params['city'] !='')
        $p_stmt->bindParam(":city",$request_params['city']);
        if(isset($request_params['pincode']) && $request_params['pincode'] !='')
        $p_stmt->bindParam(":pincode",$request_params['pincode']);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            $result = array("status"=>1,"msg"=>"profile updated succesfully!");
            return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result['msg']='No changes done!';
          return $response->withStatus(404)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
    }
    else {
      $result['msg']='Please specify atleast one field to update ';
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Sorry! error occured in updating profile';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
?>
