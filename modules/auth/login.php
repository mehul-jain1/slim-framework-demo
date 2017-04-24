<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;
function login(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("login");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['mobile_no']) && isset($request_params['password']))
  {
    $mobile_no=trim($request_params['mobile_no']);
    $password=trim($request_params['password']);
    $sql="select `uid`,`password` from `user` where `mobile_no`=:mobile_no";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":mobile_no",$mobile_no);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          $user_data=$p_stmt->fetch(PDO::FETCH_ASSOC);
          if(crypt($password,substr($user_data['password'], 0, 12)) == $user_data['password'])
          {
            $request=$request->withAttribute('uid',$user_data["uid"]);
            $response=$next($request, $response);
            return $response;
          }
          else {
            $result['msg']='Please enter correct mobile no. or password';
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
    $result['msg']='Mobile_no or Password field should not be empty';
    return $response->withStatus(302)->write(json_encode($result));
  }
}
function check_verify_status(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("check_verify_status");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['mobile_no']))
  {
    $mobile_no=trim($request_params['mobile_no']);
  // }
  // else if(!is_null($request->getAttribute('mobile_no')))
  // {
  //   $mobile_no=intval($request->getAttribute('mobile_no'));
  // }
    $sql="select `verified_status` from `user` where `mobile_no`=:mobile_no";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":mobile_no",$mobile_no);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          $verified_status=$p_stmt->fetch(PDO::FETCH_ASSOC);
          if(intval($verified_status['verified_status'])==1)
          {
            $response=$next($request, $response);
            return $response;
          }
          else {
            $result['msg']='This mobile no is not verified! please reset your account!';
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
    $result['msg']='Please enter mobile no. ';
    return $response->withStatus(302)->write(json_encode($result));
  }
}
function fetch_user_data(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("fetching user data");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $uid=$request->getAttribute('uid');
  if(isset($uid))
  {
    $user_data=array();
    $uid=intval(trim($uid));
    $user_data_sql="select `uid`,`name`,`mobile_no`,`address`,`city`,`pincode` from `user` where `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($user_data_sql);
        $p_stmt->bindParam(":uid",$uid);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            $user_data=$p_stmt->fetch(PDO::FETCH_ASSOC);
            $result = array("status"=>1,"msg"=>"user logged in!" ,"user_data"=>$user_data);
            return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result['msg']='This mobile no. is not registered!';
          return $response->withStatus(404)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='sorry, an error ocurred while logging in..';
    return $response->withStatus(500)->write(json_encode($result));
  }
}

function fetch_uid_from_mobile(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("fetching uid  from mobile");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  // check if uid is set. If not then fetch from mobile no provided
  if(!isset($request_params['uid']) && isset($request_params['mobile_no']))
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
          $row=$p_stmt->fetch(PDO::FETCH_ASSOC);
          $uid=(int)$row['uid'];
          $request=$request->withAttribute('uid',$uid); // send uid to next route
          $response=$next($request, $response);
          return $response;
        }
        else {
          $result['msg']='Sorry! This User is not registered with us !';
          return $response->withStatus(302)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(403)->write(json_encode($result));
    }
  }
  else {
    if(isset($request_params['uid']))
    {
      $response=$next($request, $response);
      return $response;
    }
    else
    {
      $result['msg']='Please provide uid or mobile no..';
      return $response->withStatus(302)->write(json_encode($result));
    }
  }
}
?>
