const INT32_MAX = 2 ** 31 - 1;
const INT32_MIN = -(2 ** 31);

const ROMAN = { I: 1, V: 5, X: 10, L: 50, C: 100, D: 500, M: 1000 };

/**
 * Make a delay for promise chains: `promise.then(delay(500))`.
 * @param {number} milliseconds
 * @returns {<T>(value: T) => Promise<T>} passes the value through after the delay
 */
export function delay(milliseconds) {
  return (value) => new Promise((resolve) => setTimeout(resolve, milliseconds, value));
}

/**
 * Make an HTTP request with fetch.
 * Resolves with parsed JSON when the response is JSON, otherwise with text.
 * Rejects with an Error carrying `status` and `response` on non-2xx responses,
 * and with a `TimeoutError` when `timeout` elapses.
 * @param {string | URL} url
 * @param {RequestInit & { timeout?: number }} [options] fetch options plus a timeout in ms
 * @returns {Promise<any>}
 */
export async function ajax(url, { timeout, ...init } = {}) {
  const signals = [init.signal, timeout > 0 && AbortSignal.timeout(timeout)].filter(Boolean);
  if (signals.length) init.signal = AbortSignal.any(signals);

  const response = await fetch(url, init);
  if (!response.ok) {
    const error = new Error(`HTTP ${response.status} ${response.statusText}`.trim());
    error.status = response.status;
    error.response = response;
    throw error;
  }
  const type = response.headers.get('content-type') ?? '';
  return type.includes('json') ? response.json() : response.text();
}

/**
 * Return the endpoints and local minima/maxima of a list of heights,
 * collapsing plateaus to a single point.
 * @param {number[]} heights
 * @returns {number[]}
 */
export function MinMax(heights) {
  const points = heights.filter((h, i) => i === 0 || h !== heights[i - 1]);
  return points.filter((h, i) => i === 0 || i === points.length - 1
    || Math.sign(h - points[i - 1]) !== Math.sign(points[i + 1] - h));
}

/**
 * Parse the leading integer of a string (skipping leading whitespace,
 * with an optional sign), clamped to the 32-bit signed range.
 * Returns 0 when the string does not start with a number.
 * @param {string} str
 * @returns {number}
 */
export function Atoi(str) {
  const match = /^\s*([+-]?\d+)/.exec(str);
  if (!match) return 0;
  return Math.min(INT32_MAX, Math.max(INT32_MIN, Number(match[1]))) || 0;
}

/**
 * Convert a roman numeral to a number.
 * Returns NaN when the string contains a character that is not a roman numeral.
 * @param {string} s
 * @returns {number}
 */
export function romanToInt(s) {
  const numeral = s.trim();
  let sum = 0;
  for (let i = 0; i < numeral.length; i++) {
    const value = ROMAN[numeral[i]];
    if (value === undefined) return NaN;
    const next = ROMAN[numeral[i + 1]] ?? 0;
    sum += value < next ? -value : value;
  }
  return sum;
}

/**
 * Return the indices of the two numbers that add up to `target`, or null if there are none.
 * @param {number[]} nums
 * @param {number} target
 * @returns {[number, number] | null}
 */
export function twoSum(nums, target) {
  const seen = new Map();
  for (let i = 0; i < nums.length; i++) {
    const j = seen.get(target - nums[i]);
    if (j !== undefined) return [j, i];
    if (!seen.has(nums[i])) seen.set(nums[i], i);
  }
  return null;
}

/**
 * Remove duplicates from a sorted array in place so each element appears once.
 * @param {number[]} nums sorted array, mutated
 * @returns {number} the new length; `nums.slice(0, length)` holds the unique values
 */
export function removeDuplicates(nums) {
  if (nums.length === 0) return 0;
  let i = 0;
  for (let j = 1; j < nums.length; j++) {
    if (nums[j] !== nums[i]) {
      i++;
      nums[i] = nums[j];
    }
  }
  return i + 1;
}

/**
 * Return the index of `target` in a sorted array, or the index where it would be inserted.
 * With duplicates, returns the first matching index.
 * @param {number[]} nums sorted ascending
 * @param {number} target
 * @returns {number}
 */
export function searchInsert(nums, target) {
  let lo = 0;
  let hi = nums.length;
  while (lo < hi) {
    const mid = (lo + hi) >>> 1;
    if (nums[mid] < target) lo = mid + 1;
    else hi = mid;
  }
  return lo;
}

/**
 * Find the smallest positive integer missing from an unsorted array. Does not mutate the input.
 * @param {number[]} nums
 * @returns {number}
 */
export function firstMissingPositive(nums) {
  const present = new Set(nums);
  let candidate = 1;
  while (present.has(candidate)) candidate++;
  return candidate;
}

/**
 * Defang an IPv4 address by replacing every "." with "[.]".
 * @param {string} address
 * @returns {string}
 */
export function defangIPaddr(address) {
  return address.replaceAll('.', '[.]');
}

/**
 * Split text into messages of at most K characters without breaking words.
 * Returns -1 when the input is invalid (S not a string or longer than 500,
 * K not an integer in 1..500) or a single word is longer than K.
 * @param {string} S
 * @param {number} K max message length
 * @returns {string[] | -1}
 */
export function splitsText(S, K) {
  if (typeof S !== 'string' || S.length > 500 || !Number.isInteger(K) || K < 1 || K > 500) return -1;

  const messages = [];
  let line = '';
  for (const word of S.split(/\s+/).filter(Boolean)) {
    if (word.length > K) return -1;
    if (!line) {
      line = word;
    } else if (line.length + 1 + word.length <= K) {
      line += ' ' + word;
    } else {
      messages.push(line);
      line = word;
    }
  }
  if (line) messages.push(line);
  return messages;
}
