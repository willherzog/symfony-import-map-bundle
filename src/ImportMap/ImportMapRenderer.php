<?php

namespace WHSymfony\WHImportMapBundle\ImportMap;

use Symfony\Component\Asset\Packages;
use Symfony\Component\AssetMapper\ImportMap\ImportMapGenerator;

use Twig\Extension\RuntimeExtensionInterface;

use WHSymfony\WHImportMapBundle\ImportMap\EntryPointsManager;

/**
 * Based on Symfony\Component\AssetMapper\ImportMap\ImportMapRenderer from the symfony/asset-mapper package.
 * Fixes some issues with the original class's ->render() method (since all its properties and methods were
 * marked as private, simply extending the original class was not an option).
 */
class ImportMapRenderer implements RuntimeExtensionInterface
{
	public function __construct(
		protected readonly string $charset,
		protected readonly ImportMapGenerator $importMapGenerator,
		protected readonly EntryPointsManager $entryPointsManager,
		protected readonly ?Packages $assetPackages = null,
		protected readonly string|false $polyfillImportName = 'es-module-shims',
		protected readonly array $scriptAttributes = []
	) {}

	protected function escapeAttributeValue(string $value): string
	{
		return htmlspecialchars($value, \ENT_COMPAT | \ENT_SUBSTITUTE, $this->charset);
	}

	protected function createAttributesString(array $attributes): string
	{
		$attributeString = '';

		$attributes += $this->scriptAttributes;

		if( isset($attributes['src']) || isset($attributes['type']) ) {
			throw new \InvalidArgumentException(sprintf('The "src" and "type" attributes are not allowed on the <script> tag rendered by "%s".', self::class));
		}

		foreach( $attributes as $name => $value ) {
			$attributeString .= ' ';

			if( $value === true ) {
				$attributeString .= $name;
				continue;
			}

			$attributeString .= sprintf('%s="%s"', $name, $this->escapeAttributeValue($value));
		}

		return $attributeString;
	}

	public function render(array $attributes = []): string
	{
		$entryPoints = $this->entryPointsManager->getEntryPoints();
		$importMapData = $this->importMapGenerator->getImportMapData($entryPoints);
		$importMap = [];
		$modulePreloads = [];
		$cssLinks = [];
		$polyFillPath = null;

		foreach( $importMapData as $importName => $data ) {
			$path = $data['path'];

			if( $this->assetPackages ) {
				// ltrim so the subdirectory can be prepended (if needed)
				$path = $this->assetPackages->getUrl(ltrim($path, '/'));
			}

			// if this represents the polyfill, hide it from the import map
			if( $importName === $this->polyfillImportName ) {
				$polyFillPath = $path;
				continue;
			}

			// jquery should always be preloaded, but it isn't being detected as such based on import statements
			if( $importName === 'jquery' ) {
				$preload = true;
			} else {
				$preload = $data['preload'] ?? false;
			}

			// for subdirectories or CDNs, the import name needs to be the full URL
			if( str_starts_with($importName, '/') && $this->assetPackages ) {
				$importName = $this->assetPackages->getUrl(ltrim($importName, '/'));
			}

			if( $data['type'] !== 'css' ) {
				$importMap[$importName] = $path;

				if( $preload ) {
					$modulePreloads[] = $path;
				}
			} elseif( $preload ) {
				$cssLinks[] = $path;
				$importMap[$importName] = 'data:application/javascript,';
			} else {
				$importMap[$importName] = 'data:application/javascript,'.rawurlencode(sprintf('document.head.appendChild(Object.assign(document.createElement("link"),{rel:"stylesheet",href:"%s"}))', addslashes($path)));
			}
		}

		$output = '';

		foreach( $cssLinks as $url ) {
			$url = $this->escapeAttributeValue($url);

			$output .= "<link rel=\"stylesheet\" href=\"$url\">\n";
		}

		$scriptAttributes = $this->createAttributesString($attributes);

		if( $this->polyfillImportName !== false && $polyFillPath !== null ) {
			$url = $this->escapeAttributeValue($polyFillPath);

			$output .= "<script async src=\"$url\"$scriptAttributes></script>\n";
		}

		$importMapJson = json_encode(['imports' => $importMap], \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_HEX_TAG);

		$output .= <<<HTML
		<script type="importmap"$scriptAttributes>
		$importMapJson
		</script>

		HTML;

		foreach( $modulePreloads as $url ) {
			$url = $this->escapeAttributeValue($url);

			$output .= "<link rel=\"modulepreload\" href=\"$url\">\n";
		}

		if( count($entryPoints) > 0 ) {
			$output .= "<script type=\"module\"$scriptAttributes>";
			$imports = [];

			foreach( $entryPoints as $entryPointName ) {
				$entryPointName = $this->escapeAttributeValue($entryPointName);
				$entryPointName = str_replace("'", "\\'", $entryPointName);

				$imports[] = "import '$entryPointName';";
			}


			$output .= implode(' ', $imports) . '</script>';
		}

		return $output;
	}
}
