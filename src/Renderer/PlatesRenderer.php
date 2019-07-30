<?php
namespace Framework\Renderer;

use League\Plates\Engine;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Template\TemplatePath;
use Zend\Expressive\Template\ArrayParametersTrait;

class PlatesRenderer implements RenderInterface
{
    const DEFAULT_NAMESPACE = '__MAIN';

    use ArrayParametersTrait;
    /**
     * @var Engine
     */
    private $loader;

    private $template;

    private $path;

    private $globals = [];

    public function __construct(Engine $template = null)
    {
        if (null === $template) {
            $template = $this->createTemplate();
        }
        $this->template = $template;
    }


    /**
     * {@inheritDoc}
     */
    public function render(string $name, array $params = []): string
    {

        //$template = $templates->make('emails::welcome');
        //$folders = $this->template->getFolders();
        $params = $this->normalizeParams($params);
        return $this->template->render($name, $params);
    }

    /**
     * Add a path for template
     *
     * Multiple calls to this method without a namespace will trigger an
     * E_USER_WARNING and act as a no-op. Plates does not handle non-namespaced
     * folders, only the default directory; overwriting the default directory
     * is likely unintended.
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (! $namespace && ! $this->template->getDirectory()) {
            $this->template->setDirectory($path);
            return;
        }
        if (! $namespace) {
            trigger_error('Cannot add duplicate un-namespaced path in Plates template adapter', E_USER_WARNING);
            return;
        }
        $this->template->addFolder($namespace, $path, true);
    }



    /**
     * {@inheritDoc}
     */
    public function getPaths() : array
    {
        $paths = $this->template->getDirectory()
            ? [ $this->getDefaultPath() ]
            : [];
        foreach ($this->getPlatesFolders() as $folder) {
            $paths[] = new TemplatePath($folder->getPath(), $folder->getName());
        }
        return $paths;
    }
    /**
     * Proxies to the Plate Engine's `addData()` method.
     *
     * {@inheritDoc}
     */
   
    public function addDefaultParam(string $templateName, string $param, $value) : void
    {
        if (! is_string($templateName) || empty($templateName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '$templateName must be a non-empty string; received %s',
                is_object($templateName) ? get_class($templateName) : gettype($templateName)
            ));
        }
        if (! is_string($param) || empty($param)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '$param must be a non-empty string; received %s',
                is_object($param) ? get_class($param) : gettype($param)
            ));
        }
        $params = [$param => $value];
        if ($templateName === self::TEMPLATE_ALL) {
            $templateName = null;
        }
        $this->template->addData($params, $templateName);
    }
    
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }
    
    /**
     * Create a default Plates engine
     */
    private function createTemplate($path) : Engine
    {
        return new Engine($path);
    }
    /**
     * Create and return a TemplatePath representing the default Plates directory.
     */
    private function getDefaultPath() : TemplatePath
    {
        return new TemplatePath($this->template->getDirectory());
    }
    /**
     * Return the internal array of plates folders.
     *
     * @return \League\Plates\Template\Folder[]
     */
    private function getPlatesFolders() : array
    {
        $folders = $this->template->getFolders();
        $r = new ReflectionProperty($folders, 'folders');
        $r->setAccessible(true);
        return $r->getValue($folders);
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
