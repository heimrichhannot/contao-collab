<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Collab\Backend;


class BackendHooks extends \Backend
{
	
	public function executePostActionsHook($strAction, \DataContainer $dc)
	{
		switch ($strAction)
		{
			case 'toggleTask':
				$dca = new TaskBackend();
				
				if (method_exists($dca, 'toggleTask'))
				{
					$dca->toggleTask(\Input::post('id'), ((\Input::post('state') == 1) ? true : false));
				}
				exit; break;
			break;
		}
	}
	
}