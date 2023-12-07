<!DOCTYPE html>
<html>
<head>

<!-- *Note: You must have internet connection on your laptop or pc other wise below code is not working -->
<!-- CSS for full calender -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
<!-- JS for jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- JS for full calender -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
<!-- bootstrap css and js -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<div id="calendar"></div>
		</div>
	</div>
</div>
<!-- Start popup dialog box -->
<div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalLabel">Event</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">x</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="img-container">
					<div class="row">
						<div class="col-sm-12">  
							<div class="form-group">
							  <label for="nama_peminjam">Nama Peminjam</label>
							  <input type="text" name="nama_peminjam" id="nama_peminjam" class="form-control">
							</div>
						</div>
						<div class="col-sm-12">  
							<div class="form-group">
							  <label for="nama_tempat">Nama Tempat</label>
							  <input type="text" name="nama_tempat" id="nama_tempat" class="form-control">
							</div>
						</div>
						<div class="col-sm-12">  
							<div class="form-group">
							  <label for="nama_kegiatan">Nama Kegiatan</label>
							  <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">  
							<div class="form-group">
							  <label for="event_start_date">Tanggal Awal</label>
							  <input type="date" name="event_start_date" id="event_start_date" class="form-control onlydatepicker" placeholder="Event start date">
							 </div>
						</div>
						<div class="col-sm-6">  
							<div class="form-group">
							  <label for="event_end_date">Tanggal Selesai</label>
							  <input type="date" name="event_end_date" id="event_end_date" class="form-control" placeholder="Event end date">
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
			</div>-->
		</div>
	</div>
</div>
<!-- End popup dialog box -->

<script>
$(document).ready(function() {
	display_events();
}); //end document.ready block

function display_events() {
	var events = new Array();
$.ajax({
    url: 'display_event.php',  
    dataType: 'json',
    success: function (response) {
         
    var result=response.data;
    $.each(result, function (i, item) {
    	events.push({
            event_id: result[i].event_id,
            title: result[i].title,
            start: result[i].start,
            end: result[i].end,
            color: result[i].color,
            url: result[i].url
        }); 	
    })
	var calendar = $('#calendar').fullCalendar({
		event: 'database_connection.php',
	    defaultView: 'month',
		 timeZone: 'local',
	    editable: true,
        selectable: true,
		selectHelper: true,
		eventClick: function(event) {
            // Menetapkan nilai modal berdasarkan data dari event yang diklik
            $('#event_name').val(event.name);
            $('#event_start_date').val(moment(event.start).format('YYYY-MM-DD'));
            $('#event_end_date').val(moment(event.end).format('YYYY-MM-DD'));

            // Menampilkan modal
            $('#event_entry_modal').modal('show');
        },
        //select: function(start, end) {
				//alert(start);
				//alert(end);
				//$('#event_start_date').val(moment(start).format('YYYY-MM-DD'));
				//$('#event_end_date').val(moment(end).format('YYYY-MM-DD'));
				//$('#event_entry_modal').modal('show');
			//},
        events: events,
		//eventRender: function(event, element, view) { 
          //  element.bind('click', function() {
			//		alert(event.event_id);
			//	});
    	//}
		}); //end fullCalendar block	
	  },//end success block
	  error: function (xhr, status) {
	  alert(response.msg);
	  }
	});//end ajax block	
}
</script>
</html> 