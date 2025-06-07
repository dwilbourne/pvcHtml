<?php

namespace pvc\htmlbuilder\config;

readonly class BuilderConfig
{
    public string $jsonDefs;
    public string $targetDir;
    public string $targetNamespace;
    public string $baseNamespace;

    public function __construct(
        BaseConfig $baseConfig,
        string $jsonDefsFileName,
        string $targetDir,
        string $baseDir,
    ) {
        $this->jsonDefs = $baseConfig->jsonDefsDir.$jsonDefsFileName;
        $this->targetDir = $baseConfig->srcDir.$targetDir;
        $this->targetNamespace = $baseConfig->srcNamespace.$targetDir;
        $this->baseNamespace = $baseConfig->srcNamespace.$baseDir;
    }
}