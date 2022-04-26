<?php

namespace App;

class Utils
{
	// htmlspecialcharsを短くする
	public static function h($str)
	{
		return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	}

	public static function num($num)
	{
		return number_format($num);
	}

	// 現在ページのフルURLを返す
	public static function getCurrentPageUrl()
	{
		return (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public static function sendDate($get_y, $get_m)
	{
		$send_date = [];
		$send_date['year'] = $get_y;
		$send_date['month'] = $get_m;
		$_SESSION['date'] = $send_date;
	}

	public static function totalAmount($male_price, $female_price)
	{
		$total_amount = $male_price + $female_price;
		echo Utils::h(Utils::num($total_amount));
	}

	public static function removeDifference($male_price, $female_price)
	{
		$difference = $male_price - $female_price;
		$remove_difference = str_replace('-', '', $difference);
		echo Utils::h(Utils::num($remove_difference));
	}

	public static function  perPersonAmount($male_price, $female_price)
	{
		$per_person_amount = ($male_price - $female_price) / 2;
		$remove_perperson_amount = str_replace('-', '', $per_person_amount);
		echo Utils::h(Utils::num($remove_perperson_amount));
	}
}
