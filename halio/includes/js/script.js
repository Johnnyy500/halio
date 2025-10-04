var map,
    pickUpMap,
    dropOffMap,
    geocoder,
    directionsRequest,
    directionsDisplay,
    mapDrawingManager,
    pickUpDrawingManager,
    dropOffDrawingManager,
    acceptanceRegionSelectedShape,
    pickUpSelectedShape,
    dropOffSelectedShape,
    originInputSelector,
    destinationInputSelector,
    mapGeometry,
    originalPolygon,
    puOriginalPolygon,
    doOriginalPolygon,
    onPpcEditPage,
    mapNewShape,
    pickUpNewShape,
    dropOffNewShape,
    originMarker,
    destinationMarker,
    canDragOriginMarker,
    canDragDestinationMarker,
    startingPosition = { lat: 53.794314, lng: -1.545982 },
    startingAutocomplete,
    destinationAutocomplete;

function initMap() {
  // Started breaking if this wasn't inside this function
  var polygonOptions = {
    fillColor: '#ffff00',
    fillOpacity: .5,
    strokeWeight: 5,
    clickable: true,
    editable: true,
    zIndex: 1
  };

  // Custom function to get polygon bounds as google maps doesn't support it
  google.maps.Polygon.prototype.getBounds = function() {
    var bounds = new google.maps.LatLngBounds();
    var paths = this.getPaths();
    var path;
    for (var i = 0; i < paths.getLength(); i++) {
      path = paths.getAt(i);

      for (var j = 0; j < path.getLength(); j++) {
        bounds.extend(path.getAt(j));
      }
    }
    return bounds;
  }

  // Can drag the origin/destination marker if they aren't using fixed addresses
  canDragOriginMarker = jQuery('#HalioStartingAddress').length > 0;
  canDragDestinationMarker = jQuery('#HalioDestinationAddress').length > 0;

  if ( jQuery('#map').length > 0 ) {
    // Page with front-facing form on it
    if ( jQuery('#HalioUnitSystem').val() == 'miles' ) {
      var unitSystem = google.maps.UnitSystem.IMPERIAL;
    } else {
      var unitSystem = google.maps.UnitSystem.METRIC;
    }

    if ( jQuery('#HalioStartingAddress').length > 0 ) {
      originInputSelector = '#HalioStartingAddress';
    } else {
      originInputSelector = '#HalioStartingAddressSelect';
    }

    if ( jQuery('#HalioDestinationAddress').length > 0 ) {
      destinationInputSelector = '#HalioDestinationAddress';
    } else {
      destinationInputSelector = '#HalioDestinationAddressSelect';
    }

    directionsRequest = {
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: unitSystem
    };
    geocoder = new google.maps.Geocoder();

    map = new google.maps.Map(document.getElementById('map'), {
      center: startingPosition,
      zoom: 5
    });

    var startingCountry = jQuery('#HalioMapStartingCountry').val();
    geocoder.geocode({ 'address': startingCountry }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        map.fitBounds(results[0].geometry.viewport);
      }
    });

    directionsDisplay = new google.maps.DirectionsRenderer({
      map: map,
      suppressMarkers: true,
      preserveViewport: true
    });

    var autocompleteOptions = {};

    if ( jQuery('#HalioAutocompleteRestriction').length > 0 ) {
      autocompleteOptions['componentRestrictions'] = {
        country: jQuery('#HalioAutocompleteRestriction').val()
      };
    }

    if ( jQuery('#HalioStartingAddress').length > 0 ) {
      // If user can type starting address
      startingAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById('HalioStartingAddress'),
        autocompleteOptions
      );

      google.maps.event.addListener(startingAutocomplete, 'place_changed', function() {
        jQuery('#HalioStartingAddress').siblings('.halio-overlay').children('.letter').addClass('green');
        jQuery('#HalioStartingAddress').parent().removeClass('has-error');

        var startingGooglePlace = startingAutocomplete.getPlace();

        if ( startingGooglePlace.hasOwnProperty('place_id') && startingGooglePlace.place_id.length > 0 ) {
          var startingGeocodeOptions = { 'placeId': startingGooglePlace.place_id };
        } else {
          var startingGeocodeOptions = { 'address': jQuery(originInputSelector).val() };
        }

        geocoder.geocode(startingGeocodeOptions, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            var latLng = { lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng() };

            jQuery('#HalioStartingLat').val(latLng.lat);
            jQuery('#HalioStartingLong').val(latLng.lng);

            if ( originMarker ) {
              originMarker.setMap(null);
            }

            originMarker = new google.maps.Marker({
              position: latLng,
              map: map,
              title: results[0].formatted_address,
              draggable: canDragOriginMarker,
              animation: google.maps.Animation.DROP
            });

            if ( jQuery('.halio-marker-helper-container').is(':hidden')  ) {
              jQuery('.halio-marker-helper-container').show();
            }

            originMarker.addListener('dragend',function(event) {

              // Write new address in address field
              var latLng = { lat: event.latLng.lat(), lng: event.latLng.lng() };
              geocoder.geocode({ 'latLng': latLng }, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK && results[1]) {
                  if (results[1]) {
                    jQuery('#HalioStartingAddress').val("Dropped pin: " + results[1].formatted_address);
                  }
                } else {
                  console.log("Geocode was not successful for the following reason: " + status);
                }
              });

              jQuery('#HalioStartingLat').val(latLng.lat);
              jQuery('#HalioStartingLong').val(latLng.lng);

              setDirectionsOnMap(true);
            });

            // If destination coords set
            if ( jQuery('#HalioDestinationLat').val().length > 0 && jQuery('#HalioDestinationLong').val().length > 0 ) {
              setDirectionsOnMap(false);
            }
          }
        });
      });

      google.maps.event.addDomListener(document.getElementById('HalioStartingAddress'), 'keydown', function(e) {
        if (e.keyCode == 13) {
          e.preventDefault();
          jQuery('#HalioDestinationAddress').focus();
        }
      });
    } else {
      // If using Fixed Addreses for starting address
      jQuery('#HalioStartingAddressSelect').change(function() {
        jQuery(this).siblings('.halio-overlay').children('.letter').addClass('green');

        geocoder.geocode({ 'address': jQuery(originInputSelector).val() }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            var latLng = { lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng() };

            jQuery('#HalioStartingLat').val(latLng.lat);
            jQuery('#HalioStartingLong').val(latLng.lng);

            if ( originMarker ) {
              originMarker.setMap(null);
            }

            originMarker = new google.maps.Marker({
              position: latLng,
              map: map,
              animation: google.maps.Animation.DROP
            });

            // If destination coords set
            if ( jQuery('#HalioDestinationLat').val().length > 0 && jQuery('#HalioDestinationLong').val().length > 0 ) {
              setDirectionsOnMap(false);
            }
          }
        });
      });
    }

    if ( jQuery('#HalioDestinationAddress').length > 0 ) {
      // If user can type destination address
      destinationAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById('HalioDestinationAddress'),
        autocompleteOptions
      );

      google.maps.event.addListener(destinationAutocomplete, 'place_changed', function() {
        jQuery('#HalioDestinationAddress').siblings('.halio-overlay').children('.letter').addClass('green');
        jQuery('#HalioDestinationAddress').parent().removeClass('has-error');

        var destinationGooglePlace = destinationAutocomplete.getPlace();

        if ( destinationGooglePlace.hasOwnProperty('place_id') && destinationGooglePlace.place_id.length > 0 ) {
          var destinationGeocodeOptions = { 'placeId': destinationGooglePlace.place_id };
        } else {
          var destinationGeocodeOptions = { 'address': jQuery(destinationInputSelector).val() };
        }

        geocoder.geocode(destinationGeocodeOptions, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            var latLng = { lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng() };

            jQuery('#HalioDestinationLat').val(latLng.lat);
            jQuery('#HalioDestinationLong').val(latLng.lng);

            if ( destinationMarker ) {
              destinationMarker.setMap(null);
            }

            destinationMarker = new google.maps.Marker({
              position: latLng,
              map: map,
              title: results[0].formatted_address,
              draggable: canDragDestinationMarker,
              animation: google.maps.Animation.DROP
            });

            if ( jQuery('.halio-marker-helper-container').is(':hidden')  ) {
              jQuery('.halio-marker-helper-container').show();
            }

            destinationMarker.addListener('dragend', function(event) {

              var latLng = { lat: event.latLng.lat(), lng: event.latLng.lng() };
              geocoder.geocode({ 'latLng': latLng }, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK && results[1]) {
                  if (results[1]) {
                    jQuery('#HalioDestinationAddress').val("Dropped pin: " + results[1].formatted_address);
                  }
                } else {
                  console.log("Geocode was not successful for the following reason: " + status);
                }
              });

              jQuery('#HalioDestinationLat').val(latLng.lat);
              jQuery('#HalioDestinationLong').val(latLng.lng);

              setDirectionsOnMap(true);
            });

            // If starting coords set
            if ( jQuery('#HalioStartingLat').val().length > 0 && jQuery('#HalioStartingLong').val().length > 0 ) {
              setDirectionsOnMap(false);
            }
          } else {
            console.log("Geocode was not successful for the following reason: " + status);
          }
        });
      });

      google.maps.event.addDomListener(document.getElementById('HalioDestinationAddress'), 'keydown', function(e) {
        if (e.keyCode == 13) {
          e.preventDefault();
        }
      });
    } else {
      // If using Fixed Addreses for destination address
      jQuery('#HalioDestinationAddressSelect').change(function() {
        jQuery(this).siblings('.halio-overlay').children('.letter').addClass('green');

        geocoder.geocode({ 'address': jQuery(destinationInputSelector).val() }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            var latLng = { lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng() };

            jQuery('#HalioDestinationLat').val(latLng.lat);
            jQuery('#HalioDestinationLong').val(latLng.lng);

            if ( destinationMarker ) {
              destinationMarker.setMap(null);
            }

            destinationMarker = new google.maps.Marker({
              position: latLng,
              map: map,
              animation: google.maps.Animation.DROP
            });

            // If starting coords set
            if ( jQuery('#HalioStartingLat').val().length > 0 && jQuery('#HalioStartingLong').val().length > 0 ) {
              setDirectionsOnMap(false);
            }
          }
        });
      });
    }

  } else if ( jQuery('#polygon_pricing_condition_pick_up_map').length > 0 ) {
    // On Polygon Pricing condition page

    // Needs to be declared here as google.maps.drawing needs to be defined
    var drawingManagerOptions = {
      drawingMode: google.maps.drawing.OverlayType.POLYGON,
      drawingControl: true,
      drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_CENTER,
        drawingModes: [
          google.maps.drawing.OverlayType.POLYGON
        ]
      },
      polygonOptions: polygonOptions
    };

    onPpcEditPage = jQuery('#polygon_pricing_condition_pick_up_map').attr('data-edit') == 'true';

    pickUpMap = new google.maps.Map(document.getElementById('polygon_pricing_condition_pick_up_map'), {
      center: startingPosition,
      zoom: 8
    });

    dropOffMap = new google.maps.Map(document.getElementById('polygon_pricing_condition_drop_off_map'), {
      center: startingPosition,
      zoom: 8
    });

    geocoder = new google.maps.Geocoder();

    var startingCountry = jQuery('#HalioMapStartingCountry').val();

    // If user is on edit page it will be centered around the polygon so this
    // would be a wasted API call
    if ( !onPpcEditPage ) {
      geocoder.geocode({ 'address': startingCountry }, function(results, status) {
        // If status OK and not on edit page, centre around default country
        if (status == google.maps.GeocoderStatus.OK && !onPpcEditPage ) {
          mapGeometry = results[0].geometry;

          pickUpMap.setCenter(results[0].geometry.location);
          pickUpMap.fitBounds(results[0].geometry.viewport);
          dropOffMap.setCenter(results[0].geometry.location);
          dropOffMap.fitBounds(results[0].geometry.viewport);
        }
      });
    }

    pickUpDrawingManager = new google.maps.drawing.DrawingManager(drawingManagerOptions);
    dropOffDrawingManager = new google.maps.drawing.DrawingManager(drawingManagerOptions);

    google.maps.event.addListener(pickUpDrawingManager, 'polygoncomplete', function(e) {
      // Do not let user draw another shape
      pickUpDrawingManager.setOptions({
        drawingControl: false,
        drawingMode: null
      });

      addCoordsToInput(e, '.halio__ppc--pick-up-coordinates');

      // Add an event listener that selects the newly-drawn shape when the user
      // mouses down on it.
      pickUpNewShape = e;
      pickUpNewShape.type = e.type;

      google.maps.event.addListener(pickUpNewShape.getPath(), 'set_at', function() {
        addCoordsToInput(e, '.halio__ppc--pick-up-coordinates');
      });

      google.maps.event.addListener(pickUpNewShape.getPath(), 'insert_at', function() {
        addCoordsToInput(e, '.halio__ppc--pick-up-coordinates');
      });

      google.maps.event.addListener(pickUpNewShape, 'click', function() {
        pickUpSetSelection(pickUpNewShape);
      });

      pickUpSetSelection(pickUpNewShape);
    });

    google.maps.event.addListener(dropOffDrawingManager, 'polygoncomplete', function(e) {
      // Do not let user draw another shape
      dropOffDrawingManager.setOptions({
        drawingControl: false,
        drawingMode: null
      });

      addCoordsToInput(e, '.halio__ppc--drop-off-coordinates');

      // Add an event listener that selects the newly-drawn shape when the user
      // mouses down on it.
      dropOffNewShape = e;
      dropOffNewShape.type = e.type;

      google.maps.event.addListener(dropOffNewShape.getPath(), 'set_at', function() {
        addCoordsToInput(e, '.halio__ppc--drop-off-coordinates');
      });

      google.maps.event.addListener(dropOffNewShape.getPath(), 'insert_at', function() {
        addCoordsToInput(e, '.halio__ppc--drop-off-coordinates');
      });

      google.maps.event.addListener(dropOffNewShape, 'click', function() {
        dropOffSetSelection(dropOffNewShape);
      });

      dropOffSetSelection(dropOffNewShape);
    });

    google.maps.event.addListener(pickUpMap, 'click', pickUpClearSelection);
    google.maps.event.addListener(dropOffMap, 'click', dropOffClearSelection);
    google.maps.event.addDomListener(document.getElementById('ppc-pick-up-delete-shape'), 'click', function(e) {
      e.preventDefault();
      pickUpDeleteSelectedShape();

      // Allow user to draw again when shape deleted
      pickUpDrawingManager.setOptions({
        drawingControl: true
      });
    });
    google.maps.event.addDomListener(document.getElementById('ppc-drop-off-delete-shape'), 'click', function(e) {
      e.preventDefault();
      dropOffDeleteSelectedShape();

      // Allow user to draw again when shape deleted
      dropOffDrawingManager.setOptions({
        drawingControl: true
      });
    });

    pickUpDrawingManager.setMap(pickUpMap);
    dropOffDrawingManager.setMap(dropOffMap);

    if ( onPpcEditPage ) {
      if ( jQuery('#HalioEditPPCPickUpCoordinates').val().length > 0 ) {
        var pu_coords = jQuery('#HalioEditPPCPickUpCoordinates').val().split('|');
      } else {
        var pu_coords = [];
      }

      if ( jQuery('#HalioEditPPCDropOffCoordinates').val().length > 0 ) {
        var do_coords = jQuery('#HalioEditPPCDropOffCoordinates').val().split('|');
      } else {
        var do_coords = [];
      }

      var puFormattedCoords = [];
      var doFormattedCoords = [];

      for (var i = 0; i < pu_coords.length; i++) {
        puFormattedCoords.push({
          lat: parseFloat(pu_coords[i].split(',')[0]),
          lng: parseFloat(pu_coords[i].split(',')[1])
        });
      }

      for (var i = 0; i < do_coords.length; i++) {
        doFormattedCoords.push({
          lat: parseFloat(do_coords[i].split(',')[0]),
          lng: parseFloat(do_coords[i].split(',')[1])
        });
      }

      // JSON.parse(JSON.stringify()) done to ensure new object is not a
      // reference to polygonOptions
      var pickUpPolygonOptions = JSON.parse(JSON.stringify(polygonOptions));
      var dropOffPolygonOptions = JSON.parse(JSON.stringify(polygonOptions));

      pickUpPolygonOptions['paths'] = puFormattedCoords;
      dropOffPolygonOptions['paths'] = doFormattedCoords;

      puOriginalPolygon = new google.maps.Polygon(pickUpPolygonOptions);
      doOriginalPolygon = new google.maps.Polygon(dropOffPolygonOptions);

      puOriginalPolygon.setMap(pickUpMap);
      doOriginalPolygon.setMap(dropOffMap);

      pickUpMap.fitBounds(puOriginalPolygon.getBounds());
      dropOffMap.fitBounds(doOriginalPolygon.getBounds());

      // When a vertex of the polygon is moved
      google.maps.event.addListener(puOriginalPolygon.getPath(), 'set_at', function(e) {
        addCoordsToInput(puOriginalPolygon, '.halio__ppc--pick-up-coordinates');
      });

      // When a new vertex is added to the polygon
      google.maps.event.addListener(puOriginalPolygon.getPath(), 'insert_at', function(e) {
        addCoordsToInput(puOriginalPolygon, '.halio__ppc--pick-up-coordinates');
      });

      google.maps.event.addListener(puOriginalPolygon, 'click', function() {
        pickUpSetSelection(puOriginalPolygon);
      });

      // When a vertex of the polygon is moved
      google.maps.event.addListener(doOriginalPolygon.getPath(), 'set_at', function(e) {
        addCoordsToInput(doOriginalPolygon, '.halio__ppc--drop-off-coordinates');
      });

      // When a new vertex is added to the polygon
      google.maps.event.addListener(doOriginalPolygon.getPath(), 'insert_at', function(e) {
        addCoordsToInput(doOriginalPolygon, '.halio__ppc--drop-off-coordinates');
      });

      google.maps.event.addListener(doOriginalPolygon, 'click', function() {
        dropOffSetSelection(doOriginalPolygon);
      });

      pickUpSetSelection(puOriginalPolygon);
      dropOffSetSelection(doOriginalPolygon);

      addCoordsToInput(puOriginalPolygon, '.halio__ppc--pick-up-coordinates');
      addCoordsToInput(doOriginalPolygon, '.halio__ppc--drop-off-coordinates');

      pickUpDrawingManager.setOptions({
        drawingControl: false,
        drawingMode: null
      });
      dropOffDrawingManager.setOptions({
        drawingControl: false,
        drawingMode: null
      });
    }
  } else if ( jQuery('#HalioNewFixedAddressAddress').length > 0 ) {
    var autocompleteInput = document.getElementById('HalioNewFixedAddressAddress');

    startingAutocomplete = new google.maps.places.Autocomplete(
      autocompleteInput,
      { types: ['geocode'] }
    );

    google.maps.event.addDomListener(autocompleteInput, 'keydown', function(e) {
      if (e.keyCode == 13) {
        e.preventDefault();
      }
    });
  } else if ( jQuery('#checkout_map').length > 0 || jQuery('#thankyou_map').length > 0 ) {
    var mapID = jQuery('#checkout_map').length > 0 ? 'checkout_map' : 'thankyou_map';
    map = new google.maps.Map(document.getElementById(mapID), {
      center: startingPosition,
      zoom: 5,
      mapTypeControl: false
    });

    directionsRequest = {
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.METRIC
    };

    geocoder = new google.maps.Geocoder();
    directionsDisplay = new google.maps.DirectionsRenderer({
      map: map
    });

    originInputSelector = '#halio_starting_address';
    destinationInputSelector = '#halio_destination_address';

    setDirectionsOnMap(false);
  } else if ( jQuery('#halio_order_origin_map').length > 0 ) {
    var originLatLng = {
      lat: parseFloat(jQuery('#HalioStartingLat').val()),
      lng: parseFloat(jQuery('#HalioStartingLong').val())
    };

    var destinationLatLng = {
      lat: parseFloat(jQuery('#HalioDestinationLat').val()),
      lng: parseFloat(jQuery('#HalioDestinationLong').val())
    };

    map = new google.maps.Map(document.getElementById('halio_order_origin_map'), {
      center: originLatLng,
      zoom: 17
    });

    var map2 = new google.maps.Map(document.getElementById('halio_order_destination_map'), {
      center: destinationLatLng,
      zoom: 17
    });

    var originMarker = new google.maps.Marker({
      position: originLatLng,
      map: map
    });

    var destinationMarker = new google.maps.Marker({
      position: destinationLatLng,
      map: map2
    });
  } else if ( jQuery('#acceptance_region_map').length > 0 ) {
    // Needs to be declared here as google.maps.drawing needs to be defined
    var drawingManagerOptions = {
      drawingMode: google.maps.drawing.OverlayType.POLYGON,
      drawingControl: true,
      drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_CENTER,
        drawingModes: [
          google.maps.drawing.OverlayType.POLYGON
        ]
      },
      polygonOptions: polygonOptions
    };

    map = new google.maps.Map(document.getElementById('acceptance_region_map'), {
      center: startingPosition,
      zoom: 8
    });

    geocoder = new google.maps.Geocoder();
    var startingCountry = jQuery('#HalioMapStartingCountry').val();

    geocoder.geocode({ 'address': startingCountry }, function(results, status) {
      // If status OK and not on edit page, centre around default country
      if (status == google.maps.GeocoderStatus.OK ) {
        mapGeometry = results[0].geometry;

        map.setCenter(results[0].geometry.location);
        map.fitBounds(results[0].geometry.viewport);
      }
    });

    mapDrawingManager = new google.maps.drawing.DrawingManager(drawingManagerOptions);

    google.maps.event.addListener(mapDrawingManager, 'polygoncomplete', function(e) {
      // Do not let user draw another shape
      mapDrawingManager.setOptions({
        drawingControl: false,
        drawingMode: null
      });

      addCoordsToInput(e, '.halio__setting--acceptance-region');

      // Add an event listener that selects the newly-drawn shape when the user
      // mouses down on it.
      mapNewShape = e;
      mapNewShape.type = e.type;

      google.maps.event.addListener(mapNewShape.getPath(), 'set_at', function() {
        addCoordsToInput(e, '.halio__setting--acceptance-region');
      });

      google.maps.event.addListener(mapNewShape.getPath(), 'insert_at', function() {
        addCoordsToInput(e, '.halio__setting--acceptance-region');
      });

      google.maps.event.addListener(mapNewShape, 'click', function() {
        acceptanceRegionSetSelection(mapNewShape);
      });

      acceptanceRegionSetSelection(mapNewShape);
    });

    google.maps.event.addListener(map, 'click', acceptanceRegionClearSelection);
    google.maps.event.addDomListener(document.getElementById('acceptance-region-delete-shape'), 'click', function(e) {
      e.preventDefault();
      acceptanceRegionDeleteSelectedShape();

      // Allow user to draw again when shape deleted
      mapDrawingManager.setOptions({
        drawingControl: true
      });
    });

    mapDrawingManager.setMap(map);

    if ( jQuery('.halio__setting--acceptance-region').val().length > 0 ) {
      var formattedCoords = [];
      var map_coords = jQuery('.halio__setting--acceptance-region').val().split('|');

      for (var i = 0; i < map_coords.length; i++) {
        formattedCoords.push({
          lat: parseFloat(map_coords[i].split(',')[0]),
          lng: parseFloat(map_coords[i].split(',')[1])
        });
      }

      // JSON.parse(JSON.stringify()) done to ensure new object is not a
      // reference to polygonOptions
      var polygonOptions = JSON.parse(JSON.stringify(polygonOptions));

      polygonOptions['paths'] = formattedCoords;

      originalPolygon = new google.maps.Polygon(polygonOptions);
      originalPolygon.setMap(map);
      map.fitBounds(originalPolygon.getBounds());

      // When a vertex of the polygon is moved
      google.maps.event.addListener(originalPolygon.getPath(), 'set_at', function(e) {
        addCoordsToInput(originalPolygon, '.halio__setting--acceptance-region');
      });

      // When a new vertex is added to the polygon
      google.maps.event.addListener(originalPolygon.getPath(), 'insert_at', function(e) {
        addCoordsToInput(originalPolygon, '.halio__setting--acceptance-region');
      });

      google.maps.event.addListener(originalPolygon, 'click', function() {
        acceptanceRegionSetSelection(originalPolygon);
      });

      acceptanceRegionSetSelection(originalPolygon);
      addCoordsToInput(originalPolygon, '.halio__setting--acceptance-region');

      mapDrawingManager.setOptions({
        drawingControl: false,
        drawingMode: null
      });
    }
  }
}

