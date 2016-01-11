<?php

namespace Mitul\Generator\Generators\Scaffold;

use Config;
use Illuminate\Support\Str;
use Mitul\Generator\CommandData;
use Mitul\Generator\FormFieldsGenerator;
use Mitul\Generator\Generators\GeneratorProvider;
use Mitul\Generator\Utils\GeneratorUtils;

class LiveViewGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $viewsPath;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_live_views', base_path('resources/views')).'/'.$this->commandData->modelNamePluralCamel.'/';
        $this->viewsPath = 'scaffold/views';
    }

    public function generate()
    {
        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $this->commandData->commandObj->comment("\nLive View created: ");

        $this->generateLive();
        $this->generateIndex();
        $this->generateShow();

    }


    private function generateLive()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('live.blade', $this->viewsPath);

        $fileName = 'live.blade.php';

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        \Log::info($fileName . ' Was Generated');
        $this->commandData->commandObj->info('live.blade.php created');
    }
    private function generateIndex()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('index.blade', $this->viewsPath);
        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);
        if ($this->commandData->paginate) {
            $paginateTemplate = $this->commandData->templatesHelper->getTemplate('paginate.blade', 'scaffold/views');
            $paginateTemplate = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $paginateTemplate);
            $templateData = str_replace('$PAGINATE$', $paginateTemplate, $templateData);
        } else {
            $templateData = str_replace('$PAGINATE$', '', $templateData);
        }
        $fileName = 'index.blade.php';
        $path = $this->path . $fileName;
        $this->commandData->fileHelper->writeFile($path, $templateData);
        \Log::info($fileName . ' Was Generated');
        $this->commandData->commandObj->info('index.blade.php created');
    }
    private function generateShow()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('show.blade', $this->viewsPath);
        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);
        $fileName = 'show.blade.php';
        $path = $this->path . $fileName;
        $this->commandData->fileHelper->writeFile($path, $templateData);
        \Log::info($fileName . ' Was Generated');
        $this->commandData->commandObj->info('show.blade.php created');
    }

}
