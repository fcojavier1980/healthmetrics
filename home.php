<?php
require('config.php');
include WEB_LOCAL . 'common/helpers.php';
if(isset($_GET['modo_ruta'])){
	$modo = $_GET['modo_ruta'];
    if($modo == "run"){
      $modo = 'walk';
    }
}else{
	//drive
	//walk
	$modo = 'bicycle';
}
if(isset($_GET['latitud1_city']) && isset($_GET['longitud1_city']) && isset($_GET['latitud2_city']) && isset($_GET['longitud2_city'])){
	$geoapify_maps_url = "https://api.geoapify.com/v1/routing?waypoints=". $_GET['latitud1_city'].",". $_GET['longitud1_city']."|". $_GET['latitud2_city'].",". $_GET['longitud2_city']."&mode=". $modo ."&apiKey=3a8ac63a3bdb42949427a187d0ef3e5c";
	$geoapify_maps_json = file_get_contents($geoapify_maps_url);
	$geoapify_maps_array = json_decode($geoapify_maps_json, true);
		
	$distancia = $geoapify_maps_array['features'][0]['properties']['distance'];
  if($_GET['modo_ruta'] =="run"){
    $tiempo = $geoapify_maps_array['features'][0]['properties']['time'];
    $tiempo_fix = ($tiempo / 60) / 120;
    $distancia_fix = $distancia / 1000;
    $calorias = $tiempo_fix * 400;
  }if($_GET['modo_ruta'] =="bicycle"){
    $tiempo = $geoapify_maps_array['features'][0]['properties']['time'];
    $distancia_fix = $distancia / 1000;
    $tiempo_fix = ($tiempo / 60) / 60;    
    $calorias = $tiempo_fix * 500;
  }
  if($_GET['modo_ruta'] =="walk"){
      $tiempo = $geoapify_maps_array['features'][0]['properties']['time'];
      $distancia_fix = $distancia / 1000;
      $tiempo_fix = ($tiempo / 60) / 60; 
      $calorias = $tiempo_fix * 120;   
    }  
  if($_GET['modo_ruta'] =="drive"){
      $tiempo = $geoapify_maps_array['features'][0]['properties']['time'];
      $distancia_fix = $distancia / 1000;
      $tiempo_fix = ($tiempo / 60) / 60;  
      $calorias = $tiempo_fix * 70;   
    }    

	//p_($distancia);	
	$coordenadas = $geoapify_maps_array['features'][0]['geometry']['coordinates'][0];
	//p_($coordenadas);
	$js_array = json_encode($coordenadas);
}

