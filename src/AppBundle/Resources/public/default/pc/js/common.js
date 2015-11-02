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
	
function showLogin(){
	$('.popBg').show();
	$('.pop').hide();
	$('.loginBlock').show();
	}
	
function login(url){
	var lName=$.trim($('.loginTxt1').val());
	var lPwd=$.trim($('.loginTxt2').val());
	if(lName==''||lPwd==''){
		alert('请输入登入信息');
		return;
		}
		else{
			$.post(url,{mobile:lName,password:lPwd},function(json){
				if(json && json.ret == 0){
					closePop();
					$('.nameBlock').html('用户名：'+json.nickname);
					$('.rightInto').html('用户名：'+ json.nickname);
				}
				else{
					alert(json.msg)
				}
			},"JSON")
		}
	}
	
var canGetYzm=true;
function getYzm(url){
	var rTel=$.trim($('.loginTxt3').val());
	var regTel=/^1[3456789]\d{9}$/;
	if(!regTel.test(rTel)||rTel==''){
		alert('请输入正确的手机号码');
		return;
		}
		else{
			if(canGetYzm){
				$.post(url,{mobile:rTel},function(json){
					if(json && json.ret == 0){
						canGetYzm=false;
						//请求获取验证码
						alert('获取成功，请注意查收您的短信');
					}
					else{
						alert(json.msg);
					}
				},"JSON");
			}
			else{
				alert('请稍后再获取验证码');
			}
		}
	}
	
function register(url){
	var rTel=$.trim($('.loginTxt3').val());
	var rYzm=$.trim($('.loginTxt4').val());
	var rPwd1=$.trim($('.loginTxt5').val());
	var rPwd2=$.trim($('.loginTxt6').val());
	var rName=$.trim($('.loginTxt7').val());
	var regTel=/^1[3456789]\d{9}$/;
	
	if(rTel==''||rYzm==''||rPwd1==''||rPwd2==''||rName==''){
		alert('请输入完整信息');
		return;
		}
		else if(!regTel.test(rTel)){
			alert('请输入正确的手机号码');
			return;
			}
			else if(rPwd1!=rPwd2){
				alert('两次密码输入不一致');
				return;
			}
			else{
				$.post(url,{mobile:rTel,password:rPwd1,nickname:rName,secode:rYzm},function(json){
					if(json.ret == 0){
						closePop();
						alert('注册成功');
						$('.nameBlock').html('用户名：'+json.nickname);
						$('.rightInto').html('用户名：'+json.nickname);
					}
					else{
						alert(json.msg);
					}
				},"JSON");
				
			}
	}
	
function showNav(){
	$('.openNav').stop().show().animate({width:980},500,'linear');
	}
	
function closeNav(){
	$('.openNav').stop().show().animate({width:0},500,'linear',function(){
		$('.openNav').hide();
		});
	}
	

$(document).ready(function(){
	$('.nav5').mouseover(function(){
		$('.navQc').show();
		});
	$('.navQc').mouseout(function(){
		$('.navQc').hide();
		});
	});
	
function showRule(){
	$('.popBg').show();
	$('.pop').hide();
	$('.rule').show();
	}
	
function showTmall(){
	$('.popBg').show();
	$('.pop').hide();
	$('.tmall').show();
	}
	
//分页
function getPagelist(pagecount, curpage, objectid)
{
	var pagehtml = "";
	var cnum = 0;
	var bnum = 0;
	var onum = 0;
	if(curpage <= 0)
		curpage = 1;
	if(curpage >= pagecount)
		curpage = pagecount;
	
	if(curpage!=1)
		pagehtml += '<a href="javascript:void(0)" class="pre" onclick="setPage(' + pagecount + ',' + (curpage-1) + ',\'' + objectid + '\')">&nbsp;</a>';
	if(curpage<=4)
	{		
		for(var i=1;i<=4;i++)
		{
			pagehtml += '<a href="javascript:void(0)"' + (curpage == i ? ' class="cur"' : '') + ' onclick="setPage(' + pagecount + ',' + i + ',\'' + objectid + '\')">' + i + '</a>';
			cnum++;
			if(cnum>=pagecount)
				break;
		}
		if(pagecount>4)
		{
			if((pagecount-cnum)>1)
				pagehtml += "...";
			pagehtml += '<a href="javascript:void(0)" onclick="setPage(' + pagecount + ',' + pagecount + ',\'' + objectid + '\')">' + pagecount + '</a>';
		}
	}
	else
	{
		pagehtml += '<a href="javascript:void(0)"' + (curpage == i ? ' class="cur"' : '') + ' onclick="setPage(' + pagecount + ',1,\'' + objectid + '\')">1</a>';
		cnum++;
		bnum = curpage-1;
		onum = curpage+1;
		if(bnum <= cnum)
			bnum = cnum+1;
		if(onum > pagecount)
			onum = pagecount;
		if(bnum>2)
			pagehtml += "...";
		for(var i=bnum; i<=onum; i++)
		{
			pagehtml += '<a href="javascript:void(0)"' + (curpage == i ? ' class="cur"' : '') + ' onclick="setPage(' + pagecount + ',' + i + ',\'' + objectid + '\')">' + i + '</a>';
			cnum++;
		}
		if(pagecount>onum)
		{
			if((pagecount-onum)>1)
				pagehtml += "...";
			pagehtml += '<a href="javascript:void(0)" onclick="setPage(' + pagecount + ',' + pagecount + ',\'' + objectid + '\')">' + pagecount + '</a>';
		}
	}	
	if(curpage!=pagecount)
		pagehtml += '<a href="javascript:void(0)" class="next" onclick="setPage(' + pagecount + ',' + (curpage+1) + ',\'' + objectid + '\')">&nbsp;</a>';
	$("#"+objectid).html(pagehtml);
}

function setPage(pagecount, curpage, objectid)
{
	if(curpage <= 0)
		curpage = 1;
	if(curpage >= pagecount)
	    curpage = pagecount;
		
		
	//ajax动态加载新闻内容
	/*$.ajax({
	    method: 'post',
	    url: webUrl+'ashx/GetNews.ashx',
	    data: { Current: curpage },
	    dataType: 'json',
	    cache: false,
	    success: function (data) {
	        var list = "<ul>";
	        for (var i = 0; i < data.dt.length; i++) {
	            if (data.dt[i]["IsBlank"] == "1") {
	                list += "<li class=\"cline1\"><a href=\"" + data.dt[i]["Link"] + "\" target=\"_blank\">" + data.dt[i]["Title"] + "</a></li>";
	            }
	            else {
	                list += "<li class=\"cline1\"><a href=\"javascript:getNews(" + data.dt[i]["NewsCenterID"] + ")\">" + data.dt[i]["Title"] + "</a></li>";
	            }
	        }
	        list += "</ul>";
	        $("#list").html(list);
	    },
	    error: function (errobj, status, msg) {
	        alert(status + "\r\n" + msg);
	    }
	});*/
	getPagelist(pagecount, curpage, objectid);
}
