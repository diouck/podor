@extends('geomlng.layout')
   
@section('content')



    <div class="row">
         
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Product</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('geomlng.index') }}"> Back</a>
            </div>
        </div>
    </div>
   
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
  
    <form action="{{ route('geomlng.update',$product->id) }}" method="POST">
        @csrf
        @method('PUT')
   
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" value="{{ $product->name }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Detail:</strong>
                    <textarea class="form-control" style="height:50px" name="detail" placeholder="Detail">{{ $product->detail }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Coord:</strong>
                    <textarea class="form-control" style="height:50px" id="coordinates" name="coord" placeholder="Coord">{{ $product->coord }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

         <div id="map"></div>
<div class="calculation-box">
  <p><strong>Les coordonnées du dernier objet déssiné : </strong></p>
  <div ></div>
</div>
   
    </form>


@endsection

@section('scripts')
<script type="text/javascript">
    

const lonCenter = 9.6042;
const latCenter = 0.3853;
const zoomLevel = 13;
const imagerieEsri = {
  version: 8,
  sources: {
    worldImagery: {
      type: "raster",
      tiles: ["https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"],
      tileSize: 256
    }
  },
  layers: [
    {
      id: "worldImagery",
      type: "raster",
      source: "worldImagery",
      minzoom: 0,
      maxzoom: 22
    }
  ]
};

mapboxgl.accessToken = "pk.eyJ1IjoiYWtlYW1hemFuIiwiYSI6InM2R2JJSkkifQ.jyvGadLX2QXVeMq4jwCncA";

var map = new mapboxgl.Map({
  container: "map", // container id
  style: imagerieEsri, //hosted style id
  center: {!!$product->coord!!}, // starting position
  zoom: zoomLevel, // starting zoom
  attributionControl: false,
  infoControl: false,
});

 

//Affichage de Signature
var signature = '<a target="_blank" href="https://miKollect.com"> With <span style="color:red;"> ❤ </span> by © miKollect SARL</a>';
var attribCont = new mapboxgl.AttributionControl({
  compact: false,
  customAttribution: signature,
});
//Add Attributions
map.addControl(attribCont,'bottom-right');

 

// Affichage boutons de navigations (Zoom +/-, Compass )
var nav = new mapboxgl.NavigationControl();
map.addControl(nav, 'top-left');

//Affichage boutton de Localisation de l'utilisateur
var trackUser = new mapboxgl.GeolocateControl({
    positionOptions: {
        enableHighAccuracy: true
    },
    trackUserLocation: true
});
map.addControl(trackUser,'top-left');

//Affichage boutton plein ecran
var fullScreen = new mapboxgl.FullscreenControl();
map.addControl(fullScreen, 'top-left');


//Affichage GeoCoder
var geocoder = new MapboxGeocoder({
  accessToken: mapboxgl.accessToken,
  types: 'poi',
  // see https://docs.mapbox.com/api/search/#geocoding-response-object for information about the schema of each response feature
  render: function(item) {
    // extract the item's maki icon or use a default
    var maki = item.properties.maki || 'marker';
    return ("<div class='geocoder-dropdown-item'><img class='geocoder-dropdown-icon' src='https://unpkg.com/@mapbox/maki@6.1.0/icons/" +maki +"-15.svg'><span class='geocoder-dropdown-text'>" +" "+item.text + '</span></div>');
  },
  mapboxgl: mapboxgl,
});
map.addControl(geocoder, 'top-right');


// Affichage de l'echelle
var scale = new mapboxgl.ScaleControl({
    maxWidth: 100,
    unit: 'imperial' //'imperial' , 'metric' or 'nautical' 
});
map.addControl(scale);

scale.setUnit('metric');

 
map.on('style.load', function() {
   
  var marker = new mapboxgl.Marker({
    draggable: true
    })
    .setLngLat({!!$product->coord!!})
    .addTo(map);
    
    function onDragEnd() {
    var lngLat = marker.getLngLat();
    coordinates.style.display = 'block';
    coordinates.innerHTML =
    '[' + lngLat.lng + ',' + lngLat.lat+']';
    }
 
marker.on('dragend', onDragEnd);
 
});

 

</script> 
@endsection