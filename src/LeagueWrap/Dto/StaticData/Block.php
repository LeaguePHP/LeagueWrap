<?php

namespace LeagueWrap\Dto\StaticData;

use LeagueWrap\Dto\AbstractListDto;

class Block extends AbstractListDto
{
    protected $listKey = 'items';

    /**
     * @param array $info
     */
    public function __construct(array $info)
    {
        if (isset($info['items'])) {
            $items = [];
            foreach ($info['items'] as $key => $item) {
                $items[$key] = new BlockItem($item);
            }
            $info['items'] = $items;
        }
        parent::__construct($info);
    }

    /**
     * Gets the item by key.
     *
     * @param mixed $key
     *
     * @return null
     */
    public function item($key)
    {
        if (!isset($this->info['items'][$key])) {
            return;
        }

        return $this->info['items'][$key];
    }
}
