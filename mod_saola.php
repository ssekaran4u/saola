<?php
/**
 * Saola
 *
 * A simple Joomla! contact form
 *
 * This content is released under the GNU General Public License
 * version 2 or later; see LICENSE.txt
 *
 * Copyright (C) 2016, Krishnan
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2
 * of the License or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Saola.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     Joomla.Site
 * @subpackage  Module.Saola
 * @author      Krishnan <krishnan57474@gmail.com>
 * @copyright   Copyright (C) 2016, Krishnan
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link        https://github.com/krishnan57474
 * @since       Version 1.0.0
 */

defined('_JEXEC') or die;

define('SAOLA', __DIR__);

require_once SAOLA . '/assets/lib/Saola.php';
require_once SAOLA . '/helper.php';

$saola = new modSaolaHelper($params);
$success = NULL;

if ($saola->isFormSubmit())
{
    if ($saola->getAllInput()
        && $saola->isValidCaptcha()
        && $saola->isValidInput()
        && $saola->sendMail())
    {
        $success = $params->get('msg_success') ? $params->get('msg_success') : sprintf(JText::_('SAOLA_SUCCESS_EMAIL'), JFactory::getConfig()->get('sitename'));
    }
}
else if ($saola->isDownloadRequest())
{
    $saola->downloadAttachment();
}

if ($params->get('captcha_verify'))
{
    $captcha = $saola->createCaptcha(array(
        'img_path'    => SAOLA . '/images/',
        'word_length' => $params->get('captcha_wlength')
    ));

    // store captcha in session
    $_SESSION['saola_captcha'] = $captcha['word'];
}

$csrf = array(
    'name' => md5(uniqid(mt_rand())),
    'hash' => md5(uniqid(mt_rand()))
);

// store csrf in session
$_SESSION['saola_csrf'] = array($csrf['name'], $csrf['hash']);

require JModuleHelper::getLayoutPath('mod_saola');