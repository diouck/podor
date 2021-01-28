@extends('app')
 
@section('content')

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    
 
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="row">
        
          <!-- /.col-md-6 -->
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
<span class="brand-text font-weight-light">L'espace sur les formations géomatique en Afrique</span>
              </div>
              <div class="card-body"> 
                <div id="map" style="width: auto;height: 500px"></div>
              </div>
            </div>



            <div class="card">
              <div class="card-header">
                <h5 class="card-title m-0">Liste des formations</h5>
                <div align="right"> <a href="{{ route('post.create') }}" class="btn btn-primary">Ajouter une formation</a></div>
              </div>
              <div class="card-body">
                   @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                             <th>Nom</th>
                            <th>Description</th>
                             <th>Ville</th>
                             <th>Site Web</th>
                            <th width="280px">Action</th>
                        </tr>
                        @foreach ($posts as $post)
                        <tr>
                            <td>{{ ++$i }} 
                            <td>{{ $post->name }}</td>
                            <td>{{ $post->description }}</td>
                             <td>{{ $post->city }}</td>
                             <td>{{ $post->website }}</td>
                            <td>
                                <form action="{{ route('post.destroy',$post->id) }}" method="POST">
                   
                                    <a class="btn btn-info" href="{{ route('post.show',$post->id) }}">Voir</a>
                    
                                    <a class="btn btn-primary" href="{{ route('post.edit',$post->id) }}">Mettre à jour</a>
                   
                                    @csrf
                                    @method('DELETE')
                      
                                    <!--<button type="submit" class="btn btn-danger">Delete</button>-->
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                  
                    {!! $posts->links() !!}
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
    
 
const lonCenter = 4.4;
const latCenter =15.7;
const zoomLevel = 1.79;
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
  center: [lonCenter,latCenter], // starting position
  zoom: zoomLevel, // starting zoom
  attributionControl: false,
  infoControl: false,
 
 
});

 

//Affichage de Signature
var signature = '<a target="_blank" href="https://geoafrica.fr"> With <span style="color:red;"> ❤ </span> by © GeoAfrica</a>';
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


//Minimap



//Affichage des Points, Ligne et Polygone
const DataGeompt = "data/geompt.php"; 

map.on('style.load', function() {
 
   
  map.addSource("post", {
    type: "geojson",
    data: DataGeompt,
    generateId: true,
  });
 
 
 
  map.addLayer({
    "id": "point",
    "type": "circle",
    "source": "post",
    "filter": ["==", "$type", "Point"],
    'layout': {
      'visibility': 'visible'
    },
    "paint": {
      "circle-radius": 5,
      "circle-color": "red"
    }
  });  
});



//Popup de click
map.on('click', function(e) {
  var features = map.queryRenderedFeatures(e.point, {
    layers: ['point' ] // replace this with the name of the layer
  });

  if (!features.length) {
    return;
  }

  var feature = features[0];

  var popup = new mapboxgl.Popup({ offset: [0, -15] })
    .setLngLat(feature.geometry.coordinates)
    .setHTML(    feature.properties.name  + '</h3><p>' + feature.properties.description + '</p><p>' + feature.properties.city + '</p><p>' + feature.properties.website + '</p>')
    .addTo(map);
  //map.jumpTo({ center: feature.geometry.coordinates});
  map.flyTo({center: feature.geometry.coordinates,
    essential: true // this animation is considered essential with respect to prefers-reduced-motion
  });
  
});

</script> 
@endsection