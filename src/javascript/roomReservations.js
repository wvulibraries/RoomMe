var calendarData;
var mobileCalendarData  = undefined;

$(function() {
			var $doc = $(document);

			if($('#deleteReservation').length) {
				$doc.on('click',  '#deleteReservation',   handler_deleteReservation);
			}

			if($('.cancelReservation').length) {
				$doc.on('click',  '.cancelReservation',   handler_deleteReservation);
			}

			if($('#calUpdateFormSubmit').length) {
				$doc.on('click',  '#calUpdateFormSubmit', handler_getCalendarJSON);
			}

			if($('#listBuildingSelect').length) {
				$doc.on('change', '#listBuildingSelect', handler_listBuildingSelect);
			}

			if($('.pagerLink').length) {
				$doc.on('click',  '.pagerLink', 				 handler_pager);
			}

			if($('#openEvent').length) {
				$doc.on('change', '#openEvent',           handler_openEvent);
			}

			if($('#building_modal').length) {
				$doc.on('change', '#building_modal',      handler_activate_submit_button);
			}

			if($('#start_month_modal').length) {
				$doc.on('change', '#start_month_modal',   handler_activate_submit_button);
			}

			if($('#start_day_modal').length) {
				$doc.on('change', '#start_day_modal',     handler_activate_submit_button);
			}

			if($('#start_year_modal').length) {
				$doc.on('change', '#start_year_modal',    handler_activate_submit_button);
			}
	})


// ------------

var windowSize;
var sizeBreakPoint_mobile;
var sizeBreakPoint_tablet;
var resizeTimer;

function handler_activate_submit_button() {
	$("#calUpdateFormSubmit").removeClass("button_inactive");
	$("#calUpdateFormSubmit").addClass("button_active");
}

function handler_openEvent() {

	if ($(this).val() == "1") {
		$(".openEventDescription").show();
	}
	else {
		$(".openEventDescription").hide();
	}

}

function initialResize() {
	windowSize = $(window).width();
	sizeBreakPoint_mobile = 768;
	sizeBreakPoint_tablet = 1024;
	handle_resizing();
}

function handle_resizing(){
	windowSize = $(window).width();
	console.log(windowSize);

	if (windowSize < sizeBreakPoint_mobile) {
		numberOfColumns = 0;
	}
	else if (windowSize < sizeBreakPoint_tablet && windowSize >= sizeBreakPoint_mobile) {
		numberOfColumns = 4;
	}
	else if (windowSize >= sizeBreakPoint_tablet) {
		numberOfColumns = 7;
	}
	else {
		// default to desktop size if something goes wrong
		numberOfColumns = 7;
	}

	buildCalendarTable(calendarData,0,numberOfColumns);
	setPagerAttributes(0,numberOfColumns);

}


$(window).resize(function() {
     clearTimeout(resizeTimer);
     resizeTimer = setTimeout(handle_resizing, 100);
});


function initialCalendarLoad() {
	handler_getCalendarJSON(false);
	setPagerAttributes(0,numberOfColumns);
}

function handler_pager() {

	var startCols = parseInt($(this).attr("data-startCols"));
	var endCols   = parseInt($(this).attr("data-endCols"));

	// build the new calendar
	buildCalendarTable(calendarData,startCols,endCols);

	// set the new pager variables
	setPagerAttributes(startCols,endCols);

	return false;
}

function setPagerAttributes(startCols,endCols) {

	// prev
	$('#pagerPrev').attr('data-startCols',(startCols-numberOfColumns < 0)?0:startCols-numberOfColumns);
	$('#pagerPrev').attr('data-endCols',(endCols-numberOfColumns <=0)?numberOfColumns:endCols-numberOfColumns);

	// next
	$('#pagerNext').attr('data-startCols',(endCols >= calendarData.rooms.length)?calendarData.rooms.length-numberOfColumns:endCols);
	$('#pagerNext').attr('data-endCols',(endCols+numberOfColumns > calendarData.rooms.length)?calendarData.rooms.length:endCols+numberOfColumns);

	// last
	$('#pagerLast').attr('data-startCols',(calendarData.rooms.length-numberOfColumns));
	$('#pagerLast').attr('data-endCols',calendarData.rooms.length);



	if (startCols <= 0) {
		$('#pagerPrev').addClass("pager-disabled");
		$('#pagerFirst').addClass("pager-disabled");
	}
	else {
		$('#pagerPrev').removeClass("pager-disabled");
		$('#pagerFirst').removeClass("pager-disabled");
	}

	if (endCols >= calendarData.rooms.length) {
		$('#pagerNext').addClass("pager-disabled");
		$('#pagerLast').addClass("pager-disabled");
	}
	else {
		$('#pagerNext').removeClass("pager-disabled");
		$('#pagerLast').removeClass("pager-disabled");
	}

}

