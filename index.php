<html>
<head>
<title>Scribble Social RSS</title>
<meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
    <style type="text/css">
    label
    {
        margin-right: 10px;
        padding-top: 1px;
    }
    </style>
</head>
<body>

<div class="container">
      <div class="hero-unit">
        <h1>Scribble Social RSS</h1>
        <p>An RSS feed from <a href="http://www.scribblelive.com">ScribbleLive</a> formatted for social networks</p>
        <div class="alert">
         <b>BETA</b> This is not an officially supported product.
        </div>
        <p><a class="btn btn-primary btn-large" href="https://github.com/scribblelive/scribble-social-rss">Download Source<br></a></p>
      </div>
      
      
      
      <form class="form-vertical" method="get" action="/twitter.php">
      <h2>Configure</h2>
      
        <label class="pull-left">Format</label> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> Twitter <span class="caret"></span> </a>
        
        <div class="btn-group">
          <ul class="dropdown-menu">
            <li>
              <a href="#">Twitter</a>
            </li>
          </ul>
        </div>
      
        <div>
            <label class="pull-left">API Token</label> <input type="text" class="input-medium" id="token" name="token" placeholder="abc123">
        </div>
        
        <div>
            <label class="pull-left">Event Id</label> <input type="text" class="input-medium" id="eventid" name="eventid" placeholder="123">
        </div>
        
        <div>
            <label class="checkbox" for="nonamecheckbox">
              <input type="checkbox" value="" onChange="document.getElementById('noname').value = (this.checked ? 0 : 1 );" id="nonamecheckbox" checked="checked">
              <span>Include usernames</span>
            </label>
        </div>
        <input type="hidden" id="noname" name="noname" value="" />
        <div>
            <label class="checkbox" for="notweetscheckbox">
              <input type="checkbox" value="" onChange="document.getElementById('notweets').value = (this.checked ? 0 : 1 );" id="notweetscheckbox" checked="checked">
              <span>Include tweets</span>
            </label>
        </div>
        <input type="hidden" id="notweets" name="notweets" value="" />
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Generate Feed</button>
          <input type="reset" class="btn" value="Reset">
        </div>
      </form>
    </div>

</body>
</html>