<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

      

      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {
        var xmlHttpRequest = new XMLHttpRequest();
        //when an xml file is loaded run this function - code partially taken from - https://www.dummies.com/web-design-development/html/how-to-load-xml-with-javascript-on-an-html5-page/
        xmlHttpRequest.onload = function(){
          var xmlDocument = new DOMParser().parseFromString(xmlHttpRequest.responseText,'text/xml');
          readings = xmlDocument.getElementsByTagName('reading'); //gets all of the elements in the xml file

          //setting out arrays to store all of the required information in
          var dates = [];
          var times = [];
          var levels = [];
          var fullDate = [];

          //runs through all of the data and stores all attributes in their respective arrays
          for (var i = 0; i < readings.length; i++){
            if (readings[i].getAttribute("time") == '<?php echo $_POST["time"]; ?>:00'){
              var date = new Date(convertDate(readings[i].getAttribute("date")));//creates a new date object from the xml data and converts it into google's format - https://www.w3schools.com/js/js_dates.asp
              dates[i] = date;
              levels[i] = parseInt(readings[i].getAttribute("val"));
              //console.log(fullDate[i] + " " + levels[i]); //outputs all data to console to check it has loaded
            }
          }
          var allData = [];
          for (var x = 0; x < dates.length && levels.length; x++){ //puts the two relevant arrays into an array of arrays for the graph to use
            allData[x] = [dates[x], levels[x]];
          }
          createGraph(allData);
        }
        //sets out which xml file is going to be loaded - uses the user's selection from the HTML file - https://www.w3schools.com/php/php_forms.asp
        xmlHttpRequest.open("GET", '<?php echo $_POST["graphLocation"]; ?>' + "_no2.xml");
        xmlHttpRequest.send();

        // Create the data table.
        function createGraph(allData){
          //console.log(allData);
          var data = new google.visualization.DataTable(allData);
          data.addColumn('date', 'Date');
          data.addColumn('number', 'Pollutant Level');
          data.addRows(allData); //sets the data elements using the array of arrays

          // Set chart options
          var options = {'title':'Pollutant levels at ' + '<?php echo $_POST["graphLocation"]; ?>' + " at " + '<?php echo $_POST["time"]; ?>',
                        hAxis: {title: 'Dates'}, //x axis label
                        vAxis: {title: 'Pollution Levels', minValue: 0}, //y axis label
                        'width':1000, //width of the graph
                        'height':750}; //height of the graph

          // Instantiate and draw our chart, passing in some options.
          var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
          chart.draw(data, options);

        }
      }

      function convertDate(date){
        var splitDate = date.split("/");
        var newDate = splitDate[2] + "-" + splitDate[1] + "-" + splitDate[0]; //This splits up and reforms the date to put it in the format that google uses
        return newDate;
      }
    </script>
  </head>

  <body>
    <!--Div that will hold the scatter graph-->
    <div id="chart_div"></div>

    <p><i>If the graph is either missing or is blank then there hasn't been any data collected for that time.</i></p>
  </body>
</html>