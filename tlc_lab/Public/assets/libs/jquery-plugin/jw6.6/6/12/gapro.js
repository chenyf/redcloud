(function(l){var r="gapro";l().registerPlugin(r,"6.0",function(g,u){function j(){return"string"==typeof a.trackingobject?window[a.trackingobject]:a.trackingobject}function v(c,b){if(void 0==c)return"";if(void 0!=b&&void 0!=c[b]&&0<c[b].length)return"file"==b?s(c[b]):c[b];if(void 0==c.file&&c.sources){var a=c.sources,e=[],d;for(d=a.length-1;0<=d;d--)a[d].file&&e.push(a[d].file);e.sort();return s(e[0])}return void 0!=c.file?c.file:""}function s(a,b){var f=a.indexOf("://"),e=a.indexOf("?");if(0<f&&(0>
e||e>f))return a;b||(b=document.location.href);var f=b.substring(0,b.indexOf("://")+3),e=b.substring(f.length,b.indexOf("/",f.length+1)),d;0===a.indexOf("/")?d=a.split("/"):(d=b.split("?")[0],d=d.substring(f.length+e.length+1,d.lastIndexOf("/")),d=d.split("/").concat(a.split("/")));for(var g=[],h=0;h<d.length;h++)d[h]&&"."!=d[h]&&(".."==d[h]?g.pop():g.push(d[h]));return f+e+"/"+g.join("/")}function t(c){var b="Not tracked";if(a.currentItem.hidden)a.currentItem.hidden&&(b+=" - current item is hidden");
else if("undefined"!=typeof j()._trackEvent){var b="Tracked Synchronously",f=a.currentItem.mediaID,e=a.pageURL;j()._trackEvent(c,f,e)}else"undefined"!=typeof j().push&&(b="Tracked Asynchronously",f=a.currentItem.mediaID,e=a.pageURL,j().push(["_trackEvent",c,f,e]));m(b,{Category:c,Action:a.currentItem.mediaID,Label:a.pageURL})}function m(c,b){a.debug&&l.utils.log(r+": "+c,b)}var a=l.utils.extend({},{trackingobject:"_gaq",idstring:"file"},u);m("Initializing");if("string"==typeof a.trackingobject&&"undefined"==
typeof window[a.trackingobject])m("Could not setup because trackingobject is not defined.");else{var k=!1,n="",p=!1,q;g.onPlaylistItem(function(c){k=!0;a.pageURL=window.top==window?window.location.href:document.referrer;a.currentItem={};q=g.getPlaylistItem(c.index);a.currentItem.mediaID=n;n=v(q,a.idstring);a.currentItem.hidden=p;p=q["ova.hidden"]?!0:!1});g.onIdle(function(){k=!0});g.onPlay(function(){k&&(k=!1,a.currentItem.mediaID=n,a.currentItem.hidden=p,t("JW Player Video Plays"))});g.onComplete(function(){t("JW Player Video Completes")})}})})(jwplayer);