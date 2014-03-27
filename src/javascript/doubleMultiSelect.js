function sortList(list) {
	var listItems = list.children('option').sort(function(a,b){
		var a1 = $(a).text();
		var b1 = $(b).text();
		return (a1 < b1) ? -1 : (a1 > b1) ? 1 : 0;
	});
	list.append(listItems);
}

function buttonClickHandlers(leftID, rightID) {
	leftID  = '#'+leftID;
	rightID = '#'+rightID;

	$('#addAll').click(function() {
		$(leftID+' option').remove().appendTo(rightID);
		sortList($(rightID));
	});
	$('#add').click(function() {
		$(leftID+' option:selected').remove().appendTo(rightID);
		sortList($(rightID));
	});
	$('#remove').click(function() {
		$(rightID+' option:selected').remove().appendTo(leftID);
		sortList($(leftID));
	});
	$('#removeAll').click(function() {
		$(rightID+' option').remove().appendTo(leftID);
		sortList($(leftID));
	});
	$(':submit').click(function() {
		$(rightID+' option').attr('selected', 'selected');
	});
}

function setWidth(element, container) {
	$(element).width( ($(container).width() - 50) / 2 );
}
