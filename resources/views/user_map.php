<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/head.php'; ?>
    <body>
        <div id="wrapper">

            <?php include 'includes/header.php'; ?>

            <main id="main">

                <?php include 'includes/sidebar.php'; ?>
                <div id="content">
                    <header class="header border">
                        <ul class="breadcrumbs list-none">
                            <li><a href="<?= asset('/adminlogin') ?>">Dashboard</a></li>
                            <li>User Map</li>
                        </ul>
                        <a href="#" class="btn-sidebar">&#9776;</a>
                    </header>
                    <div class="content-area p-details">
                        <div id="map" style="height: 400px;width: 1200px"></div>
                    </div>
                </div>
            </main>

        </div>
        <?php include 'includes/footer.php'; ?>
    </body>
    <script>
        $(document).ready(function () {
            $('#myTableUser').DataTable();
        });
        var locations = <?php echo json_encode($users); ?>;
        var map;
        var markers = [];

        function initMap() {

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 2,
                center: new google.maps.LatLng(41.850033, -87.6500523),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var marker, i;
            for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i].lat, locations[i].lng),
                    map: map,
                    title: locations[i].username
                });
                markers.push(marker);
                var userId = locations[i].id;
                var username = locations[i].username;
                var photo = locations[i].photo;
                var email = locations[i].email;
                var location = locations[i].location;

                var content = '<div class="marker-content-container"><a class="clickable" href="user_details/'+userId+'"><img class="marker-thumbnail" src='+photo+'></a><br><a class="clickable" href="user_details/'+userId+'">'+username+'</a>'+'<br><a class="clickable" href="user_details/'+userId+'">'+email+'</a>'+'<br><span>'+location+'</span></div>';
                var infowindow = new google.maps.InfoWindow();
                google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                    return function () {
                        infowindow.setContent(content);
                        infowindow.open(map, marker);
                    };
                })(marker, content, infowindow));

            }
        }
        function addMarkerInfo(location) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(location.hotspot_lat, location.hotspot_long),
                map: map
            });
            markers.push(marker);
        }
        function deleteMarkers() {
            clearMarkers();
            markers = [];
        }
        function clearMarkers() {
            setMapOnAll(null);
        }
        function setMapOnAll(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7l5RqKcybbGrvSVnI2siEFFuv-VqkuZY&callback=initMap">
    </script>
</html>