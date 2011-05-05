<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>books.chec.kr</title>
	<link href="css/epubbooks.css" type="text/css" rel="stylesheet" />
	<link href="css/epubbooks-typography.css" type="text/css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
	<script src="publication.js"></script>
	<script src="http://www.google.com/jsapi?key=ABQIAAAANh1OABxsMaSvl1OTck5I8RRL6ZglLh05n3dnEWnjIUmqeCfcGhRa7yfe_Pf1zInO6RCfBTBOMiWPLQ" type="text/javascript"></script>
    <script type="text/javascript">

	<?
		if(!empty($_GET['q'])){
			$q = $_GET['q'];
		}else{
			$q = -1;
		}
	?> 
    google.load("feeds", "1");
    
    // Our callback function, for when a feed is loaded.
    function feedLoaded(result) {
      if (!result.error) {
        // Grab the container we will put the results into
        var container = document.getElementById("content");
        container.innerHTML = '';
    
        // Loop through the feeds, putting the titles onto the page.
        // Check out the result object for a list of properties returned in each entry.
        // http://code.google.com/apis/ajaxfeeds/documentation/reference.html#JSON
        for (var i = 0; i < result.feed.entries.length; i++) {
			var entry = result.feed.entries[i];
			
			var div = document.createElement("div");
			div.className = 'books-index';
          	
			var book = new Publication(entry);
			book.getEpub();
			book.getCover();
			categories = book.getCategories();
			authors = book.getAuthors();
			title = book.getTitle();
			publisher = book.getPublisher();
			language = book.getLanguage();
							
         	// div.appendChild(document.createTextNode(i + ': ' + entry.title));
         	// container.appendChild(div);
        }
      }
    }
    
    function OnLoad() {
		
      // Create a feed instance that will grab Digg's feed.
		var q = "<? echo $q ?>";
		if(q != -1){
			
		    var feed = new google.feeds.Feed("http://bookserver.archive.org/catalog/opensearch?q=" + q);
		    feed.setResultFormat(google.feeds.Feed.MIXED_FORMAT);

		    feed.includeHistoricalEntries(); // tell the API we want to have old entries too
		    feed.setNumEntries(250); // we want a maximum of 250 entries, if they exist
    
		    // Calling load sends the request off.  It requires a callback function.
		    feed.load(feedLoaded);
			
		}
    }
    
    google.setOnLoadCallback(OnLoad);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
    <!-- <div id="content">Loading...</div> -->
	<div id="content"> 
	<div id="content-left-panel"> 


	<div class="books-index"> 
	  <div class="books-index-image"> 
	    <a href="/book/574/wonderful-visit"><img src="/img-book-covers/thumbs/wells-wonderful-visit-bookcover-thumb.jpg" alt="The Wonderful Visit Book Cover Image" /></a>
	  </div>
	  <h2 class="books-index-title"><a href="/book/574/wonderful-visit">The Wonderful Visit</a></h2> 
	  <h4 class="books-index-author"> 
	    <a href="/author/herbert-george-wells">H. G. Wells</a>  
	  </h4>
	  <p>It is the tale of a fallen ahuman, falling in love and suffering all the...</p> 
	  <p class="books-index-genres">Genre: <a href="/genre/fantasy">Fantasy</a></p> 
	</div>
	
	</div> <!--content -->
	</div> <!-- content-left-panel -->
  </body>
</html>
​