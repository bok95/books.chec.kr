/*
*  How to see historical entries in a feed.  Usually a feed only returns x number
*  of results, and you want more.  Since the Google Feeds API caches feeds, you can
*  dig into the history of entries that it has cached.  This, paired with setNumEntries,
*  allows you to get more entries than normally possible.
*/



// Our callback function, for when a feed is loaded.
function feedLoaded(result) {
  if (!result.error) {
    // Grab the container we will put the results into
    var container = document.getElementById("content");
    container.innerHTML = '';

    // Loop through the feeds, putting the titles onto the page.
    // Check out the result object for a list of properties returned in each entry.
    // http://code.google.com/apis/ajaxfeeds/documentation/reference.html#JSON
    for (var i = 0; i < result.feed.entries.length; i++) {
      var entry = result.feed.entries[i];
      var div = document.createElement("div");
      div.appendChild(document.createTextNode(i + ': ' + entry.title));
      container.appendChild(div);
    }
  }
}

function OnLoad() {
  // Create a feed instance that will grab Digg's feed.
  var feed = new google.feeds.Feed("http://bookserver.archive.org/catalog/opensearch?q=alice");

  feed.includeHistoricalEntries(); // tell the API we want to have old entries too
  feed.setNumEntries(250); // we want a maximum of 250 entries, if they exist

  // Calling load sends the request off.  It requires a callback function.
  feed.load(feedLoaded);
}

