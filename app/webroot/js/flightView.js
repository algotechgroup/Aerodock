var firstClick = false;
var secondClick = false;
var firstClickBegin;
var clickedCellContent =[];
var interval;
  var rectangle = []; 
var flightCoord = [];
var events;
var test;
var graphData=[];
var map;
var mapProp;
var chart;
var marker;
var markerStart;
var markerEnd;
var currentGraph;
var beginSlice = 0;
var endSlice;
var altOn = true;
var engineOn = false;
var rpmOn = false;
var oilOn = false;
var fuelOn = false;
var flightPath;
var globalBounds;
var image = {
      url:  "https://maps.gstatic.com/intl/en_us/mapfiles/markers2/measle_blue.png",
      size: new google.maps.Size(7, 7),
      origin: new google.maps.Point(0,0),
      anchor: new google.maps.Point(3, 3)
}
var boundsImage = {
      url:  "https://maps.gstatic.com/intl/en_us/mapfiles/markers2/measle.png",
      size: new google.maps.Size(7, 7),
      origin: new google.maps.Point(0,0),
      anchor: new google.maps.Point(3, 3)
}
var clicked = false;
var UpdatePasswordButton = document.getElementById("updatePassword");
var turnElement = document.getElementsByClassName("turn-table")[0].getElementsByTagName("td");
for (var i = turnElement.length - 1; i >= 0; i--) {
  turnElement[i].addEventListener("mouseover", highlight);
  turnElement[i].addEventListener("click",clickTurn);
};
document.getElementById("Altitude").addEventListener("click", changeGraph);
document.getElementById("Engine").addEventListener("click", changeGraph);
document.getElementById("RPM").addEventListener("click", changeGraph);
document.getElementById("Oil").addEventListener("click", changeGraph);
document.getElementById("Fuel").addEventListener("click", changeGraph);
if(document.getElementById("updatePassword")){
  document.getElementById("updatePassword").addEventListener("click", updatePassword);
}
// Set a callback to run when the Google Visualization API is loaded.
google.maps.event.addDomListener(window, 'load', initialize);
// Callback that creates and populates a data table, 
// instantiates the pie chart, passes in the data and
// draws it.
google.setOnLoadCallback(initialize);
// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});



function initialize()
{
  mapProp = {
    scrollwheel: false,
    mapTypeId:google.maps.MapTypeId.ROADMAP,
    streetViewControl: false,
    overviewMapControl: false,
    panControl: false
  };

  map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
  getCoords();
  getEvents();
}
function graphClickHandler(e){
  if(e.targetID.substring(0,23) == "categorysensitivityarea" && !clicked){
    if(!firstClick){
      var flightIndex = Math.floor((e.targetID.substr(24)/graphData.length)*(endSlice - beginSlice));
      markerStart = new google.maps.Marker({
        position: flightCoord[flightIndex],
        map: map,
        icon: boundsImage
      })
      firstClickBegin = flightIndex;
      firstClick = true;
    } else if(firstClick && !secondClick){
      marker.setMap(null);
      secondClick = true;
      markerStart.setMap(null);
      var flightIndex = Math.floor((e.targetID.substr(24)/graphData.length)*(endSlice - beginSlice));
      beginSlice = firstClickBegin;
      endSlice = flightIndex;
      getData();
    } else {
      marker.setMap(null);
      secondClick = false;
      firstClick = false;
      beginSlice = 0;
      endSlice = flightCoord.length
      getData();
    }
  }
}
function graphOutHandler(e){
      marker.setMap(null);
};
function graphOverHandler(e){
  for (var i = rectangle.length - 1; i >= 0; i--) {
    rectangle[i].setMap(null);
  };
  var flightCoordIndex = Math.floor((e.row/graphData.length)*(endSlice - beginSlice));
  marker = new google.maps.Marker({
    position: flightCoord[flightCoordIndex + beginSlice],
    map: map,
    icon: image
  })
};

function changeGraph(eventObject, argumentsObject)
{
  var buttonColors = [["#ebebeb", "#adadad"],
                      ["#fff", "#ccc"]];
  var changeIndex = -1;
  var itemClicked = eventObject.srcElement.id;
  if(itemClicked == "Altitude"){
    altOn = !altOn
    change = altOn ? 0:1
  }
  else if(itemClicked == "Engine"){
    engineOn = !engineOn
    changeIndex = engineOn? 0:1
  }
  else if(itemClicked == "Oil"){
    oilOn = !oilOn
    changeIndex = oilOn? 0:1
  }
  else if(itemClicked == "RPM"){
    rpmOn = !rpmOn
    changeIndex = rpmOn? 0:1
  }
  else if(itemClicked == "Fuel"){
    fuelOn = !fuelOn
    changeIndex = fuelOn? 0:1
  }
  if(changeIndex != -1){
    eventObject.srcElement.style.backgroundColor = buttonColors[changeIndex][0]
    eventObject.srcElement.style.borderColor = buttonColors[changeIndex][1]
  }
  getData();
  currentGraph = eventObject.srcElement.id
  updateGraph();
  document.getElementById("Altitude").style.backgroundColor = buttonColors[altOn ? 0:1][0]
  document.getElementById("Altitude").style.borderColor = buttonColors[altOn ? 0:1][1]
};