function acceptanceRegionClearSelection() {
  if (acceptanceRegionSelectedShape) {
    acceptanceRegionSelectedShape.setEditable(false);
    acceptanceRegionSelectedShape = null;
  }
}

function pickUpClearSelection() {
  if (pickUpSelectedShape) {
    pickUpSelectedShape.setEditable(false);
    pickUpSelectedShape = null;
  }
}

function dropOffClearSelection() {
  if (dropOffSelectedShape) {
    dropOffSelectedShape.setEditable(false);
    dropOffSelectedShape = null;
  }
}

function acceptanceRegionSetSelection(shape) {
  acceptanceRegionClearSelection();
  acceptanceRegionSelectedShape = shape;
  shape.setEditable(true);
}

function pickUpSetSelection(shape) {
  pickUpClearSelection();
  pickUpSelectedShape = shape;
  shape.setEditable(true);
}

function dropOffSetSelection(shape) {
  dropOffClearSelection();
  dropOffSelectedShape = shape;
  shape.setEditable(true);
}

function acceptanceRegionDeleteSelectedShape() {
  if (acceptanceRegionSelectedShape) {
    acceptanceRegionSelectedShape.setMap(null);
  }

  jQuery('.halio__setting--acceptance-region').val('');
}

function pickUpDeleteSelectedShape() {
  if (pickUpSelectedShape) {
    pickUpSelectedShape.setMap(null);
  }

  jQuery('.halio__ppc--pick-up-coordinates').val('');
}

