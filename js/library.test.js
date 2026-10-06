import { describe, it, mock, afterEach } from 'node:test';
import assert from 'node:assert/strict';

import {
  delay,
  ajax,
  MinMax,
  Atoi,
  romanToInt,
  twoSum,
  removeDuplicates,
  searchInsert,
  firstMissingPositive,
  defangIPaddr,
  splitsText,
} from './library.js';

// node:test timeouts cannot interrupt synchronous code, so time it directly.
// 50k items take a few ms in O(n) and seconds in O(n^2).
function assertFast(fn, maxMs = 250) {
  const start = performance.now();
  const result = fn();
  const elapsed = performance.now() - start;
  assert.ok(elapsed < maxMs, `took ${elapsed.toFixed(0)}ms, expected < ${maxMs}ms`);
  return result;
}

describe('delay', () => {
  it('resolves with the passed value after the timeout', async () => {
    mock.timers.enable({ apis: ['setTimeout'] });
    try {
      const pending = Promise.resolve('value').then(delay(100));
      await Promise.resolve();
      mock.timers.tick(100);
      assert.equal(await pending, 'value');
    } finally {
      mock.timers.reset();
    }
  });
});

describe('ajax', () => {
  const realFetch = globalThis.fetch;
  afterEach(() => { globalThis.fetch = realFetch; });

  it('uses fetch, no jQuery, and parses JSON responses', async () => {
    globalThis.fetch = mock.fn(async () => Response.json({ ok: 1 }));
    assert.deepEqual(await ajax('/api', { method: 'POST', timeout: 5000 }), { ok: 1 });
    const [url, init] = globalThis.fetch.mock.calls[0].arguments;
    assert.equal(url, '/api');
    assert.equal(init.method, 'POST');
    assert.ok(init.signal instanceof AbortSignal);
  });

  it('returns text for non-JSON responses', async () => {
    globalThis.fetch = async () => new Response('hi', { headers: { 'content-type': 'text/plain' } });
    assert.equal(await ajax('/txt'), 'hi');
  });

  it('rejects on HTTP errors with the status attached', async () => {
    globalThis.fetch = async () => new Response('nope', { status: 404, statusText: 'Not Found' });
    await assert.rejects(ajax('/missing'), (err) => err.status === 404);
  });

  it('aborts after the timeout', async () => {
    // AbortSignal.timeout() is unref'd, so keep the event loop alive like a real request would.
    globalThis.fetch = (_url, { signal }) => new Promise((resolve, reject) => {
      const pending = setTimeout(resolve, 5000, new Response('late'));
      signal.addEventListener('abort', () => {
        clearTimeout(pending);
        reject(signal.reason);
      });
    });
    await assert.rejects(ajax('/slow', { timeout: 10 }), { name: 'TimeoutError' });
  });
});

describe('MinMax', () => {
  it('keeps endpoints and turning points', () => {
    assert.deepEqual(MinMax([1, 2, 3, 2, 1]), [1, 3, 1]);
    assert.deepEqual(MinMax([5, 1, 4, 2, 8]), [5, 1, 4, 2, 8]);
  });

  it('skips points inside a monotonic run and plateaus', () => {
    assert.deepEqual(MinMax([1, 2, 2, 3]), [1, 3]);
    assert.deepEqual(MinMax([1, 2, 2, 2, 1]), [1, 2, 1]);
  });

  it('does not duplicate a plateau at the end', () => {
    assert.deepEqual(MinMax([1, 3, 3]), [1, 3]);
    assert.deepEqual(MinMax([3, 3, 1]), [3, 1]);
  });

  it('handles tiny inputs', () => {
    assert.deepEqual(MinMax([]), []);
    assert.deepEqual(MinMax([7]), [7]);
    assert.deepEqual(MinMax([2, 2]), [2]);
  });
});

describe('Atoi', () => {
  it('parses plain and signed numbers', () => {
    assert.equal(Atoi('42'), 42);
    assert.equal(Atoi('   -42'), -42);
    assert.equal(Atoi('+7'), 7);
  });

  it('stops at the first non-digit', () => {
    assert.equal(Atoi('4193 with words'), 4193);
    assert.equal(Atoi('0032abc'), 32);
    assert.equal(Atoi('3.14'), 3);
  });

  it('returns 0 when there is no leading number', () => {
    assert.equal(Atoi(''), 0);
    assert.equal(Atoi('   '), 0);
    assert.equal(Atoi('words and 987'), 0);
    assert.equal(Atoi('+-12'), 0);
    assert.equal(Atoi('-'), 0);
    assert.equal(Atoi('-0'), 0);
  });

  it('clamps to the 32-bit signed range', () => {
    assert.equal(Atoi('2147483647'), 2147483647);
    assert.equal(Atoi('2147483648'), 2147483647);
    assert.equal(Atoi('-91283472332'), -2147483648);
    assert.equal(Atoi('99999999999999999999999'), 2147483647);
  });
});

