<?php

/**
 * @copyright   Copyright (c) 2016 Wunderman s.r.o. <wundermanprague@wunwork.cz>
 * @author      Petr Besir Horáček <sirbesir@gmail.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     FullPageImage
 */

namespace Wunderman\CMS\FullPageImage\DI;

use Nette\DI\CompilerExtension;
use Nette\Utils\Arrays;

class FullPageImageExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$extensionConfig = $this->loadFromFile(__DIR__ . '/config.neon');
		$this->compiler->parseServices($builder, $extensionConfig, $this->name);

		$builder->parameters = Arrays::mergeTree($builder->parameters,
			Arrays::get($extensionConfig, 'parameters', []));
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('privateComposePresenter')->addSetup(
			'addExtensionService',
			['fullPageImage', $this->prefix('@fullPageImageService')]
		);

		/**
		 * PublicModule component
		 */
		$builder->getDefinition('publicComposePresenter')->addSetup(
			'setComposeComponentFactory',
			['fullPageImage', $this->prefix('@publicFullPageImageFactory')]
		);

		/**
		 * PrivateModule component
		 */
		$builder->getDefinition('privateComposePresenter')->addSetup(
			'setComposeComponentFactory',
			['fullPageImage', $this->prefix('@privateFullPageImageFactory')]
		);
	}

}
