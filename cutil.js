DEBUG = true; // set to false to disable debugging
function clog(argument){
    if (DEBUG) {
        console.log(argument);
    }
}

function getNumberStringArray(str){
	return str.match(/[\d\.]+/g);
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
