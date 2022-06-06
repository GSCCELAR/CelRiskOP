var popup = null;
var mapLayer = null;
var validator=null;
var markerDireccion = null;
var gscIcon = L.icon({
    iconUrl: 'imagenes/marker.png',
    shadowUrl: 'imagenes/marker-shadow.png',
    draggable: true,
    shadowSize:   [40, 25],
    shadowAnchor: [4, 25],
    popupAnchor:  [2, -40],
    iconSize: [25,41],
    iconAnchor: [10,40]
}); 
var latlng = L.latLng(5.0625697,-75.4956072);



