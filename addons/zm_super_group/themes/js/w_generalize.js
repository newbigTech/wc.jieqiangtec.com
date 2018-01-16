var oFm = document.poster_form;
var oP = $('#set-poster')[0];
var timer = null;
var sw = oP.getBoundingClientRect().width;
var sh = oP.getBoundingClientRect().height;

/**
 * max值设置
 */
oFm.q_x.max = sw - $('#p-qrcode')[0].getBoundingClientRect().width;
oFm.q_y.max = sh - $('#p-qrcode')[0].getBoundingClientRect().height;

oFm.h_x.max = sw - $('#p-header')[0].getBoundingClientRect().width;
oFm.h_y.max = sh - $('#p-header')[0].getBoundingClientRect().height;

oFm.n_x.max = sw - $('#p-nickname')[0].getBoundingClientRect().width;
oFm.n_y.max = sh - $('#p-nickname')[0].getBoundingClientRect().height;

oFm.t_x.max = sw - $('#p-title')[0].getBoundingClientRect().width;
oFm.t_y.max = sh - $('#p-title')[0].getBoundingClientRect().height;

oFm.c_x.max = sw - $('#p-cover')[0].getBoundingClientRect().width;
oFm.c_y.max = sh - $('#p-cover')[0].getBoundingClientRect().height;


timer = setInterval(function(){
	if(oFm.poster_bg.value != ''){  
		$('#set-poster')[0].style.backgroundImage = 'url('+$('.img-thumbnail')[0].src+')';
	}else{
		$('#set-poster')[0].style.backgroundImage = '';
	}
},300);

oFm.q_x.oninput = function(){
	var left = this.value;
	if(left >= oP.offsetWidth - $('#p-qrcode')[0].offsetWidth){
		left = oP.offsetWidth - $('#p-qrcode')[0].offsetWidth - 4;
		this.value = oP.offsetWidth - $('#p-qrcode')[0].offsetWidth;
	}
	console.log(this.value+','+oFm.q_y.value);
	$('#p-qrcode')[0].style.left = left + 'px';
}


oFm.q_y.oninput = function(){
	var top = this.value;
	if(top >= oP.offsetHeight - $('#p-qrcode')[0].offsetHeight){
		top = oP.offsetHeight - $('#p-qrcode')[0].offsetHeight - 4;
		this.value = oP.offsetHeight - $('#p-qrcode')[0].offsetHeight;
	}
	console.log(this.value+','+oFm.q_x.value);
	$('#p-qrcode')[0].style.top = top + 'px';
}

oFm.q_z.oninput = function(){
	var size = this.value;
	if(size >= oP.offsetWidth - $('#p-qrcode')[0].offsetLeft){
		size = oP.offsetWidth-2-$('#p-qrcode')[0].offsetLeft;
		this.value = oP.offsetWidth-2-$('#p-qrcode')[0].offsetLeft;
	}
	if(size >= oP.offsetHeight - $('#p-qrcode')[0].offsetTop){
		size = oP.offsetHeight - 2 - $('#p-qrcode')[0].offsetTop;
		this.value = oP.offsetHeight - 2 - $('#p-qrcode')[0].offsetTop;
	}
	$('#p-qrcode')[0].style.width = size + 'px';
	$('#p-qrcode')[0].style.height = size + 'px';
	oFm.q_x.max = sw - size;
	oFm.q_y.max = sh - size;
}



$('#poster-option button')[0].onclick = function(){
	if(this.getAttribute('data-status') == 0){
		this.className = 'btn btn-success';
		$('#p-header')[0].style.display = 'block'; 
		this.setAttribute('data-status',1);
		oFm.h_show.value = 1; 
	}else{
		$('#p-header')[0].style.display = 'none';
		this.className = 'btn btn-default';
		this.setAttribute('data-status',0);
		oFm.h_show.value = 0;
	}
}


oFm.h_x.oninput = function(){
	var left = this.value;
	if(left >= oP.offsetWidth - $('#p-header')[0].offsetWidth){
		left = oP.offsetWidth - $('#p-header')[0].offsetWidth - 4;
		this.value = oP.offsetWidth - $('#p-header')[0].offsetWidth;
	}
	$('#p-header')[0].style.left = left + 'px';
}


oFm.h_y.oninput = function(){
	var top = this.value;
	if(top >= oP.offsetHeight - $('#p-header')[0].offsetHeight){
		top = oP.offsetHeight - $('#p-header')[0].offsetHeight - 4;
		this.value = oP.offsetHeight - $('#p-header')[0].offsetHeight;
	}
	$('#p-header')[0].style.top = top + 'px';
}