function dropOffDeleteSelectedShape() {
  if (dropOffSelectedShape) {
    dropOffSelectedShape.setMap(null);
  }

  jQuery('.halio__ppc--drop-off-coordinates').val('');
}

function addCoordsToInput(e, identifier) {
  var coords = '';
  for (var i = 0; i < e.getPath().getLength(); i++) {
    coords += e.getPath().getAt(i).toUrlValue(8) + '|';
  }
  coords = coords.slice(0, -1);

  jQuery(identifier).val(coords);
}


function triggerDrawingMapResize(map, polygon, selector) {
  google.maps.event.trigger(map, "resize");

  if ( jQuery(selector).val().length > 0 ) {
    map.fitBounds(polygon.getBounds());
  } else {
    map.setCenter(mapGeometry.location);
    map.fitBounds(mapGeometry.viewport);
  }
}

function setDirectionsOnMap(triggeredFromDrag) {
  var triggeredFromDrag = !!triggeredFromDrag;

  var originLatLng = {
    lat: parseFloat(jQuery('#HalioStartingLat').val()),
    lng: parseFloat(jQuery('#HalioStartingLong').val())
  };

  var destinationLatLng = {
    lat: parseFloat(jQuery('#HalioDestinationLat').val()),
    lng: parseFloat(jQuery('#HalioDestinationLong').val())
  };

  directionsRequest['origin'] = originLatLng;
  directionsRequest['destination'] = destinationLatLng;

  var n = 0; // time of departure in seconds after current time

  if ( jQuery('#HalioPickupTime').length > 0 && jQuery('#HalioPickupTime').val().length > 0 ) {
    var then  = jQuery('#HalioPickupTime').val();
    n = moment(then, 'DD/MM/YYYY HH:mm').unix() - moment().unix();

    if (n < 0) {
      n = 0;
    }
  }

  directionsRequest['drivingOptions'] = {
    departureTime: new Date(Date.now() + (n * 1000))
  }

  // Pass the directions request to the directions service.
  var directionsService = new google.maps.DirectionsService();

  directionsService.route(directionsRequest, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      if ( jQuery('#map').length > 0 ) {
        updateFormHiddenFields(response);
        updatePriceEstimate();
      }

      directionsDisplay.setDirections(response); // Display the route on the map.

      if ( !triggeredFromDrag ) {
        map.setCenter(response.routes[0].bounds.getCenter());
        map.fitBounds(response.routes[0].bounds);
        google.maps.event.trigger(map, 'resize');
      }
    }
  });
}

