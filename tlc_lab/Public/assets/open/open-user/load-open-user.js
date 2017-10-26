;(function(){
	var ver="1.0.1";
	var loadScript;
	var scripts=document.getElementsByTagName("script");
	var reg=/\/Public\/assets\/open\/open-user\/load-open-user.js/i;
        var debugUrl;
	
	for(var i=0,script,l=scripts.length;i<l;i++)
	{
		script=scripts[i];
		var src=script.src||"";
		var mat=src.match(reg);
		if(mat){loadScript=script;break;}
	}
	for(var i=0,att;i<loadScript.attributes.length;i++)
        {
            att=loadScript.attributes[i];
            if(att.name=="debug_url"){
                debugUrl = att.value;
            }
        }
	var jq_src = 'http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js';
        
        var openUser_url = debugUrl ? debugUrl : 'https:\/\/oauth.gkk.com';
        
	var openUser_src = openUser_url+'/Public/assets/open/open-user/open-user.'+ver+'.js';
	
	if(document.readyState!='complete'){
		window.jQuery || document.write(unescape('%3Cscript%20type%3D%22text/javascript%22%20src%3D%22'+jq_src+'%22%3E%3C/script%3E'));
		document.write(unescape('%3Cscript%20type%3D%22text/javascript%22%20src%3D%22'+openUser_src+'%22%3E%3C/script%3E'));
	}else{
		var s=document.createElement("script"),attr;
		s.type="text/javascript";
		s.src=jq_src;
		
		var _s=document.createElement("script"),attr;
		_s.type="text/javascript";
		_s.src=openUser_src;
		
		var h=document.getElementsByTagName("head");
		if(h&&h[0]){
			h[0].appendChild(s);
			h[0].appendChild(_s);
		}
	}
	
})();
