<?php

namespace LeagueWrap\Dto;

class RankedStats extends AbstractListDto
{
    protected $listKey = 'champions';

    /**
     * @param array $info
     */
    public function __construct(array $info)
    {
        if (isset($info['champions'])) {
            $champions = [];
            foreach ($info['champions'] as $key => $champion) {
                // Note that champion ID 0 represents the combined stats for all champions.
                if ($champion['id'] == 0) {
                    $info['combinedStats'] = $champion;
                }

                $championStats = new ChampionStats($champion);
                // Don't transform combined stats into a Dto
                $champions[$key] = $champion['id'] == 0 ? $champion : $championStats;
            }
            $info['champions'] = $champions;
        }
        parent::__construct($info);
    }

    /**
     * Get the champion by the id returned by the API.
     *
     * @param int $championId
     *
     * @return ChampionStats|null
     */
    public function champion($championId)
    {
        if (!isset($this->info['champions'][$championId])) {
            return;
        }

        return $this->info['champions'][$championId];
    }
}
