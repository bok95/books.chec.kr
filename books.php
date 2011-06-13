<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>books.chec.kr</title>
	<link href="css/books.css" type="text/css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
	<script src="jquery.pagination.js"></script>
	<script src="cutil.js"></script>
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

	var FB = function(){
		this.shelf;

		this.loadShelf = function(args){
			clog("fbShelfLoad(0)");
			this.shelf = new FBShelf(args, onFbShelfResult);
			this.shelf.feedLoad();
			clog("fbShelfLoad(1)");
		}
	}

	var IA = function(){
		this.shelf;

		this.loadShelf = function(args){
			clog("iaShelfLoad(0)");
			this.shelf = new IAShelf(args, onIaShelfResult);
			this.shelf.feedLoad();
			clog("iaShelfLoad(1)");
		}
	}
	
	
	var PG = function(){
		this.pgShelf;
		this.pgCatalog;
		this.pgPubIDs;
		var bNextPage; //boolean

		this.loadShelf = function(args){
			clog("loadShelf(0)");
			this.pgCatalog = new PGCatalog(args, onPgCatalogResult);
			this.pgCatalog.getCatalog();
			clog("loadShelf(1)");
		}

		this.isExistNextPage = function(){
			return bNextPage;
		}

		this.existNextPage = function(val){
			return bNextPage = val;
		}

		this.getPubCount = function(){
			return (page + 1) * 25;
		}
	}

	var bsPG;
	var bsFB;
	var bsIA;
    var page;
	var q;
	var cpType = 1;

	var itemPerPage;

	function showPub(pub){
		var epub = makeDownloadTag("epub", pub.getEpub());
		clog("epub = " + epub);
		var pdf = makeDownloadTag("pdf", pub.getPdf());
		clog("pdf = " + pdf);
		var cover = makeCoverTag(pub.getCover());
		clog("cover = " + cover);
		var categories = pub.getCategories();
		clog("categories = " + categories);
		var authors = pub.getAuthors();
		clog("authors = " + authors);
		var title = pub.getTitle();
		clog("title = " + title);
		var publisher = pub.getPublisher();
		clog("publisher = " + publisher);
		var language = pub.getLanguage();
		clog("language = " + language);
		var pubType = pub.getPubType();
		clog("pubType >>>>>> " + pubType);
		
		var content_data =
						'<div class="cover">' +
							'<a >';
		content_data += cover;
		content_data += '</a>' +
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
							'<div class="format">';
			content_data += epub;
			content_data += pdf;
			content_data += '</div>' +	
						'</div>' +
						'<hr class="split">';
			$('div#list_data').append(content_data);
	}
	
	function showShelf(shelf, data, pnType){
		pubs = data['pubs'];
		result = data['result'];
		hideSearchingMsg();
		if (pubs == null || pubs.length == 0) {
			showNotFoundMsg();
			return false;
		}
		for (var i = 0; i < pubs.length; i++) {
			var pub = pubs[i];
			if(pub){
				showPub(pub);
			}
		}//for

		showResultMsg(result.feed.title);
		showPagination(shelf, result, pnType);

	}

	function showPagination(shelf, result, type){
		var numDisplayEntries;
		var numEdgeEntries;
		switch(type){
			case 0://Normal
				numEdgeEntries = 3;
				numDisplayEntries = 5;
				break;
			case 1://Simple
				numEdgeEntries = 0;
				numDisplayEntries = 0;	
				break;
		}
			
		var optInit = {
				callback: pageselectCallback, 
				current_page: page,
				num_edge_entries:numEdgeEntries, 
				num_display_entries:numDisplayEntries, 
				items_per_page: itemPerPage,
				link_to: '/?q=' + q + '&cpType=' + cpType + '&page=__id__',
				next_text:">>", 
				prev_text: "<<"
				};
		if(result){
			pubTotalCount = shelf.getPubTotalCount(result);
		}else{
			pubTotalCount = 100;
		}
		$("div.pagination").pagination(pubTotalCount, optInit);
	}

	function showResultMsg(msg){
		$('h3.result_msg').text();
	}

	function showSearchingMsg(){
		$('#searching').append('<h3 class="title">Searching ...</h3>');
	}
	
	function hideSearchingMsg(){
		$('#searching .title').remove();
	}

	function showNotFoundMsg(){
		$('#searching').append('<h3 class="title">Not found "' + q + '"</h3>');
	}

	function makeCountTag(count){
		return '<span class="resultCount"> (' + count + ')</span>';
	}
	
	function makeCoverTag(cover){
		return (cover) ? '<img src=' + cover + ' class="thumb"/>' : "";
	}
	function makeDownloadTag(type, link){
		return (link) ? '<p>' +
		'<a href=' + link + '>' + type + '</a>' + '</p>' : "";
	}
	
	function onFbShelfResult(data) {
		clog("onFbShelfResult(0)");
		result = data['result'];
		if(cpType == 1){
			clog("showShelf(0)");
			itemPerPage = 20;
			showShelf(bsFB.shelf, data, 0);
			clog("showShelf(1)");
		}
		var resultCount = bsFB.shelf.getPubTotalCount(result);
		$('p.server a#fb').append(makeCountTag(resultCount));
		clog("onFbShelfResult(1)");
		clog("fb : count " + resultCount);
	}
	
	function onIaShelfResult(data) {
		clog("onIaShelfResult(0)");
		result = data['result'];
		if(cpType == 2){
			clog("showShelf(0)");
			itemPerPage = 50;
			showShelf(bsIA.shelf, data, 0);
			clog("showShelf(1)");
		}
		var resultCount = bsIA.shelf.getPubTotalCount(result);
		$('p.server a#ia').append(makeCountTag(resultCount));
		clog("onIaShelfResult(1)");
		clog("ia : count " + resultCount);
	}

	function onPgShelfResult(data) {
		clog("onPgShelfResult(0)");
		if(data){
			hideSearchingMsg();
			if(cpType == 3){
				showPub(data);
			}
		}
		if(bsPG.pgPubIDs){
			if(bsPG.pgPubIDs.length > 0){
				if(bsPG.pgShelf){
					var id = bsPG.pgPubIDs.pop();
					bsPG.pgShelf = new PGShelf(id, onPgShelfResult);
					bsPG.pgShelf.feedLoad();
				}
			}else{
				if(isBookServer(3)){
					showPagination(bsPG.pgShelf, null, 1);
				}
			}
		}
		clog("onPgShelfResult(1)");
	}

	function onPgCatalogResult(data) {
		clog("onPgCatalogResult(0)");

		if(data==null) return;
		
		var result = data['result'];
		
		bsPG.pgPubIDs = data['pubs'];
		if(bsPG.pgPubIDs){
			if(bsPG.pgPubIDs.length > 0){
				var count = bsPG.getPubCount();
				count += "+";
				$('p.server a#pg').append(makeCountTag(count));
				
				var id = bsPG.pgPubIDs.pop();
				bsPG.pgShelf = new PGShelf(id, onPgShelfResult);
				bsPG.pgShelf.feedLoad();
			}else{
				hideSearchingMsg();
				showNotFoundMsg();
			}
		}else{
			hideSearchingMsg();
			showNotFoundMsg();
		}

		xmlUtil = new XmlUtil(result.xmlDocument);
		var nextPage = xmlUtil.getNextPage();
		if(nextPage){
			bsPG.existNextPage(true);
		}
		var prevPage = xmlUtil.getPrevPage();

		
		clog("onPgCatalogResult(1)");
	}
	    
    function onLoad() {
		if($.browser.msie==true) {
   	 		alert('IE is not supported. Please use other browsers(Chrome, Firefox, Safari, Opera ...)');
			return false;
  		} 
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

			bsFB = new FB();
			bsIA = new IA();
			bsPG = new PG();
			bsFB.loadShelf(args);
			bsIA.loadShelf(args);
			bsPG.loadShelf(args);
			
			switch(cpType) {
				case 1:
					$('p.server a#fb').addClass("selected");
					break;
				case 2:
					$('p.server a#ia').addClass("selected");
					break;					
				case 3:
					$('p.server a#pg').addClass("selected");
					break;					
				default: 
					break;
			}
			setupServers();
			$('div.left_panel').show();
			showSearchingMsg();
		}
    }
    
    google.setOnLoadCallback(onLoad);

	function isBookServer(type){
		return (cpType == type) ? true : false;
	}
	
	function pageselectCallback(page_index, jq){
		return true;
	}
	
	function setupServers() {
		clog("setupServers()");
		url = '/?' + 'q=' + q + '&page=0' + '&cpType=';
		$('p.server a#fb').attr('href', url + '1');
		$('p.server a#ia').attr('href', url + '2');
		$('p.server a#pg').attr('href', url + '3');
	}
	
	$('p.server a#fb').live('click', function (e){
		cpType = 1;
		
	});
	
	$('p.server a#ia').live('click', function (e){
		cpType = 2;
	});
	
	$('p.server a#pg').live('click', function (e){
		cpType = 3;
	});
	
	$('#searchBtn').live('click', function (e){
		_search();
    });

    $('#searchText').live('keyup', function(e){
	    if (e.keyCode==13) {
	        _search();
	    }
    });

    function _search(){
    	var str = $('#searchText').val();
		if(str != null){
	    	window.location = "/?q=" + str;
		}
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
        <div class="search">
        	<div id="home">
	            <a href="/">
    	        	<img class="logo_img" src="images/ebook-48.png" border="0" height="48">
					<h2 class="logo_txt">Checkr</h2>
				</a>
        	</div>
            <div style="position:relative;zoom:1">
                <input id="searchText" maxlength="2048" name="q" size="50" accesskey="s" id="hpq" style="text-align: left; ">
				<button id="searchBtn">search</button>
            </div>
        </div><hr class="split">
        <div class="container">
            <div class="left_panel">
                <p class="search_in">
                    Search in
                </p>
                <p class="server">
                    <a id="fb" href="http://www.feedbooks.com/">feedbooks</a>
                </p>
                <p class="server">
                    <a id="ia" href="http://www.archive.org/">Internet Archive</a>
                </p>
                <p class="server">
                    <a id="pg" href="http://www.gutenberg.org/">Gutenberg</a>
                </p>
            </div>
            <div id=list_data class="center_list">
                <div id=searching>
                </div>
            </div>
            <div class="pagination">
            </div>
        </div>
  </body>
</html>