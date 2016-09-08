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

?>
<div class="contact-form">
    <div class="header">
        <?php if ($success): ?>
        <p class="alert alert-success"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php foreach ($saola->errors as $error): ?>
        <p class="alert alert-danger"><?php echo $error; ?></p>
        <?php endforeach; ?>

        <p>Below <span class="text-danger">(*)</span> marked fields are mandatory fields.</p>
    </div>
    <div class="content">
        <fieldset>
            <form enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="saola-name">Name <span class="text-danger">(*)</span></label>
                    <input class="form-control" id="saola-name" name="saola-name" type="text"<?php if ($saola->finputs): ?> value="<?php echo $saola->finputs['name']; ?>"<?php endif; ?> required>
                </div>

                <div class="form-group">
                    <label for="saola-email">Email Address <span class="text-danger">(*)</span></label>
                    <input class="form-control" id="saola-email" name="saola-email" type="email"<?php if ($saola->finputs): ?> value="<?php echo $saola->finputs['email']; ?>"<?php endif; ?> required>
                </div>

                <div class="form-group">
                    <label for="saola-phone">Phone Number</label>
                    <input class="form-control" id="saola-phone" name="saola-phone" type="text"<?php if ($saola->finputs): ?> value="<?php echo $saola->finputs['phone']; ?>"<?php endif; ?>>
                </div>

                <div class="form-group">
                    <label for="saola-subject">Subject <span class="text-danger">(*)</span></label>
                    <input class="form-control" id="saola-subject" name="saola-subject" type="text"<?php if ($saola->finputs): ?> value="<?php echo $saola->finputs['subject']; ?>"<?php endif; ?> required>
                </div>

                <div class="form-group">
                    <label for="saola-message">Message <span class="text-danger">(*)</span></label>
                    <textarea class="form-control" id="saola-message" name="saola-message" required><?php if ($saola->finputs): echo $saola->finputs['message']; endif; ?></textarea>
                </div>

                <?php if ($params->get('captcha_verify')): ?>
                <div class="form-group form-inline">
                    <div class="form-group">
                        <label for="saola-captcha">Captcha <span class="text-danger">(*)</span></label>
                        <img src="<?php echo 'modules/' . $module->module . '/images/' . $captcha['filename']; ?>" alt>
                        <input class="form-control" id="saola-captcha" name="saola-captcha" type="text" required>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Attachment <span class="text-danger">(*)</span></label>
                    <p class="help-block">(Maximum upload file size <?php echo $params->get('attach_size'); ?>KB. Allowed file types <?php echo $params->get('attach_types'); ?>)</p>
                </div>

                <div class="form-group form-inline">
                    <div class="form-group">
                        <?php if ($params->get('attach_multiple')): ?>
                        <input name="saola-attach[]" type="file" multiple required>
                        <?php else: ?>
                        <input name="saola-attach" type="file" required>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
                    <button class="btn btn-default" name="saola-submit" type="submit" value="submit">Submit</button>
                </div>
            </form>
        </fieldset>
    </div>
</div>