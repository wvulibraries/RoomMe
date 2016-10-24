$(function(){
  var resourceID;
  var resourceType;

  $('body').on('change', '.resourceID', function(){
    $("select.resourceType").change();
    resourceID = $('.resourceID').val();
    $.ajax({
          url: roomReservationHome+"/includes/ajax/getRooms.php",
          type: "GET",
          dataType: "json",
          data: { building: resourceID },

          success : function(data) {
            /* Remove all options from the select list */
            $('.rooms').empty();

            var options;
            options += '<option value="' + 'NULL' + '">' + 'Select a Room' + '</option>';
            for (var i = 0; i < data.length; i++) {
              options += '<option value="' + data[i].ID + '">' + data[i].name + '</option>';
            }

            $('.rooms').html(options);
          },
          error: function() {
             console.log('failure');
          },
      });
  });


  $('body').on('change', '.resourceType', function(){
    resourceType = $('.resourceType').val();
    resourceID = $('.resourceID').val();

    if ((resourceType == 0) || (resourceID == 'NULL')){
      $('.rooms').prop("disabled", true);
    } else {
      $('.rooms').prop("disabled", false);
    }

    var visible = $('.resourceType option[value="1"]').is(':hidden');
    if (visible) {
      $('.resourceType option[value="1"]').hide();
    }

    var visible = $('.resourceType option[value="2"]').is(':hidden');
    if (visible) {
      $('.resourceType option[value="2"]').hide();
    }

  });

  $("select.resourceType").change();
});