if(isset($_GET['modo_ruta'])){
  $modo_js = $_GET['modo_ruta'];
}else{
  $modo_js = 'bicycle';
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Add a GeoJSON line</title>
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/v1.11.1/mapbox-gl.js"></script>
<link rel="stylesheet" href="styles.css">
<link href="https://api.mapbox.com/mapbox-gl-js/v1.11.1/mapbox-gl.css" rel="stylesheet" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css" integrity="sha384-VCmXjywReHh4PwowAiWNagnWcLhlEJLA5buUprzK8rxFgeH0kww/aWY76TfkUoSX" crossorigin="anonymous">

	</head>
		<body>
    <nav aria-label="breadcrumb" >
      <ol class="breadcrumb" style="background-color: #54a5ec;">
        <li class="breadcrumb-item active header-label" aria-current="page"><p class="header-label-color">HEALTH METRICS</p></li>
      </ol>
    </nav>      
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<!--
				<div class="autocomplete-container" id="autocomplete-container"></div>
				<div class="autocomplete-container" id="autocomplete-container-country"></div>
			-->
          <div class="mx-auto img-container" style="width: 400px;">
            <img src="images/icon-car.png" class="img-thumbnail img-activity" onclick="activityManagement(this);" onmouseover="activityManagementHover(this);" onmouseout="activityManagementOut(this);" id="drive">
            <img src="images/icon-walk.png" class="img-thumbnail img-activity" onclick="activityManagement(this);" onmouseover="activityManagementHover(this);" onmouseout="activityManagementOut(this);" id="walk">
            <img src="images/icon-bike.png" class="img-thumbnail img-activity" onclick="activityManagement(this);" onmouseover="activityManagementHover(this);" onmouseout="activityManagementOut(this);" id="bicycle">
            <img src="images/icon-run.png" class="img-thumbnail img-activity" onclick="activityManagement(this);" onmouseover="activityManagementHover(this);" onmouseout="activityManagementOut(this);" id="run">
          </div>

				<div class="autocomplete-container" id="autocomplete-container-city"></div>	
				<div class="autocomplete-container" id="autocomplete-container-city-arrival"></div>	
        <button type="submit" class="btn btn-success" id="real_button" style="display: none;">Ver ruta</button>
        <div class="btn btn-success" id="fake_button" onclick="showWarning();">Ver ruta</div>
        <div class="alert alert-warning" role="alert" style="display: none;">
          Debes seleccionar un Punto de llegada y otro de llegada para poder ver la ruta.
        </div>
				<input type="hidden" id="lat1_city" name="latitud1_city">
				<input type="hidden" id="lon1_city" name="longitud1_city">
				<input type="hidden" id="lat2_city" name="latitud2_city">
				<input type="hidden" id="lon2_city" name="longitud2_city">	
        <input type="hidden" id="id_modo_ruta" name="modo_ruta">

				<?php if(isset($distancia)){?>
        <div class="card" style="width: 18rem; margin-left: 10px;">
          <div class="card-body">
            <h5 class="card-title" style="font-weight: bold;">ESTADÍSTICAS</h5>
            <p class="card-text">DISTANCIA: <?= $distancia_fix ?><span> km.</span></p>
            <p class="card-text">TIEMPO: <?= round($tiempo_fix, 2) ?><span> horas.</span></p> 
            <p class="card-text">GASTO: <?= round($calorias, 2) ?><span> calorías.</span></p> 
          </div>
        </div>        
				<?php } ?>							
				<div id="mapita"></div>
				<div id="maparuta"></div>
			</form>	
		</body>
<script>

/////////////////////////////////// AUTOCOMPLETAR ///////////////////////////////////////

/* 
	The addressAutocomplete takes as parameters:
  - a container element (div)
  - callback to notify about address selection
  - geocoder options:
  	 - placeholder - placeholder text for an input element
     - type - location type
*/

<?php if(isset($js_array )){ ?>
	//var javascript_array = [];
	var javascript_array = JSON.parse('<?php echo $js_array ?>');
	var latitud1_city_pinta = '<?php echo $_GET['latitud1_city'] ?>';
	var longitud1_city_pinta = '<?php echo $_GET['longitud1_city'] ?>';
	pintaMapaRuta(javascript_array, longitud1_city_pinta, latitud1_city_pinta);
<?php }?>


$( document ).ready(function() {
  
  var initial_activity = "<?php echo $modo_js ?>";
  console.log(initial_activity);

  if(initial_activity == 'bicycle'){
    $('#id_modo_ruta').val('bicycle');
    $('#bicycle').css('background-color', '#c7dbec');
  }else if(initial_activity == 'drive'){
    $('#id_modo_ruta').val(initial_activity);
    $('#drive').css('background-color', '#c7dbec');
  }else if(initial_activity == 'walk'){
    $('#id_modo_ruta').val(initial_activity);
    $('#walk').css('background-color', '#c7dbec');
  }else if(initial_activity == 'run'){
    $('#id_modo_ruta').val('run');
    $('#run').css('background-color', '#c7dbec');
  }

});

function showWarning(){
  $('.alert-warning').show();
}

function activityManagement(elem){
  $('.img-activity').css('background-color', 'transparent');
  $('.img-activity').removeClass('selected-icon');
  $(elem).css('background-color', '#c7dbec');
  var actual_activity = $(elem)[0].id;
  //console.log(actual_activity);
  $('#id_modo_ruta').val('');
  $('#id_modo_ruta').val(actual_activity);
}
function activityManagementHover(elem){
  var coloractual = $(elem).css('background-color');
  if(coloractual == 'rgb(199, 219, 236)'){

  }else{
    $(elem).css('background-color', '#d0d0d0');
  }
}
function activityManagementOut(elem){
  var coloractual = $(elem).css('background-color');
  if(coloractual == 'rgb(199, 219, 236)'){

  }else{
    $(elem).css('background-color', 'transparent');
  }
}
function initialCityDataManagement(elem){
	/*
	var coord_lon_1 = elem.bbox[0];
	var coord_lat_1 = elem.bbox[1];
	var coord_lon_2 = elem.bbox[2];
	var coord_lat_2 = elem.bbox[3];



	var coord_lon_fixed = ((parseFloat(coord_lon_1) + parseFloat(coord_lon_2)) / 2);
	var coord_lat_fixed = ((parseFloat(coord_lat_1) + parseFloat(coord_lat_2)) / 2);
	*/
	//console.log('Longitud:'+coord_lon_1+' - Latitud:'+coord_lat_1);
	var coord_lon_simple = elem.properties.lon;
	var coord_lat_simple = elem.properties.lat;
	//console.log(coord_lon_simple);

	if(coord_lon_simple !=''){
		pintaMapaInitial(coord_lon_simple, coord_lat_simple);
	}
	$('#lon1_city').val(coord_lon_simple);
	$('#lat1_city').val(coord_lat_simple);

	

}


function arrivalCityDataManagement(elem2){
	var coord_lon_simple_arrival = elem2.properties.lon;
	var coord_lat_simple_arrival = elem2.properties.lat;


	$('#lon2_city').val(coord_lon_simple_arrival);
	$('#lat2_city').val(coord_lat_simple_arrival);

}


function addressAutocomplete(containerElement, callback, options, name) {
  // create input element
  var inputElement = document.createElement("input");
  inputElement.setAttribute("type", "text");
  inputElement.setAttribute("placeholder", options.placeholder);
  containerElement.appendChild(inputElement);

  // add input field clear button
  var clearButton = document.createElement("div");
  clearButton.classList.add("clear-button");
  addIcon(clearButton);
  clearButton.addEventListener("click", (e) => {
    e.stopPropagation();
    inputElement.value = '';
    callback(null);
    clearButton.classList.remove("visible");
    closeDropDownList();
  });
  containerElement.appendChild(clearButton);

  /* Current autocomplete items data (GeoJSON.Feature) */
  var currentItems;

  /* Active request promise reject function. To be able to cancel the promise when a new request comes */
  var currentPromiseReject;

  /* Focused item in the autocomplete list. This variable is used to navigate with buttons */
  var focusedItemIndex;

  /* Execute a function when someone writes in the text field: */
  inputElement.addEventListener("input", function(e) {
    var currentValue = this.value;

    /* Close any already open dropdown list */
    closeDropDownList();

    // Cancel previous request promise
    if (currentPromiseReject) {
      currentPromiseReject({
        canceled: true
      });
    }

    if (!currentValue) {
      clearButton.classList.remove("visible");
      return false;
    }

    // Show clearButton when there is a text
    clearButton.classList.add("visible");

    /* Create a new promise and send geocoding request */
    var promise = new Promise((resolve, reject) => {
      currentPromiseReject = reject;

      var apiKey = "3a8ac63a3bdb42949427a187d0ef3e5c";
      var url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(currentValue)}&limit=5&apiKey=${apiKey}`;
      
      if (options.type) {
      	url += `&type=${options.type}`;
      }

      fetch(url)
        .then(response => {
          // check if the call was successful
          if (response.ok) {
            response.json().then(data => resolve(data));
          } else {
            response.json().then(data => reject(data));
          }
        });
    });

    promise.then((data) => {
      currentItems = data.features;

      /*create a DIV element that will contain the items (values):*/
      var autocompleteItemsElement = document.createElement("div");
      autocompleteItemsElement.setAttribute("class", "autocomplete-items");
      containerElement.appendChild(autocompleteItemsElement);

      /* For each item in the results */
      data.features.forEach((feature, index) => {
        /* Create a DIV element for each element: */
        var itemElement = document.createElement("DIV");
        /* Set formatted address as item value */
        itemElement.innerHTML = feature.properties.formatted;

        /* Set the value for the autocomplete text field and notify: */
        itemElement.addEventListener("click", function(e) {
          inputElement.value = currentItems[index].properties.formatted;

          callback(currentItems[index]);

          /* Close the list of autocompleted values: */
          closeDropDownList();
        });

        autocompleteItemsElement.appendChild(itemElement);
      });
    }, (err) => {
      if (!err.canceled) {
        console.log(err);
      }
    });
  });

  /* Add support for keyboard navigation */
  inputElement.addEventListener("keydown", function(e) {
    var autocompleteItemsElement = containerElement.querySelector(".autocomplete-items");
    if (autocompleteItemsElement) {
      var itemElements = autocompleteItemsElement.getElementsByTagName("div");
      if (e.keyCode == 40) {
        e.preventDefault();
        /*If the arrow DOWN key is pressed, increase the focusedItemIndex variable:*/
        focusedItemIndex = focusedItemIndex !== itemElements.length - 1 ? focusedItemIndex + 1 : 0;
        /*and and make the current item more visible:*/
        setActive(itemElements, focusedItemIndex);
      } else if (e.keyCode == 38) {
        e.preventDefault();

        /*If the arrow UP key is pressed, decrease the focusedItemIndex variable:*/
        focusedItemIndex = focusedItemIndex !== 0 ? focusedItemIndex - 1 : focusedItemIndex = (itemElements.length - 1);
        /*and and make the current item more visible:*/
        setActive(itemElements, focusedItemIndex);
      } else if (e.keyCode == 13) {
        /* If the ENTER key is pressed and value as selected, close the list*/
        e.preventDefault();
        if (focusedItemIndex > -1) {
          closeDropDownList();
        }
      }
    } else {
      if (e.keyCode == 40) {
        /* Open dropdown list again */
        var event = document.createEvent('Event');
        event.initEvent('input', true, true);
        inputElement.dispatchEvent(event);
      }
    }
  });

  function setActive(items, index) {
    if (!items || !items.length) return false;

    for (var i = 0; i < items.length; i++) {
      items[i].classList.remove("autocomplete-active");
    }

    /* Add class "autocomplete-active" to the active element*/
    items[index].classList.add("autocomplete-active");

    // Change input value and notify
    inputElement.value = currentItems[index].properties.formatted;
    callback(currentItems[index]);
  }

  function closeDropDownList() {
    var autocompleteItemsElement = containerElement.querySelector(".autocomplete-items");
    if (autocompleteItemsElement) {
      containerElement.removeChild(autocompleteItemsElement);
    }

    focusedItemIndex = -1;
  }

  function addIcon(buttonElement) {
    var svgElement = document.createElementNS("http://www.w3.org/2000/svg", 'svg');
    svgElement.setAttribute('viewBox', "0 0 24 24");
    svgElement.setAttribute('height', "24");

    var iconElement = document.createElementNS("http://www.w3.org/2000/svg", 'path');
    iconElement.setAttribute("d", "M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z");
    iconElement.setAttribute('fill', 'currentColor');
    svgElement.appendChild(iconElement);
    buttonElement.appendChild(svgElement);
  }
  
    /* Close the autocomplete dropdown when the document is clicked. 
  	Skip, when a user clicks on the input field */
  document.addEventListener("click", function(e) {
    if (e.target !== inputElement) {
      closeDropDownList();
    } else if (!containerElement.querySelector(".autocomplete-items")) {
      // open dropdown list again
      var event = document.createEvent('Event');
      event.initEvent('input', true, true);
      inputElement.dispatchEvent(event);
    }
  });

}

/*
addressAutocomplete(document.getElementById("autocomplete-container"), (data) => {
  console.log("Selected option: ");
  console.log(data);
}, {
	placeholder: "Enter an address here"
}, 'address');

addressAutocomplete(document.getElementById("autocomplete-container-country"), (data) => {
  console.log("Selected country: ");
  console.log(data);
}, {
	placeholder: "Enter a country name here",
  type: "country"
}, 'country');
*/
addressAutocomplete(document.getElementById("autocomplete-container-city"), (datainitial) => {
  console.log("Selected city: ");
 
  initialCityDataManagement(datainitial);
}, {
	placeholder: "Introduce un punto de salida"
}, 'city');
addressAutocomplete(document.getElementById("autocomplete-container-city-arrival"), (dataarrival) => {
  console.log("Selected city arrival: ");
  arrivalCityDataManagement(dataarrival);
  $('.alert-warning').hide();
  $('#fake_button').hide();
  $('#real_button').show();
}, {
	placeholder: "Introduce un punto de llegada"
}, 'city-arrival');


/////////////////////////////////// PINTAR RUTA ///////////////////////////////////////


	// TO MAKE THE MAP APPEAR YOU MUST
	// ADD YOUR ACCESS TOKEN FROM
	// https://account.mapbox.com

	

function pintaMapaInitial(elem1, elem2){
	mapboxgl.accessToken = 'pk.eyJ1IjoiZmpncGd1YWRhbHVwZTQiLCJhIjoiY2tkZW4zaGc2MG5yZTJ4bnJ3a29kbXlsaiJ9.Dr1GlBinoJk8AE13xcNFlA';
	var mapita = new mapboxgl.Map({
	container: 'mapita',
	style: 'mapbox://styles/mapbox/streets-v11',
	center: [elem1, elem2],
	zoom: 15
	});
	
	mapita.on('load', function() {
		mapita.addSource('route', {
		'type': 'geojson',
			'data': {
			'type': 'Feature',
			'properties': {},
				'geometry': {
				'type': 'LineString',
				'coordinates': [ [-122.48369693756104, 37.83381888486939],
								[-122.48348236083984, 37.83317489144141],
								[-122.48339653015138, 37.83270036637107],
								[-122.48356819152832, 37.832056363179625],
								[-122.48404026031496, 37.83114119107971],
								[-122.48404026031496, 37.83049717427869],
								[-122.48348236083984, 37.829920943955045],
								[-122.48356819152832, 37.82954808664175],
								[-122.48507022857666, 37.82944639795659],
								[-122.48610019683838, 37.82880236636284],
								[-122.48695850372314, 37.82931081282506],
								[-122.48700141906738, 37.83080223556934],
								[-122.48751640319824, 37.83168351665737],
								[-122.48803138732912, 37.832158048267786],
								[-122.48888969421387, 37.83297152392784],
								[-122.48987674713133, 37.83263257682617],
								[-122.49043464660643, 37.832937629287755],
								[-122.49125003814696, 37.832429207817725],
								[-122.49163627624512, 37.832564787218985],
								[-122.49223709106445, 37.83337825839438],
								[-122.49378204345702, 37.83368330777276]
								]
				}
			}
		});
			mapita.addLayer({
			'id': 'route',
			'type': 'line',
			'source': 'route',
			'layout': {
			'line-join': 'round',
			'line-cap': 'round'
			},
			'paint': {
			'line-color': '#888',
			'line-width': 8
			}
		});
	});
	
}	

function pintaMapaRuta(coords, elem1b, elem2b){
	mapboxgl.accessToken = 'pk.eyJ1IjoiZmpncGd1YWRhbHVwZTQiLCJhIjoiY2tkZW4zaGc2MG5yZTJ4bnJ3a29kbXlsaiJ9.Dr1GlBinoJk8AE13xcNFlA';
	console.log(elem1b+' - '+elem2b);
	var maparuta = new mapboxgl.Map({
	container: 'maparuta',
	style: 'mapbox://styles/mapbox/streets-v11',
	center: [elem1b, elem2b],
	zoom: 15
	});
	/*
	 var coordenadas_finales = [ [-122.48369693756104, 37.83381888486939],
								[-122.48348236083984, 37.83317489144141],
								[-122.48339653015138, 37.83270036637107],
								[-122.48356819152832, 37.832056363179625],
								[-122.48404026031496, 37.83114119107971],
								[-122.48404026031496, 37.83049717427869],
								[-122.48348236083984, 37.829920943955045],
								[-122.48356819152832, 37.82954808664175],
								[-122.48507022857666, 37.82944639795659],
								[-122.48610019683838, 37.82880236636284],
								[-122.48695850372314, 37.82931081282506],
								[-122.48700141906738, 37.83080223556934],
								[-122.48751640319824, 37.83168351665737],
								[-122.48803138732912, 37.832158048267786],
								[-122.48888969421387, 37.83297152392784],
								[-122.48987674713133, 37.83263257682617],
								[-122.49043464660643, 37.832937629287755],
								[-122.49125003814696, 37.832429207817725],
								[-122.49163627624512, 37.832564787218985],
								[-122.49223709106445, 37.83337825839438],
								[-122.49378204345702, 37.83368330777276]
								];
	*/							

	//console.log(typeof coords);
	//console.log(typeof coordenadas_finales);
	//console.log(coords);
	//console.log(coordenadas_finales);								
	maparuta.on('load', function() {
		maparuta.addSource('route', {
		'type': 'geojson',
			'data': {
			'type': 'Feature',
			'properties': {},
				'geometry': {
				'type': 'LineString',
				'coordinates': coords
				}
			}
		});
			maparuta.addLayer({
			'id': 'route',
			'type': 'line',
			'source': 'route',
			'layout': {
			'line-join': 'round',
			'line-cap': 'round'
			},
			'paint': {
			'line-color': '#888',
			'line-width': 8
			}
		});
	});

}

</script>

</html>