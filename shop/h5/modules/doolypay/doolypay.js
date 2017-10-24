(function () {

	'use strict';

	angular
		.module('app')
		.config(config);

	config.$inject = ['$stateProvider', '$urlRouterProvider'];

	function config($stateProvider, $urlRouterProvider) {

		$stateProvider
			.state('doolypay', {
				needAuth: true,
				url: '/doolypay',
				title: "付款成功",
				templateUrl: 'modules/doolypay/doolypay.html',
			});

	}

})();