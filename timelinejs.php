<?php
error_reporting(0);
require 'vendor/autoload.php';
require 'lib/html2text.php';
use Guzzle\Http\Client;

date_default_timezone_set("America/Toronto"); 

$EventId = ( isset($_GET["eventid"]) ? $_GET["eventid"] : 0 );
$Token = ( isset( $_GET["token"] ) ? $_GET["token"] : 0 );

if( !$Token || !$EventId )
{
    echo "Invalid arguments";
    header("HTTP/1.0 404 Not Found");
    return;
}
    
$client = new Client('http://apiv1.scribblelive.com', 
    array
    (
        'request.options' => array(
            'query' => array("Token" => $Token, "format" => "json"),
    )
));

$request = $client->get('/event/' . $EventId . '/all?Max=100');

try
{
    $json = $request->send()->json();
}
catch( Exception $e )
{
    echo "Error hitting API. Are you sure your token and event are correct?";
    header("HTTP/1.0 505 Error hitting API");
    return;
}

$Title = $json["Title"];

$Url = $json["Websites"][0]["Url"];

preg_match( "/([0-9]+)/", $json["LastModified"], $LastModified );
$LastModified = $LastModified[0] / 1000;

header( "Content-type: application/json");
?>
{
    "timeline":
    {
     "headline":"<?php echo $Title; ?>",
        "type":"default",
        "text":"<p><?php echo $Title; ?></p>",
        "asset": {
            "media":"https://d2hlwa4qdwmewe.cloudfront.net/Style/Images/Admin/ScribbleLiveBrand.png",
            "caption":"ScribbleLive"
        },
        "date": [
            

<?php
function ShortenText( $string, $width )
{
    return substr($string, 0, strpos(wordwrap($string, $width), "\n"));
}

$IsFirst = true;
foreach( $json["Posts"] as $Post )
{
    
    
    try
    {
        if( $Post["IsDeleted"] == "1" )
        {
            continue;
        }
        
        $Content = "";
        $IsShortened = false;
        $HtmlEncoded = "";
        $TextContent = "";
        
        $PostUrl = $Url . "/" . $Post["Id"];
        
        if( !empty( $Post["Content"] ) ) 
        {
            $TextContent = convert_html_to_text( trim( $Post["Content"] ) );
        }
        
        $Media = $Post["Creator"]["Avatar"];
        
        if( ( $Post["Type"] != "IMAGE" || ( isset( $_GET["notweets"] ) && $_GET["notweets"] == "1" ) ) && preg_match( "/twitter\.com/", $Post["Source"] ) )
        {
            continue;
        }
        else if( $Post["Type"] == "TEXT" && ! empty( $TextContent )  )
        {
            $TextContent = preg_replace( "/\[http:\/\/.*?\]\((https?:\/\/.*?)\)/", "", $TextContent );
            $TextContent = preg_replace( "/\[(.*?)\]\((https?:\/\/.*?)\)/", "$1", $TextContent );
            
            if( strlen( $Content ) + strlen( $TextContent ) > 140 )
            {
                $Content = $Content . ShortenText( $TextContent, 140 - 26 - strlen( $Content ) - 3 ) . "... ";// . $PostUrl;
                $IsShortened = true;
                
                $Media = "<blockquote>" . preg_replace( "/\"/", "\\\"", $Post["Content"] ) . "</blockquote>";
                $Content = preg_replace( "/\"/", "\\\"", html_entity_decode( $Content, 0, 'UTF-8' ) );
            }
            else
            {
                $Content = preg_replace( "/\"/", "\\\"", html_entity_decode( $Post["Content"], 0, 'UTF-8' ) );
                $Media = "";
            }
            
        }
        else if( $Post["Type"] == "IMAGE" )
        {
            $Media = $Post["Media"][0]["Url"];
            if( isset( $TextContent ) )
            {
                // Strip out links with descriptions that were just a domain
                $TextContent = preg_replace( "/\[(.*?\.([a-z]{2,4}))\]\(https?:\/\/.*?\)/", "", $TextContent );
                $TextContent = preg_replace( "/\[(https?:\/\/.*?)\]\(https?:\/\/.*?\)/", "", $TextContent );
                
                // For any remaining links, just display the descriptive text
                $TextContent = preg_replace( "/\[(.*?)\]\(https?:\/\/.*?\)/", "$1", $TextContent );
                
                if( strlen( $Content ) + strlen( $TextContent ) > 140 - 26 )
                {
                    $Content = $Content . ShortenText( $TextContent, 140 - 26 - strlen( $Content ) - 3 ) . "... " ;//. $PostUrl;
                    $IsShortened = true;
                    $Content = preg_replace( "/\"/", "\\\"", html_entity_decode( $Content, 0, 'UTF-8' ) );
                }
                else
                {
                    $Content = $Content . $TextContent . " " ;//. $PostUrl;
                    $Content = preg_replace( "/\"/", "\\\"", html_entity_decode( $Post["Content"], 0, 'UTF-8' ) );
                }
                
            }
            else
            {
                //$Content = $Content . "(Image) ";// . $PostUrl;
            }
        }
        else
        {
            continue;
        }
        
        
        
        
        preg_match( "/([0-9]+)/", $Post["Created"], $Created );
        $Created = $Created[0] / 1000;
    }
    catch( Exception $ex )
    {
        continue;
    }
    
    try
    {
        $HtmlEncoded = $Content; //preg_replace( "/([^\])\"/", "$1\\\"", $Content ); //htmlentities( $Content, 0, 'UTF-8' ); 
        
        if( preg_match( "/&acirc;/", $HtmlEncoded) ) 
        {
            continue;
        }
        
        $Media = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $Media);
    }
    catch( Exception $ex )
    {
        continue;
    }
    
    if( !$IsFirst )
    {
        echo ",";
        
    }
    $IsFirst = false;
    
?>
{
        "startDate":"<?php echo date("Y,n,d,G,i", $Created ); ?>",
        "endDate":"<?php echo date("Y,n,d,G,i", $Created ); ?>",
        "headline":"<?php echo $HtmlEncoded; ?>",
        "text":"<?php echo ( $IsShortened && false ? $HtmlEncoded : "<img style='float: left; border: 0; margin-right: 5px;' src='" . $Post["Creator"]["Avatar"] . "' /><span style='font-size: 18px;'>" . $Post["Creator"]["Name"] . "</span>"  ); ?>",
        "asset": {
                    "media":"<?php echo $Media; ?>",
                    "thumbnail":"<?php echo ( strpos( $Media, "http" ) === 0 ? $Media : "" ); ?>"
                }
    }

<?php
}
?>
]
}
}