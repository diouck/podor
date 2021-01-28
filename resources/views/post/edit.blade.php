@extends('app')
   
@section('content')

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
           <span class="brand-text font-weight-light">L'espace sur les formations géomatique en Afrique</span>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('post.index') }}">Retour</a></li> 
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="row">
           <!-- /.col-md-6 -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title m-0">Geolocalisation</h5>
              </div>
              <div class="card-body">
              <div id="map" style="width: auto;height: 500px"></div>
              </div>
            </div>
 
          </div> 
          <!-- /.col-md-6 -->
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title m-0">Type de formation</h5>
              </div>
              <div class="card-body">
 
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
  
    <form action="{{ route('post.update',$post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
       @method('PATCH')
   
                 <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" value="{{ $post->name }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Description:</strong>
                            <textarea class="form-control" style="height:50px" name="description" placeholder="Detail">{{ $post->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>city:</strong>
                            <textarea class="form-control" style="height:50px" name="city" placeholder="Detail">{{ $post->city }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12"style="height:50px;visibility: hidden;"  >
                        <div class="form-group">
                            <strong>Coords:</strong>
                            <textarea class="form-control"  id="calculated-area" name="coords" placeholder="Coords" readonly >{{ $post->coords }}</textarea>
                        </div>
                    </div>
         


                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

                
         
           
            </form> 
               </div>
            </div> 
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>









 
    

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
  center: {!!$post->coords!!}, // starting position
  zoom: zoomLevel, // starting zoom
  maxZoom:18,
  attributionControl: false,
  infoControl: false,
});


// Outils de dessin
var draw = new MapboxDraw({
  displayControlsDefault: false,
  controls: {
    point: true,
    line_string: false,
    polygon: false,
    trash: true,
  }
});

//Affichage de Signature
var signature = '<a target="_blank" href="https://miKollect.com"> With <span style="color:red;"> ❤ </span> by © miKollect SARL</a>';
var attribCont = new mapboxgl.AttributionControl({
  compact: false,
  customAttribution: signature,
});
//Add Attributions
map.addControl(attribCont,'bottom-right');


//Fonction Outils de dessin
map.addControl(draw, 'bottom-right');

map.on("draw.create", updateArea);
map.on("draw.delete", updateArea);
map.on("draw.update", updateArea);

function updateArea(e) {
  var data = draw.getAll();
  var answer = document.getElementById("calculated-area");
  if (data.features.length > 0) {
    //var area = turf.area(data);
    // restrict to area to 2 decimal points
    //var rounded_area = Math.round(area * 100) / 100;
    //answer.innerHTML ="<p><strong>" + rounded_area + "</strong></p><p>square meters</p>";
    var shapeRecup = data.features[data.features.length - 1].geometry.coordinates;
    answer.innerHTML =   JSON.stringify(shapeRecup)  ;    
    //console.log(shapeRecup);

  } else {
    answer.innerHTML = "";
    if (e.type !== "draw.delete")
      alert("Use the draw tools to draw a polygon!");
  }
}



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


//Minimap



//Affichage des Points, Ligne et Polygone
const dataPoint = "https://raw.githubusercontent.com/AKEAmazan/data/master/map%20(3).geojson";
const dataLigne = "https://raw.githubusercontent.com/AKEAmazan/data/master/ourData.geojson";
const dataPolygone = "https://raw.githubusercontent.com/AKEAmazan/data/master/ourData.geojson";

map.on('style.load', function() {
  

  
var geojson ={
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Point",
        "coordinates": {!!$post->coords!!}
      },
      "properties": {
        "crop_type": "{!!$post->name!!}"
      }
    } 
  ]
};
 
 map.addSource('fields', {
    "type": "geojson",
    "data": geojson
    });
     draw.add(geojson);
     map.on('draw.update' );

map.fitBounds(geojsonExtent(geojson),{padding: 13});
 
 

});

 


</script> 
@endsection