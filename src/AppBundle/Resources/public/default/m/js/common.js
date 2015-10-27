//找到url中匹配的字符串
function findInUrl(str){
	url = location.href;
	return url.indexOf(str) == -1 ? false : true;
}
//获取url参数
function queryString(key){
    return (document.location.search.match(new RegExp("(?:^\\?|&)"+key+"=(.*?)(?=&|$)"))||['',null])[1];
}

//产生指定范围的随机数
function randomNumb(minNumb,maxNumb){
	var rn=Math.round(Math.random()*(maxNumb-minNumb)+minNumb);
	return rn;
	}
	
function closePop(){
	$('.popBg').hide();
	$('.pop').hide();
	}
	
var wHeight;
$(document).ready(function(){
	window.addEventListener('touchmove', function (e) { e.preventDefault();e.stopPropagation(); }, false);
	
	wHeight=$(window).height();
	if(wHeight<832){
		wHeight=832;
		}
	var sBl=wHeight/1130;
	$('.page').css('-webkit-transform','translate(0,'+(-(1130-(1130*sBl))/2)+'px'+') scale('+sBl+')');
	});