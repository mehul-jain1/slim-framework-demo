<?php
$g_url="";
$g_url="http://localhost:8888/";
?>
<html>

<head>
	<!-- mobile meta -->
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
	<!--title/meta_desc-->
	<title>
    CMS BackTORoots
	</title>
    <meta name="description" content="">
    <!--css-->
    <link href='<?php echo $g_url;?>public/cms/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
    <link href='<?php echo $g_url;?>public/cms/css/jquery.datetimepicker.css' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<style>
.order_tab
{
  margin-left: 10%;
  margin-top:5%
}
.add_date
{
  margin-top: 5%;
}
.btn{
  margin-top:5px;
  margin-left:5px;
  margin-bottom:5px;
}
.status{
  padding: 5px;
  text-align: center;
  background-color: #9d9896;
  border-radius: 5px;
  overflow-x: auto;
  overflow-y: auto;
  bottom: 0;
  position: fixed;
  font-size: 20px;
}
.submit_btn{
  margin-left: 5%;
}
@media(max-width:768px){
  .order_tab{
    margin-right:5%;
  }
}
</style>
</head>
<body >
<h1 style="text-align:center">Back To Roots Order Update  </h1>
<div class="col-xs-12 col-sm-4 order_tab" style="background-color: #f7f7f9;">
<div class="row ">
  <label>Order Update</label>
  <input type="text" class="form-control col-sm-8" id="order_id" name="order_id" placeholder="Enter Order id">
</div>
<div class="row submit_btn">
  <button type="button" class="btn btn-success col-sm-3" name="order_update"  value="2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Delivered</button>
  <button type="button" class="btn btn-warning col-sm-3" name="order_update"  value="1" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Place</button>
  <button type="button" class="btn btn-danger col-sm-3"  name="order_update"  value="0" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Cancel</button>
</div>
<!-- <div class="status hidden"></div> -->
</div>
<div class="col-xs-12  col-sm-4 order_tab" style="background-color: #f7f7f9;">
<div class="row">
  <label>Subscription Update (Select time as 9 pm )</label>
  <input type="text" class="form-control" id="subscription_id" name="subscription_id" placeholder="Enter subscription id">
</div>
<div class="row col-xs-12 col-sm-6">
  <input type="text" class="form-control add_date"  id="start_date" name="start_date" placeholder="click to add start date">
</div>
<div class="row col-xs-12 col-sm-offset-1 col-sm-6">
  <input type="text" class="form-control add_date"  id="end_date" name="end_date" placeholder="click to add end date">
</div>
<div class="row submit_btn">
  <button type="button" class="btn btn-success col-sm-3" name="subscription_update"  value="2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Paid</button>
  <button type="button" class="btn btn-warning col-sm-3" name="subscription_update"  value="1" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Pending</button>
  <button type="button" class="btn btn-danger col-sm-3" name="subscription_update"  value="0" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Cancel</button>
</div>
<!-- <div class="status hidden"></div> -->
</div>

<div class="col-xs-12  col-sm-4 order_tab " style="background-color: #f7f7f9;">

<div class="row">
  <label>Get Subscribed Order for Tommorrow<br/>(Run b/w 9-12 pm everyday if mail not received after 9 pm)</label>
</div>

<div class="row submit_btn">
<button type="button" class="btn btn-danger col-sm-3" name='get_orders' data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Run</button>
</div>

</div>

<div class="col-xs-12  col-sm-4 order_tab" style="background-color: #f7f7f9">
<div class="row ">
  <label>User Subscription status </label>
  <input type="text" class="form-control col-sm-8" id="subscription_status_id" name="subscription_id" placeholder="Enter Subscription id">
</div>
<div class="row submit_btn">
  <button type="button" class="btn btn-success col-sm-3" name="subscription_status"  value="1" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Start</button>
  <button type="button" class="btn btn-warning col-sm-3" name="subscription_status"  value="2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Pause</button>
  <button type="button" class="btn btn-danger col-sm-3"  name="subscription_status"  value="0" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Cancel</button>
</div>
<!-- <div class="status hidden"></div> -->
</div>

<div class="col-xs-12  col-sm-4 order_tab" style="background-color: #f7f7f9">
<div class="row ">
  <label>User Verfied status </label>
  <input type="text" class="form-control col-sm-8" id="verified_status" name="uid" placeholder="Enter uid">
</div>
<div class="row submit_btn">
  <button type="button" class="btn btn-warning col-sm-3" name="verified"  value="1" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Verfied</button>
  <button type="button" class="btn btn-danger col-sm-3" name="verified"  value="0" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing ">Unverified</button>
</div>
</div>
<div class="col-sm-12 status hidden"></div>

<script src="<?php echo $g_url;?>public/cms/js/jquery-2.0.2.min.js"></script>
<script src="<?php echo $g_url;?>public/cms/js/bootstrap.min.js"></script>
<script src="<?php echo $g_url;?>public/cms/js/jquery.datetimepicker.full.min.js"></script>