describe('romanToInt', () => {
  it('converts additive and subtractive numerals', () => {
    assert.equal(romanToInt('III'), 3);
    assert.equal(romanToInt('IV'), 4);
    assert.equal(romanToInt('IX'), 9);
    assert.equal(romanToInt('LVIII'), 58);
    assert.equal(romanToInt('MCMXCIV'), 1994);
    assert.equal(romanToInt('MMMCMXCIX'), 3999);
  });

  it('does not leak globals', () => {
    romanToInt('XIV');
    assert.equal('str' in globalThis, false);
    assert.equal('arr' in globalThis, false);
  });

  it('returns NaN for characters that are not roman numerals', () => {
    assert.ok(Number.isNaN(romanToInt('XIZ')));
  });
});

describe('twoSum', () => {
  it('returns the indices of the pair', () => {
    assert.deepEqual(twoSum([2, 7, 11, 15], 9), [0, 1]);
    assert.deepEqual(twoSum([3, 2, 4], 6), [1, 2]);
    assert.deepEqual(twoSum([3, 3], 6), [0, 1]);
  });

  it('returns null when there is no pair', () => {
    assert.equal(twoSum([1, 2, 3], 100), null);
    assert.equal(twoSum([], 0), null);
  });

  it('is linear on large inputs', () => {
    const nums = Array.from({ length: 50_000 }, (_, i) => i);
    assert.equal(assertFast(() => twoSum(nums, -1)), null);
  });
});

describe('removeDuplicates', () => {
  it('compacts a sorted array in place and returns the new length', () => {
    const nums = [0, 0, 1, 1, 1, 2, 2, 3, 3, 4];
    assert.equal(removeDuplicates(nums), 5);
    assert.deepEqual(nums.slice(0, 5), [0, 1, 2, 3, 4]);
    assert.equal(removeDuplicates([]), 0);
  });
});

describe('searchInsert', () => {
  it('finds existing values', () => {
    assert.equal(searchInsert([1, 3, 5, 6], 5), 2);
    assert.equal(searchInsert([1, 3, 3, 3, 5], 3), 1);
  });

  it('returns the insert position for missing values', () => {
    assert.equal(searchInsert([1, 3, 5, 6], 2), 1);
    assert.equal(searchInsert([1, 3, 5, 6], 7), 4);
    assert.equal(searchInsert([1, 3, 5, 6], 0), 0);
    assert.equal(searchInsert([], 5), 0);
  });
});

describe('firstMissingPositive', () => {
  it('finds the smallest missing positive', () => {
    assert.equal(firstMissingPositive([1, 2, 0]), 3);
    assert.equal(firstMissingPositive([3, 4, -1, 1]), 2);
    assert.equal(firstMissingPositive([7, 8, 9, 11, 12]), 1);
    assert.equal(firstMissingPositive([1, 1, 2, 2]), 3);
    assert.equal(firstMissingPositive([]), 1);
  });

  it('does not mutate the input', () => {
    const nums = [3, 4, -1, 1];
    firstMissingPositive(nums);
    assert.deepEqual(nums, [3, 4, -1, 1]);
  });

  it('is linear on large inputs', () => {
    const nums = Array.from({ length: 50_000 }, (_, i) => i + 1);
    assert.equal(assertFast(() => firstMissingPositive(nums)), 50_001);
  });
});

describe('defangIPaddr', () => {
  it('replaces every dot', () => {
    assert.equal(defangIPaddr('1.1.1.1'), '1[.]1[.]1[.]1');
  });
});

describe('splitsText', () => {
  it('wraps words greedily up to K characters', () => {
    assert.deepEqual(splitsText('a cat sat on mat', 5), ['a cat', 'sat', 'on', 'mat']);
    assert.deepEqual(splitsText('ab cd ef', 5), ['ab cd', 'ef']);
    assert.deepEqual(splitsText('hello', 5), ['hello']);
  });

  it('does not erase earlier words that contain a later word', () => {
    assert.deepEqual(splitsText('abc b de', 4), ['abc', 'b de']);
  });

  it('treats regex metacharacters as plain text', () => {
    assert.deepEqual(splitsText('ab c+ d', 4), ['ab', 'c+ d']);
    assert.deepEqual(splitsText('ab ( d', 4), ['ab (', 'd']);
  });

  it('ignores repeated whitespace', () => {
    assert.deepEqual(splitsText('  a   b  ', 3), ['a b']);
  });

  it('returns -1 for invalid input', () => {
    assert.equal(splitsText('toolong', 3), -1);
    assert.equal(splitsText('a', 0), -1);
    assert.equal(splitsText(42, 3), -1);
    assert.equal(splitsText('a'.repeat(501), 500), -1);
  });
});
