// $(window).load(
//   function(){

$(function(){
    var libraryID;
    $('body').on('change', '.library', function(){
      libraryID = $('.library').val();
      $.ajax({
        type: "GET",
        url: 'getcapacity.php?building='+libraryID,
        success : function(data) {
          /* Remove all options from the select list */
          $('.capacity').empty();

          /* Remove all options from the select list */
          var optionsArray = [];
          for (var i = 0; i < data.length; i++) {
            if ((data[i].capacity != null) && (data[i].capacity != '')) {
             if($.inArray(data[i].capacity, optionsArray) === -1) optionsArray.push(data[i].capacity);
            }
          }
          optionsArray.sort(function(a, b){return a-b});
          //console.log(optionsArray);

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
    }
    );

    $("select.library").change();
  }
);