function updateFormHiddenFields(response) {
  jQuery('#HalioDistance').val(response.routes[0].legs[0].distance.value);
  jQuery('#HalioPrettyDistance').val(response.routes[0].legs[0].distance.text);
  jQuery('#HalioDuration').val(response.routes[0].legs[0].duration.value);
  jQuery('#HalioPrettyDuration').val(response.routes[0].legs[0].duration.text);

  jQuery('#HalioStartingLat').val(response.routes[0].legs[0].start_location.lat);
  jQuery('#HalioStartingLong').val(response.routes[0].legs[0].start_location.lng);
  jQuery('#HalioDestinationLat').val(response.routes[0].legs[0].end_location.lat);
  jQuery('#HalioDestinationLong').val(response.routes[0].legs[0].end_location.lng);

  // Add extra info to quote box
  jQuery('.journey-stats .duration').html(response.routes[0].legs[0].duration.text);
  jQuery('.journey-stats .distance').html(response.routes[0].legs[0].distance.text);
}

function updatePriceEstimate() {
  if (
    jQuery(originInputSelector).val().length == 0 ||
    jQuery(destinationInputSelector).val().length == 0 ||
    jQuery('#HalioVehicleType option:selected').attr('disabled') == 'disabled' ||
    jQuery('#HalioNoOfOccupants option:selected').attr('disabled') == 'disabled' ||
    jQuery('#HalioDirection option:selected').attr('disabled') == 'disabled' ||
    jQuery('#HalioPickupTime').val().length == 0
  ) {
    return;
  }

  var $estimateCostButton = jQuery('.estimate-cost');

  var loadingIcon = $estimateCostButton.attr('data-disable-with');
  var loadingText = $estimateCostButton.attr('data-estimating-text');
  var data = {
    action: 'halio_estimate_price',
    vehicle_id: jQuery('#HalioVehicleType :selected').val(),
    distance_in_meters: jQuery('#HalioDistance').val(),
    occupants: jQuery('#HalioNoOfOccupants :selected').val(),
    journey_direction: jQuery('#HalioDirection :selected').val(),
    duration: jQuery('#HalioDuration').val(),
    pick_up_time: jQuery('#HalioPickupTime').val(),
    starting_coords: {
      lat: jQuery('#HalioStartingLat').val(),
      long: jQuery('#HalioStartingLong').val()
    },
    destination_coords: {
      lat: jQuery('#HalioDestinationLat').val(),
      long: jQuery('#HalioDestinationLong').val()
    }
  };

  if ( data.journey_direction == 'return' ) {
    data.return_pick_up_time = jQuery('#HalioReturnPickupTime').val();
  }

  $estimateCostButton.html(loadingIcon + loadingText);

  jQuery.ajax({
    data: data,
    type: 'post',
    url: ajax_object.ajax_url,
    success: function(return_data) {
      jQuery('.halio-form-feedback').hide();
      var parsed_data = JSON.parse(return_data);

      $estimateCostButton.html($estimateCostButton.attr('data-original-text'));

      if (parsed_data.can_book) {
        jQuery('.halio-form-container').addClass('show-quote');
        jQuery('.halio-left-container').addClass('show-quote');
        jQuery('#HalioPrice').val(parseFloat(parsed_data.price).toFixed(2));
        jQuery('.estimate-container .price').html(parseFloat(parsed_data.price).toFixed(2));
        jQuery('.estimate-container').slideDown();
        jQuery('.halio-booking-button-container').show();
      } else {
        jQuery('.estimate-container').hide();
        jQuery('.halio-booking-button-container').hide();
        jQuery('.halio-form-feedback').show().html(parsed_data.message);
      }
    }
  });
}

