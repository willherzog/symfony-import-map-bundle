<?php

namespace WHSymfony\WHImportMapBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use WHSymfony\WHImportMapBundle\ImportMap\EntryPointsManager;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class ImportMapEntryPointsExtension extends AbstractExtension
{
	/**
	 * @inheritDoc
	 */
	public function getFunctions(): array
	{
		return [
			new TwigFunction('add_script_entry_point', [EntryPointsManager::class, 'addEntryPoint']),

			new TwigFunction('have_script_entry_points', [EntryPointsManager::class, 'haveEntryPoints'])
		];
	}
}
