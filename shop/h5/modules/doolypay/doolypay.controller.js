(function () {

	'use strict';

	angular
		.module('app')
		.controller('DoolyPayController', DoolyPayController);

    DoolyPayController.$inject = ['$scope', '$http', '$location', '$state', '$stateParams', 'API', 'PaymentModel', '$interval'];

	function DoolyPayController($scope, $http, $location, $state, $stateParams, API, PaymentModel, $interval) {
        $scope.getDoolyCode = _getDoolyCode;
        $scope.doolyPay = _doolyPay;
        $scope.paymentModel = PaymentModel;
        $scope.paracont = "发送短信验证码";
        $scope.paraclass = "but_null";
        $scope.paraevent = false;
        $scope.isPayPassword = false;
        $scope.doolyCode = '';
        $scope.doolyPassWord = '';

        function _getDoolyCode() {
            console.log(PaymentModel);
            var params = {order:PaymentModel.order.id,code:"doolypay.wap"};
            API.payment.getCode(params)
                .then(function (res) {
                    if(res.code == '0'){
                        if(res.isPayPassword == '1'){
                            $scope.isPayPassword = true;
                        }else{
                            $scope.isPayPassword = false;
                        }
                        var second = 120,
                            timePromise = undefined;

                        timePromise = $interval(function(){
                            if(second<=0){
                                $interval.cancel(timePromise);
                                timePromise = undefined;

                                second = 120;
                                $scope.paracont = "重发验证码";
                                $scope.paraevent = false;
                            }else{
                                $scope.paracont = second + "秒后可重发";
                                $scope.paraevent = true;
                                second--;

                            }
                        },1000,200);
                    }else{
                        $scope.toast(res.info);
                    }
                });
        }

        function _doolyPay() {
            console.log(PaymentModel);
            console.log($scope.doolyCode);
            console.log($scope.doolyPassWord);
            if (!$scope.doolyCode || $scope.doolyCode < 1) {
                $scope.toast('请输入短信验证码');
                return;
            }
            if ((!$scope.doolyPassWord || $scope.doolyPassWord < 1) && $scope.isPayPassword) {
                $scope.toast('请输入支付密码');
                return;
            }
            var params = {order:PaymentModel.order.id,doolyCode:$scope.doolyCode,doolyPassWord:''};
            if($scope.isPayPassword){
                params.doolyPassWord = $scope.doolyPassWord;
            }
            console.log(params);
            API.payment.doolyPay(params)
                .then(function (res) {
                    if(res.code == '0'){
                        console.log(res);
                        $state.go('payment-success', {
                            order: $scope.paymentModel.order.id
                        });
                    }else{
                        $scope.toast(res.info);
                        $state.go('payment-failed', {
                            order: $scope.paymentModel.order.id
                        });
                    }
                });
        }

        function _reload() {
            $scope.paymentModel
                .reload()
                .then(function (succeed) {
                    if (succeed) {

                    }
                });
        }

        _reload();

	}

})();