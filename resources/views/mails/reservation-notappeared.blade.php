<!DOCTYPE html>
<html>
<head>
    <title>Reservation Unattended</title>
</head>
<body>
    <h1 class="text-red-500">Your Reservation {{ $itemName }}  Has Been Considered Unattended</h1>
    <p>As you have agreed to our terms and conditions before the Reservarion, there will be no refund for <strong>{{ $itemName }}</strong></p>
    <span>Date: {{ $day }}, {{ $time }}</span>
</body>
</html>