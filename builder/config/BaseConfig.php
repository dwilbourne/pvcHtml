<?php

namespace pvc\htmlbuilder\config;

readonly class BaseConfig
{
    public string $exceptionNamespace;
    public string $containerDefsNamespace;
    public string $containerDefsFilePath;

    public function __construct(
        public string $jsonDefsDir,
        public string $srcDir,
        public string $srcNamespace,
        public string $containerDefsDir,
        string $containerDefsFileName,
        string $exceptionDir,
    )
    {
        $this->exceptionNamespace = $srcNamespace.$exceptionDir;
        $this->containerDefsNamespace = $this->srcNamespace.$this->containerDefsDir;
        $this->containerDefsFilePath = $srcDir . '/'.$containerDefsDir.'/'.$containerDefsFileName;
    }
}