<html>
<head>
<title>ScribbleLive PHP</title>
<link rel="stylesheet" href="https://app.divshot.com/css/bootstrap.css">
    <link rel="stylesheet" href="https://app.divshot.com/css/bootstrap-responsive.css">
    <script src="https://app.divshot.com/js/jquery.min.js"></script>
    <script src="https://app.divshot.com/js/bootstrap.min.js"></script>
</head>
<body>
<?php
require 'vendor/autoload.php';
use Guzzle\Http\Client;
    
$client = new Client('https://scribblelive.p.mashape.com/', 
    array
    (
        'request.options' => array(
            'headers' => array("X-Mashape-Authorization" => "8HZNmWVe6XXI0Abu50NgzZoQ6V5Lfh1l"),
            'query' => array("Token" => "OBtoB7Tk"),
    )
));

$request = $client->get('/event/39048/page/last?PageSize=10');

$json = $request->send()->json();

$Title = $json["Title"];
?>
<div class="container">
      <h1 class="page-header"><?php echo $Title; ?></h1>

<?php
foreach( $json["Posts"] as $Post )
{
?>
    <div class="row" style="margin-bottom: 10px;">
        <div class="span2">
          <h4 style="text-align: right; margin-top: 0"><?php echo $Post["Creator"]["Name"]; ?></h4>
        </div>
        <div class="span8">
            <?php if ( $Post["Type"] == "IMAGE" ) {  ?>
                <img src="<?php echo $Post["Media"][0]["Url"]; ?>" class="pull-left img-rounded" style="max-height: 300px; margin-right: 15px">
            <?php } ?>
          
          <p><?php echo $Post["Content"]; ?></p>
          
        </div>
      </div>
      <hr />

<?php
}
?>

</div>
</body>
</html>