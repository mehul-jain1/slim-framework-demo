<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;
function register(Request $request, Response $response)
{
  global $container;
  $container['logger']->addInfo("register started");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['mobile_no']) && isset($request_params['password']))
  {
    $mobile_no=trim($request_params['mobile_no']);
    $pswd=crypt(trim($_REQUEST['password']));
    $name=((isset($_REQUEST['name']))?trim($_REQUEST['name']):'');
    $city=((isset($_REQUEST['city']))?trim($_REQUEST['city']):'');
    $address=((isset($_REQUEST['address']))?trim($_REQUEST['address']):'');
    $pincode=((isset($_REQUEST['pincode']))?trim($_REQUEST['pincode']):'');
    try{
      $db = $container['db'];
      $ins_q=$db->prepare("INSERT INTO `user`(`name`,`mobile_no`,`password`,`address`,`city`,`pincode`,`create_date`) VALUES(:name,:mobile_no,
      :password,:address,:city,:pincode,:create_date)");
      $user_value=array(':name'=>$name,
                       ':mobile_no'=>$mobile_no,
                       ':password'=>$pswd,
                       ':address'=>$address,
                       ':city'=>$city,
                       ':pincode'=>$pincode,
                       ':create_date'=>time()
                      );
      $ins_q->execute($user_value);
      if($ins_q->rowCount()>0)
      {
        $uid=$db->lastInsertID();
        $result = array("status"=>1,"msg"=>"user registered succesfully!" ,"uid"=>$uid);
        return $response->withStatus(200)->write(json_encode($result));
      }
      else {
          return $response->withStatus(403)->write(json_encode($result));
      }
    }
    catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(403)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='provide a  mobile no  and password';
    return $response->withStatus(403)->write(json_encode($result));
  }
  //$data = $request->getParsedBody();
  //var_dump($data);
}

