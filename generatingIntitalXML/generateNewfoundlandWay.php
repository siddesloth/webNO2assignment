<?php

echo "working .. wait";

ob_flush();

flush();



if (($handle = fopen("air_quality.csv", "r")) !== FALSE) {

    

	# define the tags - last col value in csv file is derived so ignore

	$header = array('id', 'desc', 'date', 'time', 'nox', 'no', 'no2', 'lat', 'long');

	

	# throw away the first line - field names

	fgetcsv($handle, 200, ",");

	

	# count the number of items in the $header array so we can loop using it

	$cols = count($header);

	

	#set record count to 1

	$count = 1;

	# set row count to 2 - this is the row in the original csv file

	$row = 2;

		

	# start ##################################################

	$out = '<records>';

	

	while (($data = fgetcsv($handle, 200, ",")) !== FALSE) {

        

        if ($data[0] == 11) {

			$rec = '<row count="' . $count . '" id="' . $row . '">';

		

			for ($c=0; $c < $cols; $c++) {

				$rec .= '<' . trim($header[$c]) . ' val="' . trim($data[$c]) . '"/>';

			}

			$rec .= '</row>';

			$count++;

			$out .= $rec;

		}

		$row++;

	}

	

	$out .= '</records>';

	# finish ##################################################

	

	# write out file

	file_put_contents('newfoundlandWay.xml', $out);

	

	fclose($handle);

}

echo "....all done!";

?>