## Lazy Injection

> Cette fonctionnalité permet de créer un objet seulement quand celui ci doit être utilisé.

Prenons un exemple

```php
<?php
namespace  App\Services;

class SvgDatabase
{
    private $svg_to_html;
    private $svg_to_xml;
    
    public function __construct(SvgToHtml $html, SvgToXml $xml)
    {
        $this->svg_to_html = $html;
        $this->svg_to_xml = $xml;
    }
    
    public function svgToHtml()
    {
        $this->svg_to_html->save('...');
    }
    
    public function svgToXml()
    {
        $this->svg_to_xml->save('...');
    }
}
```

Utilisation
```php
<?php
use Cocoon\Dependency\Container;
use App\Services\SvgToHtml;
use App\Services\SvgToXml;

$di = Container::getInstance();

$di->bind(SvgToHtml::class, ['@lazy' => true]);
$di->bind(SvgToXml::class, ['@lazy' => true]);
$di->bind(SvgDatabase::class, ['@constructor' => [SvgToHtml::class, SvgToXml::class]]);

$proxy = $di->get(SvgDatabase::class);
$proxy->svgToHtml();
```
> Dans cette exemple la fonction svgToXml() n'est pas appelèe et la classe SvgToXml est initialisé et injecté dans la classe mais n'est jamais utilisée.

Vous pouvez aussi utiliser la fonction lazy() du conteneur. Vous obtenez le même résultat.

```php
<?php
use Cocoon\Dependency\Container;
use App\Services\SvgToHtml;
use App\Services\SvgToXml;

$di = Container::getInstance();

$di->lazy(SvgToHtml::class);
$di->lazy(SvgToXml::class);
$di->bind(SvgDatabase::class, ['@constructor' => [SvgToHtml::class, SvgToXml::class]]);

$proxy = $di->get(SvgDatabase::class);
$proxy->svgToHtml();
```
> Note: Il est possible d'ajouter des arguments au constructeur de la classe lazy loadée

Vous devez procéder de la manière suivante

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

$di->bind(LazyClass::class, [
    '@lazy' => true,
    '@constructor' => ['arg1', 'arg2']
    ]);

// ou

$di->lazy(LazyClass::class, ['arg1', 'arg2']);

```

Cette fonctionnalité utilise la bibliothèque php [Ocramius/ProxyManager](https://github.com/Ocramius/ProxyManager). Pour plus d'information vous pouvez consulter la documentation suivante [Lazy Loading Value Holder Proxy](https://ocramius.github.io/ProxyManager/docs/lazy-loading-value-holder.html)

