let post_ids = document.querySelectorAll(".post_id");

post_ids.forEach(element => {
    let post_id = element.value;
    ((async () => {
        const res_data = await getData(post_id);
        const locations = res_data["map_data"];
        const settings = res_data["settings"];
        document.getElementById("map-" + post_id).style.height = settings.height + "px";
        let bounds = new google.maps.LatLngBounds();
        let map = new google.maps.Map(document.getElementById("map-" + post_id), {
            center: {
                lat: parseFloat(locations[0][1].lat),
                lng: parseFloat(locations[0][1].lng)
            },
            // scrollwheel: false,
            // scaleControl: false,
            streetViewControl: settings.streetViewControl,
            draggable: settings.draggable,
            mapTypeControl: settings.mapTypeControl,
            zoomControl: settings.zoomControl,
            rotateControl: settings.rotateControl,
            zoom: parseInt(settings.zoom)
        });
        for (let index = 0; index < locations.length; index++) {
            let infowindow = new google.maps.InfoWindow();
            const location = locations[index];
            let marker = new google.maps.Marker({
                position: new google.maps.LatLng(location[1].lat, location[1].lng),
                map: map
            });
            bounds.extend(marker.position);
            infowindow.setContent(location[0]);
            if (location[1].openInfoWindow == true) {
                infowindow.open(map, marker);
            }
            marker.addListener('click', function () {
                infowindow.open(map, marker);
            });
        }
        if (settings.zoom == 0) {
            map.fitBounds(bounds);
        }
    })()).catch(console.error);
});

/**
 * Get Data
 */
function getData(post_id) {
    return new Promise(function (resolve, reject) {
        let request = new XMLHttpRequest();
        request.open('POST', gmap_helper_obj.ajax_url, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
        request.send('action=get_gmap_data&post_id=' + post_id);
        request.onload = function () {
            if (this.status >= 200 && this.status < 400) {
                return resolve(JSON.parse(this.response));
            } else {
                console.log(this.response);
            }
        };
    });
}