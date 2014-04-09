<?php
class Log extends AppModel {
public function loadCSV($uploadFile, $flightId){

		$handle = fopen($uploadFile['tmp_name'],'r');
		$row = fgetcsv($handle);
		$aircraft = substr($row[2],16,-1);
		$tailNumber = substr($row[6],12,-1);
		fgetcsv($handle);
		$header = fgetcsv($handle);
		$numItems = count($header);
		$dropColumns = array(0,2,3,6,7,15,16,19,20,21,38,39,
													40,41,42,43,44,45,46,47,48,49,
													50,51,52,53,54,55,57,58,59,60,61,62);
		for ($i=0; $i < count($header); $i++) {
			$header[$i] = trim($header[$i]);
			if(substr($header[$i], 2,1) == " "){
				$header[$i] = substr($header[$i], 3);
			}
			if(substr($header[$i], 3,1) == " "){
				$header[$i] = substr($header[$i], 4);
			}
		}
		foreach ($dropColumns as $dropIndex) {
			unset($header[$dropIndex]);
		}
		$index = 0;
		$firstFlightTime = "";
		$lastFlightTime = "";

		$data = array();
		$setMaintFlag = false;
		$setAdminFlag = false;
		while($row = fgetcsv($handle)) {
			if($index == 0)
			{
				$date = $row[0];
				$firstFlightTime = $row[1];
			}

			if(count($row) == $numItems && !(ctype_space($row[4]) || ctype_space($row[5]))){
				foreach ($dropColumns as $dropIndex) {
					unset($row[$dropIndex]);
				}
				$row = array_combine($header, $row);

				$lastFlightTime = $row['Time'];

        //check for engine maintenance issues
        if (!setMaintFlag)
        {
          $avgEGT = ($row['EGT1'] + $row['EGT2'] + $row['EGT3'] + $row['EGT4'])/4;
          if (abs($avgEGT - $row['EGT1'])/$avgEGT >= .1)
          {
            $setMaintFlag = true;
          }
          else if (abs($avgEGT - $row['EGT2'])/$avgEGT >= .1)
          {
            $setMaintFlag = true;
          }
          else if (abs($avgEGT - $row['EGT3'])/$avgEGT >= .1)
          {
            $setMaintFlag = true;
          }
          else if (abs($avgEGT - $row['EGT4'])/$avgEGT >= .1)
          {
            $setMaintFlag = true;
          }
        }
        
        if (!$setMaintFlag)
        {
          $avgCHT = ($row['CHT1'] + $row['CHT2'] + $row['CHT3'] + $row['CHT4'])/4;
        
          if ( abs($avgCHT - $row['CHT1'])/$avgCHT >= .1)
          {
            $setMaintFlag = true;
          }
          else if (abs($avgCHT - $row['CHT2'])/$avgCHT >= .1)
          {
            $setMaintFlag = true;
          }
          else if (abs($avgCHT - $row['CHT3'])/$avgCHT >= .1)
          {
            $setMaintFlag = true;
          }
          else if (abs($avgCHT - $row['CHT4'])/$avgCHT >= .1)
          {
            $setMaintFlag = true;
          }
        }
        
        
        
        //check for admin issues
        if (!$setAdminFlag)
        {
          if (abs($row['Roll']) >62)
          {
            $setAdminFlag = true;
          }
          if (abs($row['Pitch']) > 35)
          {
            $setAdminFlag = true;
          }
        }
        
   
          
        $row['flight_id'] = $flightId;
				$data[$index] = $row;
				$index++;
			}

			if($index % 750 == 0){
				$this->create();

				$this->saveMany($data);
				$data = array();
			}

		}
		$flags = 0;
    if($setMaintFlag)
    {
       $flags += 1;
    }
    if($setAdminFlag)
    {
       $flags += 2;
    }

		$this->create();
		$this->saveMany($data);

		// Time format is HH:MM:SS
		$start = strtotime($firstFlightTime);
		$end = strtotime($lastFlightTime);

		$delta = $end - $start;
    
		return array("return" => true,
								 "duration" => $delta,
								 "date" => $date, 
								 "tailNo" => $tailNumber, 
								 "aircraft" => $aircraft,
								 "maintenance" => $flags);
	}

	public function deleteLog($id)
	{
		$condition = array('Log.flight_id' => $id );
		if($this->deleteAll($condition,false ))
		{
			return true;
		}
		
	}


}
