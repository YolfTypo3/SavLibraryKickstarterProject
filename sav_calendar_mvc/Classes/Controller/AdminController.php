<?php

namespace SAV\SavCalendarMvc\Controller;
/**
*  Copyright notice
*
*  (c) 2017 Laurent Foulloy <yolf.typo3@orange.fr>
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script
*/

/**
 * Controller for the form Admin
 *
 */

class AdminController extends \SAV\SavLibraryMvc\Controller\DefaultController
{

    /**
     * Main repository
     *
     * @var \SAV\SavCalendarMvc\Domain\Repository\EventRepository
     * @inject
     */
    protected $mainRepository = NULL;

    /**
     * Subform repository class names
     *
     * @var array
     */
    protected $subforms = array (
    );
 
    /**
     * Save action for this controller
     *
     * @param \SAV\SavCalendarMvc\Domain\Model\Event $data
     * @return void
     */
    public function saveAction(\SAV\SavCalendarMvc\Domain\Model\Event $data)
    {
        $this->save($data);
    }
}
?>
