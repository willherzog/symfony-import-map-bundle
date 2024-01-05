<?php

namespace WHSymfony\WHImportMapBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use WHSymfony\WHImportMapBundle\ImportMap\ImportMapRenderer;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class ImportMapRendererExtension extends AbstractExtension
{
	/**
	 * @inheritDoc
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction('wh_importmap', [ImportMapRenderer::class, 'render'], ['is_safe' => ['html']])
		];
	}
}
