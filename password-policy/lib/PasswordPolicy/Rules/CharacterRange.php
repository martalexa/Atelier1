<?php

/*
 * The Password Policy for implementing Password Policies
 *
 * @author     Anthony Ferrara <ircmaxell@ircmaxell.com>
 * @copyright  2011 The Authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Build @@version@@
 */
namespace PasswordPolicy\Rules;

class CharacterRange extends Regex
{
    public function __construct($range, $textDescription)
    {
        $this->description = "Expecting %s $textDescription characters";
        $this->regex = '/[' . $range . ']/';
    }
}
