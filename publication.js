
var Publication = function (entry) {
	this.entry = entry;
	this.node = entry.xmlNode;
	// getCover : function () {
	// 	$(this.node).find('link[type*="image/jpeg"][rel*="thumbnail"]').each(function(){
	// 		console.log($(this));
	// 	});
	// },
	this.getEpub = function () {
		epub_url = $(this.node).find('link[type*="application/epub+zip"]').attr('href');
		console.log(epub_url);
		return epub_url;
	};
	
	this.getPdf = function () {
		epub_url = $(this.node).find('link[type*="application/pdf"]').attr('href');
		console.log(epub_url);
		return epub_url;
	};
	
	this.getCover = function () {
		cover_url = $(this.node).find('link[type*="image/jpeg"][rel*="thumbnail"]').attr('href');
		console.log(cover_url);
		return cover_url;
	}
	
	this.getCategories = function () {
		categories = new Array();
	 	$(this.node).find('category').each(function() {
			categories.push($(this).attr('term'));
		});
		return categories;
	}
	
	this.getTitle = function() {
		return $(this.node).find('title').text();
	}
	
	this.getAuthors = function() {
		authors = new Array();
		$(this.node).find('author').each(function() {
			authors.push($(this).text());
		});
		return authors;
	}

	this.getPublisher = function() {
		return publisher = $(this.node).find("[*|publisher]").text();
	}
	
	
}
