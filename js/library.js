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
    
    function MinMax(A){
      return A.filter((item,i)     => i == 0 || i == A.length-1 || (A[i-1] < A[i] || A[i-1] > A[i]))
              .filter((item,i,arr) => i == 0 || i == arr.length-1 || (arr[i] < arr[i-1] && arr[i] < arr[i+1])
                || (arr[i-1] < arr[i] && arr[i] > arr[i+1]));
    }
})()