oFm.h_z.oninput = function(){
	var size = this.value;
	if(size >= oP.offsetWidth - $('#p-header')[0].offsetLeft){
		size = oP.offsetWidth- 2 -$('#p-header')[0].offsetLeft;
		this.value = oP.offsetWidth-2-$('#p-header')[0].offsetLeft;
	}
	if(size >= oP.offsetHeight - $('#p-header')[0].offsetTop){
		size = oP.offsetHeight - 2 - $('#p-header')[0].offsetTop;
		this.value = oP.offsetHeight - 2 - $('#p-header')[0].offsetTop;
	}
	$('#p-header')[0].style.width = size + 'px';
	$('#p-header')[0].style.height = size + 'px';
	
	oFm.h_x.max = sw - size;
	oFm.h_y.max = sh - size;
}


$('#poster-option button')[3].onclick = function(){
	if(this.getAttribute('data-status') == 0){
		this.className = 'btn btn-success';
		$('#p-cover')[0].style.display = 'block'; 
		this.setAttribute('data-status',1);
		oFm.c_show.value = 1; 
	}else{
		$('#p-cover')[0].style.display = 'none';
		this.className = 'btn btn-default';
		this.setAttribute('data-status',0);
		oFm.c_show.value = 0;
	}
}

oFm.c_x.oninput = function(){
	var left = this.value;
	if(left >= oP.offsetWidth - $('#p-cover')[0].offsetWidth){
		left = oP.offsetWidth - $('#p-cover')[0].offsetWidth - 4;
		this.value = oP.offsetWidth - $('#p-cover')[0].offsetWidth;
	}
	$('#p-cover')[0].style.left = left + 'px';
}


oFm.c_y.oninput = function(){
	var top = this.value;
	if(top >= oP.offsetHeight - $('#p-cover')[0].offsetHeight){
		top = oP.offsetHeight - $('#p-cover')[0].offsetHeight - 4;
		this.value = oP.offsetHeight - $('#p-cover')[0].offsetHeight;
	}
	$('#p-cover')[0].style.top = top + 'px';
}

oFm.c_w.oninput = function(){
	var size = this.value;
	if(size >= oP.offsetWidth - $('#p-cover')[0].offsetLeft){
		size = oP.offsetWidth- 2 -$('#p-cover')[0].offsetLeft;
		this.value = oP.offsetWidth-2-$('#p-cover')[0].offsetLeft;
	}
	if(size >= oP.offsetHeight - $('#p-cover')[0].offsetTop){
		size = oP.offsetHeight - 2 - $('#p-cover')[0].offsetTop;
		this.value = oP.offsetHeight - 2 - $('#p-cover')[0].offsetTop;
	}  
	$('#p-cover')[0].style.width = size + 'px'; 
	oFm.c_x.max = sw - size;
}

oFm.c_h.oninput = function(){
	var size = this.value;
	if(size >= oP.offsetWidth - $('#p-cover')[0].offsetLeft){
		size = oP.offsetWidth- 2 -$('#p-cover')[0].offsetLeft;
		this.value = oP.offsetWidth-2-$('#p-cover')[0].offsetLeft;
	}
	if(size >= oP.offsetHeight - $('#p-cover')[0].offsetTop){
		size = oP.offsetHeight - 2 - $('#p-cover')[0].offsetTop;
		this.value = oP.offsetHeight - 2 - $('#p-cover')[0].offsetTop;
	}
	$('#p-cover')[0].style.height = size + 'px';
	oFm.c_y.max = sh - size; 
}


$('#poster-option button')[1].onclick = function(){
	if(this.getAttribute('data-status') == 0){
		this.className = 'btn btn-success';
		$('#p-nickname')[0].style.display = 'block'; 
		this.setAttribute('data-status',1);
		oFm.n_show.value = 1; 
	}else{
		$('#p-nickname')[0].style.display = 'none';
		this.className = 'btn btn-default';
		this.setAttribute('data-status',0);
		oFm.n_show.value = 0;
	}
}

oFm.n_x.oninput = function(){
	var left = this.value;
	if(left >= oP.offsetWidth - $('#p-nickname')[0].offsetWidth){
		left = oP.offsetWidth - $('#p-nickname')[0].offsetWidth - 4;
		this.value = oP.offsetWidth - $('#p-nickname')[0].offsetWidth;
	}
	$('#p-nickname')[0].style.left = left + 'px';
}


