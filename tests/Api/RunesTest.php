<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiRunesTest extends PHPUnit_Framework_TestCase
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

    //TODO: change the call from summoner api to own rune api

    public function testRunes()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/74602/runes', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.runes.74602.json'));

        $api = new Api('key', $this->client);
        $runes = $api->summoner()->selectVersion('v3')->runePages(74602);
        $this->assertTrue($runes[0] instanceof LeagueWrap\Dto\RunePage);
    }

    public function testRuneArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/74602/runes', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.runes.74602.json'));

        $api = new Api('key', $this->client);
        $runes = $api->summoner()->selectVersion('v3')->runePages(74602);
        $this->assertTrue($runes[0][30] instanceof LeagueWrap\Dto\Rune);
    }

    public function testRunesSummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->twice();
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
        $bakasan = $api->summoner()->selectVersion('v3')->info(74602);
        $api->summoner()->selectVersion('v3')->runePages($bakasan);
        $this->assertEquals(5317, $bakasan->runePage(1)->rune(15)->runeId);
    }

    public function testRunesSummonerArray()
    {
        $this->markTestSkipped("Batch calls are not possible anymore");

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->twice();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/97235,7024/runes', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.runes.7024,97235.json'));
        $this->client->shouldReceive('request')
            ->with('v3/summoners/7024,97235', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.7024,97235.json'));

        $api = new Api('key', $this->client);
        $summoners = $api->summoner()->selectVersion('v3')->info([
            7024,
            97235,
        ]);
        $api->summoner()->selectVersion('v3')->runePages($summoners);
        $this->assertEquals(0, count($summoners['IS1c2d27157a9df3f5aef47']->runePage(1)->runes));
    }
}
