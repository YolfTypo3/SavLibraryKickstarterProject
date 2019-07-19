<?php
namespace YolfTypo3\SavLibraryPlus\ViewHelpers\Link;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with TYPO3 source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use YolfTypo3\SavLibraryPlus\Managers\UriManager;
use YolfTypo3\SavLibraryPlus\Controller\AbstractController;
use YolfTypo3\SavLibraryPlus\Managers\ExtensionConfigurationManager;

/**
 * A view helper for creating links to extbase actions.
 *
 * = Examples =
 *
 * <code title="link to the show-action of the current controller">
 * <f:link.action action="show">action link</f:link.action>
 * </code>
 * <output>
 * <a href="index.php?id=123&tx_myextension_plugin[action]=show&tx_myextension_plugin[controller]=Standard&cHash=xyz">action link</f:link.action>
 * (depending on the current page and your TS configuration)
 * </output>
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ActionViewHelper extends AbstractTagBasedViewHelper
{

    /**
     *
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Arguments initialization
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
        $this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
        $this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
        $this->registerArgument('action', 'string', 'Target action');
        $this->registerArgument('controller', 'string', 'Target controller. If NULL current controllerName is used');
        $this->registerArgument('extensionName', 'string', 'Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used');
        $this->registerArgument('pluginName', 'string', 'Target plugin. If empty, the current plugin name is used');
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('pageType', 'int', 'Type of the target page. See typolink.parameter');
        $this->registerArgument('noCache', 'bool', 'Set this to disable caching for the target page. You should not need this.');
        $this->registerArgument('noCacheHash', 'bool', 'Set this to suppress the cHash query parameter created by TypoLink. You should not need this.');
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI');
        $this->registerArgument('format', 'string', 'The requested format, e.g. ".html');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.');
        $this->registerArgument('additionalParams', 'array', 'Additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)');
        $this->registerArgument('absolute', 'bool', 'If set, the URI of the rendered link is absolute');
        $this->registerArgument('addQueryString', 'bool', 'If set, the current query parameters will be kept in the URI');
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'Arguments to be removed from the URI. Only active if $addQueryString = TRUE');
        $this->registerArgument('addQueryStringMethod', 'string', 'Set which parameters will be kept. Only active if $addQueryString = TRUE');
        $this->registerArgument('arguments', 'array', 'Arguments for the controller action, associative array');
    }

    /**
     * Renders the viewhelper
     *
     * @return string Rendered link
     */
    public function render()
    {
        // Gets the arguments
        $action = $this->arguments['action'];
        $pageUid = (int) $this->arguments['pageUid'] ?: null;
        $pageType = (int) $this->arguments['pageType'];
        $noCache = (bool) $this->arguments['noCache'];
        $noCacheHash = (bool) $this->arguments['useCacheHash'];
        $section = (string) $this->arguments['section'];
        $format = (string) $this->arguments['format'];
        $linkAccessRestrictedPages = (bool) $this->arguments['linkAccessRestrictedPages'];
        $additionalParams = (array) $this->arguments['additionalParams'];
        $absolute = (bool) $this->arguments['absolute'];
        $addQueryString = (bool) $this->arguments['addQueryString'];
        $argumentsToBeExcludedFromQueryString = (array) $this->arguments['argumentsToBeExcludedFromQueryString'];
        $parameters = $this->arguments['arguments'];

        // Sets the new action
        $compressedParameters = UriManager::getCompressedParameters();
        $libraryParam = AbstractController::changeCompressedParameters($compressedParameters, 'formAction', $action);
        $formName = AbstractController::getFormName();
        $libraryParam = AbstractController::changeCompressedParameters($libraryParam, 'formName', $formName);

        // Changes the other parameters if any
        if (is_array($parameters)) {
            foreach ($parameters as $parameterKey => $parameter) {
                $libraryParam = AbstractController::changeCompressedParameters($libraryParam, $parameterKey, $parameter);
            }
        }

        // sets the additionalParams
        $additionalParams = array_merge($additionalParams, [
            AbstractController::LIBRARY_NAME => $libraryParam
        ]);

        // Sets the noCacheHash based on the extension type
        $noCacheHash = ! ExtensionConfigurationManager::isCacheHashRequired();

        // Sets the noCache
        $noCache = UriManager::hasNoCacheParameter();

        // builds the uri
        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
        $uri = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($pageType)
            ->setNoCache($noCache)
            ->setUseCacheHash(! $noCacheHash)
            ->setSection($section)
            ->setFormat($format)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
            ->build();
        $this->tag->addAttribute('href', $uri);
        $this->tag->setContent($this->renderChildren());

        return $this->tag->render();
    }
}
?>
