<?php

$LOCATION = 'brislington';
$LOCATION_READABLE = 'brislington';
$POLLUTANT = 'no2';
$POLLUTANT_READABLE = 'nitrogen dioxide';
$LONGITUDE = 51.4417;
$LATTITUDE = -2.56;
$DEFAULT_POLLUTATNT_LEVEL = 'N/A';

$reader = new XMLReader();
$reader->open($LOCATION . '.xml');

#Setting up the document with element nodes etc.
$writer = xmlwriter_open_memory();
xmlwriter_set_indent($writer, 1);
$res = xmlwriter_set_indent_string($writer, ' ');

xmlwriter_start_document($writer, '1.0', 'UTF-8'); 

xmlwriter_start_element($writer, 'data');
xmlwriter_start_attribute($writer, 'type');
xmlwriter_text($writer, $POLLUTANT_READABLE);
xmlwriter_end_attribute($writer);

xmlwriter_start_element($writer, 'location');
xmlwriter_start_attribute($writer, 'id');
xmlwriter_text($writer, $LOCATION_READABLE);
xmlwriter_end_attribute($writer);
xmlwriter_start_attribute($writer, 'lat');
xmlwriter_text($writer, $LATTITUDE);
xmlwriter_end_attribute($writer);
xmlwriter_start_attribute($writer, 'long');
xmlwriter_text($writer, $LONGITUDE);
xmlwriter_end_attribute($writer);

while ($reader->read()){
    if($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'row'){
        xmlwriter_start_element($writer, 'reading');
    }
    if($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'row'){
        xmlwriter_end_element($writer);
    }

    #Gets all of the time data
    if($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'time'){
        xmlwriter_start_attribute($writer, 'time');
        xmlwriter_text($writer, $reader->getAttribute('val'));
        xmlwriter_end_attribute($writer);
    }

    #Gets all pollutant data, sets all negative data to N/A
    if($reader->nodeType == XMLReader::ELEMENT && $reader->name == $POLLUTANT){
        xmlwriter_start_attribute($writer, 'val');
        if($reader->getAttribute('val') > 0){
            xmlwriter_text($writer, $reader->getAttribute('val'));
        } else {
            xmlwriter_text($writer, $DEFAULT_POLLUTATNT_LEVEL);
        }
        xmlwriter_end_attribute($writer);
    }

    #Gets all the date data
    if($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'date'){
        xmlwriter_start_attribute($writer, 'date');
        xmlwriter_text($writer, $reader->getAttribute('val'));
        xmlwriter_end_attribute($writer);
    }
}

xmlwriter_end_element($writer);
xmlwriter_end_element($writer);
xmlwriter_end_document($writer);

file_put_contents($LOCATION . '_' . $POLLUTANT . '.xml', xmlwriter_output_memory($writer));

#Removes all of the duplicate data within the xml file
$entries = file($LOCATION . '_' . $POLLUTANT . '.xml');
$entries = array_unique($entries);
$remove = array();

#Taken from: https://stackoverflow.com/questions/15169862/php-remove-all-lines-from-a-big-string-containing-a-specific-word
foreach($entries as $line){
    if (strpos($line, $DEFAULT_POLLUTATNT_LEVEL) == TRUE){
        continue;
    }
    $remove[] = $line;
}

#Re-writes the file with the edited data
file_put_contents($LOCATION . '_' . $POLLUTANT . '.xml', implode($remove));

echo 'All finished!';
?>