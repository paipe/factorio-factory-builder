<?php


namespace App\App\Tasks;


use App\App\AppContext;
use Symfony\Component\Yaml\Yaml;

class GetDataFromYaml extends AbstractTask
{

    public function run()
    {
        $yamlFile = $this->getContext()->get(AppContext::K_YAML_FILE);
        $this->getContainer()->dataFromYaml = Yaml::parseFile($yamlFile);
    }

}