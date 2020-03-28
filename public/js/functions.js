/**
 * 这里是公共js函数调用库(2018/07/23)
 * 
 */


// 计算日期天数差
function DiffInNumOfDate(date1, date2) { //date1:小日期   date2:大日期
　　var ddate1 = Date.parse(date1); 
　　var ddate2 = Date.parse(date2); 
　　var days = Math.floor((ddate2 - ddate1) / (1000 * 60 * 60 * 24)); 
　　return days; 
} 


// 开始提醒日期
var dateofcurrent = new Date();
var dateofhint = new Date('2020-08-10 23:59:59');
var dateofsetup = new Date('2020-10-10 23:59:59');

// alert(DiffInNumOfDate(dateofcurrent, dateofhint));

if (dateofcurrent >= dateofhint) {
    var d = DiffInNumOfDate(dateofcurrent, dateofsetup);
    if (d > 0) {
        alert(
            '警告！系统框架和组件将于' + d + '天后过期，请尽快升级以免影响使用！\n\nWarning! The system framework and components will exceed the time limit after ' + d + ' days!'
        );
    }
}


// 判断PC端还是移动端
var isMobile = false;//默认PC端
function mobile() {
    try{
        document.createEvent("TouchEvent");
        return true;
    }
    catch(e){
        return false;
    }
}
isMobile = mobile();
// console.log(isMobile);
if (isMobile) {
	// alert('仅有付费系统组件支持移动端！');
	// document.execCommand("Stop");
    // window.stop();
}
 
// 给日期类对象添加日期差方法，返回日期与diff参数日期的时间差，单位为天
// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
// 例子：
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
// let time1 = new Date().Format("yyyy-MM-dd");
// let time2 = new Date().Format("yyyy-MM-dd HH:mm:ss");
 
Date.prototype.Format = function (fmt) { //author: meizz
    let o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (let k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};

// 201810141449
// 判断浏览器，IE或Edge不让使用
function checkBrowser () {
	var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
	// console.log(userAgent);
	var isFirefox = userAgent.indexOf("Firefox") > -1;
	var isChrome = userAgent.indexOf("Chrome") > -1;
	var isSafari = userAgent.indexOf("Safari") > -1;
	var isOpera = userAgent.indexOf("Opera") > -1 || userAgent.indexOf("OPR") > -1;
	if (isOpera) {
		// alert("Opera");
	}; //判断是否Firefox浏览器
	if (isFirefox) {
		// alert("FF");
	} //判断是否Chrome浏览器
	else if (isChrome){
		// alert("Chrome");
	} //判断是否Safari浏览器
	else if (isSafari) {
		// alert("Safari");
	}
	// if (userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1 && userAgent.indexOf(".NET") > -1 && !isOpera) {
	//判断是否IE浏览器
	else {
		// alert("IE");
		alert('请使用Chrome或Firefox浏览器！');
		document.execCommand("stop");
	};	
}

// 201812301547
// 获取某个月份的天数 例：getDays(2018-12)
function getDays (yearmonth) {
	var arr = yearmonth.split('-');
    // var d = new Date(year, month, 0);
    var d = new Date(arr[0], arr[1], 0); //初始化月份的第0天，由于JS中day的范围为1~31中的值，所以当设为0时，会向前一天，也即表示上个月的最后一天。
    return d.getDate();
}

// 201902151015
/*
 *   功能:实现VBScript的DateAdd功能.
 *   参数:interval,字符串表达式，表示要添加的时间间隔.
 *   参数:number,数值表达式，表示要添加的时间间隔的个数.
 *   参数:date,时间对象.
 *   返回:新的时间对象.
 *   var now = new Date();
 *   var newDate = DateAdd( "d", 5, now);
 *---------------   DateAdd(interval,number,date)   -----------------
 */
function DateAdd(interval, number, date) {
    switch (interval) {
    case "y ": {
        date.setFullYear(date.getFullYear() + number);
        return date;
        break;
    }
    case "q ": {
        date.setMonth(date.getMonth() + number * 3);
        return date;
        break;
    }
    case "m ": {
        date.setMonth(date.getMonth() + number);
        return date;
        break;
    }
    case "w ": {
        date.setDate(date.getDate() + number * 7);
        return date;
        break;
    }
    case "d ": {
        date.setDate(date.getDate() + number);
        return date;
        break;
    }
    case "h ": {
        date.setHours(date.getHours() + number);
        return date;
        break;
    }
    case "m ": {
        date.setMinutes(date.getMinutes() + number);
        return date;
        break;
    }
    case "s ": {
        date.setSeconds(date.getSeconds() + number);
        return date;
        break;
    }
    default: {
        date.setDate(d.getDate() + number);
        return date;
        break;
    }
    }
}


// 201911091107
// 切换全屏
function handleFullScreen () {
    let element = document.documentElement;
    // 判断是否已经是全屏
    // 如果是全屏，退出
    if (document.isFullScreen || document.mozFullScreen || document.webkitIsFullScreen) {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        vm_app.isfullscreen = false;
    } else {    // 否则，进入全屏
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.webkitRequestFullScreen) {
            element.webkitRequestFullScreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.msRequestFullscreen) {
            // IE11
            element.msRequestFullscreen();
        }
        vm_app.isfullscreen = true;
    }
    // 改变当前全屏状态
    // vm_app.isfullscreen = !vm_app.isfullscreen;
}

// 202001051717
/*
 *   功能:实时获取当前日期时间.
 *   参数:元素ID.
 */
function GetCurrentDatetime(id) {
    var element_id = document.getElementById(id);

    setInterval(function(){
        var time = new Date();
        var m = time.getMonth()+1; //程序计时的月从0开始取值后+1
        var t = time.getFullYear() + '-' + m + '-'
        + time.getDate() + ' ' + time.getHours() + ':'
        + time.getMinutes() + ':' + time.getSeconds();
        element_id.innerHTML = t;
    }, 1000);

}

