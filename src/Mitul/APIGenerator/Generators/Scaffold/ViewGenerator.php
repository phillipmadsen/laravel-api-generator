<?php
/**
 * User: Mitul
 * Date: 14/02/15
 * Time: 6:00 PM
 */

namespace Mitul\APIGenerator\Generators\Scaffold;


use Config;
use Illuminate\Support\Str;
use Mitul\APIGenerator\CommandData;
use Mitul\APIGenerator\Generators\GeneratorProvider;

class ViewGenerator implements GeneratorProvider
{
	/** @var  CommandData */
	private $commandData;

	private $path;

	function __construct($commandData)
	{
		$this->commandData = $commandData;
		$this->path = Config::get('generator.path_views', base_path('resources/views')) . '/' . $this->commandData->modelNamePluralCamel . '/';
	}

	public function generate()
	{
		if(!file_exists($this->path))
			mkdir($this->path, 0777, true);

		$this->commandData->commandObj->comment("\nViews created: ");
		$this->generateFields();
		$this->generateIndex();
		$this->generateShow();
		$this->generateCreate();
		$this->generateEdit();
	}

	private function generateFields()
	{
		$fieldTemplate = $this->commandData->templatesHelper->getTemplate("field.blade", "Views");

		$fieldsStr = "";

		foreach($this->commandData->inputFields as $field)
		{
			$singleFieldStr = str_replace('$FIELD_NAME_TITLE$', Str::title($field['fieldName']), $fieldTemplate);
			$singleFieldStr = str_replace('$FIELD_NAME$', $field['fieldName'], $singleFieldStr);
			$fieldsStr .= $singleFieldStr."\n\n";
		}

		$templateData = $this->commandData->templatesHelper->getTemplate("fields.blade", "Views");

		$templateData = str_replace('$FIELDS$', $fieldsStr, $templateData);

		$fileName = "fields.blade.php";

		$path = $this->path . $fileName;

		$this->commandData->fileHelper->writeFile($path, $templateData);
		$this->commandData->commandObj->info("field.blade.php created");
	}

	private function generateIndex()
	{
		$templateData = $this->commandData->templatesHelper->getTemplate("index.blade", "Views");

		$templateData = $this->fillTemplate($templateData);

		$fileName = "index.blade.php";

		$headerFields = "";

		foreach($this->commandData->inputFields as $field)
		{
			$headerFields .= "<th>" . Str::title($field['fieldName']) . "</th>\n\t\t\t";
		}

		$headerFields = trim($headerFields);

		$templateData = str_replace('$FIELD_HEADERS$', $headerFields, $templateData);

		$tableBodyFields = "";

		foreach($this->commandData->inputFields as $field)
		{
			$tableBodyFields .= "<td>{!! $" . $this->commandData->modelNameCamel . "->". $field['fieldName'] . " !!}</td>\n\t\t\t\t\t";
		}

		$tableBodyFields = trim($tableBodyFields);

		$templateData = str_replace('$FIELD_BODY$', $tableBodyFields, $templateData);

		$path = $this->path . $fileName;

		$this->commandData->fileHelper->writeFile($path, $templateData);
		$this->commandData->commandObj->info("index.blade.php created");
	}

	private function generateShow()
	{
		$fieldTemplate = $this->commandData->templatesHelper->getTemplate("show.blade", "Views");

		$fileName = "show.blade.php";

		$path = $this->path . $fileName;

		$this->commandData->fileHelper->writeFile($path, $fieldTemplate);
		$this->commandData->commandObj->info("show.blade.php created");
	}

	private function generateCreate()
	{
		$templateData = $this->commandData->templatesHelper->getTemplate("create.blade", "Views");

		$templateData = $this->fillTemplate($templateData);

		$fileName = "create.blade.php";

		$path = $this->path . $fileName;

		$this->commandData->fileHelper->writeFile($path, $templateData);
		$this->commandData->commandObj->info("create.blade.php created");
	}

	private function generateEdit()
	{
		$templateData = $this->commandData->templatesHelper->getTemplate("edit.blade", "Views");

		$templateData = $this->fillTemplate($templateData);

		$fileName = "edit.blade.php";

		$path = $this->path . $fileName;

		$this->commandData->fileHelper->writeFile($path, $templateData);
		$this->commandData->commandObj->info("edit.blade.php created");
	}

	private function fillTemplate($templateData)
	{
		$templateData = str_replace('$MODEL_NAME$', $this->commandData->modelName, $templateData);
		$templateData = str_replace('$MODEL_NAME_PLURAL$', $this->commandData->modelNamePlural, $templateData);

		$templateData = str_replace('$MODEL_NAME_CAMEL$', $this->commandData->modelNameCamel, $templateData);
		$templateData = str_replace('$MODEL_NAME_PLURAL_CAMEL$', $this->commandData->modelNamePluralCamel, $templateData);

		return $templateData;
	}
}