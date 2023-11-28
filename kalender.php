<!DOCTYPE html>
<html>
    <head>

        <!-- CSS for full calender -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
        <!-- JS for jQuery -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- JS for full calender -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>

    </head>
    <body>
        <div id="calendar"></div>
    </body>
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
            defaultView: 'month',
            events: [
              {
                title: 'Konser',
                start: '2023-11-21' // change with current date
                
              },
              {
                title: 'Siraman',
                start: '2023-11-05'
              },
              {
                title: 'Guyon Waton',
                start: '2023-11-23'
                
              }
              ] 
            });
        });
        </script>
</html> 