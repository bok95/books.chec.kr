<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>books.chec.kr</title>
<link href="css/books.css" type="text/css" rel="stylesheet" />
<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
<script src="jquery.pagination.js"></script>
<script src="cutil.js"></script>
<script src="cp.js"></script>
<script
	src="http://www.google.com/jsapi?key=ABQIAAAANh1OABxsMaSvl1OTck5I8RRL6ZglLh05n3dnEWnjIUmqeCfcGhRa7yfe_Pf1zInO6RCfBTBOMiWPLQ"
	type="text/javascript"></script>
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


	var Argument = function() {
	    this.page;
		this.q;
		this.cpType = 1;
	}
	
	var FB = function(){
		this.shelf;
		this.loadShelf = function(args){
			clog("fbShelfLoad(0)");
			var values = makeArgs(BS_TYPE.FB);
			this.shelf = new FBShelf(values, onFbShelfResult);
			this.shelf.setup(values);
			this.shelf.feedLoad();
			clog("fbShelfLoad(1)");
		}
	}

	var IA = function(){
		this.shelf;
		this.loadShelf = function(args){
			var values = makeArgs(BS_TYPE.IA);
			clog("iaShelfLoad(0)");
			this.shelf = new IAShelf(values, onIaShelfResult);
			this.shelf.feedLoad();
			clog("iaShelfLoad(1)");
		}
	}
	
	
	var PG = function(){
		this.shelf;
		this.catalog;
		this.pubIDs;
		var bNextPage; //boolean
		var curPage=0;

		this.loadShelf = function(args){
			clog("loadShelf(0)");
			var values = makeArgs(BS_TYPE.PG);
			curPage = values['page'];
			this.catalog = new PGCatalog(values, onPgCatalogResult);
			this.catalog.getCatalog();
			clog("loadShelf(1)");
		}

		this.isExistNextPage = function(){
			return bNextPage;
		}

		this.existNextPage = function(val){
			return bNextPage = val;
		}

		this.getPubCount = function(){
			return (curPage + 1) * 25;
		}
	}

	var bsPG;
	var bsFB;
	var bsIA;
	var args;

	var itemPerPage;

	function makeArgs(type){
		if(args.cpType != type){
			page = 0;
		}else{
			page = args.page;
		}
		var values = {
			type: 'search',
			query:	args.q,
			page:	page
		}
		return values;
	}
	
	function showPub(pub){
		var shelf;
		switch(args.cpType){
			case BS_TYPE.FB:
				shelf = bsFB.shelf;
				break;
			case BS_TYPE.IA:
				shelf = bsIA.shelf;
				break;
			case BS_TYPE.PG:
				shelf = bsPG.shelf;
				break;
		}
		var id = shelf.getPubID(pub);
		var epub = makeDownloadTag("epub", pub.getEpub());
		clog("epub = " + epub);
		var pdf = makeDownloadTag("pdf", pub.getPdf());
		clog("pdf = " + pdf);
		var kindle = makeDownloadTag("Kindle", pub.getKindle());
		clog("kindle = " + kindle);
		var audio = makeDownloadTag("Audio", pub.getAudio());
		clog("kindle = " + kindle);
		var cover = makeCoverTag(pub.getCoverThumb());
		clog("cover = " + cover);
		var category = pub.getCategories();
		clog("category = " + category);
		var author = pub.getAuthors();
		clog("author = " + author);
		var title = pub.getTitle();
		clog("title = " + title);
		var publisher = pub.getPublisher();
		clog("publisher = " + publisher);
		var language = pub.getLanguage();
		clog("language = " + language);
		
		var content_data =
						'<div class="cover">' +
							'<a >';
		content_data += cover;
		content_data += '</a>' +
						'</div>' +
						'<div class="content">' +
				  			'<h3 class="title">' + 
								'<a href="/' + args.cpType + '/' + id + '">' + title + '</a>' +
							'</h3>';
		content_data += makeMetaData("Author", author);
		content_data += makeMetaData("Language", language);
		content_data += makeMetaData("Category", category);
		content_data += makeMetaData("Publisher", publisher);

		content_data +=	'</div>' +
						'<div class="right_panel">' +
							'<h4 class="download_header">Download</h4>' +
							'<div class="format">';
		content_data += epub;
		content_data += pdf;
		content_data += kindle;
		content_data += audio;
		content_data += '</div>' +	
						'</div>' +
						'<hr class="split">';
		$('div#list_data').append(content_data);
	}
	
	function makeMetaData(name, tag) {
		return (tag) ? 
			'<p class="mata_tag">' + 
			name + ' : '  +
			'<span id="" class="meta_data">' + tag + '</a>' +
		'</p>' : "";
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
				current_page: args.page,
				num_edge_entries:numEdgeEntries, 
				num_display_entries:numDisplayEntries, 
				items_per_page: itemPerPage,
				link_to: '/?q=' + args.q + '&cpType=' + args.cpType + '&page=__id__',
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
		if(args.cpType == BS_TYPE.FB){
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
		if(args.cpType == BS_TYPE.IA){
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
			if(args.cpType == BS_TYPE.PG){
				showPub(data);
			}
		}
		if(bsPG.pubIDs){
			if(bsPG.pubIDs.length > 0){
				if(bsPG.shelf){
					var id = bsPG.pubIDs.pop();
					bsPG.shelf = new PGShelf(id, onPgShelfResult);
					bsPG.shelf.feedLoad();
				}
			}else{
				if(isBookServer(3)){
					showPagination(bsPG.shelf, null, 1);
				}
			}
		}
		clog("onPgShelfResult(1)");
	}

	function onPgCatalogResult(data) {
		clog("onPgCatalogResult(0)");

		if(data==null) return;
		
		var result = data['result'];
		
		bsPG.pubIDs = data['pubs'];
		if(bsPG.pubIDs){
			if(bsPG.pubIDs.length > 0){
				var count = bsPG.getPubCount();
				count += "+";
				$('p.server a#pg').append(makeCountTag(count));
				
				var id = bsPG.pubIDs.pop();
				bsPG.shelf = new PGShelf(id, onPgShelfResult);
				bsPG.shelf.feedLoad();
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
  		args = new Argument();
  		
		args.q = "<?=$q?>";
		args.page = parseInt("<?=$page?>");
		if(args.page < 0){
			args.page = 0;
		}
		args.cpType = parseInt("<?=$cpType?>");
		if(args.cpType < 0){
			args.cpType = 1;
		}
		if(args.q != -1){
			bsFB = new FB();
			bsIA = new IA();
			bsPG = new PG();
			bsFB.loadShelf();
			bsIA.loadShelf();
			bsPG.loadShelf();
			
			setupServers();
			//showLeftPanel();
			showSearchingMsg();
		}
    }
    
    google.setOnLoadCallback(onLoad);

    function isBookServer(type){
		return (args.cpType == type) ? true : false;
	}
	
	function pageselectCallback(page_index, jq){
		return true;
	}

	function serverSelected() {
		switch(args.cpType) {
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
	}

	function showLeftPanel(){
		$('div.left_panel').show();
	}
	
	function setupServers() {
		clog("setupServers()");
		serverSelected();
		url = '/?' + 'q=' + args.q + '&page=0' + '&cpType=';
		$('p.server a#fb').attr('href', url + '1');
		$('p.server a#ia').attr('href', url + '2');
		$('p.server a#pg').attr('href', url + '3');
	}
	
	$('p.server a#fb').live('click', function (e){
		args.cpType = 1;
		
	});
	
	$('p.server a#ia').live('click', function (e){
		args.cpType = 2;
	});
	
	$('p.server a#pg').live('click', function (e){
		args.cpType = 3;
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

<body style="font-family: Arial; border: 0 none;">
	<div class="search">
		<div id="home">
			<a href="/"> <img class="logo_img" src="images/ebook-48.png"
				border="0" height="48">
					<h2 class="logo_txt">Checkr</h2>
			
			</a>
		</div>
		<div style="position: relative; zoom: 1">
			<input id="searchText" maxlength="2048" name="q" size="50"
				accesskey="s" id="hpq" style="text-align: left;">
				<button id="searchBtn">search</button>
		
		</div>
	</div>

	<hr class="split">
		<div class="container">
			<div class="left_panel">
				<p class="search_in">Search in</p>
				<p class="server">
					<a id="fb" href="http://www.feedbooks.com/">feedbooks</a>
				</p>
				<p class="server">
					<a id="ia" href="http://www.archive.org/">Internet Archive</a>
				</p>
				<p class="server">
					<a id="pg" href="http://www.gutenberg.org/">Gutenberg</a>
				</p>
				<iframe id="fb_like_btn"
					src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FCheckrBooks%2F168948329834305&amp;width=190&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=true&amp;height=330"
					scrolling="no" frameborder="0" allowTransparency="true"></iframe>
			</div>
			<div id=list_data class="center_list">
				<div id=searching></div>
			</div>
			<div class="pagination"></div>
			<div id="feedback"><a href="http://www.facebook.com/pages/CheckrBooks/168948329834305"><img src="/images/feedback.gif" /></a></div>
		</div>

</body>
</html>
