function initialize() {
    var input = document.getElementById('searchLocation');
    if (!input) {
        return;
    }
    let autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        document.getElementById('gmap_lat').value = place.geometry.location.lat();
        document.getElementById('gmap_lng').value = place.geometry.location.lng();
    });
}

google.maps.event.addDomListener(window, 'load', initialize);