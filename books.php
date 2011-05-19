<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>books.chec.kr</title>
	<link href="css/books.css" type="text/css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
	<script src="jquery.pagination.js"></script>
	<script src="publication.js"></script>
	<script src="ia.js"></script>
	<script src="http://www.google.com/jsapi?key=ABQIAAAANh1OABxsMaSvl1OTck5I8RRL6ZglLh05n3dnEWnjIUmqeCfcGhRa7yfe_Pf1zInO6RCfBTBOMiWPLQ" type="text/javascript"></script>
    <script type="text/javascript">

	<?
		if(!empty($_GET['q'])){
			$q = $_GET['q'];
		}else{
			$q = -1;
		}
		
		if(!empty($_GET['page'])){
			$page = $_GET['page'];
		}else{
			$page = -1;
		}
	?> 
    google.load("feeds", "1");
    var page;
	var q;
	
    // Our callback function, for when a feed is loaded.
    function feedLoaded(result) {
      if (!result.error) {
        for (var i = 0; i < result.feed.entries.length; i++) {
			var entry = result.feed.entries[i];
			var book = new Publication(entry);
			epub = book.getEpub();
			pdf = book.getPdf();
			cover = book.getCover();
			categories = book.getCategories();
			authors = book.getAuthors();
			title = book.getTitle();
			publisher = book.getPublisher();
			language = book.getLanguage();
			
			var content_data ='<div class="cover">' +
								'<a >' + 
									'<img src=' + cover + ' class="thumb"/>' + 
								'</a>' +
							'</div>' +
							'<div class="content">' +
					  			'<h3 class="title">' + 
									'<a>' + title + '</a>' +
								'</h3>' +
					  			'<p class="mata_tag">' + 
									'Author : '  +
									'<span class="meta_data">' + authors + '</a>' +
								'</p>' +
						  		'<p class="mata_tag">' + 
									'Language : '  +
									'<span class="meta_data">' + language + '</a>' +
								'</p>' +
				  				'<p class="mata_tag">' + 
									'Category : '  +
									'<span class="meta_data">' + categories + '</a>' +
								'</p>' +
						  		'<p class="mata_tag">' + 
									'Publisher : '  +
									'<span class="meta_data">' + publisher + '</a>' +
								'</p>' +
							'</div>' +
							'<div class="right_panel">' +
								'<h4 class="download_header">Download</h4>' +
								'<div class="format">' +
									'<p>' +
										'<a href=' + epub + '>' + "epub" + '</a>' +
									'</p>' +
									'<p>' +
										'<a href=' + pdf + '>' + "pdf" + '</a>' +
									'</p>' +
								'</div>' +	
							'</div>' +
							'<hr class="split">';
							
			$('div#list_data').append(content_data);
         	// container.appendChild(div_data);
        }
		$('div.left_panel').show();
		$('a#ia').addClass("selected");
		$('h3.result_msg').text(result.feed.title);
		var optInit = {
			callback: pageselectCallback, 
			current_page: page,
			num_edge_entries:3, 
			num_display_entries:5, 
			items_per_page: 50,
			link_to: '/?q=' + q + '&page=__id__',
			next_text:">>", 
			prev_text: "<<"
			};
		
		$("div.pagination").pagination(3210, optInit);
//		var provider = new IA(result.feed);
      }
    }
    
    function OnLoad() {
		
      // Create a feed instance that will grab Digg's feed.
		q = "<?=$q?>";
		page = parseInt("<?=$page?>");
		if(page < 0){
			page = 0;
		}
			
		if(q != -1){
			var args = 'q=' + q + '&start=' + page;
			var url = "http://bookserver.archive.org/catalog/opensearch" + '?' + args;
			
		    var feed = new google.feeds.Feed(url);
		    feed.setResultFormat(google.feeds.Feed.MIXED_FORMAT);

		    feed.includeHistoricalEntries(); // tell the API we want to have old entries too
		    feed.setNumEntries(250); // we want a maximum of 250 entries, if they exist
    
		    // Calling load sends the request off.  It requires a callback function.
		    feed.load(feedLoaded);
			
		}
    }
    
    google.setOnLoadCallback(OnLoad);
	
	function pageselectCallback(page_index, jq){
		
		return true;
	}
		
    </script>

	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-23322948-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
  </head>

  <body style="font-family: Arial;border: 0 none;">
    <!-- <div id="content">Loading...</div> -->
	<div class="container">
		<div class="left_panel">
			<p class="search_in">Search in</p>
				<p class="server">
					<a id="ia" href="http://www.archive.org/">Internet Archive</a>
				</p>
		</div>
		<div id=list_data class="center_list">
		</div>
		<div class="pagination">
			
		</div>
	</div> <!--container -->
  </body>
</html>