function getMobileCalendarData() {

	var url      = roomReservationHome+"/includes/ajax/getMobileCalendarJson.php";

	$.ajax({
		url: url,
		dataType: "json",
		success: function(responseData) {

			mobileCalendarData = $.parseJSON(responseData);
			buildCalendarTable(calendarData,0,numberOfColumns);
			setHeaderDate();

		},
		error: function(jqXHR,error,exception) {
			$("#calendarData").html("Error retrieving calendar infocmation.");
		},
		async:   false

	});

}

function buildRoomList(data) {

	if (typeof mobileCalendarData == 'undefined' ) {

		getMobileCalendarData();

	}

	// console.log(mobileCalendarData);

	// $("#mobileList").empty();

	// what is the nearest 30 minutes?
	// Current time in seconds, date.now provides milliseconds
	var currentTime     = Date.now() / 1000 | 0;
	var prevHalfHour    = currentTime - (currentTime % 1800);
	var nextHalfHour    = prevHalfHour + 1800;
	var closestHalfHour = ((currentTime - prevHalfHour) > (nextHalfHour - currentTime))?nextHalfHour:prevHalfHour;

	// console.log(currentTime);
	// console.log(prevHalfHour);
	// console.log(nextHalfHour);
	// console.log(closestHalfHour);


	$.each(mobileCalendarData, function(index, building) {

		// $("#mobileList").append('<li><h4>'+index+'</h4></li>');

		$.each(building.rooms, function (index, room) {

			// is room available at the nearest 30?
			if (room.times[closestHalfHour].reserved) {
				return;
			}

			// display the room
			// $("#mobileList").append('<li><a href="'+roomReservationHome+'/building/room/?room='+room.roomID+'">'+room.displayName+'</a></li>');

		});
	});

}

function buildCalendarTable(data,startCols,endCols) {

	// data = $.parseJSON(data)
	// console.log(data);

	if (numberOfColumns <= 0) {
		buildRoomList(data);
		return;
	}

	// remove existing content
	$('#reservationsRoomTableHeaderRow').empty();
	$('#reservationsRoomTableBody').empty();

	// add in empty cell
	$('#reservationsRoomTableHeaderRow').append('<td class="tdHours tdEmpty">&nbsp;</td>');

    $.each(data.times, function (index, value) {
    	if (value.type == "hour") {
    		$('#reservationsRoomTableBody').append('<tr id="tr_'+index+'" class="'+value.type+'"><th scope="row" class="tdHours" rowspan="4">'+value.display+'</th></tr>');
    	}
    	else {
    		$('#reservationsRoomTableBody').append('<tr id="tr_'+index+'" class="'+value.type+'"></tr>');
    	}
    	// console.log(value);
    });

    var bookings = new Array();
    bookings[""] = "foo"; // Set the empty value, otherwise the first empty value will show as undefined.

    var count = 0
    $.each(data.rooms, function (index, room) {

    	if (count >= startCols && count < endCols) {

    		$('#reservationsRoomTableHeaderRow').append('<th scope="col" class="calendarCol"><a href="'+roomReservationHome+'/building/room/?room='+room.roomID+'">'+room.displayName+'</a></th>');

    		$.each(room.times, function (index, time) {
				// console.log(time);
				// console.log(index);
				// console.log(parseInt(index)+900);
				// if (typeof room.times[parseInt(index)+900] != 'undefined') {
				// 	console.log(room.times[parseInt(index)+900].reserved);
				// }

    			if (time.hourType == "hour") {
    				hasAddIndicator = false;
    			}

    			tdReservationClass = time.hourType+((time.reserved)?" reserved":" notReserved");

    			var tdContent = "";
    			if (time.reserved && typeof bookings[time.booking] == 'undefined') {
    				tdContent = '<span class="reservationName">'+time.username+'</span>&nbsp; <span class="reservationTime">'+time.displayTime+'</span>&nbsp; <span class="reservationDuration">'+time.duration+'</span>';

    				bookings[time.booking] = time.booking;
    			}
    			else if (time.reserved && time.displayTime == '' && time.username == 'Closed' && typeof bookings["closed" + room.roomID] == 'undefined') {

    				tdContent = '<span class="reservationName">'+time.username+'</span>';
    				bookings["closed" + room.roomID] = "closed";

    			}
    			// If the quarter hour is not reserved
    			// and there hasn't been a reservation indicator added for this hour yet
    			// and it isn't the last 15 minutes of an hour
    			// and the next 15 minute block isn't reserved
    			// Add the indicator.
    			//
    			// This should only add the indicator if there is at least 30 minutes in a block.
    			else if (time.reserved === false && hasAddIndicator === false && time.hourType != "quarterTill" && typeof room.times[parseInt(index)+900] != 'undefined' && room.times[parseInt(index)+900].reserved === false) {
    				hasAddIndicator = true;
    				tdContent = '<a href="'+roomReservationHome+'/building/room/?room='+room.roomID+'&reservationSTime='+index+'" class="roomClick"><i class="fa fa-plus"></i></a>';
    			}
    			else if (time.reserved === false && hasAddIndicator === true) {
    				tdContent = '<a href="'+roomReservationHome+'/building/room/?room='+room.roomID+'&reservationSTime='+index+'" class="roomClick roomClickEmpty"></a>';
    			}

    			$('#tr_'+index).append('<td class="'+tdReservationClass+'">'+tdContent+'</td>');

    		});
    	}
    	count++;
    	// console.log(value);
    });

}

