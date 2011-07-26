<!DOCTYPE HTML>
<html><head>
<? include 'head.php'; ?>

<?
	if(!empty($_GET['type'])){
		$type = $_GET['type'];
	}else{
		$type = -1;
	}
?>

<script type="text/javascript" >
	checkBrowser();
	google.load("feeds", "1");
	
	var as;
	var shelf;
	var args;
	
	var Argument = function() {
		this.type = 1;
	}

	var AS = function(type){
		this.type = type;
		this.loadShelf = function(){
			var values = makeArgs(this.type);
			clog("asShelfLoad(0)");
			shelf = new ASShelf(values, onShelfResult);
			shelf.feedLoad();
			clog("asShelfLoad(1)");
		}
	}

	function makeArgs(type){
		var values = {
			type: type
		}
		return values;
	}
	
	function gotoBookPage(id) {
		location.href = "/" + BS_TYPE.IT + "/" + id;
	}
	
	$(".item").live("click", function(){
	   gotoBookPage(this.id);
	});
	
	function showPub(pub){
		var id = shelf.getPubID(pub);
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
		var pubDate = pub.getPubDate();
		
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
		content_data += makeMetaData("Category", category);
		content_data += makeMetaData("Date", pubDate);
		content_data += makePrice(pub.getPrice());

								'</div>' +
						'</li>';
								
		$('#items').append(content_data);
	}
	
	function makePrice(price) {
		if(price.indexOf("Free") != -1){
			priceVal = price;
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
		for( i=0; i<pubs.length; i++){
			var pub = pubs[i];
			if(pub){
				showPub(pub);
			}
		}//for

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
		coverClass = ' class= "asThumb"';
		return (cover) ? '<img src=' + cover + coverClass + '/>' : "";
	}

	function onShelfResult(data) {
		clog("onShelfResult(0)");
		var pubs = data['pubs'];
		clog("showShelf(0)");
		showShelf(shelf, data, 0);
		clog("showShelf(1)");
		clog("onShelfResult(1)");
	}	
	    
    function onLoad() {
  		args = new Argument();

		args.type = parseInt("<?=$type?>");
		if(args.type < 0){
			args.type = 1;
		}

		as = new AS(args.type);
		as.loadShelf();
			
		setupServers();
		showSearchingMsg();
    }
    
    function isBookServer(type){
		return (args.type == type) ? true : false;
	}
	
	function pageselectCallback(page_index, jq){
		return true;
	}

	function serverSelected() {
		switch(args.type) {
			case 1:
				$('p.server a#pd').addClass("selected");
				break;
			case 2:
				$('p.server a#gf').addClass("selected");
				break;					
			case 3:
				$('p.server a#ppd').addClass("selected");
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
		$('#book_apps').addClass('menu_selected');
		serverSelected();
		url = '/apps.php?' + 'type=';
		$('p.server a#pd').attr('href', url + '1');
		$('p.server a#gf').attr('href', url + '2');
		$('p.server a#ppd').attr('href', url + '3');
	}
	
	$('p.server a#pd').live('click', function (e){
		args.type = AS_TYPE.PD;
		
	});
	
	$('p.server a#gf').live('click', function (e){
		args.type = AS_TYPE.GF;
	});
	
	$('p.server a#ppd').live('click', function (e){
		args.type = AS_TYPE.PPD;
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

    google.setOnLoadCallback(onLoad);

    </script>

	<? include 'ga.php'?>
</head><body id="body" >

<? include 'header.php'; ?>

<div id="container_bg" >		
<div id="container" >	
	<div id="left_panel" >
		<p class="search_in" >Book Apps for iPad</p>
		<p class="server" >
			<a id="pd" href="/" >Price Drops</a>
		</p>
		<p class="server" >
			<a id="gf" href="/" >Going Free</a>
		</p>
		<p class="server" >
			<a id="ppd" href="/" >Popular Price Drops</a>
		</p>
		
		<iframe id="fb_like_btn" src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FCheckrBooks%2F168948329834305&width=190&colorscheme=light&show_faces=true&border_color&stream=false&header=true&height=330" scrolling="no" frameborder="0" allowtransparency="true" ></iframe>
	</div> <!-- left_panel -->
	
	<div id="center_panel" >
			<div id="list_data" >
				<div id="searching" >
					
				</div>
				<ul id="items" >
				</ul>
				<div id="dummy" class="unvisible">
				</div>
			</div>			
	</div> <!-- center_panel -->
	<!-- <div class="pagination" ></div> -->
</div> <!-- container -->
</div> <!-- container_bg -->
</div></body></html>