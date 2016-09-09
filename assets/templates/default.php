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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
<title><?php echo $this->params->get('subject'); ?></title>
</head>
<body style="background-color:#ebebeb;padding:0;margin:0;width:100%">
<table cellspacing="0" cellpadding="0" style="font-family:Arial,Helvetica,sans-serif;min-width:300px;max-width:540px;margin:0 auto;width:100%;border:0;padding:30px 10px">
<tr>
<td style="background-color:#235daa;text-align:center;padding:30px 20px;-webkit-border-radius:8px 8px 0 0;-moz-border-radius:8px 8px 0 0;border-radius:8px 8px 0 0">
<a href="<?php echo JUri::base(); ?>" style="color:#fff;text-decoration:none;font-size:26px;font-weight:bold"><?php echo JFactory::getConfig()->get('sitename'); ?></a>
</td>
</tr>
<tr>
<td style="background-color:#f6f6f7;padding:30px 20px 10px">
<p style="color:#465059;font-size:24px;font-weight:bold;text-align:center;margin:0">
Contact email
</p>
</td>
</tr>
<tr>
<td style="background-color:#f6f6f7;padding:0 20px 20px">
<p style="font-size:14px;color:#A1A2A5;text-align:center;margin:0">
<?php echo $this->params->get('subject'); ?>
</p>
</td>
</tr>
<?php if ($this->finputs): ?>
<tr>
<td style="background-color:#fff;padding:20px">
<table cellspacing="0" cellpadding="0" style="margin:0 auto;border:0;width:100%">
<?php foreach ($this->finputs as $k => $v): ?>
<?php if (!$v): continue; endif; ?>
<tr>
<td style="font-size:14px;color:#A1A2A5;padding:5px 10px"><?php echo $k; ?></td>
<td style="font-size:14px;color:#555;padding:5px 10px"><?php echo htmlentities($v, ENT_QUOTES, 'UTF-8'); ?></td>
</tr>
<?php endforeach; ?>
</table>
</td>
</tr>
<?php endif; ?>
<?php if ($this->attachments): ?>
<tr>
<td style="background-color:#fff;padding:0 20px">
<p style="font-size:14px;color:#555;line-height: 22px;margin:0;font-weight:bold">
Attachments:
</p>
</td>
</tr>
<tr>
<td style="background-color:#fff;padding:0 20px">
<table cellspacing="0" cellpadding="0" style="margin:0 auto;border:0;width:100%">
<?php foreach ($this->attachments as $_attach): ?>
<tr>
<td style="font-size:14px;color:#A1A2A5;padding:5px 10px"><?php echo htmlentities($_attach['real_name'], ENT_QUOTES, 'UTF-8'); ?></td>
<td>
<a href="<?php echo JUri::current() . '?saola-download=' . $_attach['secret_key']; ?>" style="font-size:14px;color:#666">Download</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</td>
</tr>
<?php endif; ?>
<tr>
<td style="background-color:#fff;padding:5px 20px;-webkit-border-radius:0 0 8px 8px;-moz-border-radius:0 0 8px 8px;border-radius:0 0 8px 8px">
&nbsp;
</td>
</tr>
<?php if ($this->params->get('credits')): ?>
<tr>
<td style="padding:10px 20px 0">
<p style="color:#b2b2b2;font-size:12px;text-align:center;margin:0">
Powered by Saola
</p>
</td>
</tr>
<?php endif; ?>
</table>
</body>
</html>