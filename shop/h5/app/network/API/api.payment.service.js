(function () {
    'use strict';

    angular
    .module('app')
    .factory('APIPaymentService', APIPaymentService);

    APIPaymentService.$inject =  ['$http', '$q', '$timeout', 'CacheFactory','ENUM'];

    function APIPaymentService($http, $q, $timeout, CacheFactory, ENUM) {

        var service = new APIEndpoint( $http, $q, $timeout, CacheFactory, 'APIPaymentService' );

        service.pay = _pay;
        service.typeList = _typeList;
        service.getCode = _getCode;
        service.doolyPay = _doolyPay;

        return service;

        function _pay( params ) {
            return this.fetch( '/v2/ecapi.payment.pay', params, false, function(res){
                return ENUM.ERROR_CODE.OK == res.data.error_code ? res : null;
            });
        }

        function _typeList(params) {
            return this.fetch( '/v2/ecapi.payment.types.list', params, false, function(res){
                return ENUM.ERROR_CODE.OK == res.data.error_code ? res.data.payment_types : null;
            });
        }

        function _getCode(params) {
            return this.fetch( '/v2/ecapi.payment.getcode', params, false, function(res){
                return res.data;
            });
        }
        function _doolyPay(params) {
            return this.fetch( '/v2/ecapi.payment.doolypay', params, false, function(res){
                return res.data;
            });
        }
    }

})();
