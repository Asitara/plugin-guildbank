<?php
/*
 * Project:     EQdkp GuildBanker
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2005
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2005-2014 Wallenium
 * @link        http://eqdkp-plus.com
 * @package     guildbank
 * @version     $Rev$
 *
 * $Id$
 */

	// The array with the images
	$money_data = array(
		'diamond'		=> array(
			'image'			=> 'eq/images/platin.png',
			'image_large'	=> 'eq/images/platin_large.png',
			'factor'		=> 1000,
			'size'			=> 'unlimited',
			'language'		=> $user->lang['lang_platin'],
			'short_lang'	=> $user->lang['lang_p'],
		),
		'gold'		=> array(
			'image'			=> 'default/images/gold.png',
			'image_large'	=> 'default/images/gold_large.png',
			'factor'		=> 100,
			'size'			=> 2,
			'language'		=> $user->lang['lang_gold'],
			'short_lang'	=> $user->lang['lang_g'],
		),
		'silver'	=> array(
			'image'			=> 'default/images/silver.png',
			'image_large'	=> 'default/images/silver_large.png',
			'factor'		=> 10,
			'size'			=> 2,
			'language'		=> $user->lang['lang_silver'],
			'short_lang'	=> $user->lang['lang_s'],
		),
		'copper'	=> array(
			'image'			=> 'default/images/copper.png',
			'image_large'	=> 'default/images/copper_large.png',
			'factor'		=> 1,
			'size'			=> 2,
			'language'		=> $user->lang['lang_copper'],
			'short_lang'	=> $user->lang['lang_c'],
		)
	);

 ?>
