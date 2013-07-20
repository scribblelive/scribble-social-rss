<?php
require 'vendor/autoload.php';
require 'lib/html2text.php';
use Guzzle\Http\Client;

date_default_timezone_set("UTC"); 
header( "Content-type: application/xml");
    
$client = new Client('http://apiv1.scribblelive.com', 
    array
    (
        'request.options' => array(
            'query' => array("Token" => "H5lrJBkO", "format" => "json"),
    )
));

$request = $client->get('/event/39048/page/last?PageSize=20');

$json = $request->send()->json();

$Title = $json["Title"];

$Url = $json["Websites"][0]["Url"];

preg_match( "/([0-9]+)/", $json["LastModified"], $LastModified );
$LastModified = $LastModified[0] / 1000;


?><?xml version="1.0"?>
<rss version="2.0">
   <channel>
      <title><?php echo $Title; ?></title>
      <link><?php echo $Url; ?></link>
      <language>en-us</language>
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
        $Content = $Post["Creator"]["Name"] . ": ";
        
        $PostUrl = $Url . "/" . $Post["Id"];
        
        if( !empty( $Post["Content"] ) ) 
        {
            $TextContent = convert_html_to_text( trim( $Post["Content"] ) );
        }
        
        if( preg_match( "/twitter\.com/", $Post["Source"] ) )
        {
            continue;
        }
        else if( $Post["Type"] == "TEXT" && ! empty( $TextContent ) )
        {
            if( strlen( $Content ) + strlen( $TextContent ) > 140 )
            {
                $Content = $Content . ShortenText( $TextContent, 140 - strlen( $Content ) - 3 ) . "...";
            }
            else
            {
                $Content = $Content . $TextContent;
            }
        }
        else if( $Post["Type"] == "IMAGE" )
        {
            if( isset( $_GET["imgtags"] ) )
            {
                // Image tags version
                
                if( ! empty( $TextContent ) )
                {
                    if( strlen( $Content ) + strlen( $TextContent ) > 140 - 26 )
                    {
                        $Content = $Content . ShortenText( $TextContent, 140 - 26 - strlen( $Content ) - 3 ) . "... " 
                            . "<a href='" . $PostUrl . "'><img src='" . $Post["Media"][0]["Url"] . "' /></a>" ;
                    }
                    else
                    {
                        $Content = $Content . $TextContent . " "
                            . "<a href='" . $PostUrl . "'><img src='" . $Post["Media"][0]["Url"] . "' /></a>";
                    }
                }
                else
                {
                    $Content = $Content . "(Image) " . "<a href='" . $PostUrl . "'><img src='" . $Post["Media"][0]["Url"] . "' /></a>";
                }
            
            }
            else
            {
                // Twitter Cards version
                
                if( ! empty( $TextContent ) )
                {
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
    
?>
    <item>
        <title><?php echo htmlentities( $Content ); ?></title>
        <description><?php echo htmlentities( $Content ); ?></description>
        <pubDate><?php echo date(DATE_RSS, $Created ); ?></pubDate>
        <link><?php echo $PostUrl; ?></link>
        <guid><?php echo $PostUrl; ?></guid>
    </item>

<?php
}
?>
 </channel>
</rss>