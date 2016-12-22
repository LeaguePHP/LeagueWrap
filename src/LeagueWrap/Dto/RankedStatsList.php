<?php

namespace LeagueWrap\Dto;

/**
 * List made for multiple RankedStats when calling ranked stats endpoint multiple times
 * (for example for each summoner in current game).
 *
 * NOTE: This is not one of Riot's Dtos, it's just a wrapper around multiple RankedStats
 */
class RankedStatsList extends AbstractDto
{
    /**
     * RankedStatsList constructor.
     *
     * @param array $info
     */
    public function __construct(array $info)
    {
        $dtos = [];

        foreach ($info as $summonerId => $rankedStats) {
            $dtos[$summonerId] = new RankedStats($rankedStats);
        }

        parent::__construct($dtos);
    }

    /**
     * Get the ranked stats for player.
     *
     * @param int $playerStatId
     *
     * @return RankedStats|null
     */
    public function playerStat($playerStatId)
    {
        if (!isset($this->info[$playerStatId])) {
            return;
        }

        return $this->info[$playerStatId];
    }
}
