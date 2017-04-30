<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
function check_product_exist(Request $request, Response $response , $next)
{
  global $container;
  $container['logger']->addInfo("check_product_exist");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();

  if(isset($request_params['product_id']))
  {
        $product_id=intval(trim($request_params['product_id']));
        $product_status=1; // fetch only live products available
        $sql="select `product_id` from `products` where `product_id`=:product_id and `product_status`=:product_status";
        try{
            // Get DB Object
            $db = $container['db'];
            $p_stmt= $db->prepare($sql);
            $p_stmt->bindParam(":product_id",$product_id);
            $p_stmt->bindParam(":product_status",$product_status);
            $p_stmt->execute();
            if($p_stmt->rowCount()>0)
            {
              $response=$next($request, $response);
              return $response;
            }
            else {
              $result['msg']='Sorry! Provided product is not avaiable..';
              return $response->withStatus(404)->write(json_encode($result));
            }
        }catch(PDOException $e){
          $result['msg']=$e->getMessage();
          return $response->withStatus(403)->write(json_encode($result));
        }
  }
  else {
    $result['msg']='Please specify product ID..';
    return $response->withStatus(403)->write(json_encode($result));
  }
}
function check_product_subscribable(Request $request, Response $response , $next)
{
  global $container;
  $container['logger']->addInfo("check_product_subscribable");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['product_id']))
  {
        $product_id=intval(trim($request_params['product_id']));
        $product_status=1; // fetch only live products available
        $sql="select `product_subscribable` from `products` where `product_id`=:product_id and `product_status`=:product_status";
        try{
            // Get DB Object
            $db = $container['db'];
            $p_stmt= $db->prepare($sql);
            $p_stmt->bindParam(":product_id",$product_id);
            $p_stmt->bindParam(":product_status",$product_status);
            $p_stmt->execute();
            if($p_stmt->rowCount()>0)
            {
              $pdt_row=$p_stmt->fetch(PDO::FETCH_ASSOC);
              $product_subscribable=intval($pdt_row["product_subscribable"]);
              if($product_subscribable)
              {
                $response=$next($request, $response);
                return $response;
              }
              else {
                $result['msg']='Sorry! Provided product is not Subscribable ';
                return $response->withStatus(404)->write(json_encode($result));
              }

            }
            else {
              $result['msg']='Sorry! Provided product is not avaiable..';
              return $response->withStatus(404)->write(json_encode($result));
            }
        }catch(PDOException $e){
          $result['msg']=$e->getMessage();
          return $response->withStatus(403)->write(json_encode($result));
        }
  }
  else {
    $result['msg']='Please specify product ID..';
    return $response->withStatus(403)->write(json_encode($result));
  }
}
?>
