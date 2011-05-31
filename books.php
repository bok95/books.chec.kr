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
    
    var page;
	var q;
	var cpType = 1;
	var fbShelf;
	var iaShelf;
	var itemPerPage;
	
	function showShelf(shelf, data){
		pubs = data['pubs'];
		result = data['result'];
		
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
			items_per_page: itemPerPage,
			link_to: '/?q=' + q + '&cpType=' + cpType + '&page=__id__',
			next_text:">>", 
			prev_text: "<<"
			};
		
		pubTotalCount = shelf.getPubTotalCount(result);
		if(pubTotalCount > 0){
			$("div.pagination").pagination(pubTotalCount, optInit);
		}
	}

	function onFbShelfResult(data) {
		clog("onFbShelfResult(0)");
		result = data['result'];
		if(cpType == 1){
			clog("showShelf(0)");
			itemPerPage = 20;
			showShelf(fbShelf, data);
			clog("showShelf(1)");
		}
		var resultCount = fbShelf.getPubTotalCount(result);
		var countStr = '<span class="resultCount"> (' + resultCount + ')</span>';
		$('p.server a#fb').append(countStr);
		clog("onFbShelfResult(1)");
		clog("fb : count " + resultCount);
	}
	
	function onIaShelfResult(data) {
		clog("onIaShelfResult(0)");
		result = data['result'];
		if(cpType == 2){
			clog("showShelf(0)");
			itemPerPage = 50;
			showShelf(iaShelf, data);
			clog("showShelf(1)");
		}
		var resultCount = iaShelf.getPubTotalCount(result);
		var countStr = '<span class="resultCount"> (' + resultCount + ')</span>';
		$('p.server a#ia').append(countStr);
		clog("onIaShelfResult(1)");
		clog("ia : count " + resultCount);
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
			
			fbShelfLoad(args);
			iaShelfLoad(args);
			
			switch(cpType) {
				case 1:
					$('p.server a#fb').addClass("selected");
					break;
				case 2:
					$('p.server a#ia').addClass("selected");
					break;					
				default: 
					break;
			}
			setupServers();
			$('div.left_panel').show();
		}
    }
    
    google.setOnLoadCallback(onLoad);
	
	function pageselectCallback(page_index, jq){
		return true;
	}

	function fbShelfLoad(args){
		clog("fbShelfLoad(0)");
		fbShelf = new FBShelf(args, onFbShelfResult);
		fbShelf.feedLoad();
		clog("fbShelfLoad(1)");
	}

	function iaShelfLoad(args){
		clog("iaShelfLoad(0)");
		iaShelf = new IAShelf(args, onIaShelfResult);
		iaShelf.feedLoad();
		clog("iaShelfLoad(1)");
	}
	
	function setupServers() {
		clog("setupServers()");
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
            <div class="left_panel upper_line">
                <p class="search_in">
                    Search in
                </p>
                <p class="server">
                    <a id="fb" href="http://www.feedbooks.com/">feedbooks</a>
                </p>
                <p class="server">
                    <a id="ia" href="http://www.archive.org/">Internet Archive</a>
                    
                </p>
            </div>
            <div id=list_data class="center_list  upper_line">
                <div id=searching>
                    <h3 class="title">Searching ...</h3>
                </div>
            </div>
            <div class="pagination">
            </div>
        </div>
  </body>
</html>