var BOOKSERVER_FEEDBOOKS = 'http://www.feedbooks.com/books/search.atom';

var Publication = function(entry){
    this.entry = entry;
    this.node = entry.xmlNode;
	this.pubType = 1;
    
    this.getEpub = function(){
        return $(this.node).find('link[type*="application/epub+zip"]').attr('href');
    };
    
    this.getPdf = function(){
        return $(this.node).find('link[type*="application/pdf"]').attr('href');
    };
    
    this.getKindle = function(){
        return $(this.node).find('link[type*="application/x-mobipocket-ebook"]').attr('href');
    };
    
    this.getCover = function(){
        //PG : image/png
        cover_url = $(this.node).find('link[type*="image/jpeg"][rel*="thumbnail"]').attr('href');
        return (cover_url != null) ? cover_url : "";
    }
    
    //Category 
    //FB : label
    //IA : term
    //PG : term
    this.getCategoryArray = function(){
        categories = new Array();
        $(this.node).find('category').each(function(){
            categories.push($(this).attr('term'));
        });
        return categories;
    }
    
    this.getCategories = function(){
        var categories = '';
		var parent = this;
        $(this.node).find('category').each(function(){
            if (categories != '') {
				var term = $(this).attr('term');
				if(term == "Sound" || term == "sound"){
					parent.pubType = 2;
				}
                categories += ', ' + term;
            }
            else {
                categories += $(this).attr('term');
            }
        });
        return categories;
    }
    
	this.getPubType = function(){
		return this.pubType;
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
    
    //Publisher
    //IA : dcterms:publisher
    //FB : dcterms:source
    //PG : 
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
        
            var entries = result.feed.entries;
            for (var i = 0; i < entries.length; i++) {
                var entry = entries[i];
                pub = new Publication(entry);
                pubs.push(pub);
            }
            values['pubs'] = pubs;
            values['result'] = result;
            callback(values);
        }
        else {
            values['result'] = result;
            callback(values);
        }
    }
    
    this.getPubs = function(){
        return (pubs != null) ? pubs : null;
    }
    
    getPubTotalCount = function(){
    };
}

var FBShelf = function(args, callback){
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
    }
    else 
        if ($.browser.webkit) {
            q = 'totalResults'
        }
    var countStr = $(xmlDocument).find(q).text();
    var count = 0;
    if (countStr != null) {
        if (countStr.length > 0) {
            count = parseInt(countStr);
        }
    }
    return count;
}



var IAShelf = function(args, callback){
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
    var count = 0;
    
    if (title) {
        pageInfo = title.match(/[\d\.]+/g);
        if (pageInfo) {
            var countStr = pageInfo[2];
            if (countStr != null) {
                if (countStr.length > 0) {
                    count = parseInt(countStr);
                }
            }
        }
    }
    return count;
}

var Catalog = function(args, callback){
    this.callback = callback;
    
    this.getCatalog = function(){
        var feed = new google.feeds.Feed(this.url);
        feed.setResultFormat(google.feeds.Feed.MIXED_FORMAT);
        
        feed.includeHistoricalEntries();
        feed.setNumEntries(250);
        feed.load($.proxy(this, this.onCatalog));
    }
    
    this.onCatalog = function(result){
        var values = new Array();
        var pubIDs = new Array();
        
        if (!result.error) {
            entries = result.feed.entries;
            for (var i = 0; i < entries.length; i++) {
                var entry = entries[i];
                xmlNode = entry.xmlNode;
                var id = $(xmlNode).find('id').text();
                if (id) {
                    if (id.length > 0) {
                        var tmp = ".opds";
                        var extension = id.substr(-(tmp.length));
                        if (tmp == extension) {
                            pubIDs.push(id);
                        }
                    }
                }
            }
			values['pubs'] = pubIDs;
            values['result'] = result;
            callback(values);
        }
        else {
        
        }
    }
}

var PGCatalog = function(args, callback){
    this.setup(args);
    Catalog.call(this, args, callback);
}

PGCatalog.prototype = new Catalog();
PGCatalog.prototype.constructor = PGCatalog;
PGCatalog.prototype.setup = function(args){
    var page = parseInt(args['page']);
    page = (page * 25) + 1;
    arg = 'default_prefix=all' + '&sort_order=downloads' + '&query=' + args['query'] + '&start_index=' + page;
    this.url = 'http://www.gutenberg.org/ebooks/search.opds/?' + arg;
}


var PGShelf = function(pubID, callback){
    this.pubID = pubID;
    this.callback = callback;
	this.pubs = new Array();
	var values = new Array();
	
    this.feedLoad = function(){
        var id = this.pubID;
		if(id){
	        this.getPub(id);
		}else{
			callback(pubs);
		}
    }
    
    this.getPub = function(url){
        var feed = new google.feeds.Feed(url);
		clog("getPub:" + url);
        feed.setResultFormat(google.feeds.Feed.MIXED_FORMAT);
        
        feed.includeHistoricalEntries();
        feed.setNumEntries(250);
        feed.load($.proxy(this, this.onPub));
    }
    
    this.onPub = function(result){
        var pubs;
        clog("onPub");
        if (!result.error) {
            var entries = result.feed.entries;
			clog("entries = " + entries.length);
            for (var i = 0; i < entries.length; i++) {
                var entry = entries[i];
                pub = new Publication(entry);
            }
        }
		callback(pub);
    }
}


PGShelf.prototype = new PGShelf();
PGShelf.prototype.constructor = PGShelf;
PGShelf.prototype.setup = function(args){
}


PGShelf.prototype.getPubTotalCount = function(result){
    var count = 0;
    
    return count;
}

var XmlUtil = function(xmlDoc){
	this.xmlDoc = xmlDoc;
	
	this.getNextPage = function(){
		return $(this.xmlDoc).find('link[type*="application/atom+xml"][rel*="next"]').attr('href');
	}
	
	this.getPrevPage = function(){
		return $(this.xmlDoc).find('link[type*="application/atom+xml"][rel*="previous"]').attr('href');
	}
}

