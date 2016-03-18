<?php

/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2013 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.7.9, 2013-06-02
 */

/**
 * PHPExcel_Calculation_Statistical
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Statistical {
    /*     * *************************************************************************
     * 								inverse_ncdf.php
     * 							-------------------
     * 	begin				: Friday, January 16, 2004
     * 	copyright			: (C) 2004 Michael Nickerson
     * 	email				: nickersonm@yahoo.com
     *
     * ************************************************************************* */

    private static function _inverse_ncdf($p) {
        //	Inverse ncdf approximation by Peter J. Acklam, implementation adapted to
        //	PHP by Michael Nickerson, using Dr. Thomas Ziegler's C implementation as
        //	a guide. http://home.online.no/~pjacklam/notes/invnorm/index.html
        //	I have not checked the accuracy of this implementation. Be aware that PHP
        //	will truncate the coeficcients to 14 digits.
        //	You have permission to use and distribute this function freely for
        //	whatever purpose you want, but please show common courtesy and give credit
        //	where credit is due.
        //	Input paramater is $p - probability - where 0 < p < 1.
        //	Coefficients in rational approximations
        static $a = array(1 => -3.969683028665376e+01,
            2 => 2.209460984245205e+02,
            3 => -2.759285104469687e+02,
            4 => 1.383577518672690e+02,
            5 => -3.066479806614716e+01,
            6 => 2.506628277459239e+00
        );

        static $b = array(1 => -5.447609879822406e+01,
            2 => 1.615858368580409e+02,
            3 => -1.556989798598866e+02,
            4 => 6.680131188771972e+01,
            5 => -1.328068155288572e+01
        );

        static $c = array(1 => -7.784894002430293e-03,
            2 => -3.223964580411365e-01,
            3 => -2.400758277161838e+00,
            4 => -2.549732539343734e+00,
            5 => 4.374664141464968e+00,
            6 => 2.938163982698783e+00
        );

        static $d = array(1 => 7.784695709041462e-03,
            2 => 3.224671290700398e-01,
            3 => 2.445134137142996e+00,
            4 => 3.754408661907416e+00
        );

        //	Define lower and upper region break-points.
        $p_low = 0.02425;   //Use lower region approx. below this
        $p_high = 1 - $p_low;  //Use upper region approx. above this

        if (0 < $p && $p < $p_low) {
            //	Rational approximation for lower region.
            $q = sqrt(-2 * log($p));
            return ((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                    (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        } elseif ($p_low <= $p && $p <= $p_high) {
            //	Rational approximation for central region.
            $q = $p - 0.5;
            $r = $q * $q;
            return ((((($a[1] * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $r + $a[6]) * $q /
                    ((((($b[1] * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + $b[5]) * $r + 1);
        } elseif ($p_high < $p && $p < 1) {
            //	Rational approximation for upper region.
            $q = sqrt(-2 * log(1 - $p));
            return -((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                    (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        }
        //	If 0 < p < 1, return a null value
        return PHPExcel_Calculation_Functions::NULL();
    }

//	function _inverse_ncdf()

    /**
     * NORMINV
     *
     * Returns the inverse of the normal cumulative distribution for the specified mean and standard deviation.
     *
     * @param	float		$value
     * @param	float		$mean		Mean Value
     * @param	float		$stdDev		Standard Deviation
     * @return	float
     *
     */
    public static function NORMINV($probability, $mean, $stdDev) {

        if ((is_numeric($probability)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($probability < 0) || ($probability > 1)) {
                return 0;
            }
            if ($stdDev < 0) {
                return 0;
            }
            return (self::_inverse_ncdf($probability) * $stdDev) + $mean;
        }
        return 0;
    }
}
?>