function updateMaxOccupants() {
  var max = jQuery('#HalioVehicleType :selected').attr('data-max-occupants');

  var currentlySelected = jQuery('#HalioNoOfOccupants').val();
  if ( currentlySelected > max ) {
    currentlySelected = max;
  }

  jQuery('#HalioNoOfOccupants').empty();

  for (var i = 1; i <= max; i++) {
    jQuery('#HalioNoOfOccupants').append(
      '<option value="' + i + '">' + i + '</option>'
    );
  }

  jQuery('#HalioNoOfOccupants option[value="' + currentlySelected + '"]').attr('selected', 'selected');
}

function pricingConditionChangeInputState(value) {
  if (value == 'fixed') {
    jQuery('.halio__ppc--increase-amount').attr('disabled', 'disabled');
    jQuery('.halio__ppc--increase-multiplier').attr('disabled', 'disabled');
    jQuery('.halio__ppc--fixed-amount').attr('disabled', false);
  } else {
    jQuery('.halio__ppc--increase-amount').attr('disabled', false);
    jQuery('.halio__ppc--increase-multiplier').attr('disabled', false);
    jQuery('.halio__ppc--fixed-amount').attr('disabled', 'disabled');
  }
}

function ppcTogglePickUpMap(value) {
  if (value == 'specific_area') {
    jQuery('.new-ppc-pick-up-area-map').slideDown(400, function() {
      triggerDrawingMapResize(pickUpMap, puOriginalPolygon, '.halio__ppc--pick-up-coordinates');
    });
  } else {
    jQuery('.new-ppc-pick-up-area-map').slideUp();
  }
}

