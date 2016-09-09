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
 * Saola
 *
 * @package     Joomla.Site
 * @subpackage  Module.Saola
 * @author      Krishnan <krishnan57474@gmail.com>
 * @link        https://github.com/krishnan57474
 * @since       Version 1.0.0
 */
class Saola
{
    /**
     * Module params
     *
     * @var obj
     */
    protected $params;

    /**
     * Input errors
     *
     * @var array
     */
    public $errors;

    /**
     * Filtered inputs
     *
     * @var array
     */
    public $finputs;

    /**
     * Attachments
     *
     * @var array
     */
    protected $attachments;

    /**
     * Load initial values
     *
     * @param   obj     module params
     *
     * @return  void
     */
    public function __construct($params)
    {
        $this->params  = $params;
        $this->errors  = array();
        $this->finputs = array();
    }

    /**
     * Get value from $_GET array
     *
     * @param   string  array key
     *
     * @return  mixed
     */
    protected function get($key)
    {
        return key_exists($key, $_GET) ? $_GET[$key] : NULL;
    }

    /**
     * Get value from $_POST array
     *
     * @param   string  array key
     *
     * @return  mixed
     */
    protected function post($key)
    {
        return key_exists($key, $_POST) ? $_POST[$key] : NULL;
    }

    /**
     * Get value from $_SESSION array
     *
     * @param   string  array key
     *
     * @return  mixed
     */
    protected function session($key)
    {
        return key_exists($key, $_SESSION) ? $_SESSION[$key] : NULL;
    }

    /**
     * Get value from $_FILES array
     *
     * @param   string  array key
     *
     * @return  array
     */
    protected function files($key)
    {
        if (!key_exists($key, $_FILES))
        {
            return NULL;
        }

        $files = array();

        foreach ($_FILES[$key] as $k => $v)
        {
            foreach (is_array($v) ? $v : array($v) as $sk => $sv)
            {
                if (!key_exists($sk, $files))
                {
                    $files[$sk] = array();
                }

                $files[$sk][$k] = $sv;
            }
        }

        return $files;
    }

    /**
     * Extract file extension from file name
     *
     * @param   string  file name
     *
     * @return  string
     */
    protected function getFileExtension($fname)
    {
        $file_ext = '';
        $feindex = mb_strrpos($fname, '.');

        // check file has name and extension
        if ($feindex)
        {
            // get file extension
            $file_ext = mb_substr($fname, $feindex + 1);
        }

        return $file_ext;
    }

    /**
     * Sanitize file name
     *
     * @param   string  file name
     *
     * @return  string
     */
    protected function sanitizeFilename($fname)
    {
        $fname = preg_replace(array(
            '#[^a-zA-Z0-9\.\-_ ]#',
            '#\.+#'
        ), array('', '.'), $fname);

        if (!strrpos($fname, '.'))
        {
            $fname = md5(uniqid(mt_rand())) . $fname;
        }

        if (strlen($fname) > 255)
        {
            $fname = substr($fname, strlen($fname) - 255);
        }

        return $fname;
    }

    /**
     * Generates headers that force a download to happen
     *
     * @param   string  filename
     * @param   mixed   the data to be downloaded
     *
     * @return  void
     */
    protected function forceDownload($filename, $data)
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT']))
        {
            $filename = substr($filename, 0, strrpos($filename, '.') + 1) . strtoupper($this->getFileExtension($filename));
        }

        // Clean output buffer
        if (ob_get_level() !== 0 && @ob_end_clean() === FALSE)
        {
            @ob_clean();
        }

