<?php

/**
 * @copyright   Copyright (c) 2016 Wunderman s.r.o. <wundermanprague@wunwork.cz>
 * @author      Petr Besir Horáček <sirbesir@gmail.com>
 * @package     FullPageImage
 */

namespace Wunderman\CMS\FullPageImage\PrivateModule\Service;


use App\PrivateModule\AttachmentModule\Model\Service\AttachmentService;
use App\PrivateModule\PagesModule\Presenter\IExtensionService;
use Nette\Application\UI\Form;
use Nette\Http\Request;
use Nette\Utils\ArrayHash;
use Nette\Utils\Arrays;

class FullPageImageService implements IExtensionService
{

	/**
	 * @var AttachmentService
	 */
	private $attachmentService;

	/**
	 * @var Request
	 */
	private $httpRequest;

	/**
	 * @var string
	 */
	private $alt;

	/**
	 * @var string
	 */
	private $anchor;


	/**
	 * FullPageImageService constructor.
	 *
	 * @param AttachmentService $attachmentService
	 * @param Request $httpRequest
	 */
	public function __construct(AttachmentService $attachmentService, Request $httpRequest)
	{
		$this->attachmentService = $attachmentService;
		$this->httpRequest = $httpRequest;
	}


	/**
	 * Prepare adding new item, add imputs to global form etc.
	 *
	 * @param Form $button
	 *
	 * @return mixed
	 */
	public function addItem(Form $form)
	{
		if(isset($form[self::ITEM_CONTAINER])) {
			unset($form[self::ITEM_CONTAINER]);
		}

		$item = $form->addContainer(self::ITEM_CONTAINER);
		$item->addHidden('itemId');
		$item->addText('alt')->setValue($this->alt);
		$item->addText('anchor')->setValue($this->anchor);
		$item->addUpload('image')->addCondition(Form::FILLED)->addRule(Form::IMAGE,
				'File must be image of type jpg, png or gif.');
		$item->addHidden('type')->setValue('fullPageImage');
	}


	/**
	 * @param Form $form
	 *
	 * @return mixed
	 */
	public function editItemParams(Form $form, $editItem)
	{
		$params = $this->createParamsAssocArray($editItem->getParams());

		$this->addItem($form);

		$form[self::ITEM_CONTAINER]->setDefaults([
			'itemId' => $editItem->id,
			'alt' => Arrays::get($params, 'alt', null),
			'anchor' => Arrays::get($params, 'anchor', null),
		]);
	}


	/**
	 * Make magic for creating new item, e.g. save new image and return his params for save.
	 *
	 * @var array $values Form values
	 *
	 * @return array Associated array in pair [ propertyName => value ] for store to the database
	 */
	public function processNew(Form $form, ArrayHash $values)
	{
		$file = $this->httpRequest->getFile(self::ITEM_CONTAINER)['image'];
		return [
			'id' => $this->attachmentService->processFile($file),
			'alt' => $values['alt'],
			'anchor' => $values['anchor'],
		];
	}


	/**
	 * Editing current edited item
	 *
	 * @var array $values Form values
	 * @var array $itemParams
	 *
	 * @return array
	 */
	public function processEdit(Form $form, ArrayHash $values, $itemParams)
	{
		$file = $this->httpRequest->getFile(self::ITEM_CONTAINER)['image'];
		return [
			'id' => $file ? $this->attachmentService->processFile($file) : Arrays::get($itemParams, 'id', null),
			'alt' => $values['alt'],
			'anchor' => $values['anchor'],
		];
	}


	/**
	 * Compute anchor for item on the page
	 *
	 * @var object
	 *
	 * @return string
	 */
	public function getAnchor($item)
	{
		$params = $this->createParamsAssocArray($item->params);
		return isset($params['anchor']) ? $params['anchor'] : false;
	}


	/**
	 * @return string
	 */
	public function getAddItemTemplate()
	{
		return realpath(__DIR__.'/../Templates/editItem.latte');
	}


	/**
	 * @return string
	 */
	public function getEditItemTemplate()
	{
		return $this->getAddItemTemplate();
	}


	/**
	 * @param $params
	 *
	 * @return array
	 */
	private function createParamsAssocArray($params)
	{
		$assocParams = [];
		foreach ($params as $param)
		{
			$assocParams[$param->name] = $param->value;
		}

		return $assocParams;
	}

}