function ppcToggleDropOffMap(value) {
  if (value == 'specific_area') {
    jQuery('.new-ppc-drop-off-area-map').slideDown(400, function() {
      triggerDrawingMapResize(dropOffMap, doOriginalPolygon, '.halio__ppc--drop-off-coordinates');
    });
  } else {
    jQuery('.new-ppc-drop-off-area-map').slideUp();
  }
}

function toggleAcceptanceAreaMap(value) {
  if (value == '1') {
    jQuery('.acceptance-region-map').slideDown(400, function() {
      triggerDrawingMapResize(map, originalPolygon, '.halio__setting--acceptance-region');
    });
  } else {
    jQuery('.acceptance-region-map').slideUp();
  }
}

function updateErrorFields() {
  var fields = [
    '#HalioStartingAddress',
    '#HalioDestinationAddress',
    '#HalioVehicleType',
    '#HalioNoOfOccupants',
    '#HalioDirection',
    '#HalioPickupTime',
    '#HalioReturnPickupTime'
  ];

  for (var i = 0; i < fields.length; i++) {
    var $el = jQuery(fields[i]);

    if ( $el.length > 0 ) {
      $el.parents('.halio-input-container').removeClass('has-error');

      if ( $el.is('select') ) {
        if  ( jQuery(fields[i] + ' :selected').attr('disabled') == 'disabled' ) {
          $el.parents('.halio-input-container').addClass('has-error');
        }
      } else {

        if ( fields[i] == '#HalioReturnPickupTime' ) {
          if ( jQuery('#HalioDirection').val() == 'return' && $el.val().length == 0 ) {
            $el.parents('.halio-input-container').addClass('has-error');
          }
        } else if (
          (fields[i] == '#HalioStartingAddress' &&
            (typeof startingAutocomplete.getPlace() == 'undefined' || $el.val().length == 0)) ||
          (fields[i] == '#HalioDestinationAddress' &&
            (typeof destinationAutocomplete.getPlace() == 'undefined' || $el.val().length == 0))
        ) {
          $el.parent().addClass('has-error');
        } else if ( $el.val().length == 0 ) {
          $el.parents('.halio-input-container').addClass('has-error');
        }
      }
    }
  }
}

