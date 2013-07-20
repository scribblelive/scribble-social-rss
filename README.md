Scribble Social RSS
========================

An RSS feed from ScribbleLive, formatted for social networks.

##Introduction
This RSS includes only post types that would work on Twitter (i.e. text, images) but doesn't include Flash embeds, etc. All posts will be shortened to 140 characters once URLs inside them have been shortened.

##How to Use
To send the entries in this RSS to Twitter, you can use a third-party service like [IFTTT](http://ifttt.com) or [Zapier](http://zapier.com). Both services have the ability to periodically pull an RSS feed and send the results to Twitter.

##Parameters
###token (required)
A ScribbleLive API token must be passed to authenticate calls back to the ScribbleLive API.

###eventid (required)
To get the event id for your event, go to the writer interface for your event and in the sidebar select Syndication>API. 

###notweets=1
If this is passed on the query string, no posts from ScribbleLive that came from Twitter will be included.

###noname=1
By default, all enteries in the RSS will beginning with the name of the ScribbleLive user e.g. "John Smith: Hello world". If you pass noname=1 on the query string, no name will be appended.

##Requirements
PHP 5.x
