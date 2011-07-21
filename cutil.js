DEBUG = false; // set to false to disable debugging
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

function arrayToString(array){
	var str = '';
	if(array){
		$.each(array, function(index, value){
			str += value + ' ';
		});
	}
	return str;
}

function checkBrowser() {
	if($.browser.msie==true) {
 		alert('Internet Explorer is not supported. Please use others(Chrome, Firefox, Safari, Opera ...)');
		window.location = "/";
	}
}
