<?php

namespace LeagueWrap\Dto;

use LeagueWrap\Api\Staticdata;
use LeagueWrap\StaticOptimizer;

abstract class AbstractDto
{
    protected $info;

    /**
     * Set up the information about this response.
     *
     * @param array $info
     */
    public function __construct(array $info)
    {
        $this->info = $info;
    }

    /**
     * Check if an attribute exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return !is_null($this->$key);
    }

    /**
     * Gets the attribute of this Dto.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Sets a new attribute for this Dto.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Gets the attribute of this Dto.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        if (isset($this->info[$key])) {
            return $this->info[$key];
        }
    }

    /**
     * Sets a new attribute for this Dto.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->info[$key] = $value;

        return $this;
    }

    /**
     * Attempts to load all static data within the children DTO
     * objects.
     *
     * @param Staticdata $staticData
     *
     * @return $this
     */
    public function loadStaticData(Staticdata $staticData)
    {
        $fields = $this->getStaticFields();
        $optimizer = new StaticOptimizer();
        $optimizer->optimizeFields($fields)
                  ->setStaticInfo($staticData);
        $this->addStaticData($optimizer);

        return $this;
    }

    /**
     * Returns the raw info array.
     *
     * @return array
     */
    public function raw()
    {
        // unpack
        $raw = [];
        foreach ($this->info as $key => $info) {
            if (is_array($info)) {
                $info = $this->unpack($info);
            }
            // check if it's just a single object
            if ($info instanceof self) {
                $info = $info->raw();
            }
            // set the raw value
            $raw[$key] = $info;
        }

        return $raw;
    }

    /**
     * Gets all static fields that we expect to need to get all static data
     * for any child dto object.
     *
     * @return array
     */
    protected function getStaticFields()
    {
        $fields = [];
        foreach ($this->info as $info) {
            if (is_array($info)) {
                foreach ($info as $value) {
                    if ($value instanceof self) {
                        $fields += $value->getStaticFields();
                    }
                }
            }
            if ($info instanceof self) {
                $fields += $info->getStaticFields();
            }
        }

        return $fields;
    }

    /**
     * Attempts to add the static data that we got from getStaticData to
     * any children DTO objects.
     *
     * @param StaticOptimizer $optimizer
     *
     * @return void
     */
    protected function addStaticData(StaticOptimizer $optimizer)
    {
        foreach ($this->info as $info) {
            if (is_array($info)) {
                foreach ($info as $value) {
                    if ($value instanceof self) {
                        $value->addStaticData($optimizer);
                    }
                }
            }
            if ($info instanceof self) {
                $info->addStaticData($optimizer);
            }
        }
    }

    /**
     * Unpacks an array that contains Dto objects.
     *
     * @param array $info
     *
     * @return array
     */
    protected function unpack(array $info)
    {
        $return = [];
        foreach ($info as $key => $value) {
            if ($value instanceof self) {
                $value = $value->raw();
            }
            $return[$key] = $value;
        }

        return $return;
    }
}
