
var Publication = function() {
		
}

var Feed = function(){};
Feed.prototype.load = function(callback){
	var result = "result";
	callback(result);
}

var Shelf = function(args, callback){
	this.callback = callback;
	this.url;
	this.feed = new Feed();
	var totalResultCount;
};

Shelf.prototype.setCallback = function(callback) {
	this.showShelf = callback;
}
Shelf.prototype.feedLoad = function(){
	console.log(this.url);
	this.feed.load($.proxy(this, this.feedLoaded));
};

Shelf.prototype.feedLoaded = function(result){
	totalResultCount = 10;
	callback();
	//console.log(result);
};

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
IAShelf.prototype.setTotalResultCount = function(count) {
	totalResultCount = count;
}
IAShelf.prototype.getTotalResultCount = function() {
	return totalResultCount;
}


//
//FBShelf.prototype.constructor = function(callback, query, page){
//	this.args = 'query=' + q + '&page=' + page;
//   	this.url = 'http://www.feedbooks.com/books/search.atom?' + args;
//}
//
//var IAShelf = new Shelf(callback, query, page);
//





