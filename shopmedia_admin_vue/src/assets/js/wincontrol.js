
//实时获取浏览器窗口大小，当窗口大小变化触发相应事件
let winWidth = 0;
let winHeight = 0;
function wincontrol() 
{
     //获取窗口宽度
    if (window.innerWidth)
    winWidth = window.innerWidth;
    else if ((document.body) && (document.body.clientWidth))
    winWidth = document.body.clientWidth;
    //获取窗口高度
    if (window.innerHeight)
     winHeight = window.innerHeight;
    else if ((document.body) && (document.body.clientHeight))
     winHeight = document.body.clientHeight;
     //通过深入Document内部对body进行检测，获取窗口大小
    if (document.documentElement  && document.documentElement.clientHeight && document.documentElement.clientWidth)
     {
     winHeight = document.documentElement.clientHeight;
     winWidth = document.documentElement.clientWidth;
     }
		
	//根据窗口变化调整网页各版块高度
    let header_height=$(".hometitle").height(); //头部高度
	let right_title_height=$(".createtitle").height(); //右侧标题高度
   
	$(".homemain").height(winHeight-header_height-right_title_height);//控制右侧内容区的高度

}
    

window.onresize=wincontrol;
export default{
  wincontrol
}
//实时获取浏览器窗口大小并调整文本图片代码结束




	

