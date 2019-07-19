<?php

namespace YolfTypo3\SavCalendarMvc\Domain\Model;

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
 * The TYPO3 project - inspiring people to share
 */

/**
 * Category model for the extension SavCalendarMvc
 *
 */
class Category extends \YolfTypo3\SavLibraryMvc\Domain\Model\DefaultModel
{
    /**
     * The title variable.
     *
     * @var string
     * @validate raw
     */
    protected $title;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    /**
     * Getter for title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


}
?>

