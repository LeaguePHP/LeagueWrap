<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 05.07.2017
 * Time: 19:32
 */

namespace LeagueWrap\Api;


class Masteries extends AbstractApi
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
     * Gets all the mastery pages of the given user object or id.
     *
     * @param mixed $identities
     *
     * @return array
     */
    public function masteryPages($identities)
    {
        $ids = $this->extractIds($identities);
        $ids = implode(',', $ids);

        $array = $this->request('summoner/'.$ids.'/masteries');
        $summoners = [];
        foreach ($array as $summonerId => $data) {
            $masteryPages = [];
            foreach ($data['pages'] as $info) {
                if (!isset($info['masteries'])) {
                    // seting the talents to an empty array
                    $info['masteries'] = [];
                }

                $masteriesInfo = $info['masteries'];
                unset($info['masteries']);
                $masteryPage = $this->attachStaticDataToDto(new MasteryPage($info));
                // set masterys
                $masteries = [];
                foreach ($masteriesInfo as $mastery) {
                    $id = $mastery['id'];
                    $mastery = $this->attachStaticDataToDto(new Mastery($mastery));
                    $masteries[$id] = $mastery;
                }
                $masteryPage->masteries = $masteries;
                $masteryPages[] = $masteryPage;
            }
            $summoners[$summonerId] = $masteryPages;
        }

        $this->attachResponses($identities, $summoners, 'masteryPages');

        if (is_array($identities)) {
            return $summoners;
        } else {
            return reset($summoners);
        }
    }
}