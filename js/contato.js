$(function(){
		$('#form').validate({
			rules:{
				nome:"required",
				email:{
					required:true,
					email:true
				},
				assunto:"required",
				mensagem:"required"
			},
			messages:{
				nome:"",
				email:{
					required:"",
					email:""
				},
				assunto:"",
				mensagem:""
			}
		});
});