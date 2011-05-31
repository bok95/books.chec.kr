var BOOKSERVER_FEEDBOOKS = 'http://www.feedbooks.com/books/search.atom';

var Publication = function(entry){
    this.entry = entry;
    this.node = entry.xmlNode;
    
    this.getEpub = function(){
        epub_url = $(this.node).find('link[type*="application/epub+zip"]').attr('href');
        return epub_url;
    };
    
    this.getPdf = function(){
        epub_url = $(this.node).find('link[type*="application/pdf"]').attr('href');
        return epub_url;
    };
    
    this.getCover = function(){
        cover_url = $(this.node).find('link[type*="image/jpeg"][rel*="thumbnail"]').attr('href');
        return cover_url;
    }
    
    this.getCategoryArray = function(){
        categories = new Array();
        $(this.node).find('category').each(function(){
            categories.push($(this).attr('term'));
        });
        return categories;
    }
    
    this.getCategories = function(){
        var categories = '';
        $(this.node).find('category').each(function(){
            if (categories != '') {
                categories += ', ' + $(this).attr('term');
            }
            else {
                categories += $(this).attr('term');
            }
        });
        return categories;
    }
    
    this.getTitle = function(){
        return $(this.node).find('title').text();
    }
    
    this.getAuthorArray = function(){
        authors = new Array();
        $(this.node).find('author').each(function(){
            authors.push($(this).text());
        });
        return authors;
    }
    
    this.getAuthors = function(){
        var authors = '';
        $(this.node).find('author > name').each(function(){
            if (authors != '') {
                authors += ', ' + $(this).text();
            }
            else {
                authors += $(this).text();
            }
        });
        return authors;
    }
    
    this.getPublisher = function(){
        return $(this.node).find('publisher').text();
    }
    
    this.getLanguage = function(){
        return $(this.node).find('language').text();
    }
}

var Shelf = function(args, callback){
    this.callback = callback;
	this.url;
    var totalResultCount;
	
	this.feedLoad = function(){
        var feed = new google.feeds.Feed(this.url);
        feed.setResultFormat(google.feeds.Feed.MIXED_FORMAT);
        
        feed.includeHistoricalEntries();
        feed.setNumEntries(250);
        feed.load($.proxy(this, this.feedLoaded));
    }
	
    this.feedLoaded = function(result){
		var values = new Array();
		var pubs = new Array();
		
        if (!result.error) {
            
            entries = result.feed.entries;
            for (var i = 0; i < entries.length; i++) {
                var entry = entries[i];
                pub = new Publication(entry);
                pubs.push(pub);
            }
			values['pubs'] = pubs;
			values['result'] = result;
            callback(values);
        }
    }
    
    this.getPubs = function(){
        return (pubs != null) ? pubs : null;
    }
	
	getPubTotalCount = function() {};
}

var FBShelf = function(args, callback) {
	this.setup(args);
	Shelf.call(this, args, callback);
}
FBShelf.prototype = new Shelf();
FBShelf.prototype.constructor = FBShelf;
FBShelf.prototype.setup = function(args){
	var page = parseInt(args['page']);
	page++;
	arg = 'query=' + args['query'] + '&page=' + page;
   	this.url = 'http://www.feedbooks.com/books/search.atom?' + arg;
}
FBShelf.prototype.getPubTotalCount = function(result){
	xmlDocument = result.xmlDocument;
	var q; 
	if ($.browser.mozilla) {
		q = 'opensearch\\:totalResults';
	}else if($.browser.webkit){
		q = 'totalResults'
	}
	countStr = $(xmlDocument).find(q).text();
	var count = 0;
	if(countStr != null){
		if(countStr.length > 0){
				count = parseInt(countStr);
				return count;
		}else{
			
		}
	}
	return count;
}
	
var IAShelf = function(args, callback) {
	this.setup(args);
	Shelf.call(this, args, callback);
	
}
IAShelf.prototype = new Shelf();
IAShelf.prototype.constructor = IAShelf;
IAShelf.prototype.setup = function(args){
	arg = 'q=' + args['query'] + '&start=' + args['page'];
	this.url = 'http://bookserver.archive.org/catalog/opensearch?' + arg;	
}
IAShelf.prototype.getPubTotalCount = function(result){
	title = result.feed.title;
	if(title){
		pageInfo = title.match(/[\d\.]+/g);
	    if (pageInfo) {
	    	count = pageInfo[2];
	    }
	    return (count != null) ? count : 0;
	}else{
		return 0;
	}
}

