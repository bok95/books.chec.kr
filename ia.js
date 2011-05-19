var IA = function(feed){
    this.feed = feed;
	_pageInfo = this.feed.title.match(/[\d\.]+/g); 
	if(_pageInfo){
		this.total_result_count = _pageInfo[2];
	}
    
	this.getResultCount = function () {
		count = this.total_result_count;
		return (count != null) ? count : -1;
	};
	
}
