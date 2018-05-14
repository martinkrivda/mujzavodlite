<?php
http: // api.openweathermap.org/data/2.5/weather?lat=50.341838599999996&lon=16.1485639&appid=05bf066733309f6817b0a04cd21b7b8d

?>
<p id="geolocation"></p>
<script>
// Check browser support
if (typeof(Storage) !== "undefined") {
	var x = document.getElementById("geolocation");
    // Store
    getLocation();
    
} else {
    document.getElementById("geolocation").innerHTML = "no data";
}
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
   sessionStorage.setItem("latitude", position.coords.latitude);
    sessionStorage.setItem("longitude", position.coords.longitude);
}
</script>