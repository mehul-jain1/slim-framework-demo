<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;

function home_feed(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("fetching home feed");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  // get all feeds data from routes
  $product_feed=$request->getAttribute('product_category_feed');
  $banner_feed=$request->getAttribute('banner_feed');

  $result = array("status"=>1,"msg"=>"home feed generated!","banner_data"=>$banner_feed,"product_category_data"=>$product_feed);
  return $response->withStatus(200)->write(json_encode($result));
  return $response;
}
function product_category_feed(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("fetching product category feed");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $product_category_feed=array();
  $status=1; // live categories
  $sql="select `category_id`,`category_type`, `category_img` from `product_categories` where `category_status`=:category_status";
  try{
      // Get DB Object
      $db = $container['db'];
      $p_stmt= $db->prepare($sql);
      $p_stmt->bindParam(":category_status",$status);
      $p_stmt->execute();
      if($p_stmt->rowCount()>0)
      {
          while($product_row=$p_stmt->fetch(PDO::FETCH_ASSOC))
          {
            $product_category_feed[]=$product_row;
          }
            // $result = array("status"=>1,"msg"=>"product categories feched!","product_category_data"=>$product_category_feed);
            // return $response->withStatus(200)->write(json_encode($result));
            // return $response;
            $request=$request->withAttribute('product_category_feed',$product_category_feed);
            $response=$next($request, $response);
            return $response;
      }
      else {
        $result['msg']='No product categories present';
        return $response->withStatus(302)->write(json_encode($result));
      }
  }catch(PDOException $e){
    $result['msg']=$e->getMessage();
    return $response->withStatus(403)->write(json_encode($result));
  }
}
?>
