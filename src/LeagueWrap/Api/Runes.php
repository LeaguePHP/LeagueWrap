<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto;
use LeagueWrap\Dto\Mastery;
use LeagueWrap\Dto\MasteryPage;
use LeagueWrap\Dto\Rune;
use LeagueWrap\Dto\RunePage;

class Runes extends AbstractApi
{
    //TODO: build this ->

    /**
     * Valid version for this api call.
     *
     * @var array
     */
    protected $versions = [
        'v3',
    ];

    /**
     * A list of all permitted regions for the Champion api call.
     *
     * @param array
     */
    protected $permittedRegions = [
        'br',
        'eune',
        'euw',
        'lan',
        'las',
        'na',
        'oce',
        'ru',
        'tr',
        'kr',
        'jp',
    ];

    /**
     * The amount of time we intend to remember the response for.
     *
     * @var int
     */
    protected $defaultRemember = 600;

    /**
     * Attempt to get a summoner by key.
     *
     * @param string $key
     *
     * @return object|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return $this->getRegion()->getPlatformDomain();
    }

    /**
     * Gets all rune pages of the given user object or id.
     *
     * @param mixed $summonerId
     *
     * @return array
     */
    public function runePages($summonerId)
    {
        $ids = $this->extractIds($summonerId);
        $ids = implode(',', $ids);

        $array = $this->request('summoner/'.$ids.'/runes');
        $summoners = [];
        foreach ($array as $summonerId => $data) {
            $runePages = [];
            foreach ($data['pages'] as $info) {
                if (!isset($info['slots'])) {
                    // no runes in this page
                    $info['slots'] = [];
                }

                $slots = $info['slots'];
                unset($info['slots']);

                $runePage = $this->attachStaticDataToDto(new RunePage($info));

                // set runes
                $runes = [];
                foreach ($slots as $slot) {
                    $id = $slot['runeSlotId'];
                    $rune = $this->attachStaticDataToDto(new Rune($slot));
                    $runes[$id] = $rune;
                }
                $runePage->runes = $runes;
                $runePages[] = $runePage;
            }
            $summoners[$summonerId] = $runePages;
        }

        $this->attachResponses($summonerId, $summoners, 'runePages');

        if (is_array($summonerId)) {
            return $summoners;
        } else {
            return reset($summoners);
        }
    }
}
