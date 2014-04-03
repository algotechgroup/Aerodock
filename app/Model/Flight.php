<?php
class Flight extends AppModel {

//page 317 of cakePHPCookbook
	public $validate = array(
	'studentID' => array(
		'rule' => 'notEmpty'
		),
	'instructorID' => array(
		'rule' => 'notEmpty'
		),
	'aircraft' => array(
		'rule' => 'notEmpty'
		),
	'tailNo' => array(
		'rule' => 'notEmpty'
		),
	);


	public function uploadFile( $uploadData, $id ) {
		ClassRegistry::init('Log');
		$log = new Log();
		// Shifts the first item out of the array, equivalent to popping the stack
		// Check to make sure the file has data and there are no errors from upload
		if( $uploadData['size'] == 0 || $uploadData['error'] !== 0) {
			return false;
		}
		
		if($log->loadCSV($uploadData, $id)){
			return true;
		}

		return false;
	}

	// Takes the file path as a parameter and returns the latitude and longitude from the
	// csv. This method may get folded into a more efficient method that returns more values
	// at one time. This if for front end mock up purposes.
	public function getLatLong($flight_id = null) {

		if(!$flight_id){
			throw new NotFoundException(__('Invalid flight'));
		}
		ClassRegistry::init('Log');
		$log = new Log();

		// Initialize the array that we will return as an array of arrays.
		$latLongArray = array('lat' => array(),
													'long'=> array());
		$altitude = array();
		$airspeed = array();
		$engineRPM = array();
		$engineTemp = array();
		$tracking = array();
		$timestamp = array();
		$pageNum = 1;
		$flightInfo = $log->find('all', array(
			'conditions' => array('Log.flight_id' => $flight_id),
			'fields' => array('Log.Latitude', 'Log.Longitude', 'Log.AltMSL', 'Log.IAS', 'CHT1', 'RPM','TRK','Time'),
			'limit' => 1));

		$index = 0;
		$latLongArray['lat'][$index] = $flightInfo[0]['Log']['Latitude'];
		$latLongArray['long'][$index] = $flightInfo[0]['Log']['Longitude'];
		$altitude[$index] = $flightInfo[0]['Log']['AltMSL'];
		$airspeed[$index] = $flightInfo[0]['Log']['IAS'];
		$engineRPM[$index] = $flightInfo[0]['Log']['CHT1'];
		$engineTemp[$index] = $flightInfo[0]['Log']['RPM'];
		$tracking[$index] = $flightInfo[0]['Log']['TRK'];

		$timestamp[$index] = $flightInfo[0]['Log']['Time'];
		$maxLat = $latLongArray['lat'][$index];
		$minLat = $maxLat;
		$maxLong = $latLongArray['long'][$index];
		$minLong = $maxLong;

		// While there is still data to return...
		while(count($flightInfo) != 0){
			for($j=0; $j < count($flightInfo); $j++){
				$latLongArray['lat'][$index]  = $flightInfo[$j]['Log']['Latitude'];
				$latLongArray['long'][$index] = $flightInfo[$j]['Log']['Longitude'];
				$altitude[$index]   = $flightInfo[$j]['Log']['AltMSL'];
				$airspeed[$index]   = $flightInfo[$j]['Log']['IAS'];
				$engineRPM[$index]  = $flightInfo[$j]['Log']['CHT1'];
				$engineTemp[$index] = $flightInfo[$j]['Log']['RPM'];
				$tracking[$index]   = $flightInfo[$j]['Log']['TRK'];
				$timestamp[$index]  = $flightInfo[$j]['Log']['Time'];
				if($flightInfo[$j]['Log']['Latitude'] < $minLat){
					$minLat = $flightInfo[$j]['Log']['Latitude'];
				}
				if($flightInfo[$j]['Log']['Latitude'] > $maxLat){
					$maxLat = $flightInfo[$j]['Log']['Latitude'];
				}				
				if($flightInfo[$j]['Log']['Longitude'] < $minLong){
					$minLong = $flightInfo[$j]['Log']['Longitude'];
				}				
				if($flightInfo[$j]['Log']['Longitude'] > $maxLong){
					$maxLong = $flightInfo[$j]['Log']['Longitude'];
				}
				$index++;
			
			}
			$flightInfo = $log->find('all', array(
				'conditions' => array('Log.flight_id' => $flight_id),
				'fields' => array('Log.Latitude', 'Log.Longitude', 'Log.AltMSL', 'Log.IAS', 'CHT1', 'RPM','TRK','Time'),
				'limit' => 500,
				'page' => $pageNum));
			$pageNum++;
		}

		// Store that data in the array.

		$center = array('lat' => ($maxLat + $minLat)/2,
										'long' => ($maxLong + $minLong)/2);

		$minMax = array('maxLat' => $maxLat, 'minLat' => $minLat,
										'maxLong' => $maxLong, 'minLong' => $minLong);
		//pr($airspeed);
		$zoomLevel = $this->calculateZoom($minMax);
		$this->makejscript($altitude, $airspeed, $latLongArray,
											 $engineTemp, $engineRPM, $flight_id, 
											 $tracking, $timestamp);
		return array('center' => $center,
								 'zoomLevel' => $zoomLevel);

	}

	private function calculateZoom($minMax){
		$latZoom =  (int)(12 - log(($minMax['maxLat'] - $minMax['minLat'])/0.15,2));
		$longZoom = (int)(12 - log(($minMax['maxLong'] - $minMax['minLong'])/0.22,2));
		if($latZoom < $longZoom)
			return $latZoom;
		return $longZoom;
	}

	private function makejscript($altitude, $airspeed, $latLongArray,
															 $engineTemp, $engineRPM, $flight_id, 
															 $tracking, $timestamp){

		$altitudeFileName = "al" . $flight_id . ".js";
		$altitudeFile = new File('js' . DS . 'Flightjs'. DS. $altitudeFileName, true, 0644);
		$altitudeFile->create();
		$altitudeFile->write( "var altAirspeed  = [");

		$latLongFile = new File('js' . DS .'Flightjs'. DS. "latlong" . $flight_id . ".js");
		$latLongFile->create();
		$latLongFile->write( "var flightCoords  = [");

		$engineString = "var engine = [";
		$trackingString = "var tracking = [";
		for($i=0; $i<count($altitude); $i++){
			$altitudeFile->write( "[new Date(2013,0,0,".
														substr($timestamp[$i], 0,2).",".
														substr($timestamp[$i], 3,2).",".
														substr($timestamp[$i], 6,2).",0),".
														$altitude[$i].",".$airspeed[$i]."],");
			$latLongFile->write( "new google.maps.LatLng(" . 
														$latLongArray['lat'][$i] . "," . 
														$latLongArray['long'][$i] . "),");
			$engineString .= "[new Date(2013,0,0,".
														substr($timestamp[$i], 0,2).",".
														substr($timestamp[$i], 3,2).",".
														substr($timestamp[$i], 6,2).",0),".
														$engineTemp[$i].",".$engineRPM[$i]."],";
			$trackingString .= "[new Date(2013,0,0,".
														substr($timestamp[$i], 0,2).",".
														substr($timestamp[$i], 3,2).",".
														substr($timestamp[$i], 6,2).",0),".
														$tracking[$i]."],\n";
		}
		$altitudeFile->write( "];");
		$latLongFile->write( "];");
		$engineString .= "];";
		$trackingString .= "];";
		$altitudeFile->write($engineString);
		$altitudeFile->write($trackingString);
	}
}