var minDate = new Date();

if ( jQuery('#HalioMinuteBuffer').length && jQuery('#HalioMinuteBuffer').val().length > 0 ) {
  var minuteBuffer = parseInt(jQuery('#HalioMinuteBuffer').val());

  minDate = new Date(
    minDate.getFullYear(),
    minDate.getMonth(),
    minDate.getDate(),
    minDate.getHours(),
    minDate.getMinutes() + minuteBuffer,
    0,
    0
  );
}

if ( jQuery(window).width() >= 800 ) {
  var dateTimePickerPosition = 'left';
} else {
  var dateTimePickerPosition = 'right';
}

var dateTimeOptions = {
  format: 'DD/MM/YYYY HH:mm',
  sideBySide: true,
  useCurrent: true,
  minDate: minDate,
  showClose: true,
  showClear: true,
  widgetPositioning: {
    horizontal: dateTimePickerPosition
  },
  icons: {
    up: 'fa fa-arrow-up',
    down: 'fa fa-arrow-down',
    previous: 'fa fa-chevron-left',
    next: 'fa fa-chevron-right'
  }
};

jQuery('#HalioPickupTime').datetimepicker(dateTimeOptions);
jQuery('#HalioPickupTime').on('dp.change', updateErrorFields);
jQuery('#HalioPickupTime').val('');

jQuery('#HalioReturnPickupTime').datetimepicker(dateTimeOptions);
jQuery('#HalioReturnPickupTime').on('dp.change', updateErrorFields);
jQuery('#HalioReturnPickupTime').val('');

jQuery('.estimate-cost').click(function(e) {
  e.preventDefault();
  updatePriceEstimate();
});

// When vehicle type changes, change the max number of occupants
jQuery('#HalioVehicleType').change(function() {
  updateMaxOccupants();
  updatePriceEstimate();
});

jQuery('#HalioNoOfOccupants').change(updatePriceEstimate);
jQuery('#HalioDirection').change(function() {
  updatePriceEstimate();

  if ( jQuery(this).val() == 'return' ) {
    jQuery('.return-pick-up-time').show();
    jQuery('.halio-form-container').addClass('return-fare');
  } else {
    jQuery('.return-pick-up-time').hide();
    jQuery('.halio-form-container').removeClass('return-fare');
  }
});


jQuery('.halio__ppc--increase-or-fixed').change(function() {
  pricingConditionChangeInputState(this.value);
});

jQuery('.halio__ppc--pick-up-area-selector').change(function() {
  ppcTogglePickUpMap(this.value);
});

jQuery('.halio__ppc--drop-off-area-selector').change(function() {
  ppcToggleDropOffMap(this.value);
});

jQuery('.halio__setting--enforce-rejection-region').change(function() {
  toggleAcceptanceAreaMap(this.value);
});

jQuery('.halio__settings--maximum-fare.edit').keypress(function(e) {
  var charCode = (e.which) ? e.which : e.keyCode;
  // If key pressed isn't decimal point or number
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
  }

  // If input already has a decimal place
  if ( charCode == 46 && this.value.indexOf('.') > -1 ) {
    return false;
  }

  return true;
});

jQuery('.halio__settings--minimum-fare.edit').keypress(function(e) {
  var charCode = (e.which) ? e.which : e.keyCode;
  // If key pressed isn't decimal point or number
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
  }

  // If input already has a decimal place
  if ( charCode == 46 && this.value.indexOf('.') > -1 ) {
    return false;
  }

  return true;
});

jQuery('.halio__settings--enforce-maximum-fare').change(function() {
  if (this.value == '1') {
    jQuery('.halio__settings--maximum-fare').attr('disabled', false);
  } else {
    jQuery('.halio__settings--maximum-fare').attr('disabled', 'disabled');
  }
});

jQuery('.halio__settings--enforce-minimum-fare').change(function() {
  if (this.value == '1') {
    jQuery('.halio__settings--minimum-fare').attr('disabled', false);
  } else {
    jQuery('.halio__settings--minimum-fare').attr('disabled', 'disabled');
  }
});

jQuery('.halio__setting--enforce-minimum-distance').change(function() {
  if (this.value == '1') {
    jQuery('.halio__setting--minimum-distance').attr('disabled', false);
    jQuery('.halio__setting--minimum-distance-error-message').attr('disabled', false);
  } else {
    jQuery('.halio__setting--minimum-distance').attr('disabled', 'disabled');
    jQuery('.halio__setting--minimum-distance-error-message').attr('disabled', 'disabled');
  }
});

jQuery('.halio__setting--enforce-maximum-distance').change(function() {
  if (this.value == '1') {
    jQuery('.halio__setting--maximum-distance').attr('disabled', false);
    jQuery('.halio__setting--maximum-distance-error-message').attr('disabled', false);
  } else {
    jQuery('.halio__setting--maximum-distance').attr('disabled', 'disabled');
    jQuery('.halio__setting--maximum-distance-error-message').attr('disabled', 'disabled');
  }
});

jQuery('.halio__setting--title-or-image').change(function() {
  if (this.value == 'title') {
    jQuery('.halio__setting--form-title').attr('disabled', false);
    jQuery('.halio__setting--form-image-url').attr('disabled', 'disabled');
  } else {
    jQuery('.halio__setting--form-title').attr('disabled', 'disabled');
    jQuery('.halio__setting--form-image-url').attr('disabled', false);
  }
});

