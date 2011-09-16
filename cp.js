var BS_TYPE = {
    FB: 1,
    IA: 2,
    PG: 3,
	IT: 4,
	AS: 5
};

var AS_TYPE = {
	PD: 1,
	GF: 2,
	PPD: 3
}

var Publication = function(){
    this.entry;
}

Publication.prototype.getEpub = function(){
    return $(this.entry).find('link[type*="application/epub+zip"]').attr('href');
};

Publication.prototype.getPdf = function(){
    return $(this.entry).find('link[type*="application/pdf"]').attr('href');
};

Publication.prototype.getKindle = function(){
    return $(this.entry).find('link[type*="application/x-mobipocket-ebook"]').attr('href');
};

Publication.prototype.getCoverThumb = function(){
    //PG : image/png
    cover_url = $(this.entry).find('link[type*="image/jpeg"][rel*="thumbnail"]').attr('href');
    return (cover_url != null) ? cover_url : "";
}

Publication.prototype.getCover = function(){
    //PG : image/png
    cover_url = $(this.entry).find('link[type*="image/jpeg"][rel*="image"]').attr('href');
    return (cover_url != null) ? cover_url : "";
}

Publication.prototype.getCategoryArray = function(){
    categories = new Array();
    $(this.entry).find('category').each(function(){
        categories.push($(this).attr('term'));
    });
    return categories;
}

Publication.prototype.getTitle = function(){
    return $(this.entry).find('title').text();
}

Publication.prototype.getAuthorArray = function(){
    authors = new Array();
    $(this.entry).find('author').each(function(){
        authors.push($(this).text());
    });
    return authors;
}

