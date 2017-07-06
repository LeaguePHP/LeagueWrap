<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiMasteriesTest extends PHPUnit_Framework_TestCase
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

    //TODO: change the call from summoner api to own mastery api

    public function testMasteries()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/74602/masteries', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.74602.json'));

        $api = new Api('key', $this->client);
        $masteries = $api->summoner()->selectVersion('v3')->masteryPages(74602);
        $this->assertTrue($masteries[0] instanceof LeagueWrap\Dto\MasteryPage);
    }

    public function testMasteriesArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/74602/masteries', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.74602.json'));

        $api = new Api('key', $this->client);
        $masteries = $api->summoner()->selectVersion('v3')->masteryPages(74602);
        $this->assertTrue($masteries[0][4342] instanceof LeagueWrap\Dto\Mastery);
    }

    public function testMasteriesArrayOnlyOneMasterySummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://br.api.pvp.net/api/lol/br/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/401129,1234567823/masteries', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.401129.1234567823.br.json'));

        $api = new api('key', $this->client);
        $masteries = $api->setRegion('BR')->summoner()->selectVersion('v3')->masteryPages([401129, 1234567823]);
        $this->assertTrue(is_array($masteries[401129]));
    }

    public function testMasteriesSummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->twice();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/74602/masteries', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.74602.json'));
        $this->client->shouldReceive('request')
            ->with('v3/summoners/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info(74602);
        $api->summoner()->selectVersion('v3')->masteryPages($bakasan);
        $this->assertEquals(2, $bakasan->masteryPage(1)->mastery(4212)->rank);
    }

    public function testMasteriesSummonerArray()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->times(2);
        $this->client->shouldReceive('request')
            ->with('v3/summoners/97235,7024/masteries', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.7024,97235.json'));
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
        $api->summoner()->selectVersion('v3')->masteryPages($summoners);
        $this->assertEquals(0, count($summoners['IS1c2d27157a9df3f5aef47']->masteryPages));
    }
}
