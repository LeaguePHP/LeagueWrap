<?php

namespace LeagueWrap\Dto;

/**
 * Class Ban.
 */
class Ban extends AbstractDto
{
    use ImportStaticTrait;

    protected $staticFields = [
        'championId' => 'champion',
    ];
}