jQuery('.halio__setting--can-edit-vehicle-type').change(function() {
  if (this.value == '1') {
    jQuery('.halio__setting--default-vehicle-id').attr('disabled', 'disabled');
  } else {
    jQuery('.halio__setting--default-vehicle-id').attr('disabled', false);
  }
});

jQuery('.halio__setting--enforce-rejection-region').change(function() {
  if (this.value == '1') {
    jQuery('.halio__setting--acceptance-region-error-message').attr('disabled', false);
  } else {
    jQuery('.halio__setting--acceptance-region-error-message').attr('disabled', 'disabled');
  }
});

jQuery('.halio__settings--enforce-autocomplete-restriction').change(function() {
  if (this.value == '1') {
    jQuery('.halio__settings--autocomplete-country').attr('disabled', false);
  } else {
    jQuery('.halio__settings--autocomplete-country').attr('disabled', 'disabled');
  }
});

jQuery('#HalioStartingAddressSelect').change(function() {
  jQuery(this).siblings('.overlay').children('.letter').addClass('green');
});

jQuery('#HalioDestinationAddressSelect').change(function() {
  jQuery(this).siblings('.overlay').children('.letter').addClass('green');
});

jQuery('#HalioPickupTime').focusout(function() {
  if (
    (jQuery('#HalioDirection').length > 0 &&
      jQuery('#HalioDirection :selected').val() == 'return' &&
      jQuery('#HalioReturnPickupTime').val().length > 0
    ) ||
    (jQuery('#HalioDirection').length > 0 &&
      jQuery('#HalioDirection :selected').val() == 'one_way')
  ) {
    setDirectionsOnMap(false);
  }
});

// Make textareas grow when return pressed
jQuery('textarea').css('overflow', 'hidden');
jQuery("textarea").keyup(function(e) {
  while(jQuery(this).outerHeight() < this.scrollHeight + parseFloat(jQuery(this).css("borderTopWidth")) + parseFloat(jQuery(this).css("borderBottomWidth"))) {
    jQuery(this).height(jQuery(this).height()+1);
  };
});

jQuery('#HalioReturnPickupTime').focusout(function() {
  setDirectionsOnMap(false);
});

jQuery('#HalioPickupTime').focus(function() {
  var winWidth = jQuery(window).width();
  if ( winWidth >= 800 ) {
    jQuery('.bootstrap-datetimepicker-widget').css('width', '600px');
  } else {
    if ( winWidth >= 450 ) {
      jQuery('.bootstrap-datetimepicker-widget').css('width', (winWidth - 300) + 'px');
    } else {
      jQuery('.bootstrap-datetimepicker-widget').css('width', (winWidth - 100) + 'px');
    }
  }
});

jQuery('#HalioReturnPickupTime').focus(function() {
  var winWidth = jQuery(window).width();
  if ( winWidth >= 800 ) {
    jQuery('.bootstrap-datetimepicker-widget').css('width', '600px');
  } else {
    if ( winWidth >= 450 ) {
      jQuery('.bootstrap-datetimepicker-widget').css('width', (winWidth - 300) + 'px');
    } else {
      jQuery('.bootstrap-datetimepicker-widget').css('width', (winWidth - 100) + 'px');
    }
  }
});

jQuery('.estimate-cost').click(updateErrorFields);

jQuery('#HalioVehicleType').change(updateErrorFields);
jQuery('#HalioNoOfOccupants').change(updateErrorFields);
jQuery('#HalioDirection').change(updateErrorFields);

jQuery('.halio__new--vat-all-day').change(function() {
  if ( this.value == '1' ) {
    jQuery('.halio__new--vat-start-time.' + jQuery(this).attr('data-day')).attr('disabled', 'disabled');
    jQuery('.halio__new--vat-end-time.' + jQuery(this).attr('data-day')).attr('disabled', 'disabled');
  } else {
    jQuery('.halio__new--vat-start-time.' + jQuery(this).attr('data-day')).attr('disabled', false);
    jQuery('.halio__new--vat-end-time.' + jQuery(this).attr('data-day')).attr('disabled', false);
  }
});

jQuery(document).ready(function() {
  if ( jQuery('#calendar').length > 0 ) {
    var events = [];
    var data = {
      action: 'halio_get_calendar_events'
    };

    jQuery.ajax({
      data: data,
      type: 'post',
      url: ajax_object.ajax_url,
      success: function(return_data) {
        var parsed_data = JSON.parse(return_data);

        console.log(parsed_data);

        jQuery('.halio-calendar-loading-text').hide();

        for (var i = 0; i < parsed_data.length; i++) {
          var order = parsed_data[i];

          var order_options = {
            title: order.starting_address + " - " + order.destination_address + " (" + order.vehicle_name + ")",
            start: order.pick_up_time,
            url: order.url,
            backgroundColor: '#fff',
            description: order.description,
            allDay: false
          };

          if ( order.estimated_drop_off_time ) {
            order_options.end = order.estimated_drop_off_time_iso_8601;
          }

          events.push(order_options);
        }

        jQuery('#calendar').fullCalendar({
          events: events,
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
          },
          timeFormat: 'H:mmt',
          eventRender: function(event, element) {
            element.qtip({
              content: event.description,
              position: {
                viewport: jQuery(window),
                my: 'bottom center',
                at: 'top center'
              },
              hide: {
                delay: 100,
                fixed: true
              },
            });
          },
          dayClick: function(date, jsEvent, view) {
            if ( view.name == 'month' ) {
              jQuery('#calendar').fullCalendar('gotoDate', date);
              jQuery('#calendar').fullCalendar('changeView', 'agendaDay');
            }
          }
        });
      }
    });
  }
});

// On tab change, trigger acceptance region map resize to make it work
jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  if ( jQuery('#acceptance_region_map').length > 0 ) {
    triggerDrawingMapResize(map, originalPolygon, '.halio__setting--acceptance-region');
  }
});
