<?php

use LeagueWrap\Api;

class ApiTest extends PHPUnit_Framework_TestCase
{
    public function testChampion()
    {
        $api = new Api('key');
        $champion = $api->champion();
        $this->assertTrue($champion instanceof LeagueWrap\Api\Champion);
    }

    public function testSummoner()
    {
        $api = new Api('key');
        $summoner = $api->summoner();
        $this->assertTrue($summoner instanceof LeagueWrap\Api\Summoner);
    }

    public function testGame()
    {
        $api = new Api('key');
        $game = $api->game();
        $this->assertTrue($game instanceof LeagueWrap\Api\Game);
    }

    public function testLeague()
    {
        $api = new Api('key');
        $league = $api->league();
        $this->assertTrue($league instanceof LeagueWrap\Api\League);
    }

    public function testStats()
    {
        $api = new Api('key');
        $stats = $api->stats();
        $this->assertTrue($stats instanceof LeagueWrap\Api\Stats);
    }

    public function testTeam()
    {
        $api = new Api('key');
        $team = $api->team();
        $this->assertTrue($team instanceof LeagueWrap\Api\Team);
    }

    public function testStaticData()
    {
        $api = new Api('key');
        $staticData = $api->staticData();
        $this->assertTrue($staticData instanceof LeagueWrap\Api\Staticdata);
    }

    public function testMatch()
    {
        $api = new Api('key');
        $match = $api->match();
        $this->assertTrue($match instanceof LeagueWrap\Api\Match);
    }

    public function testMatchList()
    {
        $api = new Api('key');
        $matchlist = $api->matchList();
        $this->assertTrue($matchlist instanceof Leaguewrap\Api\MatchList);
    }

    public function testChampionMastery()
    {
        $api = new Api('key');
        $championMastery = $api->championMastery();
        $this->assertTrue($championMastery instanceof Leaguewrap\Api\ChampionMastery);
    }

    public function testCurrentGame()
    {
        $api = new Api('key');
        $currentGame = $api->currentGame();
        $this->assertTrue($currentGame instanceof LeagueWrap\Api\CurrentGame);
    }

    public function testStatus()
    {
        $api = new Api('key');
        $status = $api->status();
        $this->assertTrue($status instanceof LeagueWrap\Api\Status);
    }

    /**
     * @expectedException LeagueWrap\Exception\NoKeyException
     */
    public function testNoKeyException()
    {
        $api = new Api();
    }

    /**
     * @expectedException LeagueWrap\Exception\ApiClassNotFoundException
     */
    public function testApiClassNotFoundException()
    {
        $api = new Api('key');
        $nope = $api->nope();
    }

    public function testGetLimits()
    {
        $this->markTestSkipped();
        $api = new Api('key');
        $api->limit(5, 5);
        $this->assertEquals(10, count($api->getLimits()));
    }

    public function testGetLimitsOneRegion()
    {
        $this->markTestSkipped();
        $api = new Api('key');
        $api->limit(5, 5, 'na');
        $this->assertEquals(1, count($api->getLimits()));
    }
}
