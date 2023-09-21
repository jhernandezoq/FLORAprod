function Usersti(tool_id){
	var  tool_id=$("#tool_id").val();
	if (tool_id == '') {
     $("#usersti").hide('slow');
	}else{
	 $("#usersti").show('slow');
	}

}


function ActualizacionDatos(){
	alert("Datos actualizados");
}