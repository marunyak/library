<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AndyLibTest extends TestCase
{
    private AndyLib $lib;
    private string $tmp;
    private array $server;

    protected function setUp(): void
    {
        $this->lib = new AndyLib();
        $this->tmp = sys_get_temp_dir().'/andylib-'.bin2hex(random_bytes(6));
        mkdir($this->tmp, 0700, true);
        $this->server = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $this->removeDir($this->tmp);
    }

    public function testFactorialRecurses(): void
    {
        $this->assertSame(1, $this->lib->factorl(0));
        $this->assertSame(1, $this->lib->factorl(1));
        $this->assertSame(120, $this->lib->factorl(5));
        $this->assertSame(2432902008176640000, $this->lib->factorl(20));
    }

    public function testFactorialRejectsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->lib->factorl(-1);
    }

    public function testUniqueIdIsRandomHex(): void
    {
        $id = $this->lib->unqID();
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $id);
        $this->assertNotSame($id, $this->lib->unqID());
    }

    public function testGetMonth(): void
    {
        $this->assertSame('January', $this->lib->getMonth(1));
        $this->assertSame('December', $this->lib->getMonth('12'));
        $this->assertFalse($this->lib->getMonth(13));
        $this->assertFalse($this->lib->getMonth('abc'));
    }

    public static function zodiacProvider(): array
    {
        return [
            ['21.3', 'Овен'], ['20.4', 'Овен'], ['21.4', 'Телец'],
            ['21.6', 'Близнецы'], ['22.6', 'Рак'], ['23.8', 'Лев'],
            ['24.8', 'Дева'], ['23.10', 'Весы'], ['24.10', 'Скорпион'],
            ['21.12', 'Стрелец'], ['22.12', 'Козерог'], ['31.12', 'Козерог'],
            ['1.1', 'Козерог'], ['20.01', 'Козерог'], ['21.1', 'Водолей'],
            ['18.02', 'Водолей'], ['19.2', 'Рыбы'], ['29.2', 'Рыбы'],
            ['20.3', 'Рыбы'], ['05.03', 'Рыбы'],
        ];
    }

    #[DataProvider('zodiacProvider')]
    public function testZodiac(string $birthday, string $sign): void
    {
        $this->assertSame($sign, $this->lib->zodiak($birthday));
    }

    public function testZodiacCoversEveryDayOfYear(): void
    {
        $day = new DateTimeImmutable('2000-01-01');
        for ($i = 0; $i < 366; $i++, $day = $day->modify('+1 day')) {
            $this->assertIsString($this->lib->zodiak($day->format('j.n')), $day->format('j.n'));
        }
    }

    public function testZodiacRejectsInvalidDates(): void
    {
        $this->assertFalse($this->lib->zodiak('32.1'));
        $this->assertFalse($this->lib->zodiak('30.2'));
        $this->assertFalse($this->lib->zodiak('1.13'));
        $this->assertFalse($this->lib->zodiak('abc'));
        $this->assertFalse($this->lib->zodiak('15'));
    }

    public static function browserProvider(): array
    {
        return [
            'chrome'  => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36', 'Google Chrome'],
            'edge'    => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36 Edg/120.0', 'Microsoft Edge'],
            'opera'   => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36 OPR/106.0', 'Opera'],
            'firefox' => ['Mozilla/5.0 (X11; Linux x86_64; rv:121.0) Gecko/20100101 Firefox/121.0', 'Mozilla Firefox'],
            'safari'  => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 14_2) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15', 'Safari'],
            'ie11'    => ['Mozilla/5.0 (Windows NT 10.0; Trident/7.0; rv:11.0) like Gecko', 'Internet Explorer'],
            'unknown' => ['curl/8.0', 'Неизвестный'],
        ];
    }

    #[DataProvider('browserProvider')]
    public function testGetBrowser(string $ua, string $expected): void
    {
        $this->assertSame($expected, $this->lib->getBrowser($ua));
    }

    public function testGetBrowserDefaultsToServerUserAgent(): void
    {
        unset($_SERVER['HTTP_USER_AGENT']);
        $this->assertSame('Неизвестный', $this->lib->getBrowser());
        $_SERVER['HTTP_USER_AGENT'] = 'Firefox/121.0';
        $this->assertSame('Mozilla Firefox', $this->lib->getBrowser());
    }

    public function testDumpEscapesHtml(): void
    {
        $this->expectOutputRegex('/^<pre>.*&lt;script&gt;.*<\/pre>$/s');
        $this->lib->dump('<script>');
    }

    public function testFindFileSearchesRecursively(): void
    {
        mkdir($this->tmp.'/a/b', 0700, true);
        touch($this->tmp.'/a/b/target.txt');
        touch($this->tmp.'/target.txt');
        touch($this->tmp.'/a/other.txt');

        $found = $this->lib->findFile('target.txt', $this->tmp);
        sort($found);
        $this->assertSame([$this->tmp.'/a/b/target.txt', $this->tmp.'/target.txt'], $found);
        $this->assertSame([], $this->lib->findFile('target.txt', $this->tmp.'/missing'));
    }

    public function testFindDirectorySearchesRecursively(): void
    {
        mkdir($this->tmp.'/x/cache', 0700, true);
        mkdir($this->tmp.'/cache/cache', 0700, true);
        touch($this->tmp.'/x/cachefile');

        $found = $this->lib->findDirectory('cache', $this->tmp);
        sort($found);
        $this->assertSame([$this->tmp.'/cache', $this->tmp.'/cache/cache', $this->tmp.'/x/cache'], $found);
    }

    public function testShowMsgEscapesByDefault(): void
    {
        $html = $this->lib->showMsg(['err" onclick="x' => '<b>hi</b>']);
        $this->assertStringContainsString('<div class="err&quot; onclick=&quot;x">&lt;b&gt;hi&lt;/b&gt;</div>', $html);
        $this->assertStringContainsString('<div class="ok"><b>hi</b></div>', $this->lib->showMsg(['ok' => '<b>hi</b>'], false));
        $this->assertFalse($this->lib->showMsg([]));
    }

    public function testGenerateCode(): void
    {
        $seen = [];
        for ($i = 0; $i < 2000; $i++) {
            $code = $this->lib->generateCode();
            $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{4,7}$/', $code);
            $seen += array_flip(str_split($code));
        }
        // Every char of the alphabet, including the first one and x/X, shows up.
        $this->assertCount(62, $seen);
    }

    public function testCleanAmp(): void
    {
        $this->assertSame('a.php?x=1&amp;y=2', $this->lib->cleanAmp('a.php?x=1&y=2'));
        $this->assertSame('a.php?x=1&amp;y=2', $this->lib->cleanAmp('a.php?x=1&amp;y=2'));
        $this->assertSame('&#123;&amp;&#x1F;', $this->lib->cleanAmp('&#123;&&#x1F;'));
    }

    public function testGetAge(): void
    {
        $today = new DateTimeImmutable('today');
        $tenYearsAgo = $today->modify('-10 years');
        $this->assertSame(10, $this->lib->getAge($tenYearsAgo->format('j'), $tenYearsAgo->format('n'), $tenYearsAgo->format('Y')));

        $tomorrow = $today->modify('+1 day -10 years');
        $this->assertSame(9, $this->lib->getAge($tomorrow->format('j'), $tomorrow->format('n'), $tomorrow->format('Y')));

        $this->assertFalse($this->lib->getAge(31, 2, 2000));
        $nextYear = $today->modify('+1 year');
        $this->assertFalse($this->lib->getAge(1, 1, $nextYear->format('Y')));
    }

    public function testGetIp(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.7';
        $this->assertSame('203.0.113.7', $this->lib->getIp());
        $_SERVER['REMOTE_ADDR'] = '2001:db8::1';
        $this->assertSame('2001:db8::1', $this->lib->getIp());
        $_SERVER['REMOTE_ADDR'] = 'not-an-ip';
        $this->assertSame('localhost', $this->lib->getIp());
        unset($_SERVER['REMOTE_ADDR']);
        $this->assertSame('localhost', $this->lib->getIp());
    }

    public function testGetHost(): void
    {
        $_SERVER['HTTP_HOST'] = 'WWW.Example.COM';
        $this->assertSame('example.com', $this->lib->getHost());
        $_SERVER['HTTP_HOST'] = 'https://www.example.com/path';
        $this->assertSame('example.com', $this->lib->getHost());
        $_SERVER['HTTP_HOST'] = 'mywww.example.com';
        $this->assertSame('mywww.example.com', $this->lib->getHost());
        unset($_SERVER['HTTP_HOST']);
        $this->assertSame('', $this->lib->getHost());
    }

    public function testCacheRoundTripAndExpiry(): void
    {
        $dir = $this->tmp.'/cache';
        $this->assertTrue($this->lib->setCache('users', ['a' => 1, 'b' => [2, 3]], $dir));
        $this->assertSame(['a' => 1, 'b' => [2, 3]], $this->lib->getCache('users', $dir));

        $this->assertTrue($this->lib->setCache('old', [1], $dir, 0));
        $this->assertFalse($this->lib->getCache('old', $dir));
        $this->assertFileDoesNotExist($dir.'/old.sys');

        $this->assertFalse($this->lib->getCache('missing', $dir));
    }

    public function testCacheNeverUnserializesObjects(): void
    {
        $dir = $this->tmp.'/cache';
        mkdir($dir);
        file_put_contents($dir.'/evil.sys', serialize(new ArrayObject([1])));
        $this->assertFalse($this->lib->getCache('evil', $dir));
    }

    public function testCacheRejectsPathTraversal(): void
    {
        $this->assertFalse($this->lib->setCache('../escape', [1], $this->tmp.'/cache'));
        $this->assertFalse($this->lib->getCache('../escape', $this->tmp.'/cache'));
        $this->assertFileDoesNotExist($this->tmp.'/escape.sys');
    }

    public function testLogsAppendJsonLines(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.7';
        $_SERVER['HTTP_USER_AGENT'] = 'Firefox/121.0';
        unset($_SERVER['HTTP_REFERER']);
        $dir = $this->tmp.'/logs';

        $this->assertTrue($this->lib->logs('app', 'first', $dir));
        $this->assertTrue($this->lib->logs('app', 'second', $dir));

        $lines = file($dir.'/app.log', FILE_IGNORE_NEW_LINES);
        $this->assertCount(2, $lines);
        $entry = json_decode($lines[1], true);
        $this->assertSame('second', $entry['message']);
        $this->assertSame('203.0.113.7', $entry['ip']);
        $this->assertSame('Mozilla Firefox', $entry['browser']);
        $this->assertSame('', $entry['query']);
    }

    public function testHtmlTags(): void
    {
        $this->assertSame("<div class='a b'>&lt;b&gt;</div>", $this->lib->htmlTags('div', '<b>', ['a', 'b']));
        $this->assertSame('<span><i>x</i></span>', $this->lib->htmlTags('span', '<i>x</i>', '', false));
        $this->assertSame('<h2>t</h2>', $this->lib->htmlTags('h2', 't'));
        $this->assertSame("<img src='a.png&#039; onerror=&#039;x'/>", $this->lib->htmlTags('img', "a.png' onerror='x"));
        $this->assertSame("<link rel='stylesheet' href='s.css'/>", $this->lib->htmlTags('link', 's.css'));
        $this->assertSame("<link rel='icon' href='f.ico'/>", $this->lib->htmlTags('link', 'f.ico', 'icon'));
        $this->assertSame("<div class='x&#039; onclick=&#039;y'></div>", $this->lib->htmlTags('div', '', "x' onclick='y"));
        $this->assertFalse($this->lib->htmlTags('script', 'x'));
        $this->assertFalse($this->lib->htmlTags('xh1y', 'x'));
    }

    public function testHasSsl(): void
    {
        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->lib->has_ssl());
        $_SERVER['HTTPS'] = 'off';
        $this->assertFalse($this->lib->has_ssl());
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->lib->has_ssl());
    }

    public function testInsertArrRecursive(): void
    {
        $arr = [];
        $this->lib->insertArrRecursive($arr, ['main'], 'root');
        $this->lib->insertArrRecursive($arr, ['a', 'b'], 'c', 'v');
        // An existing empty array on the path is reused, not mistaken for the root.
        $this->lib->insertArrRecursive($arr, ['root'], 'k', 0);
        // A key repeated on the path does not stop the walk early.
        $this->lib->insertArrRecursive($arr, ['a', 'a'], 'z', 1);
        $this->assertSame(['root' => ['k' => 0], 'a' => ['b' => ['c' => 'v'], 'a' => ['z' => 1]]], $arr);
    }

    public function testInsertArrRecursiveRejectsScalarOnPath(): void
    {
        $arr = ['a' => 'scalar'];
        $this->expectException(InvalidArgumentException::class);
        $this->lib->insertArrRecursive($arr, ['a'], 'b', 1);
    }

    public function testKeysArr(): void
    {
        $this->assertSame(
            ['[a][b]' => 1, '[a][c][d]' => 2, '[e]' => 3, '[0]' => 4],
            $this->lib->keysArr(['a' => ['b' => 1, 'c' => ['d' => 2]], 'e' => 3, 4])
        );
        $this->assertSame([], $this->lib->keysArr([]));
    }

    public function testSaveTheConfigRoundTrip(): void
    {
        $data = ['site' => 'My $site {x}', 'quote' => "it's", 'nested' => ['a' => 1]];
        $this->assertTrue($this->lib->savetheConfig($data, 'conf.php', $this->tmp.'/', 'config'));
        $this->assertSame($data, $this->includeConfig($this->tmp.'/conf.php', 'config'));
        $this->assertSame([], glob($this->tmp.'/*.tmp'));
    }

    public function testSaveTheConfigKeepsPreDefinedKeys(): void
    {
        $this->lib->savetheConfig(['a' => 'saved', 'b' => 'saved'], 'conf.php', $this->tmp.'/', 'config');
        $config = ['a' => 'existing'];
        include $this->tmp.'/conf.php';
        $this->assertSame(['a' => 'existing', 'b' => 'saved'], $config);
    }

    public function testSaveTheConfigBlocksCodeInjection(): void
    {
        $marker = $this->tmp.'/pwned';
        $payload = "x' => 1, 'y' => file_put_contents('{$marker}', 1), '";
        $data = [$payload => "v'); file_put_contents('{$marker}', 1); //"];

        $this->lib->savetheConfig($data, 'conf.php', $this->tmp.'/', 'config');
        $this->assertSame($data, $this->includeConfig($this->tmp.'/conf.php', 'config'));
        $this->assertFileDoesNotExist($marker);
    }

    public function testSaveTheConfigRejectsInvalidVariableName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->lib->savetheConfig(['a' => 1], 'conf.php', $this->tmp.'/', 'x; system("id")');
    }

    private function includeConfig(string $file, string $name): array
    {
        $$name = null;
        include $file;
        return $$name;
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $name) {
            if ($name === '.' || $name === '..') continue;
            $path = $dir.'/'.$name;
            is_dir($path) && !is_link($path) ? $this->removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }
}
