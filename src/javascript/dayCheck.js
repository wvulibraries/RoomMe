$(function(){
  var $startDay;
  var $startMonth;
  var $startYear;

  $('body').on('change', '.date_select', function(){
  	$startDay = $('#start_day').val();
  	$startMonth = $('#start_month').val();
  	$startYear = $('#start_year').val();

  	$numberofdays = findNumberOfDays($startMonth, $startYear);
  	setNumberOfDays($numberofdays, '#start_day');
  });

  $('body').on('change', '.end_date_select', function(){
  	$startDay = $('#seriesEndDate_day').val();
  	$startMonth = $('#seriesEndDate_month').val();
  	$startYear = $('#seriesEndDate_year').val();

  	$numberofdays = findNumberOfDays($startMonth, $startYear);
  	setNumberOfDays($numberofdays, '#seriesEndDate_day');
  });

	$("select#start_day").change();
	$("select#seriesEndDate_day").change();
});

/**
 * sets number of days in month.
 * @param {number} numberofdays - grabs nums in the month
 * @param {object} cell - attaches to select object
 */
function setNumberOfDays(numberofdays, cell) {
  var total_items = $(cell).find('option').length;

  if (total_items > numberofdays) {
    for(i = numberofdays+1; i < total_items+1; i++) {
     $(cell).find("option[value=" + i + "]").remove();
    }
  }
  else if (total_items < numberofdays) {
    for(i = total_items+1; i < numberofdays+1; i++) {
     $(cell).append($('<option>', {
      value: i,
      text: i
     }));
   }
  }
}

/**
 * checks month and year for number of days and returns that to the set function
 * @param {month} month
 * @param {year} year
 */
function findNumberOfDays(month, year) {
  return (new Date(year, month, 0).getDate());
}
