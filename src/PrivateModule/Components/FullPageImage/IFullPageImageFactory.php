<?php

namespace Wunderman\CMS\FullPageImage\PrivateModule\Components\FullPageImage;

interface IFullPageImageFactory
{

	/**
	 * @return FullPageImage
	 * @param  array $componentParams
	 */
	public function create(array $componentParams);

}
