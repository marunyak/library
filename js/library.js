(function(){

    /**
     * Make a delay.
     * @param number  milliseconds
    */

    function delay(milliseconds) {
        return function(result) {
          return new Promise(function(resolve, reject) {
            setTimeout(function() {
              resolve(result);
            }, milliseconds);
          });
        };
    }

    /**
     * Make a ajax request.
     * @param options for request
    */

    function ajax(options) {
        return new Promise(function(resolve, reject) {
          $.ajax(options).done(resolve).fail(reject);
        });
    }

    /**
     * Return count of min and max heights
     * @param array array of heights
    */

    function MinMax(A){
      return A.filter((item,i)     => i == 0 || i == A.length-1 || (A[i-1] < A[i] || A[i-1] > A[i]))
              .filter((item,i,arr) => i == 0 || i == arr.length-1 || (arr[i] < arr[i-1] && arr[i] < arr[i+1])
                || (arr[i-1] < arr[i] && arr[i] > arr[i+1]));
    }

    /**
     * Return number from string
     * @param string array of heights
    */

    function Atoi(str) {
      let number = 0;
      let temp_str = '';
      str = str.trimStart();
      if (!str) return number;
      str = str.split(' ');
      temp_str = str[0];
      for (let i = 0;i < temp_str.length;i++) {
          if (temp_str[0].match(/[a-zA-Z.?*]/)) return number;

          if ((temp_str[0].match(/[a-zA-Z.?*-\/]/) && temp_str.length === 1)
              || (temp_str[0].match(/[a-zA-Z-+]/) && temp_str[1].match(/[a-zA-Z-+]/))) return number;

          if (temp_str[0] && (temp_str[i] === '-' || temp_str[i] === '+')) {
              temp += temp_str[i];
              continue;
          }

          if (temp_str[i].match(/\D/)) break;
          temp += temp_str[i];
      }
      number = parseInt(temp);
      if(number >= 2147483648) return 2147483647;
      if(number <= -2147483648) return -2147483648;
      return number;
    }

    /**
     * Considers the amount of roman number
     * @param string roman number
    */

    function romanToInt(s) {
      const roman = {'I': 1, 'V':5, 'X':10,'L':50,'C':100,'D':500,'M':1000};
      let sum = 0;
      let temp = '';
      str = s.trimStart();
      arr = str;
      for(let index = 0; index < arr.length;index++){
        if (temp === index) {
            temp = '';
            continue;
        }
        if (arr[index] === 'I' && (arr[index+1] === 'V' || arr[index+1] === 'X')) {
            sum += roman[arr[index+1]] - roman[arr[index]];
            temp = index+1;
        } else if (arr[index] === 'X' && (arr[index+1] === 'L' || arr[index+1] === 'C')) {
            sum += roman[arr[index+1]] - roman[arr[index]];
            temp = index+1;
        } else if (arr[index] === 'C' && (arr[index+1] === 'D' || arr[index+1] === 'M')) {
            sum += roman[arr[index+1]] - roman[arr[index]];
            temp = index+1;
        } else {
            sum += roman[arr[index]];
        }
      }
      return sum;
    }

    /**
     * Given an array of integers, return indices of the two numbers such that they add up to a specific target.
     * @param array
     * @param number sum of two array items
    */

    function twoSum(nums, target) {
      for(let i = 0;i < nums.length;i++) {
          for(let j = i+1;j < nums.length;j++) {
              if(nums[i] + nums[j] === target) return [i,j];
          }
      }
    };

    /**
      * Given a sorted array nums, remove the duplicates in-place such that each element appear only once and return the new length.
      * @param array nums
      * @return {number}
    */

    function removeDuplicates(nums) {
        if (nums.length === 0) return 0;
        let i = 0;
        for (let j = 1; j < nums.length; j++) {
            if (nums[j] != nums[i]) {
                i++;
                nums[i] = nums[j];
            }
        }
        return i + 1;
    };


    /**
     * Given a sorted array and a target value, return the index if the target is found. If not, return the index where it would be if it were inserted in order.
     * @param {number[]} nums
     * @param {number} target
     * @return {number}
    */

    function searchInsert(nums, target) {
      let num = 0;
      if (nums.indexOf(target) !== -1) return nums.indexOf(target);
      for (let i = 0;i < nums.length;i++) {
          if ((nums[i] < target && nums[i+1] > target)
              || (nums.length-1 === i && nums[i] < target)) {
              return i+1;
          }
      }
      return num;
    };

    /**
       * Splits text into arrays depending on the number K
       * @param string text
       * @param number max length of word
    */

    function splitsText(S, K){
      let count = 0;
      let temp_src = '';
      let messages = [];
      if (typeof S != 'string' || S.length > 500  || typeof K != 'number' || (K < 1 || K > 500)) return -1;
      //S = S.replace(/[,.:;()?!-]/g, '').replace(/\s+/g, " ").trim();
      let arr = S.trimStart().trimEnd().split(' ');

      for(let i = 0;i < arr.length;i++) {

        if (arr[i].length > K) return -1;
        count += arr[i].length;

        if (count <= K){
          if (temp_src.length >= 1) {
            temp_src += ' ' + arr[i];
            count++;

            if (count === K) {
              count = 0;
              messages.push(temp_src);
              temp_src = '';
              continue;
            }

            if (count > K) {
              temp_src = temp_src.replace(new RegExp (arr[i], 'g'), '').trimEnd().trimStart();
              messages.push(temp_src);
              temp_src = arr[i];
              count = arr[i].length;
            }
          }
          else temp_src += arr[i];
        } else {
          count = 0;
          messages.push(temp_src);
          if (arr.length - 1 === i) messages.push(arr[i]);
          temp_src = '';
          temp_src += arr[i];
          count += arr[i].length;
          continue;
        }
        if (arr.length - 1 === i) messages.push(temp_src);
      }
      return messages;
    }
})()
