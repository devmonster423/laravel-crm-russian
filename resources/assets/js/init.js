$(document).ready(function() {
	initAjaxClass();
	initAjaxForm();
	initRemoveError();
	initCheck();
	initCheckboxToggle();
	initDataTable();

	$.srSmoothscroll({
		step: 100,
		speed: 100,
		ease: 'swing',
		target: $('body'),
		container: $(window)
	});

});


$.ajaxSetup({
    headers: {
        'X-XSRF-TOKEN': $('input[name="csrf-token"]').val()
    }
});



/*
* ajaxStart
*/
$(document).ajaxStart(function() {
	Pace.restart();
});



if (history.pushState) {
	history.pushState({page: window.location.pathname, type: "page"}, document.title, window.location.pathname);

	window.addEventListener('popstate', function(e){
		setPage(e.state.page, 'popstate');
	}, false);

	$(document).on('click','a:not(.'+CLASS_NO_AJAX+'):not([href="#"])',function(e){
		setPage($(this).attr('href'));
		return false;
	});
}



/*
* initAjaxClass ���� �� ������ � ������� ajax
*/
function initAjaxClass(){
	$('.'+CLASS_AJAX).click(function(event){
		event.preventDefault();

		var url = $(this).attr('href') || $(this).attr('data-url');		//URL
		var method = $(this).attr('data-method') || 'GET';			//����� POST ��� GET
		var content = $(this).attr('data-content') || ID_AJAX_CONTENT;		//��������� � ������� ��������� ���������

		if(url && method && content){
			$.ajax({
				url: url,
				type: method,
				data: {
					'_token' : $('meta[name="csrf-token"]').attr('content')
				},
				success: function(result){
					$(content).html(result);
				}
			});
		}else{
			if(!url){alert = '��� url';}
			if(!method){alert = '��� method';}
			if(!content){alert = '��� content';}
		}
	});
}



/*
* initAjaxForm ajax form
*/
function initAjaxForm(){
	$("form:not(."+CLASS_NO_AJAX+")").submit(function(event){
		event.preventDefault();

		var $form = $(this);
		var confirm = $form.attr('data-confirm') || '';

		if(confirm != ''){
			confirmCall(confirm, function(){
				ajaxForm($form);
			});
			return false;
		}

		ajaxForm($form);
	});
}



//��� ������ ������ ������� �������
function initRemoveError(){
	$(':input').on('focus', function() {
		$(this).closest('.form-group').removeClass(CLASS_ERROR);
	});
}



/*
* iCheck
*/
function initCheck(){
	$('input[type="checkbox"]').iCheck({
		checkboxClass: 'icheckbox_flat-blue',
		radioClass: 'iradio_flat-blue'
	});
	
	$("input[name='"+NAME_CHECKBOX_TABLE+"']").on('ifClicked', function () {
		$(this).iCheck("toggle");

		if($("input[name='"+NAME_CHECKBOX_TABLE+"']:checked").length > 0){
			$('.'+TABLE_CONTROLS+' .btn-group').find(':input').removeClass(BUTTON_DISABLED);
		}else{
			$('.'+TABLE_CONTROLS+' .btn-group').find(':input').addClass(BUTTON_DISABLED);
		}
	});
}



/*
* checkbox-toggle
*/
function initCheckboxToggle(){
	$("."+CHECKBOX_TOGGLE).on('ifToggled', function () {
		var clicks = $(this).data('clicks');
		var name = $(this).data('name');
		if (clicks) {
			//Uncheck all checkboxes
			$("input[name='"+name+"']").iCheck("uncheck");
			$("."+CHECKBOX_TOGGLE+"[data-name='"+name+"']").iCheck("uncheck");
			$('.'+TABLE_CONTROLS+' .btn-group').find(':input').addClass(BUTTON_DISABLED);
		} else {
			//Check all checkboxes
			$("input[name='"+name+"']").iCheck("check");
			$("."+CHECKBOX_TOGGLE+"[data-name='"+name+"']").iCheck("check");
			$('.'+TABLE_CONTROLS+' .btn-group').find(':input').removeClass(BUTTON_DISABLED);
		}
		$(this).data("clicks", !clicks);
	});
}



/*
*	DataTable
*/
function initDataTable(){
	$("."+DATA_TABLE).DataTable({
		"language": {"url": "//cdn.datatables.net/plug-ins/1.10.10/i18n/Russian.json"},
		"pageLength": 25,
		"columnDefs": [{ targets: 'no-sort', orderable: false, width: "20px" }],
		"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "���"]],
		stateSave: true
	});

	$("."+DATA_TABLE_SMALL).DataTable({
		"language": {"url": "//cdn.datatables.net/plug-ins/1.10.10/i18n/Russian.json"},
		"pageLength": 25,
		"paging": false,
		"lengthChange": false,
		"searching": false,
		"ordering": true,
		"info": false,
		"autoWidth": false,
		"columnDefs": [{ targets: 'no-sort', orderable: false, width: "20px" }],
		stateSave: true
	});
}