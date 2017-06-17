<?php 
	require_once 'init.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Stock Symbol Tracklist App</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link   rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-route.js"></script>
    <link   rel="stylesheet" type="text/css" href="main.css">
</head>
<body ng-app="stockApp">
<header>
	<div id="_headWrapper">
		<h1 id="_heading">Stock Symbol Watchlist</h1>
	</div>
</header>
<section>
	<div id="_contentWrapper" ng-controller="MainController as MC">
		<div id="_inputWrapper">
			<form class="form-inline" id="_inputForm">
				<div class="form-group">
					<input type="text" name="symbol" class="form-control" id="_input" placeholder="Enter a symbol..." ng-model="stock.symbol" required="">
				</div>
				<button type="submit" class="btn btn-default" id="_addBtn">Add Symbol</button>
				<div class="form-group" id="_serverresponse">
					<p ng-cloak>{{ServerResponse}}</p>
					<p ng-cloak>{{JsResponse}}</p>	
				</div>
			</form>
		</div>
		<div id="_tableWrapper">
			<table class="table table-striped">
			<thead>
				<tr>
					<th ng-click="sort('symbol')">Symbol
						<span class="glyphicon sort-icon" ng-show="sortKey=='symbol'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span></th>
					<th ng-click="sort('name')">Symbol Name
						<span class="glyphicon sort-icon" ng-show="sortKey=='name'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span></th>
					<th ng-click="sort('last')">Last Price
						<span class="glyphicon sort-icon" ng-show="sortKey=='last'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span></th>
					<th ng-click="sort('change')">Change
						<span class="glyphicon sort-icon" ng-show="sortKey=='change'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span></th>
					<th ng-click="sort('pctchange')">%Change
						<span class="glyphicon sort-icon" ng-show="sortKey=='pctchange'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span></th>
					<th ng-click="sort('volume')">Volume
						<span class="glyphicon sort-icon" ng-show="sortKey=='volume'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span></th>
					<th ng-click="sort('tradetime')">Time
						<span class="glyphicon sort-icon" ng-show="sortKey=='tradetime'" ng-class="{'glyphicon-chevron-up':reverse,'glyphicon-chevron-down':!reverse}"></span></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="stock in stocks | orderBy:sortKey:reverse">
					<td><b ng-cloak>{{stock.symbol}}</b></td>
					<td><b ng-cloak>{{stock.name}}</b></td>
					<td><b ng-cloak>{{stock.last}}</b></td>
					<td><b ng-cloak>{{stock.change}}</b></td>
					<td><b ng-cloak>{{stock.pctchange}}</b></td>
					<td><b ng-cloak>{{stock.volume}}</b></td>
					<td><b ng-cloak>{{stock.tradetime}}</b></td>
					<td><a ng-click="deleteRecord($index)"><span ng-cloak class="glyphicon glyphicon-remove"></span></a></td>
					<td ng-show="stock=='undefined' ||stock=='null' || stock.length==0"><b>There are no symbols in your watchlist, please add one</b></td>
				</tr>				
			</tbody>
		</table>
		</div>
	</div>
</section>
<footer>
	
</footer>

<?php 
	$query = "SELECT * from watchlist";
	$result = mysqli_query($conn, $query);
	$rows = mysqli_num_rows($result);
	$data = array();
	while ($row = mysqli_fetch_assoc($result)){
		$data[] = $row;
	}

	$data_json = json_encode($data);


	// $query = "SELECT * FROM quotes WHERE symbol = '".$data."'";
 //   	$result = mysqli_query($conn, $query);

 //   	while ($row = mysqli_fetch_array($result)) {
 //   		echo $row[0];
 //   	}

	//mysqli_close($conn);
 ?>
 <script type="text/javascript">
 	(function(){
 		var app = angular.module('stockApp', []);

 		app.controller('MainController' , mainController);

 		mainController.$inject = ["$scope", "$http"];

 		function mainController($scope, $http){
 			$scope.stocks = <?php echo $data_json; ?> ;
 			console.log($scope.stocks);
 			if ($scope.stocks.length ==0) {
      			$scope.ServerResponse = "There are no symbols in your watchlist, please add one";
            	}

            $scope.sort = function(keyname){
     		    $scope.sortKey = keyname;   //set the sortKey to the param passed
        		$scope.reverse = !$scope.reverse; //if true make it false and vice versa
   			 }

 			$scope.deleteRecord = function(index){
 				console.log("Delete : " + $scope.stocks[index]['symbol']);
 				
 				var data = $.param({
 					symbol: $scope.stocks[index]['symbol']
 				});

 				//Send data to delete from database
 				postDelData(data, index);
     
            };

            $("#_inputForm").submit(function(){
            	console.log($scope.stock.symbol);

            	//Ckeck if data is already present in watchlist
            	var stockExist = validateWatchlist($scope.stock.symbol);
  
            	if (stockExist) {
            		$scope.JsResponse = "This symbol has already been added to watchlist";
            	}else{
            		delete $scope.JsResponse;
            		var data = $.param({
 						symbol: $scope.stock.symbol
 					});

            		console.log("Send data to add to watchlist table");
       		     	postData(data);
            	}
            	
            });


            function validateWatchlist(value){

            	var symbol = value.toUpperCase();
            		symbol = symbol.trim();
            	for (var i = 0; i < $scope.stocks.length; i++) {
            		var scopeStock = $scope.stocks[i]['symbol'];
            			scopeStock = scopeStock.trim();

            		if (scopeStock == symbol) {
            			return true;
            		}
            	}
            	return false;
            }

            function postData(data){
            	$http({
            		method: 'POST',
            		url:    'insert_watchlist.php',
            		data:    data,
            		headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' 
            	}	
            	}).then(function(response){

            		if (response.data == "NOTEXIST") {
            			$scope.ServerResponse = "The given symbol doesnot exist";
            		}else{
            			$scope.ServerResponse = response.data['symbol'] + " stock added to watchlist";
            			console.log("Data: " + response.data);

            			//insert new stock into view table
            			$scope.stocks.push(response.data);
 						console.log("Updated watchlist:" +$scope.stock);	
            		}
            			
            	}, function(response){
            		$scope.ServerResponse = "data: " + response.data +
                   					 "\n\n\n\nstatus: " + response.status;
                 			
            	});
            }

            function postDelData(data, index){
            		$http({
            		method: 'POST',
            		url:    'delete_watchlist.php',
            		data:    data,
            		headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' 
            	}	
            	}).then(function(response){

            		//	$scope.ServerResponse = response.data + " stock deleted from watchlist";
            			console.log("Data: " + response.data);

            			//Delete from view table
            			$scope.stocks.splice(index, 1);

            			if ($scope.stocks.length == 0) {
            				$scope.ServerResponse = "There are no symbols in your watchlist, please add one";
            			}


            	}, function(response){
            		$scope.ServerResponse = "data: " + response.data +
                   					 "\n\n\n\nstatus: " + response.status;
                 			
            	});
            }
 		}
 	})();
 </script>
</body>
</html>
