<!DOCTYPE html>
<html>
<head>
    <title>Reservation Notice for {{ $user }}</title>
</head>
<body>
    <h1 class="text-orange-500">Reservation {{ $itemName }} {{ $reminder_type }} </h1>
    <p>Your reservation is almost there</p>
    <p>at {{ $reservation_time }}</p>
    
    <span>This notice will be triggered every 30 and 10 minutes close to the reservation. Thank you!</span>
</body>
</html>