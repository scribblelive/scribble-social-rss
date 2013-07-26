<?php
require 'vendor/autoload.php';
require 'lib/html2text.php';
use Guzzle\Http\Client;

date_default_timezone_set("UTC"); 

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

$request = $client->get('/event/' . $EventId . '/page/last?PageSize=20');

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

header( "Content-type: application/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
   <channel>
      <title><?php echo $Title; ?></title>
      <link><?php echo $Url; ?></link>
      <pubDate><?php echo date(DATE_RSS, $LastModified ); ?></pubDate>
      <lastBuildDate><?php echo date(DATE_RSS, $LastModified ); ?></lastBuildDate>
      <docs>http://blogs.law.harvard.edu/tech/rss</docs>
      <generator>ScribbleLive</generator>

<?php
function ShortenText( $string, $width )
{
    return substr($string, 0, strpos(wordwrap($string, $width), "\n"));
}

foreach( $json["Posts"] as $Post )
{
    try
    {
        if( !isset( $_GET["noname"] ) || $_GET["noname"] != "1" ) 
        {
            $Content = $Post["Creator"]["Name"] . ": ";
        }
        else
        {
            $Content = "";
        }
        
        $PostUrl = $Url . "/" . $Post["Id"];
        
        if( !empty( $Post["Content"] ) ) 
        {
            $TextContent = convert_html_to_text( trim( $Post["Content"] ) );
        }
        
        if( ( $Post["Type"] != "IMAGE" || ( isset( $_GET["notweets"] ) && $_GET["notweets"] == "1" ) ) && preg_match( "/twitter\.com/", $Post["Source"] ) )
        {
            continue;
        }
        else if( $Post["Type"] == "TEXT" && ! empty( $TextContent )  )
        {
            $TextContent = preg_replace( "/\[http:\/\/.*?\]\((https?:\/\/.*?)\)/", "$1", $TextContent );
            $TextContent = preg_replace( "/\[(.*?)\]\((https?:\/\/.*?)\)/", "$1 $2", $TextContent );
            
            if( strlen( $Content ) + strlen( $TextContent ) > 140 )
            {
                $Content = $Content . ShortenText( $TextContent, 140 - 26 - strlen( $Content ) - 3 ) . "... " . $PostUrl;
            }
            else
            {
                $Content = $Content . $TextContent;
            }
        }
        else if( $Post["Type"] == "IMAGE" )
        {
            if( isset( $TextContent ) )
            {
                // Strip out links with descriptions that were just a domain
                $TextContent = preg_replace( "/\[(.*?\.([a-z]{2,4}))\]\(https?:\/\/.*?\)/", "", $TextContent );
                $TextContent = preg_replace( "/\[(https?:\/\/.*?)\]\(https?:\/\/.*?\)/", "", $TextContent );
                
                // For any remaining links, just display the descriptive text
                $TextContent = preg_replace( "/\[(.*?)\]\(https?:\/\/.*?\)/", "$1", $TextContent );
                
                if( strlen( $Content ) + strlen( $TextContent ) > 140 - 26 )
                {
                    $Content = $Content . ShortenText( $TextContent, 140 - 26 - strlen( $Content ) - 3 ) . "... " . $PostUrl;
                }
                else
                {
                    $Content = $Content . $TextContent . " " . $PostUrl;
                }
            }
            else
            {
                $Content = $Content . "(Image) " . $PostUrl;
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
        $HtmlEncoded = htmlentities( $Content, 0, 'UTF-8' ); 
        
        if( preg_match( "/&acirc;/", $HtmlEncoded) ) 
        {
            continue;
        }
    }
    catch( Exception $ex )
    {
        continue;
    }
    
?>
    <item>
        <title><?php echo $HtmlEncoded; ?></title>
        <description><?php echo $HtmlEncoded; ?></description>
        <pubDate><?php echo date(DATE_RSS, $Created ); ?></pubDate>
        <author><?php echo $Post["Creator"]["Name"]; ?></author>
        <link><?php echo $PostUrl; ?></link>
        <guid><?php echo $PostUrl; ?></guid>
    </item>

<?php
}
?>
 </channel>
</rss>