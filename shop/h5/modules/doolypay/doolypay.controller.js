(function () {

	'use strict';

	angular
		.module('app')
		.controller('DoolyPayController', DoolyPayController);

    DoolyPayController.$inject = ['$scope', '$http', '$location', '$state', '$stateParams'];

	function DoolyPayController($scope, $http, $location, $state, $stateParams) {

		var orderId = $stateParams.order;

		$scope.touchDetail = function () {
			$state.go('order-detail', {
				order: orderId
			});
		}

	}

})();