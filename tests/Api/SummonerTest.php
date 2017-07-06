<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiSummonerTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $client = m::mock('LeagueWrap\Client');
        $this->client = $client;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testInfo()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info('bakasan');
        $this->assertEquals(74602, $bakasan->id);
    }

    public function testInfoId()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3');
        $summoner->info(74602);
        $this->assertEquals('bakasan', $summoner->bakasan->name);
    }

    public function testAccountId()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->twice();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-account/101444', [
                'api_key' => 'key',
            ])->twice()
            ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3');
        $summoner->infoByAccountId(101444); //int accountId call
        $this->assertEquals('bakasan', $summoner->bakasan->name);

        $summoner->infoByAccountId('101444'); //string accountId call
        $this->assertEquals(101444, $summoner->bakasan->accountId);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAccountIdFalseType()
    {
        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3');
        $summoner->infoByAccountId(array(101444));
    }

    public function testInfoGetNull()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3');
        $summoner->info(74602);
        $this->assertTrue(is_null($summoner->nottherightname));
    }

    /**
     * @ig
     */
    public function testInfoMixed()
    {
        $this->markTestSkipped("Batch calls are not possible anymore");

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                    ->with('https://na1.api.riotgames.com/lol/summoner/')
                    ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/7024,97235', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.7024,97235.json'));

        $api = new Api('key', $this->client);
        $summoners = $api->summoner()->selectVersion('v3')->info([
            'bakasan',
            7024,
            97235,
        ]);
        $this->assertTrue(isset($summoners['IS1c2d27157a9df3f5aef47']));
    }

    public function testInfoDistinguishesBetweenIntegerIdsAndNumericNames()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/1337', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.1337.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summonerDto = $api->summoner()->selectVersion('v3')->info('1337');
        $this->assertEquals('1337', $summonerDto->name);

        $summonerDto = $api->summoner()->selectVersion('v3')->info(74602);
        $this->assertEquals('bakasan', $summonerDto->name);
    }

    /**
     * @expectedException LeagueWrap\Exception\ListMaxException
     */
    public function testInfoToManyIds()
    {
        $this->markTestSkipped("Batch calls are not possible anymore");

        $api = new Api('key', $this->client);
        $summoners = $api->summoner()->selectVersion('v3')->info([
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
            21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
            31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50,
        ]);
    }

    /**
     * @expectedException LeagueWrap\Exception\ListMaxException
     */
    public function testInfoToManyNames()
    {
        $this->markTestSkipped("Batch calls are not possible anymore");
        
        $api = new Api('key', $this->client);
        $summoners = $api->summoner()->selectVersion('v3')->info([
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
            'aa', 'ab', 'ac', 'ad', 'ae', 'af', 'ag', 'ah', 'ai', 'aj',
            'ba', 'bb', 'bc', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'bj',
            'ca', 'cb', 'cc', 'cd', 'ce', 'cf', 'cg', 'ch', 'ci', 'cj',
            'da', 'db', 'dc', 'dd', 'de', 'df', 'dg', 'dh', 'di', 'dj',
        ]);
    }

    public function testName()
    {
        $this->markTestSkipped("Batch calls are not possible anymore");

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602/name', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.name.74602.json'));

        $api = new Api('key', $this->client);
        $names = $api->summoner()->selectVersion('v3')->name(74602);
        $this->assertEquals('bakasan', $names[74602]);
    }

    public function testNameArray()
    {
        $this->markTestSkipped("Batch calls are not possible anymore");

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602,7024,97235/name', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.name.74602,7024,97235.json'));

        $api = new Api('key', $this->client);
        $names = $api->summoner()->selectVersion('v3')->name([
            74602,
            7024,
            97235,
        ]);
        $this->assertEquals('Jigsaw', $names[7024]);
    }





    public function testAllInfo()
    {
        $this->markTestSkipped("removd allInfo from summoner api. Runes and Masteries are requested in their own API");

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->times(3);
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602/masteries', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.masteries.74602.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602/runes', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.runes.74602.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3')->allInfo(74602);
        $this->assertTrue($summoner->masteryPages[0] instanceof LeagueWrap\Dto\MasteryPage);
        $this->assertTrue($summoner->runePages[0] instanceof LeagueWrap\Dto\RunePage);
    }

    /**
     * @expectedException LeagueWrap\Response\Http404
     */
    public function testInfoSummonerNotFound()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(new LeagueWrap\Response('', 404));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info('bakasan');
    }

    public function testInfoWithWrongParameter()
    {

    }
}
