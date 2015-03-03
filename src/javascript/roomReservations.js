var modalCalendarURL    = roomReservationHome+"/calendar/calendar.php";
var buildingCalendarURL = roomReservationHome+"/calendar/building/";

$(function() {
	$(document)
		.on('click', '.calendarModal_link', handler_calModal)
		.on('click', '.mapModal_link', handler_mapModal)
		.on('click', '.calUpdateButton', handler_changeCalDate)
		.on('click', '#calUpdateFormSubmit', handler_changeCalDateForm)
		.on('click', '#deleteReservation', handler_deleteReservation)
		.on('click', '.cancelReservation', handler_deleteReservation)
		.on('click', '#closeModalCalendar', handler_closeModal);
});

function handler_closeModal() {
	$.modal.close();
}

function handler_calModal() {
	var type = $(this).attr('data-type');
	var url  = "";

	url = roomReservationHome+"/calendar/calendar.php?"+type+"="+$(this).attr('data-id');

	$.ajax({
		url: url,
		dataType: "html",
		success: function(responseData) {
			console.log($('#calendarModal'));
			$('#calendarModal').html(responseData);
			$('#calendarModal').modal({overlayClose:true});
			// $('#reservationsRoomTable').tableScroll({height:360});
		},
		error: function(jqXHR,error,exception) {
			$('#calendarModal').html("An Error has occurred: "+error);
			$('#calendarModal').modal({overlayClose:true});
		}
	});


	return false;
}

function handler_mapModal() {
	var url = $(this).attr("href");

	var src = url;
	$.modal('<iframe src="' + src + '" height="800" width="600" style="border:0; overflow: hidden;">', {
		closeHTML:"",
		containerCss:{
			backgroundColor:"#fff",
			borderColor:"#fff",
			height:450,
			padding:0,
			width:830
		},
		overlayClose:true,
		minHeight:700,
		minWidth: 500,
	});
	

	// $.ajax({
	// 	url: url,
	// 	success: function(responseData) {
	// 		$('#calendarModal').html(responseData);
	// 		$('#calendarModal').modal({overlayClose:true});
	// 	},
	// 	error: function(jqXHR,error,exception) {
	// 		$('#calendarModal').html("An Error has occurred: "+error);
	// 		$('#calendarModal').modal({overlayClose:true});
	// 	}
	// });

	return(false);
}

function handler_changeCalDate() {
	var month = $(this).attr('data-month');
	var day   = $(this).attr('data-day');
	var year  = $(this).attr('data-year');
	var type  = $(this).attr('data-type');

	url = (($(this).attr('data-modal') == "true")?modalCalendarURL:buildingCalendarURL)+"?"+type+"="+$(this).attr('data-id')+"&month="+month+"&day="+day+"&year="+year;

	if ($(this).attr('data-modal') == "false") {
		window.location.href = url;
		return;
	}

	$.ajax({
		url: url,
		dataType: "html",
		success: function(responseData) {
			$('#calendarModal').html(responseData);
				// $('#calendarModal').modal({overlayClose:true});
				$('#reservationsRoomTable').tableScroll({height:360});
			},
			error: function(jqXHR,error,exception) {
				$('#calendarModal').html("An Error has occurred: "+error);
				// $('#calendarModal').modal({overlayClose:true});
			}
		});
}

function handler_changeCalDateForm() {
	var month = $('#start_month_modal option:selected').val();
	var day   = $('#start_day_modal option:selected').val();
	var year  = $('#start_year_modal option:selected').val();
	var type  = $(this).attr('data-type');
	var id    = $(this).attr('data-id');

	url = (($(this).attr('data-modal') == "true")?modalCalendarURL:buildingCalendarURL)+"?"+type+"="+$(this).attr('data-id')+"&month="+month+"&day="+day+"&year="+year;

	if ($(this).attr('data-modal') == "false") {
		window.location.href = url;
		return;
	}

	$.ajax({
		url: url,
		dataType: "html",
		success: function(responseData) {
			$('#calendarModal').html(responseData);
				// $('#calendarModal').modal({overlayClose:true});
				$('#reservationsRoomTable').tableScroll({height:360});
			},
			error: function(jqXHR,error,exception) {
				$('#calendarModal').html("An Error has occurred: "+error);
				// $('#calendarModal').modal({overlayClose:true});
			}
		});
}

function handler_deleteReservation() {
	return confirm("Are you sure you want to Cancel this reservation?");
}