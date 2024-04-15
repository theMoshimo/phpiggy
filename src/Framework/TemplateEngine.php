<?php

declare(strict_types=1);

namespace Framework;

/**
 * Class Template Engine
 * 
 * A simple template engine for rendering templates with data
 * 
 */
class TemplateEngine
{
    private array $globalTemplateData = [];

    // The base path for template files.
    public function __construct(private string $basePath)
    {
    }

    // Renders a template with the provided data.
    public function render(string $template, array $data = [])
    {
        // Extract the data into variables.
        extract($data, EXTR_SKIP);
        extract($this->globalTemplateData, EXTR_SKIP);
        // Start output buffering
        ob_start();

        // Include the resolved template file
        include $this->resolve($template);

        // Get the contents of the output buffer and clean it.
        $output = ob_get_contents();
        ob_end_clean();

        // Return the rendered output.
        return $output;
    }

    public function resolve(string $path)
    {
        return "{$this->basePath}/{$path}";
    }
    public function addGlobal(string $key, mixed $value)
    {
        $this->globalTemplateData[$key] = $value;
    }
}
