//-------------------------------------------For Delivery Form Validators ----------------------------------------------
var hasLoadedValidators;
var loadDeliveryValidators;

(function () {
    hasLoadedValidators = [];
    loadDeliveryValidators = function (key, jsonData) {
        if(hasLoadedValidators.indexOf(key) == -1){
            hasLoadedValidators.push(key);
            if(jsonData){
                for(key in jsonData){
                    $.each(jsonData[key], function(key, val) {
                        val.validate = eval(val.validate);
                        $("#order-form").yiiActiveForm("add", val);
                    });
                }
            }
        }
    };
})();