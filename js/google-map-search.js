(function($) {
	$(document).ready(function() {
		// Init google maps
		googleMapSearchInit();
	});
	
	var drawingManager;
	var selectedShape;
	var thePolygon;
	var theCircle;
	var theRectangle;
	
	var circleLatField = $('input[name="lat"]'),
		circleLonField = $('input[name="lon"]'),
		circleRadiusField = $('input[name="radius"]');
	var squareLatOneField = $('input[name="minLat"]'),
		squareLonOneField = $('input[name="minLon"]'),
		squareLatTwoField = $('input[name="maxLat"]'),
		squareLonTwoField = $('input[name="maxLon"]');
	var polygonField = $('input[name="polygon"]');
	

	function clearSelection() {
		if (selectedShape) {
			selectedShape.setEditable(false);
			selectedShape = null;
		}
	}

	function setSelection(shape) {
		clearSelection();
		selectedShape = shape;
		shape.setEditable(true);
	}
	
	function deleteShape(shape) {
		if (shape) {
			shape.setMap(null);

			// Empty form fields when shape is deleted..
			if (shape.type == google.maps.drawing.OverlayType.POLYGON) {
				polygonField.val('');
			}
			if (shape.type == google.maps.drawing.OverlayType.CIRCLE) {
				circleLatField.val('');
				circleLonField.val('');
				circleRadiusField.val('');
			}
			if (shape.type == google.maps.drawing.OverlayType.RECTANGLE) {
				squareLatOneField.val('');
				squareLonOneField.val('');
				squareLatTwoField.val('');
				squareLonTwoField.val('');
			}
		}
	}

	function selectColor(color) {

		// Retrieves the current options from the drawing manager and replaces the
		// stroke or fill color as appropriate.
		var polylineOptions = drawingManager.get('polylineOptions');
		polylineOptions.strokeColor = color;
		drawingManager.set('polylineOptions', polylineOptions);

		var rectangleOptions = drawingManager.get('rectangleOptions');
		rectangleOptions.fillColor = color;
		drawingManager.set('rectangleOptions', rectangleOptions);

		var circleOptions = drawingManager.get('circleOptions');
		circleOptions.fillColor = color;
		drawingManager.set('circleOptions', circleOptions);

		var polygonOptions = drawingManager.get('polygonOptions');
		polygonOptions.fillColor = color;
		drawingManager.set('polygonOptions', polygonOptions);
	}



	// Create custom button
	function DeleteShapeControl(controlDiv, map) {

		// Set CSS styles for the DIV containing the control
		// Setting padding to 5 px will offset the control
		// from the edge of the map.
		controlDiv.style.padding = '5px';
		controlDiv.id = 'delete-button';

		// Set CSS for the control border.
		var controlUI = document.createElement('div');
		controlUI.title = 'Delete Selected Shape';
		controlDiv.appendChild(controlUI);

		// Set CSS for the control interior.
		var controlText = document.createElement('div');
		controlText.innerHTML = '<span title="Delete Selected Shape">X</span>';
		controlUI.appendChild(controlText);

		google.maps.event.addDomListener(controlUI, 'click', function() {
			deleteShape(selectedShape);
		});
	}
	
	function updateRectangleFields(bounds) {
		bounds = bounds.toString();
		
		var rectangles = bounds.split('), (');
		var corner1 = rectangles[0],
			corner2 = rectangles[1];
		corner1 = corner1.replace(/\(|\)/g, '');
		corner2 = corner2.replace(/\(|\)/g, '');

		var corner1Coords = corner1.split(', ');
		squareLatOneField.val(corner1Coords[0]);
		squareLonOneField.val(corner1Coords[1]);

		var corner2Coords = corner2.split(', ');
		squareLatTwoField.val(corner2Coords[0]);
		squareLonTwoField.val(corner2Coords[1]);
	}
	
	function setDrawingControls() {
		drawingManager.set('drawingControlOptions', {
			position: google.maps.ControlPosition.TOP_LEFT,
			drawingModes: [google.maps.drawing.OverlayType.CIRCLE,
				google.maps.drawing.OverlayType.POLYGON,
				google.maps.drawing.OverlayType.RECTANGLE
			]
		});
	}
	
	function onNewShape(e) {
		// Delete old field info
		deleteSavedShape();
		
		// Set the polygon data to hidden fields
		if (e.type == google.maps.drawing.OverlayType.POLYGON) {
			polygonField.val(e.overlay.getPath().getArray());
		}
		if (e.type == google.maps.drawing.OverlayType.CIRCLE) {
			var center = e.overlay.center.toString();
			center = center.replace(/\(|\)/g, '');
			var centerCoords = center.split(', ');
			circleLatField.val(centerCoords[0]);
			circleLonField.val(centerCoords[1]);
			circleRadiusField.val(e.overlay.radius);
		}
		if (e.type == google.maps.drawing.OverlayType.RECTANGLE) {
			updateRectangleFields(e.overlay.bounds);
		}

		if (e.type != google.maps.drawing.OverlayType.MARKER) {
			// Switch back to non-drawing mode after drawing a shape.
			drawingManager.setDrawingMode(null);

			// Add an event listener that selects the newly-drawn shape when the user
			// mouses down on it.
			var newShape = e.overlay;
			newShape.type = e.type;
			google.maps.event.addListener(newShape, 'click', function() {
				setSelection(newShape);
			});
			setSelection(newShape);
		}
		
		drawingManager.set('drawingControlOptions', {
			position: google.maps.ControlPosition.TOP_LEFT,
			drawingModes: Array()
		});
		$('span[title="Delete Selected Shape"]').on('click', function() {
			setDrawingControls();
		});
	}
	
	function onNewCircle(circle) {
		google.maps.event.addListener(circle, 'radius_changed', function() {
			circleRadiusField.val(circle.getRadius());
		});
		google.maps.event.addListener(circle, 'center_changed', function() {
			var center = circle.getCenter().toString();
			center = center.replace(/\(|\)/g, '');
			var centerCoords = center.split(', ');
			circleLatField.val(centerCoords[0]);
			circleLonField.val(centerCoords[1]);
		});
	}
	
	function onNewPolygon(polygon) {
		google.maps.event.addListener(polygon.getPath(), 'set_at', function() {
			polygonField.val(polygon.getPath().getArray());
		});

		google.maps.event.addListener(polygon.getPath(), 'insert_at', function() {
			polygonField.val(polygon.getPath().getArray());
		});
	}
	
	function onNewRectangle(rectangle) {
		google.maps.event.addListener(rectangle, 'bounds_changed', function(){
			updateRectangleFields(rectangle.bounds);
		});
	}


	function googleMapSearchInit() {
		var map = new google.maps.Map(document.getElementById('gd-map'), {
			zoom: 12,
			// This is hardcoded for now but it needs to be a changable option in v2
			center: new google.maps.LatLng(39.7392, -104.9903),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var polyOptions = {
			strokeWeight: 0,
			fillOpacity: 0.45,
			editable: true
		};
		// Creates a drawing manager attached to the map that allows the user to draw
		// markers, lines, and shapes.
		drawingManager = new google.maps.drawing.DrawingManager({
			markerOptions: {
				draggable: true
			},
			polylineOptions: {
				editable: true
			},
			drawingControl: true,
			drawingControlOptions: {
				position: google.maps.ControlPosition.TOP_LEFT,
				drawingModes: [google.maps.drawing.OverlayType.CIRCLE,
					google.maps.drawing.OverlayType.POLYGON,
					google.maps.drawing.OverlayType.RECTANGLE
				]
			},
			rectangleOptions: polyOptions,
			circleOptions: polyOptions,
			polygonOptions: polyOptions,
			map: map
		});


		// Delete shape button
		// Create the DIV to hold the control and call the deleteShape() constructor
		// passing in this DIV.
		var deleteShapeControlDiv = document.createElement('div');
		var deleteShapeControl = new DeleteShapeControl(deleteShapeControlDiv, map);

		deleteShapeControlDiv.index = 1;
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(deleteShapeControlDiv);

		google.maps.event.addListener(drawingManager, 'overlaycomplete', onNewShape);
		
		google.maps.event.addListener(drawingManager, 'circlecomplete', onNewCircle);
		google.maps.event.addListener(drawingManager, 'polygoncomplete', onNewPolygon);
		google.maps.event.addListener(drawingManager, 'rectanglecomplete', onNewRectangle);

		// Clear the current selection when the drawing mode is changed, or when the
		// map is clicked.
		google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
		google.maps.event.addListener(map, 'click', clearSelection);
		
		drawSavedShape(map);
	}
	
	/**
	 * Draws our shapes from the hidden form fields
	 */
	function drawSavedShape(map) {
		// Draw square
 		if (squareLatOneField.val() && squareLonOneField.val() && squareLatTwoField.val() && squareLonTwoField.val()) {
			theRectangle = new google.maps.Rectangle({
				strokeWeight: 0,
				fillOpacity: 0.45,
				fillColor: '#388E3C',
				map: map,
				bounds: new google.maps.LatLngBounds(
				new google.maps.LatLng(squareLatOneField.val(), squareLonOneField.val()),
				new google.maps.LatLng(squareLatTwoField.val(), squareLonTwoField.val())),
				type: google.maps.drawing.OverlayType.RECTANGLE
			});
			onNewRectangle(theRectangle);
		}
		
		// Draw circle
 		if (circleLatField.val() && circleLonField.val() && circleRadiusField.val()) {
			theCircle = new google.maps.Circle({
				strokeWeight: 0,
				fillOpacity: 0.45,
				fillColor: '#388E3C',
				map: map,
				center: new google.maps.LatLng(circleLatField.val(), circleLonField.val()),
				radius: parseFloat(circleRadiusField.val()),
				type: google.maps.drawing.OverlayType.CIRCLE
			});
			onNewRectangle(theCircle);
		}
		
		// Draw polygon
 		if (polygonField.val()) {
			var coords = [];
			
			// Uses | because the API recognizes ',' as metacharacters
			var paths = polygonField
				.val()
				.replace(/\|/g, ',')
				.split('),(');
			
			for(var i = 0; i < paths.length; i++) {
				var path = paths[i]
					.replace(/\(|\)/g, '')
					.split(',');
				
				coords.push(new google.maps.LatLng(path[0], path[1]));
			}
			
			thePolygon = new google.maps.Polygon({
				strokeWeight: 0,
				fillOpacity: 0.45,
				fillColor: '#388E3C',
				map: map,
				path: coords,
				type: google.maps.drawing.OverlayType.POLYGON
			});
			
			onNewPolygon(thePolygon);
		}
	}
	
	function deleteSavedShape() {
		if (theRectangle) {
			deleteShape(theRectangle);
		}
		
		if (theCircle) {
			deleteShape(theCircle);
		}
		
		if (thePolygon) {
			deleteShape(thePolygon);
		}
	}
	
	$('#gd-map-reset').click(function(e) {
		e.preventDefault();
		
		deleteSavedShape();
		
		if (selectedShape) {
			deleteShape(selectedShape);
		}
		
		setDrawingControls();
	});
})(jQuery);