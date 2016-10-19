$(function(){
    var libraryID;

    $('body').on('change', '.library', function(){
      libraryID = $('.library').val();
      $.ajax({
        url: roomReservationHome+"/includes/ajax/getBuildingRooms.php",
        type: "GET",
        dataType: "json",
        async: false,
        data: { buildingID: libraryID },
        success : function(data) {
          /* Remove all options from the select list */
          $('.capacity').empty();

          /* Build array of valid (unique) capacity values to be added to select */
          var optionsArray = [];
          for (var i = 0; i < data.length; i++) {
            if ((data[i].capacity != null) && (data[i].capacity != '')) {
             if($.inArray(data[i].capacity, optionsArray) === -1) optionsArray.push(data[i].capacity);
            }
          }
          optionsArray.sort(function(a, b){return a-b});

          /* Create List of Room Capacity's for Current Building */
          var options;
          options += '<option value="' + '*' + '">' + 'Any Capacity' + '</option>';
          $.each(optionsArray, function(i, capacity){
              options += '<option value="' + capacity + '">' + capacity + '</option>';
          });

          $('.capacity').html(options);
        },
        error: function() {
           console.log('failure');
        },
       });
    });

    $("select.library").change();
});
