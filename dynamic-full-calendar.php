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
							  <input type="text" name="nama_peminjam" id="nama_peminjam" class="form-control" readonly>
							</div>
						</div>
						<div class="col-sm-12">  
							<div class="form-group">
							  <label for="nama_tempat">Nama Tempat</label>
							  <input type="text" name="nama_tempat" id="nama_tempat" class="form-control" readonly>
							</div>
						</div>
						<div class="col-sm-12">  
							<div class="form-group">
							  <label for="nama_kegiatan">Nama Kegiatan</label>
							  <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" readonly>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">  
							<div class="form-group">
							  <label for="event_start_date">Tanggal Awal</label>
							  <input type="date" name="event_start_date" id="event_start_date" class="form-control onlydatepicker" placeholder="Event start date" disabled>
							 </div>
						</div>
						<div class="col-sm-6">  
							<div class="form-group">
							  <label for="event_end_date">Tanggal Selesai</label>
							  <input type="date" name="event_end_date" id="event_end_date" class="form-control" placeholder="Event end date" disabled>
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

function display_events(dataCalender) {
    var Calenders = []; // Initialize empty array for events
    // Loop through each element in the dataCalender parameter
    for (var i = 0; i < dataCalender.length; i++) {
        var event = dataCalender[i];
        Calenders.push({
        	event_id: event.id, // Assuming you have these fields
            title: event.title,
			nama_tempat:event.nama_tempat,
			peminjam:event.peminjam,
            start: event.start,
            end: event.end,
            color: event.color,
            url: ''
        });
    }

    var calendar = $('#calendar').fullCalendar({
        defaultView: 'month',
        timeZone: 'local',
        editable: true,
        selectable: true,
        selectHelper: true,
        eventClick: function(event) {
            $('#nama_peminjam').val(event.peminjam);
            $('#nama_tempat').val(event.nama_tempat);
            $('#nama_kegiatan').val(event.title);
            $('#event_start_date').val(moment(event.start).format('YYYY-MM-DD'));
            $('#event_end_date').val(moment(event.end).format('YYYY-MM-DD'));
            $('#event_entry_modal').modal('show');
        },
        events: Calenders,
	});
}
display_events(<?php echo json_encode($dataKalender) ?>);
</script>
</html> 