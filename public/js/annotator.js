// Define the `phonecatApp` module
var Annotator = angular.module('Annotator', []);

// Define the `PhoneListController` controller on the `phonecatApp` module
Annotator.controller('AnnotatorController', function AnnotatorController($scope) {
  $scope.phones = [
    {
      name: 'Nexus S',
      snippet: 'Fast just got faster with Nexus S.'
    }, {
      name: 'Motorola XOOM™ with Wi-Fi',
      snippet: 'The Next, Next Generation tablet.'
    }, {
      name: 'MOTOROLA XOOM™',
      snippet: 'The Next, Next Generation tablet.'
    }
  ];
});
angular.
  module('Annotator').
  component('conatainerGraphSvg', {
    template: 'Hello, {{$ctrl.user}}!',
    controller: function GreetUserController() {
      this.user = 'world';
    }
  });