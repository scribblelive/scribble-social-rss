<html>
<head>
<title>ScribbleLive PHP</title>
<link rel="stylesheet" href="https://app.divshot.com/css/bootstrap.css">
    <link rel="stylesheet" href="https://app.divshot.com/css/bootstrap-responsive.css">
    <script src="https://app.divshot.com/js/jquery.min.js"></script>
    <script src="https://app.divshot.com/js/bootstrap.min.js"></script>
    <script src="http://platform.twitter.com/widgets.js"></script>
    

</head>
<body>
<?php
require 'vendor/autoload.php';
use Guzzle\Http\Client;
    
$client = new Client('http://apiv1.scribblelive.com', 
    array
    (
        'request.options' => array(
            'query' => array("Token" => "H5lrJBkO", "format" => "json"),
    )
));

$request = $client->get('/event/39048/page/last?PageSize=25');

$json = $request->send()->json();

$Title = $json["Title"];
?>
<div class="container">
      <h1 class="page-header"><?php echo $Title; ?></h1>

<?php
foreach( $json["Posts"] as $Post )
{
?>
    <div class="row" style="margin-bottom: 10px;" id="Post<?php echo $Post["Id"] ?>">
        <div class="span2">
          <h4 style="text-align: right; margin-top: 0"><?php echo $Post["Creator"]["Name"]; ?></h4>
        </div>
        <div class="span8">
            <?php if ( $Post["Type"] == "IMAGE" ) {  ?>
                <img src="<?php echo $Post["Media"][0]["Url"]; ?>" class="pull-left img-rounded" style="max-height: 300px; margin-right: 15px">
            <?php } ?>
          
          <p><?php echo $Post["Content"]; ?></p>
          
          <?php
          if( $Post["Source"] && preg_match( "/twitter\.com/", $Post["Source"] ) ) 
          {
              preg_match( "/[0-9]+/", $Post["Source"], $TweetIds );
          }
          else
          {
              $TweetIds = null;
          }
          
          if( $TweetIds && count( $TweetIds ) )
          {
          ?>
            <script>
            twttr.ready( function(twttr) {
                var TweetPost = $("#Post<?php echo $Post["Id"]; ?>");
                TweetPost.find("div").empty();
                twttr.widgets.createTweet('<?php echo $TweetIds[0]; ?>', TweetPost.find(".span8").get(0) );
            })
            </script>
          <?php } ?>
        </div>
      </div>
      <hr />

<?php
}
?>

</div>
</body>
</html>