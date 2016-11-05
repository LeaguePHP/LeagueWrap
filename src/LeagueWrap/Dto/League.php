<?php

namespace LeagueWrap\Dto;

class League extends AbstractListDto
{
    protected $listKey = 'entries';

    /**
     * @param array $info
     */
    public function __construct(array $info)
    {
        if (isset($info['entries'])) {
            $entries = [];
            foreach ($info['entries'] as $key => $entry) {
                $leagueEntry = new LeagueEntry($entry);
                $entries[$key] = $leagueEntry;
            }
            $info['entries'] = $entries;
        }

        $info['playerOrTeamName'] = null;

        parent::__construct($info);

        // get the current team
        if (isset($this->info['id'])) {
            $current = $this->entry($this->info['id']);
            if (!is_null($current)) {
                $this->info['playerOrTeam'] = $current;
            }
        }
    }

    /**
     * Select an entry by the team/summoner name or the team\summoner
     * id.
     *
     * @param mixed $identity
     *
     * @return LeagueEntry|null
     */
    public function entry($identity)
    {
        if (!isset($this->info['entries'])) {
            return;
        }

        $entries = $this->info['entries'];
        foreach ($entries as $entry) {
            // try the name
            if ($entry->playerOrTeamName == $identity) {
                return $entry;
            }
            // try the id
            if ($entry->playerOrTeamId == $identity) {
                return $entry;
            }
        }
    }
}
