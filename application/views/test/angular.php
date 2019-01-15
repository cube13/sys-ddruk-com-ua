<!doctype html>
<html ng-app>
  <head>
      <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
        <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <script src="/assets/js/angular.js"></script>
  </head>
  <body>
    <div>
      <label>Name:</label>
      <input type="text" ng-model="yourName" placeholder="Enter a name here">
      <hr>
      <h1>Hello {{yourName }}!</h1>
    
  </body>
</html>