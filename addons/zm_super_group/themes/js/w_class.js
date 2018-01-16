addClassCheck();
updateClass();



/**
 * 添加分类表单验证
 */
function addClassCheck(){
	var oFm = document.add_class;
	oFm.onsubmit = function(){
		if(oFm.name.value == ''){
			alert('分类名称不能为空！');
			return false;
		}
		
		if(oFm.name.value.length < 2 || oFm.name.value.length > 4){
			alert('分类名称不能小于2位，大于4位！');
			return false;
		}
		
		return true;
	}
}


function updateClass(){
	var aUpdateBtn = $('.update-btn');
	var oFm = document.update_class;
	for(var i=0;i<aUpdateBtn.length;i++){
		aUpdateBtn[i].addEventListener('click',function(){
			oFm.classId.value = this.getAttribute('data-classId');
			oFm.name.value = this.getAttribute('data-className');
		});
	}
	
	oFm.onsubmit = function(){
		if(oFm.name.value == ''){
			alert('分类名称不能为空！');
			return false;
		}
		
		if(oFm.name.value.length < 2 || oFm.name.value.length > 4){
			alert('分类名称不能小于2位，大于4位！');
			return false;
		}
		
		return true;
	}
	
}















