<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;

function banner_feed(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("fetching banner data");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $banner_feed=array();
  $sql="select *  from `banner` where `banner_status`=1";
  try{
      // Get DB Object
      $db = $container['db'];
      $p_stmt= $db->prepare($sql);
      $p_stmt->execute();
      if($p_stmt->rowCount()>0)
      {
          while($banner_row=$p_stmt->fetch(PDO::FETCH_ASSOC))
          {
            $banner_feed[]=$banner_row;
          }
            // $result = array("status"=>1,"msg"=>"banner data feched!","banner_data"=>$banner_feed);
            // return $response->withStatus(200)->write(json_encode($result));
            // return $response;
            $request=$request->withAttribute('banner_feed',$banner_feed);
            $response=$next($request, $response);
            return $response;
      }
      else {
        $result['msg']='No banner data present';
        return $response->withStatus(302)->write(json_encode($result));
      }
  }catch(PDOException $e){
    $result['msg']=$e->getMessage();
    return $response->withStatus(403)->write(json_encode($result));
  }
}
?>