Publication.prototype.getAuthors = function(){
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

Publication.prototype.getPublisher = function(){
    return $(this.entry).find('publisher').text();
}

Publication.prototype.getSummary = function() {
return $(this.entry).find('summary').text();
}

Publication.prototype.getRights = function() {
return $(this.entry).find('rights').text();
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
Publication.prototype.getScreenshotUrls = function(){
}
Publication.prototype.getCategories = function(){
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
Publication.prototype.getLanguage = function(){
    return $(this.entry).find('language').text();
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

FBPublication.prototype.getLanguage = function(){
    var q;
    if ($.browser.mozilla) {
        q = 'dcterms\\:language';
    }else if ($.browser.webkit) {
        q = 'language'
    }
    return $(this.entry).find(q).text();

}

var IAPublication = function(){
	this.files;
    Publication.call(this);
}
IAPublication.prototype = new Publication();
IAPublication.prototype.constructor = IAPublication;
IAPublication.prototype.hasAudioFile = function(){
    var dcmiType = $(this.entry).find('category[scheme*="http://purl.org/dc/terms/DCMIType"]').attr('term');
    return (dcmiType == "Sound") ? true : false;
}

IAPublication.prototype.setEntry = function(entry) {
	this.entry = entry;
}

IAPublication.prototype.getFiles = function(){
    return this.entry.files;
};

IAPublication.prototype.getEpub = function(){
	var id = this.entry.metadata.identifier;
    return "http://www.archive.org/download/" + id + "/" + id + ".epub";
};

IAPublication.prototype.getPdf = function(){
	var id = this.entry.metadata.identifier;
    return "http://www.archive.org/download/" + id + "/" + id + ".pdf";
	
    // return $(this.entry).find('link[type*="application/pdf"]').attr('href');
};

IAPublication.prototype.getKindle = function(){
    return $(this.entry).find('link[type*="application/x-mobipocket-ebook"]').attr('href');
};

IAPublication.prototype.getCover = function(){
    return this.entry.misc.image;
}

IAPublication.prototype.getTitle = function(){
    return arrayToString(this.entry.metadata.title);
}

IAPublication.prototype.getAuthors = function(){
    return arrayToString(this.entry.metadata.creator);
}

IAPublication.prototype.getPublisher = function(){
    return arrayToString(this.entry.metadata.publisher);
}

IAPublication.prototype.getSummary = function() {
	return arrayToString(this.entry.metadata.description);
}

IAPublication.prototype.setEntry = function(entry) {
	this.entry = entry;
};
IAPublication.prototype.hasAudioFile = function(){
    return false;
}

IAPublication.prototype.getAudio = function(){
}
IAPublication.prototype.sameAuthor = function(){
}
IAPublication.prototype.getCategories = function(){
    return arrayToString(this.entry.metadata.subject);
}
IAPublication.prototype.getLanguage = function(){
    return arrayToString(this.entry.metadata.language);
}

IAPublication.prototype.getID = function(){
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

IAPublication.prototype.sameAuthor = function(){
	link = $(this.entry).find('link[type*="application/atom+xml"][title*="From the same author"]').attr('href');
    return (link != null) ? link : "";
}

IAPublication.prototype.alsoDownload = function(){
	link = $(this.entry).find('link[type*="application/atom+xml"][title*="People also downloaded..."]').attr('href');
    return (link != null) ? link : "";
}

IAPublication.prototype.getLanguage = function(){
    return arrayToString(this.entry.metadata.language);
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
PGPublication.prototype.getLanguage = function(){
	var q;
    if ($.browser.mozilla) {
        q = 'dcterms\\:language';
    }else if ($.browser.webkit) {
        q = 'language'
    }
    return $(this.entry).find(q).text();
}
PGPublication.prototype.getRights = function() {
	var q;
    if ($.browser.mozilla) {
        q = 'dc\\:rights';
    }else if ($.browser.webkit) {
        q = 'rights'
    }
	return $(this.entry).find(q).text();
}

var ITPublication = function(){
    Publication.call(this);
}
ITPublication.prototype = new Publication();
ITPublication.prototype.constructor = ITPublication;

ITPublication.prototype.setEntry = function(entry) {
	this.entry = entry;
}

ITPublication.prototype.getEpub = function(){
    return null;
};

ITPublication.prototype.getPdf = function(){
    return null;
};

ITPublication.prototype.getKindle = function(){
    return null;
};

ITPublication.prototype.getCoverThumb = function(){
	return this.entry.artworkUrl60;
}

ITPublication.prototype.getCover = function(){
	return this.entry.artworkUrl100;
}

ITPublication.prototype.getTitle = function(){
    return this.entry.trackName;
}

ITPublication.prototype.getSupportedDevices = function(){
    return arrayToString(this.entry.supportedDevices);
}

ITPublication.prototype.getAuthors = function(){
    return this.entry.artistName;
}

ITPublication.prototype.getScreenshotUrls = function(){
	return this.entry.ipadScreenshotUrls;
}

ITPublication.prototype.getPublisher = function(){
    return this.entry.sellerName;
}

ITPublication.prototype.getSummary = function() {
	return this.entry.description;
}

ITPublication.prototype.setEntry = function(entry) {
	this.entry = entry;
};
ITPublication.prototype.getCategories = function(){
    return arrayToString(this.entry.genres);
}
ITPublication.prototype.getLanguage = function(){
    return arrayToString(this.entry.languageCodesISO2A);
}
ITPublication.prototype.getPrice = function(){
	return this.entry.price;
}
ITPublication.prototype.getVersion = function(){
	return this.entry.version;
}
ITPublication.prototype.getDownloadUrl = function(){
	return this.entry.trackViewUrl;
}
ITPublication.prototype.getSize = function(){
	return this.entry.fileSizeBytes;
}

ITPublication.prototype.getID = function(){
    return this.entry.trackId;
}

ITPublication.prototype.sameAuthor = function(){
	return null;
}

ITPublication.prototype.alsoDownload = function(){
	return null;
}

var ASPublication = function(){
    Publication.call(this);
}
ASPublication.prototype = new Publication();
ASPublication.prototype.constructor = ASPublication;

ASPublication.prototype.setEntry = function(entry) {
	this.entry = entry;
}
ASPublication.prototype.getCoverThumb = function(){
	return $('#dummy').html(this.entry.content).find('img[align*="left"]').attr('src');
}

ASPublication.prototype.getCover = function(){
	return null;
}

ASPublication.prototype.getTitle = function(){
    return $('#dummy').html(this.entry.content).find('h3').text();
}

ASPublication.prototype.getCategories = function(){
    return arrayToString(this.entry.categories);
}
ASPublication.prototype.getPrice = function(){
	var price;
	$('#dummy').html(this.entry.content).find('*').each(function(){
		var tmp = $(this);
		if($(this).text() == "Price:"){
			var next = $(this)[0].nextSibling;
			price = next.textContent;
			return;
		}
	});
	return price;
}

ASPublication.prototype.getVersion = function(){
	return this.entry.version;
}
ASPublication.prototype.getPubDate = function(){
	return this.entry.publishedDate;
}

ASPublication.prototype.getID = function(){
	var tmp = $('#dummy').html(this.entry.content).find('img[align*="left"]').attr('src');
	var array = getNumberStringArray(tmp);
	var str = '';
	if(array){
		$.each(array, function(index, value){
			if(value != '.'){
				str += value;
			}
		});
	}
    var id = parseInt(str);
	return id;    
}

ASPublication.prototype.sameAuthor = function(){
	return null;
}

ASPublication.prototype.alsoDownload = function(){
	return null;
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
Shelf.prototype.getPubID = function(arg){
    return null;
}

var FBShelf = function(args, callback){
	this.setup(args);
    Shelf.call(this, args, callback);
}
FBShelf.prototype = new Shelf();
FBShelf.prototype.constructor = FBShelf;
FBShelf.prototype.setup = function(args){
	var type = args['type'];
	if(type == 'search'){
	    var page = parseInt(args['page']);
	    page++;
	    arg = 'query=' + args['query'] + '&page=' + page;
	    this.url = 'http://www.feedbooks.com/books/search.atom?' + arg;
	}else if(type == 'info'){
		var id = parseInt(args['id']);
	    this.url = 'http://www.feedbooks.com/book/' + id + '/similar.atom';
	}else if(type == 'top'){
	    var page = parseInt(args['page']);
	    page++;
	    arg = 'page=' + page;
	    this.url = 'http://www.feedbooks.com/books/top.atom?' + arg;
	}else if(type == 'recent'){
	    var page = parseInt(args['page']);
	    page++;
	    arg = 'page=' + page;
	    this.url = 'http://www.feedbooks.com/books/recent.atom?' + arg;
	}
}

FBShelf.prototype.getPubTotalCount = function(result){
    xmlDocument = result.xmlDocument;
    
    var q;
    if ($.browser.mozilla) {
        q = 'opensearch\\:totalResults';
    }else if ($.browser.webkit) {
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
	var type = args['type'];
	if(type == 'search'){
	    arg = 'q=' + args['query'] + '&start=' + args['page'];
	    this.url = 'http://bookserver.archive.org/catalog/opensearch?' + arg;
	}else if(type == 'info'){
	}else if(type == 'top'){
	    this.url = 'http://bookserver.archive.org/catalog/downloads.xml';
	}else if(type == 'recent'){
	    this.url = 'http://bookserver.archive.org/catalog/new';
	}	
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
IAShelf.prototype.getPubID = function(pub){
    var idStr = $(pub.entry).find('id').text();
    var id = null;
    if (idStr) {
        id = idStr.split(':')[5];
    }
    return id;
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
		var pubs = new Array();
        
        if (!result.error) {
            entries = result.feed.entries;
            for (var i = 0; i < entries.length; i++) {
                var entry = entries[i];
                xmlNode = entry.xmlNode;

				

                var id = $(xmlNode).find('id').text();
                if (id) {
                    if (id.length > 0) {
                        var tmp = "\.opds";
                        var extension = id.substr(-(tmp.length));
                        if (tmp == extension) {
                            pubIDs.push(id);
			                pub = new PGPublication();
							pub.setEntry(xmlNode);
							pubs.push(pub);
                        }
                    }
                }
            }
            values['pubIDs'] = pubIDs;
            values['pubs'] = pubs;
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
	var type = args['type'];
	if(type == 'search'){
	    var page = parseInt(args['page']);
	    page = (page * 25) + 1;
	    arg = 'default_prefix=all' + '&sort_order=downloads' + '&query=' + args['query'] + '&start_index=' + page;
	    this.url = 'http://www.gutenberg.org/ebooks/search.opds/?' + arg;
	}else if(type == 'info'){
		var id = parseInt(args['id']);
	    this.url = 'http://www.gutenberg.org/ebooks/search.opds/?default_prefix=also_downloaded&query=' + id;
	}else if(type == 'top'){
	    var page = parseInt(args['page']);
	    page = (page * 25) + 1;
	    arg = 'default_prefix=all' + '&sort_order=downloads' + '&start_index=' + page;
	    this.url = 'http://www.gutenberg.org/ebooks/search.opds/?' + arg;
	}else if(type == 'recent'){
	    var page = parseInt(args['page']);
	    page = (page * 25) + 1;
	    arg = 'default_prefix=all' + '&sort_order=release_date' + '&start_index=' + page;
	    this.url = 'http://www.gutenberg.org/ebooks/search.opds/?' + arg;
	}
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
PGShelf.prototype.getPubID = function(pub){
    var idStr = $(pub.entry).find('id').text();
    var id = null;
    if (idStr) {
        id = idStr.split(':')[2];
    }
    return id;
}

var ITShelf = function(args, callback){
   	this.setup(args);
	// Shelf.call(this, args, callback);
	this.feedLoad = function(callback){
		this.callback = callback;
		$.ajax({
	        type: "get",
	        dataType: "jsonp",
	        url: this.url,
	        success: function(data){
				clog(data);
				if(data){
					var values = new Array();
					var pubs = new Array();
					for( x in data.results ){
		                var pub = new ITPublication();
						pub.setEntry(data.results[x]);
						pubs.push(pub);
					}
					values['pubs'] = pubs;
					callback(values);
				}
	        },
	        error: function(){
	            clog("error : " + this.url);
	        }
	    });
	}
}

ITShelf.prototype.constructor = ITShelf;
ITShelf.prototype.setup = function(args){
    arg = 'term=' + args['query'] + '&entity=' + args['entity'];
    this.url = 'http://itunes.apple.com/search?' + arg + '&limit=50';
}
ITShelf.prototype.getPubID = function(pub){
    return pub.getID();
}
ITShelf.prototype.getPubTotalCount = function(val){
	return val;
}

// AppShopper
var ASShelf = function(args, callback){
	this.callback = callback;
   	this.setup(args);
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
        
        if (result) {
			var entries = result.feed.entries;
			for( i=0; i<entries.length; i++){
				x = jQuery.inArray("Books", entries[i].categories);
				// y = jQuery.inArray("Education", entries[i].categories);
				if(x > -1){
				    pub = new ASPublication();
					pub.setEntry(entries[i]);
				    pubs.push(pub);
				}
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
}
ASShelf.prototype.constructor = ASShelf;
ASShelf.prototype.setup = function(args){
	var type = args['type'];
	switch(type){
		case AS_TYPE.PD:
			this.url = 'http://appshopper.com/feed/?device=iPad&filter=price';
			break;
		case AS_TYPE.GF:
			this.url = 'http://appshopper.com/feed/paidtofree/?device=iPad';
			break;
		case AS_TYPE.PPD:
			this.url = 'http://appshopper.com/feed/?device=iPad&mode=featured&filter=price';
			break;
	}
}
ASShelf.prototype.getPubID = function(pub){
    return pub.getID();
}

var Feeder = function(val){
    this.url;
    this.setUrl(val);
}

Feeder.prototype.setUrl = function(val){

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

var IAFeeder = function(id){
    Feeder.call(this, id);
    
}
IAFeeder.prototype = new Feeder();
IAFeeder.prototype.constructor = Feeder;
IAFeeder.prototype.setUrl = function(id){
	this.url = "http://www.archive.org/details/" + id + "&output=json"
}
IAFeeder.prototype.feedLoad = function(callback){
	$.ajax({
        type: "get",
        dataType: "jsonp",
        url: this.url,
        success: function(data){
			clog(data);
			if(data){
                var pub = new IAPublication();
				pub.setEntry(data);
				callback(pub);
			}
        },
        error: function(){
            clog("error : " + this.url);
        }
    });
}

var PGFeeder = function(id){
	
    Feeder.call(this, id);
}
PGFeeder.prototype = new Feeder();
PGFeeder.prototype.constructor = Feeder;
PGFeeder.prototype.setUrl = function(id){
	this.url = "http://www.gutenberg.org/ebooks/" + id + ".opds"
}
PGFeeder.prototype.feedLoad = function(callback){
	PGFeeder.callback = callback;

    var feed = new google.feeds.Feed(this.url);
    feed.setResultFormat(google.feeds.Feed.MIXED_FORMAT);
    feed.includeHistoricalEntries();
    feed.setNumEntries(250);
    feed.load($.proxy(this, this.feedLoaded));
}

PGFeeder.prototype.feedLoaded = function(result){
	var pub;
    if (!result.error) {
		var entries = result.feed.entries;
        clog("entries = " + entries.length);
        for (var i = 0; i < entries.length; i++) {
			var entry = entries[i];
            pub = new PGPublication();
			pub.setEntry(entry.xmlNode);
         }
    }
    PGFeeder.callback(pub);
}

// iTunes
var ITFeeder = function(id){
    Feeder.call(this, id);
    
}
ITFeeder.prototype = new Feeder();
ITFeeder.prototype.constructor = Feeder;
ITFeeder.prototype.setUrl = function(id){
    this.url = "http://itunes.apple.com/lookup?id=" + id;
}
ITFeeder.prototype.feedLoad = function(callback){
	$.ajax({
        type: "get",
        dataType: "jsonp",
        url: this.url,
        success: function(data){
			clog(data);
			if(data){
                var pub = new ITPublication();
				pub.setEntry(data.results[0]);
				callback(pub);
			}
        },
        error: function(){
            clog("error : " + this.url);
        }
    });
}



