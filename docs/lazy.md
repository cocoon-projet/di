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
        $this->svg_to_xml = $html;
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
> Dans cette exemple la fonction svgToXml() n'est pas appelèe et la classe SvgToXml est initialé et injecté dans la classe mais n'est jamais utilisée.

Cette fonctionnalité utilise la bibliothèque php [Ocramius/ProxyManager](https://github.com/Ocramius/ProxyManager). Pour plus de renseignement vous pouvez consulter la documentation suivante [Lazy Loading Value Holder Proxy](https://ocramius.github.io/ProxyManager/docs/lazy-loading-value-holder.html)