<script>
var g_url="";
var g_api__url="";
var g_api_url="";
$(document).ready(function(){
$(document).on("click",".add_date",function(){
    event.preventDefault();
    $('.add_date').datetimepicker({
      datepicker:true,
      allowTimes:[]
    });
});
$('.btn[name="subscription_status"').click(function(){
  var $this = $(this);
  $(".status").addClass("hidden");
  $this.button('loading');
  var subscription_id=$("#subscription_status_id").val();
  var subscription_status=$this.val();
  console.log(subscription_id);
  console.log(subscription_status);
  if(subscription_id!=undefined && subscription_status!==undefined && subscription_id!='' && subscription_status!='')
  {
      $.ajax({
          type:"POST",
          dataType:"json",
          url:g_api_url+"set_subscription/",
          data:
          {
            subscription_id:subscription_id,
            subscription_status:subscription_status
          },
          success:function(json)
          {
              $this.button('reset');
              $(".status").removeClass("hidden").html(json.msg);
          },
          error:function()
          {
            $this.button('reset');
            $(".status").removeClass("hidden").html("Error Ocurred!! Please Try again");
          }
      });
  }
  else {
    $this.button('reset');
    $(".status").removeClass("hidden").html("Please Fill all the details...");
  }
});

$('.btn[name="order_update"').click(function(){
  var $this = $(this);
  $(".status").addClass("hidden");
  $this.button('loading');
  var order_id=$("#order_id").val();
  var order_status=$this.val();
  console.log(order_id);
  console.log(order_status);
  if(order_id!=undefined && order_status!==undefined && order_id!='' && order_status!='')
  {
      $.ajax({
          type:"POST",
          dataType:"json",
          url:g_api_url+"set_order/",
          data:
          {
            order_id:order_id,
            order_status:order_status
          },
          success:function(json)
          {
              $this.button('reset');
              $(".status").removeClass("hidden").html(json.msg);
          },
          error:function()
          {
            $this.button('reset');
            $(".status").removeClass("hidden").html("Error Ocurred!! Please Try again");
          }
      });
  }
  else {
    $this.button('reset');
    $(".status").removeClass("hidden").html("Please Fill all the details...");
  }
});
$('.btn[name="get_orders"').click(function(){
  $(".status").addClass("hidden");
  var $this = $(this);
  if(confirm('sure you want to run (before running make sure you have checked  the mail twice)?'))
  {
    $this.button('loading');
    $.ajax({
        type:"POST",
        dataType:"json",
        url:"http://localhost:8888/cron/update_subscriptions.php",
        success:function(json)
        {
            $this.button('reset');
            $(".status").removeClass("hidden").html(json.msg);
        },
        error:function()
        {
          $this.button('reset');
          $(".status").removeClass("hidden").html("Error Ocurred!! Please Try again");
        }
    });
  }
  else
  {

  }
});

$('.btn[name="subscription_update"').click(function(){
  var $this = $(this);
  $(".status").addClass("hidden");
  $this.button('loading');
  var subscription_id=$("#subscription_id").val();
  var subscription_status=$this.val();
  var start_time=$("#start_date").val();
  var end_time=$("#end_date").val();
  console.log(start_time);
  console.log(end_time);
  if(subscription_id!=undefined && subscription_status!==undefined && start_time!==undefined && end_time!==undefined && subscription_id!='' && subscription_status!='' && start_time!='' && end_time!='')
  {
      $.ajax({
          type:"POST",
          dataType:"json",
          url:g_api_url+"set_order_subscription/",
          data:
          {
            subscription_id:subscription_id,
            subscription_status:subscription_status,
            start_time:start_time,
            end_time:end_time
          },
          success:function(json)
          {
              $this.button('reset');
              $(".status").removeClass("hidden").html(json.msg);
          },
          error:function()
          {
            $this.button('reset');
            $(".status").removeClass("hidden").html("Error Ocurred!! Please Try again");
          }
      });
  }
  else {
    $this.button('reset');
    $(".status").removeClass("hidden").html("Please Fill all the details...");
  }
});
$('.btn[name="verified"').click(function(){

  var $this = $(this);
  $(".status").addClass("hidden");
  $this.button('loading');
  var uid=$("#verified_status").val();
  var verified=$this.val();
  console.log(uid);
  console.log(verified);
  if(uid!=undefined && verified!==undefined && uid!='' && verified!='')
  {
      $.ajax({
          type:"POST",
          dataType:"json",
          url:g_api_url+"set_user/",
          data:
          {
            uid:uid,
            verified_status:verified
          },
          success:function(json)
          {
              $this.button('reset');
              $(".status").removeClass("hidden").html(json.msg);
          },
          error:function()
          {
            $this.button('reset');
            $(".status").removeClass("hidden").html("Error Ocurred!! Please Try again");
          }
      });
  }
  else {
    $this.button('reset');
    $(".status").removeClass("hidden").html("Please Fill all the details...");
  }
});

});
</script>
</body>
</html>