function makeBoxes()
{
  for (var i = 0; i < events.length; i++) {
    var boxBound = new google.maps.LatLngBounds(
        new google.maps.LatLng(events[i][4],
                               events[i][5]),
        new google.maps.LatLng(events[i][2],
                               events[i][3]));
    var rect = new google.maps.Rectangle({
      strokeColor: '#0000FF',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#0000FF',
      fillOpacity: 0.2,
      map: map,
      bounds : boxBound
      });
    rectangle[i] = rect; 
  };
}
function highlight(eventObject, argumentsObject){
  if(rectangle.length == 0){
    makeBoxes();
  }
  if(clicked == false){
    for (var i = rectangle.length - 1; i >= 0; i--) {
      rectangle[i].setMap(null);
    };
    rectangle[eventObject.srcElement.id.substr(4)].setMap(map);
  }
}

function clickTurn(eventObject, argumentsObject){
  if(clicked == false){
    for (var i = rectangle.length - 1; i >= 0; i--) {
      rectangle[i].setMap(null);
    };
    var bounds = new google.maps.LatLngBounds();
    bounds.extend(new google.maps.LatLng(events[eventObject.srcElement.id.substr(4)][4],
                                    events[eventObject.srcElement.id.substr(4)][5]));
    bounds.extend(new google.maps.LatLng(events[eventObject.srcElement.id.substr(4)][2],
                                    events[eventObject.srcElement.id.substr(4)][3]));
    map.setCenter(new google.maps.LatLng(events[eventObject.srcElement.id.substr(4)][6],
                                    events[eventObject.srcElement.id.substr(4)][7]));
    map.fitBounds(bounds);
    beginSlice = events[eventObject.srcElement.id.substr(4)][1];
    endSlice = events[eventObject.srcElement.id.substr(4)][8];
    /*
    Mark as selected, blur rest, 
    */
    clickedCellContent = [eventObject.srcElement.id,eventObject.srcElement.textContent];
    eventObject.srcElement.textContent += ' - Click to return to map'
    eventObject.srcElement.style.padding = "100px 70px 100px 70px";
    eventObject.srcElement.scrollIntoView(true);
    clicked = true;
  } else {
    map.fitBounds(globalBounds);
    map.setZoom(zoomLevel);
    clicked = false;
    beginSlice = 0;
    endSlice = flightCoord.length;
    document.getElementById(clickedCellContent[0]).textContent = clickedCellContent[1];
    document.getElementById(clickedCellContent[0]).style.padding = "8px";
    document.getElementById(clickedCellContent[0]).scrollIntoView(true);
  }  
  getData();    
  updateGraph();
}

function getData(){
  var xmlhttp = new XMLHttpRequest();
  var price;
  $.ajax({
      url: dataUrl,
      type:'post',
      data:{
        altOn: altOn,
        engineOn: engineOn,
        oilOn: oilOn,
        rpmOn: rpmOn,
        fuelOn: fuelOn,
        beginSlice: beginSlice,
        endSlice: endSlice,
        flightid: flightid,
        studentid: studentid,
      },
      success:function(data){
        graphData = JSON.parse(data);
        updateGraph();
      }
  })
}

function getEvents(){
  var xmlhttp = new XMLHttpRequest();
  var price;
  $.ajax({
      url:eventsUrl,
      type:'post',
      data:{
        flightid: flightid,
        studentid: studentid,
      },
      success:function(data){
        events = JSON.parse(data);
      }
  })
}

