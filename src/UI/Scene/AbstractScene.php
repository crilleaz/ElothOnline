<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Game;
use Game\Player\Player;
use Twig\Environment;

abstract class AbstractScene implements SceneInterface
{
    public function __construct(protected readonly Game $game, private readonly Environment $renderer)
    {

    }

    abstract public function run(): string;

    protected function renderTemplate(string $templateName, array $parameters = []): string
    {
        $fullTemplateName = sprintf('%s.html.twig', $templateName);

        return $this->renderer->render($fullTemplateName, $parameters);
    }

    /**
     * @param class-string<SceneInterface> $scene
     * @return string
     */
    protected function switchToScene(string $scene): string
    {
        return \DI::getService($scene)->run();
    }
}