oFm.n_y.oninput = function(){
	var top = this.value;
	if(top >= oP.offsetHeight - $('#p-nickname')[0].offsetHeight){
		top = oP.offsetHeight - $('#p-nickname')[0].offsetHeight - 4;
		this.value = oP.offsetHeight - $('#p-nickname')[0].offsetHeight;
	}
	$('#p-nickname')[0].style.top = top + 'px';
}

oFm.n_z.oninput = function(){
	var size = this.value;
	$('#p-nickname')[0].style.fontSize = size + 'px';
	oFm.n_x.max = sw - $('#p-nickname')[0].getBoundingClientRect().width;
	oFm.n_y.max = sh - $('#p-nickname')[0].getBoundingClientRect().height; 
}

oFm.n_cc.onchange = function(){
	oFm.n_c.value = colorRgb(this.value); 
	$('#p-nickname')[0].style.color = this.value;
}


$('#poster-option button')[2].onclick = function(){
	if(this.getAttribute('data-status') == 0){
		this.className = 'btn btn-success';
		$('#p-title')[0].style.display = 'block'; 
		this.setAttribute('data-status',1);
		oFm.t_show.value = 1; 
	}else{
		$('#p-title')[0].style.display = 'none';
		this.className = 'btn btn-default';
		this.setAttribute('data-status',0);
		oFm.t_show.value = 0;
	}
}


oFm.t_x.oninput = function(){
	var left = this.value;
	if(left >= oP.offsetWidth - $('#p-title')[0].offsetWidth){
		left = oP.offsetWidth - $('#p-title')[0].offsetWidth - 4;
		this.value = oP.offsetWidth - $('#p-title')[0].offsetWidth;
	}
	$('#p-title')[0].style.left = left + 'px';
}


oFm.t_y.oninput = function(){
	var top = this.value;
	if(top >= oP.offsetHeight - $('#p-title')[0].offsetHeight){
		top = oP.offsetHeight - $('#p-title')[0].offsetHeight - 4;
		this.value = oP.offsetHeight - $('#p-title')[0].offsetHeight;
	}
	$('#p-title')[0].style.top = top + 'px';
}

oFm.t_z.oninput = function(){
	var size = this.value;
	$('#p-title')[0].style.fontSize = size + 'px';
	oFm.t_x.max = sw - $('#p-title')[0].getBoundingClientRect().width;
	oFm.t_y.max = sh - $('#p-title')[0].getBoundingClientRect().height; 
}

oFm.t_cc.onchange = function(){
	oFm.t_c.value = colorRgb(this.value); 
	$('#p-title')[0].style.color = this.value;
}



function colorRgb(color){
    var sColor = color.toLowerCase();
    //十六进制颜色值的正则表达式
    var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;
    // 如果是16进制颜色
    if (sColor && reg.test(sColor)) {
        if (sColor.length === 4) {
            var sColorNew = "#";
            for (var i=1; i<4; i+=1) {
                sColorNew += sColor.slice(i, i+1).concat(sColor.slice(i, i+1));    
            }
            sColor = sColorNew;
        }
        //处理六位的颜色值
        var sColorChange = [];
        for (var i=1; i<7; i+=2) {
            sColorChange.push(parseInt("0x"+sColor.slice(i, i+2)));    
        }
        return "RGB(" + sColorChange.join(",") + ")";
    }
    return sColor;
};


function colorNumber(color){
    var that = color;
    //十六进制颜色值的正则表达式
    var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;
    // 如果是rgb颜色表示
    if (/^(rgb|RGB)/.test(that)) {
        var aColor = that.replace(/(?:\(|\)|rgb|RGB)*/g, "").split(",");
        var strHex = "#";
        for (var i=0; i<aColor.length; i++) {
            var hex = Number(aColor[i]).toString(16);
            if (hex === "0") {
                hex += hex;    
            }
            strHex += hex;
        }
        if (strHex.length !== 7) {
            strHex = that;    
        }
        return strHex;
    } else if (reg.test(that)) {
        var aNum = that.replace(/#/,"").split("");
        if (aNum.length === 6) {
            return that;    
        } else if(aNum.length === 3) {
            var numHex = "#";
            for (var i=0; i<aNum.length; i+=1) {
                numHex += (aNum[i] + aNum[i]);
            }
            return numHex;
        }
    }
    return that;
};