        // Generate the server headers
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($data));

        // Internet Explorer-specific headers
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
        {
            header('Cache-Control: no-cache, no-store, must-revalidate');
        }

        header('Pragma: no-cache');

        // If we have raw data - just dump it
        if ($data !== NULL)
        {
            exit($data);
        }
    }

    /**
     * Upload attachments
     *
     * @param   string   attachment field
     *
     * @return  array
     */
    protected function uploadAttachments($afield)
    {
        $attach_info = array();
        $attachments = array();
        $attach_count = 1;

        if (!is_null($this->params->get('attach_multiple')))
        {
            $attach_count = $this->params->get('attach_count');
            $attach_count = $attach_count ? $attach_count : NULL;
        }

        foreach (array_slice($this->files($afield), 0, $attach_count) as $_attach)
        {
            if (is_uploaded_file($_attach['tmp_name'])
                && $_attach['error'] == 0 && $_attach['size'])
            {
                $attachments[] = $_attach;
            }
        }

        if (!$attachments)
        {
            return $attach_info;
        }

        $max_filesize = $this->params->get('attach_size', 0, 'INT') * 1000;
        $allowed_filetypes = array();

        foreach (explode(',', $this->params->get('attach_types')) as $_ftypes)
        {
            $allowed_filetypes[] = strtolower(trim($_ftypes));
        }

        // validate cached attachments
        foreach ($attachments as $_attach)
        {
            // check file size
            if ($_attach['size'] > $max_filesize)
            {
                $this->errors[] = sprintf(JText::_('SAOLA_ERROR_ATTACH_FILE_SIZE'), htmlentities($_attach['name'], ENT_QUOTES, 'UTF-8'));

                continue;
            }

            $file_ext = $this->getFileExtension($_attach['name']);

            // validate file extension
            if (!$file_ext || !in_array(strtolower($file_ext), $allowed_filetypes))
            {
                $this->errors[] = sprintf(JText::_('SAOLA_ERROR_ATTACH_FILE_TYPE'), htmlentities($_attach['name'], ENT_QUOTES, 'UTF-8'));
            }
        }

        if ($this->errors)
        {
            return $attach_info;
        }

        $attach_path = SAOLA . '/assets/attachments/';

        foreach ($attachments as $_attach)
        {
            $file_name = md5(uniqid(mt_rand()));

            // move validated files
            if (!@copy($_attach['tmp_name'], $attach_path . $file_name))
            {
                if (!@move_uploaded_file($_attach['tmp_name'], $attach_path . $file_name))
                {
                    $this->errors[] = sprintf(JText::_('SAOLA_ERROR_ATTACH_UPLOAD'), htmlentities($_attach['name'], ENT_QUOTES, 'UTF-8'));
                    continue;
                }
            }

            $attach_info[] = array(
                'file_name'  => $file_name,
                'real_name'  => $this->sanitizeFilename($_attach['name']),
                'secret_key' => md5(uniqid(mt_rand()))
            );
        }

        return $attach_info;
    }

    /**
     * Add attachments to DB
     *
     * @param   array   attachments
     *
     * @return  void
     */
    protected function addAttachments($attachments)
    {
        if (!$attachments)
        {
            return;
        }

        $db = JFactory::getDbo();

        foreach (json_decode(json_encode($attachments)) as $_attach)
        {
            $db->insertObject('#__saola', $_attach);
        }
    }

    /**
     * Remove stored attachments
     *
     * @param   array   attachments
     *
     * @return  void
     */
    protected function removeAttachments($attachments)
    {
        if (!$attachments)
        {
            return;
        }

        foreach ($attachments as $_attach)
        {
            unlink(SAOLA . '/assets/attachments/' . $_attach['file_name']);
        }
    }

    /**
     * Returns the attachment file info
     *
     * @param   string  secret key
     *
     * @return  obj
     */
    protected function getAttachment($secret_key)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from($db->quoteName('#__saola'));
        $query->where($db->quoteName('secret_key') . ' = "' . $query->escape($secret_key) . '"');
        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Download attachment
     *
     * @return  void
     */
    public function downloadAttachment()
    {
        $attachment = $this->getAttachment($this->get('saola-download'));

        if (!$attachment)
        {
            return;
        }

        $fpath = SAOLA . '/assets/attachments/' . $attachment->file_name;

        if (!file_exists($fpath))
        {
            return;
        }

        $this->forceDownload($attachment->real_name, file_get_contents($fpath));
    }

    /**
     * Extract and filter email address
     *
     * @param   string   email address
     *
     * @return  array
     */
    protected function extractEmails($emails)
    {
        $femails = array();

        foreach (explode(',', $emails) as $email)
        {
            if (filter_var(trim($email), FILTER_VALIDATE_EMAIL))
            {
                $femails[] = trim($email);
            }
        }

        return $femails;
    }

    /**
     * Generate html email template
     *
     * @return  string
     */
    protected function getEmailTemplate()
    {
        ob_start();

        include SAOLA . '/assets/templates/' . $this->params->get('template') . '.php';

        $buffer = ob_get_contents();

        @ob_end_clean();

        return $buffer;
    }

    /**
     * Send email
     *
     * @return  bool
     */
    public function sendMail()
    {
        $sender = array(JFactory::getConfig()->get('mailfrom'), JFactory::getConfig()->get('fromname'));
        $recipient = $this->extractEmails($this->params->get('recipient'));
        $cc = $this->extractEmails($this->params->get('cc'));
        $bcc = $this->extractEmails($this->params->get('bcc'));
        $mailStatus = FALSE;

        if ($recipient)
        {
            $mailer = JFactory::getMailer();
            $mailer->isHtml();
            $mailer->setSender($sender);
            $mailer->addRecipient($recipient);
            $mailer->addCC($cc);
            $mailer->addBCC($bcc);
            $mailer->setSubject($this->params->get('subject'));
            $mailer->setBody($this->getEmailTemplate());
            $mailStatus = $mailer->Send();
        }

        if ($mailStatus)
        {
            $this->addAttachments($this->attachments);
        }
        else
        {
            $this->removeAttachments($this->attachments);
            $this->errors[] = $this->params->get('msg_error') ? $this->params->get('msg_error') : JText::_('SAOLA_ERROR_EMAIL');
        }

        // reset inputs
        $this->finputs = array();
        $this->attachments = NULL;

        return $mailStatus;
    }

    /**
     * Create captcha
     *
     * @param   array   data for the captcha
     *
     * @return  array
     */
    public function createCaptcha($data)
    {
        $defaults = array(
            'word'        => '',
            'img_path'    => '',
            'img_width'   => '150',
            'img_height'  => '30',
            'font_path'   => '',
            'expiration'  => 7200,
            'word_length' => 8,
            'font_size'   => 16,
            'pool'        => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'colors'      => array(
                'background' => array(255,255,255),
                'border'     => array(153,102,102),
                'text'       => array(0,0,0),
                'grid'       => array(255,182,182)
            )
        );

        foreach ($defaults as $key => $val)
        {
            $$key = $val;
        }

        foreach ($data as $key => $val)
        {
            $$key = $val;
        }

        if (!is_dir($img_path) || !is_writable($img_path) || !extension_loaded('gd'))
        {
            return FALSE;
        }

        // Remove old images
        $now = microtime(TRUE);

        $current_dir = @opendir($img_path);
        while ($filename = @readdir($current_dir))
        {
            if (substr($filename, -4) === '.jpg' && (str_replace('.jpg', '', $filename) + $expiration) < $now)
            {
                @unlink($img_path . $filename);
            }
        }

        @closedir($current_dir);

        // Do we have a "word" yet?
        if (empty($word))
        {
            $word = '';
            for ($i = 0, $mt_rand_max = strlen($pool) - 1; $i < $word_length; $i++)
            {
                $word .= $pool[mt_rand(0, $mt_rand_max)];
            }
        }
        elseif (!is_string($word))
        {
            $word = (string) $word;
        }

        // Determine angle and position
        $length = strlen($word);
        $angle  = ($length >= 6) ? mt_rand(-($length-6), ($length-6)) : 0;
        $x_axis = mt_rand(6, (360/$length)-16);
        $y_axis = ($angle >= 0) ? mt_rand($img_height, $img_width) : mt_rand(6, $img_height);

        // Create image
        // PHP.net recommends imagecreatetruecolor(), but it isn't always available
        $im = function_exists('imagecreatetruecolor')
            ? imagecreatetruecolor($img_width, $img_height)
            : imagecreate($img_width, $img_height);

        //  Assign colors
        is_array($colors) || $colors = $defaults['colors'];

        foreach (array_keys($defaults['colors']) as $key)
        {
            // Check for a possible missing value
            is_array($colors[$key]) || $colors[$key] = $defaults['colors'][$key];
            $colors[$key] = imagecolorallocate($im, $colors[$key][0], $colors[$key][1], $colors[$key][2]);
        }

        // Create the rectangle
        ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $colors['background']);

        //  Create the spiral pattern
        $theta      = 1;
        $thetac     = 7;
        $radius     = 16;
        $circles    = 20;
        $points     = 32;

        for ($i = 0, $cp = ($circles * $points) - 1; $i < $cp; $i++)
        {
            $theta += $thetac;
            $rad = $radius * ($i / $points);
            $x = ($rad * cos($theta)) + $x_axis;
            $y = ($rad * sin($theta)) + $y_axis;
            $theta += $thetac;
            $rad1 = $radius * (($i + 1) / $points);
            $x1 = ($rad1 * cos($theta)) + $x_axis;
            $y1 = ($rad1 * sin($theta)) + $y_axis;
            imageline($im, $x, $y, $x1, $y1, $colors['grid']);
            $theta -= $thetac;
        }

        //  Write the text

        $use_font = ($font_path !== '' && file_exists($font_path) && function_exists('imagettftext'));
        if ($use_font === FALSE)
        {
            ($font_size > 5) && $font_size = 5;
            $x = mt_rand(0, $img_width / ($length / 3));
            $y = 0;
        }
        else
        {
            ($font_size > 30) && $font_size = 30;
            $x = mt_rand(0, $img_width / ($length / 1.5));
            $y = $font_size + 2;
        }

        for ($i = 0; $i < $length; $i++)
        {
            if ($use_font === FALSE)
            {
                $y = mt_rand(0 , $img_height / 2);
                imagestring($im, $font_size, $x, $y, $word[$i], $colors['text']);
                $x += ($font_size * 2);
            }
            else
            {
                $y = mt_rand($img_height / 2, $img_height - 3);
                imagettftext($im, $font_size, $angle, $x, $y, $colors['text'], $font_path, $word[$i]);
                $x += $font_size;
            }
        }

        // Create the border
        imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $colors['border']);

        if (function_exists('imagejpeg'))
        {
            $img_filename = $now . '.jpg';
            imagejpeg($im, $img_path . $img_filename);
        }
        elseif (function_exists('imagepng'))
        {
            $img_filename = $now . '.png';
            imagepng($im, $img_path . $img_filename);
        }
        else
        {
            return FALSE;
        }

        ImageDestroy($im);

        return array('word' => $word, 'filename' => $img_filename);
    }

    /**
     * Check form submit and CSRF token
     *
     * @return  bool
     */
    public function isFormSubmit()
    {
        return ($this->post('saola-submit')
            && $this->session('csrf')
            && ($this->post($this->session('csrf')[0]) === $this->session('csrf')[1]));
    }

    /**
     * Validate captcha
     *
     * @return  bool
     */
    public function isValidCaptcha()
    {
        if ($this->params->get('captcha_verify')
            && (!$this->session('saola') || $this->session('saola') !== $this->post('saola-captcha')))
        {
            $this->errors[] = JText::_('SAOLA_ERROR_CAPTCHA_VERIFICATION');
        }

        return !$this->errors;
    }

    /**
     * Check download request
     *
     * @return  bool
     */
    public function isDownloadRequest()
    {
        return (is_string($this->get('saola-download')) && strlen($this->get('saola-download')) === 32);
    }
}