function sn_scramble(code,key,link){
	shift=code.length;
	linkstr=""	
	for (i=0; i<code.length; i++) {
		if (key.indexOf(code.charAt(i))==-1) {
			ltr = code.charAt(i);
			linkstr += (ltr);
	} else {     
	ltr = (key.indexOf(code.charAt(i))-shift+key.length) % key.length;
	linkstr += (key.charAt(ltr));
	}
	}
linkstr = linkstr.replace("hack2dirty", "-");
if (link){
document.write("<a href='mailto:"+linkstr+"'>"+linkstr+"</a>");
}else{
document.write(linkstr);
}
}
