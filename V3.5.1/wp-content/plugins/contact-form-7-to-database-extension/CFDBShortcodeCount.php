<?php

/*

    "Contact Form to Database" Copyright (C) 2011-2012 Michael Simpson  (email : michael.d.simpson@gmail.com)



    This file is part of Contact Form to Database.



    Contact Form to Database is free software: you can redistribute it and/or modify

    it under the terms of the GNU General Public License as published by

    the Free Software Foundation, either version 3 of the License, or

    (at your option) any later version.



    Contact Form to Database is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with Contact Form to Database.

    If not, see <http://www.gnu.org/licenses/>.

*/



require_once('CFDBShortcodeValue.php');



class CFDBShortcodeCount extends CFDBShortcodeValue {



    public function handleShortcode($atts) {

        $atts['function'] = 'count';

        unset($atts['show']);

        unset($atts['hide']);

        return parent::handleShortcode($atts);

    }

}

