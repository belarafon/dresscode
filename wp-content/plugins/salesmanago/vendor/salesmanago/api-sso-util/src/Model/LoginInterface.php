<?php

namespace SALESmanago\Model;


interface LoginInterface
{
    public function insert($settings);

    public function checkUser($settings);

    public function update($id, $settings);

    public function updateProperties($settings, $properties);
}
