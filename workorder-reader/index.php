<pre>
<?php
//get the names of files in the /wo directory
$workorder_numbers = scandir("../wo");//file path
$workorders = array();
$workorder = array();
$workorder_count = 0;

foreach($workorder_numbers as $wo) {
	
	//filter out the 'previous directory dots'
	if($wo!== "." && $wo !== "..") {
						//file path
		$handle = fopen("../wo/$wo", "r");

		if ($handle) {

			//file opened
			$workorder['file-location'] = "../wo/$wo";//file path

			//shipto address
			$shipto_address_found = false;
			$shipto_address_line_num = 0;
			$shipto_address_line_count = 0;

			//customer address
			$address_found = false;
			$address_line_num = 0;//the line number when "customer address: " is detected
			$address_line_count = 0;
			
			//ETA
			$eta_found = false;

			$line_number = 0;
			$parts_count = 0;
			$waybills_count = 0;

			$last_line;



		    while (($line = fgets($handle)) !== false) {

		    	$line_number++;
		        
		        //the last line contained the "customer address: " string
		        if($address_found) {

		        	//The address should be a maximum of 4 lines
		        	if( $line_number <= $address_line_num + 4 ) {
		        		//the zipcode is at the end of the address, so we don't need to continue saving address data
		        		if(preg_match("/[0-9]{5}/", $line)){
		        			$address_found = false;//the address is complete, exit this logic
		        		}
		        		$workorder['address'][$address_line_count] = trim(preg_replace('/Gas and traffic/i', '', $line));
	        			$address_line_count++;
		        		
		        	}else{
		        		$address_found = false;
		        	}
		        }

		        if($shipto_address_found) {
		        	//The address should be a maximum of 4 lines
		        	if( $line_number <= $shipto_address_line_num + 4 ) {
		        		//the zipcode is at the end of the address, so we don't need to continue saving address data
		        		if(preg_match("/[0-9]{5}/", $line)){
		        			$shipto_address_found = false;//the address is complete, exit this logic
		        		}
		        		//UPS
		        		if(preg_match('/ATTN WORLDWIDE TECH SRVS TECH/i', $line)) {
		        			$workorder['shipto_address'][$shipto_address_line_count] = "UPS";
		        			$workorder['shipto_address'][$shipto_address_line_count + 1] 
		        			= trim(preg_replace('/ATTN WORLDWIDE TECH SRVS TECH/i', '', $line));
		        		//FedEx
		        		}else if(preg_match('/Worldwide TechServices Technician Worldwide TechServices HFPU/i', $last_line)) {
		        			$workorder['shipto_address'][$shipto_address_line_count] = "FedEx";
		        			$workorder['shipto_address'][$shipto_address_line_count + 1] = trim($line);
		        		}else {
		        			$workorder['shipto_address'][$shipto_address_line_count + 1] = trim($line);
		        		}
		        		$shipto_address_line_count++;
		        		
		        	}else{
		        		$shipto_address_found = false;
		        	}
	        	}

		        //the next line contains the address
		        if (preg_match("/customer address:/i", $line)) {
		        	$address_found = true;
		        	$address_line_num = $line_number;//the line number when "customer address: " is detected
		        }

		        //shipping address
		        if(preg_match("/shipto address: /i", $line)) {
		        	$shipto_address_found = true;
		        	$shipto_address_line_num = $line_number;//the line number when "customer address: " is detected
		        }

		        //shipping address
		        if(preg_match("/parts shipped to: /i", $line)) {
		        	$shipto_address_found = true;
		        	$shipto_address_line_num = $line_number;//the line number when "customer address: " is detected
		        }

		        //the OEM is found
		        if(preg_match("/account number: dell/i", $line, $vendor)) {
		        	$workorder['vendor'] = "Dell";//OEM is Dell
		        }
		        if(preg_match("/account number: ibm/i", $line, $vendor)) {
		        	$workorder['vendor'] = "IBM";//OEM is IBM
		        }
		        //OEM case number
		        if(preg_match("/site code: /i", $line)) {
		        	$workorder['site-code'] = trim(substr(strrchr($line, ":"), 1));
		        }

		        //ETA
		        if(!$eta_found) {
		        	if(preg_match("/eta: /i", $line)) {
			        	$eta = explode(" ", $line);
			        	$workorder['eta']['date'] = $eta[1];
			        	$workorder['eta']['time'] = trim($eta[2]);
			        	$eta_found = true;
			        }
		        }
		        

		        //customer phone number
		        if(preg_match("/contact phone: /i", $line)) {
		        		$workorder['contact-phone'] = trim(substr(strrchr($line, ":"), 1));
		        }
		        //customer name
		        if(preg_match("/contact name: /i", $line)) {
		        		$workorder['contact-name'] = trim(substr(strrchr($line, ":"), 1));
		        }

		        //problem description
		        if(preg_match("/problem description: /i", $line)) {
		        		$workorder['problem-description'] = trim(substr(strrchr($line, ":"), 1));
		        }

		        //repair time
		        if(preg_match("/avg repair: /i", $line)) {
		        	$workorder['avg-repair'] = trim(substr(strrchr($line, ":"), 1));
		        }
		        //part numbers
		        if(preg_match("/part #: /i", $line)) {
		        	$part_number = explode(" ", $line);
		        	$part_number = preg_split("/[\t]/", $part_number[2]);
		        	$part_number = $part_number[0];
		        	$workorder['parts'][$parts_count] = $part_number;
		        	$parts_count++;
		        }

		        //fedex waybill
		        if(preg_match("/shipped: /i", $line)) {
		        	$waybill = explode(" ", $line);
		        	$workorder['waybills'][$waybills_count][0] = "Fedex";
		        	if(preg_match('/[0-9]/', $waybill[2])) {
		        		$workorder['waybills'][$waybills_count][1] = $waybill[2];
		        	} else if(preg_match('/[0-9]/', $waybill[4])) {
		        		$workorder['waybills'][$waybills_count][1] = $waybill[4];
		        	}
		        	
		        	$waybills_count++;
		        }
		        //ups waybill
		        if(preg_match("/waybill#/i", $line)) {
		        	$waybill = explode(" ", $line);
		        	$workorder['waybills'][$waybills_count][0] = "UPS";
		        	$workorder['waybills'][$waybills_count][1] = $waybill[4];
		        	$waybills_count++;
		        }

		        $last_line = $line;
		    }
		    fclose($handle);
		} else {
		    echo "cannot open file\n";
		}
		// add the workorder to the workorders array ... too obvious for comment?
		$workorders[$workorder_count] = $workorder;
		$workorder_count++;
		$workorder = array();
	}
}

//show all workorders data
//var_dump($workorders);

?>
</pre>