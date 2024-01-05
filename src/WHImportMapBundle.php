<?php

namespace WHSymfony\WHImportMapBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use WHSymfony\WHImportMapBundle\ImportMap\EntryPointsManager;
use WHSymfony\WHImportMapBundle\ImportMap\ImportMapRenderer;
use WHSymfony\WHImportMapBundle\Twig\ImportMapEntryPointsExtension;
use WHSymfony\WHImportMapBundle\Twig\ImportMapRendererExtension;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class WHImportMapBundle extends AbstractBundle
{
	public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$container->services()
			->set('whimportmap.entrypoints.manager', EntryPointsManager::class)
				->args([service('asset_mapper.importmap.config_reader')])
				->tag('twig.runtime')

			->set('whimportmap.renderer', ImportMapRenderer::class)
				->args([
					param('kernel.charset'),
					service('asset_mapper.importmap.generator'),
					service('whimportmap.entrypoints.manager'),
					service('assets.packages')->nullOnInvalid()
				])
				->tag('twig.runtime')

			->set('whimportmap.twig.importmap_renderer_extension', ImportMapRendererExtension::class)
				->tag('twig.extension')

			->set('whimportmap.twig.importmap_entrypoints_extension', ImportMapEntryPointsExtension::class)
				->tag('twig.extension')
		;
	}
}
