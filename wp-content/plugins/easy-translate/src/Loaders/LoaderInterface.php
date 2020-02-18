<?php

namespace EasyTranslate\Loaders;

interface LoaderInterface
{
    /**
     * Boot the loader
     */
    public function load(): void;
}