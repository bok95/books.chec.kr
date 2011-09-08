<!DOCTYPE HTML>
<html><head>
<? include 'head.php'; ?>

<?	
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

<script type="text/javascript" >

	checkBrowser();
	
	google.load("feeds", "1");

	var Argument = function() {
	    this.page;
		this.cpType = 1;
		this.itemPerPage;
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

	var IT = function(){
		this.shelf;
		this.loadShelf = function(args){
			var values = makeArgs(BS_TYPE.IT);
			values['entity'] = "iPadSoftware";
			clog("itShelfLoad(0)");
			this.shelf = new ITShelf(values);
			this.shelf.feedLoad(onItShelfResult);
			clog("itShelfLoad(1)");
		}
	}
	
	var bsPG;
	var bsFB;
	var bsIA;
	var bsIT;
	var args;


	function makeArgs(type){
		if(args.cpType != type){
			page = 0;
		}else{
			page = args.page;
		}
		var values = {
			type: 'top',
			page:	page
		}
		return values;
	}
	
	function gotoBookPage(id) {
		location.href = "/" + args.cpType + "/" + id;
	}
	
	$(".item").live("click", function(){
	   gotoBookPage(this.id);
	});
	
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
			case BS_TYPE.IT:
				shelf = bsIT.shelf;
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
						'<li ' + 'id=' + id +' class="item">' +
								'<div class="cover" >';
		content_data += cover;
		content_data += 		'</div>' +
								'<div class="meta_data">' +
									'<div class="title" >' +
					  					'<h3 class="title">' + 	title +
										'</h3>' +
									'</div>';
		content_data += makeMetaData("Author", author);
		content_data += makeMetaData("Language", language);
		content_data += makeMetaData("Category", category);
		if(args.cpType == BS_TYPE.IT){
			content_data += makePrice(pub.getPrice());
		}
								'</div>' +
						'</li>';
								
		// content_data += makeMetaData("Category", category);
		// content_data += makeMetaData("Publisher", publisher);
		// 
		// content_data +=	'</div>' +
		// 				'<div class="right_panel">' +
		// 					'<h4 class="download_header">Download</h4>' +
		// 					'<div class="format">';
		// content_data += epub;
		// content_data += pdf;
		// content_data += kindle;
		// content_data += audio;
		// content_data += '</div>' +	
		// 				'</div>' +
		// 				'<hr class="split">';
		$('#items').append(content_data);
	}
	function makePrice(price) {
		if(price == 0.0 || price == 0){
			priceVal = "free";
			priceClass = 'class="priceFree"';
		}else{
			priceVal = "" + price;
			priceClass = '';
		}
		html = '<p class="meta_tag">Price : '  + '<span ' + priceClass + '>' + priceVal + '</span></p>';
		return html;
	}
	
	function makeMetaData(name, tag) {
		return (tag) ? 
			'<p class="meta_tag">' + 
			name + ' : '  +
			'<span>' + tag + '</span>' +
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
		for( x in pubs){
			var pub = pubs[x];
			if(pub){
				showPub(pub);
			}
		}//for

		// showResultMsg(result.feed.title);
		if(args.cpType == BS_TYPE.IT){
			result = pubs.length;
		}
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
				items_per_page: args.itemPerPage,
				link_to: '/top.php?cpType=' + args.cpType + '&page=__id__',
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

	function showSearchingMsg(){
		$('#searching').append('<h3 class="title">Searching ...</h3>');
	}
	
	function hideSearchingMsg(){
		$('#searching .title').remove();
	}

	function showNotFoundMsg(){
		$('#searching').append('<h3 class="title">Not found "' + args.q + '"</h3>');
	}

	function makeCoverTag(cover){
		if(args.cpType == BS_TYPE.IT){
			coverClass = ' class= "iTunesThumb "';
		}else{
			coverClass = ' class= "thumb cover_shadow"';
		}
		return (cover) ? '<img src=' + cover + coverClass + '/>' : "";
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
			args.itemPerPage = 20;
			showShelf(bsFB.shelf, data, 0);
			clog("showShelf(1)");
		}
		var resultCount = bsFB.shelf.getPubTotalCount(result);
		// $('p.server a#fb').append(makeCountTag(resultCount));
		clog("onFbShelfResult(1)");
		clog("fb : count " + resultCount);
	}
	
	function onIaShelfResult(data) {
		clog("onIaShelfResult(0)");
		result = data['result'];
		if(args.cpType == BS_TYPE.IA){
			clog("showShelf(0)");
			args.itemPerPage = 50;
			showShelf(bsIA.shelf, data, 0);
			clog("showShelf(1)");
		}
		var resultCount = bsIA.shelf.getPubTotalCount(result);
		// $('p.server a#ia').append(makeCountTag(resultCount));
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
		
		bsPG.pubIDs = data['pubIDs'];
		if(bsPG.pubIDs){
			if(bsPG.pubIDs.length > 0){
				var count = bsPG.getPubCount();
				count += "+";
				// $('p.server a#pg').append(makeCountTag(count));
				
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

	function onItShelfResult(data) {
		clog("onItShelfResult(0)");
		var pubs = data['pubs'];
		if(args.cpType == BS_TYPE.IT){
			clog("showShelf(0)");
			args.itemPerPage = 50;
			showShelf(bsIT.shelf, data, 0);
			clog("showShelf(1)");
		}
		var resultCount = pubs.length;
		// $('p.server a#it').append(makeCountTag(resultCount));
		clog("onItShelfResult(1)");
		clog("it : count " + resultCount);
	}	
	    
    function onLoad() {
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
			bsIT = new IT();
			bsFB.loadShelf();
			bsIA.loadShelf();
			bsPG.loadShelf();
			bsIT.loadShelf();
			
			setupServers();
			//showLeftPanel();
			showSearchingMsg();
		}
		applySearchText(args.q);
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
			case 4:
				$('p.server a#it').addClass("selected");
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
		// $('#search').addClass('menu_selected');
		serverSelected();
		url = '/top.php?page=0' + '&cpType=';
		$('p.server a#fb').attr('href', url + '1');
		$('p.server a#ia').attr('href', url + '2');
		$('p.server a#pg').attr('href', url + '3');
		$('p.server a#it').attr('href', url + '4');
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

	$('p.server a#it').live('click', function (e){
		args.cpType = 4;
	});
	
	$('#searchBtn').live('click', function (e){
		_search();
    });

    $('#searchText').live('keyup', function(e){
     	    if (e.keyCode==13) {
     	        _search();
     	    }
     });

	function applySearchText(text) {
		$('#searchText').val(text);
	}
	
    function _search(){
    	var str = $('#searchText').val();
		if(str != null){
	    	window.location = "/?q=" + str;
		}
    }
    </script>

	<? include 'ga.php'?>
</head><body id="body" >

<? include 'header.php'; ?>

<div id="container_bg" >		
<div id="container" >	
	<div id="left_panel" >
		<p class="search_in" >Top Downloaded</p>
		<p class="server" >
			<a id="fb" href="http://www.feedbooks.com/" >feedbooks</a>
		</p>
		<p class="server" >
			<a id="ia" href="http://www.archive.org/" >Internet Archive</a>
		</p>
		<p class="server" >
			<a id="pg" href="http://www.gutenberg.org/" >Gutenberg</a>
		</p>
		<!-- <p class="server" >
			<a id="it" href="http://itunes.apple.com" >iTunes (AppStore)</a>
		</p> -->
		<div id="twitter_follow">
			<iframe allowtransparency="true" frameborder="0" scrolling="no"
			  src="http://platform.twitter.com/widgets/follow_button.html?screen_name=CheckrBooks&show_count=false"
			  style="width:300px; height:20px;"></iframe>
		</div>
		
		<iframe id="fb_like_btn" src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FCheckrBooks%2F168948329834305&width=190&colorscheme=light&show_faces=true&border_color&stream=false&header=true&height=340" scrolling="no" frameborder="0" allowtransparency="true" ></iframe>
	</div> <!-- left_panel -->
	<div id="center_panel" >
			<div id="list_data" >
				<div id="searching" >
					
				</div>
				<ul id="items" >
				</ul>
			</div>			
			<div class="pagination" ></div>
	</div> <!-- center_panel -->
</div> <!-- container -->
</div> <!-- container_bg -->

</div></body></html>