var BS_TYPE = {
    FB: 1,
    IA: 2,
    PG: 3
};


var Publication = function(){
    this.entry;
    
    this.getEpub = function(){
        return $(this.entry).find('link[type*="application/epub+zip"]').attr('href');
    };
    
    this.getPdf = function(){
        return $(this.entry).find('link[type*="application/pdf"]').attr('href');
    };
    
    this.getKindle = function(){
        return $(this.entry).find('link[type*="application/x-mobipocket-ebook"]').attr('href');
    };
    
    this.getCoverS = function(){
        //PG : image/png
        cover_url = $(this.entry).find('link[type*="image/jpeg"][rel*="thumbnail"]').attr('href');
        return (cover_url != null) ? cover_url : "";
    }

    this.getLCoverL = function(){
        //PG : image/png
        cover_url = $(this.entry).find('link[type*="image/jpeg"][rel*="image"]').attr('href');
        return (cover_url != null) ? cover_url : "";
    }



    
    //Category 
    //FB : label
    //IA : term
    //PG : term
    this.getCategoryArray = function(){
        categories = new Array();
        $(this.entry).find('category').each(function(){
            categories.push($(this).attr('term'));
        });
        return categories;
    }
    
    this.getCategories = function(){
        var categories = '';
        var parent = this;
        $(this.entry).find('category').each(function(){
            if (categories != '') {
                var term = $(this).attr('term');
                categories += ', ' + term;
            }
            else {
                categories += $(this).attr('term');
            }
        });
        return categories;
    }
    
    this.getTitle = function(){
        return $(this.entry).find('title').text();
    }
    
    this.getAuthorArray = function(){
        authors = new Array();
        $(this.entry).find('author').each(function(){
            authors.push($(this).text());
        });
        return authors;
    }
    
    this.getAuthors = function(){
        var authors = '';
        $(this.entry).find('author > name').each(function(){
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
        return $(this.entry).find('publisher').text();
    }
    
    this.getLanguage = function(){
        return $(this.entry).find('language').text();
    }

	this.getSummary = function() {
		return $(this.entry).find('summary').text();
	}
	
	this.getRights = function() {
		return $(this.entry).find('rights').text();
	}
	
}
Publication.prototype.setEntry = function(entry) {
	this.entry = entry;
};
Publication.prototype.hasAudioFile = function(){
    return false;
}
Publication.prototype.getID = function(){
    return null;
}
Publication.prototype.getAudio = function(){
}
Publication.prototype.sameAuthor = function(){
}


var FBPublication = function(){
    Publication.call(this);
}
FBPublication.prototype = new Publication();
FBPublication.prototype.constructor = FBPublication;
FBPublication.prototype.hasAudioFile = function(){
    var dcmiType = $(this.entry).find('category[scheme*="http://purl.org/dc/terms/DCMIType"]').attr('term');
    return (dcmiType == "Sound") ? true : false;
}

FBPublication.prototype.setEntry = function(entry) {
	this.entry = entry;
}

FBPublication.prototype.getID = function(){
    var idStr = $(this.entry).find('id').text();
    var id;
    if (idStr) {
        var array = getNumberStringArray(idStr);
        if (array) {
            id = array[0];
        }
    }
    return id;
}

FBPublication.prototype.sameAuthor = function(){
	link = $(this.entry).find('link[type*="application/atom+xml"][title*="From the same author"]').attr('href');
    return (link != null) ? link : "";
}

FBPublication.prototype.alsoDownload = function(){
	link = $(this.entry).find('link[type*="application/atom+xml"][title*="People also downloaded..."]').attr('href');
    return (link != null) ? link : "";
}

var PGPublication = function(){
    Publication.call(this);
}
PGPublication.prototype = new Publication();
PGPublication.prototype.constructor = PGPublication;
PGPublication.prototype.hasAudioFile = function(){
    var dcmiType = $(this.entry).find('category[scheme*="http://purl.org/dc/terms/DCMIType"]').attr('term');
    return (dcmiType == "Sound") ? true : false;
}

PGPublication.prototype.setEntry = function(entry) {
	this.entry = entry;
}

PGPublication.prototype.getID = function(){
    var idStr = $(this.entry).find('id').text();
    var id;
    if (idStr) {
        var array = getNumberStringArray(idStr);
        if (array) {
            id = array[0];
        }
    }
    return id;
}

PGPublication.prototype.getAudio = function(){
    if (this.hasAudioFile()) {
        var id = this.getID();
        return 'http://www.gutenberg.org/files/' + id + '/' + id + '-index.html';
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
                pub = new Publication();
				pub.setEntry(entry.xmlNode);
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
}
Shelf.prototype.getPubTotalCount = function(){
    return 0;
};
Shelf.prototype.getPubID = function(){
    return null;
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

FBShelf.prototype.getPubID = function(pub){
    var idStr = $(pub.entry).find('id').text();
    var id = null;
    if (idStr) {
        var s = idStr.lastIndexOf('/');
        id = idStr.substr(s + 1, idStr.length);
    }
    return id;
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
        if (id) {
            this.getPub(id);
        }
        else {
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
                pub = new PGPublication();
				pub.setEntry(entry.xmlNode);
            }
        }
        callback(pub);
    }
}

PGShelf.prototype = new PGShelf();
PGShelf.prototype.constructor = PGShelf;
PGShelf.prototype.setup = function(args){
}

var Feeder = function(id){
    this.url;
    this.setUrl(id);
}

Feeder.prototype.setUrl = function(id){

}



var FBFeeder = function(id){
    Feeder.call(this, id);
    
}
FBFeeder.prototype = new Feeder();
FBFeeder.prototype.constructor = Feeder;
FBFeeder.prototype.setUrl = function(id){
    this.url = "http://www.feedbooks.com/book/" + id + ".atom";
}
FBFeeder.prototype.feedLoad = function(callback){
    $.ajax({
        type: "get",
        dataType: "xml",
        url: this.url,
        success: function(xml){
			var entry = $(xml).find("entry");
            if (entry.length > 0) {
                var pub = new FBPublication();
				pub.setEntry(entry);
				callback(pub);
            }
        },
        error: function(){
            alert("xml error!!");
        }
    });
}


