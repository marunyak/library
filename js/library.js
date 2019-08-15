(function(){

    function delay(milliseconds) {
        return function(result) {
          return new Promise(function(resolve, reject) {
            setTimeout(function() {
              resolve(result);
            }, milliseconds);
          });
        };
    }

    function ajax(options) {
        return new Promise(function(resolve, reject) {
          $.ajax(options).done(resolve).fail(reject);
        });
    }

})()