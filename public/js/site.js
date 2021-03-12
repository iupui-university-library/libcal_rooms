// JS for LibCal Rooms

/**
 * Setup post page load
 */
jQuery(document).ready(function($) {
  $('[data-provide="datepicker"]').datepicker({
    format: 'yyyy-mm-dd',
    startDate: '-0d',
    autoclose: true,
  });
});

/**
 * If Date and Room selected
 * create start time options
 * @param
 * @return
 */
function startTime(){
  if( !$('[name="room-id"]').val() == 0  &&
      !$('[name="reservation-date"]').val() == 0
  ){
    var room_id = $('[name="room-id"]').val();
    var reservation_date = $('[name="reservation-date"]').val();
    url = 'start_time.php?room_id=' + room_id + '&reservation_date=' + reservation_date;
    $.getJSON(url)
      .done(function(data){
        $('#start-time').empty().append('<option disabled selected value> -- Select Start Time -- </option>');
        $('#end-time').empty().append('<option disabled selected value> -- Select End Time -- </option>');
        $.each(data, function(index, time){
          $('#start-time').append(new Option(show_time(time), time));
        });
        $('#start-time').prop('disabled', false);
        $('#end-time').prop('disabled', true);
        $('#confirm-submit').prop('disabled', true);
      })
      .fail(function( jqxhr, textStatus, error ){
        $('#start-time-div').html('<h3>Time look up failed.</h3>');
      });
  }
}

/**
 * Update end time
 * @param select [Object] - jquery select object
 * @return
 */
function endTime(select, max){
  var start_time = select.value;
  var room_id = $('[name="room-id"]').val();
  var url = "end_time.php?room_id=" + room_id + "&start_time=" + start_time +"&max=" + max;
  $.getJSON(url)
  .done(function(data){
    $("#end-time").empty().append('<option disabled selected value> -- select end time -- </option>');
    $.each(data, function(index, time){
      $("#end-time").append(new Option(show_time(time), time));
    });
    $('#end-time').prop('disabled', false);
    $('#confirm-submit').prop('disabled', true);
  })
  .fail(function( jqxhr, textStatus, error ){
    $("#end_time-div").html("<h3>Time look up failed.</h3>");
  });
}

/**
 * Format time for select text
 * @param time [string] - date/time to translate
 * @return [string] - time to display
 */
function show_time(time){
 var timeDate=new Date(time);
 var format ="AM";
 var hour=timeDate.getHours();
 var min=timeDate.getMinutes();
 if(hour>11){format="PM";}
 if (hour   > 12) { hour = hour - 12; }
 if (hour   == 0) { hour = 12; }
 if (min < 10){min = "0" + min;}
 return hour + ":" + min + " " + format;
}
