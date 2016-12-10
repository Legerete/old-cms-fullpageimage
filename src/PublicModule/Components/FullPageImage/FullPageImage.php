<?php

/**
 * @copyright   Copyright (c) 2016 Wunderman s.r.o. <wundermanprague@wunwork.cz>
 * @author      Petr Besir Horáček <sirbesir@gmail.com>
 * @package     FullPageImage
 */

namespace Wunderman\CMS\FullPageImage\PublicModule\Components\FullPageImage;

use Nette\Application\UI\Control;
use Kdyby\Doctrine\EntityManager;
use App\Entity\Attachment;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class FullPageImage extends Control
{

	/**
	 * @var EntityManager
	 */
	private $em;


	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @var array $entity_params
	 */
	public function render($entity_params)
	{
		if (!isset($entity_params['id'])) {
			throw new \InvalidArgumentException('Image id is not set.');
		}

		$this->getTemplate()->entity_params = $entity_params;
		$this->getTemplate()->image = $this->getAttachmentRepository()->find((int) $entity_params['id']);

		$this->getTemplate()->render(__DIR__.'/templates/FullPageImage.latte');
	}


	public function getAttachmentRepository()
	{
		return $this->em->getRepository(Attachment::class);
	}

}