function getCoords(){
  var xmlhttp = new XMLHttpRequest();
  var price;
  globalBounds = new google.maps.LatLngBounds();

  $.ajax({
      url: coordsUrl,
      type:'post',
      data:{
        flightid: flightid,
        studentid: studentid,
      },
      success:function(data){
        var newCoords = [];

        var crude = JSON.parse(data);
        for (var i = 0; i < crude.length; i++){
          newCoords.push(new google.maps.LatLng(crude[i][0], crude[i][1]));
          globalBounds.extend(new google.maps.LatLng(crude[i][0], crude[i][1]));
        }
        flightCoord = newCoords;
        flightPath = new google.maps.Polyline({
          path: flightCoord,
          geodesic: true,
          strokeColor: '#FF0000',
          strokeOpacity: 1.0,
          strokeWeight: 2
        });
        endSlice = flightCoord.length
        map.fitBounds(globalBounds);
        map.setZoom(zoomLevel);
        flightPath.setMap(map);
        getData();  
      }
  })
}

  function updatePassword(){
  var xmlhttp = new XMLHttpRequest();
  var newPassword = document.getElementById("newPassword").value;
  var confirmPassword = document.getElementById("passwordConfirmation").value;
  var firstName = document.getElementById("firstname").value;
  var lastName = document.getElementById("lastname").value;

  if(firstName == ""){
    $(".first").children()[0].style.color = "#f55";
  } 
      console.log(lastName == "")
  if(lastName == ""){
    $(".last").children()[0].style.color = "#f55";
  } 
  if(newPassword == confirmPassword && newPassword != "" && lastName != "" && firstName != ""){
    $.ajax({
        url: passwordUrl,
        type:'post',
        data:{
          firstname: document.getElementById("firstname").value,
          lastname: document.getElementById("lastname").value,
          password: newPassword,
          studentid: studentid,
        },
        success:function(data){
          document.getElementById("updateInfo").parentNode.removeChild(document.getElementById("updateInfo"));
        }
    })
  } else {
    $(".password").children()[0].style.color = "#f55";
    $(".confirmation").children()[0].style.color = "#f55";
  }
}

function updateGraph(){
  activeSeries = [];
  vAxesList = [];
  colorsList =[];
  var data = new google.visualization.DataTable();
  interval = 0;
  data.addColumn('string', 'Time');
  if(altOn){
    data.addColumn('number', 'Altitude');
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addColumn('number', 'Airspeed');
    data.addColumn({type: 'string', role: 'tooltip'});
    activeSeries.push([{targetAxisIndex:0, color:'green'}]);
    activeSeries.push([{targetAxisIndex:1}]);
    vAxesList.push({title: ''});
    vAxesList.push({title: ''});
    interval = interval + 2;
    colorsList.push("#0017FF", "#0DA2FF")
  }
  if(engineOn){
    data.addColumn('number', 'CHT');
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addColumn('number', 'EGT');      
    data.addColumn({type: 'string', role: 'tooltip'});
    activeSeries.push([{targetAxisIndex:2}]);
    activeSeries.push([{targetAxisIndex:3}]);
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    interval = interval + 2;
    colorsList.push("#FF0000", "#FF540D")
  }
  if(oilOn){
    data.addColumn('number', 'Oil Presure');
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addColumn('number', 'Oil Temp');
    data.addColumn({type: 'string', role: 'tooltip'});
    activeSeries.push([{targetAxisIndex:4}]);
    activeSeries.push([{targetAxisIndex:5}]);
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    interval = interval + 2;
    colorsList.push('#FF00FD', '#AD0CE8');
  }
  if(rpmOn){
    data.addColumn('number', 'MAP');
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addColumn('number', 'RPM');
    data.addColumn({type: 'string', role: 'tooltip'});
    activeSeries.push([{targetAxisIndex:6}]);
    activeSeries.push([{targetAxisIndex:7}]);
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    interval = interval + 2;
    colorsList.push("#FFE700", "#FFB70D");
  }
  if(fuelOn){
    data.addColumn('number', 'Fuel Flow');
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addColumn('number', 'Fuel Presure');
    data.addColumn({type: 'string', role: 'tooltip'});
    activeSeries.push([{targetAxisIndex:8}]);
    activeSeries.push([{targetAxisIndex:9}]);
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    vAxesList.push({title: '' ,textStyle:{ color: 'red'}});
    interval = interval + 2;
    colorsList.push('#00FF3F','#0DFFD2');
  }
  interval = Math.floor((endSlice - beginSlice) * interval / 1000);
  data.addRows(graphData);
  var options =  {'width':900,
            'height':250,
            series:activeSeries,
            vAxes:vAxesList,
            legend: { position: 'right'},
            focusTarget: 'category',
            vAxis:{ticks: []},
            colors:colorsList};

  var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
  chart.draw(data, options);

  google.visualization.events.addListener(chart, 'onmouseover', graphOverHandler);
  google.visualization.events.addListener(chart, 'onmouseout', graphOutHandler);
  google.visualization.events.addListener(chart, 'click', graphClickHandler);
}
