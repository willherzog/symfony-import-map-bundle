<?php

namespace WHSymfony\WHImportMapBundle\ImportMap;

use Symfony\Component\AssetMapper\ImportMap\ImportMapConfigReader;

use Twig\Extension\RuntimeExtensionInterface;

/**
 * Compiles a list of JavaScript entry points for the current page.
 *
 * @author Will Herzog <willherzog@gmail.com>
 */
class EntryPointsManager implements RuntimeExtensionInterface
{
	protected readonly array $configuredEntryPoints;

	protected array $entryPointsForPage = [];

	public function __construct(ImportMapConfigReader $importMapConfigReader)
	{
		$entryPointNames = [];

		foreach( $importMapConfigReader->getEntries() as $entry ) {
			if( $entry->isEntrypoint ) {
				$entryPointNames[] = $entry->importName;
			}
		}

		$this->configuredEntryPoints = $entryPointNames;
	}

	public function addEntryPoint(string $entryPointName): void
	{
		if( !in_array($entryPointName, $this->configuredEntryPoints, true) ) {
			throw new \InvalidArgumentException(sprintf('"%s" does not match the name of a configured entry point in importmap.php', $entryPointName));
		} elseif( !in_array($entryPointName, $this->entryPointsForPage, true) ) {
			$this->entryPointsForPage[] = $entryPointName;
		}
	}

	public function haveEntryPoints(): bool
	{
		return $this->entryPointsForPage !== [];
	}

	public function getEntryPoints(): array
	{
		return $this->entryPointsForPage;
	}
}