function verify_mobile_unique(Request $request, Response $response, $next)
{
    global $container;
    $container['logger']->addInfo("verify_mobile_unique");
    $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
    $request_params = $request->getParsedBody();
    $route = $request->getAttribute('route');
    $routeName = $route->getName();
    // check for route name and set variable accordingly
    if($routeName=='reset_mobile')
    {
      if(isset($request_params['new_mobile_no']) && $request_params['new_mobile_no'] !='')
      $mobile_no=$request_params['new_mobile_no'];
    }
    else {
      if(isset($request_params['mobile_no']) && $request_params['mobile_no'] !='')
      $mobile_no=$request_params['mobile_no'];
    }

    if(isset($mobile_no))
    {
      $mobile_no=trim($mobile_no);
      if(preg_match('/^\d{10}$/',$mobile_no))
      {

          $sql="select `mobile_no` from `user` where `mobile_no`=:mobile_no";
          try{
              // Get DB Object
              $db = $container['db'];
              $p_stmt= $db->prepare($sql);
              $p_stmt->bindParam(":mobile_no",$mobile_no);
              $p_stmt->execute();
              if($p_stmt->rowCount()>0)
              {
                $result['msg']='This mobile no. is alerady registered with us';
                return $response->withStatus(403)->write(json_encode($result));
              }
              else {
                $response=$next($request, $response);
                return $response;
              }
          }catch(PDOException $e){
            $result['msg']=$e->getMessage();
            return $response->withStatus(403)->write(json_encode($result));
          }
      }
      else {
        $result['msg']='provide a 10 digit valid mobile no ';
        return $response->withStatus(403)->write(json_encode($result));
      }
    }
    else {
      $result['msg']='mobile_no field should not be empty';
      return $response->withStatus(403)->write(json_encode($result));
    }

}
function verify_mobile_unique_1(Request $request, Response $response,$next)
{
    global $container;
    $container['logger']->addInfo("verify_mobile_unique");
    $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
    $request_params = $request->getParsedBody();
    if(isset($request_params['mobile_no']))
    {
      $mobile_no=trim($request_params['mobile_no']);
      if(preg_match('/^\d{10}$/',$mobile_no))
      {

          $sql="select `mobile_no` from `user` where `mobile_no`=:mobile_no";
          try{
              // Get DB Object
              $db = $container['db'];
              $p_stmt= $db->prepare($sql);
              $p_stmt->bindParam(":mobile_no",$mobile_no);
              $p_stmt->execute();
              if($p_stmt->rowCount()>0)
              {
                $result['msg']='This mobile no. is alerady registered with us';
                return $response->withStatus(403)->write(json_encode($result));
              }
              else {
                $result = array("status"=>1,"msg"=>"mobile no valid...");
                return $response->withStatus(200)->write(json_encode($result));
              }
          }catch(PDOException $e){
            $result['msg']=$e->getMessage();
            return $response->withStatus(403)->write(json_encode($result));
          }
      }
      else {
        $result['msg']='provide a 10 digit valid mobile no ';
        return $response->withStatus(403)->write(json_encode($result));
      }
    }
    else {
      $result['msg']='mobile_no field should not be empty';
      return $response->withStatus(403)->write(json_encode($result));
    }

}
function verify_user(Request $request, Response $response)
{
  global $container;
  $container['logger']->addInfo("verify_user");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['mobile_no']) && isset($request_params['verified_status']))
  {
    $mobile_no=trim($request_params['mobile_no']);
    $verified_status=intval(trim($request_params['verified_status']));
    $sql="update `user` set `verified_status`=:verified_status where `mobile_no`=:mobile_no";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":verified_status",$verified_status);
        $p_stmt->bindParam(":mobile_no",$mobile_no);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          $result = array("status"=>1,"msg"=>"mobile no. verified succesfully!");
          return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result['msg']='mobile no. alerady verified';
          return $response->withStatus(302)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='mobile no. not verified';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function check_mobile_exists(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("check_mobile_exists");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  $route = $request->getAttribute('route');
  $routeName = $route->getName();
  // check for route name and set variable accordingly
  if($routeName=='reset_mobile')
  {
    if(isset($request_params['old_mobile_no']) && $request_params['old_mobile_no'] !='')
    $mobile_no=$request_params['old_mobile_no'];
  }
  else {
    if(isset($request_params['mobile_no']) && $request_params['mobile_no'] !='')
    $mobile_no=$request_params['mobile_no'];
  }
  if(isset($mobile_no))
  {
        $mobile_no=trim($mobile_no);
        $sql="select `mobile_no` from `user` where `mobile_no`=:mobile_no";
        try{
            // Get DB Object
            $db = $container['db'];
            $p_stmt= $db->prepare($sql);
            $p_stmt->bindParam(":mobile_no",$mobile_no);
            $p_stmt->execute();
            if($p_stmt->rowCount()>0)
            {
              $response=$next($request, $response);
              return $response;
            }
            else {
              $result['msg']='Provided mobile no. not  exists in our records';
              return $response->withStatus(404)->write(json_encode($result));
            }
        }catch(PDOException $e){
          $result['msg']=$e->getMessage();
          return $response->withStatus(403)->write(json_encode($result));
        }
  }
  else {
    $result['msg']='mobile_no field should not be empty';
    return $response->withStatus(403)->write(json_encode($result));
  }
}
function sendotp(Request $request, Response $response)
{
  global $container;
  $container['logger']->addInfo("sending otp to user");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  $otp=mt_rand(1000,9999); // generate 4 digit otp number
  $otp_msg=$otp." is your BackToRoots OTP. Enter this number on the App and you're all set";
  $fields = array(
       'authkey' => "144259AjikbpnmV7MU58c02815",
       'mobile' => $request_params["mobile_no"],
       'message' => $otp_msg,
       'sender'  =>"BTROOTS",
       'otp' => $otp
  );
  $postvars = http_build_query($fields);
  $url="https://control.msg91.com/api/sendotp.php?".$postvars;
  // build the urlencoded data
  // open connection
  try
  {
    $ch = curl_init ($url);
    if (FALSE === $ch)
       throw new Exception('failed to initialize');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    $raw = curl_exec($ch);
    if (FALSE === $raw)
    throw new Exception(curl_error($ch), curl_errno($ch));
    curl_close($ch);
    $raw=(array)json_decode($raw);
    if($raw['type']=="success")
      $result = array("status"=>1,"otp"=>(string)$otp,"msg"=>"otp sent succesfully");
      else
      $result = array("status"=>1,"msg"=>"failed to send otp ");
    return $response->withStatus(200)->write(json_encode($result));
  }
  catch(Exception $e)
  {
    $result['msg']=$e->getMessage();
    return $response->withStatus(403)->write(json_encode($result));
  }
}
