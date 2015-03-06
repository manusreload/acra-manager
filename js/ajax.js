function pasrseResult(statusCode, data, opts) {
    if(opts.loading!= null) {
        document.getElementById(opts.loading).style.display = "none";
    }
    if(statusCode == 200) {
        document.getElementById(opts.content).innerHTML = data.responseText;
    } else {
        document.getElementById(opts.error).style.display = "inline";
    }
    if(typeof opts.onLoad == "function") {
        opts.onLoad();
    }
    
}
var default_options = {
    loading: "loading-ajax",
    content: "content-ajax",
    error: "content-error",
    url: "test.php"
};
/**
 * usagre:
 *  {
 *      loading: div show while loading,
 *      content: div where put data,
 *      error: div show when error,
 *      url: url to load
 *  }
 */
function ajaxLoad(options) {
    if(options.loading!= null) {
        document.getElementById(options.content).innerHTML = "<div id=\"" + options.loading + "\"><center><img src=\"img/loading.gif\" /></center></div>"
        document.getElementById(options.loading).style.display = "inline";
    }
    var xmlhttp;
    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4) {
            pasrseResult(xmlhttp.status,xmlhttp,options)
        }
    };
    xmlhttp.open("GET",options.url,true);
    return xmlhttp.send();
}