function handler_getCalendarJSON(sync) {

	sync = (typeof sync !== 'undefined')? sync : true;

	var calType  = (typeof $("#room_modal").val() !== 'undefined')?"room":"building";
	var objectID = (calType == "building")?$("#building_modal").val():$("#room_modal").val();
	var url      = roomReservationHome+"/includes/ajax/getCalendarJson.php?type="+calType+"&objectID="+objectID+"&month="+$("#start_month_modal").val()+"&day="+$("#start_day_modal").val()+"&year="+$("#start_year_modal").val();
	// alert(url);


    $('#imageLoader').css('display', 'block');

	$.ajax({
		url: url,
		dataType: "json",
		success: function(responseData) {

			calendarData = $.parseJSON(responseData);
			buildCalendarTable(calendarData,0,numberOfColumns);
			setHeaderDate();

		},
		error: function(jqXHR,error,exception) {
			$("#calendarData").html("Error retrieving calendar infocmation.");
		},
		async:   sync

	});

	return false;

}

function setHeaderDate() {
	var url = roomReservationHome+"/includes/ajax/getHeaderDate.php?month="+$("#start_month_modal").val()+"&day="+$("#start_day_modal").val()+"&year="+$("#start_year_modal").val();

	$.ajax({
		url: url,
		dataType: "html",
		success: function(responseData) {

			$("#headerDate").html(responseData);
			$('#imageLoader').css('display', 'none');

		},
		error: function(jqXHR,error,exception) {
			$("#headerDate").html("Error retrieving date.");
		}

	});
}

function handler_listBuildingSelect() {

	var url = roomReservationHome+"/includes/ajax/getBuildingRooms.php?buildingID="+$("#listBuildingSelect").val();

	$.ajax({
		url: url,
		dataType: "json",
		success: function(responseData) {

			// remove all the current elements
			$("#listBuildingRoomsSelect").find('option').remove().end()

			// Add the "Any Room" option back in
			if ($("#listBuildingRoomsSelect").attr('data-anyroom') != "false") {
				$("#listBuildingRoomsSelect").append("<option value='any'>Any Room</option>")
			}

			$.each(responseData, function(i, room) {

			$("#listBuildingRoomsSelect").append("<option value='"+room.ID+"'>"+room.name+" - "+room.number+"</option>")

			})

		},
		error: function(jqXHR,error,exception) {

		}

	});

	return false;

}

function handler_deleteReservation() {
	return confirm("Are you sure you want to Cancel this reservation?");
}
