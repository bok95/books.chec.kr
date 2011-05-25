<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>books.chec.kr</title>
	<link href="css/books.css" type="text/css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
	<script src="jquery.pagination.js"></script>
	<script src="cp.js"></script>
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
		
		if(!empty($_GET['cpType'])){
			$cpType = $_GET['cpType'];
		}else{
			$cpType = -1;
		}
	?> 
    google.load("feeds", "1");
    var page;
	var q;
	var cpType = 1;
	
	function showShelf(values){
		pubs = values['pubs'];
		result = values['result'];
		
		if (pubs == null) {
			return false;
		}
		$('#searching').hide();
		for (var i = 0; i < pubs.length; i++) {
			pub = pubs[i];
			epub = pub.getEpub();
			pdf = pub.getPdf();
			cover = pub.getCover();
			categories = pub.getCategories();
			authors = pub.getAuthors();
			title = pub.getTitle();
			publisher = pub.getPublisher();
			language = pub.getLanguage();
			
			var content_data =
							'<div class="cover">' +
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
		}//for
		
		$('h3.result_msg').text(result.feed.title);
		var optInit = {
			callback: pageselectCallback, 
			current_page: page,
			num_edge_entries:3, 
			num_display_entries:5, 
			items_per_page: 50,
			link_to: '/?q=' + q + '&cpType=' + cpType + '&page=__id__',
			next_text:">>", 
			prev_text: "<<"
			};
		
		pubTotalCount = shelf.getPubTotalCount(result);
		if(pubTotalCount > 0){
			$("div.pagination").pagination(pubTotalCount, optInit);
		}
	}
	    
    function OnLoad() {
		if($.browser.msie==true) {
   	 		alert('IE is not supported. Please use other browsers(Chrome, Firefox, Safari, Opera ...)');
			return false;
  		} 
      // Create a feed instance that will grab Digg's feed.
		q = "<?=$q?>";
		page = parseInt("<?=$page?>");
		if(page < 0){
			page = 0;
		}
		cpType = parseInt("<?=$cpType?>");
		if(cpType < 0){
			cpType = 1;
		}
		if(q != -1){
			var args = {
				query:	q,
				page:	page
			}
			switch(cpType) {
				case 1:
					$('p.server a#fb').addClass("selected");
					shelf = new FBShelf(args, showShelf);
					break;
				case 2:
					$('p.server a#ia').addClass("selected");
					shelf = new IAShelf(args, showShelf);
					break;					
				default: 
					break;
			}
			
			shelf.feedLoad();		
			setupServers();
			$('div.left_panel').show();
		}
    }
    
    google.setOnLoadCallback(OnLoad);
	
	function pageselectCallback(page_index, jq){
		return true;
	}
	
	function setupServers() {
		url = '/?' + 'q=' + q + '&page=0' + '&cpType=';
		$('p.server a#fb').attr('href', url + '1');
		$('p.server a#ia').attr('href', url + '2');
	}
	
	$('p.server a#fb').live('click', function (e){
		cpType = 1;
		
	});
	
	$('p.server a#ia').live('click', function (e){
		cpType = 2;
	});
	
	
		
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
	<div class="search">
		<a href="/">
		<img class="logo_img" src="images/ebook-48.png" border="0" height="48">
		<h2 class="logo_txt">
			checkr
		</h2>
		</a>
	</div>
	<hr class="split">
	<div class="container">
		<div class="left_panel upper_line">
			<p class="search_in">Search in</p>
				<p class="server">
					<a id="fb" href="http://www.feedbooks.com/">feedbooks</a>
				</p>
				<p class="server">
					<a id="ia" href="http://www.archive.org/">Internet Archive</a>
				</p>
		</div>
		<div id=list_data class="center_list  upper_line">
			<div id=searching>
            	<h3 class="title">
            		Searching ...
            	</h3>
            </div>
		</div>
		<div class="pagination">
			
		</div>
	</div> <!--container -->
  </body>
</html>