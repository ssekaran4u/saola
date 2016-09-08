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

/**
 * Saola helper
 *
 * @package     Joomla.Site
 * @subpackage  Module.Saola
 * @author      Krishnan <krishnan57474@gmail.com>
 * @link        https://github.com/krishnan57474
 * @since       Version 1.0.0
 */
class modSaolaHelper extends Saola
{
    /**
     * Filter user inputs
     *
     * @return  bool
     */
    public function getAllInput()
    {
        $this->finputs = array(
            'name'      => '',
            'email'     => '',
            'phone'     => '',
            'subject'   => '',
            'message'   => ''
        );

        if (is_string($this->post('saola-name')))
        {
            $this->finputs['name'] = $this->post('saola-name');
        }

        if (is_string($this->post('saola-email')))
        {
            $this->finputs['email'] = $this->post('saola-email');
        }

        if (is_string($this->post('saola-phone')))
        {
            $this->finputs['phone'] = $this->post('saola-phone');
        }

        if (is_string($this->post('saola-subject')))
        {
            $this->finputs['subject'] = $this->post('saola-subject');
        }

        if (is_string($this->post('saola-message')))
        {
            $this->finputs['message'] = $this->post('saola-message');
        }

        return !$this->errors;
    }

    /**
     * Validate all mandatory fields
     *
     * @return  bool
     */
    public function isValidInput()
    {
        if (mb_strlen($this->finputs['name']) < 4
            || mb_strlen($this->finputs['name']) > 20)
        {
            $this->errors[] = 'Name must be between 4 and 20 characters!';
        }

        if (!filter_var($this->finputs['email'], FILTER_VALIDATE_EMAIL))
        {
            $this->errors[] = 'Email does not apear to be valid. Please enter valid email address';
        }

        if (mb_strlen($this->finputs['subject']) < 4
            || mb_strlen($this->finputs['subject']) > 80)
        {
            $this->errors[] = 'Subject must be between 4 and 80 characters!';
        }

        if (mb_strlen($this->finputs['message']) < 4
            || mb_strlen($this->finputs['message']) > 2000)
        {
            $this->errors[] = 'Message must be between 4 and 2000 characters!';
        }

        if (!$this->errors)
        {
            $this->attachments = $this->uploadAttachments('saola-attach');
        }

        if (!$this->errors && !$this->attachments)
        {
            $this->errors[] = 'No attachments found';
        }

        return !$this->errors;
    }
}