var IA = function(feed){
    this.feed = feed;
	this.pageInfo = this.feed.title.match(/[\d\.]+/g); 
	if(pageInfo){
		this.total_page = pageInfo[2];
	}
    
	
}
